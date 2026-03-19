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
        Schema::table('farmers', function (Blueprint $table) {
            $table->string('divisional_secretariat')->nullable()->after('district');
            $table->string('gn_division_code')->nullable()->after('grama_niladhari_division');
        });

        Schema::table('lead_farmers', function (Blueprint $table) {
            $table->string('divisional_secretariat')->nullable()->after('district');
            $table->string('gn_division_code')->nullable()->after('grama_niladhari_division');
        });

        Schema::table('facilitators', function (Blueprint $table) {
            $table->string('divisional_secretariat')->nullable()->after('assigned_division');
            $table->string('gn_division_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farmers', function (Blueprint $table) {
            $table->dropColumn(['divisional_secretariat', 'gn_division_code']);
        });

        Schema::table('lead_farmers', function (Blueprint $table) {
            $table->dropColumn(['divisional_secretariat', 'gn_division_code']);
        });

        Schema::table('facilitators', function (Blueprint $table) {
            $table->dropColumn(['divisional_secretariat', 'gn_division_code']);
        });
    }
};
