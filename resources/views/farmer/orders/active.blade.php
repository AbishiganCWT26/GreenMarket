@extends('farmer.layouts.farmer_master')

@section('title', 'Active Orders')
@section('page-title', 'Active Orders')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/farmer/order-active.css') }}">
@endsection

@section('content')
<div class="order-active-container">
	<div class="page-header-compact">
		<div class="header-title-compact">
			<h1>
				<i class="fa-solid fa-boxes-stacked"></i>
				Active Orders
			</h1>
			<p>Manage paid & ready for pickup orders</p>
		</div>
		<div class="header-actions-compact">
			<div class="search-container">
				<input type="text" id="searchOrders" placeholder="Search orders, customers..." class="search-input">
				<button type="button" id="searchBtn"><i class="fa-solid fa-search"></i></button>
			</div>
			<span class="pending-badge">
				<i class="fa-solid fa-clock"></i>
				{{ $pendingOrders }} Pending
			</span>
			<button class="refresh-btn-compact" onclick="refreshOrders()">
				<i class="fa-solid fa-rotate"></i>
				Refresh
			</button>
			<div class="view-toggle">
				<button class="view-btn active" id="cardViewBtn" title="Card View">
					<i class="fa-solid fa-grip"></i>
				</button>
				<button class="view-btn" id="tableViewBtn" title="Table View">
					<i class="fa-solid fa-table"></i>
				</button>
			</div>
		</div>
	</div>

	<div class="stats-grid-compact">
		<div class="stat-item-compact">
			<div class="stat-content-compact">
				<h3>{{ $orders->where('order_status', 'paid')->count() }}</h3>
				<span>Paid Orders</span>
			</div>
			<div class="stat-icon-compact icon-paid-compact">
				<i class="fa-solid fa-money-bill-wave"></i>
			</div>
		</div>
		<div class="stat-item-compact">
			<div class="stat-content-compact">
				<h3>{{ $orders->where('order_status', 'ready_for_pickup')->count() }}</h3>
				<span>Ready for Pickup</span>
			</div>
			<div class="stat-icon-compact icon-ready-compact">
				<i class="fa-solid fa-truck-fast"></i>
			</div>
		</div>
		<div class="stat-item-compact">
			<div class="stat-content-compact">
				<h3>LKR {{ number_format($orders->sum('total_amount'), 0) }}</h3>
				<span>Total Revenue</span>
			</div>
			<div class="stat-icon-compact icon-revenue-compact">
				<i class="fa-solid fa-coins"></i>
			</div>
		</div>
	</div>

	<div id="cardViewContainer">
		@if($orders->count() > 0)
		<div class="orders-grid">
			@foreach($orders as $order)
			@php
				$firstItem = $order->orderItems->first();
				$pickupAddress = $firstItem && $firstItem->product ? $firstItem->product->pickup_address : 'Pickup address not specified';
				$pickupMapLink = $firstItem && $firstItem->product ? $firstItem->product->pickup_map_link : null;
				$profilePhoto = $order->buyer && $order->buyer->profile_photo ? 
					asset('uploads/profile_pictures/' . $order->buyer->profile_photo) : 
					asset('uploads/profile_pictures/buyer.svg');
			@endphp
			<div class="order-card-compact" data-order-id="{{ $order->id }}">
				<div class="card-header-compact">
					<span class="order-number-compact">
						<i class="fa-solid fa-hashtag"></i> {{ $order->order_number }}
					</span>
					<span class="status-badge-compact {{ $order->order_status == 'paid' ? 'status-paid-compact' : 'status-ready-compact' }}">
						{{ str_replace('_', ' ', ucfirst($order->order_status)) }}
					</span>
				</div>
				<div class="card-body-compact">
					<div class="customer-info-compact">
						<img src="{{ $profilePhoto }}" class="customer-avatar-compact" onerror="this.src='{{ asset('uploads/profile_pictures/buyer.svg') }}'">
						<div class="customer-details-compact">
							<h6>{{ $order->buyer->name ?? 'Customer' }}</h6>
							<small>
								<i class="fa-solid fa-phone"></i>
								{{ $order->buyer->primary_mobile ?? 'N/A' }}
							</small>
						</div>
					</div>
					<div class="pickup-chip">
						<i class="fa-solid fa-location-dot"></i>
						<p>{{ Str::limit($pickupAddress, 40) }}</p>
					</div>
					<div class="items-preview">
						@foreach($order->orderItems->take(2) as $item)
						<div class="item-preview">
							@if($item->product && $item->product->product_photo)
							<img src="{{ asset('uploads/product_images/' . $item->product->product_photo) }}" onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
							@else
							<img src="{{ asset('assets/images/product-placeholder.png') }}">
							@endif
							<div class="item-preview-info">
								<span class="item-preview-name">{{ Str::limit($item->product_name_snapshot, 15) }}</span>
								<span class="item-preview-price">LKR {{ number_format($item->item_total, 0) }}</span>
							</div>
						</div>
						@endforeach
						@if($order->orderItems->count() > 2)
						<div class="item-preview">
							<i class="fa-solid fa-ellipsis"></i>
							<span>+{{ $order->orderItems->count() - 2 }} more items</span>
						</div>
						@endif
					</div>
				</div>
				<div class="card-footer-compact">
					<div class="order-total-compact">
						Total <strong>LKR {{ number_format($order->total_amount, 0) }}</strong>
					</div>
					<div class="action-btns-compact">
						<button class="action-btn-compact view-btn-compact" onclick="viewOrderDetails({{ $order->id }})" title="View Details">
							<i class="fa-solid fa-eye"></i>
						</button>
						@if($order->order_status == 'paid')
						<button class="action-btn-compact ready-btn-compact" onclick="markAsReady({{ $order->id }})" title="Mark Ready">
							<i class="fa-solid fa-check"></i>
						</button>
						@endif
					</div>
				</div>
			</div>
			@endforeach
		</div>
		@else
		<div class="empty-state-compact">
			<div class="empty-icon-compact">
				<i class="fa-solid fa-clipboard-check"></i>
			</div>
			<h3>No Active Orders</h3>
			<p>You don't have any paid or ready-for-pickup orders at the moment.</p>
			<a href="{{ route('farmer.orders.history') }}" class="action-btn-compact view-btn-compact" style="width: auto; padding: 0.4rem 1rem;">
				<i class="fa-solid fa-history"></i> View Order History
			</a>
		</div>
		@endif
	</div>

	<div id="tableViewContainer" style="display: none;">
		@if($orders->count() > 0)
		<div class="orders-table-container">
			<table class="orders-table">
				<thead>
					<tr>
						<th>Order #</th>
						<th>Customer</th>
						<th>Contact</th>
						<th>Items</th>
						<th>Total</th>
						<th>Status</th>
						<th>Date</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					@foreach($orders as $order)
					@php
						$profilePhoto = $order->buyer && $order->buyer->profile_photo ? 
							asset('uploads/profile_pictures/' . $order->buyer->profile_photo) : 
							asset('uploads/profile_pictures/buyer.svg');
					@endphp
					<tr>
						<td><strong>{{ $order->order_number }}</strong></td>
						<td>
							<div style="display: flex; align-items: center; gap: 0.3rem;">
								<img src="{{ $profilePhoto }}" class="table-avatar" onerror="this.src='{{ asset('uploads/profile_pictures/buyer.svg') }}'">
								{{ $order->buyer->name ?? 'Customer' }}
							</div>
						</td>
						<td>{{ $order->buyer->primary_mobile ?? 'N/A' }}</td>
						<td>{{ $order->orderItems->count() }} items</td>
						<td><strong>LKR {{ number_format($order->total_amount, 0) }}</strong></td>
						<td>
							<span class="table-status {{ $order->order_status == 'paid' ? 'status-paid-compact' : 'status-ready-compact' }}">
								{{ str_replace('_', ' ', ucfirst($order->order_status)) }}
							</span>
						</td>
						<td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M, H:i') }}</td>
						<td>
							<div class="table-actions">
								<button class="table-action-btn view-btn-compact" onclick="viewOrderDetails({{ $order->id }})" title="View Details">
									<i class="fa-solid fa-eye"></i>
								</button>
								@if($order->order_status == 'paid')
								<button class="table-action-btn ready-btn-compact" onclick="markAsReady({{ $order->id }})" title="Mark Ready">
									<i class="fa-solid fa-check"></i>
								</button>
								@endif
							</div>
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		@endif
	</div>

	<div class="pagination-compact">
		{{ $orders->links('vendor.pagination.custom') }}
	</div>
</div>

<div class="modal fade" id="orderDetailsModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content modal-content-compact">
			<div class="modal-header modal-header-compact">
				<h5 class="modal-title">
					<i class="fa-solid fa-file-invoice me-2"></i>
					Order Details
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body modal-body-compact" id="orderDetailsContent">
				<div class="text-center py-4">
					<div class="loading-spinner"></div>
					<p class="mt-2 small">Loading order details...</p>
				</div>
			</div>
			<div class="modal-footer modal-footer-compact">
				<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary btn-sm" id="printOrderBtn">
					<i class="fa-solid fa-print me-1"></i>Print
				</button>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')

<script>
	let currentView = 'card';
	let searchTimeout;

	function getItemsPerPage() {
		const w = window.innerWidth;
		if (currentView === 'card') {
			if (w >= 2560) return 18;
			if (w >= 1500) return 12;
			if (w >= 1200) return 8;
			if (w >= 992) return 6;
			if (w >= 768) return 4;
			return 3;
		} else {
			if (w >= 2560) return 15;
			if (w >= 1500) return 15;
			if (w >= 1200) return 10;
			if (w >= 992) return 10;
			if (w >= 768) return 10;
			return 5;
		}
	}

	$('#cardViewBtn').click(function() {
		currentView = 'card';
		$(this).addClass('active');
		$('#tableViewBtn').removeClass('active');
		$('#cardViewContainer').show();
		$('#tableViewContainer').hide();
		localStorage.setItem('orderViewPreference', 'card');
	});

	$('#tableViewBtn').click(function() {
		currentView = 'table';
		$(this).addClass('active');
		$('#cardViewBtn').removeClass('active');
		$('#cardViewContainer').hide();
		$('#tableViewContainer').show();
		localStorage.setItem('orderViewPreference', 'table');
	});

	const savedView = localStorage.getItem('orderViewPreference');
	if (savedView === 'table') {
		$('#tableViewBtn').click();
	}

	function performSearch() {
		const searchTerm = $('#searchOrders').val().toLowerCase().trim();
		
		if (searchTerm === '') {
			$('.order-card-compact').show();
			$('.orders-table tbody tr').show();
			return;
		}

		if (currentView === 'card') {
			$('.order-card-compact').each(function() {
				const card = $(this);
				const orderNumber = card.find('.order-number-compact').text().toLowerCase();
				const customerName = card.find('.customer-details-compact h6').text().toLowerCase();
				const phone = card.find('.customer-details-compact small').text().toLowerCase();
				const items = card.find('.item-preview-name').map(function() {
					return $(this).text().toLowerCase();
				}).get().join(' ');
				
				const matches = orderNumber.includes(searchTerm) || 
							   customerName.includes(searchTerm) || 
							   phone.includes(searchTerm) || 
							   items.includes(searchTerm);
				
				card.toggle(matches);
			});
		} else {
			$('.orders-table tbody tr').each(function() {
				const row = $(this);
				const text = row.text().toLowerCase();
				row.toggle(text.includes(searchTerm));
			});
		}
	}

	$('#searchOrders').on('input', function() {
		clearTimeout(searchTimeout);
		searchTimeout = setTimeout(performSearch, 300);
	});

	$('#searchBtn').click(performSearch);

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
						@if(file_exists(public_path('assets/icons/Gif/error2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
						title: 'Error',
						text: response.message,
						timer: 2000,
						showConfirmButton: false,
						background: '#ffffff',
						toast: true,
						position: 'top-end'
					});
				}
			},
			error: function(xhr) {
				let message = 'Failed to load order details.';
				if (xhr.status === 403) message = 'Unauthorized to view this order.';
				else if (xhr.status === 404) message = 'Order not found.';
				
				Swal.fire({
					@if(file_exists(public_path('assets/icons/Gif/error2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
					title: 'Error',
					text: message,
					timer: 2000,
					showConfirmButton: false,
					background: '#ffffff',
					toast: true,
					position: 'top-end'
				});
			}
		});
	}

	function displayOrderDetails(order) {
		let itemsHtml = '';
		let pickupAddress = 'Pickup address not specified';
		let pickupMapLink = null;

		order.order_items.forEach(item => {
			if (item.product && item.product.pickup_address) {
				pickupAddress = item.product.pickup_address;
				pickupMapLink = item.product.pickup_map_link;
			}

			itemsHtml += `
				<tr>
					<td>
						<div style="display: flex; align-items: center; gap: 0.5rem;">
							${item.product && item.product.product_photo ?
								`<img src="{{ asset('uploads/product_images/') }}/${item.product.product_photo}"
									 class="modal-product-img-compact"
									 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">` :
								`<img src="{{ asset('assets/images/product-placeholder.png') }}"
									 class="modal-product-img-compact">`
							}
							<div>
								<strong>${item.product_name_snapshot}</strong>
							</div>
						</div>
					</td>
					<td>${parseFloat(item.quantity_ordered).toFixed(2)}</td>
					<td>${item.product ? (item.product.unit_of_measure || 'unit') : 'unit'}</td>
					<td class="text-end">LKR ${parseFloat(item.item_total).toFixed(2)}</td>
				</tr>
			`;
		});

		const profilePhoto = order.buyer && order.buyer.profile_photo ? 
			'{{ asset("uploads/profile_pictures") }}/' + order.buyer.profile_photo : 
			'{{ asset("uploads/profile_pictures/buyer.svg") }}';

		const html = `
			<div class="order-details-content">
				<div class="row mb-3">
					<div class="col-md-6">
						<div class="detail-row-compact">
							<span class="detail-label-compact">Customer:</span>
							<span class="detail-value-compact">
								<img src="${profilePhoto}" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 0.3rem;">
								${order.buyer ? order.buyer.name : 'N/A'}
							</span>
						</div>
						<div class="detail-row-compact">
							<span class="detail-label-compact">Mobile:</span>
							<span class="detail-value-compact">${order.buyer ? order.buyer.primary_mobile : 'N/A'}</span>
						</div>
						<div class="detail-row-compact">
							<span class="detail-label-compact">Order #:</span>
							<span class="detail-value-compact"><strong>${order.order_number}</strong></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="detail-row-compact">
							<span class="detail-label-compact">Status:</span>
							<span class="detail-value-compact">
								<span class="table-status ${order.order_status == 'paid' ? 'status-paid-compact' : 'status-ready-compact'}">
									${order.order_status.replace('_', ' ').toUpperCase()}
								</span>
							</span>
						</div>
						<div class="detail-row-compact">
							<span class="detail-label-compact">Date:</span>
							<span class="detail-value-compact">${new Date(order.created_at).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'})}</span>
						</div>
					</div>
				</div>

				<div class="alert1 alert-info py-2 mb-3" style="background: #7fd8eaff; font-size: 0.85rem; border-radius: 8px; border: none; border-left: 4px solid #0dcaf0; box-shadow: 0 2px 6px rgba(0,0,0,0.05); animation: none;">
					<div style="display: flex; align-items: flex-start; gap: 0.75rem;">
						<i class="fa-solid fa-location-dot" style="margin-top: 0.25rem; font-size: 1.1em; opacity: 0.9;"></i>
						<div style="line-height: 1.5;">
							<strong style="font-weight: 600;">Pickup:</strong> ${pickupAddress}
							${pickupMapLink ? `<br><a href="${pickupMapLink}" target="_blank" class="small" style="text-decoration: none; display: inline-block; margin-top: 0.3rem; font-weight: 500;">View on Map <i class="fa-solid fa-arrow-right" style="font-size: 0.85em; margin-left: 0.2rem;"></i></a>` : ''}
						</div>
					</div>
				</div>

				${order.payment ? `
				<div class="row mb-3">
					<div class="col-12">
						<h6 class="border-bottom pb-1 mb-2" style="font-size: 0.75rem;">
							<i class="fa-solid fa-credit-card me-1"></i>Payment Details
						</h6>
						<div class="row small">
							<div class="col-md-4">Method: ${order.payment.payment_method}</div>
							<div class="col-md-4">Amount: LKR ${parseFloat(order.payment.amount).toFixed(2)}</div>
							<div class="col-md-4">
								Status: 
								<span id="order-status-pill" class="status-indicator-gradient" 
									style="background: linear-gradient(135deg, var(--primary-green), var(--dark-green)); 
											color: #ffffff; 
											padding: 4px 12px; 
											border-radius: 50px; 
											font-size: 0.85rem; 
											font-weight: 600; 
											display: inline-block; 
											text-transform: capitalize; 
											box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
											text-shadow: 1px 1px 2px rgba(0,0,0,0.2); 
											border: none;">
									${order.payment.payment_status}
								</span>
							</div>
						</div>
					</div>
				</div>
				` : ''}

				<h6 class="border-bottom pb-1 mb-2" style="font-size: 0.75rem;">
					<i class="fa-solid fa-basket-shopping me-1"></i>Items (${order.order_items.length})
				</h6>

				<div class="table-responsive mb-3">
					<table class="items-table-compact">
						<thead>
							<tr>
								<th>Product</th>
								<th>Qty</th>
								<th>Unit</th>
								<th class="text-end">Total</th>
							</tr>
						</thead>
						<tbody>${itemsHtml}</tbody>
					</table>
				</div>

				<div class="row">
					<div class="col-md-8"></div>
					<div class="col-md-4">
						<div class="bg-light p-2 rounded border">
							<div class="d-flex justify-content-between mb-1 small">
								<span>Subtotal:</span>
								<strong>LKR ${parseFloat(order.items_total || 0).toFixed(2)}</strong>
							</div>
							<hr class="my-1">
							<div class="d-flex justify-content-between">
								<span class="fw-bold">Total:</span>
								<span class="fw-bold text-primary">LKR ${parseFloat(order.total_amount).toFixed(2)}</span>
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
				<div class="container mt-4" style="max-width: 800px; margin: 0 auto; padding: 20px;">
					<h2 class="text-center mb-4" style="font-size: 1.2rem;">Order Invoice - ${order.order_number}</h2>
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
			@if(file_exists(public_path('assets/icons/Gif/question2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/question2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
			showCancelButton: true,
			confirmButtonColor: '#10B981',
			cancelButtonColor: '#6b7280',
			confirmButtonText: 'Yes, mark ready',
			cancelButtonText: 'Cancel',
			background: '#ffffff'
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.fire({
					title: 'Processing...',
					text: 'Please wait...',
					@if(file_exists(public_path('assets/icons/Gif/loading4.gif'))) imageUrl: '{{ asset('assets/icons/Gif/loading4.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'info' @endif,
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();
					},
					background: '#ffffff'
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
								@if(file_exists(public_path('assets/icons/Gif/success3.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success3.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
								title: 'Success!',
								text: response.message,
								timer: 1500,
								showConfirmButton: false,
								background: '#ffffff',
								toast: true,
								position: 'top-end'
							}).then(() => location.reload());
						} else {
							Swal.fire({
								@if(file_exists(public_path('assets/icons/Gif/error5.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error5.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
								title: 'Error',
								text: response.message,
								timer: 2000,
								showConfirmButton: false,
								background: '#ffffff',
								toast: true,
								position: 'top-end'
							});
						}
					},
					error: function(xhr) {
						Swal.close();
						let message = 'Failed to update order status.';
						if (xhr.status === 403) message = 'Unauthorized to update this order.';
						else if (xhr.status === 404) message = 'Order not found.';
						
						Swal.fire({
							@if(file_exists(public_path('assets/icons/Gif/error6.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error6.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
							title: 'Error',
							text: message,
							timer: 2000,
							showConfirmButton: false,
							background: '#ffffff',
							toast: true,
							position: 'top-end'
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
			@if(file_exists(public_path('assets/icons/Gif/loading3.gif'))) imageUrl: '{{ asset('assets/icons/Gif/loading3.gif') }}', imageWidth: 100, imageHeight: 60 @else icon: 'info' @endif,
			allowOutsideClick: false,
			didOpen: () => {
				Swal.showLoading();
			},
			background: '#ffffff'
		});
		setTimeout(() => location.reload(), 800);
	}

	$(function() {
		const savedSearch = localStorage.getItem('orderSearchTerm');
		if (savedSearch) {
			$('#searchOrders').val(savedSearch);
			performSearch();
		}

		$('#searchOrders').on('keyup', function() {
			localStorage.setItem('orderSearchTerm', $(this).val());
		});
	});
</script>
@endsection
