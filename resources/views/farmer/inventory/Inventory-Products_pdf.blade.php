<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product Inventory Report</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; font-size: 11px; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #10b981; padding-bottom: 10px; }
        .header h1 { color: #10b981; margin: 0; font-size: 20px; }
        .header p { margin: 5px 0 0; color: #666; }
        
        .info-bar { background: #f9fafb; border: 1px solid #e5e7eb; padding: 8px 12px; margin-bottom: 20px; border-radius: 4px; }
        .info-bar span { margin-right: 20px; color: #4b5563; }
        .info-bar b { color: #111827; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f3f4f6; color: #374151; font-weight: bold; text-align: left; padding: 8px; border: 1px solid #e5e7eb; }
        td { padding: 8px; border: 1px solid #e5e7eb; vertical-align: middle; }
        tr:nth-child(even) { background-color: #fafafa; }

        .status-badge { display: inline-block; padding: 2px 6px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .status-in-stock { background-color: #d1fae5; color: #065f46; }
        .status-low-stock { background-color: #fef3c7; color: #92400e; }
        .status-critical { background-color: #fee2e2; color: #991b1b; }
        .status-out-of-stock { background-color: #f3f4f6; color: #374151; }

        .text-right { text-align: right; }
        .product-name { font-weight: bold; color: #111827; }
        .category { color: #6b7280; font-size: 9px; }
        
        .footer { position: fixed; bottom: -30px; left: 0px; right: 0px; height: 30px; text-align: center; color: #999; font-size: 9px; }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="header">
        <h1>Product Inventory Report</h1>
        <p>GreenMarket Platform</p>
    </div>

    <div class="info-bar">
        <span>Generated: <b>{{ date('M d, Y h:i A') }}</b></span>
        <span>User: <b>{{ Auth::user()->username }}</b></span>
    </div>

    <table>
        <thead>
            <tr>
                <th width="35%">Product Details</th>
                <th width="20%">Category</th>
                <th width="15%" class="text-right">Quantity</th>
                <th width="15%" class="text-right">Unit Price</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            @php
                $status = $product->inventory_status;
                $statusClass = 'status-' . strtolower(str_replace(' ', '-', $status));
            @endphp
            <tr>
                <td>
                    <div class="product-name">{{ $product->product_name }}</div>
                    <div style="font-size: 8px; color: #666;">ID: #{{ $product->id }}</div>
                </td>
                <td>
                    <div class="category">{{ $product->category->category_name ?? 'N/A' }}</div>
                    <div style="font-size: 8px; color: #999;">{{ $product->subcategory->subcategory_name ?? '' }}</div>
                </td>
                <td class="text-right">
                    <b>{{ number_format($product->quantity, 2) }}</b> {{ $product->unit_of_measure }}
                </td>
                <td class="text-right">
                    LKR {{ number_format($product->selling_price, 2) }}
                </td>
                <td>
                    <span class="status-badge {{ $statusClass }}">
                        {{ $status }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Page <span class="page-number"></span> | GreenMarket Inventory Management System
    </div>
</body>
</html>
