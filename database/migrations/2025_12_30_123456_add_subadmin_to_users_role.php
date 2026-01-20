<?php
// File: database/migrations/2024_01_01_000000_add_subadmin_to_users_role.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddSubadminToUsersRole extends Migration
{
    public function up()
    {
        // First, we need to drop the constraint, modify it, and add it back
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');

        // Add the new constraint with subadmin role
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK ((role::text = ANY (ARRAY['farmer'::character varying, 'lead_farmer'::character varying, 'buyer'::character varying, 'facilitator'::character varying, 'admin'::character varying, 'subadmin'::character varying]::text[])))");
    }

    public function down()
    {
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');

        // Restore original constraint
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK ((role::text = ANY (ARRAY['farmer'::character varying, 'lead_farmer'::character varying, 'buyer'::character varying, 'facilitator'::character varying, 'admin'::character varying]::text[])))");
    }
}
