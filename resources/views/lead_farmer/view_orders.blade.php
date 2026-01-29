@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Manage Orders')
@section('page-title', 'Manage Orders')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lead_farmer/view_orders.css') }}">
@endsection

@section('content')
<div class="orders-container">
	<div class="orders-header">
		<h1 class="orders-title">
			<i class="fa-solid fa-sack-dollar"></i>
			Orders Management
		</h1>
		
		<div class="controls-container">
			<div class="view-toggle">
				<button class="view-toggle-btn active" data-view="cards">
					<i class="fa-solid fa-grip"></i>
					Cards
				</button>
				<button class="view-toggle-btn" data-view="table">
					<i class="fa-solid fa-table"></i>
					Table
				</button>
			</div>
			
			<div class="search-box">
				<input type="text" class="search-input" placeholder="Search orders..." id="searchInput">
				<i class="fa-solid fa-search search-icon"></i>
			</div>
			
			<div class="filters-box">
				<select class="filter-select" id="statusFilter">
					<option value="">All Status</option>
					<option value="confirmed">Confirmed</option>
					<option value="paid">Paid</option>
					<option value="ready_for_pickup">Ready</option>
					<option value="completed">Completed</option>
					<option value="cancelled">Cancelled</option>
				</select>
				
				<select class="filter-select" id="paymentFilter">
					<option value="">Payment Status</option>
					<option value="pending">Pending</option>
					<option value="completed">Completed</option>
				</select>
			</div>
		</div>
	</div>
	
	<div class="quick-stats">
		<div class="stat-item">
			<div class="stat-icon">
				<i class="fa-solid fa-cart-shopping"></i>
			</div>
			<div class="stat-content">
				<div class="stat-value" id="totalOrders">0</div>
				<div class="stat-label">Total Orders</div>
			</div>
		</div>
		
		<div class="stat-item">
			<div class="stat-icon">
				<i class="fa-solid fa-money-bill-wave"></i>
			</div>
			<div class="stat-content">
				<div class="stat-value" id="pendingPayments">0</div>
				<div class="stat-label">Pending Payments</div>
			</div>
		</div>
	</div>
	
	<div class="cards-view" id="cardsView">
		@if($orders->count() > 0)
			@foreach($orders as $order)
				<div class="order-card fade-in" data-status="{{ $order->order_status }}" 
					 data-payment="{{ $order->payments->where('payment_status', 'completed')->first() ? 'completed' : 'pending' }}"
					 data-search="{{ strtolower($order->order_number . ' ' . $order->buyer->name . ' ' . $order->farmer->name) }}"
					 data-buyer-name="{{ $order->buyer->name }}"
					 data-farmer-name="{{ $order->farmer->name }}"
					 data-order-id="{{ $order->id }}"
					 data-total-amount="{{ $order->total_amount }}">
					<div class="card-header">
						<div>
							<div class="order-id">
								<i class="fa-solid fa-hashtag"></i>
								{{ $order->order_number }}
							</div>
							<div class="order-date">
								{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}
							</div>
						</div>
						<div class="status-badge status-{{ $order->order_status }}">
							{{ str_replace('_', ' ', $order->order_status) }}
						</div>
					</div>
					
					<div class="card-body">
						<div class="details-grid">
							<div class="detail-item">
								<span class="detail-label">Buyer</span>
								<span class="detail-value">{{ $order->buyer->name }}</span>
							</div>
							<div class="detail-item">
								<span class="detail-label">Farmer</span>
								<span class="detail-value">{{ $order->farmer->name }}</span>
							</div>
							<div class="detail-item">
								<span class="detail-label">Contact</span>
								<span class="detail-value">{{ $order->farmer->primary_mobile }}</span>
							</div>
							<div class="detail-item">
								<span class="detail-label">Location</span>
								<span class="detail-value">{{ $order->farmer->residential_address }}</span>
							</div>
						</div>
						
						<div class="total-row">
							<span class="total-label">Total Amount</span>
							<span class="total-amount">LKR {{ number_format($order->total_amount, 2) }}</span>
						</div>
						
						<div class="payment-status">
							@php
								$payment = $order->payments->where('payment_status', 'completed')->first();
							@endphp
							@if($payment)
								<i class="fa-solid fa-circle-check payment-completed"></i>
								<span class="payment-completed">Paid: {{ $payment->payment_method }}</span>
							@else
								<i class="fa-solid fa-clock payment-pending"></i>
								<span class="payment-pending">Payment Pending</span>
							@endif
						</div>
						
						<div class="card-actions">
							@if(!$payment && $order->order_status != 'cancelled')
								<button class="action-btn btn-primary mark-payment-btn" 
										data-order-id="{{ $order->id }}"
										data-order-number="{{ $order->order_number }}"
										data-buyer-name="{{ $order->buyer->name }}"
										data-farmer-name="{{ $order->farmer->name }}"
										data-total-amount="{{ $order->total_amount }}">
									<i class="fa-solid fa-money-bill-wave"></i>
									Mark Paid
								</button>
							@endif
							
							@if($order->order_status == 'confirmed' && !$payment)
								<button class="action-btn btn-warning ready-pickup-btn" 
										data-order-id="{{ $order->id }}">
									<i class="fa-solid fa-box-open"></i>
									Ready
								</button>
							@endif
							
							@if($order->order_status == 'ready_for_pickup' && $payment)
								<button class="action-btn btn-primary complete-order-btn" 
										data-order-id="{{ $order->id }}">
									<i class="fa-solid fa-check-circle"></i>
									Complete
								</button>
							@endif
							
							<button class="action-btn btn-secondary view-details-btn" 
									data-order-id="{{ $order->id }}">
								<i class="fa-solid fa-eye"></i>
								View
							</button>
						</div>
						
						<div class="order-items" style="display: none;" data-order-id="{{ $order->id }}">
							@foreach($order->orderItems as $item)
								<div class="order-item" data-name="{{ $item->product_name_snapshot }}" data-quantity="{{ $item->quantity_ordered }}">
									{{ $item->product_name_snapshot }} ({{ $item->quantity_ordered }})
								</div>
							@endforeach
						</div>
					</div>
				</div>
			@endforeach
		@else
			<div class="no-orders">
				<div class="no-orders-icon">
					<i class="fa-solid fa-shopping-cart"></i>
				</div>
				<h3>No Orders Found</h3>
				<p>When orders are placed, they will appear here</p>
			</div>
		@endif
	</div>
	
	<div class="tables-view" id="tableView">
		@if($orders->count() > 0)
			<div class="table-container">
				<table class="data-table">
					<thead>
						<tr>
							<th>Order #</th>
							<th>Date</th>
							<th>Buyer</th>
							<th>Farmer</th>
							<th>Amount</th>
							<th>Status</th>
							<th>Payment</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($orders as $order)
							@php
								$payment = $order->payments->where('payment_status', 'completed')->first();
							@endphp
							<tr data-status="{{ $order->order_status }}" 
								data-payment="{{ $payment ? 'completed' : 'pending' }}"
								data-search="{{ strtolower($order->order_number . ' ' . $order->buyer->name . ' ' . $order->farmer->name) }}"
								data-buyer-name="{{ $order->buyer->name }}"
								data-farmer-name="{{ $order->farmer->name }}"
								data-order-id="{{ $order->id }}"
								data-total-amount="{{ $order->total_amount }}">
								<td>{{ $order->order_number }}</td>
								<td>{{ \Carbon\Carbon::parse($order->created_at)->format('M d') }}</td>
								<td>{{ $order->buyer->name }}</td>
								<td>{{ $order->farmer->name }}</td>
								<td>LKR {{ number_format($order->total_amount, 2) }}</td>
								<td>
									<span class="status-badge status-{{ $order->order_status }}">
										{{ str_replace('_', ' ', $order->order_status) }}
									</span>
								</td>
								<td>
									@if($payment)
										<span class="payment-completed">
											<i class="fa-solid fa-check-circle"></i> Paid
										</span>
									@else
										<span class="payment-pending">
											<i class="fa-solid fa-clock"></i> Pending
										</span>
									@endif
								</td>
								<td>
									<div class="table-actions">
										@if(!$payment && $order->order_status != 'cancelled')
											<button class="table-btn btn-primary mark-payment-btn" 
													data-order-id="{{ $order->id }}"
													data-order-number="{{ $order->order_number }}"
													data-buyer-name="{{ $order->buyer->name }}"
													data-farmer-name="{{ $order->farmer->name }}"
													data-total-amount="{{ $order->total_amount }}">
												<i class="fa-solid fa-money-bill-wave"></i>
											</button>
										@endif
										
										<button class="table-btn btn-secondary view-details-btn" 
												data-order-id="{{ $order->id }}">
											<i class="fa-solid fa-eye"></i>
										</button>
									</div>
									
									<div class="order-items" style="display: none;" data-order-id="{{ $order->id }}">
										@foreach($order->orderItems as $item)
											<div class="order-item" data-name="{{ $item->product_name_snapshot }}" data-quantity="{{ $item->quantity_ordered }}">
												{{ $item->product_name_snapshot }} ({{ $item->quantity_ordered }})
											</div>
										@endforeach
									</div>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		@else
			<div class="no-orders">
				<div class="no-orders-icon">
					<i class="fa-solid fa-shopping-cart"></i>
				</div>
				<h3>No Orders Found</h3>
				<p>When orders are placed, they will appear here</p>
			</div>
		@endif
	</div>
	
	@if($orders->hasPages())
		<div class="compact-pagination">
			@if($orders->onFirstPage())
				<span class="page-item disabled">
					<span class="page-link"><i class="fa-solid fa-chevron-left"></i></span>
				</span>
			@else
				<a href="{{ $orders->previousPageUrl() }}" class="page-item">
					<span class="page-link"><i class="fa-solid fa-chevron-left"></i></span>
				</a>
			@endif
			
			@for($i = 1; $i <= $orders->lastPage(); $i++)
				@if($i == $orders->currentPage())
					<span class="page-item active">
						<span class="page-link">{{ $i }}</span>
					</span>
				@else
					<a href="{{ $orders->url($i) }}" class="page-item">
						<span class="page-link">{{ $i }}</span>
					</a>
				@endif
			@endfor
			
			@if($orders->hasMorePages())
				<a href="{{ $orders->nextPageUrl() }}" class="page-item">
					<span class="page-link"><i class="fa-solid fa-chevron-right"></i></span>
				</a>
			@else
				<span class="page-item disabled">
					<span class="page-link"><i class="fa-solid fa-chevron-right"></i></span>
				</span>
			@endif
		</div>
	@endif
</div>

<div class="payment-modal" id="paymentModal">
	<div class="modal-content">
		<div class="modal-header">
			<h2>
				<i class="fa-solid fa-money-bill-wave"></i>
				Confirm Payment
			</h2>
			<button class="close-modal" id="closePaymentModal">
				<i class="fa-solid fa-times"></i>
			</button>
		</div>
		
		<div id="paymentForm">
			<div class="form-group">
				<label class="form-label">Order Number</label>
				<input type="text" class="form-control" id="modalOrderNumber" readonly>
			</div>
			
			<div class="form-group">
				<label class="form-label">Total Amount</label>
				<input type="text" class="form-control" id="modalOrderAmount" readonly>
			</div>
			
			<div class="form-group">
				<label class="form-label">Payment Method</label>
				<select class="form-control" id="paymentMethod">
					<option value="cash">Cash</option>
					<option value="bank">Bank Transfer</option>
					<option value="mobile_wallet">Mobile Wallet</option>
				</select>
			</div>
			
			<div class="form-group">
				<label class="form-label">Reference Number</label>
				<input type="text" class="form-control" id="transactionNumber" 
					   placeholder="Enter reference number">
			</div>
			
			<div class="invoice-preview">
				<div class="invoice-row">
					<span>Buyer:</span>
					<span id="invoiceBuyer"></span>
				</div>
				<div class="invoice-row">
					<span>Farmer:</span>
					<span id="invoiceFarmer"></span>
				</div>
				<div class="invoice-row">
					<span>Items:</span>
					<span id="invoiceItems"></span>
				</div>
			</div>
			
			<div class="modal-actions">
				<button class="btn btn-cancel" id="cancelPayment">
					Cancel
				</button>
				<button class="btn btn-success" id="confirmPayment">
					<i class="fa-solid fa-check-circle"></i>
					Confirm
				</button>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
	const cardsView = document.getElementById('cardsView');
	const tableView = document.getElementById('tableView');
	const viewToggleBtns = document.querySelectorAll('.view-toggle-btn');
	const searchInput = document.getElementById('searchInput');
	const statusFilter = document.getElementById('statusFilter');
	const paymentFilter = document.getElementById('paymentFilter');
	const paymentModal = document.getElementById('paymentModal');
	const closePaymentModal = document.getElementById('closePaymentModal');
	const cancelPayment = document.getElementById('cancelPayment');
	
	let currentOrderId = null;
	let currentOrderNumber = null;
	
	function updateStats() {
		const totalOrders = document.querySelectorAll('.order-card').length;
		const pendingPayments = document.querySelectorAll('.payment-pending').length;
		const readyForPickup = document.querySelectorAll('.status-ready_for_pickup').length;
		
		let totalRevenue = 0;
		document.querySelectorAll('.total-amount').forEach(element => {
			const text = element.textContent.replace('LKR', '').replace(',', '').trim();
			const amount = parseFloat(text);
			if (!isNaN(amount)) {
				totalRevenue += amount;
			}
		});
		
		document.getElementById('totalOrders').textContent = totalOrders;
		document.getElementById('pendingPayments').textContent = pendingPayments;
		document.getElementById('readyForPickup').textContent = readyForPickup;
		document.getElementById('totalRevenue').textContent = 'LKR ' + totalRevenue.toFixed(2);
	}
	
	viewToggleBtns.forEach(btn => {
		btn.addEventListener('click', function() {
			viewToggleBtns.forEach(b => b.classList.remove('active'));
			this.classList.add('active');
			
			const view = this.getAttribute('data-view');
			if (view === 'cards') {
				cardsView.style.display = 'grid';
				tableView.style.display = 'none';
			} else {
				cardsView.style.display = 'none';
				tableView.style.display = 'block';
			}
		});
	});
	
	function filterOrders() {
		const searchTerm = searchInput.value.toLowerCase();
		const statusValue = statusFilter.value;
		const paymentValue = paymentFilter.value;
		
		const cards = document.querySelectorAll('.order-card');
		const rows = document.querySelectorAll('.data-table tbody tr');
		
		cards.forEach(card => {
			const cardSearch = card.getAttribute('data-search');
			const cardStatus = card.getAttribute('data-status');
			const cardPayment = card.getAttribute('data-payment');
			
			let show = true;
			
			if (searchTerm && !cardSearch.includes(searchTerm)) {
				show = false;
			}
			
			if (statusValue && cardStatus !== statusValue) {
				show = false;
			}
			
			if (paymentValue && cardPayment !== paymentValue) {
				show = false;
			}
			
			card.style.display = show ? 'block' : 'none';
		});
		
		rows.forEach(row => {
			const rowSearch = row.getAttribute('data-search');
			const rowStatus = row.getAttribute('data-status');
			const rowPayment = row.getAttribute('data-payment');
			
			let show = true;
			
			if (searchTerm && !rowSearch.includes(searchTerm)) {
				show = false;
			}
			
			if (statusValue && rowStatus !== statusValue) {
				show = false;
			}
			
			if (paymentValue && rowPayment !== paymentValue) {
				show = false;
			}
			
			row.style.display = show ? 'table-row' : 'none';
		});
		
		updateStats();
	}
	
	searchInput.addEventListener('input', filterOrders);
	statusFilter.addEventListener('change', filterOrders);
	paymentFilter.addEventListener('change', filterOrders);
	
	function showPaymentModal(orderId, orderNumber, orderData) {
		currentOrderId = orderId;
		currentOrderNumber = orderNumber;
		
		document.getElementById('modalOrderNumber').value = orderNumber;
		document.getElementById('modalOrderAmount').value = 'LKR ' + parseFloat(orderData.total_amount).toFixed(2);
		document.getElementById('invoiceBuyer').textContent = orderData.buyer_name;
		document.getElementById('invoiceFarmer').textContent = orderData.farmer_name;
		document.getElementById('invoiceItems').textContent = orderData.items;
		
		paymentModal.style.display = 'flex';
		document.body.style.overflow = 'hidden';
	}
	
	document.addEventListener('click', function(e) {
		if (e.target.classList.contains('mark-payment-btn') || e.target.closest('.mark-payment-btn')) {
			const btn = e.target.classList.contains('mark-payment-btn') ? e.target : e.target.closest('.mark-payment-btn');
			const orderId = btn.getAttribute('data-order-id');
			const orderNumber = btn.getAttribute('data-order-number');
			const buyerName = btn.getAttribute('data-buyer-name');
			const farmerName = btn.getAttribute('data-farmer-name');
			const totalAmount = btn.getAttribute('data-total-amount');
			
			const orderCard = btn.closest('.order-card') || btn.closest('tr');
			const orderItemsContainer = orderCard.querySelector('.order-items[data-order-id="' + orderId + '"]');
			
			let itemsText = '';
			if (orderItemsContainer) {
				const orderItems = orderItemsContainer.querySelectorAll('.order-item');
				const itemsArray = [];
				orderItems.forEach(item => {
					const name = item.getAttribute('data-name');
					const quantity = item.getAttribute('data-quantity');
					itemsArray.push(name + ' (' + quantity + ')');
				});
				itemsText = itemsArray.join(', ');
			}
			
			const orderData = {
				total_amount: totalAmount || '0',
				buyer_name: buyerName || '',
				farmer_name: farmerName || '',
				items: itemsText || 'No items found'
			};
			
			showPaymentModal(orderId, orderNumber, orderData);
		}
		
		if (e.target.classList.contains('ready-pickup-btn') || e.target.closest('.ready-pickup-btn')) {
			const btn = e.target.classList.contains('ready-pickup-btn') ? e.target : e.target.closest('.ready-pickup-btn');
			const orderId = btn.getAttribute('data-order-id');
			
			Swal.fire({
				title: 'Mark as Ready?',
				text: 'Are you sure products are ready for pickup?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: '#f59e0b',
				cancelButtonColor: '#6b7280',
				confirmButtonText: 'Yes, mark ready',
				cancelButtonText: 'Cancel',
				width: 300
			}).then((result) => {
				if (result.isConfirmed) {
					updateOrderStatus(orderId, 'ready_for_pickup');
				}
			});
		}
		
		if (e.target.classList.contains('complete-order-btn') || e.target.closest('.complete-order-btn')) {
			const btn = e.target.classList.contains('complete-order-btn') ? e.target : e.target.closest('.complete-order-btn');
			const orderId = btn.getAttribute('data-order-id');
			
			Swal.fire({
				title: 'Complete Order?',
				text: 'Mark this order as completed?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: '#10B981',
				cancelButtonColor: '#6b7280',
				confirmButtonText: 'Yes, complete',
				cancelButtonText: 'Cancel',
				width: 300
			}).then((result) => {
				if (result.isConfirmed) {
					updateOrderStatus(orderId, 'completed');
				}
			});
		}
		
		if (e.target.classList.contains('view-details-btn') || e.target.closest('.view-details-btn')) {
			const btn = e.target.classList.contains('view-details-btn') ? e.target : e.target.closest('.view-details-btn');
			const orderId = btn.getAttribute('data-order-id');
			
			window.location.href = '{{ route("lf.orders.view", ["id" => ":id"]) }}'.replace(':id', orderId);
		}
	});
	
	function closeModal() {
		paymentModal.style.display = 'none';
		document.body.style.overflow = 'auto';
		currentOrderId = null;
		currentOrderNumber = null;
	}
	
	closePaymentModal.addEventListener('click', closeModal);
	cancelPayment.addEventListener('click', closeModal);
	
	paymentModal.addEventListener('click', function(e) {
		if (e.target === paymentModal) {
			closeModal();
		}
	});
	
	document.getElementById('confirmPayment').addEventListener('click', function() {
		const paymentMethod = document.getElementById('paymentMethod').value;
		const transactionNumber = document.getElementById('transactionNumber').value;
		
		if (!currentOrderId) {
			Swal.fire({
				title: 'Error',
				text: 'No order selected',
				icon: 'error',
				width: 300
			});
			return;
		}
		
		Swal.fire({
			title: 'Confirm Payment',
			text: 'Are you sure payment was received?',
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#10B981',
			cancelButtonColor: '#6b7280',
			confirmButtonText: 'Yes, confirm',
			cancelButtonText: 'Cancel',
			width: 300
		}).then((result) => {
			if (result.isConfirmed) {
				fetch('{{ route("lf.orders.markPayment") }}', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': '{{ csrf_token() }}',
						'Accept': 'application/json'
					},
					body: JSON.stringify({
						order_id: currentOrderId,
						payment_method: paymentMethod,
						transaction_number: transactionNumber || null
					})
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						Swal.fire({
							title: 'Success!',
							text: data.message,
							icon: 'success',
							timer: 2000,
							showConfirmButton: false,
							width: 300
						});
						
						setTimeout(() => {
							location.reload();
						}, 2000);
					} else {
						Swal.fire({
							title: 'Error',
							text: data.message || 'Failed to mark payment',
							icon: 'error',
							width: 300
						});
					}
					
					closeModal();
				})
				.catch(error => {
					Swal.fire({
						title: 'Network Error',
						text: 'Please try again',
						icon: 'error',
						width: 300
					});
				});
			}
		});
	});

	function updateOrderStatus(orderId, status) {
		fetch('{{ route("lf.orders.updateStatus") }}', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': '{{ csrf_token() }}',
				'Accept': 'application/json'
			},
			body: JSON.stringify({
				order_id: orderId,
				status: status
			})
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				Swal.fire({
					title: 'Success!',
					text: data.message,
					icon: 'success',
					timer: 2000,
					showConfirmButton: false,
					width: 300
				});
				
				setTimeout(() => {
					location.reload();
				}, 2000);
			} else {
				Swal.fire({
					title: 'Error',
					text: data.message || 'Failed to update status',
					icon: 'error',
					width: 300
				});
			}
		})
		.catch(error => {
			Swal.fire({
				title: 'Network Error',
				text: 'Please try again',
				icon: 'error',
				width: 300
			});
		});
	}
	
	updateStats();
	
	document.querySelectorAll('.order-card').forEach((card, index) => {
		card.style.animationDelay = (index * 0.1) + 's';
	});
});
</script>
@endsection