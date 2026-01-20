<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #0f1724;
            margin: 0;
            padding: 0;
            background-color: #f6f8fa;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(15,23,36,0.1);
        }

        .header {
            background: linear-gradient(135deg, #10B981, #059669);
            padding: 30px;
            text-align: center;
            color: white;
        }

        .logo {
            height: 40px;
            margin-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .content {
            padding: 30px;
        }

        .otp-box {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.08), rgba(59, 130, 246, 0.08));
            border: 2px solid rgba(16, 185, 129, 0.2);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 25px 0;
        }

        .otp-code {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 5px;
            color: #10B981;
            margin: 15px 0;
            font-family: monospace;
        }

        .info-box {
            background-color: rgba(245, 158, 11, 0.05);
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
        }

        .info-box i {
            color: #f59e0b;
            margin-right: 8px;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #10B981, #059669);
            color: white;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 16px;
            margin: 20px 0;
            transition: all 0.3s ease;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }

        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            border-top: 1px solid #e5e7eb;
        }

        .warning {
            color: #ef4444;
            font-weight: 600;
        }

        .security-note {
            background-color: rgba(239, 68, 68, 0.05);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 8px;
            padding: 12px;
            margin: 15px 0;
            font-size: 13px;
        }

        @media (max-width: 600px) {
            .content {
                padding: 20px;
            }

            .otp-code {
                font-size: 24px;
                letter-spacing: 3px;
            }

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <!-- Logo Section -->
            @php
                $logoPngPath = public_path('assets/images/Logo-4.png');
                $logoSvgPath = public_path('assets/images/Logo-4.svg');
            @endphp

            @if(file_exists($logoPngPath))
                <img src="{{ $message->embed($logoPngPath) }}" alt="GreenMarket Logo" style="max-width: 80px; height: auto; display: block; margin: 0 auto 10px;">
            @elseif(file_exists($logoSvgPath))
                <img src="{{ $message->embed($logoSvgPath) }}" alt="GreenMarket Logo" style="max-width: 80px; height: auto; display: block; margin: 0 auto 10px;">
            @endif

            <h1>GreenMarket</h1>
            <p>Password Reset OTP</p>
        </div>

        <div class="content">
            <h2>Password Reset Request</h2>
            <p>Hello,</p>
            <p>We received a request to reset your password for your GreenMarket account. Use the OTP below to verify your identity:</p>

            <div class="otp-box">
                <h3>Your Verification Code</h3>
                <div class="otp-code">{{ $otp }}</div>
                <p>This code is valid for {{ $expiry_minutes }} minutes</p>
            </div>

            <div class="info-box">
                <p><i class="fas fa-info-circle"></i> If you didn't request this password reset, please ignore this email.</p>
            </div>

            <div class="security-note">
                <p class="warning">⚠️ Security Notice:</p>
                <p>Never share this OTP with anyone. GreenMarket will never ask for your password or OTP.</p>
            </div>

            <p>Need help? Contact our support team at <a href="mailto:support@smartmarket.com">support@smartmarket.com</a></p>

            <p style="margin-top: 30px;">
                Best regards,<br>
                <strong>GreenMarket Team</strong>
            </p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} GreenMarket. All rights reserved.</p>
            <p>This email was sent to you as part of your GreenMarket account security.</p>
        </div>
    </div>
</body>
</html>
