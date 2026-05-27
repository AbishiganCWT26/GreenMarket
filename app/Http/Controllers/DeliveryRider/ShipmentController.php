<?php

namespace App\Http\Controllers\DeliveryRider;

use App\Http\Controllers\Controller;
use App\Models\BusDispatch;
use App\Models\RiderDelivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShipmentController extends Controller
{
    public function incoming()
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        $districts = $rider && $rider->assigned_districts ? json_decode($rider->assigned_districts, true) : [];

        // Fetch dispatches that contain unassigned or self-assigned deliveries in the rider's district
        // Acceptance window: > 45 min before ETA
        $dispatches = BusDispatch::where('dispatch_status', 'in_transit')
            ->whereNotNull('estimated_arrival_time')
            ->where('estimated_arrival_time', '>', now()->addMinutes(45))
            ->whereHas('riderDeliveries', function($q) use ($user, $districts) {
                $q->where('delivery_status', 'assigned')
                  ->whereNull('claimed_by_rider_id')
                  ->where(function($sub) use ($user) {
                      $sub->whereNull('rider_id')
                          ->orWhere('rider_id', $user->id);
                  });

                if (!empty($districts)) {
                    $q->whereHas('order.buyer', function($sub) use ($districts) {
                        $sub->whereIn('district', $districts);
                    });
                }
            })
            ->with(['leadFarmer.leadFarmer'])
            ->withCount(['riderDeliveries as total_orders' => function($q) use ($user, $districts) {
                $q->where('delivery_status', 'assigned')
                  ->whereNull('claimed_by_rider_id')
                  ->where(function($sub) use ($user) {
                      $sub->whereNull('rider_id')
                          ->orWhere('rider_id', $user->id);
                  });
                if (!empty($districts)) {
                    $q->whereHas('order.buyer', function($sub) use ($districts) {
                        $sub->whereIn('district', $districts);
                    });
                }
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('delivery-rider.incoming_shipments', compact('dispatches', 'rider'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        $districts = $rider && $rider->assigned_districts ? json_decode($rider->assigned_districts, true) : [];

        $dispatch = BusDispatch::with(['leadFarmer.leadFarmer', 'riderDeliveries' => function($q) use ($user, $districts) {
            $q->where('delivery_status', 'assigned')
              ->where(function($sub) use ($user) {
                  $sub->whereNull('rider_id')
                      ->orWhere('rider_id', $user->id);
              })
              ->with(['order.buyer', 'order.orderItems']);

            if (!empty($districts)) {
                $q->whereHas('order.buyer', function($sub) use ($districts) {
                    $sub->whereIn('district', $districts);
                });
            }
        }])->findOrFail($id);

        return view('delivery-rider.shipment_details', compact('dispatch', 'rider'));
    }
    
    public function checkAvailability($id)
    {
        $dispatch = BusDispatch::find($id);
        if (!$dispatch) {
            return response()->json(['available' => false, 'message' => 'Shipment not found.']);
        }
        
        if (!$dispatch->estimated_arrival_time || $dispatch->estimated_arrival_time <= now()->addMinutes(45)) {
            return response()->json(['available' => false, 'message' => 'Acceptance window is closed.']);
        }

        return response()->json(['available' => true]);
    }

    public function accept($id)
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        $districts = $rider && $rider->assigned_districts ? json_decode($rider->assigned_districts, true) : [];

        DB::beginTransaction();
        try {
            $dispatch = BusDispatch::findOrFail($id);
            
            // Check acceptance window
            if (!$dispatch->estimated_arrival_time || $dispatch->estimated_arrival_time <= now()->addMinutes(45)) {
                return response()->json(['success' => false, 'message' => 'The acceptance window for this shipment is closed.'], 403);
            }

            // Fetch all assigned rider deliveries for this dispatch that match rider's filter with pessimistic locking
            $deliveriesQuery = RiderDelivery::where('bus_dispatch_id', $dispatch->id)
                ->where('delivery_status', 'assigned')
                ->whereNull('claimed_by_rider_id')
                ->where(function($sub) use ($user) {
                    $sub->whereNull('rider_id')
                        ->orWhere('rider_id', $user->id);
                })
                ->lockForUpdate();

            if (!empty($districts)) {
                $deliveriesQuery->whereHas('order.buyer', function($sub) use ($districts) {
                    $sub->whereIn('district', $districts);
                });
            }

            $deliveries = $deliveriesQuery->get();

            if ($deliveries->isEmpty()) {
                DB::rollBack();
                if (request()->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'No eligible shipments to accept or already claimed.'], 409);
                }
                return redirect()->route('delivery-rider.incoming-shipments')->with('error', 'No eligible shipments to accept.');
            }

            foreach ($deliveries as $delivery) {
                $delivery->update([
                    'rider_id' => $user->id,
                    'claimed_by_rider_id' => $user->id,
                    'claimed_at' => now(),
                    'delivery_status' => 'delivering'
                ]);

                // Update original Order status to "delivering" or keep as "Dispatched" but tracking status active
                if ($delivery->order) {
                    $delivery->order->update([
                        'order_status' => 'Delivering'
                    ]);
                }
            }

            DB::commit();
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Shipment accepted successfully. You can now start the deliveries.',
                    'redirect' => route('delivery-rider.active-deliveries')
                ]);
            }
            return redirect()->route('delivery-rider.active-deliveries')->with('success', 'Shipment accepted successfully. You can now start the deliveries.');
        } catch (\Exception $e) {
            DB::rollBack();
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to accept shipment: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Failed to accept shipment: ' . $e->getMessage());
        }
    }
}
