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
        // Increase the length of notification_type column to 50
        // Using DB::statement for PostgreSQL compatibility as seen in other migrations
        DB::statement('ALTER TABLE notifications ALTER COLUMN notification_type TYPE VARCHAR(50)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to 20 if needed, but be careful as existing data might be truncated
        DB::statement('ALTER TABLE notifications ALTER COLUMN notification_type TYPE VARCHAR(20)');
    }
};
