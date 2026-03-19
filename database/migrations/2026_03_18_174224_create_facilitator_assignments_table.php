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
        Schema::create('facilitator_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facilitator_id')->constrained('facilitators')->onDelete('cascade');
            $table->string('district');
            $table->string('divisional_secretariat');
            $table->string('gn_division');
            $table->string('gn_division_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilitator_assignments');
    }
};
