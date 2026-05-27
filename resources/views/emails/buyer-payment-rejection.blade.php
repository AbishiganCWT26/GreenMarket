<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Inter', sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e1e1e1; border-radius: 10px; }
        .header { background: #f8f9fa; padding: 15px; border-radius: 8px 8px 0 0; text-align: center; }
        .logo { max-width: 200px; height: auto; margin-bottom: 15px; }
        .content { padding: 20px; }
        .reason { background: #fff5f5; border-left: 4px solid #e53e3e; padding: 15px; margin: 15px 0; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('assets/images/Logo Green Market.png') }}" alt="Green Market Logo" class="logo">
            <h2>Payment Rejection</h2>
            <p>Order #{{ $order->order_number }}</p>
        </div>
        <div class="content">
            <p>Dear {{ $order->buyer->name }},</p>
            <p>Your payment slip has been rejected for the following reason:</p>
            <div class="reason">
                {{ $rejectionReason }}
            </div>
            <p>If you are satisfied with the rejection reason, please re-upload the slip or contact the seller. We'll get your order moving as soon as your payment goes through! The delivery process starts right after the payment is accepted. Please note: The delivery process begins only after payment confirmation.</p>
            
            <p>To fix this, please visit your Order History page and re-upload your slip</p>
        </div>
    </div>
</body>
</html>