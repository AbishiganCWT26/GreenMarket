@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Manage Dispatch')
@section('page-title', 'Product Dispatch Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lead_farmer/delivery_products.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
<div class="dispatch-wrap">
	<div class="dispatch-container">
		<div class="dispatch-head">
			<div class="dispatch-head-icon">
				<i class="fas fa-truck-loading"></i>
			</div>
			<div class="dispatch-head-text">
				<h1>Logistics Management</h1>
				<p>Batch orders and dispatch via bus service</p>
			</div>
		</div>

		<div class="dispatch-filters">
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
			<select id="filterDistrict" class="dispatch-select">
				<option value="">All Districts</option>
				@foreach($districts as $district)
					<option value="{{ $district }}">{{ $district }}</option>
				@endforeach
			</select>
		</div>

		<div id="dispatchContainer">
			@include('lead_farmer.partials.delivery_products_table', ['orders' => $orders])
		</div>
	</div>
</div>

<div class="dispatch-modal" id="dispatchModal">
	<div class="dispatch-modal-box">
		<div class="dispatch-modal-head">
			<h3><i class="fas fa-bus"></i> Dispatch via Bus</h3>
			<button class="dispatch-modal-close" id="closeDispatchModal"><i class="fas fa-times"></i></button>
		</div>
		<form id="dispatchForm" enctype="multipart/form-data">
			@csrf
			<div class="dispatch-modal-body">
				<div class="form-field">
					<label><i class="fas fa-bus"></i> Bus Number <span class="required">*</span></label>
					<input type="text" name="bus_number" placeholder="e.g. NC-1234" required>
				</div>
				<div class="form-field">
					<label><i class="fas fa-image"></i> Bus Image</label>
					<input type="file" name="bus_image" accept="image/*">
				</div>
				<div class="form-field">
					<label><i class="fas fa-user"></i> Conductor Name <span class="required">*</span></label>
					<input type="text" id="conductorName" name="conductor_name" placeholder="Full Name" required>
					<span id="conductorNameError" style="display:none; color:#ef4444; font-size:0.82rem; margin-top:4px; display:block;"></span>
				</div>
				<div class="form-field">
					<label><i class="fas fa-phone"></i> Conductor Mobile <span class="required">*</span></label>
					<input type="tel" id="conductorMobile" name="conductor_mobile" placeholder="07XXXXXXXX" maxlength="10" inputmode="numeric" required>
					<span id="conductorMobileError" style="display:none; color:#ef4444; font-size:0.82rem; margin-top:4px; display:block;"></span>
				</div>
				<div class="form-field">
					<label><i class="fas fa-clock"></i> Estimated Arrival <span class="required">*</span></label>
					<input type="datetime-local" name="estimated_arrival_time" required>
				</div>
				<div class="form-field">
					<label><i class="fas fa-boxes"></i> Order Products</label>
					<div id="productSelector" class="product-list-display">
						<!-- Products will be listed here -->
					</div>
				</div>
			</div>
			<div class="dispatch-modal-foot">
				<button type="button" class="dispatch-btn cancel" id="btnCancelDispatch">Cancel</button>
				<button type="submit" class="dispatch-btn submit">Submit Dispatch</button>
			</div>
		</form>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {


	function fetchProducts() {
		const data = {
			order_id: $('#searchOrderId').val(),
			buyer_name: $('#searchBuyer').val(),
			product_name: $('#searchProduct').val(),
			district: $('#filterDistrict').val()
		};
		$.ajax({
			url: "{{ route('lf.deliveryProducts') }}",
			data: data,
			success: function(html) {
				$('#dispatchContainer').html(html);
			}
		});
	}

	let searchTimer;
	$('#searchOrderId, #searchBuyer, #searchProduct').on('keyup', function() {
		clearTimeout(searchTimer);
		searchTimer = setTimeout(fetchProducts, 500);
	});
	$('#filterDistrict').on('change', fetchProducts);

	$(document).on('click', '.open-dispatch-modal', function() {
		const order = $(this).data('order');
		$('#productSelector').empty();

		if (order.order_items && order.order_items.length) {
			order.order_items.forEach(item => {
				const rowHtml = `
					<div class="product-list-item">
						<input type="hidden" name="selected_order_items[]" value="${item.id}">
						<span class="item-order-num">${order.order_number}</span>
						<span class="item-name">${item.product_name_snapshot}</span>
						<span class="item-qty">Qty: ${item.quantity_ordered}</span>
					</div>
				`;
				$('#productSelector').append(rowHtml);
			});
		}

		// Set min date-time to now
		const now = new Date();
		now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
		const minDateTime = now.toISOString().slice(0, 16);
		$('input[name="estimated_arrival_time"]').attr('min', minDateTime);

		$('#dispatchModal').css('display', 'flex').hide().fadeIn(200);
		$('body').addClass('no-scroll');
	});

	function closedispatchModal() {
		$('#dispatchModal').fadeOut(200);
		$('body').removeClass('no-scroll');
		$('#dispatchForm')[0].reset();
		$('#productSelector').empty();
	}

	$('#closeDispatchModal, #btnCancelDispatch').on('click', function() {
		closedispatchModal();
	});

	// --- Conductor Name validation helpers ---
	function showNameError(msg) {
		$('#conductorNameError').text(msg).css('display', 'block');
		$('#conductorName').css('border-color', '#ef4444');
	}
	function clearNameError() {
		$('#conductorNameError').text('').css('display', 'none');
		$('#conductorName').css('border-color', '');
	}
	function validateConductorName() {
		const val = $('#conductorName').val().trim();
		if (val === '') {
			showNameError('Conductor name is required.');
			return false;
		}
		if (/[^a-zA-Z\s]/.test(val)) {
			showNameError('Name must contain only letters — no numbers or special characters.');
			return false;
		}
		clearNameError();
		return true;
	}

	// Strip digits and special characters from Conductor Name in real-time
	$('#conductorName').on('input', function() {
		let cleaned = $(this).val().replace(/[^a-zA-Z\s]/g, '');
		$(this).val(cleaned);
		if (cleaned.trim().length > 0) {
			validateConductorName();
		} else {
			clearNameError();
		}
	}).on('blur', function() {
		if ($(this).val().trim().length > 0) {
			validateConductorName();
		}
	});

	// --- Conductor Mobile validation helpers ---
	function showMobileError(msg) {
		$('#conductorMobileError').text(msg).css('display', 'block');
		$('#conductorMobile').css('border-color', '#ef4444');
	}
	function clearMobileError() {
		$('#conductorMobileError').text('').css('display', 'none');
		$('#conductorMobile').css('border-color', '');
	}
	function validateConductorMobile() {
		const val = $('#conductorMobile').val();
		if (val === '') {
			showMobileError('Conductor mobile number is required.');
			return false;
		}
		if (!/^\d+$/.test(val)) {
			showMobileError('Only digits are allowed — no spaces, letters, or special characters.');
			return false;
		}
		if (!val.startsWith('07')) {
			showMobileError('Mobile number must start with 07 (e.g. 07XXXXXXXX).');
			return false;
		}
		if (val.length !== 10) {
			showMobileError('Mobile number must be exactly 10 digits.');
			return false;
		}
		clearMobileError();
		return true;
	}

	// Strip non-digit characters in real-time and validate on the fly
	$('#conductorMobile').on('input', function() {
		let cleaned = $(this).val().replace(/\D/g, '');
		$(this).val(cleaned);
		if (cleaned.length > 0) {
			validateConductorMobile();
		} else {
			clearMobileError();
		}
	}).on('blur', function() {
		if ($(this).val().length > 0) {
			validateConductorMobile();
		}
	});

	$('#dispatchForm').on('submit', function(e) {
		e.preventDefault();


		// Validate conductor name first
		if (!validateConductorName()) {
			$('#conductorName').focus();
			return;
		}

		// Validate conductor mobile
		if (!validateConductorMobile()) {
			$('#conductorMobile').focus();
			return;
		}

		const arrivalInput = $('input[name="estimated_arrival_time"]');
		const arrivalTime = new Date(arrivalInput.val());
		const now = new Date();
		
		if (arrivalTime < now) {
			Swal.fire({
				icon: 'error',
				title: 'Invalid Time',
				text: 'Please select a date and time starting from now. Past dates and times are not available.',
				confirmButtonColor: '#10B981'
			});
			return;
		}

		const formData = new FormData(this);
		closedispatchModal();
		Swal.fire({
			title: 'Confirm Dispatch',
			text: 'Are you sure you want to dispatch these selected items?',
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#10B981',
			cancelButtonColor: '#6b7280',
			confirmButtonText: 'Yes, dispatch',
			cancelButtonText: 'Cancel',
			reverseButtons: true
		}).then((result) => {
			
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ route('lf.orders.submitBusDispatch') }}",
					method: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					success: function(response) {
						if (response.success) {
							closedispatchModal();
							fetchProducts();

							Swal.fire({
								icon: 'success',
								title: 'Success',
								text: response.message,
								confirmButtonColor: '#10B981',
								timer: 2000,
								showConfirmButton: true
							});
						} else {
							closedispatchModal();
							Swal.fire({
								icon: 'error',
								title: 'Error',
								text: response.message,
								confirmButtonColor: '#10B981'
							});
						}
					},
					error: function(xhr) {
						closedispatchModal();
						let msg = 'Something went wrong. Please try again.';
						if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
							let errors = '';
							$.each(xhr.responseJSON.errors, function(k, v) {
								errors += v[0] + '<br>';
							});
							msg = errors;
						} else if (xhr.responseJSON && xhr.responseJSON.message) {
							msg = xhr.responseJSON.message;
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
	});
});
</script>
@endsection