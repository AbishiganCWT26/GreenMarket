@extends('public_master')

@section('title', 'Buyer Registration - GreenMarket')

@section('styles')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	
	<link rel="stylesheet" href="{{ asset('css/buyer-register.css') }}">
	<style>
		.swal2-image {
			margin: 0em auto 0em !important;
		}

		/* Base responsive styles for SweetAlert2 */
		.responsive-swal-popup {
		  border-radius: 20px !important;
		  padding: 1.2rem !important;
		}

		.swal-title-responsive {
		  font-size: clamp(18px, 5vw, 24px) !important;
		}

		.swal-text-responsive {
		  font-size: clamp(14px, 4vw, 18px) !important;
		}

		.swal-button-responsive {
		  font-size: clamp(12px, 3.5vw, 16px) !important;
		  padding: 0.6rem 1.2rem !important;
		}

		/* Media queries for width */
		@media (min-width: 2560px) {
		  .swal2-popup { width: 35% !important; max-width: 600px !important; }
		}
		@media (min-width: 1501px) and (max-width: 2559px) {
		  .swal2-popup { width: 40% !important; max-width: 550px !important; }
		}
		@media (min-width: 1400px) and (max-width: 1500px) {
		  .swal2-popup { width: 45% !important; max-width: 500px !important; }
		}
		@media (min-width: 1200px) and (max-width: 1399px) {
		  .swal2-popup { width: 50% !important; max-width: 480px !important; }
		}
		@media (min-width: 1001px) and (max-width: 1199px) {
		  .swal2-popup { width: 60% !important; max-width: 450px !important; }
		}
		@media (min-width: 993px) and (max-width: 1000px) {
		  .swal2-popup { width: 65% !important; max-width: 420px !important; }
		}
		@media (min-width: 992px) and (max-width: 999px) {
		  .swal2-popup { width: 70% !important; max-width: 400px !important; }
		}
		@media (min-width: 768px) and (max-width: 991px) {
		  .swal2-popup { width: 80% !important; max-width: 380px !important; }
		}
		@media (min-width: 576px) and (max-width: 767px) {
		  .swal2-popup { width: 90% !important; max-width: 350px !important; }
		}
		@media (min-width: 481px) and (max-width: 575px) {
		  .swal2-popup { width: 95% !important; max-width: 320px !important; }
		}
		@media (min-width: 380px) and (max-width: 480px) {
		  .swal2-popup { width: 98% !important; max-width: 280px !important; }
		}
		@media (max-width: 379px) {
		  .swal2-popup { width: 100% !important; margin: 0 10px !important; }
		  .responsive-swal-popup { padding: 0.8rem !important; }
		}
	</style>

	<script src="{{ asset('js/form-validation.js') }}"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
												title="Enter valid NIC number (9 digits with letter or 12 digits)" required>
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
									<div class="nic-status" id="usernameStatus"></div>
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
											placeholder="07XXXXXXXX" maxlength="10" required>
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
							<p class="text-muted small mb-3">If you do not have a business, leave 'Business Name' and 'Business Type' blank.</p>

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
										<option value="hotel" {{ old('business_type') == 'hotel' ? 'selected' : '' }}>Hotel
										</option>
										<option value="retailer" {{ old('business_type') == 'retailer' ? 'selected' : '' }}>
											Retailer</option>
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
								<label for="residential_address" class="form-label required-field">Residential
									Address</label>
								<div class="input-with-icon">
									<i class="fas fa-home"></i>
									<textarea class="form-control @error('residential_address') is-invalid @enderror"
										id="residential_address" name="residential_address" rows="4"
										placeholder="No.123,&#10;Main Street,&#10;Kandy Roady,&#10;Colombo."
										required>{{ old('residential_address') }}</textarea>
								</div>
								@error('residential_address')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
							</div>

							<div class="mb-3">
								<label for="google_map_link" class="form-label required-field">Google Map Link of the Residential Address</label>
								<div class="input-with-icon">
									<i class="fas fa-map-marked-alt"></i>
									<input type="url" class="form-control @error('google_map_link') is-invalid @enderror"
										id="google_map_link" name="google_map_link" value="{{ old('google_map_link') }}"
										placeholder="Enter Google Map link" required>
								</div>
								<small class="form-text text-muted">
									<i class="fas fa-info-circle"></i> Mention product will be delivery to the Residential Address of the google map link
								</small>
								@error('google_map_link')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
							</div>

							<div class="mb-3">
								<label for="district" class="form-label required-field">District</label>
								<div class="input-with-icon">
									<i class="fas fa-map-marker-alt"></i>
									<select class="form-select @error('district') is-invalid @enderror" id="district"
										name="district" required style="padding-left: 2.5rem;">
										<option value="" disabled selected>Select District</option>
									</select>
								</div>
								@error('district')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
							</div>

							<div class="mb-3">
								<label for="whatsapp_number" class="form-label">WhatsApp Number</label>
								<div class="input-with-icon">
									<i class="fab fa-whatsapp"></i>
									<input type="tel" class="form-control @error('whatsapp_number') is-invalid @enderror"
										id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number') }}"
										placeholder="Optional WhatsApp number" maxlength="10">
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
									<div class="password-container" style="position: relative;">
										<div class="input-with-icon">
											<i class="fas fa-lock"></i>
											<input type="password"
												class="form-control @error('password') is-invalid @enderror" id="password"
												name="password" placeholder="Create a strong password" required>
										</div>
										<i class="fa-regular fa-eye password-toggle" id="password-toggle-icon"
											style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"
											onclick="togglePasswordVisibility('password', 'password-toggle-icon')"></i>
									</div>
									<div class="password-strength mt-3">
										<div class="d-flex justify-content-between align-items-center mb-1">
											<small>Strength: <span id="strengthText">None</span></small>
										</div>
										<div class="strength-bar" id="strengthBar"
											style="height: 5px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
											<div class="strength-fill"
												style="width: 0%; height: 100%; transition: width 0.3s;"></div>
										</div>
									</div>
									<div class="requirements mt-3">
										<h6 class="mb-2" style="font-size: 0.9rem;">Requirements:</h6>
										<ul class="list-unstyled mb-0"
											style="font-size: 0.8rem; display: grid; grid-template-columns: 1fr 1fr; gap: 5px;">
											<li id="rule-length" class="text-danger"><i class="fas fa-times me-1"></i> 8+
												chars</li>
											<li id="rule-number" class="text-danger"><i class="fas fa-times me-1"></i>
												Number</li>
											<li id="rule-capital" class="text-danger"><i class="fas fa-times me-1"></i>
												Capital</li>
											<li id="rule-lowercase" class="text-danger"><i class="fas fa-times me-1"></i>
												Lowercase</li>
											<li id="rule-special" class="text-danger"><i class="fas fa-times me-1"></i>
												Special</li>
											<li id="rule-no-space" class="text-danger"><i class="fas fa-times me-1"></i> No
												spaces</li>
											<li id="rule-no-repeat" class="text-danger"><i class="fas fa-times me-1"></i> No
												repeat</li>
											<li id="rule-no-sequence" class="text-danger"><i class="fas fa-times me-1"></i>
												No sequence</li>
											<li id="rule-not-common" class="text-danger"><i class="fas fa-times me-1"></i>
												Not common</li>
											<li id="rule-no-links" class="text-danger"><i class="fas fa-times me-1"></i> No
												links</li>
											<li id="rule-no-personal" class="text-danger"><i class="fas fa-times me-1"></i>
												No Personal Info</li>
										</ul>
									</div>
									@error('password')
										<div class="invalid-feedback d-block">{{ $message }}</div>
									@enderror
								</div>

								<div class="col-md-6">
									<label for="password_confirmation" class="form-label required-field">Confirm
										Password</label>
									<div class="password-container" style="position: relative;">
										<div class="input-with-icon">
											<i class="fas fa-lock"></i>
											<input type="password" class="form-control" id="password_confirmation"
												name="password_confirmation" placeholder="Confirm your password" required>
										</div>
										<i class="fa-regular fa-eye password-toggle" id="confirm-password-toggle-icon"
											style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"
											onclick="togglePasswordVisibility('password_confirmation', 'confirm-password-toggle-icon')"></i>
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
									<input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox"
										id="terms" name="terms" required>
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
	<script src="{{ asset('js/gn-data.js') }}"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	
	<script>
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

		document.addEventListener('DOMContentLoaded', function () {
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
			const usernameInput = document.getElementById('username');
			const usernameStatus = document.getElementById('usernameStatus');

			const fullNameInput = document.getElementById('name');
			const emailInput = document.getElementById('email');
			const mobileInput = document.getElementById('primary_mobile');
			const whatsappInput = document.getElementById('whatsapp_number');
			const addressInput = document.getElementById('residential_address');
			const mapLinkInput = document.getElementById('google_map_link');

			const containsLink = (text) => /http:\/\/|https:\/\/|www\.|ftp:\/\//i.test(text);

			const districtSelect = document.getElementById('district');
			if (districtSelect && typeof gnData !== 'undefined') {
				Object.keys(gnData).forEach(district => {
					const option = document.createElement('option');
					option.value = district;
					option.textContent = district;
					if (district === "{{ old('district') }}") {
						option.selected = true;
					}
					districtSelect.appendChild(option);
				});
			}

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
						if (field.id === 'google_map_link' && field.value.trim()) {
							if (!/^(https?:\/\/|www\.)/i.test(field.value.trim())) {
								allValid = false;
							}
						}
						if (field.id === 'email' && field.value.trim()) {
							const val = field.value.toLowerCase();
							const outlookPattern = /@(outlook\.com|hotmail\.com|live\.com|msn\.com)$/i;
							if (containsLink(val) || outlookPattern.test(val)) {
								allValid = false;
							}
						}
					});
					// Instead of disabling the button, we keep it enabled so we can show an error popup on click.
					// We will only disable it if there's no nextStep
					btn.disabled = !nextStep;
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
					if (field.id === 'google_map_link' && field.value.trim()) {
						if (!/^(https?:\/\/|www\.)/i.test(field.value.trim())) {
							return false;
						}
					}
					if (field.id === 'email' && field.value.trim()) {
						const val = field.value.toLowerCase();
						const outlookPattern = /@(outlook\.com|hotmail\.com|live\.com|msn\.com)$/i;
						if (containsLink(val) || outlookPattern.test(val)) {
							return false;
						}
					}
					if (field.type === 'checkbox' && !field.checked) {
						return false;
					}
				}
				return validatePasswordStrength() && validatePasswordMatch();
			}

			function validatePasswordStrength() {
				const passwordValue = password.value;
				if (!passwordValue) {
					strengthText.textContent = 'None';
					strengthText.style.color = '#cbd5e1';
					strengthBar.querySelector('.strength-fill').style.width = '0%';
					return false;
				}

				const result = validateAdvancedPassword(passwordValue, {
					username: document.getElementById('username').value,
					email: document.getElementById('email').value
				});

				updatePasswordRuleFeedback(result);

				strengthText.textContent = result.strengthText;
				strengthText.style.color = result.color;
				const fill = strengthBar.querySelector('.strength-fill');
				fill.style.backgroundColor = result.color;
				fill.style.width = result.percent + '%';

				return result.isValid;
			}

			function validatePasswordMatch() {
				return password.value === confirmPassword.value;
			}

			nextButtons.forEach(button => {
				button.addEventListener('click', function () {
					const nextStep = parseInt(this.dataset.next);
					if (nextStep && currentStep < nextStep) {
						const currentStepEl = document.getElementById(`step-${currentStep}`);
						const requiredFields = currentStepEl.querySelectorAll('[required]');
						let isValid = true;
						let errorMessage = 'Please fill all required fields correctly.';

						requiredFields.forEach(field => {
							if (!isValid) return; // Stop at first error
							let val = field.value.trim();
							
							if (!val) {
								isValid = false;
								let fieldName = document.querySelector(`label[for="${field.id}"]`) ? document.querySelector(`label[for="${field.id}"]`).innerText.replace('*', '').trim() : field.name;
								errorMessage = `${fieldName} is required.<br><br>Please check your details and try again.`;
							} else if (field.id === 'name' && (containsLink(val) || /[^a-zA-Z\s]/.test(val))) {
								isValid = false;
								errorMessage = 'Invalid Full Name: Full Name must contain only letters and spaces. No numbers, special characters, or links.<br><br>Please check your details and try again.';
							} else if (field.id === 'nic_no' && !validateNIC(val)) {
								isValid = false;
								errorMessage = 'Invalid NIC Number: NIC Number can only contain digits, X, or V. Example: 123456789V<br><br>Please check your details and try again.';
							} else if (field.id === 'email') {
								const valLow = val.toLowerCase();
								const outlookPattern = /@(outlook\.com|hotmail\.com|live\.com|msn\.com)$/i;
								if (containsLink(valLow)) {
									isValid = false;
									errorMessage = 'Invalid Email Address: Email address cannot contain links.<br><br>Please check your details and try again.';
								} else if (outlookPattern.test(valLow)) {
									isValid = false;
									errorMessage = 'Invalid Email Address: Outlook / Hotmail / Live addresses are not allowed. Please use a different email provider.<br><br>Please check your details and try again.';
								}
							} else if (field.id === 'username' && (containsLink(val) || /\s/.test(val))) {
								isValid = false;
								errorMessage = 'Invalid Username: Username cannot contain spaces or links. Use letters, numbers, underscores, or dots only.<br><br>Please check your details and try again.';
							} else if ((field.id === 'primary_mobile' || field.id === 'whatsapp_number') && (val.length !== 10 || /[^0-9]/.test(val))) {
								isValid = false;
								const labelName = field.id === 'primary_mobile' ? 'Mobile Number' : 'WhatsApp Number';
								errorMessage = `Invalid ${labelName}: ${labelName} must be exactly 10 digits (no spaces, no special characters).<br><br>Please check your details and try again.`;
							} else if (field.id === 'google_map_link' && !/^(https?:\/\/|www\.|maps\.app\.goo\.gl)/i.test(val)) {
								isValid = false;
								errorMessage = 'Invalid Google Map Link of the Residential Address: Please enter a valid Google Maps link (e.g., https://maps.app.goo.gl/... or a full URL).<br><br>Please check your details and try again.';
							}
						});

						if (!isValid) {
							Swal.fire({
								title: 'Validation Error',
								html: errorMessage,
								imageUrl: "{{ asset('assets/icons/Gif/error5.gif') }}",
								imageWidth: 80,
								imageHeight: 80,
								imageAlt: 'Error Icon',
								confirmButtonColor: '#3085d6',
								confirmButtonText: 'OK',
								width: 'auto',
								customClass: {
									popup: 'responsive-swal-popup',
									title: 'swal-title-responsive',
									htmlContainer: 'swal-text-responsive',
									confirmButton: 'swal-button-responsive'
								}
							});
							return;
						}

						updateStep(nextStep);
					}
				});
			});

			prevButtons.forEach(button => {
				button.addEventListener('click', function () {
					const prevStep = parseInt(this.dataset.prev);
					if (prevStep && currentStep > prevStep) {
						updateStep(prevStep);
					}
				});
			});

			// 1. Full Name
			fullNameInput.addEventListener('input', function (e) {
				const originalValue = this.value;
				let newValue = originalValue.replace(/[^a-zA-Z\s]/g, ''); // strip non-letters/spaces
				newValue = newValue.replace(/\s{2,}/g, ' '); // single space max

				if (containsLink(originalValue) || originalValue !== newValue) {
					this.value = newValue;
					Swal.fire({
						title: 'Validation Error',
						html: 'Full Name can only contain letters and spaces. No numbers, special characters, or links allowed.',
						imageUrl: "{{ asset('assets/icons/Gif/error3.gif') }}",
						imageWidth: 80,
						imageHeight: 80,
						imageAlt: 'Error Icon',
						confirmButtonColor: '#3085d6',
						confirmButtonText: 'OK',
						width: 'auto',
						customClass: {
							popup: 'responsive-swal-popup',
							title: 'swal-title-responsive',
							htmlContainer: 'swal-text-responsive',
							confirmButton: 'swal-button-responsive'
						}
					});
				} else {
					this.value = newValue;
				}
				updateNavigationButtons();
			});

			// 2. NIC Number
			nicInput.addEventListener('input', function () {
				const originalValue = this.value;
				let newValue = originalValue.replace(/[^0-9xvXV]/g, '').toUpperCase();
				
				if (containsLink(originalValue) || originalValue !== newValue) {
					this.value = newValue;
					Swal.fire({
						title: 'Validation Error',
						html: 'NIC Number can only contain digits, X, or V. No other letters or special characters allowed.',
						imageUrl: "{{ asset('assets/icons/Gif/error3.gif') }}",
						imageWidth: 80,
						imageHeight: 80,
						imageAlt: 'Error Icon',
						confirmButtonColor: '#3085d6',
						confirmButtonText: 'OK',
						width: 'auto',
						customClass: {
							popup: 'responsive-swal-popup',
							title: 'swal-title-responsive',
							htmlContainer: 'swal-text-responsive',
							confirmButton: 'swal-button-responsive'
						}
					});
				} else {
					this.value = newValue;
				}

				if (newValue === '') {
					nicStatus.className = 'nic-status';
					nicStatus.textContent = '';
				} else if (validateNIC(newValue)) {
					nicStatus.className = 'nic-status valid';
					nicStatus.innerHTML = '<i class="fas fa-check-circle"></i> Valid NIC format';
				} else {
					nicStatus.className = 'nic-status invalid';
					nicStatus.innerHTML = '<i class="fas fa-times-circle"></i> Invalid NIC format';
				}
				updateNavigationButtons();
			});

			nicInput.addEventListener('blur', function () {
				const nicValue = this.value.trim().toUpperCase();
				if (nicValue && validateNIC(nicValue)) {
					this.value = formatNIC(nicValue);
				}
			});

			// 3. Email Address
			const validateEmailField = function() {
				const val = emailInput.value.toLowerCase();
				if (containsLink(val)) {
					Swal.fire({
						title: 'Validation Error',
						html: 'Email address cannot contain links (http://, https://, www., etc.).',
						imageUrl: "{{ asset('assets/icons/Gif/error3.gif') }}",
						imageWidth: 80,
						imageHeight: 80,
						imageAlt: 'Error Icon',
						confirmButtonColor: '#3085d6',
						confirmButtonText: 'OK',
						width: 'auto',
						customClass: {
							popup: 'responsive-swal-popup',
							title: 'swal-title-responsive',
							htmlContainer: 'swal-text-responsive',
							confirmButton: 'swal-button-responsive'
						}
					});
					return false;
				}
				const outlookPattern = /@(outlook\.com|hotmail\.com|live\.com|msn\.com)$/i;
				if (outlookPattern.test(val)) {
					Swal.fire({
						title: 'Validation Error',
						html: 'Outlook email addresses (@outlook.com, @hotmail.com, etc.) are not allowed. Please use a different email provider.',
						imageUrl: "{{ asset('assets/icons/Gif/error3.gif') }}",
						imageWidth: 80,
						imageHeight: 80,
						imageAlt: 'Error Icon',
						confirmButtonColor: '#3085d6',
						confirmButtonText: 'OK',
						width: 'auto',
						customClass: {
							popup: 'responsive-swal-popup',
							title: 'swal-title-responsive',
							htmlContainer: 'swal-text-responsive',
							confirmButton: 'swal-button-responsive'
						}
					});
					return false;
				}
				return true;
			};
			emailInput.addEventListener('blur', validateEmailField);

			// 4. Username
			usernameInput.addEventListener('input', function () {
				const originalValue = this.value;
				let newValue = originalValue.replace(/\s/g, ''); // strip spaces

				if (containsLink(originalValue) || originalValue !== newValue) {
					if (containsLink(originalValue)) newValue = ''; // completely remove if it's a link paste
					this.value = newValue;
					Swal.fire({
						title: 'Validation Error',
						html: 'Username cannot contain spaces or links. Use letters, numbers, underscores, or dots only.',
						imageUrl: "{{ asset('assets/icons/Gif/error3.gif') }}",
						imageWidth: 80,
						imageHeight: 80,
						imageAlt: 'Error Icon',
						confirmButtonColor: '#3085d6',
						confirmButtonText: 'OK',
						width: 'auto',
						customClass: {
							popup: 'responsive-swal-popup',
							title: 'swal-title-responsive',
							htmlContainer: 'swal-text-responsive',
							confirmButton: 'swal-button-responsive'
						}
					});
				} else {
					this.value = newValue;
				}
				
				if (this.value.includes('.')) {
					// Allowing dots per requirements, remove old specific logic rejecting it, but leaving status just in case
				}
				updateNavigationButtons();
			});

			// 5 & 9. Mobile and WhatsApp Number
			const handlePhoneInput = function(e) {
				const originalValue = this.value;
				let newValue = originalValue.replace(/[^0-9]/g, '');
				
				if (newValue.length > 10) {
					newValue = newValue.substring(0, 10);
					if (originalValue !== newValue) {
						this.value = newValue;
						Swal.fire({
							title: 'Validation Error',
							html: `${this.id === 'primary_mobile' ? 'Mobile' : 'WhatsApp'} Number cannot exceed 10 digits.`,
							imageUrl: "{{ asset('assets/icons/Gif/error3.gif') }}",
							imageWidth: 80,
							imageHeight: 80,
							imageAlt: 'Error Icon',
							confirmButtonColor: '#3085d6',
							confirmButtonText: 'OK',
							width: 'auto',
							customClass: {
								popup: 'responsive-swal-popup',
								title: 'swal-title-responsive',
								htmlContainer: 'swal-text-responsive',
								confirmButton: 'swal-button-responsive'
							}
						});
					}
				} else if (containsLink(originalValue) || originalValue !== newValue) {
					this.value = newValue;
					Swal.fire({
						title: 'Validation Error',
						html: `${this.id === 'primary_mobile' ? 'Mobile' : 'WhatsApp'} Number can only contain digits (0-9). No letters, spaces, or special characters allowed.`,
						imageUrl: "{{ asset('assets/icons/Gif/error3.gif') }}",
						imageWidth: 80,
						imageHeight: 80,
						imageAlt: 'Error Icon',
						confirmButtonColor: '#3085d6',
						confirmButtonText: 'OK',
						width: 'auto',
						customClass: {
							popup: 'responsive-swal-popup',
							title: 'swal-title-responsive',
							htmlContainer: 'swal-text-responsive',
							confirmButton: 'swal-button-responsive'
						}
					});
				} else {
					this.value = newValue;
				}
				updateNavigationButtons();
			};
			mobileInput.addEventListener('input', handlePhoneInput);
			if (whatsappInput) whatsappInput.addEventListener('input', handlePhoneInput);

			// 7. Residential Address Comma
			addressInput.addEventListener('keydown', function(e) {
				if (e.key === ',') {
					e.preventDefault();
					const start = this.selectionStart;
					const end = this.selectionEnd;
					const value = this.value;
					this.value = value.substring(0, start) + ',\n' + value.substring(end);
					this.selectionStart = this.selectionEnd = start + 2;
					updateNavigationButtons();
				}
			});

			addressInput.addEventListener('paste', function(e) {
				e.preventDefault();
				let paste = (e.clipboardData || window.clipboardData).getData('text');
				
				// Replace comma followed by optional spaces (including newlines) with comma + newline
				paste = paste.replace(/,\s*/g, ',\n');
				
				const start = this.selectionStart;
				const end = this.selectionEnd;
				const value = this.value;
				
				this.value = value.substring(0, start) + paste + value.substring(end);
				this.selectionStart = this.selectionEnd = start + paste.length;
				updateNavigationButtons();
			});

			// 8. Google Map Link
			mapLinkInput.addEventListener('blur', function() {
				const val = this.value.trim();
				if (val && !/^(https?:\/\/|www\.)/i.test(val)) {
					Swal.fire({
						title: 'Validation Error',
						html: 'Please enter a valid Google Maps link (e.g., https://maps.app.goo.gl/... or a full URL).',
						imageUrl: "{{ asset('assets/icons/Gif/error3.gif') }}",
						imageWidth: 80,
						imageHeight: 80,
						imageAlt: 'Error Icon',
						confirmButtonColor: '#3085d6',
						confirmButtonText: 'OK',
						width: 'auto',
						customClass: {
							popup: 'responsive-swal-popup',
							title: 'swal-title-responsive',
							htmlContainer: 'swal-text-responsive',
							confirmButton: 'swal-button-responsive'
						}
					});
				}
			});

			password.addEventListener('input', function () {
				validatePasswordStrength();
				updateNavigationButtons();
			});

			confirmPassword.addEventListener('input', function () {
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

			form.addEventListener('submit', async function (e) {
				e.preventDefault();

				// Validate email domain on submit
				if (!validateEmailField()) {
					return;
				}

				// Validate Google Maps Link on submit
				const mapVal = mapLinkInput.value.trim();
				if (mapVal && !/^(https?:\/\/|www\.)/i.test(mapVal)) {
					Swal.fire({
						icon: 'error',
						title: 'Validation Error',
						text: 'Please enter a valid Google Maps link (e.g., https://maps.app.goo.gl/... or a full URL).',
						confirmButtonColor: '#3085d6',
						confirmButtonText: 'OK'
					});
					return;
				}

				if (!validateAllSteps()) {
					Swal.fire({
						@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
						title: 'Validation Error',
						text: 'Please fill all required fields correctly.',
						confirmButtonColor: '#10B981'
					});
					return;
				}
				if (!validateNIC(nicInput.value)) {
					Swal.fire({
						@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
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
							@if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
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
							@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
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
						@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
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

			window.addEventListener('resize', function () {
				updateNavigationButtons();
			});

			document.querySelectorAll('.btn-register, .btn-next, .btn-prev').forEach(button => {
				button.addEventListener('mousedown', function (e) {
					this.style.transform = 'scale(0.98)';
				});
				button.addEventListener('mouseup', function (e) {
					this.style.transform = '';
				});
				button.addEventListener('mouseleave', function (e) {
					this.style.transform = '';
				});
			});
		});
	</script>
@endsection
