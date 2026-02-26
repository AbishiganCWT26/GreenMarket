@extends('farmer.layouts.farmer_master')

@section('title', 'File Complaint')
@section('page-title', 'File a Complaint')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/farmer/complaints-create.css') }}">
@endsection

@section('content')
<div class="complaint-create">
	<div class="create-header">
		<div class="header-left">
			<div class="header-icon">
				<i class="fas fa-headset"></i>
			</div>
			<div class="header-text">
				<h2 class="header-title">File a Complaint</h2>
				<p class="header-subtitle">Report any issues you're facing</p>
			</div>
		</div>
		<div class="header-right">
			<a href="{{ route('farmer.complaints.list') }}" class="btn-secondary">
				<i class="fas fa-arrow-left"></i>
				<span>Back</span>
			</a>
		</div>
	</div>

	<div class="create-card">
		<form id="complaintForm" method="POST" action="{{ route('farmer.complaints.store') }}">
			@csrf

			<div class="form-section">
				<div class="section-title">
					<i class="fas fa-exclamation-circle"></i>
					<h3>Complaint Details</h3>
				</div>

				<div class="form-grid">
					<div class="form-group full">
						<label class="form-label">
							<i class="fas fa-tag"></i>
							Complaint Type <span class="required">*</span>
						</label>
						<div class="radio-options">
							<label class="radio-card">
								<input type="radio" name="complaint_type" value="payment_delay" required>
								<div class="radio-content">
									<i class="fas fa-clock"></i>
									<span>Payment Delay</span>
								</div>
							</label>
							<label class="radio-card">
								<input type="radio" name="complaint_type" value="payment_missing" required>
								<div class="radio-content">
									<i class="fas fa-money-bill-wave"></i>
									<span>Missing Payment</span>
								</div>
							</label>
							<label class="radio-card">
								<input type="radio" name="complaint_type" value="wrong_data_entry" required>
								<div class="radio-content">
									<i class="fas fa-database"></i>
									<span>Wrong Data Entry</span>
								</div>
							</label>
							<label class="radio-card">
								<input type="radio" name="complaint_type" value="other" required>
								<div class="radio-content">
									<i class="fas fa-ellipsis-h"></i>
									<span>Other Issue</span>
								</div>
							</label>
						</div>
					</div>

					<div class="form-group full">
						<label class="form-label">
							<i class="fas fa-shopping-cart"></i>
							Related Order
						</label>
						<div class="select-wrapper">
							<select name="related_order_id" class="form-control">
								<option value="">Select order (optional)</option>
								@foreach($orders as $order)
								<option value="{{ $order->id }}">
									#{{ $order->order_number }} - {{ $order->buyer->name ?? 'Customer' }}
								</option>
								@endforeach
							</select>
							<i class="fas fa-chevron-down"></i>
						</div>
					</div>

					<div class="form-group full">
						<label class="form-label">
							<i class="fas fa-user-shield"></i>
							Complaint Against
						</label>
						<div class="recipient-selection">
							@if($leadFarmerUser)
								<label class="recipient-card">
									<input type="checkbox" name="against_user_id" value="{{ $leadFarmerUser->id }}">
									<div class="recipient-content">
										<div class="recipient-info">
											<span class="recipient-name">{{ $leadFarmerUser->name }}</span>
											<span class="recipient-role">Your Lead Farmer</span>
										</div>
										<i class="fas fa-check-circle"></i>
									</div>
								</label>
							@else
								<div class="no-recipient">
									<i class="fas fa-info-circle"></i>
									<span>No lead farmer assigned to your profile.</span>
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>

			<div class="form-section">
				<div class="section-title">
					<i class="fas fa-align-left"></i>
					<h3>Description</h3>
				</div>

				<div class="form-group full">
					<label class="form-label">
						<i class="fas fa-file-alt"></i>
						Description <span class="required">*</span>
					</label>
					<textarea
						name="description"
						id="description"
						class="form-control"
						rows="4"
						placeholder="Please provide detailed information about your complaint..."
						minlength="10"
						maxlength="1000"
						required
					></textarea>
					<div class="char-counter">
						<span id="charCount">0</span>/1000
					</div>
				</div>
			</div>

			<div class="form-actions">
				<a href="{{ route('farmer.complaints.list') }}" class="btn-cancel">
					<i class="fas fa-times"></i>
					Cancel
				</a>
				<button type="submit" class="btn-submit" id="submitBtn">
					<i class="fas fa-paper-plane"></i>
					Submit Complaint
				</button>
			</div>
		</form>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
	const desc = $('#description');
	const counter = $('#charCount');

	desc.on('input', function() {
		const len = $(this).val().length;
		counter.text(len);
		if (len < 10) {
			$(this).css('border-color', '#f59e0b');
		} else if (len > 1000) {
			$(this).css('border-color', '#ef4444');
		} else {
			$(this).css('border-color', '#10B981');
		}
	});

	$('.radio-card').on('click', function() {
		$('.radio-card').removeClass('selected');
		$(this).addClass('selected');
	});

	$('#complaintForm').on('submit', function(e) {
		e.preventDefault();

		const type = $('input[name="complaint_type"]:checked').val();
		const descVal = desc.val().trim();

		if (!type) {
			Swal.fire({
				icon: 'warning',
				title: 'Required Field',
				text: 'Please select a complaint type',
				confirmButtonColor: '#10B981'
			});
			return;
		}

		if (!descVal) {
			Swal.fire({
				icon: 'warning',
				title: 'Required Field',
				text: 'Please enter a description',
				confirmButtonColor: '#10B981'
			});
			return;
		}

		if (descVal.length < 10) {
			Swal.fire({
				icon: 'warning',
				title: 'Too Short',
				text: 'Description must be at least 10 characters',
				confirmButtonColor: '#10B981'
			});
			return;
		}

		const btn = $('#submitBtn');
		btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');

		Swal.fire({
			title: 'Submitting',
			text: 'Please wait...',
			allowOutsideClick: false,
			didOpen: () => Swal.showLoading()
		});

		$.ajax({
			url: $(this).attr('action'),
			type: 'POST',
			data: $(this).serialize(),
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
			success: function(res) {
				Swal.close();
				if (res.success) {
					Swal.fire({
						icon: 'success',
						title: 'Success!',
						html: `
							<div class="success-message">
								<i class="fas fa-check-circle"></i>
								<p>Your complaint has been submitted</p>
							</div>
						`,
						showConfirmButton: true,
						confirmButtonText: 'View Complaints',
						confirmButtonColor: '#10B981'
					}).then((r) => {
						if (r.isConfirmed) {
							window.location.href = '{{ route("farmer.complaints.list") }}';
						}
					});
				} else {
					btn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Submit Complaint');
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: res.message || 'Submission failed',
						confirmButtonColor: '#10B981'
					});
				}
			},
			error: function(xhr) {
				Swal.close();
				btn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Submit Complaint');

				let msg = 'An error occurred';
				if (xhr.responseJSON?.errors) {
					msg = Object.values(xhr.responseJSON.errors)[0][0];
				} else if (xhr.responseJSON?.message) {
					msg = xhr.responseJSON.message;
				}

				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: msg,
					confirmButtonColor: '#10B981'
				});
			}
		});
	});
});
</script>
@endsection