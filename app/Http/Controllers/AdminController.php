<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

            // Keeping the fully-qualified Log call as it already references facade
            Log::info('AdminController: User ID = ' . $user->id);

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

            // Keeping the fully-qualified Log call as it already references facade
            Log::info('AdminController: Notifications count = ' . $notifications->count());

        $unreadNotifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

            // Keeping the fully-qualified Log call as it already references facade
            Log::info('AdminController: Unread notifications = ' . $unreadNotifications);

        return view('admin.dashboard', [
            'notifications' => $notifications,
            'unreadNotifications' => $unreadNotifications
        ]);
    }
}
