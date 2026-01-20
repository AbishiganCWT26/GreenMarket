@extends('farmer.layouts.farmer_master')

@section('title', 'Active Orders')
@section('page-title', 'Active Orders')

@section('styles')
<style>
	:root {
		--primary-green: #10B981;
		--dark-green: #059669;
		--body-bg: #f6f8fa;
		--card-bg: #ffffff;
		--text-color: #0f1724;
		--muted: #6b7280;
		--accent-amber: #f59e0b;
		--blue: #3b82f6;
		--purple: #8b5cf6;
		--yellow: #f59e0b;
		--shadow-sm: 0 1px 3px rgba(15,23,36,0.04);
		--shadow-md: 0 7px 15px rgba(15,23,36,0.08);
	}

	.order-cards {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
		gap: 1.25rem;
		padding: 1rem 0;
	}

	.order-card {
		background: var(--card-bg);
		border-radius: 14px;
		box-shadow: var(--shadow-sm);
		border: 1px solid #e2e8f0;
		overflow: hidden;
		transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		position: relative;
	}

	.order-card:hover {
		transform: translateY(-6px);
		box-shadow: 0 12px 25px rgba(15,23,36,0.12);
		border-color: var(--primary-green);
	}

	.order-card::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		height: 4px;
		background: linear-gradient(90deg, var(--primary-green), var(--blue));
		opacity: 0;
		transition: opacity 0.3s ease;
	}

	.order-card:hover::before {
		opacity: 1;
	}

	.order-top {
		padding: 1.25rem;
		background: linear-gradient(135deg, #f0f9ff, #f8fafc);
		border-bottom: 1px solid #e2e8f0;
		position: relative;
	}

	.order-number {
		font-size: 1.1rem;
		font-weight: 700;
		color: var(--text-color);
		display: flex;
		align-items: center;
		gap: 0.5rem;
	}

	.order-number i {
		color: var(--primary-green);
	}

	.order-time {
		font-size: 0.85rem;
		color: var(--muted);
		display: flex;
		align-items: center;
		gap: 0.35rem;
		margin-top: 0.35rem;
	}

	.order-status-badge {
		position: absolute;
		top: 1.25rem;
		right: 1.25rem;
		padding: 0.35rem 0.85rem;
		border-radius: 20px;
		font-size: 0.75rem;
		font-weight: 700;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}

	.status-paid {
		background: linear-gradient(135deg, #dbeafe, #bfdbfe);
		color: #1e40af;
	}

	.status-ready {
		background: linear-gradient(135deg, #d1fae5, #a7f3d0);
		color: #065f46;
	}

	.customer-section {
		padding: 1rem 1.25rem;
		background: var(--card-bg);
	}

	.customer-info {
		display: flex;
		align-items: center;
		gap: 0.85rem;
	}

	.customer-avatar {
		width: 42px;
		height: 42px;
		border-radius: 50%;
		background: linear-gradient(135deg, var(--primary-green), var(--blue));
		display: flex;
		align-items: center;
		justify-content: center;
		color: white;
		font-size: 1.1rem;
	}

	.customer-details h6 {
		font-size: 0.95rem;
		font-weight: 600;
		margin-bottom: 0.15rem;
		color: var(--text-color);
	}

	.customer-details small {
		font-size: 0.8rem;
		color: var(--muted);
		display: flex;
		align-items: center;
		gap: 0.35rem;
	}

	.pickup-info {
		padding: 0.85rem 1.25rem;
		background: linear-gradient(135deg, #fef3c7, #fde68a);
		border-left: 3px solid var(--accent-amber);
		margin: 0.5rem 1.25rem;
		border-radius: 8px;
	}

	.pickup-info h6 {
		font-size: 0.9rem;
		font-weight: 600;
		margin-bottom: 0.35rem;
		color: #92400e;
		display: flex;
		align-items: center;
		gap: 0.5rem;
	}

	.pickup-info p {
		font-size: 0.8rem;
		color: #92400e;
		margin-bottom: 0.5rem;
		line-height: 1.4;
	}

	.pickup-map {
		display: inline-flex;
		align-items: center;
		gap: 0.35rem;
		font-size: 0.8rem;
		color: var(--blue);
		text-decoration: none;
		font-weight: 600;
		transition: all 0.2s ease;
	}

	.pickup-map:hover {
		color: var(--dark-green);
		transform: translateX(3px);
	}

	.order-items-list {
		padding: 0.85rem 1.25rem;
		max-height: 160px;
		overflow-y: auto;
		scrollbar-width: thin;
		scrollbar-color: var(--primary-green) #f1f5f9;
	}

	.order-items-list::-webkit-scrollbar {
		width: 5px;
	}

	.order-items-list::-webkit-scrollbar-track {
		background: #f1f5f9;
		border-radius: 10px;
	}

	.order-items-list::-webkit-scrollbar-thumb {
		background: var(--primary-green);
		border-radius: 10px;
	}

	.order-item {
		display: flex;
		align-items: center;
		padding: 0.65rem;
		margin-bottom: 0.5rem;
		background: #f8fafc;
		border-radius: 10px;
		border: 1px solid #e2e8f0;
		transition: all 0.2s ease;
	}

	.order-item:hover {
		transform: translateX(3px);
		background: #ffffff;
		border-color: var(--primary-green);
		box-shadow: 0 3px 8px rgba(16, 185, 129, 0.1);
	}

	.item-image {
		width: 50px;
		height: 50px;
		border-radius: 8px;
		object-fit: cover;
		border: 2px solid #e2e8f0;
		margin-right: 0.85rem;
		transition: all 0.3s ease;
	}

	.order-item:hover .item-image {
		transform: scale(1.05);
		border-color: var(--primary-green);
	}

	.item-info {
		flex: 1;
	}

	.item-name {
		font-size: 0.9rem;
		font-weight: 600;
		color: var(--text-color);
		margin-bottom: 0.15rem;
	}

	.item-meta {
		font-size: 0.75rem;
		color: var(--muted);
		display: flex;
		align-items: center;
		gap: 0.5rem;
	}

	.item-price {
		font-weight: 700;
		color: var(--dark-green);
		font-size: 0.9rem;
		background: linear-gradient(135deg, #d1fae5, #ecfdf5);
		padding: 0.35rem 0.65rem;
		border-radius: 6px;
		min-width: 80px;
		text-align: center;
		transition: all 0.3s ease;
	}

	.order-item:hover .item-price {
		transform: scale(1.05);
		box-shadow: 0 3px 8px rgba(16, 185, 129, 0.15);
	}

	.order-bottom {
		padding: 1rem 1.25rem;
		background: linear-gradient(135deg, #f8fafc, #f1f5f9);
		border-top: 1px solid #e2e8f0;
	}

	.order-total {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 1rem;
	}

	.total-label {
		font-size: 0.9rem;
		color: var(--muted);
	}

	.total-amount {
		font-size: 1.4rem;
		font-weight: 800;
		color: var(--text-color);
		background: linear-gradient(135deg, var(--primary-green), var(--blue));
		-webkit-background-clip: text;
		-webkit-text-fill-color: transparent;
		background-clip: text;
	}

	.payment-status {
		display: inline-flex;
		align-items: center;
		gap: 0.5rem;
		padding: 0.35rem 0.85rem;
		border-radius: 15px;
		font-size: 0.75rem;
		font-weight: 700;
		margin-bottom: 1rem;
	}

	.payment-completed {
		background: linear-gradient(135deg, #d1fae5, #a7f3d0);
		color: #065f46;
		border: 1px solid #10b981;
	}

	.payment-pending {
		background: linear-gradient(135deg, #fef3c7, #fde68a);
		color: #92400e;
		border: 1px solid #f59e0b;
	}

	.order-actions {
		display: flex;
		gap: 0.65rem;
	}

	.action-btn {
		flex: 1;
		padding: 0.65rem;
		border: none;
		border-radius: 8px;
		font-size: 0.85rem;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 0.5rem;
	}

	.action-btn i {
		font-size: 0.9rem;
	}

	.btn-view {
		background: linear-gradient(135deg, #3b82f6, #2563eb);
		color: white;
	}

	.btn-ready {
		background: linear-gradient(135deg, #10b981, #059669);
		color: white;
	}

	.btn-view:hover {
		transform: translateY(-2px);
		box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
	}

	.btn-ready:hover {
		transform: translateY(-2px);
		box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
	}

	.stats-cards {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
		gap: 1rem;
		margin-bottom: 2rem;
	}

	.stat-card {
		background: var(--card-bg);
		padding: 1.25rem;
		border-radius: 12px;
		box-shadow: var(--shadow-sm);
		display: flex;
		align-items: center;
		justify-content: space-between;
		border: 1px solid #e2e8f0;
		transition: all 0.3s ease;
	}

	.stat-card:hover {
		transform: translateY(-4px);
		box-shadow: var(--shadow-md);
		border-color: var(--primary-green);
	}

	.stat-content h3 {
		font-size: 1.8rem;
		font-weight: 800;
		margin-bottom: 0.25rem;
		color: var(--text-color);
	}

	.stat-content h6 {
		font-size: 0.85rem;
		color: var(--muted);
		font-weight: 500;
	}

	.stat-icon {
		width: 50px;
		height: 50px;
		border-radius: 10px;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 1.4rem;
		color: white;
		transition: all 0.3s ease;
	}

	.stat-card:hover .stat-icon {
		transform: rotate(10deg) scale(1.1);
	}

	.icon-paid {
		background: linear-gradient(135deg, #3b82f6, #1d4ed8);
	}

	.icon-ready {
		background: linear-gradient(135deg, #10b981, #059669);
	}

	.icon-revenue {
		background: linear-gradient(135deg, #8b5cf6, #7c3aed);
	}

	.empty-state {
		text-align: center;
		padding: 3rem 1.5rem;
		background: var(--card-bg);
		border-radius: 14px;
		box-shadow: var(--shadow-sm);
		border: 2px dashed #e2e8f0;
	}

	.empty-icon {
		font-size: 3.5rem;
		color: #cbd5e1;
		margin-bottom: 1rem;
		animation: bounce 2s infinite;
	}

	@keyframes bounce {
		0%, 100% { transform: translateY(0); }
		50% { transform: translateY(-10px); }
	}

	.empty-state h3 {
		font-size: 1.5rem;
		font-weight: 700;
		margin-bottom: 0.5rem;
		color: var(--text-color);
	}

	.empty-state p {
		color: var(--muted);
		margin-bottom: 1.5rem;
		font-size: 0.95rem;
	}

	.page-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 2rem;
		flex-wrap: wrap;
		gap: 1rem;
		padding-bottom: 1.25rem;
		border-bottom: 1px solid #e2e8f0;
	}

	.header-title h1 {
		font-size: 1.8rem;
		font-weight: 800;
		color: var(--text-color);
		display: flex;
		align-items: center;
		gap: 0.5rem;
	}

	.header-title h1 i {
		color: var(--primary-green);
	}

	.header-title p {
		color: var(--muted);
		font-size: 0.95rem;
		margin-top: 0.25rem;
	}

	.header-actions {
		display: flex;
		align-items: center;
		gap: 1rem;
	}

	.pending-count {
		background: linear-gradient(135deg, #f59e0b, #d97706);
		color: white;
		padding: 0.5rem 1rem;
		border-radius: 20px;
		font-size: 0.85rem;
		font-weight: 700;
		display: flex;
		align-items: center;
		gap: 0.5rem;
	}

	.refresh-btn {
		background: linear-gradient(135deg, #3b82f6, #2563eb);
		color: white;
		border: none;
		padding: 0.5rem 1.25rem;
		border-radius: 8px;
		font-size: 0.9rem;
		font-weight: 600;
		cursor: pointer;
		display: flex;
		align-items: center;
		gap: 0.5rem;
		transition: all 0.3s ease;
	}

	.refresh-btn:hover {
		transform: translateY(-2px) rotate(5deg);
		box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
	}

	.modal-product-img {
		width: 70px;
		height: 70px;
		border-radius: 8px;
		object-fit: cover;
		border: 2px solid #e2e8f0;
	}

	@media (max-width: 1200px) {
		.order-cards {
			grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
		}
	}

	@media (max-width: 992px) {
		.order-cards {
			grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
			gap: 1rem;
		}
		.stats-cards {
			grid-template-columns: repeat(2, 1fr);
		}
	}

	@media (max-width: 768px) {
		.order-cards {
			grid-template-columns: 1fr;
			gap: 1rem;
		}
		.stats-cards {
			grid-template-columns: 1fr;
			gap: 0.85rem;
		}
		.page-header {
			flex-direction: column;
			align-items: flex-start;
		}
		.header-actions {
			width: 100%;
			justify-content: space-between;
		}
		.order-actions {
			flex-direction: column;
		}
		.action-btn {
			width: 100%;
		}
	}

	@media (max-width: 480px) {
		.order-item {
			flex-direction: column;
			align-items: flex-start;
			gap: 0.5rem;
		}
		.item-image {
			width: 100%;
			height: 100px;
			margin-right: 0;
		}
		.item-price {
			align-self: stretch;
			text-align: center;
		}
		.total-amount {
			font-size: 1.2rem;
		}
		.stat-card {
			padding: 1rem;
		}
		.stat-icon {
			width: 45px;
			height: 45px;
			font-size: 1.2rem;
		}
	}

	@media (min-width: 1000px) {
		.container-fluid {
			max-width: 1200px;
			margin: 0 auto;
		}
	}

	.loading-spinner {
		border: 3px solid #f1f5f9;
		border-top: 3px solid var(--primary-green);
		border-radius: 50%;
		width: 40px;
		height: 40px;
		animation: spin 1s linear infinite;
		margin: 2rem auto;
	}

	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}

	.order-item-count {
		position: absolute;
		top: -8px;
		right: -8px;
		background: var(--accent-amber);
		color: white;
		width: 22px;
		height: 22px;
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 0.7rem;
		font-weight: 700;
	}

	.grade-badge {
		display: inline-block;
		padding: 0.15rem 0.5rem;
		border-radius: 4px;
		font-size: 0.7rem;
		font-weight: 600;
		background: linear-gradient(135deg, #e0f2fe, #bae6fd);
		color: #0369a1;
	}
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
	<div class="page-header">
		<div class="header-title">
			<h1>
				<i class="fa-solid fa-boxes-stacked"></i>
				Active Orders
			</h1>
			<p>Manage orders that are paid and ready for pickup</p>
		</div>
		<div class="header-actions">
			<span class="pending-count">
				<i class="fa-solid fa-clock"></i>
				{{ $pendingOrders }} Pending
			</span>
			<button class="refresh-btn" onclick="refreshOrders()">
				<i class="fa-solid fa-rotate"></i>
				Refresh
			</button>
		</div>
	</div>

	<div class="stats-cards">
		<div class="stat-card">
			<div class="stat-content">
				<h3>{{ $orders->where('order_status', 'paid')->count() }}</h3>
				<h6>Paid Orders</h6>
			</div>
			<div class="stat-icon icon-paid">
				<i class="fa-solid fa-money-bill-wave"></i>
			</div>
		</div>
		<div class="stat-card">
			<div class="stat-content">
				<h3>{{ $orders->where('order_status', 'ready_for_pickup')->count() }}</h3>
				<h6>Ready for Pickup</h6>
			</div>
			<div class="stat-icon icon-ready">
				<i class="fa-solid fa-truck-fast"></i>
			</div>
		</div>
		<div class="stat-card">
			<div class="stat-content">
				<h3>LKR {{ number_format($orders->sum('total_amount'), 0) }}</h3>
				<h6>Total Revenue</h6>
			</div>
			<div class="stat-icon icon-revenue">
				<i class="fa-solid fa-coins"></i>
			</div>
		</div>
	</div>

	@if($orders->count() > 0)
	<div class="order-cards">
		@foreach($orders as $order)
		@php
			$firstItem = $order->orderItems->first();
			$pickupAddress = $firstItem && $firstItem->product ? $firstItem->product->pickup_address : 'Pickup address not specified';
			$pickupMapLink = $firstItem && $firstItem->product ? $firstItem->product->pickup_map_link : null;
		@endphp
		<div class="order-card">
			<div class="order-top">
				<div class="order-number">
					<i class="fa-solid fa-hashtag"></i>
					{{ $order->order_number }}
				</div>
				<div class="order-time">
					<i class="fa-solid fa-calendar"></i>
					{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y h:i A') }}
				</div>
				<span class="order-status-badge {{ $order->order_status == 'paid' ? 'status-paid' : 'status-ready' }}">
					{{ str_replace('_', ' ', ucfirst($order->order_status)) }}
				</span>
			</div>

			<div class="customer-section">
				<div class="customer-info">
					<div class="customer-avatar">
						<i class="fa-solid fa-user"></i>
					</div>
					<div class="customer-details">
						<h6>{{ $order->buyer->name ?? 'Customer' }}</h6>
						<small>
							<i class="fa-solid fa-phone"></i>
							{{ $order->buyer->primary_mobile ?? 'N/A' }}
						</small>
					</div>
				</div>
			</div>

			<div class="pickup-info">
				<h6><i class="fa-solid fa-location-dot"></i> Pickup Location</h6>
				<p>{{ $pickupAddress }}</p>
				@if($pickupMapLink)
				<a href="{{ $pickupMapLink }}" target="_blank" class="pickup-map">
					<i class="fa-solid fa-map"></i> View on Map
				</a>
				@endif
			</div>

			<div class="order-items-list">
				@foreach($order->orderItems as $item)
				<div class="order-item">
					@if($item->product && $item->product->product_photo)
					<img src="{{ asset('uploads/product_images/' . $item->product->product_photo) }}"
						 class="item-image"
						 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
					@else
					<img src="{{ asset('assets/images/product-placeholder.png') }}"
						 class="item-image">
					@endif
					<div class="item-info">
						<div class="item-name">{{ $item->product_name_snapshot }}</div>
						<div class="item-meta">
							<span>{{ $item->quantity_ordered }} {{ $item->product->unit_of_measure ?? 'unit' }}</span>
							@if($item->product && $item->product->quality_grade)
							<span class="grade-badge">{{ $item->product->quality_grade }}</span>
							@endif
						</div>
					</div>
					<div class="item-price">LKR {{ number_format($item->item_total, 2) }}</div>
				</div>
				@endforeach
			</div>

			<div class="order-bottom">
				<div class="order-total">
					<span class="total-label">Total Amount</span>
					<span class="total-amount">LKR {{ number_format($order->total_amount, 2) }}</span>
				</div>

				@if($order->payment)
				<span class="payment-status {{ $order->payment->payment_status == 'completed' ? 'payment-completed' : 'payment-pending' }}">
					<i class="fa-solid fa-credit-card"></i>
					{{ ucfirst($order->payment->payment_method) }} - {{ ucfirst($order->payment->payment_status) }}
				</span>
				@endif

				<div class="order-actions">
					<button class="action-btn btn-view" onclick="viewOrderDetails({{ $order->id }})">
						<i class="fa-solid fa-eye"></i>
						View Details
					</button>
					@if($order->order_status == 'paid')
					<button class="action-btn btn-ready" onclick="markAsReady({{ $order->id }})">
						<i class="fa-solid fa-check-circle"></i>
						Mark Ready
					</button>
					@endif
				</div>
			</div>
		</div>
		@endforeach
	</div>
	@else
	<div class="empty-state">
		<div class="empty-icon">
			<i class="fa-solid fa-clipboard-check"></i>
		</div>
		<h3>No Active Orders</h3>
		<p>You don't have any paid or ready-for-pickup orders at the moment.</p>
		<a href="{{ route('farmer.orders.history') }}" class="action-btn btn-view" style="width: auto; padding: 0.75rem 1.5rem;">
			<i class="fa-solid fa-history"></i> View Order History
		</a>
	</div>
	@endif
</div>

<div class="modal fade" id="orderDetailsModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header" style="background: linear-gradient(135deg, var(--primary-green), var(--dark-green)); color: white;">
				<h5 class="modal-title">
					<i class="fa-solid fa-file-invoice me-2"></i>
					Order Details
				</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body" id="orderDetailsContent">
				<div class="text-center py-5">
					<div class="loading-spinner"></div>
					<p class="mt-3">Loading order details...</p>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="printOrderBtn">
					<i class="fa-solid fa-print me-2"></i>Print
				</button>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
	function viewOrderDetails(orderId) {
		const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
		modal.show();

		$.ajax({
			url: '{{ route("farmer.orders.view", ":id") }}'.replace(':id', orderId),
			type: 'GET',
			headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
			success: function(response) {
				if (response.success) {
					displayOrderDetails(response.order);
				} else {
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: response.message,
						timer: 3000,
						showConfirmButton: false
					});
				}
			},
			error: function(xhr) {
				let message = 'Failed to load order details.';
				if (xhr.status === 403) {
					message = 'Unauthorized to view this order.';
				} else if (xhr.status === 404) {
					message = 'Order not found.';
				}
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: message
				});
			}
		});
	}

	function displayOrderDetails(order) {
		let itemsHtml = '';
		let totalItems = 0;
		let pickupAddress = 'Pickup address not specified';
		let pickupMapLink = null;

		order.order_items.forEach(item => {
			totalItems += parseFloat(item.quantity_ordered);
			if (item.product && item.product.pickup_address) {
				pickupAddress = item.product.pickup_address;
				pickupMapLink = item.product.pickup_map_link;
			}

			itemsHtml += `
				<tr>
					<td>
						<div class="d-flex align-items-center">
							${item.product && item.product.product_photo ?
								`<img src="{{ asset('uploads/product_images/') }}/${item.product.product_photo}"
									 class="modal-product-img me-3"
									 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">` :
								`<img src="{{ asset('assets/images/product-placeholder.png') }}"
									 class="modal-product-img me-3">`
							}
							<div>
								<strong>${item.product_name_snapshot}</strong>
								<div class="text-muted small">
									${item.product ? (item.product.product_description || 'No description') : 'No description available'}
								</div>
							</div>
						</div>
					</td>
					<td class="text-center align-middle">${parseFloat(item.quantity_ordered).toFixed(2)}</td>
					<td class="text-center align-middle">${item.product ? item.product.unit_of_measure : 'unit'}</td>
					<td class="text-center align-middle">${item.product ? (item.product.quality_grade || 'N/A') : 'N/A'}</td>
					<td class="text-end align-middle">LKR ${parseFloat(item.unit_price_snapshot).toFixed(2)}</td>
					<td class="text-end align-middle">LKR ${parseFloat(item.item_total).toFixed(2)}</td>
				</tr>
			`;
		});

		const pickupHtml = `
			<div class="alert alert-info mb-3">
				<div class="d-flex align-items-center">
					<i class="fa-solid fa-location-dot fa-2x me-3"></i>
					<div>
						<strong>Pickup Location</strong><br>
						${pickupAddress}
						${pickupMapLink ? `<br><a href="${pickupMapLink}" target="_blank" class="btn btn-sm btn-outline-primary mt-2"><i class="fa-solid fa-map me-1"></i> View on Map</a>` : ''}
					</div>
				</div>
			</div>
		`;

		const paymentHtml = order.payment ? `
			<div class="row mb-3">
				<div class="col-12">
					<h6 class="border-bottom pb-2 mb-3">
						<i class="fa-solid fa-credit-card me-2"></i>Payment Details
					</h6>
					<div class="row">
						<div class="col-md-6">
							<div class="mb-2"><strong>Payment Method:</strong> ${order.payment.payment_method}</div>
							<div class="mb-2"><strong>Amount Paid:</strong> LKR ${parseFloat(order.payment.amount).toFixed(2)}</div>
						</div>
						<div class="col-md-6">
							<div class="mb-2">
								<strong>Status:</strong>
								<span class="badge ${order.payment.payment_status == 'completed' ? 'bg-success' : 'bg-warning'} ms-2">
									${order.payment.payment_status.toUpperCase()}
								</span>
							</div>
							<div class="mb-2"><strong>Payment Date:</strong> ${new Date(order.payment.payment_date).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'})}</div>
						</div>
					</div>
				</div>
			</div>
		` : '';

		const html = `
			<div class="order-details-content">
				<div class="row">
					<div class="col-md-6 mb-3">
						<h6 class="border-bottom pb-2 mb-3">
							<i class="fa-solid fa-user me-2"></i>Customer Details
						</h6>
						<table class="table table-borderless">
							<tr><th width="35%">Name:</th><td>${order.buyer ? order.buyer.name : 'N/A'}</td></tr>
							<tr><th>Mobile:</th><td>${order.buyer ? order.buyer.primary_mobile : 'N/A'}</td></tr>
							<tr><th>Address:</th><td>${order.buyer ? (order.buyer.residential_address || 'N/A') : 'N/A'}</td></tr>
						</table>
					</div>
					<div class="col-md-6 mb-3">
						<h6 class="border-bottom pb-2 mb-3">
							<i class="fa-solid fa-receipt me-2"></i>Order Information
						</h6>
						<table class="table table-borderless">
							<tr><th width="35%">Order Number:</th><td><strong>${order.order_number}</strong></td></tr>
							<tr><th>Status:</th><td><span class="badge bg-success">${order.order_status.replace('_', ' ').toUpperCase()}</span></td></tr>
							<tr><th>Order Date:</th><td>${new Date(order.created_at).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'})}</td></tr>
						</table>
					</div>
				</div>

				${pickupHtml}

				${paymentHtml}

				<h6 class="border-bottom pb-2 mb-3">
					<i class="fa-solid fa-basket-shopping me-2"></i>Order Items (${order.order_items.length})
				</h6>

				<div class="table-responsive mb-3">
					<table class="table table-hover">
						<thead class="table-light">
							<tr>
								<th>Product</th>
								<th class="text-center">Quantity</th>
								<th class="text-center">Unit</th>
								<th class="text-center">Grade</th>
								<th class="text-end">Unit Price</th>
								<th class="text-end">Total</th>
							</tr>
						</thead>
						<tbody>${itemsHtml}</tbody>
					</table>
				</div>

				<div class="row">
					<div class="col-md-8">
						<div class="alert alert-light">
							<div class="d-flex align-items-center">
								<i class="fa-solid fa-info-circle me-3"></i>
								<div>
									<strong>Pickup Instructions</strong><br>
									${pickupAddress}
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="bg-light p-3 rounded border">
							<h6 class="border-bottom pb-2 mb-3">Order Summary</h6>
							<div class="d-flex justify-content-between mb-2">
								<span>Items Total:</span>
								<strong>LKR ${parseFloat(order.items_total || 0).toFixed(2)}</strong>
							</div>
							<div class="d-flex justify-content-between mb-2">
								<span>Delivery Fee:</span>
								<strong>LKR ${parseFloat(order.delivery_fee || 0).toFixed(2)}</strong>
							</div>
							<hr>
							<div class="d-flex justify-content-between">
								<span class="h5">Grand Total:</span>
								<span class="h4 text-primary">LKR ${parseFloat(order.total_amount).toFixed(2)}</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		`;

		$('#orderDetailsContent').html(html);

		$('#printOrderBtn').off('click').on('click', function() {
			const printContent = $('#orderDetailsContent').html();
			const originalContent = document.body.innerHTML;
			document.body.innerHTML = `
				<div class="container mt-4">
					<h2 class="text-center mb-4">Order Invoice - ${order.order_number}</h2>
					${printContent}
				</div>
			`;
			window.print();
			document.body.innerHTML = originalContent;
			location.reload();
		});
	}

	function markAsReady(orderId) {
		Swal.fire({
			title: 'Mark as Ready for Pickup?',
			text: 'This will notify the buyer that their order is ready for collection.',
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#10B981',
			cancelButtonColor: '#6b7280',
			confirmButtonText: 'Yes, mark ready',
			cancelButtonText: 'Cancel'
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.fire({
					title: 'Processing...',
					text: 'Please wait while we update the order status.',
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();
					}
				});

				$.ajax({
					url: '{{ url("farmer/orders/mark-ready") }}/' + orderId,
					type: 'POST',
					headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
					data: {_method: 'PUT'},
					success: function(response) {
						Swal.close();
						if (response.success) {
							Swal.fire({
								icon: 'success',
								title: 'Success!',
								text: response.message,
								timer: 2000,
								showConfirmButton: false
							}).then(() => location.reload());
						} else {
							Swal.fire({
								icon: 'error',
								title: 'Error',
								text: response.message
							});
						}
					},
					error: function(xhr) {
						Swal.close();
						let message = 'Failed to update order status.';
						if (xhr.status === 403) {
							message = 'Unauthorized to update this order.';
						} else if (xhr.status === 404) {
							message = 'Order not found.';
						}
						Swal.fire({
							icon: 'error',
							title: 'Error',
							text: message
						});
					}
				});
			}
		});
	}

	function refreshOrders() {
		Swal.fire({
			title: 'Refreshing Orders',
			text: 'Please wait...',
			allowOutsideClick: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});
		setTimeout(() => location.reload(), 800);
	}

	let refreshInterval;
	function startAutoRefresh() {
		refreshInterval = setInterval(() => {
			if (document.visibilityState === 'visible') {
				$.ajax({
					url: '{{ route("farmer.orders.active") }}',
					type: 'GET',
					success: function(data) {
						const newCount = $(data).find('.order-card').length;
						const currentCount = $('.order-card').length;
						if (newCount !== currentCount) {
							Swal.fire({
								icon: 'info',
								title: 'New Orders Available',
								text: 'Refreshing page...',
								timer: 1500,
								showConfirmButton: false
							}).then(() => location.reload());
						}
					}
				});
			}
		}, 30000);
	}

	document.addEventListener('visibilitychange', function() {
		if (document.visibilityState === 'hidden') {
			clearInterval(refreshInterval);
		} else {
			startAutoRefresh();
		}
	});

	$(function() {
		startAutoRefresh();
	});
</script>
@endsection
