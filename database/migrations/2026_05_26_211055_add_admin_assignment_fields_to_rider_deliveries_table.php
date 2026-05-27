<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rider_deliveries', function (Blueprint $table) {
            $table->boolean('acceptance_window_closed')->default(false)->after('claimed_at');
            $table->unsignedBigInteger('admin_assigned_rider_id')->nullable()->after('acceptance_window_closed');
            $table->timestamp('assigned_by_admin_at')->nullable()->after('admin_assigned_rider_id');
            $table->integer('escalation_level')->default(0)->after('assigned_by_admin_at');

            $table->foreign('admin_assigned_rider_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rider_deliveries', function (Blueprint $table) {
            $table->dropForeign(['admin_assigned_rider_id']);
            $table->dropColumn([
                'acceptance_window_closed',
                'admin_assigned_rider_id',
                'assigned_by_admin_at',
                'escalation_level'
            ]);
        });
    }
};
