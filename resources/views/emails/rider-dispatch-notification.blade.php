<!DOCTYPE html>
<html>
<head>
    <title>New Delivery Available</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px;">New Delivery Available</h2>
    <p>A new shipment is available for delivery in your district.</p>
    
    <div style="background-color: #f9f9f9; border-left: 4px solid #3498db; padding: 15px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #2980b9;">Shipment Details:</h3>
        <ul style="list-style-type: none; padding-left: 0;">
            <li style="margin-bottom: 10px;"><strong>Order Number:</strong> {{ $shipmentData['order_number'] ?? 'N/A' }}</li>
            <li style="margin-bottom: 10px;"><strong>Buyer Name:</strong> {{ $shipmentData['buyer_name'] ?? 'N/A' }}</li>
            <li style="margin-bottom: 10px;"><strong>Product Details:</strong> {{ $shipmentData['product_details'] ?? 'N/A' }}</li>
            <li style="margin-bottom: 10px;"><strong>Delivery Address:</strong> {{ $shipmentData['delivery_address'] ?? 'N/A' }}</li>
            <li style="margin-bottom: 10px;"><strong>Bus Details:</strong> {{ $shipmentData['bus_details'] ?? 'N/A' }}</li>
            <li style="margin-bottom: 10px;"><strong>Estimated Time of Arrival (ETA):</strong> <span style="color: #e74c3c; font-weight: bold;">{{ $shipmentData['eta'] ?? 'N/A' }}</span></li>
        </ul>
    </div>

    <p style="background-color: #e8f4f8; padding: 10px; border-radius: 5px; text-align: center;">
        Please log in to the system immediately to accept or manage this delivery.
    </p>

    <div style="margin-top: 30px; font-size: 0.9em; color: #7f8c8d; border-top: 1px solid #eee; padding-top: 10px;">
        <p>This is an automated notification. Please do not reply to this email.</p>
    </div>
</body>
</html>
