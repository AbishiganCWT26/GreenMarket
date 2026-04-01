@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Manage Orders')
@section('page-title', 'Manage Orders')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lead_farmer/view_orders.css') }}">
@endsection

@section('content')
<div class="order-app">
	<div class="app-header">
		<div class="header-left">
			<div class="header-icon">
				<i class="fas fa-sack-dollar"></i>
			</div>
			<div class="header-text">
				<h2 class="header-title">Orders Management</h2>
				<p class="header-subtitle">Track and manage customer orders</p>
			</div>
		</div>
		<div class="header-right">
			<div class="view-toggles">
				<button class="view-btn active" data-view="cards">
					<i class="fa-solid fa-table-cells"></i>
				</button>
				<button class="view-btn" data-view="table">
					<i class="fas fa-table"></i>
				</button>
			</div>
		</div>
	</div>

	<div class="stats-bar">
		<div class="stat-block">
			<span class="stat-value" id="totalOrders">0</span>
			<span class="stat-label">Total Orders</span>
		</div>
		<div class="stat-block">
			<span class="stat-value" id="pendingPayments">0</span>
			<span class="stat-label">Pending</span>
		</div>
		<div class="stat-block">
			<span class="stat-value" id="readyForPickup">0</span>
			<span class="stat-label">Ready</span>
		</div>
		<div class="stat-block">
			<span class="stat-value" id="totalRevenue">LKR 0</span>
			<span class="stat-label">Revenue</span>
		</div>
	</div>

	<div class="filter-bar">
		<div class="search-wrap">
			<i class="fas fa-search search-icon"></i>
			<input type="text" class="search-input" id="searchInput" placeholder="Search orders...">
		</div>
		<div class="filter-group">
			<select class="filter-select" id="statusFilter">
				<option value="">All Status</option>
				<option value="confirmed">Confirmed</option>
				<option value="paid">Paid</option>
				<option value="ready_for_pickup">Ready</option>
				<option value="completed">Completed</option>
				<option value="cancelled">Cancelled</option>
			</select>
			<select class="filter-select" id="paymentFilter">
				<option value="">All Payments</option>
				<option value="pending">Pending</option>
				<option value="completed">Completed</option>
			</select>
		</div>
	</div>

	<div class="cards-view active" id="cardsView">
		@if($orders->count() > 0)
			@foreach($orders as $order)
				@php $payment = $order->payments->where('payment_status', 'completed')->first(); @endphp
				<div class="order-card" data-status="{{ $order->order_status }}" 
					 data-payment="{{ $payment ? 'completed' : 'pending' }}"
					 data-search="{{ strtolower($order->order_number . ' ' . $order->buyer->name . ' ' . $order->farmer->name) }}"
					 data-buyer="{{ $order->buyer->name }}"
					 data-farmer="{{ $order->farmer->name }}"
					 data-id="{{ $order->id }}"
					 data-amount="{{ $order->total_amount }}">
					<div class="card-head">
						<div class="order-badge">#{{ $order->order_number }}</div>
						<span class="order-status status-{{ $order->order_status }}">{{ str_replace('_', ' ', $order->order_status) }}</span>
					</div>
					<div class="card-body">
						<div class="order-users">
							<div class="user-chip">
								<i class="fas fa-user"></i>
								<span>{{ $order->buyer->name }}</span>
							</div>
							<div class="user-chip">
								<i class="fas fa-user-tie"></i>
								<span>{{ $order->farmer->name }}</span>
							</div>
						</div>
						<div class="order-location">
							<i class="fas fa-map-pin"></i>
							<span>{{ $order->farmer->residential_address ?? 'No address' }}</span>
						</div>
						<div class="order-total">
							<span class="total-label">Total</span>
							<span class="total-value">LKR {{ number_format($order->total_amount, 2) }}</span>
						</div>
						<div class="payment-chip {{ $payment ? 'paid' : 'pending' }}">
							<i class="fas {{ $payment ? 'fa-check-circle' : 'fa-clock' }}"></i>
							<span>{{ $payment ? 'Paid via ' . $payment->payment_method : 'Payment Pending' }}</span>
						</div>
					</div>
					<div class="card-foot">
						<div class="action-group">
							@if(!$payment && $order->order_status != 'cancelled')
								<button class="action-btn primary small mark-payment" 
										data-id="{{ $order->id }}"
										data-number="{{ $order->order_number }}"
										data-buyer="{{ $order->buyer->name }}"
										data-farmer="{{ $order->farmer->name }}"
										data-amount="{{ $order->total_amount }}"
										data-items="{{ $order->orderItems->map(fn($i) => $i->product_name_snapshot . ' (' . $i->quantity_ordered . ')')->join(', ') }}">
									<i class="fas fa-money-bill-wave"></i>
								</button>
							@endif
							@if($order->order_status == 'confirmed' && !$payment)
								<button class="action-btn warning small ready-pickup" data-id="{{ $order->id }}">
									<i class="fas fa-box-open"></i>
								</button>
							@endif
							@if($order->order_status == 'ready_for_pickup' && $payment)
								<button class="action-btn primary small complete-order" data-id="{{ $order->id }}">
									<i class="fas fa-check-circle"></i>
								</button>
							@endif
							<button class="action-btn secondary small view-details" data-id="{{ $order->id }}">
								<i class="fas fa-eye"></i>
							</button>
						</div>
					</div>
					<div class="order-items-hidden" data-id="{{ $order->id }}" style="display:none;">
						@foreach($order->orderItems as $item)
							<div data-name="{{ $item->product_name_snapshot }}" data-qty="{{ $item->quantity_ordered }}"></div>
						@endforeach
					</div>
				</div>
			@endforeach
		@else
			<div class="empty-box">
				<i class="fas fa-shopping-cart"></i>
				<h3>No Orders Found</h3>
				<p>Orders will appear here when placed</p>
			</div>
		@endif
	</div>

	<div class="table-view" id="tableView">
		@if($orders->count() > 0)
			<div class="table-wrap">
				<table class="order-table">
					<thead>
						<tr>
							<th>Order</th>
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
							@php $payment = $order->payments->where('payment_status', 'completed')->first(); @endphp
							<tr data-status="{{ $order->order_status }}" 
								data-payment="{{ $payment ? 'completed' : 'pending' }}"
								data-search="{{ strtolower($order->order_number . ' ' . $order->buyer->name . ' ' . $order->farmer->name) }}"
								data-id="{{ $order->id }}">
								<td><span class="order-num">#{{ $order->order_number }}</span></td>
								<td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M') }}</td>
								<td>{{ $order->buyer->name }}</td>
								<td>{{ $order->farmer->name }}</td>
								<td class="amount-cell">LKR {{ number_format($order->total_amount, 2) }}</td>
								<td><span class="status-badge status-{{ $order->order_status }}">{{ str_replace('_', ' ', $order->order_status) }}</span></td>
								<td>
									@if($payment)
										<span class="pay-badge paid"><i class="fas fa-check-circle"></i> Paid</span>
									@else
										<span class="pay-badge pending"><i class="fas fa-clock"></i> Pending</span>
									@endif
								</td>
								<td>
									<div class="table-actions">
										@if(!$payment && $order->order_status != 'cancelled')
											<button class="table-btn primary mark-payment" 
													data-id="{{ $order->id }}"
													data-number="{{ $order->order_number }}"
													data-buyer="{{ $order->buyer->name }}"
													data-farmer="{{ $order->farmer->name }}"
													data-amount="{{ $order->total_amount }}"
													data-items="{{ $order->orderItems->map(fn($i) => $i->product_name_snapshot . ' (' . $i->quantity_ordered . ')')->join(', ') }}">
												<i class="fas fa-money-bill-wave"></i>
											</button>
										@endif
										<button class="table-btn secondary view-details" data-id="{{ $order->id }}">
											<i class="fas fa-eye"></i>
										</button>
									</div>
									<div class="order-items-hidden" data-id="{{ $order->id }}" style="display:none;">
										@foreach($order->orderItems as $item)
											<div data-name="{{ $item->product_name_snapshot }}" data-qty="{{ $item->quantity_ordered }}"></div>
										@endforeach
									</div>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		@endif
	</div>

	@if($orders->hasPages())
		<div class="page-bar">
			@if($orders->onFirstPage())
				<span class="page-link disabled"><i class="fas fa-chevron-left"></i></span>
			@else
				<a href="{{ $orders->previousPageUrl() }}" class="page-link"><i class="fas fa-chevron-left"></i></a>
			@endif
			
			@for($i = 1; $i <= $orders->lastPage(); $i++)
				@if($i == $orders->currentPage())
					<span class="page-link active">{{ $i }}</span>
				@else
					<a href="{{ $orders->url($i) }}" class="page-link">{{ $i }}</a>
				@endif
			@endfor
			
			@if($orders->hasMorePages())
				<a href="{{ $orders->nextPageUrl() }}" class="page-link"><i class="fas fa-chevron-right"></i></a>
			@else
				<span class="page-link disabled"><i class="fas fa-chevron-right"></i></span>
			@endif
		</div>
	@endif
</div>

<!-- Payment Modal -->
<div class="modal-overlay" id="paymentModal">
	<div class="modal-box">
		<div class="modal-head">
			<h3><i class="fas fa-money-bill-wave"></i> Confirm Payment</h3>
			<button class="modal-close" id="closePaymentModal"><i class="fas fa-times"></i></button>
		</div>
		<div class="modal-body">
			<div class="modal-field">
				<label>Order Number</label>
				<input type="text" class="modal-input" id="modalOrderNumber" readonly>
			</div>
			<div class="modal-field">
				<label>Total Amount</label>
				<input type="text" class="modal-input" id="modalOrderAmount" readonly>
			</div>
			<div class="modal-field">
				<label>Payment Method</label>
				<select class="modal-select" id="paymentMethod">
					<option value="cash">Cash</option>
					<option value="bank">Bank Transfer</option>
					<option value="mobile_wallet">Mobile Wallet</option>
				</select>
			</div>
			<div class="modal-field">
				<label>Reference Number</label>
				<input type="text" class="modal-input" id="transactionNumber" placeholder="Optional">
			</div>
			<div class="modal-preview">
				<div class="preview-row"><span>Buyer:</span> <span id="invoiceBuyer"></span></div>
				<div class="preview-row"><span>Farmer:</span> <span id="invoiceFarmer"></span></div>
				<div class="preview-row"><span>Items:</span> <span id="invoiceItems"></span></div>
			</div>
		</div>
		<div class="modal-foot">
			<button class="modal-btn cancel" id="cancelPayment">Cancel</button>
			<button class="modal-btn confirm" id="confirmPayment"><i class="fas fa-check"></i> Confirm</button>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
	const cardsView = document.getElementById('cardsView');
	const tableView = document.getElementById('tableView');
	const viewBtns = document.querySelectorAll('.view-btn');
	const searchInput = document.getElementById('searchInput');
	const statusFilter = document.getElementById('statusFilter');
	const paymentFilter = document.getElementById('paymentFilter');
	const modal = document.getElementById('paymentModal');
	const closeModal = document.getElementById('closePaymentModal');
	const cancelModal = document.getElementById('cancelPayment');

	let currentOrderId = null;
	let currentOrderNumber = null;

	// View toggle
	viewBtns.forEach(btn => {
		btn.addEventListener('click', function() {
			viewBtns.forEach(b => b.classList.remove('active'));
			this.classList.add('active');
			const view = this.dataset.view;
			if (view === 'cards') {
				cardsView.classList.add('active');
				tableView.classList.remove('active');
			} else {
				tableView.classList.add('active');
				cardsView.classList.remove('active');
			}
		});
	});

	// Filter function
	function filterItems() {
		const term = searchInput.value.toLowerCase();
		const status = statusFilter.value;
		const payment = paymentFilter.value;

		// Filter cards
		document.querySelectorAll('.order-card').forEach(card => {
			let show = true;
			const cardSearch = card.dataset.search;
			const cardStatus = card.dataset.status;
			const cardPayment = card.dataset.payment;

			if (term && !cardSearch.includes(term)) show = false;
			if (status && cardStatus !== status) show = false;
			if (payment && cardPayment !== payment) show = false;

			card.style.display = show ? 'block' : 'none';
		});

		// Filter table rows
		document.querySelectorAll('.order-table tbody tr').forEach(row => {
			let show = true;
			const rowSearch = row.dataset.search;
			const rowStatus = row.dataset.status;
			const rowPayment = row.dataset.payment;

			if (term && !rowSearch.includes(term)) show = false;
			if (status && rowStatus !== status) show = false;
			if (payment && rowPayment !== payment) show = false;

			row.style.display = show ? 'table-row' : 'none';
		});

		updateStats();
	}

	searchInput.addEventListener('input', filterItems);
	statusFilter.addEventListener('change', filterItems);
	paymentFilter.addEventListener('change', filterItems);

	// Stats update
	function updateStats() {
		const cards = document.querySelectorAll('.order-card');
		const pending = document.querySelectorAll('.payment-chip.pending').length;
		const ready = document.querySelectorAll('.status-ready_for_pickup').length;
		let total = 0;

		cards.forEach(c => {
			const amount = parseFloat(c.dataset.amount) || 0;
			total += amount;
		});

		document.getElementById('totalOrders').textContent = cards.length;
		document.getElementById('pendingPayments').textContent = pending;
		document.getElementById('readyForPickup').textContent = ready;
		document.getElementById('totalRevenue').textContent = 'LKR ' + total.toFixed(2);
	}

	// Payment modal
	function openPaymentModal(orderId, orderNumber, data) {
		currentOrderId = orderId;
		currentOrderNumber = orderNumber;
		document.getElementById('modalOrderNumber').value = orderNumber;
		document.getElementById('modalOrderAmount').value = 'LKR ' + parseFloat(data.amount).toFixed(2);
		document.getElementById('invoiceBuyer').textContent = data.buyer;
		document.getElementById('invoiceFarmer').textContent = data.farmer;
		document.getElementById('invoiceItems').textContent = data.items;
		modal.style.display = 'flex';
		document.body.style.overflow = 'hidden';
	}

	function closeModalFunc() {
		modal.style.display = 'none';
		document.body.style.overflow = 'auto';
		currentOrderId = null;
		currentOrderNumber = null;
	}

	closeModal.addEventListener('click', closeModalFunc);
	cancelModal.addEventListener('click', closeModalFunc);
	modal.addEventListener('click', (e) => {
		if (e.target === modal) closeModalFunc();
	});

	// Event delegation for buttons
	document.addEventListener('click', function(e) {
		const target = e.target.closest('button');
		if (!target) return;

		// Mark payment
		if (target.classList.contains('mark-payment')) {
			const id = target.dataset.id;
			const number = target.dataset.number;
			const data = {
				buyer: target.dataset.buyer,
				farmer: target.dataset.farmer,
				amount: target.dataset.amount,
				items: target.dataset.items
			};
			openPaymentModal(id, number, data);
		}

		// Ready for pickup
		if (target.classList.contains('ready-pickup')) {
			const id = target.dataset.id;
			Swal.fire({
				title: 'Mark as Ready?',
				text: 'Confirm products are ready for pickup',
				@if(file_exists(public_path('assets/icons/Gif/question1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/question1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
				showCancelButton: true,
				confirmButtonColor: '#f59e0b',
				cancelButtonColor: '#6b7280',
				confirmButtonText: 'Yes, mark ready'
			}).then(r => {
				if (r.isConfirmed) updateOrderStatus(id, 'ready_for_pickup');
			});
		}

		// Complete order
		if (target.classList.contains('complete-order')) {
			const id = target.dataset.id;
			Swal.fire({
				title: 'Complete Order?',
				text: 'Mark this order as completed',
				@if(file_exists(public_path('assets/icons/Gif/question1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/question1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
				showCancelButton: true,
				confirmButtonColor: '#10B981',
				cancelButtonColor: '#6b7280',
				confirmButtonText: 'Yes, complete'
			}).then(r => {
				if (r.isConfirmed) updateOrderStatus(id, 'completed');
			});
		}

		// View details
		if (target.classList.contains('view-details')) {
			const id = target.dataset.id;
			window.location.href = '{{ route("lf.orders.view", ["id" => ":id"]) }}'.replace(':id', id);
		}
	});

	// Confirm payment
	document.getElementById('confirmPayment').addEventListener('click', function() {
		const method = document.getElementById('paymentMethod').value;
		const ref = document.getElementById('transactionNumber').value;

		if (!currentOrderId) return;

		Swal.fire({
			title: 'Confirm Payment',
			text: 'Mark this order as paid?',
			@if(file_exists(public_path('assets/icons/Gif/question1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/question1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
			showCancelButton: true,
			confirmButtonColor: '#10B981',
			cancelButtonColor: '#6b7280',
			confirmButtonText: 'Yes, confirm'
		}).then(r => {
			if (r.isConfirmed) {
				fetch('{{ route("lf.orders.markPayment") }}', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					},
					body: JSON.stringify({
						order_id: currentOrderId,
						payment_method: method,
						transaction_number: ref || null
					})
				})
				.then(res => res.json())
				.then(data => {
					if (data.success) {
						Swal.fire({ @if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif, title: 'Success', text: data.message, timer: 1500, showConfirm: false });
						closeModalFunc();
						setTimeout(() => location.reload(), 1500);
					} else {
						Swal.fire({ @if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif, title: 'Error', text: data.message });
					}
				})
				.catch(() => Swal.fire({ @if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif, title: 'Error', text: 'Network error' }));
			}
		});
	});

	function updateOrderStatus(id, status) {
		fetch('{{ route("lf.orders.updateStatus") }}', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
			body: JSON.stringify({ order_id: id, status: status })
		})
		.then(res => res.json())
		.then(data => {
			if (data.success) {
				Swal.fire({ @if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif, title: 'Success', text: data.message, timer: 1500, showConfirm: false });
				setTimeout(() => location.reload(), 1500);
			} else {
				Swal.fire({ @if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif, title: 'Error', text: data.message });
			}
		})
		.catch(() => Swal.fire({ @if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif, title: 'Error', text: 'Network error' }));
	}

	// Animation delays
	document.querySelectorAll('.order-card').forEach((card, i) => {
		card.style.animationDelay = (i * 0.05) + 's';
	});

	updateStats();
});
</script>
@endsection