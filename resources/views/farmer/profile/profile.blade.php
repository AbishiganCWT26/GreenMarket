@extends('farmer.layouts.farmer_master')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('styles')
	<link rel="stylesheet" href="{{ asset('css/farmer/profile.css') }}">
	
@endsection

@section('content')
	<div class="profile-wrap">
		<div class="profile-container">
			<div class="profile-card">
				<div class="profile-head">
					<div class="profile-avatar">
						<img src="{{ Auth::user()->profile_photo ? asset('uploads/profile_pictures/' . Auth::user()->profile_photo) : asset('assets/images/default-avatar.png') }}"
							alt="Profile" id="profileAvatar">
					</div>
					<div class="profile-info">
						<h2>{{ $farmer->name ?? Auth::user()->username }}</h2>
						<p><i class="fa-solid fa-envelope"></i> {{ Auth::user()->email ?? 'Not set' }}</p>
						<p><i class="fa-solid fa-phone"></i> {{ $farmer->primary_mobile ?? 'Not set' }}</p>
					</div>
				</div>

				<div class="profile-tabs">
					<div class="tabs-nav">
						<button class="tab-btn active" data-tab="personal">
							<i class="fa-solid fa-user"></i>
							<span>Personal</span>
						</button>
					</div>

					<div class="tabs-content">
						<div class="tab-pane active" id="personal">
							<form action="{{ route('farmer.profile.update') }}" method="POST" id="profileForm">
								@csrf
								<div class="form-section">
									<h3><i class="fa-solid fa-id-card"></i> Personal Details</h3>
									<div class="form-row">
										<div class="form-field">
											<label><i class="fa-solid fa-signature"></i> Full Name <span
													class="required">*</span></label>
											<input type="text" name="name"
												value="{{ old('name', $farmer->name ?? Auth::user()->username) }}" required>
											@error('name')<span class="error">{{ $message }}</span>@enderror
										</div>
										<div class="form-field">
											<label><i class="fa-solid fa-at"></i> Email <span
													class="required">*</span></label>
											<input type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
												required>
											@error('email')<span class="error">{{ $message }}</span>@enderror
										</div>
										<div class="form-field">
											<label><i class="fa-solid fa-mobile"></i> Mobile <span
													class="required">*</span></label>
											<input type="tel" name="primary_mobile"
												value="{{ old('primary_mobile', $farmer->primary_mobile ?? '') }}" required>
											@error('primary_mobile')<span class="error">{{ $message }}</span>@enderror
										</div>
										<div class="form-field">
											<label><i class="fa-brands fa-whatsapp"></i> WhatsApp</label>
											<input type="tel" name="whatsapp_number"
												value="{{ old('whatsapp_number', $farmer->whatsapp_number ?? '') }}">
											@error('whatsapp_number')<span class="error">{{ $message }}</span>@enderror
										</div>
										<div class="form-field">
											<label><i class="fa-solid fa-id-card"></i> NIC No. <span
													class="required">*</span></label>
											<div class="nic-field" id="nicWrapper">
												<input type="text" name="nic_no" value="{{ $farmer->nic_no ?? '' }}"
													readonly>
												<i class="fa-solid fa-lock"></i>
											</div>
										</div>
										<div class="form-field">
											<label><i class="fa-solid fa-user"></i> Username <span
													class="required">*</span></label>
											<input type="text" name="username"
												value="{{ old('username', Auth::user()->username) }}" required>
											@error('username')<span class="error">{{ $message }}</span>@enderror
										</div>
									</div>
								</div>

								<div class="form-section">
									<h3><i class="fa-solid fa-location-dot"></i> Address Details</h3>
									<div class="form-row">
										<div class="form-field">
											<label><i class="fa-solid fa-globe"></i> District <span
													class="required">*</span></label>
											<select name="district" id="district" required>
												<option value="">Select District</option>
											</select>
											@error('district')<span class="error">{{ $message }}</span>@enderror
										</div>
										<div class="form-field">
											<label><i class="fa-solid fa-building"></i> Divisional Secretariat <span
													class="required">*</span></label>
											<select name="divisional_secretariat" id="divisional_secretariat" required
												disabled>
												<option value="">Select DS</option>
											</select>
											@error('divisional_secretariat')<span
											class="error">{{ $message }}</span>@enderror
										</div>
										<div class="form-field">
											<label><i class="fa-solid fa-landmark"></i> GN Division <span
													class="required">*</span></label>
											<select name="grama_niladhari_division" id="grama_niladhari_division" required
												disabled>
												<option value="">Select GN Division</option>
											</select>
											@error('grama_niladhari_division')<span
											class="error">{{ $message }}</span>@enderror
										</div>
										<div class="form-field">
											<label><i class="fa-solid fa-hashtag"></i> GN Code</label>
											<input type="text" name="gn_division_code" id="gn_division_code"
												value="{{ old('gn_division_code', $farmer->gn_division_code ?? '') }}"
												readonly>
										</div>
										<div class="form-field full-width">
											<label><i class="fa-solid fa-house"></i> Address <span
													class="required">*</span></label>
											<textarea name="residential_address" rows="3"
												required>{{ old('residential_address', $farmer->residential_address ?? '') }}</textarea>
											@error('residential_address')<span class="error">{{ $message }}</span>@enderror
										</div>
										<div class="form-field full-width">
											<label><i class="fa-solid fa-map"></i> Google Maps Link <span
													class="required">*</span></label>
											<input type="url" name="address_map_link"
												value="{{ old('address_map_link', $farmer->address_map_link ?? '') }}"
												placeholder="https://maps.google.com/..." required>
											@error('address_map_link')<span class="error">{{ $message }}</span>@enderror
											<small>Copy your location from Google Maps for accurate pickup</small>
										</div>
									</div>
								</div>

								<div class="form-actions">
									<button type="submit" class="btn-submit">
										<i class="fa-solid fa-floppy-disk"></i>
										<span>Update Profile</span>
									</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('scripts')
	
	<script src="{{ asset('js/gn-data.js') }}"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			@if(session('success'))
				Swal.fire({
					@if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
					title: 'Success!',
					text: '{{ session('success') }}',
					toast: true,
					position: 'top-end',
					showConfirmButton: false,
					timer: 3000,
					timerProgressBar: true,
					background: '#10B981',
					color: 'white'
				});
			@endif

			@if(session('error'))
				Swal.fire({
					@if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
					title: 'Error!',
					text: '{{ session('error') }}',
					toast: true,
					position: 'top-end',
					showConfirmButton: false,
					timer: 4000,
					timerProgressBar: true,
					background: '#ef4444',
					color: 'white'
				});
			@endif

					const tabBtns = document.querySelectorAll('.tab-btn');
			tabBtns.forEach(btn => {
				btn.addEventListener('click', function () {
					tabBtns.forEach(b => b.classList.remove('active'));
					this.classList.add('active');
					document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
					document.getElementById(this.dataset.tab).classList.add('active');
				});
			});

			const profileForm = document.getElementById('profileForm');
			if (profileForm) {
				profileForm.addEventListener('submit', function (e) {
					e.preventDefault();
					Swal.fire({
						title: 'Updating...',
						html: '<div class="spinner"></div>',
						showConfirmButton: false,
						allowOutsideClick: false
					});
					setTimeout(() => {
						this.submit();
					}, 500);
				});
			}

			const nicWrapper = document.getElementById('nicWrapper');
			if (nicWrapper) {
				nicWrapper.addEventListener('click', function () {
					Swal.fire({
						title: 'NIC Cannot Be Changed',
						text: 'Please contact your assigned Lead Farmer to update your NIC number.',
						imageUrl: 'https://i.pinimg.com/originals/64/4b/0f/644b0f12e3f1dcb3890db07459e13e4c.gif',
						imageWidth: 80,
						imageHeight: 80,
						imageAlt: 'Alert Icon',
						confirmButtonColor: '#10B981'
					});
				});
			}

			const profileAvatar = document.getElementById('profileAvatar');
			if (profileAvatar) {
				profileAvatar.addEventListener('error', function () {
					this.src = '{{ asset('assets/images/default-avatar.png') }}';
				});
			}

			const districtSelect = document.getElementById('district');
			const dsSelect = document.getElementById('divisional_secretariat');
			const gnSelect = document.getElementById('grama_niladhari_division');
			const gnCodeInput = document.getElementById('gn_division_code');

			const initialDistrict = "{{ old('district', $farmer->district ?? '') }}";
			const initialDS = "{{ old('divisional_secretariat', $farmer->divisional_secretariat ?? '') }}";
			const initialGN = "{{ old('grama_niladhari_division', $farmer->grama_niladhari_division ?? '') }}";

			function populateDistricts() {
				if (typeof gnData !== 'undefined') {
					Object.keys(gnData).sort().forEach(dist => {
						const option = new Option(dist, dist);
						if (dist === initialDistrict) option.selected = true;
						districtSelect.add(option);
					});
					if (initialDistrict) populateDS(initialDistrict);
				}
			}

			function populateDS(dist) {
				dsSelect.innerHTML = '<option value="">Select DS</option>';
				dsSelect.disabled = !dist;
				gnSelect.innerHTML = '<option value="">Select GN Division</option>';
				gnSelect.disabled = true;
				gnCodeInput.value = '';

				if (dist && gnData[dist]) {
					Object.keys(gnData[dist]).sort().forEach(ds => {
						const option = new Option(ds, ds);
						if (ds === initialDS) option.selected = true;
						dsSelect.add(option);
					});
					if (initialDS) populateGN(dist, initialDS);
				}
			}

			function populateGN(dist, ds) {
				gnSelect.innerHTML = '<option value="">Select GN Division</option>';
				gnSelect.disabled = !ds;
				gnCodeInput.value = '';

				if (dist && ds && gnData[dist][ds]) {
					gnData[dist][ds].forEach(gn => {
						const option = new Option(gn.name, gn.name);
						option.dataset.code = gn.code;
						if (gn.name === initialGN) {
							option.selected = true;
							gnCodeInput.value = gn.code;
						}
						gnSelect.add(option);
					});
				}
			}

			districtSelect.addEventListener('change', function () {
				populateDS(this.value);
			});

			dsSelect.addEventListener('change', function () {
				populateGN(districtSelect.value, this.value);
			});

			gnSelect.addEventListener('change', function () {
				const selected = this.options[this.selectedIndex];
				gnCodeInput.value = selected.dataset.code || '';
			});

			populateDistricts();

			window.addEventListener('resize', function () {
				if (window.innerWidth <= 576) {
					document.querySelectorAll('.form-actions .btn-submit').forEach(btn => {
						btn.style.width = '100%';
					});
				} else {
					document.querySelectorAll('.form-actions .btn-submit').forEach(btn => {
						btn.style.width = '';
					});
				}
			});
		});
	</script>
@endsection
