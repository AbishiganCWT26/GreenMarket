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
            $table->unsignedBigInteger('claimed_by_rider_id')->nullable()->after('rider_id');
            $table->timestamp('claimed_at')->nullable()->after('claimed_by_rider_id');
            
            $table->foreign('claimed_by_rider_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rider_deliveries', function (Blueprint $table) {
            $table->dropForeign(['claimed_by_rider_id']);
            $table->dropColumn(['claimed_by_rider_id', 'claimed_at']);
        });
    }
};
