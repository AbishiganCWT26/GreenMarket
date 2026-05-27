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
        Schema::create('payment_delivery_order', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('transaction_id', 255);
            $table->date('transaction_date');
            $table->time('transaction_time');
            $table->string('payment_slip_name', 255);
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('order_id', 'fk_payment_delivery_order_id')
                  ->references('id')
                  ->on('orders')
                  ->onDelete('cascade');
            
            $table->index('order_id', 'idx_payment_delivery_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_delivery_order');
    }
};
