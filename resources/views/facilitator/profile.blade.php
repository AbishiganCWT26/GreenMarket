@extends('facilitator.layouts.facilitator_master')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<link rel="stylesheet" href="{{ asset('css/Facilitator/profile.css') }}">

<div class="profile-container">
	<div class="top-bar">
		<div class="header-title">
			<h3>Facilitator Profile</h3>
			<p>Manage your personal information and account settings</p>
		</div>
		<div class="badge-status">
			<i class="fa-regular fa-circle-check"></i>
			<span>Active Account</span>
		</div>
	</div>

	<div class="profile-grid">
		<div class="profile-card profile-side">
			<div class="avatar-wrapper">
				<div class="avatar-container">
					<img src="{{ asset('uploads/profile_pictures/' . (Auth::user()->profile_photo ?? 'default-avatar.png')) }}"
						 id="photo-preview"
						 onerror="this.src='{{ asset('assets/icons/facilitator-icon.svg') }}'">
					<button class="avatar-edit-btn" onclick="document.getElementById('profile_photo').click()">
						<i class="fa-solid fa-camera"></i>
					</button>
				</div>
				<h3>{{ $facilitator->name ?? 'Facilitator Name' }}</h3>
				<span class="role-tag">Agricultural Facilitator</span>
				<form action="{{ route('facilitator.profile.photo') }}" method="POST" enctype="multipart/form-data" id="photoForm">
					@csrf
					<input type="file" name="profile_photo" id="profile_photo" accept="image/*" hidden>
					<button type="submit" class="save-photo-btn" id="uploadBtn">
						<i class="fa-solid fa-cloud-arrow-up"></i>
						Save Photo
					</button>
				</form>
			</div>

			<div class="info-list">
				<div class="info-row">
					<div class="info-icon">
						<i class="fa-solid fa-id-card"></i>
					</div>
					<div class="info-content">
						<span class="info-label">NIC Number</span>
						<span class="info-value">{{ $facilitator->nic_no ?? 'Not Provided' }}</span>
					</div>
				</div>
				<div class="info-row">
					<div class="info-icon">
						<i class="fa-solid fa-calendar-days"></i>
					</div>
					<div class="info-content">
						<span class="info-label">Registered At</span>
						<span class="info-value">{{ $facilitator->created_at ? $facilitator->created_at->format('Y-m-d') : 'N/A' }}</span>
					</div>
				</div>
			</div>

			<button class="password-change-btn" onclick="changePassword()">
				<i class="fa-solid fa-lock"></i>
				Change Password
			</button>
		</div>

		<div class="profile-card profile-main-content">
			<div class="card-header">
				<i class="fa-regular fa-pen-to-square"></i>
				<h4>Personal Information</h4>
			</div>

			<form action="{{ route('facilitator.profile.update') }}" method="POST" id="profileForm">
				@csrf
				<div class="form-grid">
					<div class="form-field">
						<label>
							<i class="fa-regular fa-user"></i>
							Full Name
						</label>
						<input type="text" name="name" value="{{ $facilitator->name ?? '' }}" placeholder="Enter full name" required>
					</div>

					<div class="form-field">
						<label>
							<i class="fa-solid fa-user-tag"></i>
							Username
						</label>
						<input type="text" name="username" value="{{ Auth::user()->username ?? '' }}" placeholder="Enter username" required>
					</div>

					<div class="form-field">
						<label>
							<i class="fa-regular fa-envelope"></i>
							Email Address
						</label>
						<input type="email" name="email" value="{{ $facilitator->email ?? '' }}" placeholder="Enter email" required>
					</div>

					<div class="form-field">
						<label>
							<i class="fa-solid fa-phone"></i>
							Primary Mobile
						</label>
						<div class="input-with-action">
							<input type="text" name="primary_mobile" id="primary_mobile" value="{{ $facilitator->primary_mobile ?? '' }}" placeholder="Enter mobile number" required>
							<button type="button" class="verify-btn" id="verify_primary_btn" style="display: none;" onclick="sendOTP('primary_mobile')">Verify</button>
						</div>
						<div id="primary_mobile_status" class="verification-status">
							<i class="fa-solid fa-circle-check text-success"></i> Verified
						</div>
					</div>

					<div class="form-field">
						<label>
							<i class="fa-brands fa-whatsapp"></i>
							WhatsApp Number
						</label>
						<div class="input-with-action">
							<input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ $facilitator->whatsapp_number ?? '' }}" placeholder="Enter WhatsApp number">
							<button type="button" class="verify-btn" id="verify_whatsapp_btn" style="display: none;" onclick="sendOTP('whatsapp_number')">Verify</button>
						</div>
						<div id="whatsapp_number_status" class="verification-status">
							@if($facilitator->whatsapp_number)
								<i class="fa-solid fa-circle-check text-success"></i> Verified
							@endif
						</div>
					</div>

					<div class="form-field">
						<label>
							<i class="fa-solid fa-id-card"></i>
							NIC Number
						</label>
						<input type="text" name="nic_no" id="nic_no" value="{{ $facilitator->nic_no ?? '' }}" placeholder="Enter NIC number" required>
						<div id="nicStatus" class="nic-status" style="font-size: 0.75rem; margin-top: 4px;"></div>
					</div>
				</div>

				<div class="form-footer">
					<button type="submit" class="save-btn">
						<i class="fa-regular fa-floppy-disk"></i>
						Update Profile
					</button>
				</div>
			</form>

			<div class="divider"></div>

			<div class="card-header mt-4">
				<i class="fa-solid fa-location-dot"></i>
				<h4>Assignment Details</h4>
				<small class="text-muted ms-auto">(View Only)</small>
			</div>

			<div class="assignment-table-container">
				<table class="assignment-table">
					<thead>
						<tr>
							<th>District</th>
							<th>Divisional Secretariat</th>
							<th>GN Division</th>
							<th>GN Division Code</th>
						</tr>
					</thead>
					<tbody>
						@forelse($facilitator->assignments as $assignment)
						<tr>
							<td>{{ $assignment->district }}</td>
							<td>{{ $assignment->divisional_secretariat }}</td>
							<td>{{ $assignment->gn_division }}</td>
							<td>{{ $assignment->gn_division_code }}</td>
						</tr>
						@empty
						<tr>
							<td colspan="4" class="text-center">No assignments found</td>
						</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>


<script src="{{ asset('js/form-validation.js') }}"></script>
<script>
document.getElementById('profile_photo').addEventListener('change', function(e) {
	const file = e.target.files[0];
	if (file) {
		const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
		if (!validTypes.includes(file.type)) {
			Swal.fire({
				@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
				title: 'Invalid File',
				text: 'Please upload only JPEG, PNG, JPG or GIF images.',
				confirmButtonColor: '#10B981'
			});
			this.value = '';
			return;
		}
		if (file.size > 2 * 1024 * 1024) {
			Swal.fire({
				@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
				title: 'File Too Large',
				text: 'Image size should be less than 2MB.',
				confirmButtonColor: '#10B981'
			});
			this.value = '';
			return;
		}
		const reader = new FileReader();
		reader.onload = function(e) {
			document.getElementById('photo-preview').src = e.target.result;
			document.getElementById('uploadBtn').style.display = 'inline-flex';
		};
		reader.readAsDataURL(file);
	}
});

document.getElementById('photoForm').addEventListener('submit', function(e) {
	e.preventDefault();
	Swal.fire({
		title: 'Uploading...',
		html: '<div class="spinner-border text-primary"></div>',
		showConfirmButton: false,
		allowOutsideClick: false
	});
	const formData = new FormData(this);
	fetch(this.action, {
		method: 'POST',
		body: formData,
		headers: {
			'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
		}
	})
	.then(response => response.json())
	.then(data => {
		Swal.close();
		if (data.success) {
			Swal.fire({
				@if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
				title: 'Success!',
				text: 'Profile photo updated successfully!',
				confirmButtonColor: '#10B981'
			}).then(() => location.reload());
		} else {
			Swal.fire({
				@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
				title: 'Failed',
				text: data.message || 'Failed to upload photo',
				confirmButtonColor: '#10B981'
			});
		}
	})
	.catch(() => {
		Swal.close();
		Swal.fire({
			@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
			title: 'Error',
			text: 'An error occurred while uploading.',
			confirmButtonColor: '#10B981'
		});
	});
});

document.getElementById('profileForm').addEventListener('submit', function(e) {
	e.preventDefault();

	// Check if verification is needed
	const primaryMobile = document.getElementById('primary_mobile').value;
	const originalPrimary = "{{ $facilitator->primary_mobile }}";
	const whatsappNumber = document.getElementById('whatsapp_number').value;
	const originalWhatsapp = "{{ $facilitator->whatsapp_number }}";

	if (primaryMobile !== originalPrimary && !primaryVerified) {
		Swal.fire({
			@if(file_exists(public_path('assets/icons/Gif/alert1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/alert1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'warning' @endif,
			title: 'Action Required',
			text: 'Please verify your new primary mobile number first.',
			confirmButtonColor: '#10B981'
		});
		return;
	}

	if (whatsappNumber !== originalWhatsapp && whatsappNumber !== "" && !whatsappVerified) {
		Swal.fire({
			@if(file_exists(public_path('assets/icons/Gif/alert1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/alert1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'warning' @endif,
			title: 'Action Required',
			text: 'Please verify your new WhatsApp number first.',
			confirmButtonColor: '#10B981'
		});
		return;
	}

	Swal.fire({
		title: 'Updating...',
		html: '<div class="spinner-border text-primary"></div>',
		showConfirmButton: false,
		allowOutsideClick: false
	});
	const formData = new FormData(this);
	fetch(this.action, {
		method: 'POST',
		body: formData,
		headers: {
			'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
			'Accept': 'application/json'
		}
	})
	.then(response => response.json())
	.then(data => {
		Swal.close();
		if (data.success) {
			Swal.fire({
				@if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
				title: 'Success!',
				text: data.message || 'Profile updated successfully!',
				confirmButtonColor: '#10B981'
			}).then(() => location.reload());
		} else {
			Swal.fire({
				@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
				title: 'Failed',
				text: data.message || 'Failed to update profile.',
				confirmButtonColor: '#10B981'
			});
		}
	})
	.catch(err => {
		Swal.close();
		Swal.fire({
			@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
			title: 'Error',
			text: 'An error occurred while updating.',
			confirmButtonColor: '#10B981'
		});
	});
});

let primaryVerified = true;
let whatsappVerified = true;

document.getElementById('primary_mobile').addEventListener('input', function() {
	const current = this.value;
	const original = "{{ $facilitator->primary_mobile }}";
	const btn = document.getElementById('verify_primary_btn');
	const status = document.getElementById('primary_mobile_status');
	
	if (current !== original) {
		btn.style.display = 'block';
		status.style.display = 'none';
		primaryVerified = false;
	} else {
		btn.style.display = 'none';
		status.style.display = 'block';
		primaryVerified = true;
	}
});

document.getElementById('whatsapp_number').addEventListener('input', function() {
	const current = this.value;
	const original = "{{ $facilitator->whatsapp_number }}";
	const btn = document.getElementById('verify_whatsapp_btn');
	const status = document.getElementById('whatsapp_number_status');
	
	if (current !== original && current !== "") {
		btn.style.display = 'block';
		status.style.display = 'none';
		whatsappVerified = false;
	} else {
		btn.style.display = 'none';
		status.innerHTML = original ? '<i class="fa-solid fa-circle-check text-success"></i> Verified' : '';
		status.style.display = 'block';
		whatsappVerified = true;
	}
});

function sendOTP(type) {
	const number = document.getElementById(type).value;
	if (number.length < 10) {
		Swal.fire({ title: 'Error', html: 'Invalid phone number', @if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif });
		return;
	}

	Swal.fire({
		title: 'Sending OTP...',
		didOpen: () => {
			Swal.showLoading();
		}
	});

	fetch("{{ route('facilitator.profile.send-otp') }}", {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
		},
		body: JSON.stringify({ type: type, number: number })
	})
	.then(res => res.json())
	.then(data => {
		if (data.success) {
			Swal.fire({
				title: 'Enter OTP',
				input: 'text',
				inputLabel: 'OTP sent to your primary mobile: ' + "{{ $facilitator->primary_mobile }}",
				showCancelButton: true,
				confirmButtonText: 'Verify',
				confirmButtonColor: '#10B981',
				showLoaderOnConfirm: true,
				preConfirm: (otp) => {
					return fetch("{{ route('facilitator.profile.verify-otp') }}", {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
						},
						body: JSON.stringify({ type: type, otp: otp, number: number })
					})
					.then(res => res.json())
					.then(data => {
						if (!data.success) {
							throw new Error(data.message);
						}
						return data;
					})
					.catch(error => {
						Swal.showValidationMessage(`Request failed: ${error}`);
					});
				}
			}).then(result => {
				if (result.isConfirmed) {
					if (type === 'primary_mobile') {
						primaryVerified = true;
						document.getElementById('verify_primary_btn').style.display = 'none';
						document.getElementById('primary_mobile_status').innerHTML = '<i class="fa-solid fa-circle-check text-success"></i> Verified';
						document.getElementById('primary_mobile_status').style.display = 'block';
					} else {
						whatsappVerified = true;
						document.getElementById('verify_whatsapp_btn').style.display = 'none';
						document.getElementById('whatsapp_number_status').innerHTML = '<i class="fa-solid fa-circle-check text-success"></i> Verified';
						document.getElementById('whatsapp_number_status').style.display = 'block';
					}
					Swal.fire({ title: 'Verified!', html: 'Your number has been verified.', @if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif });
				}
			});
		} else {
			Swal.fire({ title: 'Error', html: data.message, @if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif });
		}
	})
	.catch(err => Swal.fire({ title: 'Error', html: 'Something went wrong', @if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif }));
}

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

function changePassword() {
	Swal.fire({
		title: 'Change Password',
		html: `
			<div class="text-start">
				<div class="mb-3">
					<label class="form-label">New Password</label>
					<div class="password-container" style="position: relative;">
						<input type="password" class="form-control" id="newPassword" placeholder="New Password" required oninput="updateStrength(this.value)">
						<i class="fa-regular fa-eye password-toggle" id="toggleNewPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;" onclick="togglePasswordVisibility('newPassword', 'toggleNewPassword')"></i>
					</div>
					<div class="password-strength mt-3">
						<div class="d-flex justify-content-between align-items-center mb-1">
							<small>Strength: <span id="strengthText">None</span></small>
						</div>
						<div class="strength-bar" id="strengthBar" style="height: 5px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
							<div class="strength-fill" style="width: 0%; height: 100%; transition: width 0.3s;"></div>
						</div>
					</div>
					<div class="requirements mt-3">
						<h6 class="mb-2" style="font-size: 0.9rem;">Requirements:</h6>
						<ul class="list-unstyled mb-0" style="font-size: 0.8rem; display: grid; grid-template-columns: 1fr 1fr; gap: 5px;">
							<li id="rule-length" class="text-danger"><i class="fas fa-times me-1"></i> 8+ chars</li>
							<li id="rule-number" class="text-danger"><i class="fas fa-times me-1"></i> Number</li>
							<li id="rule-capital" class="text-danger"><i class="fas fa-times me-1"></i> Capital</li>
							<li id="rule-lowercase" class="text-danger"><i class="fas fa-times me-1"></i> Lowercase</li>
							<li id="rule-special" class="text-danger"><i class="fas fa-times me-1"></i> Special</li>
							<li id="rule-no-space" class="text-danger"><i class="fas fa-times me-1"></i> No spaces</li>
							<li id="rule-no-repeat" class="text-danger"><i class="fas fa-times me-1"></i> No repeat</li>
							<li id="rule-no-sequence" class="text-danger"><i class="fas fa-times me-1"></i> No sequence</li>
							<li id="rule-not-common" class="text-danger"><i class="fas fa-times me-1"></i> Not common</li>
							<li id="rule-no-links" class="text-danger"><i class="fas fa-times me-1"></i> No links</li>
							<li id="rule-no-personal" class="text-danger"><i class="fas fa-times me-1"></i> No Personal Info</li>
						</ul>
					</div>
				</div>
				<div class="mb-3">
					<label class="form-label">Confirm Password</label>
					<div class="password-container" style="position: relative;">
						<input type="password" class="form-control" id="confirmPassword" placeholder="Confirm Password" required>
						<i class="fa-regular fa-eye password-toggle" id="toggleConfirmPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;" onclick="togglePasswordVisibility('confirmPassword', 'toggleConfirmPassword')"></i>
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
		`,
		showCancelButton: true,
		confirmButtonText: 'Change',
		confirmButtonColor: '#10B981',
		cancelButtonColor: '#6b7280',
		showLoaderOnConfirm: true,
		didOpen: () => {
			const confirmInput = document.getElementById('confirmPassword');
			confirmInput.addEventListener('input', () => {
				const pass = document.getElementById('newPassword').value;
				const confirm = confirmInput.value;
				const matchIndicator = document.getElementById('passwordMatch');
				const success = matchIndicator.querySelector('.text-success');
				const error = matchIndicator.querySelector('.text-danger');
				if (pass && confirm) {
					if (pass === confirm) {
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
			});
		},
		preConfirm: () => {
			const newPass = document.getElementById('newPassword').value;
			const confirm = document.getElementById('confirmPassword').value;

			if (newPass !== confirm) {
				Swal.showValidationMessage('Passwords do not match');
				return false;
			}
			
			// Re-verify strength
			const result = calculateStrength(newPass);
			if (!result.allValid) {
				Swal.showValidationMessage('Please meet all password requirements');
				return false;
			}

			return fetch("{{ route('facilitator.profile.update.password') }}", {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
					'Accept': 'application/json'
				},
				body: JSON.stringify({ new_password: newPass })
			})
			.then(response => {
				if (!response.ok) {
					return response.json().then(json => { throw new Error(json.message || 'Failed to update password'); });
				}
				return response.json();
			})
			.catch(error => {
				Swal.showValidationMessage(`Request failed: ${error}`);
			});
		}
	}).then(result => {
		if (result.isConfirmed) {
			Swal.fire({ @if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif, title: 'Success!', text: 'Password changed successfully.', confirmButtonColor: '#10B981' }).then(() => location.reload());
		}
	});
}

function calculateStrength(password) {
    const username = "{{ Auth::user()->username }}";
    const email = "{{ Auth::user()->email }}";
    
    const result = validateAdvancedPassword(password, { username, email });
    updatePasswordRuleFeedback(result);
    return result;
}

function updateStrength(password) {
    const result = calculateStrength(password);
    const strengthText = document.getElementById('strengthText');
    const strengthBar = document.getElementById('strengthBar');
    
    if (strengthText) {
        strengthText.textContent = result.strengthText;
        strengthText.style.color = result.color;
    }
    if (strengthBar) {
        const fill = strengthBar.querySelector('.strength-fill');
        if (fill) {
            fill.style.backgroundColor = result.color;
            fill.style.width = result.percent + '%';
        }
    }
}

function validateNIC(nic) {
    if (!nic) return false;
    nic = nic.trim().toUpperCase();
    const oldNicPattern = /^[0-9]{9}[VX]$/;
    const newNicPattern = /^[0-9]{12}$/;
    if (oldNicPattern.test(nic)) {
        const days = parseInt(nic.substr(2, 3));
        return (days > 0 && days <= 366) || (days > 500 && days <= 866);
    }
    if (newNicPattern.test(nic)) {
        const year = parseInt(nic.substr(0, 4));
        const days = parseInt(nic.substr(4, 3));
        return year >= 1900 && year <= 2100 && ((days > 0 && days <= 366) || (days > 500 && days <= 866));
    }
    return false;
}

document.getElementById('nic_no').addEventListener('input', function() {
    const status = document.getElementById('nicStatus');
    if (validateNIC(this.value)) {
        status.innerHTML = '<span style="color: #10B981;"><i class="fas fa-check-circle"></i> Valid NIC</span>';
    } else {
        status.innerHTML = '<span style="color: #ef4444;"><i class="fas fa-times-circle"></i> Invalid NIC format</span>';
    }
});
</script>
@endsection
