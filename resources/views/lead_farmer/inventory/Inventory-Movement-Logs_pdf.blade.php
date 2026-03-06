<!DOCTYPE html>
<html>
<head>
	<title>Movement Logs Report - {{ date('Y-m-d') }}</title>
	<style>
		body { font-family: sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
		.header { text-align: center; margin-bottom: 10px; }
		.header h1 { margin: 0; color: #10b981; font-size: 20px; }
		.header p { margin: 5px 0; color: #666; font-size: 12px; }
		
		.summary { 
			width: 100%; 
			margin-bottom: 10px; 
			text-align: center;
		}
		.summary-box { 
			width: 25%; 
			display: inline-block;
			margin: 0 10px;
			padding: 8px 5px; 
			background: #f8fafc; 
			border: 1px solid #e2e8f0; 
			text-align: center;
			vertical-align: top;
		}
		.summary-box .value { 
			font-size: 16px; 
			font-weight: bold; 
			display: block; 
			margin-bottom: 2px; 
			color: #1e293b;
		}
		.summary-box .label { 
			font-size: 9px; 
			color: #64748b; 
			text-transform: uppercase; 
			letter-spacing: 0.5px;
		}

		table { width: 100%; border-collapse: collapse; margin-top: 5px; }
		th { background: #f8fafc; padding: 8px 10px; text-align: left; font-size: 9px; border-bottom: 2px solid #e2e8f0; color: #475569; text-transform: uppercase; }
		td { padding: 4px 5px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
		
		.text-end { text-align: right; }
		.fw-bold { font-weight: bold; }
		
		.badge { 
			padding: 3px 3px; 
			border-radius: 4px; 
			font-size: 9px;  
			display: inline-block;
		}
		
		/* Activity Types Styles */
		.log-order_placed { background: #dbeafe; color: #1d4ed8; }
		.log-order_cancelled { background: #fee2e2; color: #b91c1c; }
		.log-payment_confirmed { background: #dcfce7; color: #15803d; }
		.log-manual_add { background: #f0fdf4; color: #166534; }
		.log-manual_reduce { background: #fff1f2; color: #9f1239; }
		.log-manual_adjust { background: #fef3c7; color: #92400e; }
		
		.qty-up { color: #15803d; font-weight: bold; }
		.qty-down { color: #b91c1c; font-weight: bold; }
		
		.footer { margin-top: 30px; text-align: center; font-size: 9px; color: #94a3b8; }
	</style>
</head>
<body>
	<div class="header">
		<h1>GreenMarket Movement Logs Report</h1>
		<p>Lead Farmer Group: {{ Auth::user()->leadFarmer->group_name }}</p>
		<p>Generated on {{ date('l, F j, Y \a\t H:i') }}</p>
	</div>

	@php
		$totalEntries = $logs->count();
		$additions = $logs->where('quantity_change', '>', 0)->count();
		$reductions = $logs->where('quantity_change', '<', 0)->count();
	@endphp

	<div class="summary">
		<div class="summary-box">
			<span class="value">{{ number_format($totalEntries) }}</span>
			<span class="label">Total Activities</span>
		</div>
		<div class="summary-box">
			<span class="value">{{ $additions }}</span>
			<span class="label">Stock Inflow</span>
		</div>
		<div class="summary-box">
			<span class="value">{{ $reductions }}</span>
			<span class="label">Stock Outflow</span>
		</div>
	</div>

	<table>
		<thead>
			<tr>
				<th style="width: 15%;">Date & Time</th>
				<th style="width: 15%;">Farmer</th>
				<th style="width: 15%;">Product</th>
				<th style="width: 10%;">Activity</th>
				<th style="width: 12%;" class="text-end">Qty Change</th>
				<th style="width: 13%;">Performed By</th>
				<th style="width: 20%;">Notes</th>
			</tr>
		</thead>
		<tbody>
			@foreach($logs as $log)
			<tr>
				<td>
					<span class="fw-bold">{{ $log->created_at->format('d M Y') }}</span><br>
					<small style="color: #64748b;">{{ $log->created_at->format('H:i:s') }}</small>
				</td>
				<td>
					{{ $log->product->farmer->name ?? 'N/A' }}
				</td>
				<td>
					{{ $log->product->product_name ?? 'N/A' }}
				</td>
				<td>
					<span class="badge log-{{ $log->type }}">
						{{ str_replace('_', ' ', $log->type) }}
					</span>
				</td>
				<td class="text-end">
					<span class="{{ $log->quantity_change > 0 ? 'qty-up' : ($log->quantity_change < 0 ? 'qty-down' : '') }}">
						{{ $log->quantity_change > 0 ? '+' : '' }}{{ number_format($log->quantity_change, 2) }}
					</span>
				</td>
				<td>{{ $log->user->name ?? ($log->user->username ?? 'System') }}</td>
				<td><small>{{ $log->reason ?? '—' }}</small></td>
			</tr>
			@endforeach
		</tbody>
	</table>

	<div class="footer">
		<p>© {{ date('Y') }} CSIAP GreenMarket All rights reserved.</p>
	</div>
</body>
</html>
