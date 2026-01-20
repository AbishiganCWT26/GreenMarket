<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Order Receipt</title>
</head>

<body style="font-family: Arial, sans-serif;">

<!-- Logo Section -->
<div style="text-align: center; background: #10B981; padding: 20px; margin-bottom: 20px;">
    @php
        $logoPngPath = public_path('assets/images/Logo-4.png');
        $logoSvgPath = public_path('assets/images/Logo-4.svg');
    @endphp

    @if(file_exists($logoPngPath))
        <img src="{{ $message->embed($logoPngPath) }}" alt="GreenMarket Logo" style="max-width: 120px; height: auto; display: block; margin: 0 auto;">
    @elseif(file_exists($logoSvgPath))
        <img src="{{ $message->embed($logoSvgPath) }}" alt="GreenMarket Logo" style="max-width: 120px; height: auto; display: block; margin: 0 auto;">
    @else
        <h2 style="color: white; margin: 0;">GreenMarket</h2>
    @endif
</div>

<h2>Thank you for your purchase!</h2>

<p>Your order <strong>#{{ $order->order_number }}</strong> has been confirmed.</p>

<h3>Order Summary</h3>

<table width="100%" border="1" cellspacing="0" cellpadding="8">
    <thead>
        <tr>
            <th>Item</th>
            <th>Qty</th>
            <th>Price (LKR)</th>
        </tr>
    </thead>

    <tbody>
        @foreach($order->items as $i)
        <tr>
            <td>{{ $i->product->name }}</td>
            <td>{{ $i->quantity }}</td>
            <td>{{ number_format($i->price,2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h3>Total: LKR {{ number_format($order->total_amount,2) }}</h3>

<p>We appreciate your business.</p>

</body>
</html>
