<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>GreenMarket | Login</title>
	<link rel="stylesheet" href="{{ asset('css/login.css') }}" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
	@include('includes.loader')
	<div class="auth-container">
		<div class="auth-card">
			<div class="auth-header" role="button">
				<img src="{{ asset('assets/images/Logo Green Market.png') }}" alt="GreenMarket" class="auth-logo">
				<div class="auth-brand">
					<h1>GreenMarket</h1>
				</div>
			</div>

			<div class="auth-body">
				<div class="auth-title">
					<h2>Welcome Back</h2>
					<p>Sign in to your account</p>
				</div>

				<form id="loginForm" method="POST" action="{{ url('/login') }}">
					@csrf

					<div class="input-field">
						<label><i class="fas fa-user"></i> Username / NIC</label>
						<input type="text" id="username" name="username" placeholder="Enter username" value="{{ old('username') }}" required>
					</div>

					<div class="input-field">
						<label><i class="fas fa-lock"></i> Password</label>
						<div class="password-wrapper">
							<input type="password" id="password" name="password" placeholder="Enter password" required>
							<button type="button" class="password-toggle" id="togglePassword">
								<i class="fas fa-eye"></i>
							</button>
						</div>
					</div>

					<div class="auth-options">
						<a href="#" id="forgotLink"><i class="fas fa-key"></i> Forgot password?</a>
					</div>

					<button type="submit" class="auth-btn">
						<i class="fas fa-sign-in-alt"></i> Sign In
					</button>
				</form>

				<div class="auth-footer">
					<p>New to GreenMarket?</p>
					<a href="{{ url('/register/buyer') }}" class="register-link">
						<i class="fas fa-user-plus"></i> Register as Buyer
					</a>
				</div>

				<div class="auth-back">
					<a href="/"><i class="fas fa-home"></i> Back to Home</a>
				</div>
			</div>
		</div>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const togglePassword = document.getElementById('togglePassword');
			const passwordInput = document.getElementById('password');
			const forgotLink = document.getElementById('forgotLink');
			const loginForm = document.getElementById('loginForm');

			if (togglePassword) {
				togglePassword.addEventListener('click', function() {
					const type = passwordInput.type === 'password' ? 'text' : 'password';
					passwordInput.type = type;
					this.querySelector('i').classList.toggle('fa-eye');
					this.querySelector('i').classList.toggle('fa-eye-slash');
				});
			}

			if (forgotLink) {
				forgotLink.addEventListener('click', function(e) {
					e.preventDefault();
					Swal.fire({
						title: 'Reset Password',
						html: '<input type="text" id="resetUsername" class="swal2-input" placeholder="Username or Email">',
						background: '#ffffffff',
						color: '#0f1724',
						showCancelButton: true,
						confirmButtonColor: '#10B981',
						cancelButtonColor: '#6b7280',
						confirmButtonText: 'Send OTP',
						preConfirm: () => {
							const username = document.getElementById('resetUsername').value;
							if (!username) {
								Swal.showValidationMessage('Please enter your username');
								return false;
							}
							return username;
						}
					}).then((result) => {
						if (result.isConfirmed) {
							Swal.fire({
								title: 'Processing',
								text: 'Please wait...',
								allowOutsideClick: false,
								showConfirmButton: false,
								didOpen: () => {
									Swal.showLoading();
								}
							});

							fetch('{{ route("password.forgot") }}', {
								method: 'POST',
								headers: {
									'Content-Type': 'application/json',
									'X-CSRF-TOKEN': '{{ csrf_token() }}'
								},
								body: JSON.stringify({ 
									username: result.value,
									send_sms: true 
								})
							})
							.then(res => res.json())
							.then(data => {
								if (data.success) {
									Swal.fire({
										icon: 'success',
										title: 'OTP Sent',
										text: 'Check your email for OTP',
										timer: 2000,
										showConfirmButton: false
									}).then(() => {
										window.location.href = data.redirect_url;
									});
								} else {
									Swal.fire({
										icon: 'error',
										title: 'Error',
										text: data.message || 'User not found'
									});
								}
							});
						}
					});
				});
			}

			if (loginForm) {
				loginForm.addEventListener('submit', function(e) {
					const username = document.getElementById('username').value.trim();
					const password = document.getElementById('password').value.trim();

					if (!username || !password) {
						e.preventDefault();
						Swal.fire({
							icon: 'warning',
							title: 'Required Fields',
							text: 'Please fill in all fields'
						});
					}
				});
			}

			@if ($errors->any())
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: '{{ $errors->first() }}'
				});
			@endif

			@if (session('error'))
				Swal.fire({
					icon: 'error',
					title: 'Login Failed',
					text: '{{ session('error') }}'
				});
			@endif

			@if (session('login_success'))
				Swal.fire({
					icon: 'success',
					title: 'Welcome {{ session('name') ?? 'User' }}',
					text: 'Redirecting to dashboard...',
					timer: 2000,
					showConfirmButton: false
				}).then(() => {
					let url = '/';
					switch('{{ session('role') }}') {
						case 'admin': url = '/admin/dashboard'; break;
						case 'facilitator': url = '/facilitator/dashboard'; break;
						case 'lead_farmer': url = '/lead-farmer/dashboard'; break;
						case 'farmer': url = '/farmer/dashboard'; break;
						case 'buyer': url = '/buyer/dashboard'; break;
					}
					window.location.href = url;
				});
			@endif

			@if (session('password_reset_success'))
				Swal.fire({
					icon: 'success',
					title: 'Password Reset Complete!',
					text: 'New credentials sent to your registered contact',
					confirmButtonColor: '#10B981'
				});
			@endif
		});
	</script>
</body>
</html>