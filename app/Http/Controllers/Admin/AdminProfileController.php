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
            'phone' => 'nullable|string|max:20',
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
}
