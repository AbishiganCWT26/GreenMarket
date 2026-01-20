@extends('facilitator.layouts.facilitator_master')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="row">
	<div class="col-lg-4 mb-4">
		<div class="dashboard-card">
			<div class="card-header">
				<h5><i class="fa-solid fa-id-card me-2"></i> Profile Photo</h5>
			</div>
			<div class="card-body text-center">
				<div class="profile-photo-wrapper mb-3">
					<img src="{{ asset('uploads/profile_pictures/' . (Auth::user()->profile_photo ?? 'default-avatar.png')) }}"
						 class="profile-photo-large"
						 id="photo-preview"
						 onerror="this.src='{{ asset('assets/icons/facilitator-icon.svg') }}'">
					<div class="photo-overlay">
						<button class="btn-change-photo" onclick="document.getElementById('profile_photo').click()">
							<i class="fa-solid fa-camera"></i>
						</button>
					</div>
				</div>
				<form action="{{ route('facilitator.profile.photo') }}" method="POST" enctype="multipart/form-data" id="photoForm">
					@csrf
					<input type="file" name="profile_photo" id="profile_photo" accept="image/*" style="display: none;">
					<button type="submit" class="btn-primary mt-2" style="display: none;" id="uploadBtn">
						<i class="fa-solid fa-upload me-2"></i>Upload Photo
					</button>
				</form>
				<p class="text-muted mt-3 mb-0">
					<small>Max file size: 2MB • Formats: JPG, PNG, GIF</small>
				</p>
			</div>
		</div>

		<div class="dashboard-card mt-4">
			<div class="card-header">
				<h5><i class="fa-solid fa-shield-alt me-2"></i> Account Security</h5>
			</div>
			<div class="card-body">
				<div class="security-info">
					<div class="security-item">
						<div class="security-icon">
							<i class="fa-solid fa-key"></i>
						</div>
						<div>
							<h6>Password</h6>
							<p class="text-muted mb-0">Last changed: Never</p>
						</div>
						<button class="btn-action" onclick="changePassword()">
							<i class="fa-solid fa-pencil"></i>
						</button>
					</div>
					<div class="security-item">
						<div class="security-icon">
							<i class="fa-solid fa-mobile-screen"></i>
						</div>
						<div>
							<h6>Mobile Verification</h6>
							<p class="text-muted mb-0">Verified: {{ $facilitator->primary_mobile ?? 'N/A' }}</p>
						</div>
						<span class="badge bg-success">
							<i class="fa-solid fa-check"></i>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-8 mb-4">
		<div class="dashboard-card">
			<div class="card-header">
				<h5><i class="fa-solid fa-user-pen me-2"></i> Profile Information</h5>
			</div>
			<div class="card-body">
				<form action="{{ route('facilitator.profile.update') }}" method="POST" id="profileForm">
					@csrf
					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label">Full Name *</label>
							<input type="text" class="form-control" name="name" value="{{ $facilitator->name ?? '' }}" required>
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">NIC Number *</label>
							<input type="text" class="form-control" value="{{ $facilitator->nic_no ?? '' }}" readonly disabled>
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">Email Address *</label>
							<input type="email" class="form-control" name="email" value="{{ $facilitator->email ?? '' }}" required>
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">Username</label>
							<input type="text" class="form-control" value="{{ Auth::user()->username ?? '' }}" readonly disabled>
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">Primary Mobile *</label>
							<input type="text" class="form-control" name="primary_mobile" value="{{ $facilitator->primary_mobile ?? '' }}" required>
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">WhatsApp Number</label>
							<input type="text" class="form-control" name="whatsapp_number" value="{{ $facilitator->whatsapp_number ?? '' }}">
						</div>
						<div class="col-12 mb-3">
							<label class="form-label">Assigned Division *</label>
							<input type="text" class="form-control" name="assigned_division" value="{{ $facilitator->assigned_division ?? '' }}" required>
						</div>
						<div class="col-12">
							<button type="submit" class="btn-primary">
								<i class="fa-solid fa-save me-2"></i>Update Profile
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="dashboard-card mt-4">
			<div class="card-header">
				<h5><i class="fa-solid fa-chart-line me-2"></i> Activity Summary</h5>
			</div>
			<div class="card-body">
				<div class="row text-center">
					<div class="col-6 col-md-3 mb-3">
						<div class="stat-circle">
							<i class="fa-solid fa-users"></i>
							<h4>0</h4>
							<p>Users Trained</p>
						</div>
					</div>
					<div class="col-6 col-md-3 mb-3">
						<div class="stat-circle">
							<i class="fa-solid fa-layer-group"></i>
							<h4>0</h4>
							<p>Categories Added</p>
						</div>
					</div>
					<div class="col-6 col-md-3 mb-3">
						<div class="stat-circle">
							<i class="fa-solid fa-flag"></i>
							<h4>0</h4>
							<p>Complaints Resolved</p>
						</div>
					</div>
					<div class="col-6 col-md-3 mb-3">
						<div class="stat-circle">
							<i class="fa-solid fa-calendar-check"></i>
							<h4>{{ \Carbon\Carbon::parse(Auth::user()->created_at)->diffInDays(now()) }}</h4>
							<p>Days Active</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
.profile-photo-wrapper {
	position: relative;
	display: inline-block;
	margin: 0 auto;
}

.profile-photo-large {
	width: 180px;
	height: 180px;
	border-radius: 20px;
	object-fit: cover;
	border: 5px solid #ffffff;
	box-shadow: var(--shadow-md);
	transition: var(--transition);
}

.profile-photo-wrapper:hover .profile-photo-large {
	transform: scale(1.05);
	border-color: #10B981;
}

.photo-overlay {
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: rgba(0,0,0,0.5);
	border-radius: 20px;
	display: flex;
	align-items: center;
	justify-content: center;
	opacity: 0;
	transition: var(--transition);
}

.profile-photo-wrapper:hover .photo-overlay {
	opacity: 1;
}

.btn-change-photo {
	width: 50px;
	height: 50px;
	border-radius: 50%;
	background: #10B981;
	color: white;
	border: none;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 1.2rem;
	cursor: pointer;
	transition: var(--transition);
}

.btn-change-photo:hover {
	background: #059669;
	transform: rotate(15deg) scale(1.1);
}

.security-info {
	display: flex;
	flex-direction: column;
	gap: 1rem;
}

.security-item {
	display: flex;
	align-items: center;
	gap: 1rem;
	padding: 1rem;
	background: #f8fafc;
	border-radius: 10px;
	transition: var(--transition);
}

.security-item:hover {
	background: #f1f5f9;
	transform: translateX(5px);
}

.security-icon {
	width: 40px;
	height: 40px;
	border-radius: 10px;
	background: rgba(16,185,129,0.1);
	color: #10B981;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 1.2rem;
}

.security-item h6 {
	font-weight: 600;
	color: var(--text-color);
	margin-bottom: 0.25rem;
	font-size: 0.95rem;
}

.security-item p {
	font-size: 0.85rem;
	margin: 0;
}

.stat-circle {
	padding: 1.5rem;
	background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
	border-radius: 15px;
	transition: var(--transition);
}

.stat-circle:hover {
	transform: translateY(-8px);
	box-shadow: 0 10px 25px rgba(14,165,233,0.2);
	background: linear-gradient(135deg, #e0f2fe, #bae6fd);
}

.stat-circle i {
	font-size: 2rem;
	color: #0ea5e9;
	margin-bottom: 0.75rem;
}

.stat-circle h4 {
	font-weight: 700;
	color: var(--text-color);
	margin-bottom: 0.25rem;
	font-size: 1.8rem;
}

.stat-circle p {
	font-size: 0.9rem;
	color: var(--muted);
	margin: 0;
}

@media (max-width: 767px) {
	.profile-photo-large {
		width: 150px;
		height: 150px;
	}

	.stat-circle {
		padding: 1rem;
	}

	.stat-circle i {
		font-size: 1.5rem;
	}

	.stat-circle h4 {
		font-size: 1.5rem;
	}
}
</style>

<script>
document.getElementById('profile_photo').addEventListener('change', function(e) {
	const file = e.target.files[0];
	if (file) {
		const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];

		if (!validTypes.includes(file.type)) {
			Swal.fire({
				icon: 'error',
				title: 'Invalid File Type',
				text: 'Please upload only JPEG, PNG, JPG or GIF images.',
				confirmButtonColor: '#10B981'
			});
			this.value = '';
			return;
		}

		if (file.size > 2 * 1024 * 1024) {
			Swal.fire({
				icon: 'error',
				title: 'File Too Large',
				text: 'Image size should be less than 2MB.',
				confirmButtonColor: '#10B981'
			});
			this.value = '';
			return;
		}

		const reader = new FileReader();
		reader.onload = function(e) {
			const preview = document.getElementById('photo-preview');
			preview.src = e.target.result;
			preview.style.transform = 'scale(1.1)';
			setTimeout(() => {
				preview.style.transform = '';
			}, 300);

			document.getElementById('uploadBtn').style.display = 'inline-block';
		};
		reader.readAsDataURL(file);
	}
});

document.getElementById('photoForm').addEventListener('submit', function(e) {
	e.preventDefault();

	Swal.fire({
		title: 'Uploading Photo...',
		html: `
			<div class="text-center">
				<div class="spinner-border text-primary mb-3"></div>
				<p>Please wait while we upload your photo...</p>
			</div>
		`,
		showConfirmButton: false,
		allowOutsideClick: false,
		background: '#ffffff',
		color: '#0f1724'
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
			toastr.success('Profile photo updated successfully!');
			setTimeout(() => location.reload(), 1000);
		} else {
			Swal.fire({
				icon: 'error',
				title: 'Upload Failed',
				text: data.message || 'Failed to upload photo',
				confirmButtonColor: '#10B981'
			});
		}
	})
	.catch(error => {
		Swal.close();
		Swal.fire({
			icon: 'error',
			title: 'Upload Failed',
			text: 'An error occurred while uploading the photo.',
			confirmButtonColor: '#10B981'
		});
	});
});

document.getElementById('profileForm').addEventListener('submit', function(e) {
	e.preventDefault();

	Swal.fire({
		title: 'Updating Profile...',
		html: `
			<div class="text-center">
				<div class="spinner-border text-primary mb-3"></div>
				<p>Saving your changes...</p>
			</div>
		`,
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
		if (data.success || data.message) {
			toastr.success('Profile updated successfully!');
			setTimeout(() => location.reload(), 1000);
		} else {
			Swal.fire({
				icon: 'error',
				title: 'Update Failed',
				text: 'Failed to update profile. Please try again.',
				confirmButtonColor: '#10B981'
			});
		}
	})
	.catch(error => {
		Swal.close();
		Swal.fire({
			icon: 'error',
			title: 'Update Failed',
			text: 'An error occurred while updating the profile.',
			confirmButtonColor: '#10B981'
		});
	});
});

function changePassword() {
	Swal.fire({
		title: 'Change Password',
		html: `
			<div class="text-start">
				<div class="mb-3">
					<label class="form-label">Current Password *</label>
					<input type="password" class="form-control swal2-input" id="currentPassword" required>
				</div>
				<div class="mb-3">
					<label class="form-label">New Password *</label>
					<input type="password" class="form-control swal2-input" id="newPassword" required>
				</div>
				<div class="mb-3">
					<label class="form-label">Confirm New Password *</label>
					<input type="password" class="form-control swal2-input" id="confirmPassword" required>
				</div>
			</div>
		`,
		showCancelButton: true,
		confirmButtonText: 'Change Password',
		confirmButtonColor: '#10B981',
		cancelButtonColor: '#6b7280',
		background: '#ffffff',
		color: '#0f1724',
		width: '500px',
		preConfirm: () => {
			const current = document.getElementById('currentPassword').value;
			const newPass = document.getElementById('newPassword').value;
			const confirm = document.getElementById('confirmPassword').value;

			if (!current || !newPass || !confirm) {
				Swal.showValidationMessage('All fields are required');
				return false;
			}

			if (newPass !== confirm) {
				Swal.showValidationMessage('New passwords do not match');
				return false;
			}

			if (newPass.length < 6) {
				Swal.showValidationMessage('Password must be at least 6 characters long');
				return false;
			}

			return { current, newPass };
		}
	}).then(result => {
		if (result.isConfirmed) {
			Swal.fire({
				title: 'Success!',
				text: 'Password changed successfully.',
				icon: 'success',
				confirmButtonColor: '#10B981'
			});
		}
	});
}
</script>
@endsection
