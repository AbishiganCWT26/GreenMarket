<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Mail\AdminPasswordChangedMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Admin;
use App\Models\OtpVerification;
use Carbon\Carbon;

class AdminProfileController extends Controller
{
    public function index()
    {
        $admin = Auth::user();
        $adminDetails = $admin->adminDetails ?? null;

        return view('admin.profile.index', compact('admin', 'adminDetails'));
    }

    public function editDetails()
    {
        $admin = Auth::user();
        $adminDetails = $admin->adminDetails ?? null;

        return view('admin.profile.index', compact('admin', 'adminDetails'));
    }

    public function updateDetails(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'phone' => 'nullable|digits:10',
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            \DB::beginTransaction();

            $user->username = $request->username;
            $user->email = $request->email;
            $user->save();

            $adminDetails = $user->adminDetails;
            if (!$adminDetails) {
                $adminDetails = new Admin();
                $adminDetails->user_id = $user->id;
                $adminDetails->nic_no = 'NOT_SET'; // Default value for NIC
                $adminDetails->role = 'admin'; // Default role
                $adminDetails->zone_assigned_area = 'Sri Lanka'; // Default zone
            }

            $adminDetails->full_name = $request->name;
            $adminDetails->phone_number = $request->phone;
            $adminDetails->save();

            \DB::commit();

            return back()->with('success', 'Profile details updated successfully!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/'
            ],
        ], [
            'new_password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        try {
            $oldPassword = $request->current_password;
            $newPassword = $request->new_password;

            $user->password = Hash::make($newPassword);
            $user->save();

            try {
                Mail::to($user->email)->send(new AdminPasswordChangedMail($user, $newPassword));
            } catch (\Exception $mailException) {
                \Log::error('Failed to send password change email: ' . $mailException->getMessage());
            }

            return back()->with('success', 'Password updated successfully! An email has been sent with your new credentials.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update password: ' . $e->getMessage());
        }
    }

    public function updatePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Invalid image file. Please upload JPG, PNG or GIF image (max 5MB).');
        }

        try {
            $user = Auth::user();

            if ($request->hasFile('profile_photo')) {
                $file = $request->file('profile_photo');

                $directory = 'uploads/profile_pictures';
                $uploadPath = public_path($directory);

                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $fileName = 'admin_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                if ($file->move($uploadPath, $fileName)) {
                    if ($user->profile_photo && $user->profile_photo !== 'default-avatar.png') {
                        $oldPhotoPath = public_path($directory . '/' . $user->profile_photo);
                        if (file_exists($oldPhotoPath)) {
                            unlink($oldPhotoPath);
                        }
                    }

                    $user->profile_photo = $fileName;
                    $user->save();

                    return redirect()->route('admin.profile.index')->with('success', 'Profile photo updated successfully!');
                } else {
                    return back()->with('error', 'Failed to upload photo. Please try again.');
                }
            }

            return back()->with('error', 'No photo uploaded.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update photo: ' . $e->getMessage());
        }
    }

    public function deletePhoto()
    {
        try {
            $user = Auth::user();

            if ($user->profile_photo && $user->profile_photo !== 'default-avatar.png') {
                $photoPath = public_path('uploads/profile_pictures/' . $user->profile_photo);
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }

            $user->profile_photo = 'default-avatar.png';
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile photo removed successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove photo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function photoPage()
    {
        return view('admin.profile.photo');
    }

    public function sendNicUpdateOtp(Request $request)
    {
        try {
            $user = Auth::user();
            $adminDetails = $user->adminDetails;

            if (!$adminDetails || !$adminDetails->phone_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number not found. please update your phone number first.'
                ], 400);
            }

            $otp = rand(100000, 999999);
            
            // Log the OTP for development/testing
            \Log::info("NIC Update OTP for Admin (User ID: {$user->id}): " . $otp);

            OtpVerification::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'action' => 'nic_update',
                'expires_at' => Carbon::now()->addMinutes(10),
                'used' => false
            ]);

            // Send OTP via SMS using textit.biz gateway
            $message = "Your GreenMarket OTP for NIC update is: $otp. \nThis code is valid for 10 minutes. \nDo not share this with anyone.";
            $smsSent = $this->sendSMS($adminDetails->phone_number, $message);

            if (!$smsSent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP via SMS. Please try again later.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your registered phone number.'
            ]);
        } catch (\Exception $e) {
            \Log::error('NIC OTP Sending Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyNicUpdateOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
            'nic_no' => 'required|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input. Please check the OTP and NIC format.'
            ], 400);
        }

        try {
            $user = Auth::user();
            
            $otpRecord = OtpVerification::where('user_id', $user->id)
                ->where('otp', $request->otp)
                ->where('action', 'nic_update')
                ->where('used', false)
                ->where('expires_at', '>', Carbon::now())
                ->latest()
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP.'
                ], 400);
            }

            // Backend validation for NIC
            $nic = $request->nic_no;
            $isValidNic = false;
            if (preg_match('/^[0-9]{9}[VXvx]$/', $nic)) {
                $isValidNic = true;
            } elseif (preg_match('/^[0-9]{12}$/', $nic)) {
                $isValidNic = true;
            }

            if (!$isValidNic) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid NIC format.'
                ], 400);
            }

            // Check if NIC is already taken by another admin
            $existingAdmin = Admin::where('nic_no', $nic)->where('user_id', '!=', $user->id)->first();
            if ($existingAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'This NIC number is already registered in the system.'
                ], 400);
            }

            \DB::beginTransaction();

            $adminDetails = $user->adminDetails;
            $adminDetails->nic_no = $nic;
            $adminDetails->save();

            $otpRecord->used = true;
            $otpRecord->used_at = Carbon::now();
            $otpRecord->save();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'NIC updated successfully!'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('NIC Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update NIC: ' . $e->getMessage()
            ], 500);
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
                \Log::info("SMS Sent successfully to $to for NIC Update. Response: $ret");
                return true;
            } else {
                \Log::error("SMS Sending Failed to $to for NIC Update. Response: $ret");
                return false;
            }
        } catch (\Exception $e) {
            \Log::error('SMS Error: ' . $e->getMessage());
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
