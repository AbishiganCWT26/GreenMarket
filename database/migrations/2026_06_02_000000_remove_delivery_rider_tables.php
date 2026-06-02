<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('rider_deliveries');
        Schema::dropIfExists('delivery_riders');
        
        // Let's also drop any table reference to dispatch_products?
        // Wait, dispatch_products might be tied to bus_dispatches. I will leave it alone if not strictly delivery rider.
    }

    public function down(): void
    {
        // Cannot reverse deletion easily without re-creating all tables
    }
};
