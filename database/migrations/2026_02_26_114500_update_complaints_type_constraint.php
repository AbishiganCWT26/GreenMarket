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
        // PostgreSQL specific: Update the check constraint for complaint_type
        // We need to drop the old constraint and add the new one
        DB::statement('ALTER TABLE complaints DROP CONSTRAINT IF EXISTS complaints_complaint_type_check');
        
        DB::statement("ALTER TABLE complaints ADD CONSTRAINT complaints_complaint_type_check 
            CHECK (complaint_type::text = ANY (ARRAY[
                'product_quality'::text, 
                'wrong_location'::text, 
                'farmer_contact'::text, 
                'availability_issue'::text, 
                'payment_issue'::text, 
                'invoice_error'::text, 
                'category_misclassification'::text, 
                'farmer_no_show'::text, 
                'product_photo_mismatch'::text, 
                'request_ignored'::text, 
                'filter_issue'::text, 
                'vague_instructions'::text, 
                'payment_technical'::text, 
                'payment_delay'::text, 
                'payment_missing'::text, 
                'wrong_data_entry'::text, 
                'other'::text
            ]::text[]))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the original constraint if rolled back
        DB::statement('ALTER TABLE complaints DROP CONSTRAINT IF EXISTS complaints_complaint_type_check');
        
        DB::statement("ALTER TABLE complaints ADD CONSTRAINT complaints_complaint_type_check 
            CHECK (complaint_type::text = ANY (ARRAY[
                'product_quality'::text, 
                'wrong_location'::text, 
                'farmer_contact'::text, 
                'availability_issue'::text, 
                'payment_issue'::text, 
                'invoice_error'::text, 
                'category_misclassification'::text, 
                'farmer_no_show'::text, 
                'product_photo_mismatch'::text, 
                'request_ignored'::text, 
                'filter_issue'::text, 
                'vague_instructions'::text, 
                'payment_technical'::text, 
                'other'::text
            ]::text[]))");
    }
};
