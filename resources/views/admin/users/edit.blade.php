@extends('admin.layouts.admin_master')

@section('title', 'Edit User')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/edit-user-management.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<div class="edit-user-container">
	<div class="page-header">
		<div class="header-content">
			<div class="back-btn-wrapper">
				<a href="{{ route('admin.users.index') }}" class="btn-back">
					<i class="fas fa-arrow-left"></i>
				</a>
			</div>
			<div class="header-main">
				<h1><i class="fas fa-user-edit"></i> Edit User</h1>
				<p>Update user information and account settings</p>
			</div>
			<div class="user-status-badge">
				@if($user->is_active)
				<span class="status-active">
					<i class="fas fa-circle"></i> Active
				</span>
				@else
				<span class="status-inactive">
					<i class="fas fa-circle"></i> Inactive
				</span>
				@endif
			</div>
		</div>
		<div class="user-quick-info">
			<div class="user-details">
				<h3>{{ $user->username }}</h3>
				<div class="user-meta">
					<span class="role-badge role-{{ $user->role }}">
						<i class="fas fa-{{ $user->role == 'admin' ? 'crown' : ($user->role == 'farmer' ? 'tractor' : ($user->role == 'buyer' ? 'shopping-cart' : 'user')) }}"></i>
						{{ ucfirst(str_replace('_', ' ', $user->role)) }}
					</span>
					<span class="user-id">ID: HGH{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
				</div>
			</div>
		</div>
	</div>

	<div class="edit-form-wrapper">
		<form method="POST" action="{{ route('admin.users.update', $user->id) }}" id="editUserForm" class="edit-form">
			@csrf
			@method('PUT')

			<div class="form-sections">
				<div class="form-section">
					<div class="section-header">
						<div class="section-icon">
							<i class="fas fa-user-circle"></i>
						</div>
						<h3>Basic Information</h3>
					</div>
					<div class="form-fields">
						<div class="form-row">
							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-user"></i> Username
								</label>
								<input type="text" name="username" class="form-input" value="{{ $user->username }}" required>
							</div>
							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-envelope"></i> Email
								</label>
								<input type="email" name="email" class="form-input" value="{{ $user->email }}">
							</div>
						</div>

						<div class="form-row">
							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-user-tag"></i> Role
								</label>
								@if($user->id == Auth::id())
								<input type="text" class="form-input" value="{{ ucfirst(str_replace('_', ' ', $user->role)) }}" readonly>
								<input type="hidden" name="role" value="{{ $user->role }}">
								<small class="form-note">You cannot change your own role</small>
								@elseif(in_array($user->role, ['facilitator', 'buyer', 'admin', 'subadmin']))
								<input type="text" class="form-input" value="{{ ucfirst(str_replace('_', ' ', $user->role)) }}" readonly>
								<input type="hidden" name="role" value="{{ $user->role }}">
								<small class="form-note">Role cannot be changed for {{ ucfirst(str_replace('_', ' ', $user->role)) }}</small>
								@elseif($user->role == 'farmer')
								<select name="role" class="form-select">
									<option value="farmer" {{ $user->role == 'farmer' ? 'selected' : '' }}>Farmer</option>
									<option value="lead_farmer" {{ $user->role == 'lead_farmer' ? 'selected' : '' }}>Lead Farmer</option>
								</select>
								<small class="form-note">Farmer can only be changed to Lead Farmer</small>
								@elseif($user->role == 'lead_farmer')
								<select name="role" class="form-select">
									<option value="lead_farmer" {{ $user->role == 'lead_farmer' ? 'selected' : '' }}>Lead Farmer</option>
									<option value="farmer" {{ $user->role == 'farmer' ? 'selected' : '' }}>Farmer</option>
								</select>
								<small class="form-note">Lead Farmer can only be changed to Farmer</small>
								@endif
							</div>
							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-toggle-on"></i> Account Status
								</label>
								<select name="is_active" class="form-select" {{ $user->id == Auth::id() ? 'disabled' : '' }}>
									<option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
									<option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive</option>
								</select>
								@if($user->id == Auth::id())
								<small class="form-note">You cannot deactivate your own account</small>
								@endif
							</div>
						</div>
					</div>
				</div>

				@if(in_array($user->role, ['farmer', 'lead_farmer']))
					@php
						$userDetails = $user->role == 'farmer'
							? DB::table('farmers')->where('user_id', $user->id)->first()
							: DB::table('lead_farmers')->where('user_id', $user->id)->first();
					@endphp

					@if($userDetails)
					<div class="form-section payment-section">
						<div class="section-header">
							<div class="section-icon">
								<i class="fas fa-money-bill-wave"></i>
							</div>
							<div class="section-title">
								<h3>Payment Details</h3>
								<p class="section-subtitle">Changes to payment details require OTP verification</p>
							</div>
							<div class="section-badge">
								<span class="secure-badge">
									<i class="fas fa-shield-alt"></i> Secure
								</span>
							</div>
						</div>
						<div class="form-fields">
							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-credit-card"></i> Preferred Payment Method
								</label>
								<select name="preferred_payment" class="form-select">
									<option value="bank" {{ $userDetails->preferred_payment == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
									<option value="ezcash" {{ $userDetails->preferred_payment == 'ezcash' ? 'selected' : '' }}>Ez Cash</option>
									<option value="mcash" {{ $userDetails->preferred_payment == 'mcash' ? 'selected' : '' }}>mCash</option>
									<option value="all" {{ $userDetails->preferred_payment == 'all' ? 'selected' : '' }}>All Methods</option>
								</select>
							</div>

							<div class="form-row">
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-university"></i> Bank Account Number
									</label>
									<input type="text" name="account_number" class="form-input" value="{{ $userDetails->account_number ?? '' }}">
								</div>
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-user-tie"></i> Account Holder Name
									</label>
									<input type="text" name="account_holder_name" class="form-input" value="{{ $userDetails->account_holder_name ?? '' }}">
								</div>
							</div>

							<div class="form-row">
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-landmark"></i> Bank Name
									</label>
									<input type="text" name="bank_name" class="form-input" value="{{ $userDetails->bank_name ?? '' }}">
								</div>
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-map-marker-alt"></i> Bank Branch
									</label>
									<input type="text" name="bank_branch" class="form-input" value="{{ $userDetails->bank_branch ?? '' }}">
								</div>
							</div>

							<div class="form-row">
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-mobile-alt"></i> Ez Cash Number
									</label>
									<input type="text" name="ezcash_mobile" class="form-input" value="{{ $userDetails->ezcash_mobile ?? '' }}">
								</div>
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-phone-alt"></i> mCash Number
									</label>
									<input type="text" name="mcash_mobile" class="form-input" value="{{ $userDetails->mcash_mobile ?? '' }}">
								</div>
							</div>

							@if($user->role == 'lead_farmer' && $userDetails)
							<div class="form-row">
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-users"></i> Group Name
									</label>
									<input type="text" name="group_name" class="form-input" value="{{ $userDetails->group_name ?? '' }}">
								</div>
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-hashtag"></i> Group Number
									</label>
									<input type="text" name="group_number" class="form-input" value="{{ $userDetails->group_number ?? '' }}">
								</div>
							</div>
							@endif
						</div>
					</div>
					@endif
				@endif

				@if($user->role == 'buyer')
					@php
						$buyerDetails = DB::table('buyers')->where('user_id', $user->id)->first();
					@endphp

					@if($buyerDetails)
					<div class="form-section business-section">
						<div class="section-header">
							<div class="section-icon">
								<i class="fas fa-briefcase"></i>
							</div>
							<h3>Business Information</h3>
						</div>
						<div class="form-fields">
							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-store"></i> Business Name
								</label>
								<input type="text" name="business_name" class="form-input" value="{{ $buyerDetails->business_name }}">
							</div>

							<div class="form-row">
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-building"></i> Business Type
									</label>
									<select name="business_type" class="form-select">
										<option value="individual" {{ $buyerDetails->business_type == 'individual' ? 'selected' : '' }}>Individual</option>
										<option value="restaurant" {{ $buyerDetails->business_type == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
										<option value="hotel" {{ $buyerDetails->business_type == 'hotel' ? 'selected' : '' }}>Hotel</option>
										<option value="retailer" {{ $buyerDetails->business_type == 'retailer' ? 'selected' : '' }}>Retailer</option>
										<option value="wholesaler" {{ $buyerDetails->business_type == 'wholesaler' ? 'selected' : '' }}>Wholesaler</option>
									</select>
								</div>
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-phone"></i> Contact Number
									</label>
									<input type="text" class="form-input" value="{{ $buyerDetails->primary_mobile ?? '' }}" readonly>
									<small class="form-note">Contact number cannot be changed here</small>
								</div>
							</div>
						</div>
					</div>
					@endif
				@endif

				@if($user->role == 'facilitator')
					@php
						$facilitatorDetails = DB::table('facilitators')->where('user_id', $user->id)->first();
					@endphp

					@if($facilitatorDetails)
					<div class="form-section facilitator-section">
						<div class="section-header">
							<div class="section-icon">
								<i class="fas fa-user-tie"></i>
							</div>
							<h3>Facilitator Details</h3>
						</div>
						<div class="form-fields">
							<div class="form-group">
								<label class="form-label">
									<i class="fas fa-id-card"></i> NIC Number
								</label>
								<input type="text" class="form-input" value="{{ $facilitatorDetails->nic_no }}" readonly>
							</div>

							<div class="form-row">
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-map-pin"></i> Assigned Division
									</label>
									<input type="text" class="form-input" value="{{ $facilitatorDetails->assigned_division }}" readonly>
								</div>
								<div class="form-group">
									<label class="form-label">
										<i class="fas fa-phone"></i> Contact Number
									</label>
									<input type="text" class="form-input" value="{{ $facilitatorDetails->primary_mobile ?? '' }}" readonly>
								</div>
							</div>
						</div>
					</div>
					@endif
				@endif
			</div>

			<div class="form-actions">
				<button type="button" class="btn-secondary btn-cancel">
					<i class="fas fa-times"></i> Cancel
				</button>
				<button type="submit" class="btn-primary btn-save">
					<i class="fas fa-save"></i> Save Changes
				</button>
			</div>
		</form>
	</div>
</div>

<div class="otp-modal-overlay" id="otpModal">
	<div class="otp-modal-dialog">
		<div class="otp-modal-content">
			<div class="otp-modal-header">
				<div class="otp-modal-icon">
					<i class="fas fa-shield-alt"></i>
				</div>
				<div class="otp-modal-title">
					<h3>OTP Verification Required</h3>
					<p>Enter the OTP sent to user's mobile number</p>
				</div>
				<button class="otp-close-btn" id="closeOtpModal">
					<i class="fas fa-times"></i>
				</button>
			</div>
			<div class="otp-modal-body">
				<div class="otp-inputs">
					<input type="text" maxlength="1" class="otp-input" data-index="1">
					<input type="text" maxlength="1" class="otp-input" data-index="2">
					<input type="text" maxlength="1" class="otp-input" data-index="3">
					<input type="text" maxlength="1" class="otp-input" data-index="4">
					<input type="text" maxlength="1" class="otp-input" data-index="5">
					<input type="text" maxlength="1" class="otp-input" data-index="6">
				</div>

				<div class="otp-timer">
					<i class="fas fa-clock"></i>
					<span>OTP expires in: <strong id="otpTimer">05:00</strong></span>
				</div>

				<div class="otp-actions">
					<button class="btn-otp-secondary" id="resendOtpBtn">
						<i class="fas fa-redo"></i> Resend OTP
					</button>
					<button class="btn-otp-primary" id="verifyOtpBtn">
						<i class="fas fa-check"></i> Verify & Save
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
	let otpTimer = null;
	let timeLeft = 300;
	let otpVerified = false;
	let formDataToSubmit = null;

	function showAlert(icon, title, text) {
		Swal.fire({
			icon: icon,
			title: title,
			text: text,
			confirmButtonColor: '#10B981',
			confirmButtonText: 'OK',
			timer: 3000,
			timerProgressBar: true,
			showClass: {
				popup: 'animate__animated animate__fadeInDown'
			},
			hideClass: {
				popup: 'animate__animated animate__fadeOutUp'
			}
		});
	}

	function showSuccess(message, title = 'Success') {
		Swal.fire({
			icon: 'success',
			title: title,
			text: message,
			toast: true,
			position: 'top-end',
			showConfirmButton: false,
			timer: 3000,
			timerProgressBar: true,
			background: 'var(--card-bg)',
			color: 'var(--text-color)'
		});
	}

	function showError(message, title = 'Error') {
		Swal.fire({
			icon: 'error',
			title: title,
			text: message,
			toast: true,
			position: 'top-end',
			showConfirmButton: false,
			timer: 4000,
			timerProgressBar: true,
			background: 'var(--card-bg)',
			color: 'var(--text-color)'
		});
	}

	function startOtpTimer() {
		clearInterval(otpTimer);
		timeLeft = 300;

		otpTimer = setInterval(function() {
			timeLeft--;
			let minutes = Math.floor(timeLeft / 60);
			let seconds = timeLeft % 60;

			$('#otpTimer').text(`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);

			if (timeLeft <= 0) {
				clearInterval(otpTimer);
				showError('OTP has expired. Please request a new OTP');
			}
		}, 1000);
	}

	$('.btn-cancel').click(function() {
		Swal.fire({
			title: 'Discard Changes?',
			text: 'All unsaved changes will be lost',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#ef4444',
			cancelButtonColor: '#6b7280',
			confirmButtonText: 'Discard',
			cancelButtonText: 'Continue Editing',
			background: 'var(--card-bg)',
			color: 'var(--text-color)'
		}).then((result) => {
			if (result.isConfirmed) {
				window.location.href = '{{ route("admin.users.index") }}';
			}
		});
	});

	$('#editUserForm').submit(function(e) {
		e.preventDefault();

		const userRole = '{{ $user->role }}';
		const userId = {{ $user->id }};
		const isPaymentChanged = checkPaymentChanges();

		formDataToSubmit = new FormData(this);

		if ((userRole === 'farmer' || userRole === 'lead_farmer') && isPaymentChanged && !otpVerified) {
			Swal.fire({
				title: 'OTP Verification Required',
				html: `Changes to payment details require OTP verification.<br><br>
					  <small class="text-muted">An OTP will be sent to the user's registered mobile number</small>`,
				icon: 'info',
				showCancelButton: true,
				confirmButtonColor: '#10B981',
				cancelButtonColor: '#6b7280',
				confirmButtonText: 'Send OTP',
				cancelButtonText: 'Cancel',
				background: 'var(--card-bg)',
				color: 'var(--text-color)'
			}).then((result) => {
				if (result.isConfirmed) {
					sendOtpAndShowModal();
				}
			});
		} else {
			submitForm();
		}
	});

	function checkPaymentChanges() {
		const originalData = {
			preferred_payment: '{{ $userDetails->preferred_payment ?? "" }}',
			account_number: '{{ $userDetails->account_number ?? "" }}',
			account_holder_name: '{{ $userDetails->account_holder_name ?? "" }}',
			bank_name: '{{ $userDetails->bank_name ?? "" }}',
			bank_branch: '{{ $userDetails->bank_branch ?? "" }}',
			ezcash_mobile: '{{ $userDetails->ezcash_mobile ?? "" }}',
			mcash_mobile: '{{ $userDetails->mcash_mobile ?? "" }}'
		};

		const currentData = {
			preferred_payment: $('select[name="preferred_payment"]').val(),
			account_number: $('input[name="account_number"]').val(),
			account_holder_name: $('input[name="account_holder_name"]').val(),
			bank_name: $('input[name="bank_name"]').val(),
			bank_branch: $('input[name="bank_branch"]').val(),
			ezcash_mobile: $('input[name="ezcash_mobile"]').val(),
			mcash_mobile: $('input[name="mcash_mobile"]').val()
		};

		for (const key in originalData) {
			if (originalData[key] !== currentData[key]) {
				return true;
			}
		}

		return false;
	}

	function sendOtpAndShowModal() {
		const userId = {{ $user->id }};

		Swal.fire({
			title: 'Sending OTP...',
			text: 'Please wait',
			allowOutsideClick: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});

		$.ajax({
			url: '{{ route("admin.users.sendOtp") }}',
			method: 'POST',
			data: {
				_token: '{{ csrf_token() }}',
				user_id: userId,
				action: 'edit_payment'
			},
			success: function(response) {
				Swal.close();
				$('#otpModal').fadeIn();
				$('.otp-input').val('');
				startOtpTimer();
				showSuccess('OTP sent successfully to user');
			},
			error: function(xhr) {
				Swal.close();
				showError(xhr.responseJSON?.message || 'Failed to send OTP. Please try again');
			}
		});
	}

	function submitForm() {
		Swal.fire({
			title: 'Saving Changes...',
			text: 'Please wait while we update the user',
			allowOutsideClick: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});

		$.ajax({
			url: $('#editUserForm').attr('action'),
			method: 'POST',
			data: formDataToSubmit,
			processData: false,
			contentType: false,
			headers: {
				'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
			success: function(response) {
				Swal.fire({
					icon: 'success',
					title: 'Updated Successfully!',
					html: `User details have been updated.<br><br>
						  <small class="text-muted">The user has been notified of these changes</small>`,
					confirmButtonColor: '#10B981',
					showConfirmButton: true,
					allowOutsideClick: false,
					background: 'var(--card-bg)',
					color: 'var(--text-color)'
				}).then((result) => {
					window.location.href = '{{ route("admin.users.index") }}';
				});
			},
			error: function(xhr) {
				Swal.fire({
					icon: 'error',
					title: 'Update Failed',
					text: xhr.responseJSON?.message || 'Failed to update user details',
					confirmButtonColor: '#10B981',
					background: 'var(--card-bg)',
					color: 'var(--text-color)'
				});
			}
		});
	}

	$('.otp-input').on('input', function() {
		const index = parseInt($(this).data('index'));
		const value = $(this).val();

		if (value.length === 1 && index < 6) {
			$(`.otp-input[data-index="${index + 1}"]`).focus();
		}
	});

	$('.otp-input').on('keydown', function(e) {
		if (e.key === 'Backspace' && $(this).val() === '') {
			const index = parseInt($(this).data('index'));
			if (index > 1) {
				$(`.otp-input[data-index="${index - 1}"]`).focus();
			}
		}
	});

	$('#verifyOtpBtn').click(function() {
		const otp = $('.otp-input').map(function() {
			return $(this).val();
		}).get().join('');

		if (otp.length !== 6) {
			showError('Please enter the complete 6-digit OTP');
			return;
		}

		const userId = {{ $user->id }};

		Swal.fire({
			title: 'Verifying OTP...',
			allowOutsideClick: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});

		$.ajax({
			url: '{{ route("admin.users.verifyOtp") }}',
			method: 'POST',
			data: {
				_token: '{{ csrf_token() }}',
				user_id: userId,
				otp: otp,
				action: 'edit_payment'
			},
			success: function(response) {
				Swal.close();
				clearInterval(otpTimer);
				otpVerified = true;
				$('#otpModal').fadeOut();
				showSuccess('OTP verified successfully');

				setTimeout(() => {
					submitForm();
				}, 1500);
			},
			error: function(xhr) {
				Swal.close();
				showError(xhr.responseJSON?.message || 'Invalid OTP. Please try again');
			}
		});
	});

	$('#resendOtpBtn').click(function() {
		const userId = {{ $user->id }};

		Swal.fire({
			title: 'Resending OTP...',
			text: 'Please wait',
			allowOutsideClick: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});

		$.ajax({
			url: '{{ route("admin.users.resendOtp") }}',
			method: 'POST',
			data: {
				_token: '{{ csrf_token() }}',
				user_id: userId
			},
			success: function(response) {
				Swal.close();
				showSuccess('New OTP has been sent to the user');
				startOtpTimer();
			},
			error: function(xhr) {
				Swal.close();
				showError('Failed to resend OTP');
			}
		});
	});

	$('#closeOtpModal').click(function() {
		$('#otpModal').fadeOut();
		clearInterval(otpTimer);
	});

	$(window).click(function(e) {
		if ($(e.target).hasClass('otp-modal-overlay')) {
			$('#otpModal').fadeOut();
			clearInterval(otpTimer);
		}
	});

	$('.form-input, .form-select').on('focus', function() {
		$(this).closest('.form-group').addClass('focused');
	});

	$('.form-input, .form-select').on('blur', function() {
		$(this).closest('.form-group').removeClass('focused');
	});

	$('.form-select').change(function() {
		$(this).addClass('changed');
		setTimeout(() => {
			$(this).removeClass('changed');
		}, 1000);
	});

	$('.form-input').on('input', function() {
		$(this).addClass('typing');
	});

	$('.form-input').on('blur', function() {
		$(this).removeClass('typing');
		if ($(this).val() !== $(this).data('original')) {
			$(this).addClass('modified');
		} else {
			$(this).removeClass('modified');
		}
	});

	$('.form-input').each(function() {
		$(this).data('original', $(this).val());
	});
});
</script>
@endsection
