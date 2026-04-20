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
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        try {
            if (Schema::hasTable('system_config')) {
                $dbAdminEmail = \Illuminate\Support\Facades\DB::table('system_config')
                ->where('config_key', 'admin_email')
                ->value('config_value');


                if ($dbAdminEmail) {
                // This overrides the .env value for the current request
                Config::set('mail.admin_email', $dbAdminEmail);
                }
            }
        } catch (\Exception $e) {
            // Suppress database connection errors during build/console commands
            // where the database might not be available.
        }

        // View Composer for Farmer Views
        view()->composer(['farmer.*', 'farmer.layouts.*'], function ($view) {
            if (auth()->check() && auth()->user()->role === 'farmer') {
                $user = auth()->user();
                $farmer = \App\Models\Farmer::where('user_id', $user->id)->first();

                if ($farmer) {
                    $notifications = \App\Models\Notification::where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();

                    $unreadNotifications = \App\Models\Notification::where('user_id', $user->id)
                        ->where('is_read', false)
                        ->count();

                    $sharedCounts = [
                        'productCount' => \App\Models\Product::where('farmer_id', $farmer->id)->count(),
                        'pendingOrders' => \App\Models\Order::where('farmer_id', $farmer->id)
                            ->whereIn('order_status', ['paid', 'ready_for_pickup'])
                            ->count(),
                        'openComplaints' => \App\Models\Complaint::where('complainant_user_id', $user->id)
                            ->where('status', 'new')
                            ->count()
                    ];

                    $view->with(compact('notifications', 'unreadNotifications', 'sharedCounts'));
                }
            }
        });

        // View Composer for Buyer Views
        view()->composer(['buyer.*', 'buyer.layouts.*'], function ($view) {
            if (auth()->check() && auth()->user()->role === 'buyer') {
                $user = auth()->user();

                $notifications = \App\Models\Notification::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

                $unreadNotifications = \App\Models\Notification::where('user_id', $user->id)
                    ->where('is_read', false)
                    ->count();

                $view->with(compact('notifications', 'unreadNotifications'));
            }
        });
    }
}
