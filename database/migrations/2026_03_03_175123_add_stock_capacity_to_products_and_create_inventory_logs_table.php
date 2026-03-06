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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('stock_capacity', 12, 2)->default(0)->after('quantity');
            $table->decimal('low_stock_threshold_percent', 5, 2)->default(20)->after('stock_capacity');
        });

        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->decimal('quantity_change', 12, 2);
            $table->decimal('new_quantity', 12, 2);
            $table->string('type'); // manual_add, manual_reduce, order_placed, etc.
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock_capacity', 'low_stock_threshold_percent']);
        });
    }
};
