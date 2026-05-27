<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing constraint
        DB::statement('ALTER TABLE notifications DROP CONSTRAINT IF EXISTS notifications_notification_type_check');

        // Add updated constraint with new delivery-related notification types
        DB::statement("ALTER TABLE notifications ADD CONSTRAINT notifications_notification_type_check 
            CHECK (notification_type IN (
                'order_payment', 
                'admin_alert', 
                'system', 
                'payment_confirmation', 
                'payment_received', 
                'ready_for_pickup', 
                'delivery_order_received', 
                'order_confirmed', 
                'payment_rejected', 
                'dispatch'
            ))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE notifications DROP CONSTRAINT IF EXISTS notifications_notification_type_check');

        // Restore original constraint
        DB::statement("ALTER TABLE notifications ADD CONSTRAINT notifications_notification_type_check 
            CHECK (notification_type IN (
                'order_payment', 
                'admin_alert', 
                'system', 
                'payment_confirmation', 
                'payment_received', 
                'ready_for_pickup'
            ))");
    }
};
