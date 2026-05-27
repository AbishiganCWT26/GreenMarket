<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
	public function up(): void
	{
		DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
		DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK ((role::text = ANY (ARRAY['farmer'::character varying, 'lead_farmer'::character varying, 'buyer'::character varying, 'facilitator'::character varying, 'admin'::character varying, 'delivery_rider'::character varying]::text[])))");
	}

	public function down(): void
	{
		DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
		DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK ((role::text = ANY (ARRAY['farmer'::character varying, 'lead_farmer'::character varying, 'buyer'::character varying, 'facilitator'::character varying, 'admin'::character varying]::text[])))");
	}
};
