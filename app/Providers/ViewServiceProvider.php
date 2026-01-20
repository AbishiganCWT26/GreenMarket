<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Add this import
use App\Http\View\Composers\AdminComposer;
use App\Http\View\Composers\DashboardComposer;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Admin shared data (notifications, messages)
        View::composer(
            ['admin.*'],              // all admin views
            AdminComposer::class
        );

        // Dashboard stats composer
        View::composer(
            ['admin.dashboard'],      // only dashboard
            DashboardComposer::class
        );

        // Share $unread variable with buyer_nav for all buyer views
        View::composer('includes.buyer_nav', function ($view) {
            if (Auth::check()) {
                // Use your custom notifications table structure
                $unread = DB::table('notifications')
                    ->where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->get();
            } else {
                $unread = collect();
            }
            $view->with('unread', $unread);
        });

        // Optional: Also share with all buyer.* views if needed elsewhere
        View::composer('buyer.*', function ($view) {
            if (!isset($view->getData()['unread']) && Auth::check()) {
                $unread = DB::table('notifications')
                    ->where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->get();
                $view->with('unread', $unread);
            }
        });
    }
}
