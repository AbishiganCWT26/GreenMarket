<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #4CAF50;
        }
        .logo {
            max-width: 150px;
            height: auto;
        }
        .logo-text {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
            margin: 10px 0;
        }
        .content {
            padding: 30px 20px;
        }
        .order-details {
            background-color: #f9f9f9;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            margin: 20px 0;
        }
        .detail-item {
            margin: 10px 0;
            display: flex;
        }
        .detail-label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        .detail-value {
            flex: 1;
        }
        .message-box {
            background-color: #e8f5e9;
            border: 1px solid #c8e6c9;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #eee;
            color: #777;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 0;
        }
        .important-note {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        @media (max-width: 600px) {
            .container {
                padding: 10px;
            }
            .detail-item {
                flex-direction: column;
            }
            .detail-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @php
				$logoPngPath = public_path('assets/images/Logo-4.png');
				$logoSvgPath = public_path('assets/images/Logo-4.svg');
			@endphp

			@if(file_exists($logoPngPath))
				<img src="{{ $message->embed($logoPngPath) }}" alt="GreenMarket Logo" style="max-width: 100px; height: auto; display: block; margin: 0 auto 15px;">
			@elseif(file_exists($logoSvgPath))
				<img src="{{ $message->embed($logoSvgPath) }}" alt="GreenMarket Logo" style="max-width: 100px; height: auto; display: block; margin: 0 auto 15px;">
			@else
				<h2 style="color: var(--primary-green); margin: 0 0 10px;">GreenMarket</h2>
			@endif

            <h2 style="color: #4CAF50; margin-top: 10px;">
                @if($mailData['type'] === 'farmer')
                    New Order Received
                @else
                    New COD Order Notification
                @endif
            </h2>
        </div>

        <div class="content">
            <p>Dear {{ $mailData['type'] === 'farmer' ? $mailData['farmer_name'] : $mailData['lead_farmer_name'] }},</p>

            <div class="order-details">
                <div class="detail-item">
                    <span class="detail-label">Order Number:</span>
                    <span class="detail-value"><strong>{{ $mailData['order_number'] }}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Buyer Name:</span>
                    <span class="detail-value">{{ $mailData['buyer_name'] }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Order Date:</span>
                    <span class="detail-value">{{ date('M d, Y h:i A', strtotime($mailData['order_date'])) }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Products:</span>
                    <span class="detail-value">{{ $mailData['product_list'] }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value"><strong>Rs. {{ number_format($mailData['total_amount'], 2) }}</strong></span>
                </div>
                @if($mailData['type'] === 'lead_farmer')
                <div class="detail-item">
                    <span class="detail-label">Farmer Name:</span>
                    <span class="detail-value">{{ $mailData['farmer_name'] }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Farmer Contact:</span>
                    <span class="detail-value">{{ $mailData['farmer_mobile'] }}</span>
                </div>
                @endif
            </div>

            <div class="message-box">
                <p>{{ $mailData['message'] }}</p>
            </div>

            @if($mailData['type'] === 'farmer')
            <div class="important-note">
                <h4 style="margin-top: 0; color: #856404;">Important Instructions:</h4>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Prepare the ordered products for pickup</li>
                    <li>Ensure products meet quality standards</li>
                    <li>Keep products ready for lead farmer collection</li>
                    <li>Contact lead farmer if you have any questions</li>
                </ul>
            </div>
            @else
            <div class="important-note">
                <h4 style="margin-top: 0; color: #856404;">Action Required:</h4>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Contact the farmer for product pickup</li>
                    <li>Collect products from farmer (outside system)</li>
                    <li>Deliver products to buyer</li>
                    <li>Collect payment from buyer on delivery (Cash on Delivery)</li>
                    <li>Update payment status in the system</li>
                    <li>Give payment to farmer (outside system)</li>
                </ul>
            </div>
            @endif

            <p style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/login') }}" class="button">Login to System</a>
            </p>
        </div>

        <div class="footer">
            <p>This is an automated notification from GreenMarket System.</p>
            <p>Please do not reply to this email.</p>
            <p>© {{ date('Y') }} GreenMarket. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
