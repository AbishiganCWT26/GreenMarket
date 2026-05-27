@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Payment Verification')
@section('page-title', 'Delivery Payment Verification')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lead_farmer/Delivery_Order_transactions.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
<div class="txn-wrap">
	<div class="txn-container">
		<div class="txn-head">
			<div class="txn-head-icon">
				<i class="fas fa-file-invoice-dollar"></i>
			</div>
			<div class="txn-head-text">
				<h1>Payment Verification</h1>
				<p>Verify buyer payment slips for delivery orders</p>
			</div>
		</div>

		<div class="txn-filters">
			<div class="search-field">
				<i class="fas fa-search"></i>
				<input type="text" id="searchOrderId" placeholder="Order ID...">
			</div>
			<div class="search-field">
				<i class="fas fa-user"></i>
				<input type="text" id="searchBuyer" placeholder="Buyer Name...">
			</div>
			<div class="search-field">
				<i class="fas fa-tag"></i>
				<input type="text" id="searchProduct" placeholder="Product Name...">
			</div>
			<select id="filterDistrict" class="txn-select">
				<option value="">All Districts</option>
				@foreach($districts as $district)
					<option value="{{ $district }}">{{ $district }}</option>
				@endforeach
			</select>
		</div>

		<div class="txn-views">
			<button class="view-btn active" data-view="table">
				<i class="fas fa-table"></i>
				<span>Table</span>
			</button>
			<button class="view-btn" data-view="card">
				<i class="fas fa-grid-2"></i>
				<span>Card</span>
			</button>
		</div>

		<div id="txnContainer">
			@include('lead_farmer.partials.delivery_transactions_table', ['orders' => $orders])
		</div>
	</div>
</div>

<div class="txn-modal" id="verificationModal">
	<div class="txn-modal-box">
		<div class="txn-modal-head">
			<h3><i class="fas fa-check-double"></i> Verify Payment Slip</h3>
			<button class="txn-modal-close" id="closeVerificationModal"><i class="fas fa-times"></i></button>
		</div>
		<div class="txn-modal-body">
			<div class="slip-view" id="slipPreview"></div>
			<div class="order-details">
				<div><strong>Order #</strong><span id="modalOrderNum">-</span></div>
				<div><strong>Amount</strong><span id="modalAmount">-</span></div>
				<div><strong>Transaction ID</strong><span id="modalTransId">-</span></div>
				<div><strong>Date</strong><span id="modalDate">-</span></div>
			</div>
		</div>
		<div class="txn-modal-foot">
			<button class="txn-btn reject" id="btnRejectAction">Reject</button>
			<button class="txn-btn confirm" id="btnConfirmAction">Confirm</button>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
	let currentOrderId = null;
	let currentView = localStorage.getItem('lfTransactionsView') || 'table';

	function setView(mode) {
		currentView = mode;
		localStorage.setItem('lfTransactionsView', mode);
		$('.view-btn').removeClass('active');
		$(`.view-btn[data-view="${mode}"]`).addClass('active');
		fetchTransactions();
	}

	$('.view-btn').on('click', function() {
		setView($(this).data('view'));
	});

	function fetchTransactions() {
		const data = {
			order_id: $('#searchOrderId').val(),
			buyer_name: $('#searchBuyer').val(),
			product_name: $('#searchProduct').val(),
			district: $('#filterDistrict').val(),
			view: currentView
		};
		$.ajax({
			url: "{{ route('lf.deliveryTransactions') }}",
			data: data,
			success: function(html) {
				$('#txnContainer').html(html);
			}
		});
	}

	let searchTimer;
	$('#searchOrderId, #searchBuyer, #searchProduct').on('keyup', function() {
		clearTimeout(searchTimer);
		searchTimer = setTimeout(fetchTransactions, 500);
	});
	$('#filterDistrict').on('change', fetchTransactions);

	$(document).on('click', '.open-verify-modal', function() {
		const order = $(this).data('order');
		currentOrderId = order.id;
		
		$('#modalOrderNum').text(order.order_number);
		$('#modalAmount').text('LKR ' + parseFloat(order.total_amount).toLocaleString());
		$('#modalTransId').text(order.payment_delivery_order.transaction_id);
		$('#modalDate').text(order.payment_delivery_order.transaction_date + ' ' + order.payment_delivery_order.transaction_time);
		
		const slipPath = order.payment_delivery_order.payment_slip_path;
		const slipUrl = "{{ asset('') }}" + slipPath;
		const extension = slipPath.split('.').pop().toLowerCase();
		
		if (extension === 'pdf') {
			$('#slipPreview').html('<iframe src="' + slipUrl + '#toolbar=0"></iframe>');
		} else {
			$('#slipPreview').html('<img src="' + slipUrl + '" alt="Payment Slip">');
		}
		
		$('#verificationModal').fadeIn(200);
		$('body').addClass('no-scroll');
	});

	$('#closeVerificationModal').on('click', function() {
		$('#verificationModal').fadeOut(200);
		$('body').removeClass('no-scroll');
	});

	$('#btnConfirmAction').on('click', function() {
		Swal.fire({
			title: 'Confirm Payment',
			text: 'Are you sure you want to confirm this payment?',
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#10B981',
			cancelButtonColor: '#6b7280',
			confirmButtonText: 'Yes, confirm',
			cancelButtonText: 'Cancel',
			reverseButtons: true
		}).then((result) => {
			if (result.isConfirmed) {
				processVerification('confirm');
			}
		});
	});

	$('#btnRejectAction').on('click', function() {
		Swal.fire({
			title: 'Reject Payment',
			text: 'Please provide a reason for rejection:',
			input: 'textarea',
			inputPlaceholder: 'Enter reason here...',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#ef4444',
			cancelButtonColor: '#6b7280',
			confirmButtonText: 'Yes, reject',
			cancelButtonText: 'Cancel',
			inputValidator: (value) => {
				if (!value) return 'You need to provide a reason!';
			},
			reverseButtons: true
		}).then((result) => {
			if (result.isConfirmed) {
				processVerification('reject', result.value);
			}
		});
	});

	function processVerification(action, reason = '') {
		$.ajax({
			url: "{{ route('lf.orders.verifyDeliveryPayment') }}",
			method: 'POST',
			data: {
				_token: "{{ csrf_token() }}",
				order_id: currentOrderId,
				action: action,
				rejection_reason: reason
			},
			success: function(response) {
				if (response.success) {
					Swal.fire({
						icon: 'success',
						title: 'Success',
						text: response.message,
						confirmButtonColor: '#10B981',
						timer: 2000,
						showConfirmButton: true
					}).then(() => {
						$('#verificationModal').fadeOut(200);
						$('body').removeClass('no-scroll');
						fetchTransactions();
					});
				} else {
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: response.message,
						confirmButtonColor: '#10B981'
					});
				}
			},
			error: function(xhr) {
				let msg = 'Something went wrong. Please try again.';
				if (xhr.responseJSON) {
					if (xhr.responseJSON.errors) {
						msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
					} else if (xhr.responseJSON.message) {
						msg = xhr.responseJSON.message;
					}
				}
				Swal.fire({
					icon: 'error',
					title: 'Error',
					html: msg,
					confirmButtonColor: '#10B981'
				});
			}
		});
	}
});
</script>
@endsection