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
        // Drop the unique constraint on payment_reference in the payments table
        DB::statement('ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_payment_reference_key');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add the unique constraint if rolling back
        DB::statement('ALTER TABLE payments ADD CONSTRAINT payments_payment_reference_key UNIQUE (payment_reference)');
    }
};
