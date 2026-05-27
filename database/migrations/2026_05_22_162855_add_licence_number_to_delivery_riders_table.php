<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds licence_number to delivery_riders table.
     */
    public function up(): void
    {
        Schema::table('delivery_riders', function (Blueprint $table) {
            $table->string('licence_number')->nullable()->after('nic_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_riders', function (Blueprint $table) {
            $table->dropColumn('licence_number');
        });
    }
};
