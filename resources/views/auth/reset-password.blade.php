<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>GreenMarket | Reset Password</title>
	<link rel="stylesheet" href="{{ asset('css/login.css') }}" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<style>
		.password-strength {
			margin-top: 8px;
			height: 4px;
			background: #e5e7eb;
			border-radius: 2px;
			overflow: hidden;
			position: relative;
		}

		.strength-meter {
			height: 100%;
			width: 0;
			border-radius: 2px;
			transition: all 0.3s ease;
		}

		.strength-text {
			font-size: 12px;
			color: #6b7280;
			margin-top: 4px;
			text-align: right;
		}

		.password-rules {
			background: rgba(16, 185, 129, 0.05);
			border: 1px solid rgba(16, 185, 129, 0.1);
			border-radius: 10px;
			padding: 12px;
			margin: 15px 0;
			font-size: 13px;
		}

		.password-rules ul {
			list-style: none;
			padding-left: 0;
			margin: 0;
		}

		.password-rules li {
			margin-bottom: 5px;
			display: flex;
			align-items: center;
			gap: 8px;
		}

		.password-rules i {
			font-size: 12px;
			width: 16px;
		}

		.rule-valid {
			color: #10B981;
		}

		.rule-invalid {
			color: #6b7280;
		}

		.info-box {
			background: rgba(59, 130, 246, 0.05);
			border: 1px solid rgba(59, 130, 246, 0.1);
			border-radius: 10px;
			padding: 12px;
			margin-bottom: 20px;
			text-align: center;
			font-size: 14px;
		}

		.info-box i {
			color: #3b82f6;
			margin-right: 5px;
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="login-wrapper">
			<div class="logo-section" onclick="window.location.href='/'" tabindex="0" role="button">
				<div class="logo-container">
					<img src="{{ asset('assets/images/logo-4.png') }}" alt="GreenMarket Logo" class="logo">
					<h1 class="logo-title">GreenMarket</h1>
				</div>
				<p class="logo-tagline">Set your new password</p>
			</div>

			<div class="login-card">
				<div class="card-header">
					<h2><i class="fas fa-lock"></i> Reset Password</h2>
					<p>Create a new strong password</p>
				</div>

				<div class="info-box">
					<i class="fas fa-user-shield"></i>
					Password reset for: <strong>{{ session('reset_username') }}</strong>
				</div>

				<form id="resetForm" method="POST" action="{{ route('password.reset.submit') }}" class="login-form">
					@csrf

					<div class="input-group">
						<label for="password"><i class="fas fa-key"></i> New Password</label>
						<div class="password-wrapper">
							<input type="password" id="password" name="password" placeholder="Enter new password" required>
							<button type="button" class="password-toggle" data-target="password">
								<i class="fas fa-eye"></i>
							</button>
							<div class="input-focus-line"></div>
						</div>
						<div class="password-strength">
							<div class="strength-meter" id="strengthMeter"></div>
						</div>
						<div class="strength-text" id="strengthText"></div>
					</div>

					<div class="input-group">
						<label for="password_confirmation"><i class="fas fa-key"></i> Confirm Password</label>
						<div class="password-wrapper">
							<input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" required>
							<button type="button" class="password-toggle" data-target="password_confirmation">
								<i class="fas fa-eye"></i>
							</button>
							<div class="input-focus-line"></div>
						</div>
					</div>

					<div class="password-rules">
						<ul>
							<li id="rule-length">
								<i class="fas fa-circle"></i>
								<span>At least 8 characters</span>
							</li>
							<li id="rule-uppercase">
								<i class="fas fa-circle"></i>
								<span>At least one uppercase letter</span>
							</li>
							<li id="rule-lowercase">
								<i class="fas fa-circle"></i>
								<span>At least one lowercase letter</span>
							</li>
							<li id="rule-number">
								<i class="fas fa-circle"></i>
								<span>At least one number</span>
							</li>
							<li id="rule-special">
								<i class="fas fa-circle"></i>
								<span>At least one special character</span>
							</li>
						</ul>
					</div>

					@if ($errors->any())
						<div style="color: #ef4444; text-align: center; font-size: 14px; margin: 10px 0;">
							{{ $errors->first() }}
						</div>
					@endif

					<button type="submit" class="login-btn" id="resetBtn">
						<i class="fas fa-sync-alt"></i> Reset Password
					</button>
				</form>

				<div class="quick-links">
					<a href="{{ route('login') }}" class="home-link">
						<i class="fas fa-arrow-left"></i> Back to Login
					</a>
				</div>
			</div>
		</div>
	</div>

	<script>
		function checkPasswordStrength(password) {
			let strength = 0;
			const rules = {
				length: false,
				uppercase: false,
				lowercase: false,
				number: false,
				special: false
			};

			if (password.length >= 8) {
				strength += 20;
				rules.length = true;
			}

			if (/[A-Z]/.test(password)) {
				strength += 20;
				rules.uppercase = true;
			}

			if (/[a-z]/.test(password)) {
				strength += 20;
				rules.lowercase = true;
			}

			if (/[0-9]/.test(password)) {
				strength += 20;
				rules.number = true;
			}

			if (/[^A-Za-z0-9]/.test(password)) {
				strength += 20;
				rules.special = true;
			}

			return { strength, rules };
		}

		function updatePasswordStrength() {
			const password = document.getElementById('password').value;
			const confirmPassword = document.getElementById('password_confirmation').value;
			const { strength, rules } = checkPasswordStrength(password);
			const meter = document.getElementById('strengthMeter');
			const text = document.getElementById('strengthText');
			const resetBtn = document.getElementById('resetBtn');

			let color = '#ef4444';
			let message = 'Very Weak';

			if (strength >= 20) {
				color = '#f59e0b';
				message = 'Weak';
			}
			if (strength >= 40) {
				color = '#f59e0b';
				message = 'Fair';
			}
			if (strength >= 60) {
				color = '#3b82f6';
				message = 'Good';
			}
			if (strength >= 80) {
				color = '#10B981';
				message = 'Strong';
			}
			if (strength >= 100) {
				color = '#059669';
				message = 'Very Strong';
			}

			meter.style.width = strength + '%';
			meter.style.backgroundColor = color;
			text.textContent = message;
			text.style.color = color;

			Object.keys(rules).forEach(rule => {
				const icon = document.querySelector(`#rule-${rule} i`);
				const text = document.querySelector(`#rule-${rule} span`);

				if (rules[rule]) {
					icon.className = 'fas fa-check-circle rule-valid';
					icon.style.color = '#10B981';
				} else {
					icon.className = 'fas fa-circle rule-invalid';
					icon.style.color = '#9ca3af';
				}
			});

			const passwordsMatch = password === confirmPassword;
			const isStrong = strength >= 80;
			const allRulesMet = Object.values(rules).every(rule => rule);

			resetBtn.disabled = !(passwordsMatch && isStrong && allRulesMet);

			if (resetBtn.disabled) {
				resetBtn.style.opacity = '0.7';
				resetBtn.style.cursor = 'not-allowed';
			} else {
				resetBtn.style.opacity = '1';
				resetBtn.style.cursor = 'pointer';
			}
		}

		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.password-toggle').forEach(button => {
				button.addEventListener('click', function() {
					const targetId = this.getAttribute('data-target');
					const targetInput = document.getElementById(targetId);
					const icon = this.querySelector('i');

					if (targetInput.type === 'password') {
						targetInput.type = 'text';
						icon.classList.replace('fa-eye', 'fa-eye-slash');
						icon.style.color = '#10B981';
					} else {
						targetInput.type = 'password';
						icon.classList.replace('fa-eye-slash', 'fa-eye');
						icon.style.color = '#6b7280';
					}
				});
			});

			document.getElementById('password').addEventListener('input', updatePasswordStrength);
			document.getElementById('password_confirmation').addEventListener('input', updatePasswordStrength);

			document.getElementById('resetForm').addEventListener('submit', function(e) {
				const password = document.getElementById('password').value;
				const confirmPassword = document.getElementById('password_confirmation').value;
				const { strength, rules } = checkPasswordStrength(password);

				if (password !== confirmPassword) {
					e.preventDefault();
					Swal.fire({
						icon: 'error',
						title: 'Passwords Don\'t Match',
						text: 'Please make sure both passwords match.',
						background: '#ffffff',
						color: '#0f1724',
						confirmButtonColor: '#10B981'
					});
					return false;
				}

				if (strength < 80 || !Object.values(rules).every(rule => rule)) {
					e.preventDefault();
					Swal.fire({
						icon: 'warning',
						title: 'Weak Password',
						html: `
							<p>Your password must:</p>
							<ul style="text-align: left; margin: 10px 20px;">
								<li>Be at least 8 characters</li>
								<li>Include uppercase & lowercase letters</li>
								<li>Include numbers</li>
								<li>Include special characters</li>
							</ul>
						`,
						background: '#ffffff',
						color: '#0f1724',
						confirmButtonColor: '#10B981'
					});
					return false;
				}
			});

			updatePasswordStrength();

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
		});
	</script>
</body>
</html>
