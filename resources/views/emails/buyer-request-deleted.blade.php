<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Request Removed</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            line-height: 1.6;
        }

        .email-container {
            max-width: 700px;
            margin: 15px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #16a34a, #059669);
            padding: 25px;
            text-align: center;
        }

        .logo {
            width: 64px;
            height: 64px;
            margin-bottom: 10px;
        }

        .header h1 {
            color: #ffffff;
            font-size: 20px;
            font-weight: 600;
        }

        /* Content */
        .content {
            padding: 10px;
        }

        .title {
            text-align: center;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #111827;
        }

        .intro {
            margin-bottom: 20px;
        }

        .notice-box {
            background-color: #fff7ed;
            border-left: 5px solid #f59e0b;
            padding: 18px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-size: 15px;
        }

        .section-title {
            font-size: 17px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        /* Table */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #f9fafb;
            border-radius: 6px;
            overflow: hidden;
        }

        .details-table tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .details-table tr:last-child {
            border-bottom: none;
        }

        .details-table td {
            padding: 12px 15px;
            font-size: 14px;
        }

        .label {
            font-weight: 600;
            color: #6b7280;
            width: 40%;
        }

        .value {
            font-weight: 500;
            color: #111827;
        }

        .btn-wrapper {
            text-align: center;
            margin: 10px 0 10px;
        }

        .dashboard-btn {
            display: inline-block;
            padding: 12px 34px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            color: #16a34a;
            border: 2px solid #16a34a;
            background-color: #ffffff;
            transition: all 0.3s ease;
        }

        .dashboard-btn:hover {
            background-color: #16a34a;
            color: #ffffff;
        }

        /* Footer */
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }

        .footer p {
            margin-bottom: 6px;
        }

        @media (max-width: 600px) {
            .content {
                padding: 20px;
            }

            .details-table td {
                display: block;
                width: 100%;
            }

            .label {
                margin-bottom: 4px;
            }
        }
    </style>
</head>

<body>
<div class="email-container">

    <!-- Header -->
    <div class="header">
        <div style="text-align: center; margin-bottom: 20px;">
            @php
                // Define the logo paths
                $logoPngPath = public_path('assets/images/Logo-4.png');
                $logoSvgPath = public_path('assets/images/Logo-4.svg');
            @endphp

            @if(file_exists($logoPngPath))
                <img src="{{ $message->embed($logoPngPath) }}" alt="GreenMarket Logo" style="max-width: 80px; height: auto; display: block; margin: 0 auto 10px;">
            @elseif(file_exists($logoSvgPath))
                <img src="{{ $message->embed($logoSvgPath) }}" alt="GreenMarket Logo" style="max-width: 80px; height: auto; display: block; margin: 0 auto 10px;">
            @endif
                <p style="font-weight: bold; font-size: 24px; color: #ffffff; margin: 10px 0;">GreenMarket</p>

        </div>
        <h1>Product Request Notification</h1>
    </div>

    <!-- Content -->
    <div class="content">
        <h2 class="title">Product Request Removed</h2>

        <p class="intro">
            Dear <strong>{{ $mailData['buyer_name'] }}</strong>,
        </p>

        <div class="notice-box">
            <p>
                After careful review by our administration team, your product request has been removed as it does not currently meet our platform guidelines.
            </p>
            <p>
                We truly appreciate your interest in GreenMarket and encourage you to submit a new request that aligns with our product standards.
            </p>
        </div>

        <h3 class="section-title">Request Details</h3>

        <table class="details-table">
            <tr>
                <td class="label">Product Name</td>
                <td class="value">{{ $mailData['product_name'] }}</td>
            </tr>
            <tr>
                <td class="label">Needed Date</td>
                <td class="value">{{ \Carbon\Carbon::parse($mailData['needed_date'])->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td class="label">Quantity</td>
                <td class="value">{{ $mailData['quantity'] }}</td>
            </tr>
        </table>

        <div class="btn-wrapper">
            <a href="{{ url('/buyer/dashboard') }}" class="dashboard-btn">
                Go to Dashboard
            </a>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>© {{ date('Y') }} GreenMarket. All rights reserved.</p>
        <p>This is an automated message. Please do not reply.</p>
    </div>

</div>
</body>
</html>
