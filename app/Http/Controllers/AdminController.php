<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Notification; // Make sure this matches your Notification model namespace

class AdminController extends Controller
{
    // In your controller
    public function dashboard()
    {
        $user = Auth::user();

        \Log::info('AdminController: User ID = ' . $user->id);

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        \Log::info('AdminController: Notifications count = ' . $notifications->count());

        $unreadNotifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        \Log::info('AdminController: Unread notifications = ' . $unreadNotifications);

        return view('admin.dashboard', [
            'notifications' => $notifications,
            'unreadNotifications' => $unreadNotifications
        ]);
    }
}
