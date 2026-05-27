<?php

namespace App\Http\Controllers\DeliveryRider;

use App\Http\Controllers\Controller;
use App\Models\RiderDelivery;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        $districts = $rider && $rider->assigned_districts ? json_decode($rider->assigned_districts, true) : [];

                // Query deliveries assigned to this rider user (either direct rider_id or admin_assigned_rider_id)
                $deliveries = RiderDelivery::where(function($q) use ($user) {
                                $q->where('rider_id', $user->id)
                                    ->orWhere('admin_assigned_rider_id', $user->id);
                        })
                        ->with(['order.orderItems', 'busDispatch'])
                        ->get();

        // 1. Pending Incoming Shipments count
        $incomingCount = \App\Models\BusDispatch::where('dispatch_status', 'in_transit')
            ->whereHas('riderDeliveries', function($q) use ($user, $districts) {
                $q->where('delivery_status', 'assigned')
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
            ->count();

        // 2. Active Deliveries count (status 'delivering')
        $activeCount = $deliveries->where('delivery_status', 'delivering')->count();

        // 3. Completed Today count
        $completedTodayCount = $deliveries->where('delivery_status', 'completed')
            ->filter(function($d) {
                return $d->updated_at && $d->updated_at->isToday();
            })
            ->count();

        // 4. Total Earnings Today (LKR) based on delivery fee logic: total_amount - items_total
        $earningsToday = 0;
        foreach ($deliveries->where('delivery_status', 'completed') as $delivery) {
            if ($delivery->updated_at && $delivery->updated_at->isToday() && $delivery->order) {
                $itemsTotal = $delivery->order->orderItems->sum('item_total');
                $deliveryFee = $delivery->order->total_amount - $itemsTotal;
                $earningsToday += max(0, $deliveryFee);
            }
        }

        $stats = [
            'incoming_shipments' => $incomingCount,
            'active_deliveries' => $activeCount,
            'completed_today' => $completedTodayCount,
            'earnings_today' => $earningsToday,
        ];

        // Fetch Today's Schedule Overview (deliveries active, updated today,
        // or assigned deliveries whose bus dispatch is arriving today)
        $todaysSchedule = RiderDelivery::where(function($q) use ($user) {
                $q->where('rider_id', $user->id)
                  ->orWhere('admin_assigned_rider_id', $user->id);
            })
            ->where(function($q) {
                $q->whereDate('updated_at', today())
                  ->orWhere('delivery_status', 'delivering')
                  ->orWhere(function($q2) {
                      $q2->where('delivery_status', 'assigned')
                         ->whereHas('busDispatch', function($qb) {
                             $qb->whereDate('estimated_arrival_time', today());
                         });
                  });
            })
            ->with(['order.buyer', 'order.orderItems', 'busDispatch'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Fetch Recent Activity (last 5 notifications)
        $recentActivity = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Fetch recent 5 deliveries assigned to the rider for the legacy panel
        $recentDeliveries = RiderDelivery::where('rider_id', $user->id)
            ->with(['order.buyer', 'order.orderItems'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('delivery-rider.dashboard', compact('user', 'rider', 'stats', 'recentDeliveries', 'todaysSchedule', 'recentActivity'));
    }

    public function toggleStatus(Request $request)
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;

        if ($rider) {
            $rider->is_online = $request->input('is_online') === 'true' || $request->input('is_online') === true || $request->input('is_online') === 1;
            $rider->save();

            return response()->json([
                'success' => true,
                'is_online' => $rider->is_online,
                'message' => $rider->is_online ? 'You are now online and available for deliveries.' : 'You are now offline.'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Rider profile not found.'], 404);
    }

    public function getNotifications()
    {
        $user = Auth::user();

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return view('delivery-rider.notifications', compact('notifications', 'unreadCount'));
    }

    public function markNotificationRead($id)
    {
        $user = Auth::user();

        $notification = Notification::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->is_read = true;
            $notification->save();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
    }

    public function markAllNotificationsAsRead()
    {
        $user = Auth::user();

        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
