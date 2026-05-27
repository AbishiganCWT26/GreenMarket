<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DeliveryRider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeliveryRiderController extends Controller
{
	public function __construct()
	{
		$this->middleware(['auth', 'delivery_rider']);
	}

	public function dashboard()
	{
		$user = Auth::user();
		$rider = $user->deliveryRider;

		$stats = [
			'total_deliveries' => 0,
			'pending_pickups' => 0,
			'completed_deliveries' => 0,
			'earnings' => 0,
		];

		return view('delivery-rider.dashboard', compact('user', 'rider', 'stats'));
	}

	public function profile()
	{
		$user = Auth::user();
		$rider = $user->deliveryRider;

		return view('delivery-rider.profile', compact('user', 'rider'));
	}

	public function sendProfileOTP(Request $request)
	{
		try {
			$user = Auth::user();
			$rider = $user->deliveryRider;
			$type = $request->input('type');
			$number = $request->input('number');

			if (!$type || !$number) {
				return response()->json(['success' => false, 'message' => 'Invalid request'], 400);
			}

			$otp = rand(100000, 999999);
			$action = 'profile_update_' . $type;

			session([
				$action . '_otp' => $otp,
				$action . '_number' => $number,
				$action . '_expires_at' => now()->addMinutes(5)
			]);

			$message = "Your GreenMarket OTP for updating your $type is: $otp. Valid for 5 minutes.";
			
			$sendTo = $rider->primary_mobile;
			$smsSent = $this->sendSMS($sendTo, $message);

			if (!$smsSent) {
				return response()->json(['success' => false, 'message' => 'Failed to send SMS'], 500);
			}

			return response()->json(['success' => true, 'message' => 'OTP sent successfully']);
		} catch (\Exception $e) {
			return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}

	public function verifyProfileOTP(Request $request)
	{
		try {
			$type = $request->input('type');
			$otp = $request->input('otp');
			$number = $request->input('number');
			$action = 'profile_update_' . $type;

			$storedOtp = session($action . '_otp');
			$storedNumber = session($action . '_number');
			$expiresAt = session($action . '_expires_at');

			if (!$storedOtp || !$expiresAt || now()->gt($expiresAt)) {
				return response()->json(['success' => false, 'message' => 'OTP expired or not found'], 400);
			}

			if ($otp != $storedOtp || $number != $storedNumber) {
				return response()->json(['success' => false, 'message' => 'Invalid OTP or number'], 400);
			}

			session([$action . '_verified' => true]);

			return response()->json(['success' => true, 'message' => 'OTP verified successfully']);
		} catch (\Exception $e) {
			return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}

	public function updateProfile(Request $request)
	{
		try {
			$user = User::findOrFail(Auth::id());
			$rider = $user->deliveryRider;

			$validated = $request->validate([
				'name' => 'required|string|max:255',
				'username' => 'required|string|max:50|unique:users,username,' . $user->id,
				'email' => 'required|email|max:100|unique:users,email,' . $user->id,
				'primary_mobile' => 'required|string|max:20',
				'whatsapp_number' => 'nullable|string|max:20',
				'nic_no' => 'required|string|max:20',
				'vehicle_type' => 'required|string|max:50',
				'vehicle_number' => 'required|string|max:50',
				'max_kg_capacity' => 'required|integer|min:1',
				'residential_address' => 'nullable|string|max:500',
			]);

			DB::beginTransaction();

			if ($request->primary_mobile !== $rider->primary_mobile) {
				if (!session('profile_update_primary_mobile_verified') || session('profile_update_primary_mobile_number') !== $request->primary_mobile) {
					return response()->json(['success' => false, 'message' => 'Primary mobile verification required'], 403);
				}
			}

			if ($request->whatsapp_number !== $rider->whatsapp_number) {
				if ($request->whatsapp_number && (!session('profile_update_whatsapp_number_verified') || session('profile_update_whatsapp_number_number') !== $request->whatsapp_number)) {
					return response()->json(['success' => false, 'message' => 'WhatsApp number verification required'], 403);
				}
			}

			$user->update([
				'username' => $validated['username'],
				'email' => $validated['email'],
			]);

			$rider->update([
				'name' => $validated['name'],
				'email' => $validated['email'],
				'nic_no' => $validated['nic_no'],
				'primary_mobile' => $validated['primary_mobile'],
				'whatsapp_number' => $validated['whatsapp_number'],
				'vehicle_type' => $validated['vehicle_type'],
				'vehicle_number' => $validated['vehicle_number'],
				'max_kg_capacity' => $validated['max_kg_capacity'],
				'residential_address' => $validated['residential_address'],
			]);

			session()->forget([
				'profile_update_primary_mobile_otp', 'profile_update_primary_mobile_number', 'profile_update_primary_mobile_expires_at', 'profile_update_primary_mobile_verified',
				'profile_update_whatsapp_number_otp', 'profile_update_whatsapp_number_number', 'profile_update_whatsapp_number_expires_at', 'profile_update_whatsapp_number_verified'
			]);

			DB::commit();

			if ($request->expectsJson() || $request->ajax()) {
				return response()->json([
					'success' => true,
					'message' => 'Profile updated successfully!'
				]);
			}

			return back()->with('success', 'Profile updated successfully.');
		} catch (\Illuminate\Validation\ValidationException $e) {
			if ($request->expectsJson() || $request->ajax()) {
				return response()->json([
					'success' => false,
					'message' => $e->validator->errors()->first()
				], 422);
			}
			throw $e;
		} catch (\Exception $e) {
			DB::rollBack();
			if ($request->expectsJson() || $request->ajax()) {
				return response()->json([
					'success' => false,
					'message' => 'Error: ' . $e->getMessage()
				], 500);
			}
			return back()->with('error', 'Error: ' . $e->getMessage());
		}
	}

	public function updatePhoto(Request $request)
	{
		try {
			$request->validate([
				'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
			]);

			$user = Auth::user();

			if ($request->hasFile('profile_photo')) {
				$image = $request->file('profile_photo');
				$imageName = time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();

				if ($user->profile_photo && $user->profile_photo !== 'default-avatar.png') {
					$oldPath = public_path('uploads/profile_pictures/' . $user->profile_photo);
					if (file_exists($oldPath)) {
						@unlink($oldPath);
					}
				}

				$image->move(public_path('uploads/profile_pictures'), $imageName);
				$user->profile_photo = $imageName;
				$user->save();
			}

			if ($request->expectsJson() || $request->ajax()) {
				return response()->json([
					'success' => true,
					'message' => 'Profile photo updated successfully!'
				]);
			}

			return back()->with('success', 'Profile photo updated successfully!');
		} catch (\Illuminate\Validation\ValidationException $e) {
			if ($request->expectsJson() || $request->ajax()) {
				return response()->json([
					'success' => false,
					'message' => $e->validator->errors()->first()
				], 422);
			}
			throw $e;
		} catch (\Exception $e) {
			if ($request->expectsJson() || $request->ajax()) {
				return response()->json([
					'success' => false,
					'message' => 'An error occurred while updating the photo.'
				], 500);
			}
			return back()->with('error', 'An error occurred while updating the photo.');
		}
	}

	public function updatePassword(Request $request)
	{
		try {
			$user = Auth::user();

			$request->validate([
				'new_password' => 'required|min:8',
			]);

			$user->password = Hash::make($request->new_password);
			$user->save();

			if ($request->expectsJson() || $request->ajax()) {
				return response()->json([
					'success' => true,
					'message' => 'Password updated successfully!'
				]);
			}

			return back()->with('success', 'Password updated successfully!');
		} catch (\Illuminate\Validation\ValidationException $e) {
			if ($request->expectsJson() || $request->ajax()) {
				return response()->json([
					'success' => false,
					'message' => $e->validator->errors()->first()
				], 422);
			}
			throw $e;
		} catch (\Exception $e) {
			if ($request->expectsJson() || $request->ajax()) {
				return response()->json([
					'success' => false,
					'message' => 'An error occurred while updating the password.'
				], 500);
			}
			return back()->with('error', 'An error occurred while updating the password.');
		}
	}

	private function sendSMS($to, $message)
	{
		try {
			$user = env('SMS_USER');
			$password = env('SMS_PASSWORD');
			$baseurl = env('SMS_API_URL', 'https://textit.biz/sendmsg');

			$to = preg_replace('/[^0-9]/', '', $to);
			$text = urlencode($message);
			
			$baseurl = rtrim($baseurl, '/') . '/';
			$url = $baseurl . "?id=" . $user . "&pw=" . $password . "&to=" . $to . "&text=" . $text;
			
			$ret = $this->get_web_page($url);
			$res = explode(":", $ret);
			
			if (trim($res[0]) == "OK") {
				Log::info("SMS Sent successfully to $to. Response: $ret");
				return true;
			} else {
				Log::error("SMS Sending Failed to $to. Response: $ret");
				return false;
			}
		} catch (\Exception $e) {
			Log::error('SMS Error: ' . $e->getMessage());
			return false;
		}
	}

	private function get_web_page($url)
	{
		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT => 30,
		);

		$ch = curl_init($url);
		curl_setopt_array($ch, $options);
		$content = curl_exec($ch);
		curl_close($ch);

		return $content;
	}
}
