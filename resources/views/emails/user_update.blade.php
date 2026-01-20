<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #10B981; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 5px 5px; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Logo Section -->
            @php
                $logoPngPath = public_path('assets/images/Logo-4.png');
                $logoSvgPath = public_path('assets/images/Logo-4.svg');
            @endphp

            @if(file_exists($logoPngPath))
                <img src="{{ $message->embed($logoPngPath) }}" alt="GreenMarket Logo" style="max-width: 100px; height: auto; display: block; margin: 0 auto;">
            @elseif(file_exists($logoSvgPath))
                <img src="{{ $message->embed($logoSvgPath) }}" alt="GreenMarket Logo" style="max-width: 100px; height: auto; display: block; margin: 0 auto;">
            @else
                <h2 style="color: white; margin: 0;">GreenMarket</h2>
            @endif
        </div>
        <div class="content">
            <h3>Dear {{ $user->username }},</h3>

            <p>{!! nl2br(e($content)) !!}</p>

            <p>If you have any questions, please contact our support team.</p>

            <p>Best regards,<br>
            GreenMarket Team</p>
        </div>
        <div class="footer">
            <p>This is an automated message, please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} GreenMarket. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
