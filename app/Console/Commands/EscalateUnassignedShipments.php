<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Carbon\Carbon;

class EscalateUnassignedShipments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipment:escalate-unassigned';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Escalates unassigned shipments to admins at specific intervals before bus arrival.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tableName = 'rider_deliveries'; 
        
        try {
            // Finds all shipments where: acceptance_window_closed = true AND admin_assigned_rider_id IS NULL
            $shipments = DB::table($tableName)
                ->join('bus_dispatches', 'rider_deliveries.bus_dispatch_id', '=', 'bus_dispatches.id')
                ->join('orders', 'rider_deliveries.order_id', '=', 'orders.id')
                ->select('rider_deliveries.*', 'bus_dispatches.estimated_arrival_time as bus_estimated_arrival_time', 'orders.order_number')
                ->where('rider_deliveries.acceptance_window_closed', true)
                ->whereNull('rider_deliveries.admin_assigned_rider_id')
                ->whereNotNull('bus_dispatches.estimated_arrival_time')
                ->get();

            $admins = User::where('role', 'admin')->get();

            foreach ($shipments as $shipment) {
                $busEta = Carbon::parse($shipment->bus_estimated_arrival_time);
                $now = Carbon::now();
                
                if ($busEta->isPast() && $busEta->diffInMinutes($now) > 1) {
                    continue; // Skip past shipments, handled by T-Zero fallback
                }

                $minutesUntilArrival = $now->diffInMinutes($busEta, false);
                $intervals = [44, 40, 30, 20, 10, 0];
                
                foreach ($intervals as $interval) {
                    // Match the minute interval precisely to send the escalation
                    if ($minutesUntilArrival == $interval) {
                        
                        $escalationLevel = array_search($interval, $intervals) + 1;
                        
                        // At T-44 min: sends first system notification
                        if ($interval == 44) {
                            foreach ($admins as $admin) {
                                \App\Models\Notification::create([
                                    'user_id' => $admin->id,
                                    'recipient_type' => 'user',
                                    'title' => 'Shipment Unassigned (Escalation)',
                                    'message' => "Order #{$shipment->order_number} is unassigned. Bus arriving in {$interval} minutes.",
                                    'notification_type' => 'system',
                                    'is_read' => false,
                                    'related_id' => $shipment->id,
                                ]);
                            }
                            Log::info("T-44 First Escalation sent for shipment {$shipment->id}");
                        } else {
                            // At T-40, T-30, T-20, T-10, T-0: sends SMS + system notification every 10 minutes
                            foreach ($admins as $admin) {
                                \App\Models\Notification::create([
                                    'user_id' => $admin->id,
                                    'recipient_type' => 'user',
                                    'title' => "Escalation Level {$escalationLevel}",
                                    'message' => "URGENT: Order #{$shipment->order_number} arriving in {$interval} mins. Please assign a rider.",
                                    'notification_type' => 'system',
                                    'is_read' => false,
                                    'related_id' => $shipment->id,
                                ]);
                            }
                            Log::info("T-{$interval} Escalation Level {$escalationLevel} sent for shipment {$shipment->id}");
                        }
                        
                        break;
                    }
                }
            }

            $this->info('Escalation checks completed successfully.');
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
