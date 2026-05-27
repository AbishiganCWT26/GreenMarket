<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HandleTZeroCrisisFallback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipment:handle-t-zero-crisis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handles shipments that have reached bus ETA without being assigned a rider.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tableName = 'rider_deliveries'; 
        
        try {
            // Finds all shipments where: bus_estimated_arrival_time <= NOW() AND admin_assigned_rider_id IS NULL AND claimed_by_rider_id IS NULL
            $shipments = DB::table($tableName)
                ->join('bus_dispatches', 'rider_deliveries.bus_dispatch_id', '=', 'bus_dispatches.id')
                ->join('orders', 'rider_deliveries.order_id', '=', 'orders.id')
                ->select('rider_deliveries.*', 'bus_dispatches.estimated_arrival_time as bus_estimated_arrival_time', 'orders.order_number')
                ->where('bus_dispatches.estimated_arrival_time', '<=', now())
                ->whereNull('rider_deliveries.admin_assigned_rider_id')
                ->whereNull('rider_deliveries.claimed_by_rider_id')
                ->where('rider_deliveries.delivery_status', 'assigned') // Ensure it is still in assigned state
                ->get();

            foreach ($shipments as $shipment) {
                
                // Executes configured fallback approach
                $this->executeFallbackStrategy($shipment);

                Log::alert("T-Zero Crisis Fallback executed for shipment {$shipment->id}");
                $this->info("T-Zero Crisis Fallback executed for shipment {$shipment->id}");
            }

            $this->info('T-Zero Crisis checks completed.');
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }

    /**
     * Executes the specific fallback approach.
     * Structure is ready to accommodate any of the six options.
     */
    protected function executeFallbackStrategy($shipment)
    {
        // Specific fallback approach (1-6) will be implemented after client selection.
        $fallbackOption = config('app.t_zero_fallback_option', 'default');
        
        switch ($fallbackOption) {
            case 1:
                // Option 1
                break;
            case 2:
                // Option 2
                break;
            case 3:
                // Option 3
                break;
            case 4:
                // Option 4
                break;
            case 5:
                // Option 5
                break;
            case 6:
                // Option 6
                break;
            default:
                // Default action: Update order status accordingly and send final notifications
                DB::table('rider_deliveries')
                    ->where('id', $shipment->id)
                    ->update(['delivery_status' => 'crisis_unassigned']);
                
                // Final notification logic could be added here
                break;
        }
    }
}
