<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationAdminController extends Controller
{
	public function index(Request $request)
	{
		$view = $request->get('view', 'card');
		$perPage = $request->get('per_page', $view === 'card' ? 10 : 15);
		
		$notifications = Notification::with('user')
			->orderByDesc('created_at')
			->paginate($perPage)
			->withQueryString();

		$users = User::select('id', 'username', 'email')
			->orderBy('username')
			->get();

		if($request->ajax()){
			return view('admin.notifications.partials.notification-list', compact('notifications', 'view'))->render();
		}

		return view('admin.notifications.index', compact('notifications', 'users'));
	}



	public function sendNotification(Request $request)
	{
		try{
			$request->validate([
				'user_id' => 'required|exists:users,id',
				'title' => 'required|string|max:255',
				'message' => 'required|string'
			]);

			DB::beginTransaction();

			$notification = Notification::create([
				'user_id' => $request->user_id,
				'recipient_type' => 'user',
				'recipient_address' => null,
				'title' => $request->title,
				'message' => $request->message,
				'notification_type' => 'admin_alert',
				'is_read' => false,
				'related_id' => null,
				'created_at' => now(),
				'updated_at' => now()
			]);

			DB::commit();

			return response()->json([
				'status' => 'success',
				'message' => 'Notification sent successfully',
				'data' => $notification
			]);
		} catch(\Exception $e){
			DB::rollBack();
			return response()->json([
				'status' => 'error',
				'message' => 'Failed to send notification: ' . $e->getMessage()
			], 500);
		}
	}

	public function markAllAsRead()
	{
		try{
			DB::beginTransaction();
			
			Notification::where('is_read', false)->update([
				'is_read' => true,
				'updated_at' => now()
			]);
			
			DB::commit();
			
			return response()->json([
				'success' => true,
				'message' => 'All notifications marked as read'
			]);
		} catch(\Exception $e){
			DB::rollBack();
			return response()->json([
				'success' => false,
				'message' => 'Failed to mark notifications as read'
			], 500);
		}
	}

	public function markAsRead($id)
	{
		try{
			DB::beginTransaction();
			
			$notification = Notification::findOrFail($id);
			$notification->update([
				'is_read' => true,
				'updated_at' => now()
			]);
			
			DB::commit();
			
			return response()->json([
				'success' => true,
				'message' => 'Notification marked as read'
			]);
		} catch(\Exception $e){
			DB::rollBack();
			return response()->json([
				'success' => false,
				'message' => 'Failed to mark notification as read'
			], 500);
		}
	}
}