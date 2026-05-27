<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing constraint
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_order_status_check');

        // Add updated constraint including new tracking statuses
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_order_status_check 
            CHECK (order_status IN (
                'Processing order', 
                'confirmed', 
                'paid', 
                'ready_for_pickup', 
                'Dispatched', 
                'completed', 
                'cancelled', 
                'refunded', 
                'Payment Pending', 
                'awaiting_verification',
                'arrived_to_district',
                'Order_is_on_the_way'
            ))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_order_status_check');

        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_order_status_check 
            CHECK (order_status IN (
                'Processing order', 
                'confirmed', 
                'paid', 
                'ready_for_pickup', 
                'Dispatched', 
                'completed', 
                'cancelled', 
                'refunded', 
                'Payment Pending', 
                'awaiting_verification'
            ))");
    }
};
