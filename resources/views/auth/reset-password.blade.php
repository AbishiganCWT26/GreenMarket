<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>GreenMarket | Reset Password</title>
	<link rel="stylesheet" href="{{ asset('css/login.css') }}" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="{{ asset('js/form-validation.js') }}"></script>
	<style>
		:root {
			--primary: #10B981;
			--primary-hover: #059669;
			--bg-light: #f8fafc;
			--border: #e2e8f0;
			--text-main: #1e293b;
			--text-muted: #64748b;
			--radius-lg: 12px;
			--radius-md: 8px;
			--radius-sm: 6px;
			--shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
		}

		.password-strength { margin-bottom: 1rem; }
		.strength-bar {
			height: 6px;
			background-color: #e2e8f0;
			border-radius: 3px;
			overflow: hidden;
			margin-top: 5px;
		}
		.strength-fill {
			height: 100%;
			width: 0%;
			transition: all 0.3s ease;
		}
		
		.requirements-grid {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 8px;
			margin-top: 10px;
			padding: 12px;
			background: #f8fafc;
			border-radius: var(--radius-md);
			border: 1px solid var(--border);
		}
		
		.rule-item {
			font-size: 0.75rem;
			display: flex;
			align-items: center;
			gap: 6px;
			color: #64748b;
		}
		
		.rule-item.valid { color: #10B981; }
		.rule-item.invalid { color: #ef4444; }
		
		#passwordMatch small {
			font-size: 0.75rem;
			display: flex;
			align-items: center;
			gap: 5px;
		}

		.d-none { display: none !important; }
		.text-success { color: #10B981 !important; }
		.text-danger { color: #ef4444 !important; }
		.mt-1 { margin-top: 0.25rem !important; }
		.mt-2 { margin-top: 0.5rem !important; }
		.mt-3 { margin-top: 1rem !important; }
		.mt-4 { margin-top: 1.5rem !important; }
		.mb-1 { margin-bottom: 0.25rem !important; }
		.mb-2 { margin-bottom: 0.5rem !important; }
		.mb-3 { margin-bottom: 1rem !important; }
		.d-flex { display: flex !important; }
		.justify-content-between { justify-content: space-between !important; }
		.align-items-center { align-items: center !important; }

		.info-box {
			background: rgba(59,130,246,0.05);
			border: 1px solid rgba(59,130,246,0.1);
			border-radius: var(--radius-sm);
			padding: 10px;
			margin-bottom: 20px;
			text-align: center;
			font-size: 0.85rem;
		}
		.info-box i { color: #3b82f6; margin-right: 6px; }
		
		.password-wrapper { position: relative; }
		.password-toggle {
			position: absolute;
			right: 12px;
			top: 50%;
			transform: translateY(-50%);
			background: none;
			border: none;
			color: #94a3b8;
			cursor: pointer;
			padding: 4px;
			z-index: 10;
		}
		.password-toggle:hover { color: var(--primary); }
	</style>
</head>
<body>
	<div class="auth-container">
		<div class="auth-card">
			<div class="auth-header" onclick="window.location.href='/'" role="button">
				<img src="{{ asset('assets/images/Logo Green Market.png') }}" alt="GreenMarket" class="auth-logo">
				<div class="auth-brand">
					<h1>GreenMarket</h1>
					<p>create new password</p>
				</div>
			</div>

			<div class="auth-body">
				<div class="auth-title">
					<h2><i class="fas fa-lock" style="color: var(--primary);"></i> Reset Password</h2>
					<p>Create a strong password</p>
				</div>

				<div class="info-box">
					<i class="fas fa-user-shield"></i>
					Reset for: <strong>{{ session('reset_username') }}</strong>
				</div>

				<form id="resetForm" method="POST" action="{{ route('password.reset.submit') }}">
					@csrf

					<div class="input-field">
						<label><i class="fas fa-key"></i> New Password</label>
						<div class="password-wrapper">
							<input type="password" id="password" name="password" placeholder="Enter new password" required>
							<button type="button" class="password-toggle" data-target="password">
								<i class="fas fa-eye"></i>
							</button>
						</div>
						
						<div class="password-strength mt-3">
							<div class="d-flex justify-content-between align-items-center mb-1">
								<small style="font-size: 0.75rem;">Strength: <span id="strengthText" style="font-weight: 600;">None</span></small>
							</div>
							<div class="strength-bar" id="strengthBar">
								<div class="strength-fill"></div>
							</div>
						</div>

						<div class="requirements mt-3">
							<h6 class="mb-2" style="font-size: 0.85rem; font-weight: 600;">Security Requirements:</h6>
							<div class="requirements-grid">
								<div id="rule-length" class="rule-item invalid"><i class="fas fa-times-circle"></i> 8+ characters</div>
								<div id="rule-number" class="rule-item invalid"><i class="fas fa-times-circle"></i> At least 1 number</div>
								<div id="rule-capital" class="rule-item invalid"><i class="fas fa-times-circle"></i> 1 Uppercase</div>
								<div id="rule-lowercase" class="rule-item invalid"><i class="fas fa-times-circle"></i> 1 Lowercase</div>
								<div id="rule-special" class="rule-item invalid"><i class="fas fa-times-circle"></i> 1 Special char</div>
								<div id="rule-no-space" class="rule-item invalid"><i class="fas fa-times-circle"></i> No spaces</div>
								<div id="rule-no-repeat" class="rule-item invalid"><i class="fas fa-times-circle"></i> No repetition</div>
								<div id="rule-no-sequence" class="rule-item invalid"><i class="fas fa-times-circle"></i> No sequence</div>
								<div id="rule-not-common" class="rule-item invalid"><i class="fas fa-times-circle"></i> Not common</div>
								<div id="rule-no-links" class="rule-item invalid"><i class="fas fa-times-circle"></i> No links</div>
								<div id="rule-no-personal" class="rule-item invalid"><i class="fas fa-times-circle"></i> No personal info</div>
							</div>
						</div>
					</div>

					<div class="input-field mt-4">
						<label><i class="fas fa-key"></i> Confirm Password</label>
						<div class="password-wrapper">
							<input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" required>
							<button type="button" class="password-toggle" data-target="password_confirmation">
								<i class="fas fa-eye"></i>
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

					<button type="submit" class="auth-btn mt-4" id="resetBtn">
						<i class="fas fa-sync-alt"></i> Reset Password
					</button>
				</form>

				<div class="auth-back">
					<a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Back to Login</a>
				</div>
			</div>
		</div>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const passInput = document.getElementById('password');
			const confirmInput = document.getElementById('password_confirmation');
			const strengthBar = document.getElementById('strengthBar');
			const strengthText = document.getElementById('strengthText');
			const matchIndicator = document.getElementById('passwordMatch');
			const resetForm = document.getElementById('resetForm');
			const resetBtn = document.getElementById('resetBtn');

			const username = "{{ session('reset_username') }}";
			const email = "{{ session('reset_email') }}";

			// Password toggle functionality
			document.querySelectorAll('.password-toggle').forEach(btn => {
				btn.addEventListener('click', function() {
					const target = document.getElementById(this.dataset.target);
					const icon = this.querySelector('i');
					if (target.type === 'password') {
						target.type = 'text';
						icon.classList.replace('fa-eye', 'fa-eye-slash');
					} else {
						target.type = 'password';
						icon.classList.replace('fa-eye-slash', 'fa-eye');
					}
				});
			});

			function validateUI() {
				const passValue = passInput.value;
				const confirmValue = confirmInput.value;

				// 1. Password Strength and Rules
				if (!passValue) {
					strengthText.textContent = 'None';
					strengthText.style.color = '#cbd5e1';
					strengthBar.querySelector('.strength-fill').style.width = '0%';
					
					// Reset rules UI
					updatePasswordRuleFeedback({ 
						rules: { 
							'length': false, 'number': false, 'capital': false, 'lowercase': false, 
							'special': false, 'no-space': false, 'no-repeat': false, 'no-sequence': false, 
							'not-common': false, 'no-links': false, 'no-personal': false 
						} 
					});
				} else {
					const result = validateAdvancedPassword(passValue, { username, email });
					
					// Update Strength Bar
					strengthText.textContent = result.strengthText;
					strengthText.style.color = result.color;
					const fill = strengthBar.querySelector('.strength-fill');
					fill.style.width = result.percent + '%';
					fill.style.backgroundColor = result.color;

					// Update Rules Checklist
					updatePasswordRuleFeedback(result);
				}

				// 2. Password Matching
				const success = matchIndicator.querySelector('.text-success');
				const error = matchIndicator.querySelector('.text-danger');

				if (passValue && confirmValue) {
					if (passValue === confirmValue) {
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

				// Update Button State (Optional, but good for UX)
				// resetBtn.disabled = !status.isValid || passValue !== confirmValue;
			}

			passInput.addEventListener('input', validateUI);
			confirmInput.addEventListener('input', validateUI);

			resetForm.addEventListener('submit', function(e) {
				const passValue = passInput.value;
				const confirmValue = confirmInput.value;
				const result = validateAdvancedPassword(passValue, { username, email });

				if (!result.isValid) {
					e.preventDefault();
					Swal.fire({
						icon: 'warning',
						title: 'Weak Password',
						text: 'Please meet all 11 security requirements for a strong password.',
						confirmButtonColor: '#10B981'
					});
					return;
				}

				if (passValue !== confirmValue) {
					e.preventDefault();
					Swal.fire({
						icon: 'error',
						title: 'Passwords Mismatch',
						text: 'New password and confirmation do not match.',
						confirmButtonColor: '#10B981'
					});
					return;
				}

				resetBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resetting...';
				resetBtn.disabled = true;
			});

			// Initial validation
			validateUI();
		});
	</script>
</body>
</html>
/html>