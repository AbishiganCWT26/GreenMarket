<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class DeliveryRiderComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (Auth::check() && Auth::user()->role === 'delivery_rider') {
            $user = Auth::user();

            // Fetch recent notifications for the logged in user
            $recentActivities = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Fetch unread notification count
            $totalNotifications = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();

            $sharedCounts = [
                'totalNotifications' => $totalNotifications
            ];

            $view->with('recentActivities', $recentActivities);
            $view->with('sharedCounts', $sharedCounts);
        }
    }
}
