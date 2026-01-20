@extends('buyer.layouts.buyer_master')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/buyer/profile.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
<div class="profile-container">
	<div class="profile-header">
		<div class="profile-avatar">
			<img src="{{ Auth::user()->profile_photo ? asset('uploads/profile_pictures/' . Auth::user()->profile_photo) : asset('assets/images/default-avatar.png') }}"
				 alt="Profile Photo" class="avatar-img" id="profileAvatar">
			<div class="avatar-overlay">
				<a href="{{ route('buyer.profile.photo') }}" class="btn-change-photo">
					<i class="fa-solid fa-camera"></i>
				</a>
			</div>
		</div>
		<div class="profile-info">
			<h2>{{ $buyer->name ?? Auth::user()->username }}</h2>
			<p class="mb-2">
				<i class="fa-solid fa-envelope me-2"></i> {{ Auth::user()->email }}
			</p>
			<p class="mb-2">
				<i class="fa-solid fa-phone me-2"></i> {{ $buyer->primary_mobile ?? 'Not set' }}
			</p>
			@if($buyer && $buyer->business_name)
				<span class="badge">{{ ucfirst($buyer->business_type) }} Account</span>
			@endif
		</div>
	</div>

	<ul class="nav nav-tabs" id="profileTab" role="tablist">
		<li class="nav-item" role="presentation">
			<button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button">Personal Details</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" id="business-tab" data-bs-toggle="tab" data-bs-target="#business" type="button">Business Details</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button">Security</button>
		</li>
	</ul>

	<div class="tab-content" id="profileTabContent">
		<div class="tab-pane fade show active" id="personal" role="tabpanel">
			<form action="{{ route('buyer.profile.update') }}" method="POST" id="personalForm">
				@csrf
				@method('PUT')

				<div class="row">
					<div class="col-md-6 mb-3">
						<label for="name" class="form-label">Full Name *</label>
						<input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $buyer->name ?? Auth::user()->username) }}" required>
						@error('name')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>
					<div class="col-md-6 mb-3">
						<label for="email" class="form-label">Email Address *</label>
						<input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
						@error('email')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>
				</div>

				<div class="row">
					<div class="col-md-6 mb-3">
						<label for="primary_mobile" class="form-label">Mobile Number *</label>
						<input type="tel" class="form-control @error('primary_mobile') is-invalid @enderror" id="primary_mobile" name="primary_mobile" value="{{ old('primary_mobile', $buyer->primary_mobile ?? '') }}" required>
						@error('primary_mobile')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>
					<div class="col-md-6 mb-3">
						<label for="whatsapp_number" class="form-label">WhatsApp Number</label>
						<input type="tel" class="form-control @error('whatsapp_number') is-invalid @enderror" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', $buyer->whatsapp_number ?? '') }}">
						@error('whatsapp_number')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>
				</div>

				<div class="mb-3">
					<label for="residential_address" class="form-label">Residential Address *</label>
					<textarea class="form-control @error('residential_address') is-invalid @enderror" id="residential_address" name="residential_address" rows="3" required>{{ old('residential_address', $buyer->residential_address ?? '') }}</textarea>
					@error('residential_address')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>

				<button type="submit" class="btn btn-success">
					<i class="fa-solid fa-save me-2"></i> Update Personal Details
				</button>
			</form>
		</div>

		<div class="tab-pane fade" id="business" role="tabpanel">
			<form action="{{ route('buyer.business.update') }}" method="POST" id="businessForm">
				@csrf
				@method('PUT')

				<div class="row">
					<div class="col-md-6 mb-3">
						<label for="business_name" class="form-label">Business Name</label>
						<input type="text" class="form-control @error('business_name') is-invalid @enderror" id="business_name" name="business_name" value="{{ old('business_name', $buyer->business_name ?? '') }}">
						@error('business_name')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>
					<div class="col-md-6 mb-3">
						<label for="business_type" class="form-label">Business Type</label>
						<select class="form-select @error('business_type') is-invalid @enderror" id="business_type" name="business_type">
							<option value="">Select Type</option>
							<option value="individual" {{ ($buyer->business_type ?? '') == 'individual' ? 'selected' : '' }}>Individual</option>
							<option value="restaurant" {{ ($buyer->business_type ?? '') == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
							<option value="hotel" {{ ($buyer->business_type ?? '') == 'hotel' ? 'selected' : '' }}>Hotel</option>
							<option value="retailer" {{ ($buyer->business_type ?? '') == 'retailer' ? 'selected' : '' }}>Retailer</option>
							<option value="wholesaler" {{ ($buyer->business_type ?? '') == 'wholesaler' ? 'selected' : '' }}>Wholesaler</option>
						</select>
						@error('business_type')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>
				</div>

				<div class="mb-3">
					<label for="business_address" class="form-label">Business Address</label>
					<textarea class="form-control @error('business_address') is-invalid @enderror" id="business_address" name="business_address" rows="3">{{ old('business_address', $buyer->business_address ?? '') }}</textarea>
					@error('business_address')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>

				<div class="mb-3">
					<label for="tax_id" class="form-label">Tax ID / VAT Number</label>
					<input type="text" class="form-control @error('tax_id') is-invalid @enderror" id="tax_id" name="tax_id" value="{{ old('tax_id', $buyer->tax_id ?? '') }}">
					@error('tax_id')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>

				<button type="submit" class="btn btn-success">
					<i class="fa-solid fa-building me-2"></i> Update Business Details
				</button>
			</form>
		</div>

		<div class="tab-pane fade" id="security" role="tabpanel">
			<form action="{{ route('buyer.password.update') }}" method="POST" id="passwordForm">
				@csrf
				@method('PUT')

				<div class="mb-3">
					<label for="current_password" class="form-label">Current Password *</label>
					<div class="password-container">
						<input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
						<button type="button" class="password-toggle" onclick="togglePasswordVisibility('current_password', 'current_password_icon')">
							<i class="fa-regular fa-eye" id="current_password_icon"></i>
						</button>
					</div>
					@error('current_password')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>

				<div class="row">
					<div class="col-md-6 mb-3">
						<label for="new_password" class="form-label">New Password *</label>
						<div class="password-container">
							<input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required oninput="checkPasswordStrength(this.value)">
							<button type="button" class="password-toggle" onclick="togglePasswordVisibility('new_password', 'new_password_icon')">
								<i class="fa-regular fa-eye" id="new_password_icon"></i>
							</button>
						</div>
						@error('new_password')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
						<div class="password-strength">
							<div class="password-strength-text">Password strength: <span id="strength-text">None</span></div>
							<div class="strength-bar">
								<div class="strength-fill" id="strength-bar"></div>
							</div>
						</div>
					</div>
					<div class="col-md-6 mb-3">
						<label for="new_password_confirmation" class="form-label">Confirm New Password *</label>
						<div class="password-container">
							<input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
							<button type="button" class="password-toggle" onclick="togglePasswordVisibility('new_password_confirmation', 'confirm_password_icon')">
								<i class="fa-regular fa-eye" id="confirm_password_icon"></i>
							</button>
						</div>
					</div>
				</div>

				<div class="alert alert-info mb-4">
					<i class="fa-solid fa-info-circle me-2"></i>
					Password must be at least 8 characters long and contain uppercase, lowercase, and numbers.
				</div>

				<button type="submit" class="btn btn-success">
					<i class="fa-solid fa-key me-2"></i> Change Password
				</button>
			</form>
		</div>
	</div>
</div>

<div class="loading-overlay" id="loadingOverlay">
	<div class="spinner-border text-success" role="status">
		<span class="visually-hidden">Loading...</span>
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
				title: 'Success',
				text: '{{ session('success') }}',
				timer: 3000,
				timerProgressBar: true,
				showConfirmButton: false,
				position: 'top-end',
				toast: true
			});
		@endif

		@if(session('error'))
			Swal.fire({
				icon: 'error',
				title: 'Error',
				text: '{{ session('error') }}',
				timer: 4000,
				timerProgressBar: true,
				showConfirmButton: false,
				position: 'top-end',
				toast: true
			});
		@endif

		@if($errors->any())
			let errorMessages = '';
			@foreach($errors->all() as $error)
				errorMessages += '{{ $error }}\n';
			@endforeach

			Swal.fire({
				icon: 'error',
				title: 'Validation Error',
				html: errorMessages.replace(/\n/g, '<br>'),
				showConfirmButton: true
			});
		@endif

		const forms = ['personalForm', 'businessForm', 'passwordForm'];
		forms.forEach(formId => {
			const form = document.getElementById(formId);
			if (form) {
				form.addEventListener('submit', function(e) {
					e.preventDefault();
					document.getElementById('loadingOverlay').style.display = 'flex';

					Swal.fire({
						title: 'Updating...',
						text: 'Please wait while we update your information.',
						allowOutsideClick: false,
						didOpen: () => {
							Swal.showLoading();
						}
					});

					setTimeout(() => {
						this.submit();
					}, 500);
				});
			}
		});

		const avatarImg = document.getElementById('profileAvatar');
		if (avatarImg) {
			avatarImg.addEventListener('error', function() {
				this.src = '{{ asset('assets/images/default-avatar.png') }}';
			});
		}

		const tabLinks = document.querySelectorAll('#profileTab .nav-link');
		tabLinks.forEach(link => {
			link.addEventListener('click', function() {
				tabLinks.forEach(l => l.classList.remove('active'));
				this.classList.add('active');
			});
		});

		window.addEventListener('resize', function() {
			adjustLayout();
		});

		function adjustLayout() {
			const container = document.querySelector('.profile-container');
			if (window.innerWidth <= 480) {
				container.classList.add('mobile-view');
			} else {
				container.classList.remove('mobile-view');
			}
		}

		adjustLayout();
	});

	function togglePasswordVisibility(fieldId, iconId) {
		const passwordField = document.getElementById(fieldId);
		const toggleIcon = document.getElementById(iconId);

		if (passwordField.type === 'password') {
			passwordField.type = 'text';
			toggleIcon.classList.remove('fa-eye');
			toggleIcon.classList.add('fa-eye-slash');
		} else {
			passwordField.type = 'password';
			toggleIcon.classList.remove('fa-eye-slash');
			toggleIcon.classList.add('fa-eye');
		}
	}

	function checkPasswordStrength(password) {
		let strength = 0;
		const strengthText = document.getElementById('strength-text');
		const strengthBar = document.getElementById('strength-bar');

		if (password.length >= 8) strength++;
		if (/[A-Z]/.test(password)) strength++;
		if (/[a-z]/.test(password)) strength++;
		if (/[0-9]/.test(password)) strength++;
		if (/[^A-Za-z0-9]/.test(password)) strength++;

		let strengthClass = 'strength-weak';
		let text = 'Weak';
		let width = '20%';

		if (strength >= 4) {
			strengthClass = 'strength-strong';
			text = 'Strong';
			width = '100%';
		} else if (strength >= 3) {
			strengthClass = 'strength-medium';
			text = 'Medium';
			width = '60%';
		}

		strengthText.textContent = text;
		strengthBar.className = 'strength-fill ' + strengthClass;
		strengthBar.style.width = width;
	}
</script>
@endsection
