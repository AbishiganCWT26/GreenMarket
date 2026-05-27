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
        Schema::create('temporary_delivery_order_items', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->string('order_number', 50);
            $table->integer('buyer_id');
            $table->integer('farmer_id');
            $table->integer('lead_farmer_id');
            $table->integer('product_id');
            $table->string('product_name_snapshot', 255);
            $table->decimal('quantity_ordered', 10, 2);
            $table->decimal('unit_price_snapshot', 10, 2);
            $table->decimal('item_total', 10, 2);
            $table->string('order_status', 20)->default('pending');
            $table->timestamps();

            $table->unique(['order_id', 'product_id'], 'temp_delivery_unique');
        });

        // Adding check constraint for order_status
        DB::statement("ALTER TABLE temporary_delivery_order_items ADD CONSTRAINT temp_delivery_status_check CHECK (order_status = 'pending')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_delivery_order_items');
    }
};
