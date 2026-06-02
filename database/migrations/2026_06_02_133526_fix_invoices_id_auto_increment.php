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
        // For PostgreSQL: create sequence, set default, and sync the sequence with max(id)
        DB::statement("CREATE SEQUENCE IF NOT EXISTS invoices_id_seq");
        DB::statement("ALTER TABLE invoices ALTER COLUMN id SET DEFAULT nextval('invoices_id_seq')");
        DB::statement("ALTER SEQUENCE invoices_id_seq OWNED BY invoices.id");
        
        // Sync sequence with the current maximum ID to avoid duplicate key errors
        DB::statement("SELECT setval('invoices_id_seq', (SELECT COALESCE(MAX(id), 0) + 1 FROM invoices), false)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE invoices ALTER COLUMN id DROP DEFAULT");
        DB::statement("DROP SEQUENCE IF EXISTS invoices_id_seq");
    }
};
