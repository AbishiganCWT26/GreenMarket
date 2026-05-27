<?php

namespace App\Http\Controllers\DeliveryRider;

use App\Http\Controllers\Controller;
use App\Models\RiderDelivery;
use App\Models\Order;
use App\Models\BusDispatch;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeliveryController extends Controller
{
    // === Active Deliveries ===

    public function active()
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;

        // Fetch all active deliveries for this rider across all in-progress statuses:
        // delivering (self-claimed), rider_assigned (admin-assigned), arrived_district
        $deliveries = RiderDelivery::where(function ($q) use ($user) {
                $q->where('rider_id', $user->id)
                  ->orWhere('admin_assigned_rider_id', $user->id);
            })
            ->whereIn('delivery_status', ['delivering', 'rider_assigned', 'arrived_district'])
            ->with(['order.buyer', 'order.orderItems', 'busDispatch'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('delivery-rider.active_deliveries', compact('deliveries', 'rider'));
    }

    public function showDelivery($id)
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;

        $delivery = RiderDelivery::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('rider_id', $user->id)
                  ->orWhere('admin_assigned_rider_id', $user->id);
            })
            ->with(['order.buyer', 'order.orderItems.product', 'busDispatch'])
            ->firstOrFail();

        // ACCESS GATE: Rider must confirm arrival before completing delivery
        if ($delivery->delivery_status !== 'arrived_district') {
            return redirect()
                ->route('delivery-rider.active-deliveries')
                ->with('error', 'You must confirm arrival in the buyer\'s district before completing this delivery.');
        }

        return view('delivery-rider.delivery_details', compact('delivery', 'rider'));
    }

    public function confirmArrival(Request $request, $id)
    {
        $user = Auth::user();

        $delivery = RiderDelivery::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('rider_id', $user->id)
                  ->orWhere('admin_assigned_rider_id', $user->id);
            })
            ->firstOrFail();

        // Only allow transition from active states
        if (!in_array($delivery->delivery_status, ['delivering', 'rider_assigned'])) {
            return response()->json([
                'success' => false,
                'message' => 'This delivery cannot be confirmed for arrival at this stage.'
            ], 422);
        }

        DB::transaction(function () use ($delivery) {
            // Update rider delivery
            $delivery->update([
                'delivery_status'      => 'arrived_district',
                'pickup_confirmed_at'  => now(),
            ]);

            // Update order status (uses existing DB constraint value)
            if ($delivery->order) {
                $delivery->order->update([
                    'order_status' => 'arrived_to_district',
                ]);

                // Notify buyer
                Notification::create([
                    'user_id'           => $delivery->order->buyer->user_id,
                    'recipient_type'    => 'buyer',
                    'title'             => 'Products Arrived in Your District',
                    'message'           => "Your products for order #{$delivery->order->order_number} have arrived in your district. Your delivery rider will deliver to your address shortly.",
                    'notification_type' => 'delivery_order_received',
                    'related_id'        => $delivery->order->id,
                ]);
            }

            // Update bus dispatch status if applicable
            if ($delivery->busDispatch) {
                $delivery->busDispatch->update([
                    'dispatch_status' => 'arrived',
                ]);
            }
        });

        return response()->json([
            'success'    => true,
            'message'    => 'Arrival confirmed! You can now complete the delivery.',
            'new_status' => 'arrived_district',
        ]);
    }

    public function completeForm($id)
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;

        $delivery = RiderDelivery::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('rider_id', $user->id)
                  ->orWhere('admin_assigned_rider_id', $user->id);
            })
            ->whereIn('delivery_status', ['delivering', 'rider_assigned'])
            ->with(['order.buyer'])
            ->firstOrFail();

        return view('delivery-rider.complete_delivery', compact('delivery', 'rider'));
    }

    public function complete(Request $request, $id)
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;

        $request->validate([
            'notes' => 'nullable|string|max:500',
            'delivery_proof' => 'nullable|image|max:5120' // Max 5MB
        ]);

        $delivery = RiderDelivery::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('rider_id', $user->id)
                  ->orWhere('admin_assigned_rider_id', $user->id);
            })
            ->whereIn('delivery_status', ['delivering', 'rider_assigned'])
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $proofPath = null;
            if ($request->hasFile('delivery_proof')) {
                $file = $request->file('delivery_proof');
                $filename = 'proof_' . $delivery->order_id . '_' . time() . '.' . $file->getClientOriginalExtension();

                $uploadPath = public_path('uploads/delivery_proofs');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                $file->move($uploadPath, $filename);
                $proofPath = 'uploads/delivery_proofs/' . $filename;
            }

            // Update Rider Delivery status
            $delivery->update([
                'delivery_status' => 'completed'
            ]);

            // Update Order Status
            if ($delivery->order) {
                $delivery->order->update([
                    'order_status' => 'completed',
                    'completed_date' => Carbon::now()
                ]);

                // Notify Buyer
                Notification::create([
                    'user_id' => $delivery->order->buyer->user_id,
                    'recipient_type' => 'buyer',
                    'title' => 'Order Delivered',
                    'message' => "Your order #{$delivery->order->order_number} has been delivered successfully by the rider. Thank you for shopping with GreenMarket!",
                    'notification_type' => 'delivery_completed',
                    'related_id' => $delivery->order->id
                ]);
            }

            // Check if all deliveries in the bus dispatch are completed, update bus dispatch status
            $dispatch = $delivery->busDispatch;
            if ($dispatch) {
                $hasUncompleted = RiderDelivery::where('bus_dispatch_id', $dispatch->id)
                    ->where('delivery_status', '!=', 'completed')
                    ->exists();

                if (!$hasUncompleted) {
                    $dispatch->update([
                        'dispatch_status' => 'delivered'
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('delivery-rider.completed-deliveries')->with('success', 'Delivery marked as completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to complete delivery: ' . $e->getMessage());
        }
    }

    // === Completed Deliveries ===

    public function history(Request $request)
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;

        // Base query for stats
        $baseQuery = RiderDelivery::where('rider_id', $user->id)
            ->where('delivery_status', 'completed');

        // Calculate Stats
        $stats = [
            'today' => (clone $baseQuery)->whereDate('updated_at', now()->today())->count(),
            'week' => (clone $baseQuery)->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month' => (clone $baseQuery)->whereMonth('updated_at', now()->month)->whereYear('updated_at', now()->year)->count(),
        ];

        // Apply filters to main query
        $query = clone $baseQuery;

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('order', function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('buyer', function ($bq) use ($search) {
                      $bq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_filter')) {
            switch ($request->date_filter) {
                case 'today':
                    $query->whereDate('updated_at', now()->today());
                    break;
                case 'week':
                    $query->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('updated_at', now()->month)->whereYear('updated_at', now()->year);
                    break;
                case 'custom':
                    if ($request->filled('start_date') && $request->filled('end_date')) {
                        $query->whereBetween('updated_at', [
                            $request->start_date . ' 00:00:00',
                            $request->end_date . ' 23:59:59'
                        ]);
                    }
                    break;
            }
        }

        $deliveries = $query->with(['order.buyer', 'order.orderItems', 'busDispatch'])
            ->orderBy('updated_at', 'desc')
            ->paginate(12)->withQueryString();

        return view('delivery-rider.completed_deliveries', compact('deliveries', 'rider', 'stats'));
    }

    public function showCompleted($id)
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;

        $delivery = RiderDelivery::where('rider_id', $user->id)
            ->where('id', $id)
            ->where('delivery_status', 'completed')
            ->with(['order.buyer', 'order.orderItems.product', 'busDispatch'])
            ->firstOrFail();

        return view('delivery-rider.completed_delivery_details', compact('delivery', 'rider'));
    }
}
