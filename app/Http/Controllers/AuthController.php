<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetOTP;
use App\Mail\PasswordResetSuccess;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginField = filter_var($request->username, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $user = DB::table('users')
            ->where($loginField, $request->username)
            ->where('is_active', true)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'username' => 'User not found or inactive.',
            ])->onlyInput('username');
        }

        $credentials = [
            $loginField => $request->username,
            'password' => $request->password,
            'is_active' => true
        ];

        if (!Auth::attempt($credentials, $request->has('remember'))) {
            return back()->withErrors([
                'username' => 'Incorrect username or password.',
            ])->onlyInput('username');
        }

        DB::table('users')
            ->where('id', $user->id)
            ->update(['last_login' => now()]);

        $request->session()->regenerate();

        return redirect('/login')->with([
            'login_success' => true, // Changed from 'success' to 'login_success'
            'role' => $user->role,
            'name' => $user->username
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    protected function redirectByRole($role)
    {
        switch ($role) {
            case 'admin':
                return redirect('/admin/dashboard');
            case 'facilitator':
                return redirect('/facilitator/dashboard');
            case 'lead_farmer':
                return redirect('/lead-farmer/dashboard');
            case 'farmer':
                return redirect('/farmer/dashboard');
            case 'buyer':
                return redirect('/buyer/dashboard');
            default:
                return redirect('/');
        }
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'username' => 'required'
        ]);

        $username = $request->username;

        $user = DB::table('users')
            ->where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if (!$user) {
            $user = DB::table('users')
                ->whereIn('id', function($query) use ($username) {
                    $query->select('user_id')
                        ->from('farmers')
                        ->where('nic_no', $username);
                })
                ->orWhereIn('id', function($query) use ($username) {
                    $query->select('user_id')
                        ->from('buyers')
                        ->where('nic_no', $username);
                })
                ->orWhereIn('id', function($query) use ($username) {
                    $query->select('user_id')
                        ->from('lead_farmers')
                        ->where('nic_no', $username);
                })
                ->orWhereIn('id', function($query) use ($username) {
                    $query->select('user_id')
                        ->from('facilitators')
                        ->where('nic_no', $username);
                })
                ->first();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found. Please check your username/email.'
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive. Please contact support.'
            ]);
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(10);

        DB::table('otp_verifications')->insert([
            'user_id' => $user->id,
            'otp' => $otp,
            'action' => 'password_reset',
            'expires_at' => $expiresAt,
            'used' => false,
            'created_at' => now()
        ]);

        $email = $user->email;
        $hasPhone = false;

        if ($email) {
            try {
                Mail::to($email)->send(new PasswordResetOTP($otp));
            } catch (\Exception $e) {
                \Log::error('Failed to send OTP email: ' . $e->getMessage());
            }
        }

        if ($request->send_sms) {
            $phone = $this->getUserPhoneNumber($user->id);
            if ($phone) {
                $hasPhone = $this->sendSmsOTP($phone, $otp);
            }
        }

        session([
            'reset_user_id' => $user->id,
            'reset_username' => $user->username,
            'reset_email' => $user->email,
            'reset_otp' => $otp,
            'otp_expiry' => $expiresAt
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.',
            'has_phone' => $hasPhone,
            'redirect_url' => route('password.verify.otp')
        ]);
    }

    public function showVerifyOTP()
    {
        if (!session('reset_user_id') || !session('reset_otp')) {
            return redirect()->route('login')->with('error', 'Invalid OTP session.');
        }

        return view('auth.verify-otp');
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        $userId = session('reset_user_id');
        $storedOTP = session('reset_otp');
        $otpExpiry = session('otp_expiry');

        if (!$userId || !$storedOTP || !$otpExpiry) {
            return back()->with('error', 'Invalid OTP session.');
        }

        if (now()->greaterThan($otpExpiry)) {
            session()->forget(['reset_user_id', 'reset_otp', 'otp_expiry']);
            return back()->with('error', 'OTP has expired. Please request a new one.');
        }

        if ($request->otp !== $storedOTP) {
            return back()->with('error', 'Invalid OTP. Please try again.');
        }

        $otpRecord = DB::table('otp_verifications')
            ->where('user_id', $userId)
            ->where('otp', $request->otp)
            ->where('action', 'password_reset')
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return back()->with('error', 'Invalid OTP.');
        }

        DB::table('otp_verifications')
            ->where('id', $otpRecord->id)
            ->update([
                'used' => true,
                'used_at' => now()
            ]);

        session([
            'otp_verified' => true,
            'reset_token' => Str::random(60)
        ]);

        return redirect()->route('password.reset');
    }

    public function showResetPassword()
    {
        if (!session('otp_verified') || !session('reset_token')) {
            return redirect()->route('login')->with('error', 'Invalid reset session.');
        }

        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed'
        ]);

        if (!session('otp_verified') || !session('reset_token') || !session('reset_user_id')) {
            return back()->with('error', 'Invalid reset session.');
        }

        $userId = session('reset_user_id');
        $newPassword = $request->password;
        $hashedPassword = Hash::make($newPassword);
        $username = session('reset_username');
        $email = session('reset_email');

        DB::table('users')
            ->where('id', $userId)
            ->update([
                'password' => $hashedPassword,
                'updated_at' => now()
            ]);

        if (DB::getSchemaBuilder()->hasTable('password_history')) {
            try {
                DB::table('password_history')->insert([
                    'user_id' => $userId,
                    'password_hash' => $hashedPassword,
                    'changed_at' => now(),
                    'changed_by' => $userId,
                    'change_reason' => 'password_reset'
                ]);
            } catch (\Exception $e) {
                \Log::warning('Could not insert into password_history: ' . $e->getMessage());
            }
        }

        $phone = $this->getUserPhoneNumber($userId);
        $smsSent = false;
        $emailSent = false;

        if ($email) {
            $emailSent = $this->sendPasswordEmail($email, $username, $newPassword);
        }

        if ($phone) {
            $smsSent = $this->sendPasswordSMS($phone, $username, $newPassword);
        }

        session()->forget([
            'reset_user_id',
            'reset_username',
            'reset_email',
            'reset_otp',
            'otp_expiry',
            'otp_verified',
            'reset_token'
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with([
            'password_reset_success' => true, // Changed from 'success' to 'password_reset_success'
            'username' => $username,
            'email_sent' => $emailSent,
            'sms_sent' => $smsSent
        ]);
    }

    private function getUserPhoneNumber($userId)
    {
        $phone = DB::table('buyers')
            ->where('user_id', $userId)
            ->value('primary_mobile');

        if (!$phone) {
            $phone = DB::table('farmers')
                ->where('user_id', $userId)
                ->value('primary_mobile');
        }

        if (!$phone) {
            $phone = DB::table('lead_farmers')
                ->where('user_id', $userId)
                ->value('primary_mobile');
        }

        if (!$phone) {
            $phone = DB::table('facilitators')
                ->where('user_id', $userId)
                ->value('primary_mobile');
        }

        return $phone;
    }

    private function sendSmsOTP($phone, $otp)
    {
        try {
            $user = env('SMS_USER');
            $password = env('SMS_PASSWORD');
            $baseurl = env('SMS_API_URL');

            if (!$user || !$password || !$baseurl) {
                \Log::error('SMS credentials not configured.');
                return false;
            }

            $text = urlencode("Your GreenMarket password reset OTP is: $otp. Valid for 10 minutes.");
            $to = preg_replace('/[^0-9]/', '', $phone);

            if (strlen($to) !== 10 || !preg_match('/^[0-9]{10}$/', $to)) {
                \Log::error('Invalid phone number format: ' . $phone);
                return false;
            }

            $url = "$baseurl/?id=$user&pw=$password&to=$to&text=$text";

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            if (strpos($response, 'OK') === 0) {
                \Log::info('SMS sent successfully to ' . $phone);
                return true;
            } else {
                \Log::error('SMS failed: ' . $response);
                return false;
            }
        } catch (\Exception $e) {
            \Log::error('SMS error: ' . $e->getMessage());
            return false;
        }
    }

    private function sendPasswordResetConfirmation($email, $username, $password, $phone = null)
    {
        $this->sendPasswordEmail($email, $username, $password);

        if ($phone) {
            $this->sendPasswordSMS($phone, $username, $password);
        }
    }

    private function sendPasswordEmail($email, $username, $password)
    {
        try {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST', 'smtp.gmail.com');
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
            $mail->Port = env('MAIL_PORT', 587);

            $mail->setFrom(env('MAIL_FROM_ADDRESS', 'noreply@greenmarket.com'), env('MAIL_FROM_NAME', 'GreenMarket'));
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'GreenMarket - Password Reset Successful';
            $mail->Body = $this->getPasswordResetEmailBody($username, $password);
            $mail->AltBody = "Your GreenMarket password has been reset successfully.\nUsername: $username\nPassword: $password\n\nPlease login at: " . url('/login');

            $mail->send();
            \Log::info('Password reset email sent to: ' . $email);
            return true;
        } catch (Exception $e) {
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
            return false;
        }
    }

    private function sendPasswordSMS($phone, $username, $password)
    {
        try {
            $user = env('SMS_USER');
            $password_sms = env('SMS_PASSWORD');
            $baseurl = env('SMS_API_URL');

            if (!$user || !$password_sms || !$baseurl) {
                \Log::error('SMS credentials not configured.');
                return false;
            }

            $text = urlencode("GreenMarket password reset successful.\nUsername: $username\nPassword: $password");
            $to = preg_replace('/[^0-9]/', '', $phone);

            if (strlen($to) !== 10 || !preg_match('/^[0-9]{10}$/', $to)) {
                \Log::error('Invalid phone number format: ' . $phone);
                return false;
            }

            $url = "$baseurl/?id=$user&pw=$password_sms&to=$to&text=$text";

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            if (strpos($response, 'OK') === 0) {
                \Log::info('Password reset SMS sent to ' . $phone);
                return true;
            } else {
                \Log::error('Password reset SMS failed: ' . $response);
                return false;
            }
        } catch (\Exception $e) {
            \Log::error('SMS error: ' . $e->getMessage());
            return false;
        }
    }

    private function getPasswordResetEmailBody($username, $password)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Password Reset Successful</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; border-radius: 10px; }
                .header { background: linear-gradient(135deg, #10B981, #059669); padding: 20px; border-radius: 10px 10px 0 0; color: white; text-align: center; }
                .content { padding: 30px; background: white; border-radius: 0 0 10px 10px; }
                .credentials { background: #f0f9ff; border: 2px solid #10B981; border-radius: 8px; padding: 20px; margin: 20px 0; }
                .warning { background: #fef3c7; border: 2px solid #f59e0b; border-radius: 8px; padding: 15px; margin: 20px 0; }
                .btn { display: inline-block; background: linear-gradient(135deg, #10B981, #059669); color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>GreenMarket</h1>
                    <p>Password Reset Successful</p>
                </div>
                <div class="content">
                    <h2>Your Password Has Been Reset</h2>
                    <p>Hello ' . htmlspecialchars($username) . ',</p>
                    <p>Your GreenMarket account password has been successfully reset.</p>

                    <div class="credentials">
                        <h3>Your New Login Credentials:</h3>
                        <p><strong>Username:</strong> ' . htmlspecialchars($username) . '</p>
                        <p><strong>Password:</strong> ' . htmlspecialchars($password) . '</p>
                    </div>

                    <div class="warning">
                        <h4>⚠️ Security Notice:</h4>
                        <p>For your security, please:</p>
                        <ul>
                            <li>Change your password immediately after logging in</li>
                            <li>Never share your credentials with anyone</li>
                            <li>Use a strong, unique password</li>
                        </ul>
                    </div>

                    <p style="text-align: center; margin: 30px 0;">
                        <a href="' . url('/login') . '" class="btn">Login to GreenMarket</a>
                    </p>

                    <div class="footer">
                        <p>© ' . date('Y') . ' GreenMarket. All rights reserved.</p>
                        <p>This email was sent to you regarding your GreenMarket account security.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>';
    }
}
