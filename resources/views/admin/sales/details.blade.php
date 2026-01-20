@extends('admin.layouts.admin_master')

@section('title', 'Sales Order Details')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
	:root{
		--primary-green:#10B981;
		--dark-green:#059669;
		--body-bg:#f6f8fa;
		--card-bg:#ffffff;
		--text-color:#0f1724;
		--muted:#6b7280;
		--accent-amber:#f59e0b;
		--blue:#3b82f6;
		--purple:#8b5cf6;
		--yellow:#f59e0b;
		--shadow-sm:0 1px 3px rgba(15,23,36,0.04);
		--shadow-md:0 6px 12px rgba(15,23,36,0.08);
		--shadow-lg:0 10px 20px rgba(15,23,36,0.12);
		--border-radius:8px;
		--transition:all 0.25s ease;
	}

	body{
		background-color:var(--body-bg);
		color:var(--text-color);
		font-family:'Segoe UI',system-ui,-apple-system,sans-serif;
	}

	.sales-details-container{
		max-width:900px;
		margin:0 auto;
		padding:14px;
		animation:fadeInUp 0.45s ease;
	}

	@keyframes fadeInUp{
		from{opacity:0;transform:translateY(8px)}
		to{opacity:1;transform:translateY(0)}
	}

	.back-btn{
		display:inline-flex;
		align-items:center;
		gap:6px;
		padding:8px 16px;
		background:var(--blue);
		color:#fff;
		border:none;
		border-radius:6px;
		font-size:13px;
		font-weight:600;
		cursor:pointer;
		box-shadow:var(--shadow-sm);
		transition:var(--transition);
		margin-bottom:12px;
	}

	.back-btn:hover{
		background:#2563eb;
		transform:translateY(-1px);
		box-shadow:var(--shadow-md);
	}

	.compact-card{
		background:var(--card-bg);
		border-radius:var(--border-radius);
		box-shadow:var(--shadow-sm);
		margin-bottom:12px;
		border:1px solid #e5e7eb;
		transition:var(--transition);
		animation:cardPop 0.4s ease;
	}

	@keyframes cardPop{
		from{opacity:0;transform:scale(0.98)}
		to{opacity:1;transform:scale(1)}
	}

	.compact-card:hover{
		transform:translateY(-2px);
		box-shadow:var(--shadow-lg);
		border-color:var(--primary-green);
	}

	.card-header-compact{
		padding:12px 14px;
		background:linear-gradient(135deg,var(--primary-green),var(--dark-green));
		display:flex;
		justify-content:space-between;
		align-items:center;
		gap:8px;
	}

	.card-header-compact h2{
		margin:0;
		font-size:15px;
		font-weight:600;
		color:#fff;
		display:flex;
		align-items:center;
		gap:6px;
	}

	.status-badge-compact{
		padding:4px 12px;
		border-radius:50px;
		font-size:11px;
		font-weight:700;
		background:#fff;
	}

	.status-paid{color:var(--primary-green)}
	.status-completed{color:var(--blue)}

	.card-body-compact{
		padding:14px;
	}

	.compact-grid{
		display:grid;
		grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
		gap:10px;
		margin-bottom:12px;
	}

	.compact-section{
		background:#f8fafc;
		border-radius:6px;
		padding:12px;
		border:1px solid #e5e7eb;
		transition:var(--transition);
	}

	.compact-section:hover{
		background:#fff;
		transform:translateY(-1px);
		box-shadow:var(--shadow-sm);
	}

	.compact-section h3{
		margin:0 0 10px 0;
		font-size:14px;
		font-weight:600;
		color:var(--primary-green);
		display:flex;
		align-items:center;
		gap:6px;
		padding-bottom:6px;
		border-bottom:1px solid #e5e7eb;
	}

	.info-row-compact{
		display:flex;
		justify-content:space-between;
		padding:5px 0;
		font-size:13px;
	}

	.info-row-compact:not(:last-child){
		border-bottom:1px dashed #e5e7eb;
	}

	.info-label-compact{
		color:var(--muted);
		font-weight:500;
	}

	.info-value-compact{
		font-weight:600;
		text-align:right;
	}

	.amount-compact{
		color:var(--primary-green);
		font-weight:700;
	}

	.products-section-compact{
		background:#f8fafc;
		border-radius:6px;
		padding:12px;
		border:1px solid #e5e7eb;
	}

	.products-section-compact h3{
		margin:0 0 10px 0;
		font-size:14px;
		font-weight:600;
		color:var(--purple);
		display:flex;
		align-items:center;
		gap:6px;
	}

	.products-table-compact{
		width:100%;
		border-collapse:collapse;
		font-size:13px;
		background:#fff;
		box-shadow:var(--shadow-sm);
	}

	.products-table-compact th{
		background:var(--purple);
		color:#fff;
		padding:8px;
		font-size:11px;
		font-weight:600;
	}

	.products-table-compact td{
		padding:8px;
		border-bottom:1px solid #f1f5f9;
	}

	.products-table-compact tr:hover{
		background:#f8fafc;
	}

	.total-row-compact{
		background:#fef3c7 !important;
		font-weight:700;
	}

	.action-buttons-compact{
		display:flex;
		gap:10px;
		margin-top:12px;
		flex-wrap:wrap;
	}

	.action-btn-compact{
		padding:8px 16px;
		font-size:13px;
		font-weight:600;
		border:none;
		border-radius:6px;
		cursor:pointer;
		display:inline-flex;
		align-items:center;
		gap:6px;
		box-shadow:var(--shadow-sm);
		transition:var(--transition);
	}

	.print-btn-compact{
		background:var(--accent-amber);
		color:#fff;
	}

	.action-btn-compact:hover{
		transform:translateY(-1px) scale(1.02);
		box-shadow:var(--shadow-md);
	}

	@media(max-width:991px){
		.sales-details-container{padding:12px}
		.compact-grid{grid-template-columns:repeat(2,1fr)}
	}

	@media(max-width:767px){
		.compact-grid{grid-template-columns:1fr}
		.card-header-compact{flex-direction:column;text-align:center}
		.products-table-compact{display:block;overflow-x:auto}
		.action-buttons-compact{flex-direction:column}
		.action-btn-compact{width:100%;justify-content:center}
		.back-btn{width:100%;justify-content:center}
	}

	@media(max-width:480px){
		.sales-details-container{padding:10px}
		.card-body-compact{padding:10px}
		.compact-section{padding:10px}
		.products-section-compact{padding:10px}
		.info-row-compact{font-size:12px}
		.products-table-compact{font-size:12px}
	}
</style>

@endsection

@section('content')
<div class="sales-details-container">
	<button onclick="goBack()" class="back-btn">
		<i class="fas fa-arrow-left"></i> Back to Sales
	</button>

	<div class="compact-card">
		<div class="card-header-compact">
			<h2>
				<i class="fas fa-receipt"></i>
				Order: {{ $order->order_number }}
			</h2>
			<span class="status-badge-compact status-{{ $order->order_status }}">
				{{ ucfirst($order->order_status) }}
			</span>
		</div>

		<div class="card-body-compact">
			<div class="compact-grid">
				<div class="compact-section">
					<h3><i class="fas fa-user"></i> Buyer Details</h3>
					<div class="info-row-compact">
						<span class="info-label-compact">Name:</span>
						<span class="info-value-compact">{{ $order->buyer_name ?? 'N/A' }}</span>
					</div>
					<div class="info-row-compact">
						<span class="info-label-compact">Contact:</span>
						<span class="info-value-compact">{{ $order->buyer_mobile ?? 'N/A' }}</span>
					</div>
					<div class="info-row-compact">
						<span class="info-label-compact">Order Date:</span>
						<span class="info-value-compact">{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y h:i A') }}</span>
					</div>
				</div>

				<div class="compact-section">
					<h3><i class="fas fa-tractor"></i> Farmer Details</h3>
					<div class="info-row-compact">
						<span class="info-label-compact">Lead Farmer:</span>
						<span class="info-value-compact">{{ $order->lead_farmer_name ?? 'N/A' }}</span>
					</div>
					<div class="info-row-compact">
						<span class="info-label-compact">Contact:</span>
						<span class="info-value-compact">{{ $order->lead_farmer_mobile ?? 'N/A' }}</span>
					</div>
					<div class="info-row-compact">
						<span class="info-label-compact">Individual Farmer:</span>
						<span class="info-value-compact">{{ $order->farmer_name ?? 'N/A' }}</span>
					</div>
				</div>

				<div class="compact-section">
					<h3><i class="fas fa-money-bill"></i> Payment Details</h3>
					<div class="info-row-compact">
						<span class="info-label-compact">Total Amount:</span>
						<span class="info-value-compact amount-compact">LKR {{ number_format($order->total_amount, 2) }}</span>
					</div>
					<div class="info-row-compact">
						<span class="info-label-compact">Status:</span>
						<span class="info-value-compact">{{ ucfirst($order->order_status) }}</span>
					</div>
					@if($order->paid_date)
					<div class="info-row-compact">
						<span class="info-label-compact">Paid Date:</span>
						<span class="info-value-compact">{{ \Carbon\Carbon::parse($order->paid_date)->format('M d, Y') }}</span>
					</div>
					@endif
					@if($order->completed_date)
					<div class="info-row-compact">
						<span class="info-label-compact">Completed Date:</span>
						<span class="info-value-compact">{{ \Carbon\Carbon::parse($order->completed_date)->format('M d, Y') }}</span>
					</div>
					@endif
				</div>
			</div>

			<div class="products-section-compact">
				<h3><i class="fas fa-shopping-basket"></i> Ordered Products</h3>
				<table class="products-table-compact">
					<thead>
						<tr>
							<th>Product Name</th>
							<th>Quantity</th>
							<th>Unit Price (LKR)</th>
							<th>Total (LKR)</th>
						</tr>
					</thead>
					<tbody>
						@if(isset($orderItems) && count($orderItems) > 0)
							@foreach($orderItems as $item)
							<tr>
								<td>{{ $item->product_name ?? 'N/A' }}</td>
								<td>{{ number_format($item->quantity_ordered, 2) }}</td>
								<td>{{ number_format($item->unit_price_snapshot, 2) }}</td>
								<td>{{ number_format($item->item_total, 2) }}</td>
							</tr>
							@endforeach
						@else
							<tr>
								<td colspan="4" style="text-align: center; padding: 20px;">No product details available</td>
							</tr>
						@endif
					</tbody>
					<tfoot>
						<tr class="total-row-compact">
							<td colspan="3" style="text-align: right;">Grand Total:</td>
							<td style="font-weight: 700;">LKR {{ number_format($order->total_amount, 2) }}</td>
						</tr>
					</tfoot>
				</table>
			</div>

			<div class="action-buttons-compact">
				<button class="action-btn-compact print-btn-compact" onclick="printOrder()">
					<i class="fas fa-print"></i> Print Details
				</button>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
	function goBack() {
		window.history.back();
	}

	function printOrder() {
		const printContent = `
			<!DOCTYPE html>
			<html>
			<head>
				<title>Order Details - {{ $order->order_number }}</title>
				<style>
                    body { font-family: Arial, sans-serif; margin: 5px; font-size: 13px; line-height: 1.2; }
                    .header { text-align: center; margin-bottom: 5px; border-bottom: 2px solid #10B981; padding-bottom: 5px; }
                    .section { margin-bottom: 8px; }
                    .section h3 { color: #059669; border-bottom: 1px solid #ddd; padding-bottom: 2px; margin: 5px 0; font-size: 15px; }
                    .info-row { display: flex; justify-content: space-between; padding: 2px 0; }
                    table { width: 100%; border-collapse: collapse; margin: 5px 0; }
                    th { background: #8b5cf6; color: white; padding: 4px 8px; text-align: left; }
                    td { padding: 4px 8px; border-bottom: 1px solid #ddd; }
                    .total-row { background: #fef3c7; font-weight: bold; }
                    .footer { text-align: center; margin-top: 10px; color: #6b7280; font-size: 10px; }
                    @media print {
                        body { margin: 0; padding: 5px; }
                        .no-print { display: none; }
                    }
                </style>
			</head>
			<body>
				<div class="header">
					<h1>Order Details - {{ $order->order_number }}</h1>
					<p>Generated on: ${new Date().toLocaleDateString()} ${new Date().toLocaleTimeString()}</p>
				</div>

				<div class="section">
					<h3>Buyer Information</h3>
					<div class="info-row"><strong>Name:</strong> {{ $order->buyer_name ?? 'N/A' }}</div>
					<div class="info-row"><strong>Contact:</strong> {{ $order->buyer_mobile ?? 'N/A' }}</div>
					<div class="info-row"><strong>Order Date:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y h:i A') }}</div>
				</div>

				<div class="section">
					<h3>Farmer Information</h3>
					<div class="info-row"><strong>Lead Farmer:</strong> {{ $order->lead_farmer_name ?? 'N/A' }}</div>
					<div class="info-row"><strong>Contact:</strong> {{ $order->lead_farmer_mobile ?? 'N/A' }}</div>
					<div class="info-row"><strong>Individual Farmer:</strong> {{ $order->farmer_name ?? 'N/A' }}</div>
				</div>

				<div class="section">
					<h3>Payment Information</h3>
					<div class="info-row"><strong>Total Amount:</strong> LKR {{ number_format($order->total_amount, 2) }}</div>
					<div class="info-row"><strong>Status:</strong> {{ ucfirst($order->order_status) }}</div>
					@if($order->paid_date)
					<div class="info-row"><strong>Paid Date:</strong> {{ \Carbon\Carbon::parse($order->paid_date)->format('M d, Y') }}</div>
					@endif
				</div>

				<div class="section">
					<h3>Ordered Products</h3>
					<table>
						<thead>
							<tr>
								<th>Product Name</th>
								<th>Quantity</th>
								<th>Unit Price (LKR)</th>
								<th>Total (LKR)</th>
							</tr>
						</thead>
						<tbody>
							@if(isset($orderItems) && count($orderItems) > 0)
								@foreach($orderItems as $item)
								<tr>
									<td>{{ $item->product_name ?? 'N/A' }}</td>
									<td>{{ number_format($item->quantity_ordered, 2) }}</td>
									<td>{{ number_format($item->unit_price_snapshot, 2) }}</td>
									<td>{{ number_format($item->item_total, 2) }}</td>
								</tr>
								@endforeach
							@endif
						</tbody>
						<tfoot>
							<tr class="total-row">
								<td colspan="3" style="text-align: right;"><strong>Grand Total:</strong></td>
								<td><strong>LKR {{ number_format($order->total_amount, 2) }}</strong></td>
							</tr>
						</tfoot>
					</table>
				</div>

				<div class="footer">
					<p>Generated by GreenMarket</p>
				</div>
				<script>
					window.onload = function() {
						window.print();
					}
				<\/script>
			</body>
			</html>
		`;

		const printWindow = window.open('', '_blank');
		printWindow.document.write(printContent);
		printWindow.document.close();
	}

	document.addEventListener('DOMContentLoaded', function() {
		const cards = document.querySelectorAll('.compact-card, .compact-section');
		cards.forEach((card, index) => {
			card.style.animationDelay = `${index * 0.1}s`;
		});
	});
</script>
@endsection
