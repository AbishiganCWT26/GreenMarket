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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_type', 20)->default('Pickup')->after('id');
        });

        // Adding check constraint using raw SQL as Laravel Blueprint doesn't natively support check constraints for strings easily across all DBs without raw SQL
        DB::statement("ALTER TABLE orders ADD CONSTRAINT order_type_check CHECK (order_type IN ('Pickup', 'Delivery'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('order_type');
        });
    }
};
