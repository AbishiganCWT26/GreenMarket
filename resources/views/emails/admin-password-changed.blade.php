<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Changed - GreenMarket</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f6f8fa;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .email-logo {
            max-height: 60px;
            margin-bottom: 15px;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px;
        }
        .alert-box {
            background: #dcfce7;
            border-left: 4px solid #10B981;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .alert-title {
            color: #059669;
            font-weight: 600;
            font-size: 18px;
            margin: 0 0 10px 0;
        }
        .alert-message {
            color: #065f46;
            margin: 0;
            font-size: 14px;
        }
        .credentials-box {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
            padding: 25px;
            margin: 25px 0;
        }
        .credential-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        .credential-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #4b5563;
            font-size: 14px;
            margin-bottom: 5px;
            display: block;
        }
        .value {
            font-size: 16px;
            color: #111827;
            font-weight: 500;
            background: white;
            padding: 12px 15px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
            word-break: break-all;
        }
        .warning-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        .warning-title {
            color: #d97706;
            font-weight: 600;
            font-size: 16px;
            margin: 0 0 10px 0;
        }
        .warning-list {
            margin: 0;
            padding-left: 20px;
            color: #92400e;
            font-size: 14px;
        }
        .warning-list li {
            margin-bottom: 8px;
        }
        .support-box {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }
        .support-title {
            color: #1d4ed8;
            font-weight: 600;
            font-size: 16px;
            margin: 0 0 10px 0;
        }
        .support-text {
            color: #1e40af;
            margin: 0;
            font-size: 14px;
            line-height: 1.5;
        }
        .footer {
            text-align: center;
            padding: 30px;
            background: #f3f4f6;
            color: #6b7280;
            font-size: 12px;
        }
        .footer a {
            color: #10B981;
            text-decoration: none;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
        }
        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 20px;
            }
            .email-header {
                padding: 20px;
            }
            .email-header h1 {
                font-size: 20px;
            }
            .value {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            @php
                $logoUrl = asset('assets/images/Logo Green Market.png');
            @endphp
            <img src="{{ $logoUrl }}" alt="GreenMarket Logo" style="max-width: 120px; height: auto; display: block; margin: 0 auto;">
            <h1>Admin Password Changed</h1>
        </div>

        <div class="email-body">
            <div class="alert-box">
                <p class="alert-title"><i class="fas fa-shield-alt"></i> Security Update</p>
                <p class="alert-message">Your admin account password has been successfully changed. Please use the new credentials below to log in.</p>
            </div>

            <p>Hello <strong>{{ $user->username }}</strong>,</p>
            <p>Your administrator password for <strong>{{ config('app.name', 'GreenMarket') }}</strong> has been updated as requested.</p>

            <div class="credentials-box">
                <div class="credential-item">
                    <span class="label">Username:</span>
                    <div class="value">{{ $user->username }}</div>
                </div>
                <div class="credential-item">
                    <span class="label">New Password:</span>
                    <div class="value">{{ $newPassword }}</div>
                </div>
            </div>

            <div class="warning-box">
                <p class="warning-title"><i class="fas fa-exclamation-triangle"></i> Important Security Notice</p>
                <ul class="warning-list">
                    <li>Never share your password with anyone</li>
                    <li>Change your password regularly (every 90 days)</li>
                    <li>Use a unique password for this account</li>
                    <li>Log out from shared computers</li>
                    <li>Enable two-factor authentication if available</li>
                </ul>
            </div>

            <center>
                <a href="{{ url('/admin/login') }}" class="button">Login to Admin Panel</a>
            </center>

            <div class="support-box">
                <p class="support-title"><i class="fas fa-headset"></i> Need Help?</p>
                <p class="support-text">
                    If you did not request this password change or need assistance,
                    please contact our support team immediately at
                    <a href="mailto:{{ config('mail.admin_email', 'support@hghub.com') }}">{{ config('mail.admin_email', 'support@hghub.com') }}</a>
                </p>
            </div>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name', 'GreenMarket') }}. All rights reserved.</p>
            <p>This is an automated security notification. Please do not reply to this email.</p>
            <p>
                <a href="{{ config('app.url') }}">Visit Website</a> |
                <a href="{{ config('app.url') }}/privacy">Privacy Policy</a> |
                <a href="{{ config('app.url') }}/terms">Terms of Service</a>
            </p>
        </div>
    </div>
</body>
</html>
