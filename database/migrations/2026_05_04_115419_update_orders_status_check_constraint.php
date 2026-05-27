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

        // Add updated constraint
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_order_status_check 
            CHECK (order_status IN ('pending', 'confirmed', 'paid', 'ready_for_pickup', 'completed', 'cancelled', 'refunded', 'Payment Pending', 'awaiting_verification'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop updated constraint
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_order_status_check');

        // Re-add original constraint
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_order_status_check 
            CHECK (order_status IN ('pending', 'confirmed', 'paid', 'ready_for_pickup', 'completed', 'cancelled', 'refunded'))");
    }
};
