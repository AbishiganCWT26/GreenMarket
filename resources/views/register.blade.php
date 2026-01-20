@extends('public_master')

@section('title', 'Buyer Registration - GreenMarket')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="{{ asset('css/buyer-register.css') }}">
@endsection

@section('content')
<div class="registration-container">
	<div class="registration-wrapper">
		<div class="registration-card">
			<div class="form-section">
				<div class="form-header">
					<h2>Create Your Account</h2>
					<p>Join thousands of buyers enjoying fresh produce</p>
				</div>

				<div class="progress-steps">
					<div class="step active" data-step="1">
						<div class="step-number">1</div>
						<div class="step-label">Personal</div>
					</div>
					<div class="step" data-step="2">
						<div class="step-number">2</div>
						<div class="step-label">Business</div>
					</div>
					<div class="step" data-step="3">
						<div class="step-number">3</div>
						<div class="step-label">Password</div>
					</div>
				</div>

				<form method="POST" action="{{ route('buyer.register.submit') }}" id="registrationForm">
					@csrf

					<div class="form-step active" id="step-1">
						<h5 class="section-title">
							<i class="fas fa-user-circle"></i>
							Personal Information
						</h5>

						<div class="row g-3">
							<div class="col-md-6">
								<label for="name" class="form-label required-field">Full Name</label>
								<div class="input-with-icon">
									<i class="fas fa-user"></i>
									<input type="text" class="form-control @error('name') is-invalid @enderror"
										   id="name" name="name" value="{{ old('name') }}"
										   placeholder="Enter your full name" required>
								</div>
								@error('name')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
							</div>

							<div class="col-md-6">
								<label for="nic_no" class="form-label required-field">NIC Number</label>
								<div class="nic-input-container">
									<div class="input-with-icon">
										<i class="fas fa-id-card"></i>
										<input type="text" class="form-control @error('nic_no') is-invalid @enderror"
											   id="nic_no" name="nic_no" value="{{ old('nic_no') }}"
											   placeholder="Enter NIC (e.g., 123456789V or 200123456789)"
											   pattern="^([0-9]{9}[xXvV]|[0-9]{12})$"
											   title="Enter valid NIC number (9 digits with letter or 12 digits)"
											   required>
									</div>
									<div class="nic-format">
										<i class="fas fa-info-circle"></i>
										Format: 123456789V (old) or 200123456789 (new)
									</div>
									<div class="nic-status" id="nicStatus"></div>
								</div>
								@error('nic_no')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
							</div>

							<div class="col-md-6">
								<label for="email" class="form-label required-field">Email Address</label>
								<div class="input-with-icon">
									<i class="fas fa-envelope"></i>
									<input type="email" class="form-control @error('email') is-invalid @enderror"
										   id="email" name="email" value="{{ old('email') }}"
										   placeholder="Enter your email" required>
								</div>
								@error('email')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
							</div>

							<div class="col-md-6">
								<label for="username" class="form-label required-field">Username</label>
								<div class="input-with-icon">
									<i class="fas fa-at"></i>
									<input type="text" class="form-control @error('username') is-invalid @enderror"
										   id="username" name="username" value="{{ old('username') }}"
										   placeholder="Choose a username" required>
								</div>
								@error('username')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
							</div>

							<div class="col-md-6">
								<label for="primary_mobile" class="form-label required-field">Mobile Number</label>
								<div class="input-with-icon">
									<i class="fas fa-phone"></i>
									<input type="tel" class="form-control @error('primary_mobile') is-invalid @enderror"
										   id="primary_mobile" name="primary_mobile" value="{{ old('primary_mobile') }}"
										   placeholder="07X XXX XXXX" required>
								</div>
								@error('primary_mobile')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
							</div>
						</div>

						<div class="navigation-buttons">
							<button type="button" class="btn btn-prev" disabled>
								<i class="fas fa-arrow-left me-2"></i> Previous
							</button>
							<button type="button" class="btn btn-next" data-next="2">
								Next <i class="fas fa-arrow-right ms-2"></i>
							</button>
						</div>
					</div>

					<div class="form-step" id="step-2">
						<h5 class="section-title">
							<i class="fas fa-briefcase"></i>
							Business Information (Optional)
						</h5>

						<div class="row g-3">
							<div class="col-md-6">
								<label for="business_name" class="form-label">Business Name</label>
								<div class="input-with-icon">
									<i class="fas fa-building"></i>
									<input type="text" class="form-control @error('business_name') is-invalid @enderror"
										   id="business_name" name="business_name" value="{{ old('business_name') }}"
										   placeholder="Your business name">
								</div>
								@error('business_name')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
							</div>

							<div class="col-md-6">
								<label for="business_type" class="form-label">Business Type</label>
								<select class="form-select @error('business_type') is-invalid @enderror"
										id="business_type" name="business_type">
									<option value="">Select Type</option>
									<option value="individual" {{ old('business_type') == 'individual' ? 'selected' : '' }}>Individual</option>
									<option value="restaurant" {{ old('business_type') == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
									<option value="hotel" {{ old('business_type') == 'hotel' ? 'selected' : '' }}>Hotel</option>
									<option value="retailer" {{ old('business_type') == 'retailer' ? 'selected' : '' }}>Retailer</option>
									<option value="wholesaler" {{ old('business_type') == 'wholesaler' ? 'selected' : '' }}>Wholesaler</option>
								</select>
								@error('business_type')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
							</div>
						</div>

						<h5 class="section-title mt-4">
							<i class="fas fa-map-marker-alt"></i>
							Address Information
						</h5>

						<div class="mb-3">
							<label for="residential_address" class="form-label required-field">Residential Address</label>
							<div class="input-with-icon">
								<i class="fas fa-home"></i>
								<textarea class="form-control @error('residential_address') is-invalid @enderror"
										  id="residential_address" name="residential_address"
										  rows="3" placeholder="Enter your complete address" required>{{ old('residential_address') }}</textarea>
							</div>
							@error('residential_address')
								<div class="invalid-feedback d-block">{{ $message }}</div>
							@enderror
						</div>

						<div class="mb-3">
							<label for="whatsapp_number" class="form-label">WhatsApp Number</label>
							<div class="input-with-icon">
								<i class="fab fa-whatsapp"></i>
								<input type="tel" class="form-control @error('whatsapp_number') is-invalid @enderror"
									   id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number') }}"
									   placeholder="Optional WhatsApp number">
							</div>
							@error('whatsapp_number')
								<div class="invalid-feedback d-block">{{ $message }}</div>
							@enderror
						</div>

						<div class="navigation-buttons">
							<button type="button" class="btn btn-prev" data-prev="1">
								<i class="fas fa-arrow-left me-2"></i> Previous
							</button>
							<button type="button" class="btn btn-next" data-next="3">
								Next <i class="fas fa-arrow-right ms-2"></i>
							</button>
						</div>
					</div>

					<div class="form-step" id="step-3">
						<h5 class="section-title">
							<i class="fas fa-key"></i>
							Account Security
						</h5>

						<div class="row g-3">
							<div class="col-md-6">
								<label for="password" class="form-label required-field">Password</label>
								<div class="password-container">
									<div class="input-with-icon">
										<i class="fas fa-lock"></i>
										<input type="password" class="form-control @error('password') is-invalid @enderror"
											   id="password" name="password"
											   placeholder="Create a strong password" required>
									</div>
									<i class="fa-regular fa-eye password-toggle" id="password-toggle-icon" onclick="togglePasswordVisibility()"></i>
								</div>
								<div class="password-strength mt-3">
									<div class="strength-bar" id="strengthBar">
										<div class="strength-fill"></div>
									</div>
									<span class="strength-text" id="strengthText">Weak</span>
								</div>
								@error('password')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
								<small class="form-text text-muted">Minimum 8 characters with letters and numbers</small>
							</div>

							<div class="col-md-6">
								<label for="password_confirmation" class="form-label required-field">Confirm Password</label>
								<div class="password-container">
									<div class="input-with-icon">
										<i class="fas fa-lock"></i>
										<input type="password" class="form-control"
											   id="password_confirmation" name="password_confirmation"
											   placeholder="Confirm your password" required>
									</div>
									<i class="fa-regular fa-eye password-toggle" id="confirm-password-toggle-icon" onclick="toggleConfirmPasswordVisibility()"></i>
								</div>
								<div id="passwordMatch" class="mt-3">
									<small class="form-text text-success d-none">
										<i class="fas fa-check-circle"></i> Passwords match
									</small>
									<small class="form-text text-danger d-none">
										<i class="fas fa-times-circle"></i> Passwords don't match
									</small>
								</div>
							</div>
						</div>

						<div class="mt-4">
							<div class="form-check">
								<input class="form-check-input @error('terms') is-invalid @enderror"
									   type="checkbox" id="terms" name="terms" required>
								<label class="form-check-label" for="terms">
									I agree to the <a href="#" class="text-success">Terms & Conditions</a> and
									<a href="#" class="text-success">Privacy Policy</a> *
								</label>
								@error('terms')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
							</div>
						</div>

						<div class="navigation-buttons">
							<button type="button" class="btn btn-prev" data-prev="2">
								<i class="fas fa-arrow-left me-2"></i> Previous
							</button>
							<button type="submit" class="btn btn-register" id="submitBtn">
								<i class="fas fa-user-plus me-2"></i> Create Account
							</button>
						</div>
					</div>
				</form>

				<div class="login-link">
					Already have an account?
					<a href="{{ route('login') }}">Login here</a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
	function togglePasswordVisibility() {
		const passwordField = document.getElementById('password');
		const toggleIcon = document.getElementById('password-toggle-icon');
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

	function toggleConfirmPasswordVisibility() {
		const confirmPasswordField = document.getElementById('password_confirmation');
		const toggleIcon = document.getElementById('confirm-password-toggle-icon');
		if (confirmPasswordField.type === 'password') {
			confirmPasswordField.type = 'text';
			toggleIcon.classList.remove('fa-eye');
			toggleIcon.classList.add('fa-eye-slash');
		} else {
			confirmPasswordField.type = 'password';
			toggleIcon.classList.remove('fa-eye-slash');
			toggleIcon.classList.add('fa-eye');
		}
	}

	function validateNIC(nic) {
		if (!nic) return false;
		nic = nic.trim().toUpperCase();
		const oldNicPattern = /^[0-9]{9}[VX]$/;
		const newNicPattern = /^[0-9]{12}$/;
		if (oldNicPattern.test(nic)) {
			const year = parseInt(nic.substr(0, 2));
			const days = parseInt(nic.substr(2, 3));
			if (days > 500) {
				return days <= 866;
			}
			return days > 0 && days <= 366;
		}
		if (newNicPattern.test(nic)) {
			const year = parseInt(nic.substr(0, 4));
			const days = parseInt(nic.substr(4, 3));
			if (days > 500) {
				return days <= 866;
			}
			return year >= 1900 && year <= 2100 && days > 0 && days <= 366;
		}
		return false;
	}

	function formatNIC(nic) {
		if (!nic) return '';
		nic = nic.trim().toUpperCase();
		if (nic.length === 10 && /^[0-9]{9}[VX]$/.test(nic)) {
			return nic;
		}
		if (nic.length === 12 && /^[0-9]{12}$/.test(nic)) {
			return nic;
		}
		return nic;
	}

	document.addEventListener('DOMContentLoaded', function() {
		const form = document.getElementById('registrationForm');
		const steps = document.querySelectorAll('.form-step');
		const stepButtons = document.querySelectorAll('.step');
		const nextButtons = document.querySelectorAll('.btn-next');
		const prevButtons = document.querySelectorAll('.btn-prev');
		const password = document.getElementById('password');
		const confirmPassword = document.getElementById('password_confirmation');
		const strengthBar = document.getElementById('strengthBar');
		const strengthText = document.getElementById('strengthText');
		const termsCheckbox = document.getElementById('terms');
		const submitBtn = document.getElementById('submitBtn');
		const nicInput = document.getElementById('nic_no');
		const nicStatus = document.getElementById('nicStatus');

		let currentStep = 1;

		function updateStep(step) {
			steps.forEach(s => s.classList.remove('active'));
			stepButtons.forEach(b => {
				b.classList.remove('active', 'completed');
				if (parseInt(b.dataset.step) < step) {
					b.classList.add('completed');
				} else if (parseInt(b.dataset.step) === step) {
					b.classList.add('active');
				}
			});
			document.getElementById(`step-${step}`).classList.add('active');
			currentStep = step;
			updateNavigationButtons();
		}

		function updateNavigationButtons() {
			prevButtons.forEach(btn => {
				const prevStep = parseInt(btn.dataset.prev);
				btn.disabled = !prevStep || currentStep === 1;
			});
			nextButtons.forEach(btn => {
				const nextStep = parseInt(btn.dataset.next);
				const currentStepEl = document.getElementById(`step-${currentStep}`);
				const requiredFields = currentStepEl.querySelectorAll('[required]');
				let allValid = true;
				requiredFields.forEach(field => {
					if (!field.value.trim()) {
						allValid = false;
					}
					if (field.id === 'nic_no' && !validateNIC(field.value)) {
						allValid = false;
					}
				});
				btn.disabled = !nextStep || !allValid;
			});
			submitBtn.disabled = !validateAllSteps();
		}

		function validateAllSteps() {
			const requiredFields = form.querySelectorAll('[required]');
			for (let field of requiredFields) {
				if (!field.value.trim()) {
					return false;
				}
				if (field.id === 'nic_no' && !validateNIC(field.value)) {
					return false;
				}
				if (field.type === 'checkbox' && !field.checked) {
					return false;
				}
			}
			return validatePasswordStrength() && validatePasswordMatch();
		}

		function validatePasswordStrength() {
			const passwordValue = password.value;
			if (!passwordValue) return false;
			let strength = 0;
			if (passwordValue.length >= 8) strength++;
			if (/[A-Z]/.test(passwordValue)) strength++;
			if (/[0-9]/.test(passwordValue)) strength++;
			if (/[^A-Za-z0-9]/.test(passwordValue)) strength++;
			return strength >= 2;
		}

		function validatePasswordMatch() {
			return password.value === confirmPassword.value;
		}

		nextButtons.forEach(button => {
			button.addEventListener('click', function() {
				const nextStep = parseInt(this.dataset.next);
				if (nextStep && currentStep < nextStep) {
					updateStep(nextStep);
				}
			});
		});

		prevButtons.forEach(button => {
			button.addEventListener('click', function() {
				const prevStep = parseInt(this.dataset.prev);
				if (prevStep && currentStep > prevStep) {
					updateStep(prevStep);
				}
			});
		});

		nicInput.addEventListener('input', function() {
			const nicValue = this.value.trim().toUpperCase();
			this.value = nicValue;
			if (nicValue === '') {
				nicStatus.className = 'nic-status';
				nicStatus.textContent = '';
			} else if (validateNIC(nicValue)) {
				nicStatus.className = 'nic-status valid';
				nicStatus.innerHTML = '<i class="fas fa-check-circle"></i> Valid NIC format';
			} else {
				nicStatus.className = 'nic-status invalid';
				nicStatus.innerHTML = '<i class="fas fa-times-circle"></i> Invalid NIC format';
			}
			updateNavigationButtons();
		});

		nicInput.addEventListener('blur', function() {
			const nicValue = this.value.trim().toUpperCase();
			if (nicValue && validateNIC(nicValue)) {
				this.value = formatNIC(nicValue);
			}
		});

		password.addEventListener('input', function() {
			const passwordValue = this.value;
			let strength = 0;
			let strengthClass = 'strength-weak';
			let strengthMessage = 'Weak';
			if (passwordValue.length >= 8) strength++;
			if (/[A-Z]/.test(passwordValue)) strength++;
			if (/[0-9]/.test(passwordValue)) strength++;
			if (/[^A-Za-z0-9]/.test(passwordValue)) strength++;
			if (strength >= 4) {
				strengthClass = 'strength-strong';
				strengthMessage = 'Strong';
			} else if (strength >= 2) {
				strengthClass = 'strength-medium';
				strengthMessage = 'Medium';
			}
			strengthBar.className = 'strength-bar ' + strengthClass;
			strengthText.textContent = strengthMessage;
			updateNavigationButtons();
		});

		confirmPassword.addEventListener('input', function() {
			const match = password.value === this.value;
			const matchIndicator = document.getElementById('passwordMatch');
			const success = matchIndicator.querySelector('.text-success');
			const error = matchIndicator.querySelector('.text-danger');
			if (password.value && this.value) {
				if (match) {
					success.classList.remove('d-none');
					error.classList.add('d-none');
				} else {
					success.classList.add('d-none');
					error.classList.remove('d-none');
				}
			} else {
				success.classList.add('d-none');
				error.classList.add('d-none');
			}
			updateNavigationButtons();
		});

		form.querySelectorAll('input, select, textarea').forEach(field => {
			field.addEventListener('input', updateNavigationButtons);
			field.addEventListener('change', updateNavigationButtons);
		});

		termsCheckbox.addEventListener('change', updateNavigationButtons);

		form.addEventListener('submit', async function(e) {
			e.preventDefault();
			if (!validateAllSteps()) {
				Swal.fire({
					icon: 'error',
					title: 'Validation Error',
					text: 'Please fill all required fields correctly.',
					confirmButtonColor: '#10B981'
				});
				return;
			}
			if (!validateNIC(nicInput.value)) {
				Swal.fire({
					icon: 'error',
					title: 'Invalid NIC',
					text: 'Please enter a valid NIC number.',
					confirmButtonColor: '#10B981'
				});
				return;
			}
			const formData = new FormData(this);
			const submitBtn = document.getElementById('submitBtn');
			submitBtn.disabled = true;
			submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Creating Account...';
			try {
				const response = await fetch(this.action, {
					method: 'POST',
					body: formData,
					headers: {
						'Accept': 'application/json',
						'X-Requested-With': 'XMLHttpRequest',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
					}
				});
				const result = await response.json();
				if (response.ok && result.success) {
					Swal.fire({
						icon: 'success',
						title: 'Registration Successful!',
						html: 'Please check your email and SMS for login details.',
						confirmButtonText: 'Go to Login',
						confirmButtonColor: '#10B981',
						allowOutsideClick: false,
						allowEscapeKey: false
					}).then((result) => {
						if (result.isConfirmed) {
							window.location.href = result.redirect || '{{ route("login") }}';
						}
					});
				} else {
					let errorMessage = 'Registration failed. Please try again.';
					if (result.errors) {
						const firstError = Object.values(result.errors)[0];
						if (Array.isArray(firstError)) {
							errorMessage = firstError[0];
						} else {
							errorMessage = firstError;
						}
					} else if (result.message) {
						errorMessage = result.message;
					} else if (result.error) {
						errorMessage = result.error;
					}
					Swal.fire({
						icon: 'error',
						title: 'Registration Failed',
						html: errorMessage,
						confirmButtonColor: '#10B981'
					});
					submitBtn.disabled = false;
					submitBtn.innerHTML = '<i class="fas fa-user-plus me-2"></i> Create Account';
				}
			} catch (error) {
				console.error('Error:', error);
				Swal.fire({
					icon: 'error',
					title: 'Network Error',
					text: 'Please check your internet connection and try again.',
					confirmButtonColor: '#10B981'
				});
				submitBtn.disabled = false;
				submitBtn.innerHTML = '<i class="fas fa-user-plus me-2"></i> Create Account';
			}
		});

		updateStep(1);
		updateNavigationButtons();

		window.addEventListener('resize', function() {
			updateNavigationButtons();
		});

		document.querySelectorAll('.btn-register, .btn-next, .btn-prev').forEach(button => {
			button.addEventListener('mousedown', function(e) {
				this.style.transform = 'scale(0.98)';
			});
			button.addEventListener('mouseup', function(e) {
				this.style.transform = '';
			});
			button.addEventListener('mouseleave', function(e) {
				this.style.transform = '';
			});
		});
	});
</script>
@endsection
