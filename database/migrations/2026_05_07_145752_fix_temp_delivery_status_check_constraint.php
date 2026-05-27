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
        // Drop the existing constraint that only allows 'pending'
        DB::statement('ALTER TABLE temporary_delivery_order_items DROP CONSTRAINT IF EXISTS temp_delivery_status_check');

        // Add the new constraint with 'Processing order'
        DB::statement("ALTER TABLE temporary_delivery_order_items ADD CONSTRAINT temp_delivery_status_check CHECK (order_status = 'Processing order')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE temporary_delivery_order_items DROP CONSTRAINT IF EXISTS temp_delivery_status_check');
        
        DB::statement("ALTER TABLE temporary_delivery_order_items ADD CONSTRAINT temp_delivery_status_check CHECK (order_status = 'pending')");
    }
};
