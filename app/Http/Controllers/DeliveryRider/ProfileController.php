<?php

namespace App\Http\Controllers\DeliveryRider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DeliveryRider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;

        return view('delivery-rider.my_profile', compact('user', 'rider'));
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
                // If it fails (e.g. dev environment check bypass), log and let it pass if dev env
                if (env('APP_ENV') === 'local') {
                    Log::info("SMS sending bypassed in local environment. OTP code: $otp");
                    return response()->json(['success' => true, 'message' => 'OTP sent successfully (Development Bypass: ' . $otp . ')']);
                }
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

    public function update(Request $request)
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
                'residential_address' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            // Username OTP verification check
            if ($request->username !== $user->username) {
                if (!session('profile_update_username_verified') || session('profile_update_username_number') !== $request->username) {
                    return response()->json(['success' => false, 'message' => 'Username verification required'], 403);
                }
            }

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
                'primary_mobile' => $validated['primary_mobile'],
                'whatsapp_number' => $validated['whatsapp_number'],
                'residential_address' => $validated['residential_address'],
            ]);

            session()->forget([
                'profile_update_username_otp', 'profile_update_username_number', 'profile_update_username_expires_at', 'profile_update_username_verified',
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
                'profile_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
                'crop_x' => 'nullable|numeric',
                'crop_y' => 'nullable|numeric',
                'crop_width' => 'nullable|numeric',
                'crop_height' => 'nullable|numeric',
            ]);

            $user = Auth::user();

            if ($request->hasFile('profile_photo')) {
                $image = $request->file('profile_photo');
                $extension = strtolower($image->getClientOriginalExtension());
                
                $imageName = time() . '_' . $user->id . '.' . $extension;
                $uploadDir = public_path('uploads/profile_pictures');
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $targetPath = $uploadDir . '/' . $imageName;
                
                // Image Processing using GD
                $sourcePath = $image->getPathname();
                $mimeType = $image->getMimeType();
                
                $sourceImage = null;
                switch ($mimeType) {
                    case 'image/jpeg':
                        $sourceImage = @imagecreatefromjpeg($sourcePath);
                        break;
                    case 'image/png':
                        $sourceImage = @imagecreatefrompng($sourcePath);
                        break;
                    case 'image/gif':
                        $sourceImage = @imagecreatefromgif($sourcePath);
                        break;
                    case 'image/webp':
                        $sourceImage = @imagecreatefromwebp($sourcePath);
                        break;
                    default:
                        if (function_exists('imagecreatefromstring')) {
                            $sourceImage = @imagecreatefromstring(file_get_contents($sourcePath));
                        }
                        break;
                }
                
                if ($sourceImage !== false && $sourceImage !== null) {
                    $origWidth = imagesx($sourceImage);
                    $origHeight = imagesy($sourceImage);
                    
                    if ($request->filled(['crop_width', 'crop_height'])) {
                        $cWidth = max(1, (int)$request->input('crop_width'));
                        $cHeight = max(1, (int)$request->input('crop_height'));
                        $cX = (int)$request->input('crop_x');
                        $cY = (int)$request->input('crop_y');
                    } else {
                        // Fallback: Center square crop
                        $size = min($origWidth, $origHeight);
                        $cWidth = $size;
                        $cHeight = $size;
                        $cX = ($origWidth - $size) / 2;
                        $cY = ($origHeight - $size) / 2;
                    }
                    
                    // Final output size
                    $outSize = 300;
                    $destImage = imagecreatetruecolor($outSize, $outSize);
                    
                    if (in_array($extension, ['png', 'webp', 'gif'])) {
                        imagealphablending($destImage, false);
                        imagesavealpha($destImage, true);
                        $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
                        imagefill($destImage, 0, 0, $transparent);
                    }
                    
                    imagecopyresampled($destImage, $sourceImage, 0, 0, $cX, $cY, $outSize, $outSize, $cWidth, $cHeight);
                    
                    switch ($extension) {
                        case 'png':
                            imagepng($destImage, $targetPath, 8);
                            break;
                        case 'webp':
                            imagewebp($destImage, $targetPath, 85);
                            break;
                        case 'gif':
                            imagegif($destImage, $targetPath);
                            break;
                        default:
                            imagejpeg($destImage, $targetPath, 85);
                            break;
                    }
                    
                    imagedestroy($sourceImage);
                    imagedestroy($destImage);
                } else {
                    $image->move($uploadDir, $imageName);
                }

                if ($user->profile_photo && $user->profile_photo !== 'default-avatar.png') {
                    $oldPath = $uploadDir . '/' . $user->profile_photo;
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                $user->profile_photo = $imageName;
                $user->save();
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'profile_photo_url' => asset('uploads/profile_pictures/' . $user->profile_photo) . '?t=' . time(),
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
            return back()->withErrors($e->validator)->withInput();
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

    public function deletePhoto(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user->profile_photo && $user->profile_photo !== 'default-avatar.png') {
                $oldPath = public_path('uploads/profile_pictures/' . $user->profile_photo);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            
            $user->profile_photo = null;
            $user->save();
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile photo removed successfully!'
                ]);
            }
            
            return back()->with('success', 'Profile photo removed successfully!');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while removing the photo.'
                ], 500);
            }
            return back()->with('error', 'An error occurred while removing the photo.');
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

            if (!$user || !$password) {
                Log::warning("SMS credentials not set. Bypassing sending SMS.");
                return false;
            }

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
