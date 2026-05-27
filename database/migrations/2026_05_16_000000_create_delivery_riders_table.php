<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('delivery_riders', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained('users')->onDelete('cascade');
			$table->string('name');
			$table->string('nic_no');
			$table->string('primary_mobile');
			$table->string('email')->nullable();
			$table->string('vehicle_number');
			$table->string('vehicle_type');
			$table->integer('max_kg_capacity');
			$table->string('whatsapp_number')->nullable();
			$table->text('residential_address')->nullable();
			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('delivery_riders');
	}
};
