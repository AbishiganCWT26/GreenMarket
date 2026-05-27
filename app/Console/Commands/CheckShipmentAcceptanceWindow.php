<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class CheckShipmentAcceptanceWindow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipment:check-acceptance-window';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Locks shipment if acceptance window closed, removes from rider incoming dashboards, triggers first admin alert';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Use 'orders' or 'shipments' table based on your schema. Using 'shipments' as per documentation.
        $tableName = 'rider_deliveries'; 
        
        try {
            // Finds all shipments where: delivery_status = 'assigned' AND claimed_by_rider_id IS NULL AND estimated_arrival_time - 45 minutes <= NOW()
            $shipments = DB::table($tableName)
                ->join('bus_dispatches', 'rider_deliveries.bus_dispatch_id', '=', 'bus_dispatches.id')
                ->join('orders', 'rider_deliveries.order_id', '=', 'orders.id')
                ->select('rider_deliveries.*', 'bus_dispatches.estimated_arrival_time as bus_estimated_arrival_time', 'orders.order_number')
                ->where('rider_deliveries.delivery_status', 'assigned')
                ->whereNull('rider_deliveries.claimed_by_rider_id')
                ->where('bus_dispatches.estimated_arrival_time', '<=', Carbon::now()->addMinutes(45))
                ->where(function($query) {
                    $query->where('rider_deliveries.acceptance_window_closed', false)
                          ->orWhereNull('rider_deliveries.acceptance_window_closed');
                })
                ->get();

            foreach ($shipments as $shipment) {
                // Locks shipment (sets acceptance_window_closed = true)
                DB::table($tableName)
                    ->where('id', $shipment->id)
                    ->update(['acceptance_window_closed' => true]);

                // Triggers first admin alert
                $admins = User::where('role', 'admin')->get();
                if ($admins->isNotEmpty()) {
                    $alertData = [
                        'order_number' => $shipment->order_number ?? $shipment->id,
                        'shipment_id' => $shipment->id,
                        'bus_eta' => $shipment->bus_estimated_arrival_time,
                        'minutes_remaining' => 45 // Initial alert at exactly 45 min mark
                    ];
                    
                    foreach ($admins as $admin) {
                        \App\Models\Notification::create([
                            'user_id' => $admin->id,
                            'recipient_type' => 'user',
                            'title' => 'Shipment Unassigned',
                            'message' => "Order #{$alertData['order_number']} is unassigned. Bus arriving in {$alertData['minutes_remaining']} minutes.",
                            'notification_type' => 'system',
                            'is_read' => false,
                            'related_id' => $shipment->id,
                        ]);
                    }
                }

                $this->info("Shipment {$shipment->id} locked. Initial admin alert sent.");
            }

            $this->info('Checked shipment acceptance windows successfully.');
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
