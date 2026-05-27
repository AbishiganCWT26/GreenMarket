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
        Schema::create('bus_dispatches', function (Blueprint $table) {
            $table->id();
            $table->string('bus_number');
            $table->string('bus_image')->nullable();
            $table->string('conductor_mobile');
            $table->string('conductor_name');
            $table->dateTime('estimated_arrival_time');
            $table->string('dispatch_status')->default('in_transit');
            $table->foreignId('lead_farmer_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('dispatch_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_dispatch_id')->constrained('bus_dispatches')->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('rider_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_dispatch_id')->constrained('bus_dispatches')->onDelete('cascade');
            $table->foreignId('rider_id')->nullable()->constrained('users');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('delivery_status')->default('assigned');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rider_deliveries');
        Schema::dropIfExists('dispatch_products');
        Schema::dropIfExists('bus_dispatches');
    }
};
