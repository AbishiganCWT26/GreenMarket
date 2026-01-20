<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminComposer
{
    public function compose(View $view)
    {
        $userId = Auth::id();

        // Notifications (lightweight)
        $unreadNotifications = DB::table('notifications')
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        $notifications = DB::table('notifications')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $view->with([
            'unreadNotifications' => $unreadNotifications,
            'notifications' => $notifications
        ]);
    }
}
