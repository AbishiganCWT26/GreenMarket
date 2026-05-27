<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rider_deliveries', function (Blueprint $table) {
            $table->timestamp('pickup_confirmed_at')->nullable()->after('escalation_level');
        });
    }

    public function down(): void
    {
        Schema::table('rider_deliveries', function (Blueprint $table) {
            $table->dropColumn('pickup_confirmed_at');
        });
    }
};
