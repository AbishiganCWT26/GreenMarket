@extends('farmer.layouts.farmer_master')

@section('title', 'Farmer Dashboard')
@section('page-title', 'Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/farmer/dashboard.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
<div class="dashboard-container">
	<div class="welcome-card">
		<div class="welcome-content">
			<h2 class="welcome-text">
				Welcome Back,
				<span class="welcome-name">{{ Auth::user()->farmer->name ?? Auth::user()->username }}</span>
			</h2>
			<p class="welcome-subtext">Here's what's happening with your farm today</p>
			<div class="welcome-date">
				<i class="fas fa-calendar-alt"></i>
				{{ now()->format('l, F j, Y') }}
			</div>
		</div>
		<div class="welcome-illustration">
			<div class="farm-icon">
				<i class="fas fa-tractor"></i>
			</div>
		</div>
	</div>

	<div class="stats-grid">
		<div class="stat-card green">
			<div class="stat-icon">
				<i class="fas fa-seedling"></i>
			</div>
			<div class="stat-content">
				<h3 class="stat-value">{{ $productCount ?? 0 }}</h3>
				<p class="stat-label">Active Products</p>
			</div>
			<a href="{{ route('farmer.products.my-products') }}" class="stat-link">
				View All <i class="fas fa-arrow-right"></i>
			</a>
		</div>

		<div class="stat-card blue">
			<div class="stat-icon">
				<i class="fas fa-shopping-cart"></i>
			</div>
			<div class="stat-content">
				<h3 class="stat-value">{{ $pendingOrders ?? 0 }}</h3>
				<p class="stat-label">Pending Orders</p>
			</div>
			<a href="{{ route('farmer.orders.active') }}" class="stat-link">
				Manage <i class="fas fa-arrow-right"></i>
			</a>
		</div>

		<div class="stat-card amber">
			<div class="stat-icon">
				<i class="fas fa-truck"></i>
			</div>
			<div class="stat-content">
				<h3 class="stat-value">{{ $pendingPickups ?? 0 }}</h3>
				<p class="stat-label">Ready for Pickup</p>
			</div>
			<a href="{{ route('farmer.orders.active') }}" class="stat-link">
				Check <i class="fas fa-arrow-right"></i>
			</a>
		</div>

		<div class="stat-card purple">
			<div class="stat-icon">
				<i class="fas fa-flag"></i>
			</div>
			<div class="stat-content">
				<h3 class="stat-value">{{ $openComplaints ?? 0 }}</h3>
				<p class="stat-label">Open Complaints</p>
			</div>
			<a href="{{ route('farmer.complaints.list') }}" class="stat-link">
				View <i class="fas fa-arrow-right"></i>
			</a>
		</div>
	</div>

	@if(Auth::user()->farmer && Auth::user()->farmer->lead_farmer_id)
	<div class="lead-farmer-card">
		<div class="lead-header">
			<div class="lead-icon">
				<i class="fas fa-users"></i>
			</div>
			<div class="lead-info">
				<h3>Your Lead Farmer</h3>
				<p class="lead-name">{{ Auth::user()->farmer->leadFarmer->name ?? 'Not Assigned' }}</p>
				<p class="lead-group">Group: {{ Auth::user()->farmer->leadFarmer->group_name ?? 'No Group' }}</p>
			</div>
		</div>
		<div class="lead-contact">
			<div class="contact-item">
				<i class="fas fa-phone"></i>
				<span>{{ Auth::user()->farmer->leadFarmer->primary_mobile ?? 'N/A' }}</span>
			</div>
			<div class="contact-item">
				<i class="fas fa-map-marker-alt"></i>
				<span>{{ Auth::user()->farmer->leadFarmer->grama_niladhari_division ?? 'N/A' }}</span>
			</div>
		</div>
	</div>
	@endif

	<div class="content-grid">
		<div class="recent-orders">
			<div class="section-header">
				<h3><i class="fas fa-history"></i> Recent Orders</h3>
				<a href="{{ route('farmer.orders.history') }}" class="view-all">View All</a>
			</div>
			@if(isset($recentOrders) && count($recentOrders) > 0)
			<div class="orders-list">
				@foreach($recentOrders as $order)
				<div class="order-item" data-order-id="{{ $order->id }}" onclick="viewOrderDetails({{ $order->id }})">
					<div class="order-left">
						<div class="order-id">Order #{{ $order->order_number }}</div>
						<div class="order-date">{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</div>
					</div>
					<div class="order-right">
						<span class="order-status {{ str_replace('_', '-', $order->order_status) }}">{{ ucfirst(str_replace('_', ' ', $order->order_status)) }}</span>
						<div class="order-amount">LKR {{ number_format($order->total_amount, 2) }}</div>
					</div>
				</div>
				@endforeach
			</div>
			@else
			<div class="empty-state">
				<i class="fas fa-shopping-basket"></i>
				<p>No orders yet</p>
			</div>
			@endif
		</div>

		<div class="low-stock">
			<div class="section-header">
				<h3><i class="fas fa-exclamation-triangle"></i> Low Stock Alert</h3>
				<a href="{{ route('farmer.products.my-products') }}" class="view-all">View All</a>
			</div>
			@if(isset($lowStockProducts) && count($lowStockProducts) > 0)
			<div class="low-stock-list">
				@foreach($lowStockProducts as $stockItem)
				@php
					$product = $stockItem['product'];
					$availableQuantity = $stockItem['available_quantity'];
					$totalOrdered = $stockItem['total_ordered'];
				@endphp
				<div class="stock-item">
					<div class="stock-left">
						<div class="stock-name">{{ $product->product_name }}</div>
						<div class="stock-quantity">
							<span>Available: {{ number_format($availableQuantity, 2) }} {{ $product->unit_of_measure }}</span>
						</div>
						<div class="stock-details">
							Stock: {{ number_format($product->quantity, 2) }} | Ordered: {{ number_format($totalOrdered, 2) }}
						</div>
					</div>
					<div class="stock-right">
						<span class="stock-alert">Low Stock</span>
					</div>
				</div>
				@endforeach
			</div>
			@else
			<div class="empty-state">
				<i class="fas fa-check-circle"></i>
				<p>All products have good stock</p>
			</div>
			@endif
		</div>
	</div>

	<div class="quick-actions">
		<h3 class="actions-title">Quick Actions</h3>
		<div class="actions-grid">
			<a href="{{ route('farmer.products.my-products') }}" class="action-card">
				<div class="action-icon">
					<i class="fas fa-boxes"></i>
				</div>
				<div class="action-content">
					<h4>My Products</h4>
					<p>View and manage your products</p>
				</div>
			</a>

			<a href="{{ route('farmer.complaints.create') }}" class="action-card">
				<div class="action-icon">
					<i class="fas fa-flag"></i>
				</div>
				<div class="action-content">
					<h4>File Complaint</h4>
					<p>Report issues to facilitator</p>
				</div>
			</a>

			<a href="{{ route('farmer.profile.profile') }}" class="action-card">
				<div class="action-icon">
					<i class="fas fa-user"></i>
				</div>
				<div class="action-content">
					<h4>My Profile</h4>
					<p>Update your profile details</p>
				</div>
			</a>

			<a href="{{ route('farmer.notifications') }}" class="action-card">
				<div class="action-icon">
					<i class="fas fa-bell"></i>
				</div>
				<div class="action-content">
					<h4>Notifications</h4>
					<p>Check your notifications</p>
				</div>
			</a>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
	@if(session('success'))
	Swal.fire({
		icon: 'success',
		title: 'Success!',
		text: '{{ session("success") }}',
		timer: 3000,
		showConfirmButton: false,
		position: 'top-end',
		toast: true,
		background: '#10B981',
		color: 'white'
	});
	@endif

	@if(session('error'))
	Swal.fire({
		icon: 'error',
		title: 'Error!',
		text: '{{ session("error") }}',
		timer: 3000,
		showConfirmButton: false,
		position: 'top-end',
		toast: true,
		background: '#EF4444',
		color: 'white'
	});
	@endif

	@if(session('warning'))
	Swal.fire({
		icon: 'warning',
		title: 'Warning!',
		text: '{{ session("warning") }}',
		timer: 3000,
		showConfirmButton: false,
		position: 'top-end',
		toast: true,
		background: '#F59E0B',
		color: 'white'
	});
	@endif

	const statCards = document.querySelectorAll('.stat-card');
	statCards.forEach(card => {
		card.addEventListener('mouseenter', function() {
			this.style.transform = 'translateY(-8px)';
			this.style.boxShadow = '0 12px 25px rgba(0,0,0,0.15)';
		});

		card.addEventListener('mouseleave', function() {
			this.style.transform = 'translateY(0)';
			this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
		});
	});

	const actionCards = document.querySelectorAll('.action-card');
	actionCards.forEach(card => {
		card.addEventListener('mouseenter', function() {
			this.style.transform = 'translateY(-5px)';
			this.style.boxShadow = '0 8px 20px rgba(0,0,0,0.12)';
		});

		card.addEventListener('mouseleave', function() {
			this.style.transform = 'translateY(0)';
			this.style.boxShadow = 'none';
		});
	});

	const orderItems = document.querySelectorAll('.order-item');
	orderItems.forEach(item => {
		item.addEventListener('mouseenter', function() {
			this.style.transform = 'translateX(5px)';
			this.style.borderColor = '#10B981';
			this.style.background = '#F3F4F6';
		});

		item.addEventListener('mouseleave', function() {
			this.style.transform = 'translateX(0)';
			this.style.borderColor = '#e5e7eb';
			this.style.background = '#F9FAFB';
		});
	});

	if (typeof gsap !== 'undefined') {
		gsap.from('.stat-card', {
			duration: 0.8,
			y: 30,
			opacity: 0,
			stagger: 0.1,
			ease: 'power2.out',
			delay: 0.3
		});

		gsap.from('.welcome-card', {
			duration: 0.8,
			y: -20,
			opacity: 0,
			ease: 'power2.out'
		});
	}
});

async function viewOrderDetails(orderId) {
	try {
		Swal.fire({
			title: 'Loading...',
			allowOutsideClick: false,
			showConfirmButton: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});

		const response = await fetch(`/farmer/orders/view/${orderId}`, {
			method: 'GET',
			headers: {
				'X-CSRF-TOKEN': '{{ csrf_token() }}',
				'Accept': 'application/json'
			}
		});

		const data = await response.json();

		Swal.close();

		if (data.success && data.order) {
			const order = data.order;
			const buyer = order.buyer;
			const orderItems = order.order_items;

			let htmlContent = `
				<div style="text-align: left; max-height: 500px; overflow-y: auto;">
					<div style="background: #f8fafc; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
						<h4 style="margin: 0 0 15px 0; font-size: 18px; color: #0f1724;">Order Details</h4>
						<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
							<div>
								<span style="font-size: 14px; color: #6b7280; display: block; margin-bottom: 5px;">Order Number:</span>
								<p style="margin: 0; font-weight: 600; color: #0f1724; font-size: 16px;">
									${order.order_number}
								</p>
							</div>
							<div>
								<span style="font-size: 14px; color: #6b7280; display: block; margin-bottom: 5px;">Status:</span>
								<p style="margin: 0; font-weight: 600; color: #0f1724; font-size: 16px;">
									${order.order_status.replace('_', ' ').toUpperCase()}
								</p>
							</div>
							<div>
								<span style="font-size: 14px; color: #6b7280; display: block; margin-bottom: 5px;">Total Amount:</span>
								<p style="margin: 0; font-weight: 600; color: #0f1724; font-size: 16px;">
									LKR ${parseFloat(order.total_amount).toFixed(2)}
								</p>
							</div>
							<div>
								<span style="font-size: 14px; color: #6b7280; display: block; margin-bottom: 5px;">Order Date:</span>
								<p style="margin: 0; font-weight: 600; color: #0f1724; font-size: 16px;">
									${new Date(order.created_at).toLocaleDateString('en-GB')}
								</p>
							</div>
						</div>
					</div>`;

			if (buyer) {
				htmlContent += `
					<div style="background: #f0f9ff; padding: 20px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #3b82f6;">
						<h4 style="margin: 0 0 15px 0; font-size: 18px; color: #0f1724;">Buyer Information</h4>
						<div style="display: flex; flex-direction: column; gap: 12px;">
							<div style="display: flex; align-items: center; gap: 10px;">
								<i class="fas fa-user" style="color: #3b82f6; width: 16px;"></i>
								<span><strong>Name:</strong> ${buyer.name}</span>
							</div>
							<div style="display: flex; align-items: center; gap: 10px;">
								<i class="fas fa-phone" style="color: #3b82f6; width: 16px;"></i>
								<span><strong>Phone:</strong> ${buyer.primary_mobile || 'N/A'}</span>
							</div>
							${buyer.business_name ? `
							<div style="display: flex; align-items: center; gap: 10px;">
								<i class="fas fa-building" style="color: #3b82f6; width: 16px;"></i>
								<span><strong>Business:</strong> ${buyer.business_name}</span>
							</div>` : ''}
							<div style="display: flex; align-items: center; gap: 10px;">
								<i class="fas fa-map-marker-alt" style="color: #3b82f6; width: 16px;"></i>
								<span><strong>Address:</strong> ${buyer.residential_address || 'N/A'}</span>
							</div>
						</div>
					</div>`;
			}

			if (orderItems && orderItems.length > 0) {
				htmlContent += `
					<div style="background: #fef3c7; padding: 20px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #f59e0b;">
						<h4 style="margin: 0 0 15px 0; font-size: 18px; color: #0f1724;">Products to Deliver</h4>
						<div style="display: flex; flex-direction: column; gap: 15px;">
				`;

				orderItems.forEach(item => {
					const product = item.product;
					htmlContent += `
						<div style="background: white; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
							<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
								<h5 style="margin: 0; font-size: 16px; color: #0f1724;">${item.product_name_snapshot}</h5>
								<span style="font-weight: 600; color: #10B981; font-size: 16px;">
									${parseFloat(item.quantity_ordered).toFixed(2)} ${product?.unit_of_measure || ''}
								</span>
							</div>
							<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 14px;">
								<div>
									<span style="color: #6b7280;">Unit Price:</span>
									<p style="margin: 5px 0 0 0; font-weight: 600; color: #0f1724;">
										LKR ${parseFloat(item.unit_price_snapshot).toFixed(2)}
									</p>
								</div>
								<div>
									<span style="color: #6b7280;">Total:</span>
									<p style="margin: 5px 0 0 0; font-weight: 600; color: #0f1724;">
										LKR ${parseFloat(item.item_total).toFixed(2)}
									</p>
								</div>
							</div>
						</div>
					`;
				});

				htmlContent += `
						</div>
					</div>
				`;
			}

			htmlContent += `</div>`;

			Swal.fire({
				title: 'Order #' + order.order_number,
				html: htmlContent,
				width: 700,
				padding: '25px',
				showCloseButton: true,
				confirmButtonText: 'Close',
				confirmButtonColor: '#10B981',
				customClass: {
					popup: 'order-details-modal'
				}
			});
		} else {
			Swal.fire({
				icon: 'error',
				title: 'Error',
				text: data.message || 'Failed to load order details.',
				confirmButtonColor: '#10B981',
			});
		}
	} catch (error) {
		console.error('Error viewing order:', error);
		Swal.close();
		Swal.fire({
			icon: 'error',
			title: 'Error',
			text: 'Something went wrong. Please try again.',
			confirmButtonColor: '#10B981',
		});
	}
}
</script>
@endsection
