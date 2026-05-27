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
        Schema::table('payment_delivery_order', function (Blueprint $table) {
            // Rename payment_slip_name to payment_slip_path if it exists
            if (Schema::hasColumn('payment_delivery_order', 'payment_slip_name') && !Schema::hasColumn('payment_delivery_order', 'payment_slip_path')) {
                $table->renameColumn('payment_slip_name', 'payment_slip_path');
            }

            // Add payment_status if it doesn't exist
            if (!Schema::hasColumn('payment_delivery_order', 'payment_status')) {
                $table->string('payment_status', 255)->default('Payment Pending')->after('payment_slip_path');
            }

            // Add optional resubmission tracking columns
            if (!Schema::hasColumn('payment_delivery_order', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('payment_delivery_order', 'resubmission_count')) {
                $table->integer('resubmission_count')->default(0)->after('rejection_reason');
            }
            if (!Schema::hasColumn('payment_delivery_order', 'last_resubmitted_at')) {
                $table->timestamp('last_resubmitted_at')->nullable()->after('resubmission_count');
            }
        });

        // Add the CHECK constraint
        // First drop if exists (though it likely doesn't yet)
        DB::statement('ALTER TABLE payment_delivery_order DROP CONSTRAINT IF EXISTS payment_delivery_order_status_check');
        
        DB::statement("ALTER TABLE payment_delivery_order ADD CONSTRAINT payment_delivery_order_status_check 
            CHECK (payment_status IN ('Payment Pending', 'awaiting_verification', 'confirmed', 'rejected', 'resubmitted'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE payment_delivery_order DROP CONSTRAINT IF EXISTS payment_delivery_order_status_check');

        Schema::table('payment_delivery_order', function (Blueprint $table) {
            $table->dropColumn(['last_resubmitted_at', 'resubmission_count', 'rejection_reason']);
            
            // Note: We don't necessarily want to drop payment_status if it was already there, 
            // but since we added it in 'up', we should probably remove it in 'down' if we want a full rollback.
            // However, renameColumn is tricky to reverse if we don't know the original state.
            // For safety, we'll just drop the columns we added.
            $table->dropColumn('payment_status');
            
            if (Schema::hasColumn('payment_delivery_order', 'payment_slip_path')) {
                $table->renameColumn('payment_slip_path', 'payment_slip_name');
            }
        });
    }
};
