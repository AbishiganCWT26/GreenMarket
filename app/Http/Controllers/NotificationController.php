<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAllAsRead(Request $request)
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function markAsRead(Request $request, $id = null)
    {
        $notificationId = $id ?? $request->notification_id ?? $request->id;

        if (!$notificationId) {
            \Log::warning('Notification markAsRead failed: No ID provided', ['request' => $request->all(), 'id_param' => $id]);
            return response()->json(['success' => false, 'message' => 'Notification ID not provided'], 400);
        }

        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notification->update(['is_read' => true]);
            return response()->json(['success' => true]);
        }

        \Log::error('Notification markAsRead failed: Notification not found or unauthorized', [
            'user_id' => Auth::id(),
            'notification_id' => $notificationId
        ]);
        return response()->json(['success' => false, 'message' => 'Notification not found or unauthorized'], 404);
    }
}
