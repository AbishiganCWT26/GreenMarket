@extends('buyer.layouts.buyer_master')

@section('title', 'Change Profile Photo')
@section('page-title', 'Change Profile Photo')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
	:root {
		--primary-green: #10B981;
		--dark-green: #059669;
		--body-bg: #f6f8fa;
		--card-bg: #ffffff;
		--text-color: #0f1724;
		--muted: #6b7280;
		--shadow-sm: 0 1px 3px rgba(15,23,36,0.04);
		--shadow-md: 0 7px 15px rgba(15,23,36,0.08);
		--transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
	}

	.photo-container {
		max-width: 600px;
		margin: 0 auto;
		padding: 20px;
		animation: fadeIn 0.5s ease;
	}

	@keyframes fadeIn {
		from { opacity: 0; transform: translateY(20px); }
		to { opacity: 1; transform: translateY(0); }
	}

	.photo-card {
		background: var(--card-bg);
		border-radius: 16px;
		overflow: hidden;
		box-shadow: var(--shadow-sm);
		border: 1px solid rgba(0,0,0,0.05);
		transition: var(--transition-smooth);
	}

	.photo-card:hover {
		box-shadow: var(--shadow-md);
		transform: translateY(-2px);
	}

	.photo-header {
		background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
		color: white;
		padding: 25px;
		text-align: center;
	}

	.photo-header h4 {
		margin: 0;
		font-weight: 600;
		font-size: 1.3rem;
	}

	.photo-body {
		padding: 30px;
	}

	.photo-preview-container {
		text-align: center;
		margin-bottom: 30px;
	}

	.photo-preview-wrapper {
		width: 200px;
		height: 200px;
		margin: 0 auto 20px;
		position: relative;
		border-radius: 50%;
		overflow: hidden;
		border: 4px solid var(--primary-green);
		box-shadow: 0 10px 30px rgba(16, 185, 129, 0.2);
		transition: var(--transition-smooth);
	}

	.photo-preview-wrapper:hover {
		transform: scale(1.03);
		box-shadow: 0 15px 40px rgba(16, 185, 129, 0.3);
	}

	#photo-preview {
		width: 100%;
		height: 100%;
		object-fit: cover;
		transition: var(--transition-smooth);
	}

	.photo-upload-area {
		border: 2px dashed #cbd5e1;
		border-radius: 12px;
		padding: 40px 20px;
		text-align: center;
		background-color: #f8fafc;
		cursor: pointer;
		transition: var(--transition-smooth);
		margin-bottom: 25px;
		position: relative;
		overflow: hidden;
	}

	.photo-upload-area:hover {
		border-color: var(--primary-green);
		background-color: #f0fdf4;
		transform: translateY(-3px);
	}

	.photo-upload-area::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.05));
		opacity: 0;
		transition: var(--transition-smooth);
	}

	.photo-upload-area:hover::before {
		opacity: 1;
	}

	.upload-icon {
		font-size: 48px;
		color: var(--primary-green);
		margin-bottom: 15px;
		transition: var(--transition-smooth);
	}

	.photo-upload-area:hover .upload-icon {
		transform: scale(1.1);
		color: var(--dark-green);
	}

	.photo-upload-area h5 {
		color: var(--text-color);
		margin-bottom: 10px;
		font-weight: 600;
	}

	.photo-upload-area p {
		color: var(--muted);
		margin-bottom: 15px;
		font-size: 0.9rem;
	}

	.btn-select-photo {
		background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
		color: white;
		border: none;
		padding: 10px 24px;
		border-radius: 8px;
		font-weight: 600;
		transition: var(--transition-smooth);
		box-shadow: 0 6px 20px rgba(16, 185, 129, 0.2);
	}

	.btn-select-photo:hover {
		transform: translateY(-2px);
		box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
		background: linear-gradient(135deg, var(--dark-green), #047857);
	}

	.photo-guidelines {
		background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
		border-left: 4px solid var(--blue);
		padding: 20px;
		border-radius: 12px;
		margin-top: 25px;
	}

	.photo-guidelines h6 {
		color: #0369a1;
		margin-bottom: 12px;
		font-weight: 600;
		font-size: 0.95rem;
	}

	.photo-guidelines ul {
		margin: 0;
		padding-left: 20px;
	}

	.photo-guidelines li {
		color: var(--text-color);
		margin-bottom: 6px;
		font-size: 0.85rem;
	}

	.photo-actions {
		display: flex;
		gap: 15px;
		justify-content: center;
		margin-top: 30px;
		padding-top: 25px;
		border-top: 1px solid #e2e8f0;
		flex-wrap: wrap;
	}

	.btn-update,
	.btn-remove,
	.btn-back {
		padding: 12px 28px;
		border-radius: 10px;
		font-weight: 600;
		font-size: 0.95rem;
		transition: var(--transition-smooth);
		text-decoration: none;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		gap: 8px;
		border: none;
		cursor: pointer;
	}

	.btn-update {
		background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
		color: white;
		box-shadow: 0 6px 20px rgba(16, 185, 129, 0.2);
	}

	.btn-update:hover {
		transform: translateY(-2px);
		box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
		background: linear-gradient(135deg, var(--dark-green), #047857);
	}

	.btn-remove {
		background: linear-gradient(135deg, #ef4444, #dc2626);
		color: white;
		box-shadow: 0 6px 20px rgba(239, 68, 68, 0.2);
	}

	.btn-remove:hover {
		transform: translateY(-2px);
		box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
		background: linear-gradient(135deg, #dc2626, #b91c1c);
	}

	.btn-back {
		background: var(--body-bg);
		color: var(--text-color);
		border: 1px solid #e2e8f0;
	}

	.btn-back:hover {
		background: #e2e8f0;
		transform: translateY(-2px);
	}

	.profile-photo-link {
		display: inline-block;
		position: relative;
	}

	.profile-photo-link::after {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		border-radius: 50%;
		background: rgba(16, 185, 129, 0.1);
		opacity: 0;
		transition: var(--transition-smooth);
	}

	.profile-photo-link:hover::after {
		opacity: 1;
	}

	@media (max-width: 1200px) {
		.photo-container {
			max-width: 550px;
		}
	}

	@media (max-width: 992px) {
		.photo-container {
			max-width: 500px;
			padding: 15px;
		}

		.photo-header {
			padding: 20px;
		}

		.photo-body {
			padding: 25px;
		}

		.photo-preview-wrapper {
			width: 180px;
			height: 180px;
		}

		.photo-upload-area {
			padding: 30px 15px;
		}
	}

	@media (max-width: 768px) {
		.photo-container {
			max-width: 450px;
		}

		.photo-header h4 {
			font-size: 1.2rem;
		}

		.photo-body {
			padding: 20px;
		}

		.photo-preview-wrapper {
			width: 160px;
			height: 160px;
		}

		.upload-icon {
			font-size: 40px;
		}

		.photo-upload-area h5 {
			font-size: 1rem;
		}

		.photo-upload-area p {
			font-size: 0.85rem;
		}

		.photo-actions {
			flex-direction: column;
			gap: 10px;
		}

		.btn-update,
		.btn-remove,
		.btn-back {
			width: 100%;
			padding: 14px 24px;
		}
	}

	@media (max-width: 480px) {
		.photo-container {
			padding: 10px;
		}

		.photo-card {
			border-radius: 12px;
		}

		.photo-header {
			padding: 15px;
		}

		.photo-header h4 {
			font-size: 1.1rem;
		}

		.photo-body {
			padding: 15px;
		}

		.photo-preview-wrapper {
			width: 140px;
			height: 140px;
			border-width: 3px;
		}

		.photo-upload-area {
			padding: 20px 10px;
			border-radius: 10px;
		}

		.upload-icon {
			font-size: 36px;
			margin-bottom: 10px;
		}

		.photo-guidelines {
			padding: 15px;
			margin-top: 20px;
		}

		.photo-guidelines h6 {
			font-size: 0.9rem;
			margin-bottom: 10px;
		}

		.photo-guidelines li {
			font-size: 0.8rem;
		}

		.photo-actions {
			margin-top: 20px;
			padding-top: 20px;
		}
	}

	@media (max-width: 1000px) {
		.photo-container {
			max-width: 90%;
		}
	}

	.form-control-file {
		display: none;
	}

	.loading-overlay {
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: rgba(255, 255, 255, 0.9);
		display: flex;
		align-items: center;
		justify-content: center;
		z-index: 9999;
		opacity: 0;
		visibility: hidden;
		transition: var(--transition-smooth);
	}

	.loading-overlay.active {
		opacity: 1;
		visibility: visible;
	}

	.loading-spinner {
		width: 60px;
		height: 60px;
		border: 4px solid #f3f3f3;
		border-top: 4px solid var(--primary-green);
		border-radius: 50%;
		animation: spin 1s linear infinite;
	}

	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}

	.pulse-animation {
		animation: pulse 2s infinite;
	}

	@keyframes pulse {
		0%, 100% { transform: scale(1); }
		50% { transform: scale(1.05); }
	}

	.shake-animation {
		animation: shake 0.5s ease-in-out;
	}

	@keyframes shake {
		0%, 100% { transform: translateX(0); }
		25% { transform: translateX(-5px); }
		75% { transform: translateX(5px); }
	}
</style>
@endsection

@section('content')
<div class="photo-container">
	<div class="photo-card">
		<div class="photo-header">
			<h4><i class="fa-solid fa-camera me-2"></i> Change Profile Photo</h4>
		</div>

		<div class="photo-body">
			<div class="photo-preview-container">
				<div class="photo-preview-wrapper">
					<img id="photo-preview"
						 src="{{ Auth::user()->profile_photo ? asset('uploads/profile_pictures/' . Auth::user()->profile_photo) : asset('assets/images/default-avatar.png') }}"
						 alt="Profile Photo Preview"
						 onerror="this.src='{{ asset('uploads/profile_pictures/default-buyer.png') }}'">
				</div>
				<p class="text-muted">Preview your new profile photo here</p>
			</div>

			<form action="{{ route('buyer.profile.photo.update') }}" method="POST" enctype="multipart/form-data" id="photoForm">
				@csrf
				@method('PUT')

				<div class="photo-upload-area" onclick="document.getElementById('profile_photo').click()">
					<div class="upload-icon">
						<i class="fa-solid fa-cloud-arrow-up"></i>
					</div>
					<h5>Select New Photo</h5>
					<p>Click here or drag and drop to upload</p>
					<button type="button" class="btn-select-photo">
						<i class="fa-solid fa-folder-open me-2"></i> Browse Files
					</button>
				</div>

				<input type="file" name="profile_photo" id="profile_photo"
					   class="form-control-file" accept="image/*" onchange="previewPhoto(event)">

				<div class="photo-guidelines">
					<h6><i class="fa-solid fa-info-circle me-2"></i> Photo Guidelines:</h6>
					<ul>
						<li>Maximum file size: 5MB</li>
						<li>Supported formats: JPG, PNG, GIF</li>
						<li>Recommended size: 400x400 pixels</li>
						<li>Clear face photos work best</li>
					</ul>
				</div>

				<div class="photo-actions">
					<button type="submit" class="btn-update">
						<i class="fa-solid fa-save me-2"></i> Update Photo
					</button>

					@if(Auth::user()->profile_photo && Auth::user()->profile_photo != 'default-avatar.png' && Auth::user()->profile_photo != 'default-buyer.png')
					<button type="button" class="btn-remove" onclick="confirmDelete()">
						<i class="fa-solid fa-trash me-2"></i> Remove Photo
					</button>
					@endif

					<a href="{{ route('buyer.profile.profile') }}" class="btn-back">
						<i class="fa-solid fa-arrow-left me-2"></i> Back to Profile
					</a>
				</div>
			</form>

			@if(Auth::user()->profile_photo && Auth::user()->profile_photo != 'default-avatar.png' && Auth::user()->profile_photo != 'default-buyer.png')
			<form id="delete-photo-form" action="{{ route('buyer.profile.photo.delete') }}" method="POST" class="d-none">
				@csrf
				@method('DELETE')
			</form>
			@endif
		</div>
	</div>
</div>

<div class="loading-overlay" id="loadingOverlay">
	<div class="loading-spinner"></div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
	function previewPhoto(event) {
		const file = event.target.files[0];
		if (!file) return;

		if (!file.type.match('image.*')) {
			Swal.fire({
				icon: 'error',
				title: 'Invalid File',
				text: 'Please select an image file (JPG, PNG, GIF)',
				timer: 3000,
				timerProgressBar: true
			});
			return;
		}

		if (file.size > 5 * 1024 * 1024) {
			Swal.fire({
				icon: 'error',
				title: 'File Too Large',
				text: 'Maximum file size is 5MB',
				timer: 3000,
				timerProgressBar: true
			});
			return;
		}

		const reader = new FileReader();
		reader.onload = function(e) {
			const preview = document.getElementById('photo-preview');
			if (preview) {
				preview.src = e.target.result;
				preview.parentElement.classList.add('pulse-animation');
				setTimeout(() => {
					preview.parentElement.classList.remove('pulse-animation');
				}, 1000);
			}
		};
		reader.readAsDataURL(file);
	}

	function confirmDelete() {
		Swal.fire({
			title: 'Remove Profile Photo?',
			text: 'Your current profile photo will be removed and replaced with the default avatar.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#ef4444',
			cancelButtonColor: '#6b7280',
			confirmButtonText: 'Yes, remove it!',
			cancelButtonText: 'Cancel',
			reverseButtons: true
		}).then((result) => {
			if (result.isConfirmed) {
				document.getElementById('loadingOverlay').classList.add('active');
				document.getElementById('delete-photo-form').submit();
			}
		});
	}

	document.addEventListener('DOMContentLoaded', function() {
		const photoForm = document.getElementById('photoForm');
		if (photoForm) {
			photoForm.addEventListener('submit', function(e) {
				const fileInput = document.getElementById('profile_photo');
				if (!fileInput.files[0]) {
					e.preventDefault();
					Swal.fire({
						icon: 'error',
						title: 'No Photo Selected',
						text: 'Please select a photo to upload',
						timer: 3000,
						timerProgressBar: true
					}).then(() => {
						document.querySelector('.photo-upload-area').classList.add('shake-animation');
						setTimeout(() => {
							document.querySelector('.photo-upload-area').classList.remove('shake-animation');
						}, 500);
					});
					return;
				}

				document.getElementById('loadingOverlay').classList.add('active');
			});
		}

		@if(session('success'))
			Swal.fire({
				icon: 'success',
				title: 'Success',
				text: '{{ session('success') }}',
				timer: 3000,
				timerProgressBar: true,
				showConfirmButton: false
			});
		@endif

		@if(session('error'))
			Swal.fire({
				icon: 'error',
				title: 'Error',
				text: '{{ session('error') }}',
				timer: 4000,
				timerProgressBar: true,
				showConfirmButton: true
			});
		@endif

		const uploadArea = document.querySelector('.photo-upload-area');
		if (uploadArea) {
			uploadArea.addEventListener('dragover', function(e) {
				e.preventDefault();
				this.style.borderColor = 'var(--primary-green)';
				this.style.backgroundColor = '#f0fdf4';
			});

			uploadArea.addEventListener('dragleave', function(e) {
				e.preventDefault();
				this.style.borderColor = '#cbd5e1';
				this.style.backgroundColor = '#f8fafc';
			});

			uploadArea.addEventListener('drop', function(e) {
				e.preventDefault();
				this.style.borderColor = '#cbd5e1';
				this.style.backgroundColor = '#f8fafc';

				const fileInput = document.getElementById('profile_photo');
				if (e.dataTransfer.files.length) {
					fileInput.files = e.dataTransfer.files;
					previewPhoto({ target: fileInput });
				}
			});
		}

		window.addEventListener('resize', function() {
			const container = document.querySelector('.photo-container');
			if (window.innerWidth <= 480) {
				container.classList.add('mobile-view');
			} else {
				container.classList.remove('mobile-view');
			}
		});

		if (window.innerWidth <= 480) {
			document.querySelector('.photo-container').classList.add('mobile-view');
		}
	});

	window.onbeforeunload = function() {
		document.getElementById('loadingOverlay').classList.add('active');
	};
</script>
@endsection
