@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Order Details')
@section('page-title', 'Order Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lead_farmer/order_details.css') }}">
<link rel="stylesheet" href="{{ asset('css/lead_farmer/sweetalert_custom.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<div class="order-details-container">
	<div class="page-header">
		<div class="header-left">
			<h1 class="page-title"><i class="fas fa-file-invoice"></i> Order Details</h1>
			<p class="page-subtitle">View complete order information</p>
		</div>
		<div class="header-actions">
			<a href="{{ route('lf.orders') }}" class="btn-back">
				<i class="fas fa-arrow-left"></i> Back to Orders
			</a>
			<button class="btn-print" onclick="window.print()">
				<i class="fas fa-print"></i> Print
			</button>
		</div>
	</div>

	<div class="content-wrapper">
		<div class="order-summary-card">
			<div class="order-header-section">
				<div class="order-id-badge">
					<span class="badge-icon"><i class="fas fa-hashtag"></i></span>
					<span class="order-id-text">Order #{{ $order->order_number }}</span>
				</div>
				<div class="status-badge status-{{ strtolower(str_replace('_', '-', $order->order_status)) }}">
					@if($order->order_status == 'pending')
						<i class="fas fa-clock"></i>
					@elseif($order->order_status == 'confirmed')
						<i class="fas fa-check-circle"></i>
					@elseif($order->order_status == 'paid')
						<i class="fas fa-money-bill-wave"></i>
					@elseif($order->order_status == 'ready_for_pickup')
						<i class="fas fa-box-open"></i>
					@elseif($order->order_status == 'completed')
						<i class="fas fa-check-double"></i>
					@elseif($order->order_status == 'cancelled')
						<i class="fas fa-times-circle"></i>
					@else
						<i class="fas fa-file-invoice"></i>
					@endif
					<span class="status-text">{{ ucfirst(str_replace('_', ' ', $order->order_status)) }}</span>
				</div>
			</div>

			<div class="order-details-grid">
				<div class="order-info-section">
					<div class="section-header">
						<i class="fas fa-info-circle"></i>
						<h3>Order Information</h3>
					</div>
					<div class="info-grid">
						<div class="info-item">
							<span class="info-label"><i class="fas fa-calendar-alt"></i> Order Date:</span>
							<span class="info-value">{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d h:i A') }}</span>
						</div>
						<div class="info-item">
							<span class="info-label"><i class="fas fa-calendar-check"></i> Paid Date:</span>
							<span class="info-value">
								@if($order->paid_date)
									{{ \Carbon\Carbon::parse($order->paid_date)->format('Y-m-d h:i A') }}
								@else
									<span class="text-muted">Not paid yet</span>
								@endif
							</span>
						</div>
						<div class="info-item">
							<span class="info-label"><i class="fas fa-money-bill-wave"></i> Payment Status:</span>
							<span class="info-value">
								@php
									$paymentStatus = 'Pending';
									if($order->order_status == 'paid' || $order->order_status == 'completed') {
										$paymentStatus = 'Completed';
									} elseif($order->order_status == 'cancelled') {
										$paymentStatus = 'Cancelled';
									}
								@endphp
								<span class="payment-status-badge status-{{ strtolower($paymentStatus) }}">
									{{ $paymentStatus }}
								</span>
							</span>
						</div>
						<div class="info-item">
							<span class="info-label"><i class="fas fa-coins"></i> Total Amount:</span>
							<span class="info-value total-amount">LKR {{ number_format($order->total_amount, 2) }}</span>
						</div>
					</div>
				</div>

				<div class="customer-info-section">
					<div class="section-header">
						<i class="fas fa-user"></i>
						<h3>Buyer Information</h3>
					</div>
					<div class="info-grid">
						<div class="info-item">
							<span class="info-label"><i class="fas fa-user-tag"></i> Name:</span>
							<span class="info-value">{{ $order->buyer->name ?? 'N/A' }}</span>
						</div>
						<div class="info-item">
							<span class="info-label"><i class="fas fa-phone"></i> Phone:</span>
							<span class="info-value">{{ $order->buyer->primary_mobile ?? 'N/A' }}</span>
						</div>
					</div>
				</div>

				<div class="farmer-info-section">
					<div class="section-header">
						<i class="fas fa-user-tie"></i>
						<h3>Farmer Information</h3>
					</div>
					<div class="info-grid">
						<div class="info-item">
							<span class="info-label"><i class="fas fa-user-tag"></i> Name:</span>
							<span class="info-value">{{ $order->farmer->name ?? 'N/A' }}</span>
						</div>
						<div class="info-item">
							<span class="info-label"><i class="fas fa-phone"></i> Phone:</span>
							<span class="info-value">{{ $order->farmer->primary_mobile ?? 'N/A' }}</span>
						</div>
					</div>
				</div>

				<div class="pickup-info-section">
					<div class="section-header">
						<i class="fas fa-map-marker-alt"></i>
						<h3>Pickup Information</h3>
					</div>
					<div class="info-grid">
						<div class="info-item">
							<span class="info-label"><i class="fas fa-location-dot"></i> Location:</span>
							<span class="info-value">{{ $order->pickup_address ?? $order->farmer->residential_address ?? 'N/A' }}</span>
						</div>
						@if($order->pickup_map_link ?? $order->farmer->address_map_link)
						<div class="info-item">
							<span class="info-label"><i class="fas fa-map"></i> Map Link:</span>
							<a href="{{ $order->pickup_map_link ?? $order->farmer->address_map_link }}" target="_blank" class="info-value link">
								<i class="fas fa-external-link-alt"></i> Open in Maps
							</a>
						</div>
						@endif
					</div>
				</div>
			</div>
		</div>

		<div class="order-items-section">
			<div class="section-header">
				<i class="fas fa-shopping-cart"></i>
				<h3>Order Items</h3>
				<span class="item-count">({{ $order->orderItems->count() }} items)</span>
			</div>

			<div class="items-table-container">
				<table class="items-table">
					<thead>
						<tr>
							<th class="item-pic">Image</th>
							<th class="item-name">Product</th>
							<th class="item-quantity">Quantity</th>
							<th class="item-stock">Available Stock</th>
							<th class="item-price">Unit Price (LKR)</th>
							<th class="item-total">Total (LKR)</th>
						</tr>
					</thead>
					<tbody>
						@foreach($order->orderItems as $item)
						<tr class="item-row">
							<td class="item-pic">
								<div class="product-image">
									@php
										$imagePath = 'uploads/product_images/' . ($item->product->product_photo ?? '');
										$fullPath = public_path($imagePath);
										$placeholderPath = public_path('assets/images/product-placeholder.png');
										
										if($item->product && $item->product->product_photo && file_exists($fullPath)) {
											$imageUrl = asset($imagePath);
										} else {
											$imageUrl = asset('assets/images/product-placeholder.png');
										}
									@endphp
									<img src="{{ $imageUrl }}" alt="{{ $item->product_name_snapshot }}" 
										 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
								</div>
							</td>
							<td class="item-name">
								<div class="product-name">{{ $item->product_name_snapshot }}</div>
								@if($item->product)
								<div class="product-info">
									<small class="text-muted">Product ID: {{ $item->product->id }}</small>
								</div>
								@endif
							</td>
							<td class="item-quantity">
								<div class="quantity-display">
									<span class="quantity-value">{{ $item->quantity_ordered }}</span>
									<small class="text-muted">{{ $item->product->unit_of_measure ?? 'unit' }}</small>
								</div>
							</td>
							<td class="item-stock">
								@if($item->product)
									@if($item->product->quantity >= $item->quantity_ordered)
										<span class="stock-available">
											<i class="fas fa-check-circle"></i>
											{{ $item->product->quantity }} available
										</span>
									@else
										<span class="stock-low">
											<i class="fas fa-exclamation-triangle"></i>
											{{ $item->product->quantity }} available
										</span>
									@endif
								@else
									<span class="stock-na">N/A</span>
								@endif
							</td>
							<td class="item-price">
								LKR {{ number_format($item->unit_price_snapshot, 2) }}
							</td>
							<td class="item-total">
								<span class="total-price">LKR {{ number_format($item->item_total, 2) }}</span>
							</td>
						</tr>
						@endforeach
					</tbody>
					<tfoot>
						<tr class="order-summary-row">
							<td colspan="5" class="text-right summary-label">
								<strong>Order Total:</strong>
							</td>
							<td class="order-total">
								<strong>LKR {{ number_format($order->total_amount, 2) }}</strong>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>

		<div class="payment-history-section">
			<div class="section-header">
				<i class="fas fa-history"></i>
				<h3>Payment History</h3>
			</div>
			<div class="payments-container">
				@if($order->payments && $order->payments->count() > 0)
					@foreach($order->payments as $payment)
					<div class="payment-card">
						<div class="payment-header">
							<div class="payment-ref">
								<i class="fas fa-receipt"></i>
								<span>{{ $payment->payment_reference }}</span>
							</div>
							<div class="payment-status status-{{ strtolower($payment->payment_status) }}">
								{{ ucfirst($payment->payment_status) }}
							</div>
						</div>
						<div class="payment-details">
							<div class="payment-item">
								<span class="payment-label">Amount:</span>
								<span class="payment-value">LKR {{ number_format($payment->amount, 2) }}</span>
							</div>
							<div class="payment-item">
								<span class="payment-label">Method:</span>
								<span class="payment-value">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
							</div>
							<div class="payment-item">
								<span class="payment-label">Date:</span>
								<span class="payment-value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d h:i A') }}</span>
							</div>
							@if($payment->transaction_id)
							<div class="payment-item">
								<span class="payment-label">Transaction ID:</span>
								<span class="payment-value">{{ $payment->transaction_id }}</span>
							</div>
							@endif
						</div>
					</div>
					@endforeach
				@else
				<div class="no-payments">
					<i class="fas fa-money-bill-slash"></i>
					<p>No payment records found for this order.</p>
				</div>
				@endif
			</div>
		</div>

		<div class="action-buttons">
			@if($order->order_status == 'pending' || $order->order_status == 'confirmed')
			<button class="btn-mark-paid" onclick="markPaymentReceived({{ $order->id }})">
				<i class="fas fa-money-bill-wave"></i> Mark as Paid
			</button>
			@endif
			
			@if($order->order_status == 'paid')
			<button class="btn-ready-pickup" onclick="updateOrderStatus({{ $order->id }}, 'ready_for_pickup')">
				<i class="fas fa-box-open"></i> Ready for Pickup
			</button>
			@endif
			
			@if($order->order_status == 'ready_for_pickup')
			<button class="btn-complete" onclick="updateOrderStatus({{ $order->id }}, 'completed')">
				<i class="fas fa-check-double"></i> Mark as Completed
			</button>
			@endif
		</div>
	</div>
</div>
@endsection

@section('scripts')

<script src="{{ asset('js/lead_farmer/order_details.js') }}"></script>
<script>
function markPaymentReceived(orderId) {
	Swal.fire({
		title: 'Mark Payment Received',
		html: `
			<div class="payment-modal">
				<div class="form-group">
					<label>Payment Method *</label>
					<select id="paymentMethod" class="swal2-input">
						<option value="cash">Cash</option>
						<option value="bank">Bank Transfer</option>
						<option value="mobile_wallet">Mobile Wallet</option>
					</select>
				</div>
				<div class="form-group">
					<label>Transaction Number (Optional)</label>
					<input type="text" id="transactionNumber" class="swal2-input" placeholder="Enter transaction number">
				</div>
			</div>
		`,
		showCancelButton: true,
		confirmButtonText: 'Mark as Paid',
		confirmButtonColor: '#10B981',
		cancelButtonText: 'Cancel',
		cancelButtonColor: '#6b7280',
		reverseButtons: true,
		width: '400px',
		customClass: {
			popup: 'payment-swal-popup'
		},
		preConfirm: () => {
			const method = document.getElementById('paymentMethod').value;
			const transaction = document.getElementById('transactionNumber').value;
			
			if (!method) {
				Swal.showValidationMessage('Please select payment method');
				return false;
			}
			
			return { method, transaction };
		}
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: "{{ route('lf.orders.markPayment') }}",
				method: 'POST',
				data: {
					order_id: orderId,
					payment_method: result.value.method,
					transaction_number: result.value.transaction,
					_token: '{{ csrf_token() }}'
				},
				beforeSend: function() {
					Swal.fire({
						title: 'Processing...',
						text: 'Please wait',
						@if(file_exists(public_path('assets/icons/Gif/loading4.gif'))) imageUrl: '{{ asset('assets/icons/Gif/loading4.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'info' @endif,
						allowOutsideClick: false,
						didOpen: () => {
							Swal.showLoading();
						}
					});
				},
				success: function(response) {
					if (response.success) {
						Swal.fire({
							title: 'Success!',
							text: response.message,
							@if(file_exists(public_path('assets/icons/Gif/success3.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success3.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
							confirmButtonColor: '#10B981',
							timer: 2000,
							showConfirmButton: false
						}).then(() => {
							location.reload();
						});
					} else {
						Swal.fire({
							title: 'Error!',
							text: response.message,
							@if(file_exists(public_path('assets/icons/Gif/error2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
							confirmButtonColor: '#EF4444'
						});
					}
				},
				error: function(xhr) {
					const error = xhr.responseJSON;
					Swal.fire({
						title: 'Error!',
						text: error.message || 'Something went wrong',
						@if(file_exists(public_path('assets/icons/Gif/error5.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error5.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
						confirmButtonColor: '#EF4444'
					});
				}
			});
		}
	});
}

function updateOrderStatus(orderId, status) {
	const statusText = status.replace('_', ' ').toUpperCase();
	
	Swal.fire({
		title: 'Update Order Status',
		text: `Are you sure you want to mark this order as ${statusText}?`,
		@if(file_exists(public_path('assets/icons/Gif/question2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/question2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
		showCancelButton: true,
		confirmButtonText: `Yes, mark as ${statusText}`,
		confirmButtonColor: '#10B981',
		cancelButtonText: 'Cancel',
		cancelButtonColor: '#6b7280',
		reverseButtons: true
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: "{{ route('lf.orders.updateStatus') }}",
				method: 'POST',
				data: {
					order_id: orderId,
					status: status,
					_token: '{{ csrf_token() }}'
				},
				beforeSend: function() {
					Swal.fire({
						title: 'Updating...',
						text: 'Please wait',
						@if(file_exists(public_path('assets/icons/Gif/loading3.gif'))) imageUrl: '{{ asset('assets/icons/Gif/loading3.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'warning' @endif,
						allowOutsideClick: false,
						didOpen: () => {
							Swal.showLoading();
						}
					});
				},
				success: function(response) {
					if (response.success) {
						Swal.fire({
							title: 'Success!',
							text: response.message,
							@if(file_exists(public_path('assets/icons/Gif/success4.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success4.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
							confirmButtonColor: '#10B981',
							timer: 1500,
							showConfirmButton: false
						}).then(() => {
							location.reload();
						});
					} else {
						Swal.fire({
							title: 'Error!',
							text: response.message,
							@if(file_exists(public_path('assets/icons/Gif/error6.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error6.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
							confirmButtonColor: '#EF4444'
						});
					}
				},
				error: function(xhr) {
					const error = xhr.responseJSON;
					Swal.fire({
						title: 'Error!',
						text: error.message || 'Something went wrong',
						@if(file_exists(public_path('assets/icons/Gif/error2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
						confirmButtonColor: '#EF4444'
					});
				}
			});
		}
	});
}

</script>
@endsection
