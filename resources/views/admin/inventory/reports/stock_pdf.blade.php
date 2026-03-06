<!DOCTYPE html>
<html>
<head>
	<title>Stock Report - {{ date('Y-m-d') }}</title>
	<style>
		body { font-family: sans-serif; font-size: 12px; color: #333; }
		.header { text-align: center; margin-bottom: 10px; }
		.header h1 { margin: 0; color: #10b981; }
		.header p { margin: 5px 0; color: #666; }
		.summary { 
			width: 100%; 
			margin-bottom: 10px; 
			text-align: center;
		}
		.summary-box { 
			width: 20%; 
			display: inline-block;
			margin: 0 10px;
			padding: 8px 3px; 
			background: #f8fafc; 
			border: 1px solid #e2e8f0; 
			text-align: center;
			vertical-align: top;
		}
		.summary-box .value { 
			font-size: 15px; 
			font-weight: bold; 
			display: block; 
			margin-bottom: 4px; 
		}
		.summary-box .label { 
			font-size: 10px; 
			color: #64748b; 
			text-transform: uppercase; 
		}
		table { width: 100%; border-collapse: collapse; margin-top: 20px; }
		th { background: #f8fafc; padding: 10px; text-align: left; font-size: 11px; border-bottom: 2px solid #e2e8f0; }
		td { padding: 10px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
		.text-end { text-align: right; }
		.fw-bold { font-weight: bold; }
		.status { padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: bold; }
		.status-in-stock { background: #dcfce7; color: #15803d; }
		.status-low-stock { background: #fef3c7; color: #b45309; }
		.status-critical { background: #fee2e2; color: #b91c1c; }
		.status-out-of-stock { background: #e0f2fe; color: #0369a1; }
	</style>
</head>
<body>
	<div class="header">
		<h1>GreenMarket Inventory Stock Report</h1>
		<p>Generated on {{ date('l, F j, Y \a\t H:i') }}</p>
	</div>

	@php
		$totalQty = $products->sum('quantity');
		$totalValue = $products->sum(function($p) { return $p->quantity * $p->selling_price; });
		$lowStockItems = $products->filter(function($p) { return $p->inventory_status == 'Low Stock' || $p->inventory_status == 'Critical'; })->count();
		$outOfStockItems = $products->filter(function($p) { return $p->inventory_status == 'Out of Stock'; })->count();
	@endphp

	<div class="summary">
		<div class="summary-box">
			<span class="value">{{ number_format($products->count()) }}</span>
			<span class="label">Total Products</span>
		</div>
		<div class="summary-box">
			<span class="value">LKR {{ number_format($totalValue, 2) }}</span>
			<span class="label">On-Hand Value</span>
		</div>
		<div class="summary-box">
			<span class="value">{{ $lowStockItems }}</span>
			<span class="label">Low/Critical</span>
		</div>
		<div class="summary-box">
			<span class="value">{{ $outOfStockItems }}</span>
			<span class="label">Out of Stock</span>
		</div>
	</div>

	<table>
		<thead>
			<tr>
				<th>Product</th>
				<th>Group</th>
				<th class="text-end">Current Stock</th>
				<th class="text-end">Price</th>
				<th class="text-end">Total Value</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
			@foreach($products as $product)
			@php
				$status = $product->inventory_status;
				$statusClass = strtolower(str_replace(' ', '-', $status));
			@endphp
			<tr>
				<td>{{ $product->product_name }}</td>
				<td>{{ $product->leadFarmer->group_name ?? 'N/A' }}</td>
				<td class="text-end fw-bold">{{ number_format($product->quantity, 2) }} {{ $product->unit_of_measure }}</td>
				<td class="text-end">LKR {{ number_format($product->selling_price, 2) }}</td>
				<td class="text-end fw-bold">LKR {{ number_format($product->quantity * $product->selling_price, 2) }}</td>
				<td>
					<span class="status status-{{ $statusClass }}">{{ $status }}</span>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</body>
</html>