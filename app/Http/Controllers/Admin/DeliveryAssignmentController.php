<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\NewShipmentNotification;
use Carbon\Carbon;

class DeliveryAssignmentController extends Controller
{
    /**
     * Returns the assignment page with unassigned orders.
     */
    public function index()
    {
        // Unassigned orders: acceptance_window_closed = true AND admin_assigned_rider_id IS NULL
        $unassignedOrders = DB::table('rider_deliveries')
            ->join('bus_dispatches', 'rider_deliveries.bus_dispatch_id', '=', 'bus_dispatches.id')
            ->join('orders', 'rider_deliveries.order_id', '=', 'orders.id')
            ->leftJoin('buyers', 'orders.buyer_id', '=', 'buyers.id')
            ->select(
                'rider_deliveries.*',
                'orders.order_number',
                'orders.total_amount',
                'buyers.name as buyer_name',
                'buyers.primary_mobile as buyer_phone',
                'buyers.residential_address as delivery_address',
                'buyers.district as district',
                'bus_dispatches.estimated_arrival_time as bus_estimated_arrival_time',
                'bus_dispatches.bus_number as bus_details'
            )
            ->where('rider_deliveries.acceptance_window_closed', true)
            ->whereNull('rider_deliveries.admin_assigned_rider_id')
            ->orderBy('bus_dispatches.estimated_arrival_time', 'asc')
            ->get()
            ->map(function ($order) {
                // Fetch product details
                $items = DB::table('order_items')->where('order_id', $order->order_id)->get();
                $order->product_details = $items->map(function($item) {
                    return $item->product_name_snapshot . ' (x' . $item->quantity_ordered . ')';
                })->implode(', ');

                $eta = Carbon::parse($order->bus_estimated_arrival_time);
                $now = Carbon::now();
                $order->minutes_remaining = $now->diffInMinutes($eta, false);
                
                // Urgency level
                if ($order->minutes_remaining < 0) {
                    $order->urgency = 'critical'; // Past ETA
                } elseif ($order->minutes_remaining < 10) {
                    $order->urgency = 'high';
                } elseif ($order->minutes_remaining <= 30) {
                    $order->urgency = 'medium';
                } else {
                    $order->urgency = 'low';
                }
                
                return $order;
            });

        // Assignment history: recent manual assignments
        $assignmentHistory = DB::table('rider_deliveries')
            ->join('orders', 'rider_deliveries.order_id', '=', 'orders.id')
            ->leftJoin('buyers', 'orders.buyer_id', '=', 'buyers.user_id')
            ->select('rider_deliveries.*', 'orders.order_number', 'buyers.district')
            ->whereNotNull('rider_deliveries.admin_assigned_rider_id')
            ->whereNotNull('rider_deliveries.assigned_by_admin_at')
            ->orderBy('rider_deliveries.assigned_by_admin_at', 'desc')
            ->limit(50)
            ->get();

        // Get distinct districts for filter
        $districts = DB::table('rider_deliveries')
            ->join('orders', 'rider_deliveries.order_id', '=', 'orders.id')
            ->join('buyers', 'orders.buyer_id', '=', 'buyers.user_id')
            ->where('rider_deliveries.acceptance_window_closed', true)
            ->whereNull('rider_deliveries.admin_assigned_rider_id')
            ->distinct()
            ->pluck('buyers.district');

        return view('admin.delivery-rider-assignment', compact(
            'unassignedOrders',
            'assignmentHistory',
            'districts'
        ));
    }

    /**
     * Validates rider selection, assigns rider to order, triggers notifications, stops escalation.
     */
    public function assign(Request $request)
    {
        $request->validate([
            'shipment_id' => 'required|integer',
            'rider_id' => 'required|integer',
        ]);

        try {
            DB::beginTransaction();

            $shipment = DB::table('rider_deliveries')
                ->join('bus_dispatches', 'rider_deliveries.bus_dispatch_id', '=', 'bus_dispatches.id')
                ->join('orders', 'rider_deliveries.order_id', '=', 'orders.id')
                ->leftJoin('buyers', 'orders.buyer_id', '=', 'buyers.id')
                ->select(
                    'rider_deliveries.*', 
                    'orders.order_number',
                    'orders.total_amount',
                    'buyers.name as buyer_name',
                    'buyers.primary_mobile as buyer_phone',
                    'buyers.residential_address as delivery_address',
                    'buyers.district as district', 
                    'bus_dispatches.estimated_arrival_time as bus_estimated_arrival_time'
                )
                ->where('rider_deliveries.id', $request->shipment_id)
                ->first();

            if (!$shipment) {
                return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
            }

            if ($shipment->admin_assigned_rider_id) {
                return response()->json(['success' => false, 'message' => 'This shipment has already been assigned.'], 409);
            }

            // Assign rider
            DB::table('rider_deliveries')
                ->where('id', $request->shipment_id)
                ->update([
                    'admin_assigned_rider_id' => $request->rider_id,
                    'assigned_by_admin_at' => Carbon::now(),
                    'delivery_status' => 'rider_assigned',
                ]);

            // Send notification to assigned rider
            $rider = User::find($request->rider_id);
            if ($rider) {
                $orderNumber = $shipment->order_number ?? $shipment->id;
                $eta = \Carbon\Carbon::parse($shipment->bus_estimated_arrival_time)->format('h:i A');
                $buyerName = $shipment->buyer_name ?? 'N/A';
                $buyerPhone = $shipment->buyer_phone ?? 'N/A';
                $deliveryAddress = $shipment->delivery_address ?? 'N/A';
                $district = $shipment->district ?? 'N/A';
                $totalAmount = number_format($shipment->total_amount ?? 0, 2);

                // 1. System Notification
                \App\Models\Notification::create([
                    'user_id' => $rider->id,
                    'recipient_type' => 'user',
                    'title' => 'New Delivery Assigned',
                    'message' => "Order #{$orderNumber} assigned to you. Buyer: {$buyerName}, District: {$district}, ETA: {$eta}",
                    'notification_type' => 'system',
                    'is_read' => false,
                    'related_id' => $request->shipment_id,
                ]);

                // 2. SMS Notification
                $riderPhone = DB::table('delivery_riders')
                    ->where('user_id', $rider->id)
                    ->value('primary_mobile');

                if ($riderPhone) {
                    $smsMessage = "GreenMarket: New delivery assigned! Order #{$orderNumber}. "
                        . "Buyer: {$buyerName} ({$buyerPhone}). "
                        . "Address: {$deliveryAddress}, {$district}. "
                        . "Amount: Rs.{$totalAmount}. "
                        . "Bus ETA: {$eta}. "
                        . "Please pick up and deliver.";
                    
                    try {
                        $this->sendSMS($riderPhone, $smsMessage);
                    } catch (\Exception $smsEx) {
                        \Log::warning('SMS failed for rider assignment: ' . $smsEx->getMessage());
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rider assigned successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Assignment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX endpoint returning riders in a district with current load.
     */
    public function getAvailableRiders($district)
    {
        $riders = DB::table('users')
            ->join('delivery_riders', 'users.id', '=', 'delivery_riders.user_id')
            ->where('users.role', 'delivery_rider')
            ->where('users.is_active', true)
            ->where('delivery_riders.is_online', true)
            ->whereJsonContains('delivery_riders.assigned_districts', $district)
            ->select(
                'users.id',
                'delivery_riders.name',
                'delivery_riders.primary_mobile as phone',
                'delivery_riders.vehicle_type',
                'delivery_riders.assigned_districts',
                DB::raw("(SELECT COUNT(*) FROM rider_deliveries WHERE rider_deliveries.admin_assigned_rider_id = users.id AND rider_deliveries.delivery_status IN ('rider_assigned', 'in_transit')) as current_load")
            )
            ->get();

        return response()->json(['riders' => $riders]);
    }

    /**
     * AJAX endpoint returning ALL available riders (for bulk assignment dropdown).
     */
    public function getAllAvailableRiders()
    {
        $riders = DB::table('users')
            ->join('delivery_riders', 'users.id', '=', 'delivery_riders.user_id')
            ->where('users.role', 'delivery_rider')
            ->where('users.is_active', true)
            ->where('delivery_riders.is_online', true)
            ->select(
                'users.id',
                'delivery_riders.name',
                'delivery_riders.primary_mobile as phone',
                'delivery_riders.vehicle_type',
                'delivery_riders.assigned_districts',
                DB::raw("(SELECT COUNT(*) FROM rider_deliveries WHERE rider_deliveries.admin_assigned_rider_id = users.id AND rider_deliveries.delivery_status IN ('rider_assigned', 'in_transit')) as current_load")
            )
            ->get();

        return response()->json(['riders' => $riders]);
    }

    /**
     * Reassigns to a different rider if needed.
     */
    public function reassign(Request $request)
    {
        $request->validate([
            'shipment_id' => 'required|integer',
            'rider_id' => 'required|integer',
        ]);

        try {
            DB::beginTransaction();

            $shipment = DB::table('rider_deliveries')
                ->join('bus_dispatches', 'rider_deliveries.bus_dispatch_id', '=', 'bus_dispatches.id')
                ->join('orders', 'rider_deliveries.order_id', '=', 'orders.id')
                ->leftJoin('buyers', 'orders.buyer_id', '=', 'buyers.id')
                ->select(
                    'rider_deliveries.*', 
                    'orders.order_number',
                    'orders.total_amount',
                    'buyers.name as buyer_name',
                    'buyers.primary_mobile as buyer_phone',
                    'buyers.residential_address as delivery_address',
                    'buyers.district as district', 
                    'bus_dispatches.estimated_arrival_time as bus_estimated_arrival_time'
                )
                ->where('rider_deliveries.id', $request->shipment_id)
                ->first();

            if (!$shipment) {
                return response()->json(['success' => false, 'message' => 'Shipment not found.'], 404);
            }

            $previousRiderId = $shipment->admin_assigned_rider_id;

            // Reassign rider
            DB::table('rider_deliveries')
                ->where('id', $request->shipment_id)
                ->update([
                    'admin_assigned_rider_id' => $request->rider_id,
                    'assigned_by_admin_at' => Carbon::now(),
                ]);

            // Notify the new rider
            $rider = User::find($request->rider_id);
            if ($rider) {
                $orderNumber = $shipment->order_number ?? $shipment->id;
                $eta = \Carbon\Carbon::parse($shipment->bus_estimated_arrival_time)->format('h:i A');
                $buyerName = $shipment->buyer_name ?? 'N/A';
                $buyerPhone = $shipment->buyer_phone ?? 'N/A';
                $deliveryAddress = $shipment->delivery_address ?? 'N/A';
                $district = $shipment->district ?? 'N/A';
                $totalAmount = number_format($shipment->total_amount ?? 0, 2);

                // 1. System Notification
                \App\Models\Notification::create([
                    'user_id' => $rider->id,
                    'recipient_type' => 'user',
                    'title' => 'New Delivery Assigned (Reassigned)',
                    'message' => "Order #{$orderNumber} reassigned to you. Buyer: {$buyerName}, District: {$district}, ETA: {$eta}",
                    'notification_type' => 'system',
                    'is_read' => false,
                    'related_id' => $request->shipment_id,
                ]);

                // 2. SMS Notification
                $riderPhone = DB::table('delivery_riders')
                    ->where('user_id', $rider->id)
                    ->value('primary_mobile');

                if ($riderPhone) {
                    $smsMessage = "GreenMarket: Delivery reassigned! Order #{$orderNumber}. "
                        . "Buyer: {$buyerName} ({$buyerPhone}). "
                        . "Address: {$deliveryAddress}, {$district}. "
                        . "Amount: Rs.{$totalAmount}. "
                        . "Bus ETA: {$eta}. "
                        . "Please pick up and deliver.";
                    
                    try {
                        $this->sendSMS($riderPhone, $smsMessage);
                    } catch (\Exception $smsEx) {
                        \Log::warning('SMS failed for rider reassignment: ' . $smsEx->getMessage());
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rider reassigned successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Reassignment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send SMS via textit.biz gateway.
     */
    private function sendSMS($to, $message)
    {
        try {
            $user = env('SMS_USER');
            $password = env('SMS_PASSWORD');
            $baseurl = env('SMS_API_URL', 'https://textit.biz/sendmsg');

            $to = preg_replace('/[^0-9]/', '', $to);
            $text = urlencode($message);
            
            $baseurl = rtrim($baseurl, '/') . '/';
            $url = $baseurl . "?id=" . $user . "&pw=" . $password . "&to=" . $to . "&text=" . $text;
            
            $ret = $this->get_web_page($url);
            $res = explode(":", $ret);
            
            if (trim($res[0]) == "OK") {
                \Log::info("SMS Sent successfully to $to. Response: $ret");
                return true;
            } else {
                \Log::error("SMS Sending Failed to $to. Response: $ret");
                return false;
            }
        } catch (\Exception $e) {
            \Log::error('SMS Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * cURL helper for SMS gateway.
     */
    private function get_web_page($url)
    {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        curl_close($ch);

        return $content;
    }
}
