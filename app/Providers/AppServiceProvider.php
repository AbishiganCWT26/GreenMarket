<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (Schema::hasTable('system_config')) {
            $dbAdminEmail = DB::table('system_config')
            ->where('config_key', 'admin_email')
            ->value('config_value');


            if ($dbAdminEmail) {
            // This overrides the .env value for the current request
            Config::set('mail.admin_email', $dbAdminEmail);
            }
        }
    }
}
