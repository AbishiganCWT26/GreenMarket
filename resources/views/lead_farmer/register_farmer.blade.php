@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Register Farmer')
@section('page-title', 'Register Farmer')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lead_farmer/farmer_registation.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
@endsection

@section('content')
<div class="reg-wrap">
	<div class="reg-container">
		<div class="reg-card">
			<div class="reg-head">
				<i class="fas fa-user-plus"></i>
				<h2>Register Farmer</h2>
				<p>Add a new farmer to your group</p>
			</div>

			<form id="farmerForm" action="{{ route('lf.storeFarmer') }}" method="POST" enctype="multipart/form-data">
				@csrf
				<div class="reg-steps">
					<div class="step-indicators">
						<span class="step active" data-step="1">1. Basic</span>
						<span class="step" data-step="2">2. Contact</span>
						<span class="step" data-step="3">3. Address</span>
						<span class="step" data-step="4">4. Payment</span>
					</div>
					<div class="step-content" id="step1">
						<div class="form-section">
							<h3><i class="fas fa-user-circle"></i> Basic Information</h3>
							<div class="form-row">
								<div class="form-field">
									<label><i class="fas fa-user"></i> Full Name <span class="req">*</span></label>
									<input type="text" name="name" id="fullName" placeholder="Enter full name" required>
								</div>
								<div class="form-field">
									<label><i class="fas fa-id-card"></i> NIC Number <span class="req">*</span></label>
									<input type="text" name="nic_no" id="nicNo" placeholder="123456789V or 200123456789" required>
									<small>9 digits + V/X OR 12 digits</small>
								</div>
								<div class="form-field">
									<label><i class="fas fa-at"></i> Username <span class="req">*</span></label>
									<input type="text" name="username" id="username" placeholder="Choose username" required>
								</div>
								<div class="form-field">
									<label><i class="fas fa-lock"></i> Password <span class="req">*</span></label>
									<div class="pass-wrap">
										<input type="password" name="password" id="password" placeholder="Enter password" required>
										<i class="fas fa-eye pass-toggle" onclick="togglePassword('password', this)"></i>
									</div>
									<div class="strength-bar">
										<div class="strength-fill" id="strengthFill"></div>
									</div>
									<span class="strength-text" id="strengthText">None</span>
								</div>
								<div class="form-field">
									<label><i class="fas fa-lock"></i> Confirm Password <span class="req">*</span></label>
									<div class="pass-wrap">
										<input type="password" name="password_confirmation" id="confirmPassword" placeholder="Confirm password" required>
										<i class="fas fa-eye pass-toggle" onclick="togglePassword('confirmPassword', this)"></i>
									</div>
									<span class="match-status" id="matchStatus"></span>
								</div>
							</div>
							<div class="pass-rules">
								<p><i class="fas fa-shield-alt"></i> Password Requirements:</p>
								<ul class="rules-list" id="rulesList">
									<li id="ruleLength"><i class="fas fa-times"></i> 8+ characters</li>
									<li id="ruleUpper"><i class="fas fa-times"></i> Uppercase letter</li>
									<li id="ruleLower"><i class="fas fa-times"></i> Lowercase letter</li>
									<li id="ruleNumber"><i class="fas fa-times"></i> Number</li>
									<li id="ruleSpecial"><i class="fas fa-times"></i> Special character</li>
									<li id="ruleNospace"><i class="fas fa-times"></i> No spaces</li>
									<li id="ruleRepeat"><i class="fas fa-times"></i> No 3x repeats</li>
									<li id="ruleSequence"><i class="fas fa-times"></i> No sequences</li>
									<li id="ruleCommon"><i class="fas fa-times"></i> Not common</li>
									<li id="ruleNolinks"><i class="fas fa-times"></i> No links</li>
									<li id="rulePersonal"><i class="fas fa-times"></i> No personal info</li>
								</ul>
							</div>
						</div>
					</div>

					<div class="step-content" id="step2" style="display:none;">
						<div class="form-section">
							<h3><i class="fas fa-address-book"></i> Contact Information</h3>
							<div class="form-row">
								<div class="form-field">
									<label><i class="fas fa-mobile-alt"></i> Mobile <span class="req">*</span></label>
									<input type="tel" name="primary_mobile" id="mobile" placeholder="0712345678" maxlength="10" required>
								</div>
								<div class="form-field">
									<label><i class="fab fa-whatsapp"></i> WhatsApp</label>
									<input type="tel" name="whatsapp_number" id="whatsapp" placeholder="0712345678" maxlength="10">
								</div>
								<div class="form-field">
									<label><i class="fas fa-envelope"></i> Email <span class="req">*</span></label>
									<input type="email" name="email" id="email" placeholder="farmer@email.com" required>
								</div>
								<div class="form-field full">
									<label><i class="fas fa-camera"></i> Profile Photo <span class="req">*</span></label>
									<div class="upload-area" id="uploadArea">
										<input type="file" name="profile_photo" id="profilePhoto" accept="image/jpeg,image/png,image/heic,image/heif" required>
										<div class="upload-placeholder">
											<i class="fas fa-cloud-upload-alt"></i>
											<span>Click or drag image</span>
											<small>JPG, PNG, HEIC up to 5MB (no GIF)</small>
										</div>
									</div>
									<div class="preview-container" id="previewContainer" style="display:none;">
										<img id="previewImg" class="preview-img">
										<button type="button" class="remove-photo" id="removePhoto"><i class="fas fa-trash"></i> Remove</button>
										<button type="button" class="edit-photo" id="editPhoto"><i class="fas fa-crop"></i> Edit</button>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="step-content" id="step3" style="display:none;">
						<div class="form-section">
							<h3><i class="fas fa-map-marker-alt"></i> Address Information</h3>
							<div class="form-row">
								<div class="form-field full">
									<label><i class="fas fa-home"></i> Residential Address <span class="req">*</span></label>
									<textarea name="residential_address" id="address" rows="3" placeholder="Enter address (commas become new lines)" required></textarea>
								</div>
								<div class="form-field">
									<label><i class="fas fa-map"></i> District <span class="req">*</span></label>
									<select name="district" id="district" required>
										<option value="">Select District</option>
									</select>
								</div>
								<div class="form-field">
									<label><i class="fas fa-building"></i> Divisional Secretariat <span class="req">*</span></label>
									<select name="divisional_secretariat" id="dsSelect" required disabled>
										<option value="">Select District First</option>
									</select>
								</div>
								<div class="form-field">
									<label><i class="fas fa-landmark"></i> GN Division <span class="req">*</span></label>
									<select name="grama_niladhari_division" id="gnSelect" required disabled>
										<option value="">Select DS First</option>
									</select>
								</div>
								<div class="form-field">
									<label><i class="fas fa-barcode"></i> GN Code <span class="req">*</span></label>
									<input type="text" name="gn_division_code" id="gnCode" readonly>
								</div>
								<div class="form-field full">
									<label><i class="fas fa-map-marked-alt"></i> Google Maps Link <span class="req">*</span></label>
									<input type="url" name="address_map_link" id="mapLink" placeholder="https://maps.google.com/..." required>
								</div>
							</div>
						</div>
					</div>

					<div class="step-content" id="step4" style="display:none;">
						<div class="form-section">
							<h3><i class="fas fa-money-bill-wave"></i> Payment Information</h3>
							<div class="form-row">
								<div class="form-field">
									<label><i class="fas fa-credit-card"></i> Payment Method <span class="req">*</span></label>
									<select name="preferred_payment" id="paymentMethod" required>
										<option value="">Select Method</option>
										<option value="bank">Bank Transfer</option>
										<option value="ezcash">EzCash</option>
										<option value="mcash">mCash</option>
										<option value="all">All Methods</option>
									</select>
								</div>
							</div>
							<div id="bankFields" class="payment-fields" style="display:none;">
								<h4><i class="fas fa-university"></i> Bank Details</h4>
								<div class="form-row">
									<div class="form-field"><label>Bank Name <span class="req">*</span></label><input type="text" name="bank_name" id="bankName"></div>
									<div class="form-field"><label>Bank Branch <span class="req">*</span></label><input type="text" name="bank_branch" id="bankBranch"></div>
									<div class="form-field"><label>Account Holder <span class="req">*</span></label><input type="text" name="account_holder_name" id="accountHolder"></div>
									<div class="form-field"><label>Account Number <span class="req">*</span></label><input type="text" name="account_number" id="accountNumber"></div>
								</div>
							</div>
							<div id="ezcashFields" class="payment-fields" style="display:none;">
								<h4><i class="fas fa-mobile-alt"></i> EzCash Details</h4>
								<div class="form-field"><label>EzCash Number <span class="req">*</span></label><input type="tel" name="ezcash_mobile" id="ezcashMobile" maxlength="10" placeholder="074/076/077 followed by 7 digits"></div>
							</div>
							<div id="mcashFields" class="payment-fields" style="display:none;">
								<h4><i class="fas fa-mobile-alt"></i> mCash Details</h4>
								<div class="form-field"><label>mCash Number <span class="req">*</span></label><input type="tel" name="mcash_mobile" id="mcashMobile" maxlength="10" placeholder="070/071 followed by 7 digits"></div>
							</div>
						</div>
					</div>

					<div class="reg-actions">
						<button type="button" class="btn-prev" id="prevBtn" style="display:none;">Previous</button>
						<button type="button" class="btn-next" id="nextBtn">Next</button>
						<button type="submit" class="btn-submit" id="submitBtn" style="display:none;">Register Farmer</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Cropper Modal -->
<div class="photo-crop-modal" id="cropperModal">
	<div class="cropper-box">
		<div class="cropper-head">
			<h3><i class="fas fa-crop"></i> Edit Profile Photo</h3>
			<button class="cropper-close" id="closeCropper"><i class="fas fa-times"></i></button>
		</div>
		<div class="cropper-body">
			<img id="cropperImage" style="max-width:100%;">
		</div>
		<div class="cropper-controls">
			<button type="button" id="rotateBtn"><i class="fas fa-undo-alt"></i> Rotate</button>
			<button type="button" id="resetBtn"><i class="fas fa-redo-alt"></i> Reset</button>
			<button type="button" id="cropBtn"><i class="fas fa-check"></i> Apply</button>
		</div>
	</div>
</div>

<script src="{{ asset('js/gn-data.js') }}"></script>
<script>
let currentStep = 1;
let cropper = null;
let cropperImageFile = null;

document.addEventListener('DOMContentLoaded', function() {
	initStepNavigation();
	initRealTimeFilters();
	initGNHierarchy();
	initPaymentMethodToggle();
	initPhotoUpload();
	initCropper();

	document.getElementById('nextBtn').addEventListener('click', function() {
		if (validateStep(currentStep)) {
			if (currentStep < 4) {
				showStep(currentStep + 1);
			}
		}
	});

	document.getElementById('prevBtn').addEventListener('click', function() {
		if (currentStep > 1) showStep(currentStep - 1);
	});

	document.getElementById('farmerForm').addEventListener('submit', function(e) {
		e.preventDefault();
		if (validateAllFields()) {
			let submitBtn = document.getElementById('submitBtn');
			submitBtn.disabled = true;
			submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';
			
			let formData = new FormData(this);
			fetch(this.action, {
				method: 'POST',
				body: formData,
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					'Accept': 'application/json'
				}
			})
			.then(response => response.json().then(data => ({ status: response.status, body: data })))
			.then(res => {
				if (res.status === 200 || res.status === 201 || res.body.success) {
					Swal.fire({
						imageUrl: '{{ asset("assets/icons/Gif/Send successfully1.gif") }}',
						imageWidth: 80, imageHeight: 80,
						title: 'Success!',
						text: res.body.message || 'Farmer registered successfully.',
						confirmButtonColor: '#10B981',
						customClass: { popup: 'swal-responsive' }
					}).then(() => {
						window.location.href = res.body.redirect || '{{ route("lf.manageFarmers") }}';
					});
				} else {
					submitBtn.disabled = false;
					submitBtn.innerHTML = 'Register Farmer';
					
					let errorMsg = res.body.message || 'An error occurred during registration.';
					
					if (res.status === 422 && res.body.errors) {
						errorMsg = Object.values(res.body.errors).map(e => e.join('<br>')).join('<br>');
					} else if (errorMsg.includes('users_email_key')) {
						errorMsg = 'This email address is already registered.';
					} else if (errorMsg.includes('users_username_key')) {
						errorMsg = 'This username is already taken.';
					} else if (errorMsg.includes('nic_no')) {
						errorMsg = 'This NIC Number is already registered.';
					} else if (errorMsg.includes('SQLSTATE')) {
						errorMsg = 'A database error occurred. Please try again.';
					}
					
					showError('Registration Failed', errorMsg);
				}
			})
			.catch(error => {
				submitBtn.disabled = false;
				submitBtn.innerHTML = 'Register Farmer';
				showError('Network Error', 'Could not connect to the server. Please try again.');
			});
		}
	});
});

function initStepNavigation() {
	showStep(1);
}

function showStep(step) {
	document.querySelectorAll('.step-content').forEach((el, idx) => {
		el.style.display = idx + 1 === step ? 'block' : 'none';
	});
	document.querySelectorAll('.step').forEach((el, idx) => {
		if (idx + 1 === step) el.classList.add('active');
		else el.classList.remove('active');
	});
	currentStep = step;
	document.getElementById('prevBtn').style.display = step === 1 ? 'none' : 'inline-flex';
	document.getElementById('nextBtn').style.display = step === 4 ? 'none' : 'inline-flex';
	document.getElementById('submitBtn').style.display = step === 4 ? 'inline-flex' : 'none';
}

function showError(field, message) {
	Swal.fire({
		imageUrl: '{{ asset("assets/icons/Gif/Validation Error1.gif") }}',
		imageWidth: 80,
		imageHeight: 80,
		title: 'Validation Error',
		html: `<strong>${field}</strong><br>${message}`,
		confirmButtonColor: '#10B981',
		customClass: { popup: 'swal-responsive' }
	});
}

function validateStep(step) {
	if (step === 1) return validateBasic();
	if (step === 2) return validateContact();
	if (step === 3) return validateAddress();
	if (step === 4) return validatePayment();
	return true;
}

function validateBasic() {
	let name = document.getElementById('fullName').value.trim();
	if (!name) { showError('Full Name', 'Please enter full name.'); return false; }
	if (/[0-9!@#$%^&*(),.?":{}|<>]/.test(name)) { showError('Full Name', 'Only letters and spaces allowed.'); return false; }

	let nic = document.getElementById('nicNo').value.trim().toUpperCase();
	if (!nic) { showError('NIC Number', 'Please enter NIC number.'); return false; }
	if (!/^[0-9]{9}[VX]$/.test(nic) && !/^[0-9]{12}$/.test(nic)) { showError('NIC Number', 'Must be 9 digits + V/X OR 12 digits.'); return false; }

	let username = document.getElementById('username').value.trim();
	if (!username) { showError('Username', 'Please enter username.'); return false; }
	if (/[\s]/.test(username)) { showError('Username', 'No spaces allowed.'); return false; }

	let pass = document.getElementById('password').value;
	if (!pass) { showError('Password', 'Please enter password.'); return false; }
	if (!validatePasswordStrength(pass)) { showError('Password', 'Does not meet all security requirements.'); return false; }

	let confirm = document.getElementById('confirmPassword').value;
	if (pass !== confirm) { showError('Confirm Password', 'Passwords do not match.'); return false; }
	return true;
}

function validateContact() {
	let mobile = document.getElementById('mobile').value.trim();
	if (!mobile) { showError('Mobile Number', 'Please enter mobile number.'); return false; }
	if (!/^[0-9]{10}$/.test(mobile)) { showError('Mobile Number', 'Must be exactly 10 digits.'); return false; }

	let whatsapp = document.getElementById('whatsapp').value.trim();
	if (whatsapp && !/^[0-9]{10}$/.test(whatsapp)) { showError('WhatsApp', 'Must be exactly 10 digits.'); return false; }

	let email = document.getElementById('email').value.trim();
	if (!email) { showError('Email', 'Please enter email address.'); return false; }
	let blockedDomains = ['outlook.com', 'hotmail.com', 'live.com', 'msn.com'];
	let domain = email.split('@')[1];
	if (blockedDomains.includes(domain)) { showError('Email', 'Outlook/Hotmail/Live addresses not allowed.'); return false; }
	if (/http:\/\/|https:\/\/|www\./.test(email)) { showError('Email', 'Email cannot contain links.'); return false; }

	let photo = document.getElementById('profilePhoto').files[0];
	if (!photo) { showError('Profile Photo', 'Profile photo is required.'); return false; }
	if (photo) {
		let validTypes = ['image/jpeg', 'image/png', 'image/heic', 'image/heif'];
		if (!validTypes.includes(photo.type)) { showError('Profile Photo', 'Only JPG, PNG, HEIC allowed. GIF is not permitted.'); return false; }
		if (photo.size > 5 * 1024 * 1024) { showError('Profile Photo', 'Maximum size is 5MB.'); return false; }
	}
	return true;
}

function validateAddress() {
	let address = document.getElementById('address').value.trim();
	if (!address) { showError('Residential Address', 'Residential Address is required.'); return false; }

	let district = document.getElementById('district').value;
	if (!district) { showError('District', 'Please select district.'); return false; }

	let ds = document.getElementById('dsSelect').value;
	if (!ds) { showError('Divisional Secretariat', 'Please select DS division.'); return false; }

	let gn = document.getElementById('gnSelect').value;
	if (!gn) { showError('GN Division', 'Please select GN division.'); return false; }

	let gnCode = document.getElementById('gnCode').value;
	if (!gnCode) { showError('GN Code', 'GN Division Code is required.'); return false; }

	let mapLink = document.getElementById('mapLink').value.trim();
	if (!mapLink) { showError('Google Maps Link', 'Please provide Google Maps link.'); return false; }
	if (!mapLink.match(/^https?:\/\//) && !mapLink.match(/^www\./)) { showError('Google Maps Link', 'Must be a valid URL.'); return false; }
	return true;
}

function validatePayment() {
	let method = document.getElementById('paymentMethod').value;
	if (!method) { showError('Payment Method', 'Please select payment method.'); return false; }

	if (method === 'bank' || method === 'all') {
		let bankName = document.getElementById('bankName').value.trim();
		let bankBranch = document.getElementById('bankBranch').value.trim();
		let accountHolder = document.getElementById('accountHolder').value.trim();
		let accountNumber = document.getElementById('accountNumber').value.trim();
		if (!bankName || !bankBranch || !accountHolder || !accountNumber) {
			showError('Bank Details', 'Please fill all bank fields.'); return false;
		}
	}
	if (method === 'ezcash' || method === 'all') {
		let ezcash = document.getElementById('ezcashMobile').value.trim();
		if (!ezcash) { showError('EzCash', 'Please enter EzCash number.'); return false; }
		if (!/^(074|076|077)[0-9]{7}$/.test(ezcash)) { showError('EzCash', 'Must start with 074/076/077 and be 10 digits.'); return false; }
	}
	if (method === 'mcash' || method === 'all') {
		let mcash = document.getElementById('mcashMobile').value.trim();
		if (!mcash) { showError('mCash', 'Please enter mCash number.'); return false; }
		if (!/^(070|071)[0-9]{7}$/.test(mcash)) { showError('mCash', 'Must start with 070/071 and be 10 digits.'); return false; }
	}
	return true;
}

function validateAllFields() {
	return validateBasic() && validateContact() && validateAddress() && validatePayment();
}

function validatePasswordStrength(pass) {
	let username = document.getElementById('username').value;
	let email = document.getElementById('email') ? document.getElementById('email').value : '';
	let name = document.getElementById('fullName').value;
	let mobile = document.getElementById('mobile') ? document.getElementById('mobile').value : '';
	let personalInfo = [username, email.split('@')[0], name, mobile].filter(Boolean);

	let seqChars = 'abcdefghijklmnopqrstuvwxyz0123456789';
	let hasSequence = false;
	for (let i = 0; i < pass.length - 2; i++) {
		let sub = pass.substring(i, i + 3).toLowerCase();
		if (seqChars.includes(sub)) { hasSequence = true; break; }
		let revSub = sub.split('').reverse().join('');
		if (seqChars.includes(revSub)) { hasSequence = true; break; }
	}

	let checks = {
		length: pass.length >= 8,
		upper: /[A-Z]/.test(pass),
		lower: /[a-z]/.test(pass),
		number: /[0-9]/.test(pass),
		special: /[!@#$%^&*(),.?":{}|<>\-_=+\[\]\\;'/`~]/.test(pass),
		nospace: !/\s/.test(pass),
		repeat: !/(.)\1{2}/.test(pass),
		sequence: !hasSequence,
		common: !/password|12345678|qwerty|admin123|letmein|welcome|monkey|dragon|master|login/.test(pass.toLowerCase()),
		nolinks: !/https?:\/\/|www\.|ftp:\/\/|\.com|\.org|\.net|\.lk/.test(pass.toLowerCase()),
		personal: !personalInfo.some(info => info && info.length >= 3 && pass.toLowerCase().includes(info.toLowerCase()))
	};

	let ruleMap = {
		length: 'ruleLength', upper: 'ruleUpper', lower: 'ruleLower', number: 'ruleNumber',
		special: 'ruleSpecial', nospace: 'ruleNospace', repeat: 'ruleRepeat', sequence: 'ruleSequence',
		common: 'ruleCommon', nolinks: 'ruleNolinks', personal: 'rulePersonal'
	};

	let ruleLabels = {
		length: '8+ characters', upper: 'Uppercase letter', lower: 'Lowercase letter', number: 'Number',
		special: 'Special character', nospace: 'No spaces', repeat: 'No 3x repeats', sequence: 'No sequences',
		common: 'Not common', nolinks: 'No links', personal: 'No personal info'
	};

	Object.keys(ruleMap).forEach(rule => {
		let el = document.getElementById(ruleMap[rule]);
		if (el) {
			el.className = checks[rule] ? 'valid' : 'invalid';
			el.innerHTML = '<i class="fas fa-' + (checks[rule] ? 'check' : 'times') + '"></i> ' + ruleLabels[rule];
		}
	});

	let rules = Object.keys(checks);
	let strength = Object.values(checks).filter(Boolean).length;
	let percent = (strength / rules.length) * 100;
	let strengthFill = document.getElementById('strengthFill');
	let strengthText = document.getElementById('strengthText');
	let color = percent < 30 ? '#ef4444' : percent < 60 ? '#f59e0b' : percent < 90 ? '#3b82f6' : '#10B981';
	let label = percent < 30 ? 'Weak' : percent < 60 ? 'Fair' : percent < 90 ? 'Good' : 'Strong';
	if (strengthFill) { strengthFill.style.width = percent + '%'; strengthFill.style.backgroundColor = color; }
	if (strengthText) { strengthText.textContent = label; strengthText.style.color = color; }
	return Object.values(checks).every(Boolean);
}

function initRealTimeFilters() {
	document.getElementById('fullName').addEventListener('input', function(e) {
		this.value = this.value.replace(/[0-9!@#$%^&*(),.?":{}|<>]/g, '');
	});
	document.getElementById('nicNo').addEventListener('input', function(e) {
		this.value = this.value.toUpperCase().replace(/[^0-9VX]/g, '');
		if (this.value.length > 12) this.value = this.value.slice(0, 12);
	});
	document.getElementById('mobile').addEventListener('input', function(e) {
		this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
	});
	document.getElementById('whatsapp').addEventListener('input', function(e) {
		this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
	});
	document.getElementById('username').addEventListener('input', function(e) {
		this.value = this.value.replace(/\s/g, '');
	});
	let addressInput = document.getElementById('address');
	addressInput.addEventListener('keypress', function(e) {
		if (e.key === ',') {
			let pos = this.selectionStart;
			let val = this.value;
			this.value = val.substring(0, pos) + ',\n' + val.substring(pos);
			this.selectionStart = this.selectionEnd = pos + 2;
			e.preventDefault();
		}
	});
	addressInput.addEventListener('paste', function(e) {
		e.preventDefault();
		let text = (e.originalEvent || e).clipboardData.getData('text/plain');
		text = text.replace(/,\s*/g, ',\n');
		let pos = this.selectionStart;
		let val = this.value;
		this.value = val.substring(0, pos) + text + val.substring(this.selectionEnd);
		this.selectionStart = this.selectionEnd = pos + text.length;
	});
	document.getElementById('password').addEventListener('input', function() {
		validatePasswordStrength(this.value);
		let confirm = document.getElementById('confirmPassword').value;
		if (confirm) checkPasswordMatch();
	});
	document.getElementById('confirmPassword').addEventListener('input', checkPasswordMatch);

	// Bank Name: only letters and spaces
	document.getElementById('bankName').addEventListener('input', function() {
		this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
	});
	// Account Holder: only letters and spaces
	document.getElementById('accountHolder').addEventListener('input', function() {
		this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
	});
	// Account Number: only numbers
	document.getElementById('accountNumber').addEventListener('input', function() {
		this.value = this.value.replace(/[^0-9]/g, '');
	});
	// EzCash: only 10 digit numbers
	document.getElementById('ezcashMobile').addEventListener('input', function() {
		this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
	});
	// mCash: only 10 digit numbers
	document.getElementById('mcashMobile').addEventListener('input', function() {
		this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
	});
}

function checkPasswordMatch() {
	let pass = document.getElementById('password').value;
	let confirm = document.getElementById('confirmPassword').value;
	let matchSpan = document.getElementById('matchStatus');
	if (confirm) {
		if (pass === confirm) {
			matchSpan.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match';
			matchSpan.style.color = '#10B981';
		} else {
			matchSpan.innerHTML = '<i class="fas fa-times-circle"></i> Passwords do not match';
			matchSpan.style.color = '#ef4444';
		}
	} else { matchSpan.innerHTML = ''; matchSpan.style.color = ''; }
}

function initGNHierarchy() {
	let district = document.getElementById('district');
	let dsSelect = document.getElementById('dsSelect');
	let gnSelect = document.getElementById('gnSelect');
	let gnCode = document.getElementById('gnCode');

	if (typeof gnData !== 'undefined') {
		Object.keys(gnData).forEach(d => {
			let opt = document.createElement('option');
			opt.value = d; opt.textContent = d;
			district.appendChild(opt);
		});
	}

	district.addEventListener('change', function() {
		let dist = this.value;
		dsSelect.innerHTML = '<option value="">Select DS</option>';
		gnSelect.innerHTML = '<option value="">Select DS First</option>';
		gnSelect.disabled = true;
		gnCode.value = '';
		if (dist && gnData[dist]) {
			dsSelect.disabled = false;
			Object.keys(gnData[dist]).forEach(ds => {
				let opt = document.createElement('option');
				opt.value = ds; opt.textContent = ds;
				dsSelect.appendChild(opt);
			});
		} else dsSelect.disabled = true;
	});

	dsSelect.addEventListener('change', function() {
		let dist = district.value;
		let ds = this.value;
		gnSelect.innerHTML = '<option value="">Select GN Division</option>';
		gnCode.value = '';
		if (dist && ds && gnData[dist] && gnData[dist][ds]) {
			gnSelect.disabled = false;
			gnData[dist][ds].forEach(gn => {
				let opt = document.createElement('option');
				opt.value = gn.name; opt.textContent = gn.name;
				opt.dataset.code = gn.code;
				gnSelect.appendChild(opt);
			});
		} else gnSelect.disabled = true;
	});

	gnSelect.addEventListener('change', function() {
		let opt = this.options[this.selectedIndex];
		gnCode.value = opt.dataset.code || '';
	});
}

function initPaymentMethodToggle() {
	let method = document.getElementById('paymentMethod');
	let bankDiv = document.getElementById('bankFields');
	let ezcashDiv = document.getElementById('ezcashFields');
	let mcashDiv = document.getElementById('mcashFields');

	method.addEventListener('change', function() {
		let val = this.value;
		bankDiv.style.display = 'none';
		ezcashDiv.style.display = 'none';
		mcashDiv.style.display = 'none';
		if (val === 'bank') bankDiv.style.display = 'block';
		else if (val === 'ezcash') ezcashDiv.style.display = 'block';
		else if (val === 'mcash') mcashDiv.style.display = 'block';
		else if (val === 'all') {
			bankDiv.style.display = 'block';
			ezcashDiv.style.display = 'block';
			mcashDiv.style.display = 'block';
		}
	});
}

function initPhotoUpload() {
	let input = document.getElementById('profilePhoto');
	let container = document.getElementById('previewContainer');
	let preview = document.getElementById('previewImg');
	let uploadArea = document.getElementById('uploadArea');

	function handleFile(file) {
		let validTypes = ['image/jpeg', 'image/png', 'image/heic', 'image/heif'];
		if (!validTypes.includes(file.type)) {
			showError('Profile Photo', 'Only JPG, PNG, HEIC allowed. GIF is not permitted.');
			input.value = '';
			return;
		}
		if (file.size > 5 * 1024 * 1024) {
			showError('Profile Photo', 'Maximum size is 5MB.');
			input.value = '';
			return;
		}
		let dt = new DataTransfer();
		dt.items.add(file);
		input.files = dt.files;
		let reader = new FileReader();
		reader.onload = function(e) {
			preview.src = e.target.result;
			cropperImageFile = e.target.result;
			container.style.display = 'flex';
		};
		reader.readAsDataURL(file);
	}

	input.addEventListener('change', function(e) {
		let file = e.target.files[0];
		if (file) handleFile(file);
	});

	// Click on upload area triggers file input
	uploadArea.addEventListener('click', function(e) {
		if (e.target !== input) input.click();
	});

	// Drag and drop support
	uploadArea.addEventListener('dragover', function(e) {
		e.preventDefault();
		e.stopPropagation();
		this.style.borderColor = '#10B981';
		this.style.background = '#f0fdf4';
	});
	uploadArea.addEventListener('dragleave', function(e) {
		e.preventDefault();
		e.stopPropagation();
		this.style.borderColor = '';
		this.style.background = '';
	});
	uploadArea.addEventListener('drop', function(e) {
		e.preventDefault();
		e.stopPropagation();
		this.style.borderColor = '';
		this.style.background = '';
		let file = e.dataTransfer.files[0];
		if (file) handleFile(file);
	});

	// Remove photo button
	document.getElementById('removePhoto').addEventListener('click', function() {
		input.value = '';
		preview.src = '';
		cropperImageFile = null;
		container.style.display = 'none';
	});
}

function initCropper() {
	let modal = document.getElementById('cropperModal');
	let img = document.getElementById('cropperImage');
	let openBtn = document.getElementById('editPhoto');
	let closeBtn = document.getElementById('closeCropper');
	let rotateBtn = document.getElementById('rotateBtn');
	let resetBtn = document.getElementById('resetBtn');
	let cropBtn = document.getElementById('cropBtn');

	if (openBtn) {
		openBtn.addEventListener('click', function() {
			if (cropperImageFile) {
				img.src = cropperImageFile;
				modal.style.display = 'flex';
				if (cropper) cropper.destroy();
				cropper = new Cropper(img, {
					aspectRatio: 1,
					viewMode: 1,
					movable: true,
					zoomable: true,
					rotatable: true,
					scalable: true
				});
			}
		});
	}
	if (closeBtn) closeBtn.addEventListener('click', function() { modal.style.display = 'none'; });
	if (rotateBtn) rotateBtn.addEventListener('click', function() { if (cropper) cropper.rotate(90); });
	if (resetBtn) resetBtn.addEventListener('click', function() { if (cropper) cropper.reset(); });
	if (cropBtn) cropBtn.addEventListener('click', function() {
		if (cropper) {
			let canvas = cropper.getCroppedCanvas({ width: 400, height: 400 });
			let croppedData = canvas.toDataURL('image/jpeg');
			document.getElementById('previewImg').src = croppedData;
			fetch(croppedData).then(res => res.blob()).then(blob => {
				let file = new File([blob], 'cropped.jpg', { type: 'image/jpeg' });
				let dt = new DataTransfer();
				dt.items.add(file);
				document.getElementById('profilePhoto').files = dt.files;
				cropperImageFile = croppedData;
			});
			modal.style.display = 'none';
		}
	});
}

function togglePassword(fieldId, icon) {
	let field = document.getElementById(fieldId);
	if (field.type === 'password') {
		field.type = 'text';
		icon.classList.remove('fa-eye');
		icon.classList.add('fa-eye-slash');
	} else {
		field.type = 'password';
		icon.classList.remove('fa-eye-slash');
		icon.classList.add('fa-eye');
	}
}
</script>
@endsection