<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Movement Report</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; font-size: 11px; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #10b981; padding-bottom: 10px; }
        .header h1 { color: #10b981; margin: 0; font-size: 20px; }
        .header p { margin: 5px 0 0; color: #666; }
        
        .footer { position: fixed; bottom: -30px; left: 0px; right: 0px; height: 30px; text-align: center; color: #999; font-size: 9px; }
        .page-number:after { content: counter(page); }

        .summary-container { margin-bottom: 20px; width: 100%; border-collapse: collapse; }
        .summary-box { background: #f9fafb; border: 1px solid #e5e7eb; padding: 10px; text-align: center; width: 33.33%; }
        .summary-label { display: block; color: #6b7280; font-size: 9px; text-transform: uppercase; margin-bottom: 4px; }
        .summary-value { display: block; font-size: 14px; font-weight: bold; color: #111827; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f3f4f6; color: #374151; font-weight: bold; text-align: left; padding: 8px; border: 1px solid #e5e7eb; }
        td { padding: 8px; border: 1px solid #e5e7eb; vertical-align: middle; }
        tr:nth-child(even) { background-color: #fafafa; }

        .badge { display: inline-block; padding: 2px 6px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .badge-manual_add { background-color: #d1fae5; color: #065f46; }
        .badge-manual_reduce { background-color: #fee2e2; color: #991b1b; }
        .badge-manual_adjust { background-color: #fef3c7; color: #92400e; }
        .badge-order_placed { background-color: #dbeafe; color: #1e40af; }
        .badge-order_cancelled { background-color: #f3f4f6; color: #374151; }
        .badge-payment_confirmed { background-color: #e0e7ff; color: #3730a3; }

        .qty-plus { color: #059669; font-weight: bold; }
        .qty-minus { color: #dc2626; font-weight: bold; }
        
        .product-name { font-weight: bold; color: #111827; }
        .timestamp { color: #6b7280; font-size: 9px; }
        .reason { font-style: italic; color: #4b5563; font-size: 9px; max-width: 150px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Inventory Movement Report</h1>
        <p>Generated on {{ date('M d, Y h:i A') }}</p>
    </div>

    <table class="summary-container">
        <tr>
            <td class="summary-box">
                <span class="summary-label">Total Activities</span>
                <span class="summary-value">{{ $logs->count() }}</span>
            </td>
            <td class="summary-box">
                <span class="summary-label">Stock Inflow</span>
                <span class="summary-value">{{ number_format($logs->where('quantity_change', '>', 0)->sum('quantity_change'), 2) }}</span>
            </td>
            <td class="summary-box">
                <span class="summary-label">Stock Outflow</span>
                <span class="summary-value">{{ number_format(abs($logs->where('quantity_change', '<', 0)->sum('quantity_change')), 2) }}</span>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="15%">Date & Time</th>
                <th width="20%">Product</th>
                <th width="15%">Activity</th>
                <th width="12%">Qty Change</th>
                <th width="18%">Performed By</th>
                <th width="20%">Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>
                    <div class="timestamp">
                        {{ $log->created_at->format('Y-m-d') }}<br>
                        {{ $log->created_at->format('h:i A') }}
                    </div>
                </td>
                <td>
                    <div class="product-name">{{ $log->product->product_name ?? 'Unknown' }}</div>
                    <div style="font-size: 8px; color: #666;">ID: #{{ $log->product_id }}</div>
                </td>
                <td>
                    <span class="badge badge-{{ $log->type }}">
                        {{ ucwords(str_replace('_', ' ', $log->type)) }}
                    </span>
                </td>
                <td align="right">
                    <span class="{{ $log->quantity_change > 0 ? 'qty-plus' : ($log->quantity_change < 0 ? 'qty-minus' : '') }}">
                        {{ $log->quantity_change > 0 ? '+' : '' }}{{ number_format($log->quantity_change, 2) }}
                    </span>
                </td>
                <td>{{ $log->user->username ?? 'System' }}</td>
                <td>
                    <div class="reason">{{ $log->reason ?: '-' }}</div>
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
