@extends('farmer.layouts.farmer_master')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/farmer/profile.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
<div class="profile-container">
	<div class="profile-header">
		<div class="profile-avatar">
			<img src="{{ Auth::user()->profile_photo ? asset('uploads/profile_pictures/' . Auth::user()->profile_photo) : asset('assets/images/default-avatar.png') }}"
				 alt="Profile Photo" class="avatar-img" id="profileAvatar">
		</div>
		<div class="profile-info">
			<h2>{{ $farmer->name ?? Auth::user()->username }}</h2>
			<p>
				<i class="fa-solid fa-envelope"></i> {{ Auth::user()->email ?? 'Not set' }}
			</p>
			<p>
				<i class="fa-solid fa-phone"></i> {{ $farmer->primary_mobile ?? 'Not set' }}
			</p>
		</div>
	</div>

	<div class="profile-tabs">
		<div class="nav-tabs">
			<button class="nav-link active" data-tab="personal">
				<i class="fa-solid fa-user-pen"></i> Personal Details
			</button>
		</div>

		<div class="tab-content">
			<div class="tab-pane active" id="personal">
				<form action="{{ route('farmer.profile.update') }}" method="POST" id="profileForm" class="form-section">
					@csrf
					<h4><i class="fa-solid fa-user-gear"></i> Personal Information</h4>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label">
								<i class="fa-solid fa-signature"></i> Full Name *
							</label>
							<input type="text" class="form-control @error('name') is-invalid @enderror"
								   name="name" value="{{ old('name', $farmer->name ?? Auth::user()->username) }}" required>
							@error('name')
								<div class="invalid-feedback">
									<i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
								</div>
							@enderror
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">
								<i class="fa-solid fa-at"></i> Email *
							</label>
							<input type="email" class="form-control @error('email') is-invalid @enderror"
								   name="email" value="{{ old('email', Auth::user()->email) }}" required>
							@error('email')
								<div class="invalid-feedback">
									<i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
								</div>
							@enderror
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">
								<i class="fa-solid fa-mobile-screen-button"></i> Mobile *
							</label>
							<input type="tel" class="form-control @error('primary_mobile') is-invalid @enderror"
								   name="primary_mobile" value="{{ old('primary_mobile', $farmer->primary_mobile ?? '') }}" required>
							@error('primary_mobile')
								<div class="invalid-feedback">
									<i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
								</div>
							@enderror
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">
								<i class="fa-brands fa-whatsapp"></i> WhatsApp
							</label>
							<input type="tel" class="form-control @error('whatsapp_number') is-invalid @enderror"
								   name="whatsapp_number" value="{{ old('whatsapp_number', $farmer->whatsapp_number ?? '') }}">
							@error('whatsapp_number')
								<div class="invalid-feedback">
									<i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
								</div>
							@enderror
						</div>
						<div class="col-12 mb-3">
							<label class="form-label">
								<i class="fa-solid fa-user-tag"></i> Username *
							</label>
							<input type="text" class="form-control @error('username') is-invalid @enderror"
								   name="username" value="{{ old('username', Auth::user()->username) }}" required>
							@error('username')
								<div class="invalid-feedback">
									<i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
								</div>
							@enderror
						</div>
					</div>

					<h4><i class="fa-solid fa-map-location-dot"></i> Address Information</h4>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label">
								<i class="fa-solid fa-earth-asia"></i> District *
							</label>
							<select class="form-select @error('district') is-invalid @enderror" name="district" required>
								<option value="">Select District</option>
								<option value="Colombo" {{ ($farmer->district ?? '') == 'Colombo' ? 'selected' : '' }}>Colombo</option>
								<option value="Gampaha" {{ ($farmer->district ?? '') == 'Gampaha' ? 'selected' : '' }}>Gampaha</option>
								<option value="Kalutara" {{ ($farmer->district ?? '') == 'Kalutara' ? 'selected' : '' }}>Kalutara</option>
								<option value="Kandy" {{ ($farmer->district ?? '') == 'Kandy' ? 'selected' : '' }}>Kandy</option>
								<option value="Matale" {{ ($farmer->district ?? '') == 'Matale' ? 'selected' : '' }}>Matale</option>
								<option value="Nuwara Eliya" {{ ($farmer->district ?? '') == 'Nuwara Eliya' ? 'selected' : '' }}>Nuwara Eliya</option>
								<option value="Galle" {{ ($farmer->district ?? '') == 'Galle' ? 'selected' : '' }}>Galle</option>
								<option value="Matara" {{ ($farmer->district ?? '') == 'Matara' ? 'selected' : '' }}>Matara</option>
								<option value="Hambantota" {{ ($farmer->district ?? '') == 'Hambantota' ? 'selected' : '' }}>Hambantota</option>
								<option value="Jaffna" {{ ($farmer->district ?? '') == 'Jaffna' ? 'selected' : '' }}>Jaffna</option>
								<option value="Kilinochchi" {{ ($farmer->district ?? '') == 'Kilinochchi' ? 'selected' : '' }}>Kilinochchi</option>
								<option value="Mannar" {{ ($farmer->district ?? '') == 'Mannar' ? 'selected' : '' }}>Mannar</option>
								<option value="Vavuniya" {{ ($farmer->district ?? '') == 'Vavuniya' ? 'selected' : '' }}>Vavuniya</option>
								<option value="Mullaitivu" {{ ($farmer->district ?? '') == 'Mullaitivu' ? 'selected' : '' }}>Mullaitivu</option>
								<option value="Batticaloa" {{ ($farmer->district ?? '') == 'Batticaloa' ? 'selected' : '' }}>Batticaloa</option>
								<option value="Ampara" {{ ($farmer->district ?? '') == 'Ampara' ? 'selected' : '' }}>Ampara</option>
								<option value="Trincomalee" {{ ($farmer->district ?? '') == 'Trincomalee' ? 'selected' : '' }}>Trincomalee</option>
								<option value="Kurunegala" {{ ($farmer->district ?? '') == 'Kurunegala' ? 'selected' : '' }}>Kurunegala</option>
								<option value="Puttalam" {{ ($farmer->district ?? '') == 'Puttalam' ? 'selected' : '' }}>Puttalam</option>
								<option value="Anuradhapura" {{ ($farmer->district ?? '') == 'Anuradhapura' ? 'selected' : '' }}>Anuradhapura</option>
								<option value="Polonnaruwa" {{ ($farmer->district ?? '') == 'Polonnaruwa' ? 'selected' : '' }}>Polonnaruwa</option>
								<option value="Badulla" {{ ($farmer->district ?? '') == 'Badulla' ? 'selected' : '' }}>Badulla</option>
								<option value="Monaragala" {{ ($farmer->district ?? '') == 'Monaragala' ? 'selected' : '' }}>Monaragala</option>
								<option value="Ratnapura" {{ ($farmer->district ?? '') == 'Ratnapura' ? 'selected' : '' }}>Ratnapura</option>
								<option value="Kegalle" {{ ($farmer->district ?? '') == 'Kegalle' ? 'selected' : '' }}>Kegalle</option>
							</select>
							@error('district')
								<div class="invalid-feedback">
									<i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
								</div>
							@enderror
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">
								<i class="fa-solid fa-landmark"></i> Grama Niladhari Division *
							</label>
							<input type="text" class="form-control @error('grama_niladhari_division') is-invalid @enderror"
								   name="grama_niladhari_division" value="{{ old('grama_niladhari_division', $farmer->grama_niladhari_division ?? '') }}" required>
							@error('grama_niladhari_division')
								<div class="invalid-feedback">
									<i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
								</div>
							@enderror
						</div>
							<div class="col-12 mb-3">
								<label class="form-label">
									<i class="fa-solid fa-house"></i> Residential Address *
								</label>
								<textarea class="form-control @error('residential_address') is-invalid @enderror"
										  name="residential_address" rows="3" required>{{ old('residential_address', $farmer->residential_address ?? '') }}</textarea>
								@error('residential_address')
									<div class="invalid-feedback">
										<i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
									</div>
								@enderror
							</div>
							<div class="col-12 mb-3">
								<label class="form-label">
									<i class="fa-solid fa-map-pin"></i> Google Maps Link *
								</label>
								<input type="url" class="form-control @error('address_map_link') is-invalid @enderror"
									   name="address_map_link" value="{{ old('address_map_link', $farmer->address_map_link ?? '') }}" required placeholder="https://maps.google.com/...">
								@error('address_map_link')
									<div class="invalid-feedback">
										<i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
									</div>
								@enderror
								<div class="form-hint">
									<i class="fa-solid fa-circle-info"></i>
									Copy your location from Google Maps for accurate pickup location.
								</div>
							</div>
						</div>

						<button type="submit" class="btn-submit">
							<i class="fa-solid fa-floppy-disk"></i> Update Profile Information
						</button>
					</form>
				</div>
			</div>
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
				title: 'Success!',
				text: '{{ session('success') }}',
				toast: true,
				position: 'top-end',
				showConfirmButton: false,
				timer: 3000,
				timerProgressBar: true,
				background: 'linear-gradient(135deg, #10B981 0%, #059669 100%)',
				color: 'white',
				iconColor: 'white',
				customClass: {
					popup: 'sweetalert-success'
				}
			});
		@endif

		@if(session('error'))
			Swal.fire({
				icon: 'error',
				title: 'Error!',
				text: '{{ session('error') }}',
				toast: true,
				position: 'top-end',
				showConfirmButton: false,
				timer: 4000,
				timerProgressBar: true,
				background: '#ef4444',
				color: 'white',
				iconColor: 'white',
				customClass: {
					popup: 'sweetalert-error'
				}
			});
		@endif

		document.querySelectorAll('.nav-link').forEach(button => {
			button.addEventListener('click', function() {
				const tabId = this.getAttribute('data-tab');
				document.querySelectorAll('.nav-link').forEach(btn => {
					btn.classList.remove('active');
					btn.style.transform = '';
				});
				document.querySelectorAll('.tab-pane').forEach(pane => {
					pane.classList.remove('active');
					pane.style.animation = '';
				});
				this.classList.add('active');
				this.style.transform = 'translateY(-2px)';
				const activePane = document.getElementById(tabId);
				activePane.classList.add('active');
				activePane.style.animation = 'fadeInUp 0.4s ease-out';
			});
		});

		const profileForm = document.getElementById('profileForm');
		if (profileForm) {
			profileForm.addEventListener('submit', function(e) {
				e.preventDefault();
				Swal.fire({
					title: 'Updating Profile...',
					text: 'Please wait while we update your information.',
					allowOutsideClick: false,
					showConfirmButton: false,
					willOpen: () => {
						Swal.showLoading();
					},
					didOpen: () => {
						setTimeout(() => {
							profileForm.submit();
						}, 500);
					}
				});
			});
		}

		const inputs = document.querySelectorAll('.form-control, .form-select');
		inputs.forEach(input => {
			input.addEventListener('focus', function() {
				this.parentElement.style.transform = 'translateY(-5px)';
			});

			input.addEventListener('blur', function() {
				this.parentElement.style.transform = '';
			});

			input.addEventListener('mouseenter', function() {
				this.style.boxShadow = '0 5px 20px rgba(16, 185, 129, 0.15)';
			});

			input.addEventListener('mouseleave', function() {
				if (!this.matches(':focus')) {
					this.style.boxShadow = '';
				}
			});
		});

		const profileAvatar = document.getElementById('profileAvatar');
		if (profileAvatar) {
			profileAvatar.addEventListener('error', function() {
				this.src = '{{ asset('assets/images/default-avatar.png') }}';
			});

			profileAvatar.addEventListener('mouseenter', function() {
				this.style.transform = 'scale(1.1)';
			});

			profileAvatar.addEventListener('mouseleave', function() {
				this.style.transform = 'scale(1)';
			});
		}

		const labels = document.querySelectorAll('.form-label');
		labels.forEach(label => {
			label.addEventListener('mouseenter', function() {
				const icon = this.querySelector('i');
				if (icon) {
					icon.style.transform = 'scale(1.2) rotate(10deg)';
				}
			});

			label.addEventListener('mouseleave', function() {
				const icon = this.querySelector('i');
				if (icon) {
					icon.style.transform = '';
				}
			});
		});

		window.addEventListener('resize', function() {
			if (window.innerWidth <= 767) {
				document.querySelectorAll('.btn-submit').forEach(btn => {
					btn.style.width = '100%';
					btn.style.justifyContent = 'center';
				});
			} else {
				document.querySelectorAll('.btn-submit').forEach(btn => {
					btn.style.width = '';
					btn.style.justifyContent = '';
				});
			}
		});

		const submitButton = document.querySelector('.btn-submit');
		if (submitButton) {
			submitButton.addEventListener('mouseenter', function() {
				this.style.letterSpacing = '1px';
			});

			submitButton.addEventListener('mouseleave', function() {
				this.style.letterSpacing = '0.5px';
			});
		}
	});
</script>
@endsection
