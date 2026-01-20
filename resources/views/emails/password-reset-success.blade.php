<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Successful</title>
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

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .header p {
            margin: 10px 0 0;
            font-size: 16px;
            opacity: 0.9;
        }

        .content {
            padding: 30px;
        }

        .success-icon {
            text-align: center;
            margin: 20px 0;
        }

        .success-icon i {
            font-size: 60px;
            color: #10B981;
        }

        .credentials-box {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.08), rgba(59, 130, 246, 0.08));
            border: 2px solid rgba(16, 185, 129, 0.2);
            border-radius: 10px;
            padding: 25px;
            margin: 25px 0;
        }

        .credential-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(16, 185, 129, 0.1);
        }

        .credential-item:last-child {
            border-bottom: none;
        }

        .credential-label {
            font-weight: 600;
            color: #0f1724;
        }

        .credential-value {
            font-family: monospace;
            font-size: 16px;
            background: white;
            padding: 8px 15px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            color: #059669;
        }

        .security-notice {
            background-color: rgba(245, 158, 11, 0.05);
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }

        .security-notice h4 {
            color: #f59e0b;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .login-button {
            display: inline-block;
            background: linear-gradient(135deg, #10B981, #059669);
            color: white;
            text-decoration: none;
            padding: 15px 35px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
            transition: all 0.3s ease;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            border-top: 1px solid #e5e7eb;
        }

        .warning-text {
            color: #ef4444;
            font-weight: 600;
        }

        @media (max-width: 600px) {
            .content {
                padding: 20px;
            }

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 20px;
            }

            .credentials-box {
                padding: 15px;
            }

            .credential-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .login-button {
                display: block;
                width: 100%;
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
            <p>Password Reset Successful</p>
        </div>

        <div class="content">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>

            <h2 style="text-align: center; color: #059669;">Password Reset Complete!</h2>
            <p style="text-align: center;">Hello <strong>{{ $username }}</strong>,</p>
            <p style="text-align: center;">Your GreenMarket account password has been successfully reset.</p>

            <div class="credentials-box">
                <h3 style="text-align: center; margin-top: 0; color: #0f1724;">Your New Login Credentials</h3>

                <div class="credential-item">
                    <span class="credential-label">Username:</span>
                    <span class="credential-value">{{ $username }}</span>
                </div>

                <div class="credential-item">
                    <span class="credential-label">Password:</span>
                    <span class="credential-value">{{ $password }}</span>
                </div>
            </div>

            <div class="security-notice">
                <h4>
                    <i class="fas fa-shield-alt"></i>
                    Security Notice
                </h4>
                <p class="warning-text">IMPORTANT: For your account security:</p>
                <ul>
                    <li>Login and change your password immediately</li>
                    <li>Never share your credentials with anyone</li>
                    <li>Use a strong, unique password</li>
                    <li>GreenMarket will never ask for your password</li>
                </ul>
            </div>

            <div class="button-container">
                <a href="{{ url('/login') }}" class="login-button">
                    <i class="fas fa-sign-in-alt"></i>
                    Login to GreenMarket
                </a>
            </div>

            <p style="text-align: center; color: #6b7280; font-size: 14px;">
                If you did not request this password reset, please contact our support team immediately.
            </p>

            <p style="margin-top: 30px; text-align: center;">
                Best regards,<br>
                <strong>The GreenMarket Team</strong>
            </p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} GreenMarket. All rights reserved.</p>
            <p>This email was sent to you regarding your GreenMarket account security.</p>
            <p>Need help? Contact: support@greenmarket.com</p>
        </div>
    </div>
</body>
</html>
