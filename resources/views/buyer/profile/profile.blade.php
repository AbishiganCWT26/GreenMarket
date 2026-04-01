@extends('buyer.layouts.buyer_master')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('styles')
	<link rel="stylesheet" href="{{ asset('css/buyer/profile.css') }}">
	
	<style>
		.strength-bar {
			height: 5px;
			background: #e2e8f0;
			border-radius: 3px;
			margin-top: 5px;
			overflow: hidden;
		}

		.strength-fill {
			height: 100%;
			width: 0;
			transition: all 0.3s;
		}

		.password-container {
			position: relative;
		}

		.password-toggle {
			position: absolute;
			right: 10px;
			top: 50%;
			transform: translateY(-50%);
			border: none;
			background: none;
			cursor: pointer;
			color: #64748b;
		}

		.requirements li {
			margin-bottom: 5px;
			transition: all 0.3s;
		}

		.text-success {
			color: #10B981 !important;
		}

		.text-danger {
			color: #ef4444 !important;
		}
	</style>
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
				<button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal"
					type="button">Personal Details</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="business-tab" data-bs-toggle="tab" data-bs-target="#business"
					type="button">Business Details</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security"
					type="button">Security</button>
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
							<input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
								name="name" value="{{ old('name', $buyer->name ?? Auth::user()->username) }}" required>
							@error('name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6 mb-3">
							<label for="email" class="form-label">Email Address *</label>
							<input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
								name="email" value="{{ old('email', Auth::user()->email) }}" required>
							@error('email')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="primary_mobile" class="form-label">Mobile Number *</label>
							<input type="tel" class="form-control @error('primary_mobile') is-invalid @enderror"
								id="primary_mobile" name="primary_mobile"
								value="{{ old('primary_mobile', $buyer->primary_mobile ?? '') }}" pattern="^07[0-9]{8}$"
								maxlength="10" minlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
								title="Must start with 07 and be 10 digits long" required>
							@error('primary_mobile')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6 mb-3">
							<label for="whatsapp_number" class="form-label">WhatsApp Number</label>
							<input type="tel" class="form-control @error('whatsapp_number') is-invalid @enderror"
								id="whatsapp_number" name="whatsapp_number"
								value="{{ old('whatsapp_number', $buyer->whatsapp_number ?? '') }}" pattern="^07[0-9]{8}$"
								maxlength="10" minlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
								title="Must start with 07 and be 10 digits long">
							@error('whatsapp_number')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>

					<div class="row">
						<div class="col-md-12 mb-3">
							<label for="nic" class="form-label">NIC Number</label>
							<div class="input-group">
								<input type="text" class="form-control" id="nic" name="nic_no"
									value="{{ $buyer->nic_no ?? '' }}" readonly>
								<button class="btn btn-outline-secondary" type="button" onclick="requestNICEdit()">
									<i class="fa-solid fa-pen"></i>
								</button>
							</div>
						</div>
					</div>

					<div class="mb-3">
						<label for="residential_address" class="form-label">Residential Address *</label>
						<textarea class="form-control @error('residential_address') is-invalid @enderror"
							id="residential_address" name="residential_address" rows="3"
							required>{{ old('residential_address', $buyer->residential_address ?? '') }}</textarea>
						@error('residential_address')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>

					<div class="mb-3">
						<label for="district" class="form-label">District *</label>
						<select class="form-select @error('district') is-invalid @enderror" id="district" name="district"
							required>
							<option value="{{ $buyer->district ?? '' }}" selected>
								{{ $buyer->district ?? 'Select District' }}
							</option>
						</select>
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
							<input type="text" class="form-control @error('business_name') is-invalid @enderror"
								id="business_name" name="business_name"
								value="{{ old('business_name', $buyer->business_name ?? '') }}">
							@error('business_name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6 mb-3">
							<label for="business_type" class="form-label">Business Type</label>
							<select class="form-select @error('business_type') is-invalid @enderror" id="business_type"
								name="business_type">
								<option value="">Select Type</option>
								<option value="individual" {{ ($buyer->business_type ?? '') == 'individual' ? 'selected' : '' }}>Individual</option>
								<option value="restaurant" {{ ($buyer->business_type ?? '') == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
								<option value="hotel" {{ ($buyer->business_type ?? '') == 'hotel' ? 'selected' : '' }}>Hotel
								</option>
								<option value="retailer" {{ ($buyer->business_type ?? '') == 'retailer' ? 'selected' : '' }}>
									Retailer</option>
								<option value="wholesaler" {{ ($buyer->business_type ?? '') == 'wholesaler' ? 'selected' : '' }}>Wholesaler</option>
							</select>
							@error('business_type')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
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

					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="new_password" class="form-label">New Password *</label>
							<div class="password-container">
								<input type="password" class="form-control @error('new_password') is-invalid @enderror"
									id="new_password" name="new_password" required>
								<button type="button" class="password-toggle"
									onclick="togglePasswordVisibility('new_password', 'new_password_icon')">
									<i class="fa-regular fa-eye" id="new_password_icon"></i>
								</button>
							</div>
							@error('new_password')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
							<div class="password-strength mt-3">
								<div class="d-flex justify-content-between align-items-center mb-1">
									<small>Strength: <span id="strengthText">None</span></small>
								</div>
								<div class="strength-bar" id="strengthBar">
									<div class="strength-fill" style="width: 0%; height: 100%; transition: width 0.3s;">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6 mb-3">
							<label for="new_password_confirmation" class="form-label">Confirm New Password *</label>
							<div class="password-container">
								<input type="password" class="form-control" id="new_password_confirmation"
									name="new_password_confirmation" required>
								<button type="button" class="password-toggle"
									onclick="togglePasswordVisibility('new_password_confirmation', 'confirm_password_icon')">
									<i class="fa-regular fa-eye" id="confirm_password_icon"></i>
								</button>
							</div>
							<div id="passwordMatch" class="mt-2">
								<small class="text-success d-none">
									<i class="fas fa-check-circle"></i> Passwords match
								</small>
								<small class="text-danger d-none">
									<i class="fas fa-times-circle"></i> Passwords don't match
								</small>
							</div>
						</div>
					</div>

					<div class="requirements mt-3">
						<h6 class="mb-2">Password Requirements:</h6>
						<ul class="list-unstyled"
							style="font-size: 0.8rem; display: grid; grid-template-columns: 1fr 1fr; gap: 5px;">
							<li id="rule-length" class="text-danger"><i class="fas fa-times me-2"></i> Minimum 8 characters
							</li>
							<li id="rule-number" class="text-danger"><i class="fas fa-times me-2"></i> At least 1 number
								(0–9)</li>
							<li id="rule-capital" class="text-danger"><i class="fas fa-times me-2"></i> At least 1 capital
								(A–Z)</li>
							<li id="rule-lowercase" class="text-danger"><i class="fas fa-times me-2"></i> At least 1
								lowercase (a–z)</li>
							<li id="rule-special" class="text-danger"><i class="fas fa-times me-2"></i> At least 1 special
								char</li>
							<li id="rule-no-space" class="text-danger"><i class="fas fa-times me-2"></i> No spaces allowed
							</li>
							<li id="rule-no-repeat" class="text-danger"><i class="fas fa-times me-2"></i> No consecutive
								repeat</li>
							<li id="rule-no-sequence" class="text-danger"><i class="fas fa-times me-2"></i> No sequential
								chars</li>
							<li id="rule-not-common" class="text-danger"><i class="fas fa-times me-2"></i> No common
								passwords</li>
							<li id="rule-no-links" class="text-danger"><i class="fas fa-times me-2"></i> No links or URLs
							</li>
							<li id="rule-no-personal" class="text-danger"><i class="fas fa-times me-2"></i> No personal info
							</li>
						</ul>
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
	
	<script src="{{ asset('js/form-validation.js') }}"></script>
	<script src="{{ asset('js/gn-data.js') }}"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			@if(session('success'))
				Swal.fire({
					title: 'Success',
					text: '{{ session('success') }}',
					imageUrl: '{{ asset('assets/icons/success1.gif') }}',
					imageWidth: 80,
					imageHeight: 80,
					timer: 3000,
					timerProgressBar: true,
					showConfirmButton: false,
					position: 'top-end',
					toast: true,
					customClass: {
						popup: 'swal-popup-compact'
					}
				});
			@endif

			@if(session('error'))
				Swal.fire({
					title: 'Error',
					text: '{{ session('error') }}',
					imageUrl: '{{ asset('assets/icons/error1.gif') }}',
					imageWidth: 80,
					imageHeight: 80,
					timer: 4000,
					timerProgressBar: true,
					showConfirmButton: false,
					position: 'top-end',
					toast: true,
					customClass: {
						popup: 'swal-popup-compact'
					}
				});
			@endif

				@if($errors->any())
					let errorMessages = '';
					@foreach($errors->all() as $error)
						errorMessages += '{{ $error }}\n';
					@endforeach

					Swal.fire({
						title: 'Validation Error',
						html: errorMessages.replace(/\n/g, '<br>'),
						imageUrl: '{{ asset('assets/icons/error1.gif') }}',
						imageWidth: 80,
						imageHeight: 80,
						showConfirmButton: true,
						customClass: {
							popup: 'swal-popup-compact'
						}
					});
				@endif

				const forms = ['personalForm', 'businessForm', 'passwordForm'];
			forms.forEach(formId => {
				const form = document.getElementById(formId);
				if (form) {
					form.addEventListener('submit', function (e) {
						e.preventDefault();

						if (formId === 'personalForm') {
							const primaryMobile = document.getElementById('primary_mobile').value;
							const whatsappNumber = document.getElementById('whatsapp_number').value;
							let errors = [];

							if (primaryMobile && !/^07[0-9]{8}$/.test(primaryMobile)) {
								errors.push('Mobile Number must start with "07" and be exactly 10 digits.');
							}
							if (whatsappNumber && !/^07[0-9]{8}$/.test(whatsappNumber)) {
								errors.push('WhatsApp Number must start with "07" and be exactly 10 digits.');
							}

							if (errors.length > 0) {
								Swal.fire({
									title: 'Validation Error',
									html: errors.join('<br>'),
									imageUrl: '{{ asset('assets/icons/error1.gif') }}',
									imageWidth: 80,
									imageHeight: 80,
									showConfirmButton: true,
									customClass: {
										popup: 'swal-popup-compact'
									}
								});
								return; // Stop submission
							}
						}

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
				avatarImg.addEventListener('error', function () {
					this.src = '{{ asset('assets/images/default-avatar.png') }}';
				});
			}

			const tabLinks = document.querySelectorAll('#profileTab .nav-link');
			tabLinks.forEach(link => {
				link.addEventListener('click', function () {
					tabLinks.forEach(l => l.classList.remove('active'));
					this.classList.add('active');
				});
			});

			window.addEventListener('resize', function () {
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

		function validatePasswordStrength(passwordValue) {
			const strengthText = document.getElementById('strengthText');
			const strengthBar = document.getElementById('strengthBar');
			const fill = strengthBar.querySelector('.strength-fill');

			if (!passwordValue) {
				strengthText.textContent = 'None';
				strengthText.style.color = '#cbd5e1';
				fill.style.width = '0%';
				updatePasswordRuleFeedback({ rules: { length: false, number: false, capital: false, lowercase: false, special: false, 'no-space': false, 'no-repeat': false, 'no-sequence': false, 'not-common': false, 'no-links': false, 'no-personal': false } });
				return false;
			}

			const username = "{{ Auth::user()->username }}";
			const email = "{{ Auth::user()->email }}";

			const result = validateAdvancedPassword(passwordValue, { username, email });
			updatePasswordRuleFeedback(result);

			strengthText.textContent = result.strengthText;
			strengthText.style.color = result.color;
			fill.style.backgroundColor = result.color;
			fill.style.width = result.percent + '%';

			return result.isValid;
		}

		document.addEventListener('DOMContentLoaded', function () {
			const passwordInput = document.getElementById('new_password');
			const confirmInput = document.getElementById('new_password_confirmation');
			const submitBtn = document.querySelector('#passwordForm button[type="submit"]');
			const passwordMatch = document.getElementById('passwordMatch');

			if (passwordInput && confirmInput && submitBtn) {
				submitBtn.disabled = true;

				function validatePasswordMatch() {
					const match = passwordInput.value === confirmInput.value;
					const successMsg = passwordMatch.querySelector('.text-success');
					const errorMsg = passwordMatch.querySelector('.text-danger');

					if (passwordInput.value && confirmInput.value) {
						if (match) {
							successMsg.classList.remove('d-none');
							errorMsg.classList.add('d-none');
						} else {
							successMsg.classList.add('d-none');
							errorMsg.classList.remove('d-none');
						}
					} else {
						successMsg.classList.add('d-none');
						errorMsg.classList.add('d-none');
					}
					return match;
				}

				function updateSubmitState() {
					const isStrengthValid = validatePasswordStrength(passwordInput.value);
					const isMatchValid = validatePasswordMatch();
					submitBtn.disabled = !(isStrengthValid && isMatchValid);
				}

				passwordInput.addEventListener('input', updateSubmitState);
				confirmInput.addEventListener('input', updateSubmitState);
			}
		});

		// Region Data Initialization
		if (typeof gnData !== 'undefined') {
			const distSelect = document.getElementById('district');
			const initialDist = "{{ $buyer->district ?? '' }}";

			// Populate Districts
			distSelect.innerHTML = '<option value="" disabled>Select District</option>';
			Object.keys(gnData).forEach(dist => {
				distSelect.append(new Option(dist, dist, dist === initialDist, dist === initialDist));
			});
		}

		function requestNICEdit() {
			Swal.fire({
				title: 'Contact System Administrator to change NIC No.',
				imageUrl: '{{ asset('assets/icons/info1.gif') }}',
				imageWidth: 80,
				imageHeight: 80,
				showCancelButton: true,
				confirmButtonText: 'Contact Us',
				cancelButtonText: 'Cancel',
				customClass: {
					icon: 'no-border'
				}
			}).then((result) => {
				if (result.isConfirmed) {
					window.location.href = "{{ url('contact-us') }}"; // using conventional URL structure for contact us. We assume the route exists.
				}
			});
		}
	</script>
@endsection
