<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasswordHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('password_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('password_hash');
            $table->timestamp('changed_at');
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('change_reason')->default('password_reset');
            $table->timestamps();

            $table->index('user_id');
            $table->index('changed_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('password_history');
    }
}
