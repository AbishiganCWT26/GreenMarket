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
        // First drop the existing constraint
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_order_status_check');

        // Update existing data
        DB::table('orders')->where('order_status', 'pending')->update(['order_status' => 'Processing order']);
        DB::table('temporary_delivery_order_items')->where('order_status', 'pending')->update(['order_status' => 'Processing order']);

        // Add the new constraint with 'Processing order'
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_order_status_check 
            CHECK (order_status IN ('Processing order', 'confirmed', 'paid', 'ready_for_pickup', 'completed', 'cancelled', 'refunded', 'Payment Pending', 'awaiting_verification'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_order_status_check');

        DB::table('orders')->where('order_status', 'Processing order')->update(['order_status' => 'pending']);
        DB::table('temporary_delivery_order_items')->where('order_status', 'Processing order')->update(['order_status' => 'pending']);

        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_order_status_check 
            CHECK (order_status IN ('pending', 'confirmed', 'paid', 'ready_for_pickup', 'completed', 'cancelled', 'refunded', 'Payment Pending', 'awaiting_verification'))");
    }
};
