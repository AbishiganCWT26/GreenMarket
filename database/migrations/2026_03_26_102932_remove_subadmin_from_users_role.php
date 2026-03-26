<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveSubadminFromUsersRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the constraint with subadmin and restore original
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');

        // Restore original constraint without subadmin
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK ((role::text = ANY (ARRAY['farmer'::character varying, 'lead_farmer'::character varying, 'buyer'::character varying, 'facilitator'::character varying, 'admin'::character varying]::text[])))");

        // If there are any users with 'subadmin' role, reassign them to 'admin' (optional, safety)
        DB::table('users')->where('role', 'subadmin')->update(['role' => 'admin']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');

        // Add back the subadmin role to the constraint
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK ((role::text = ANY (ARRAY['farmer'::character varying, 'lead_farmer'::character varying, 'buyer'::character varying, 'facilitator'::character varying, 'admin'::character varying, 'subadmin'::character varying]::text[])))");
    }
}
