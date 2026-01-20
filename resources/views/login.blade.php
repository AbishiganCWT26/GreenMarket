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
	<div class="container">
		<div class="login-wrapper">
			<div class="logo-section" onclick="window.location.href='/'" tabindex="0" role="button">
				<div class="logo-container">
					<img src="{{ asset('assets/images/logo-4.png') }}" alt="GreenMarket Logo" class="logo">
					<h1 class="logo-title">GreenMarket</h1>
				</div>
				<p class="logo-tagline">Fresh from farm to table</p>
			</div>

			<div class="login-card">
				<div class="card-header">
					<h2> Welcome Back</h2>
					<p>Sign in to your account</p>
				</div>

				<form id="loginForm" method="POST" action="{{ url('/login') }}" class="login-form">
					@csrf
					<div class="input-group">
						<label for="username"><i class="fas fa-user"></i> Username / NIC</label>
						<input type="text" id="username" name="username" placeholder="Enter username or NIC" required>
						<div class="input-focus-line"></div>
					</div>

					<div class="input-group">
						<label for="password"><i class="fas fa-lock"></i> Password</label>
						<div class="password-wrapper">
							<input type="password" id="password-field" name="password" placeholder="Enter password" required>
							<button type="button" class="password-toggle" id="password-toggle">
								<i class="fas fa-eye"></i>
							</button>
							<div class="input-focus-line"></div>
						</div>
					</div>

					<div class="form-options">
						<a href="#" class="forgot-password" id="forgotPasswordBtn">
							<i class="fas fa-key"></i> Forgot Password?
						</a>
					</div>

					<button type="submit" class="login-btn">
						<i class="fas fa-sign-in-alt"></i> Sign In
					</button>
				</form>

				<div class="register-section">
					<p>New to GreenMarket?</p>
					<a href="{{ url('/register/buyer') }}" class="register-btn">
						<i class="fas fa-user-plus"></i> Register as Buyer
					</a>
				</div>

				<div class="quick-links">
					<a href="/" class="home-link">
						<i class="fas fa-home"></i> Back to Home
					</a>
				</div>
			</div>
		</div>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
            const passwordToggle = document.getElementById('password-toggle');
            const forgotPasswordBtn = document.getElementById('forgotPasswordBtn');
            const loginForm = document.getElementById('loginForm');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password-field');

            function togglePasswordVisibility() {
                const passwordField = document.getElementById('password-field');
                const toggleIcon = document.querySelector('#password-toggle i');
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
                    toggleIcon.style.color = '#10B981';
                } else {
                    passwordField.type = 'password';
                    toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
                    toggleIcon.style.color = '#6b7280';
                }
            }

            if (passwordToggle) {
                passwordToggle.addEventListener('click', togglePasswordVisibility);
                passwordToggle.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') togglePasswordVisibility();
                });
            }

            if (forgotPasswordBtn) {
                forgotPasswordBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Reset Password',
                        html: `
                            <div class="forgot-password-modal">
                                <p style="margin-bottom: 15px; color: #666;">Enter your username or email to receive OTP</p>
                                <input type="text" id="resetUsername" class="swal2-input" placeholder="Username or Email" style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px;">
                                <div style="margin-top: 10px; text-align: left;">
                                    <label style="display: flex; align-items: center; gap: 5px; cursor: pointer;">
                                        <input type="checkbox" id="sendSms" checked>
                                        <span style="font-size: 13px;">Send OTP via SMS (if mobile number registered)</span>
                                    </label>
                                </div>
                            </div>
                        `,
                        background: '#ffffff',
                        color: '#0f1724',
                        showCancelButton: true,
                        confirmButtonColor: '#10B981',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Send OTP',
                        cancelButtonText: 'Cancel',
                        width: window.innerWidth <= 480 ? '90%' : '420px',
                        preConfirm: () => {
                            const username = document.getElementById('resetUsername').value.trim();
                            const sendSms = document.getElementById('sendSms').checked;

                            if (!username) {
                                Swal.showValidationMessage('Please enter your username or email');
                                return false;
                            }

                            return { username, sendSms };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const { username, sendSms } = result.value;

                            fetch('{{ route("password.forgot") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    username: username,
                                    send_sms: sendSms
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'OTP Sent!',
                                        html: `
                                            <p style="margin-bottom: 10px;">OTP has been sent to your registered email${data.has_phone ? ' and mobile' : ''}.</p>
                                            <p style="font-size: 12px; color: #666;">Redirecting to OTP verification...</p>
                                        `,
                                        background: '#ffffff',
                                        color: '#0f1724',
                                        confirmButtonColor: '#10B981',
                                        timer: 2000,
                                        showConfirmButton: false,
                                        timerProgressBar: true,
                                        didClose: () => {
                                            window.location.href = data.redirect_url;
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Failed',
                                        text: data.message || 'User not found. Please check your username/email.',
                                        background: '#ffffff',
                                        color: '#0f1724',
                                        confirmButtonColor: '#10B981'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred. Please try again.',
                                    background: '#ffffff',
                                    color: '#0f1724',
                                    confirmButtonColor: '#10B981'
                                });
                            });
                        }
                    });
                });
            }

            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    const username = usernameInput.value.trim();
                    const password = passwordInput.value.trim();
                    let hasError = false;

                    if (!username && !password) {
                        hasError = true;
                        Swal.fire({
                            icon: 'error',
                            title: 'Missing Credentials',
                            text: 'Please enter both username and password.',
                            background: '#ffffff',
                            color: '#0f1724',
                            confirmButtonColor: '#10B981',
                            width: window.innerWidth <= 480 ? '90%' : undefined
                        });
                    } else if (!username) {
                        hasError = true;
                        Swal.fire({
                            icon: 'warning',
                            title: 'Username Required',
                            text: 'Please enter your username or NIC number.',
                            background: '#ffffff',
                            color: '#0f1724',
                            confirmButtonColor: '#10B981',
                            width: window.innerWidth <= 480 ? '90%' : undefined
                        });
                    } else if (!password) {
                        hasError = true;
                        Swal.fire({
                            icon: 'warning',
                            title: 'Password Required',
                            text: 'Please enter your password.',
                            background: '#ffffff',
                            color: '#0f1724',
                            confirmButtonColor: '#10B981',
                            width: window.innerWidth <= 480 ? '90%' : undefined
                        });
                    }

                    if (hasError) {
                        e.preventDefault();
                        return false;
                    }
                });
            }

            @if ($errors->any())
                let errorMessages = "";
                @foreach ($errors->all() as $err)
                    errorMessages += `• {{ addslashes($err) }}<br>`;
                @endforeach
                setTimeout(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: `<div style="text-align: left;">${errorMessages}</div>`,
                        background: '#ffffff',
                        color: '#0f1724',
                        confirmButtonColor: '#10B981',
                        width: window.innerWidth <= 480 ? '90%' : undefined
                    });
                }, 300);
            @endif

            @if (session('error'))
                setTimeout(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: "{{ addslashes(session('error')) }}",
                        background: '#ffffff',
                        color: '#0f1724',
                        confirmButtonColor: '#10B981',
                        width: window.innerWidth <= 480 ? '90%' : undefined
                    });
                }, 300);
            @endif

            @if (session('login_success'))
                setTimeout(() => {
                    const role = '{{ session('role') }}';
                    let redirectUrl = '/';

                    switch(role) {
                        case 'admin':
                            redirectUrl = '/admin/dashboard';
                            break;
                        case 'facilitator':
                            redirectUrl = '/facilitator/dashboard';
                            break;
                        case 'lead_farmer':
                            redirectUrl = '/lead-farmer/dashboard';
                            break;
                        case 'farmer':
                            redirectUrl = '/farmer/dashboard';
                            break;
                        case 'buyer':
                            redirectUrl = '/buyer/dashboard';
                            break;
                        default:
                            redirectUrl = '/';
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Welcome Back! 👋',
                        html: `
                            <div style="text-align: center;">
                                <p style="font-size: 18px; margin-bottom: 15px;">Welcome <b>{{ addslashes(session('name') ?? 'User') }}</b></p>
                                <div style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(59, 130, 246, 0.1)); padding: 15px; border-radius: 10px; margin: 15px 0;">
                                    <p style="margin: 5px 0;"><i class="fas fa-user-tag" style="color: #10B981;"></i> Role: <span style="text-transform: capitalize; font-weight: bold;">${role.replace('_', ' ')}</span></p>
                                    <p style="margin: 5px 0;"><i class="fas fa-clock" style="color: #3b82f6;"></i> Last Login: Just Now</p>
                                </div>
                                <p style="color: #6b7280; font-size: 14px; margin-top: 15px;">Redirecting to your dashboard...</p>
                            </div>
                        `,
                        background: '#ffffff',
                        color: '#0f1724',
                        confirmButtonColor: '#10B981',
                        timer: 2500,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        width: window.innerWidth <= 480 ? '90%' : '500px',
                        didClose: () => {
                            window.location.href = redirectUrl;
                        }
                    });
                }, 300);
            @endif

            @if (session('password_reset_success'))
                setTimeout(() => {
                    const username = '{{ session('username') }}';
                    const emailSent = {{ session('email_sent', 'false') }};
                    const smsSent = {{ session('sms_sent', 'false') }};

                    let deliveryMessage = '';
                    if (emailSent && smsSent) {
                        deliveryMessage = 'New credentials sent to your mail and SMS.';
                    } else if (emailSent) {
                        deliveryMessage = 'New credentials sent to your mail.';
                    } else if (smsSent) {
                        deliveryMessage = 'New credentials sent via SMS.';
                    } else {
                        deliveryMessage = 'Please remember your new credentials.';
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Password Reset Complete!',
                        html: `
                            <div style="text-align: center;">
                                <p style="font-size: 16px; margin-bottom: 15px;">Your password has been successfully reset.</p>
                                <p style="color: #10B981; margin: 10px 0;"> ${deliveryMessage}</p>
                                <p style="color: #6b7280; font-size: 14px; margin-top: 15px;">You can now login with your new credentials</p>
                            </div>
                        `,
                        background: '#ffffff',
                        color: '#0f1724',
                        confirmButtonColor: '#10B981',
                        confirmButtonText: 'Go to Login',
                        showCancelButton: false,
                        width: window.innerWidth <= 480 ? '90%' : '500px',
                        allowOutsideClick: false
                    });
                }, 300);
            @endif

            document.querySelectorAll('.input-group input').forEach(input => {
                const line = input.parentElement.querySelector('.input-focus-line');

                input.addEventListener('focus', function() {
                    if (line) line.style.width = '100%';
                });

                input.addEventListener('blur', function() {
                    if (line && !this.value) {
                        line.style.width = '0';
                    }
                });

                input.addEventListener('input', function() {
                    if (line) {
                        line.style.backgroundColor = this.value ? '#10B981' : '#3b82f6';
                    }
                });
            });

            document.querySelectorAll('.logo-section').forEach(logo => {
                logo.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                });

                logo.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });

            document.querySelectorAll('button, a').forEach(element => {
                element.addEventListener('focus', function() {
                    this.style.outline = '2px solid #10B981';
                    this.style.outlineOffset = '2px';
                });

                element.addEventListener('blur', function() {
                    this.style.outline = 'none';
                });
            });

            window.addEventListener('resize', function() {
                document.body.style.backgroundAttachment = window.innerWidth <= 480 ? 'scroll' : 'fixed';
            });
        });
    </script>
</body>
</html>
