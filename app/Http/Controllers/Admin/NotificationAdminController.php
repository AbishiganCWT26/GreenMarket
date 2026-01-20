<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NotificationAdminController extends Controller
{
	public function index(Request $request)
	{
		$notifications = Notification::with('user')
			->orderByDesc('created_at')
			->paginate(12);

		$notifications->getCollection()->transform(function ($item) {
			$item->created_at = Carbon::parse($item->created_at);
			return $item;
		});

		$users = User::select('id', 'username', 'email')->get();

		return view('admin.notifications.index', compact('notifications', 'users'));
	}

	public function search(Request $request)
	{
		$query = strtolower($request->get('q'));

		$notifications = Notification::with('user')
			->whereRaw('LOWER(title) LIKE ?', ["%{$query}%"])
			->orWhereRaw('LOWER(message) LIKE ?', ["%{$query}%"])
			->orderByDesc('created_at')
			->paginate(12);

		$notifications->getCollection()->transform(function ($item) {
			$item->created_at = Carbon::parse($item->created_at);
			return $item;
		});

		return view('admin.notifications.partials.notification-list', compact('notifications'))->render();
	}

	public function sendNotification(Request $request)
	{
		$request->validate([
			'user_id' => 'required|exists:users,id',
			'title' => 'required|string|max:255',
			'message' => 'required|string'
		]);

		Notification::create([
			'user_id' => $request->user_id,
			'recipient_type' => 'user',
			'title' => $request->title,
			'message' => $request->message,
			'notification_type' => 'admin_alert',
			'is_read' => false
		]);

		return response()->json(['status' => 'success']);
	}

	public function markAllAsRead()
	{
		Notification::where('is_read', false)->update(['is_read' => true]);
		return response()->json(['status' => 'success']);
	}

	public function markAsRead($id)
	{
		Notification::where('id', $id)->update(['is_read' => true]);
		return response()->json(['status' => 'success']);
	}
}
