@extends('public_master')

@section('title', 'Contact Us')

@section('content')
<div class="contact-page">
	<div class="contact-header">
		<div class="container">
			<h1>Contact Us</h1>
			<p>Get in touch with us</p>
		</div>
	</div>

	<div class="container contact-container">
		<div class="contact-wrapper">
			<div class="contact-form-wrapper">
				<div class="contact-form-card">
					<h2>Send Message</h2>
					<p class="form-subtitle">We'll get back to you soon</p>

					<form id="contactForm" method="POST" action="{{ route('contact.send') }}">
						@csrf

						<div class="form-grid">
							<div class="form-group">
								<label for="name">Full Name *</label>
								<input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" required>
								<div class="input-indicator"></div>
							</div>

							<div class="form-group">
								<label for="email">Email Address *</label>
								<input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" required>
								<div class="input-indicator"></div>
							</div>
						</div>

						<div class="form-group">
							<label for="subject">Subject</label>
							<input type="text" id="subject" name="subject" class="form-input" value="{{ old('subject') }}">
							<div class="input-indicator"></div>
						</div>

						<div class="form-group">
							<label for="message">Message *</label>
							<textarea id="message" name="message" class="form-input textarea" rows="4" required>{{ old('message') }}</textarea>
							<div class="input-indicator"></div>
						</div>

						<button type="submit" class="submit-btn">
							<span class="btn-text">Send Message</span>
							<span class="btn-icon">→</span>
							<div class="btn-spinner">
								<div class="spinner"></div>
							</div>
						</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('styles')
<style>
	:root {
		--primary: #10B981;
		--primary-dark: #059669;
		--bg: #ffffff;
		--surface: #f8fafc;
		--border: #e2e8f0;
		--text: #1e293b;
		--text-light: #64748b;
		--shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
		--shadow-hover: 0 8px 24px rgba(16, 185, 129, 0.12);
		--radius: 12px;
		--transition: all 0.25s ease;
	}

	.contact-page {
		background: var(--surface);
		min-height: 100vh;
	}

	.contact-header {
		background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
		padding: 40px 0;
		position: relative;
		overflow: hidden;
	}

	.contact-header::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
	}

	.contact-header .container {
		position: relative;
		z-index: 1;
	}

	.contact-header h1 {
		color: white;
		font-size: 2.5rem;
		font-weight: 700;
		margin: 0;
		text-align: center;
		animation: slideDown 0.6s ease;
	}

	.contact-header p {
		color: rgba(255, 255, 255, 0.9);
		font-size: 1.1rem;
		text-align: center;
		margin-top: 10px;
		animation: slideUp 0.6s ease 0.2s both;
	}

	.contact-container {
		padding: 40px 20px;
		max-width: 800px;
		margin: -40px auto 0;
		position: relative;
		z-index: 2;
	}

	.contact-wrapper {
		display: grid;
		gap: 30px;
	}

	.contact-form-wrapper {
		animation: fadeIn 0.8s ease 0.4s both;
	}

	.contact-form-card {
		background: var(--bg);
		border-radius: var(--radius);
		padding: 40px;
		box-shadow: var(--shadow);
		border: 1px solid var(--border);
		transition: var(--transition);
		position: relative;
		overflow: hidden;
	}

	.contact-form-card::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		width: 4px;
		height: 100%;
		background: var(--primary);
		transition: var(--transition);
	}

	.contact-form-card:hover {
		box-shadow: var(--shadow-hover);
		transform: translateY(-4px);
	}

	.contact-form-card:hover::before {
		width: 8px;
		background: linear-gradient(to bottom, var(--primary), var(--primary-dark));
	}

	.contact-form-card h2 {
		color: var(--text);
		font-size: 1.8rem;
		font-weight: 600;
		margin: 0 0 8px 0;
		position: relative;
		display: inline-block;
	}

	.contact-form-card h2::after {
		content: '';
		position: absolute;
		bottom: -4px;
		left: 0;
		width: 40px;
		height: 3px;
		background: var(--primary);
		border-radius: 2px;
		transition: var(--transition);
	}

	.contact-form-card:hover h2::after {
		width: 80px;
	}

	.form-subtitle {
		color: var(--text-light);
		font-size: 0.95rem;
		margin-bottom: 30px;
	}

	.form-grid {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 20px;
		margin-bottom: 20px;
	}

	.form-group {
		position: relative;
		margin-bottom: 24px;
	}

	.form-group label {
		display: block;
		color: var(--text);
		font-size: 0.9rem;
		font-weight: 500;
		margin-bottom: 8px;
		transition: var(--transition);
	}

	.form-input {
		width: 100%;
		padding: 12px 16px;
		background: var(--surface);
		border: 2px solid var(--border);
		border-radius: 8px;
		font-size: 1rem;
		color: var(--text);
		transition: var(--transition);
		font-family: inherit;
	}

	.form-input:hover {
		border-color: #cbd5e1;
		background: white;
	}

	.form-input:focus {
		outline: none;
		border-color: var(--primary);
		background: white;
		box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
	}

	.input-indicator {
		position: absolute;
		bottom: 0;
		left: 50%;
		transform: translateX(-50%);
		width: 0;
		height: 2px;
		background: var(--primary);
		transition: var(--transition);
		border-radius: 2px;
	}

	.form-input:focus ~ .input-indicator {
		width: 100%;
	}

	.textarea {
		min-height: 100px;
		resize: vertical;
		line-height: 1.5;
	}

	.submit-btn {
		position: relative;
		width: 100%;
		padding: 14px 24px;
		background: var(--primary);
		color: white;
		border: none;
		border-radius: 8px;
		font-size: 1rem;
		font-weight: 600;
		cursor: pointer;
		overflow: hidden;
		transition: var(--transition);
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 10px;
		margin-top: 10px;
	}

	.submit-btn::before {
		content: '';
		position: absolute;
		top: 50%;
		left: 50%;
		width: 0;
		height: 0;
		border-radius: 50%;
		background: rgba(255, 255, 255, 0.1);
		transform: translate(-50%, -50%);
		transition: width 0.6s, height 0.6s;
	}

	.submit-btn:hover {
		background: var(--primary-dark);
		transform: translateY(-2px);
		box-shadow: 0 6px 20px rgba(16, 185, 129, 0.25);
	}

	.submit-btn:hover::before {
		width: 300px;
		height: 300px;
	}

	.submit-btn:hover .btn-icon {
		transform: translateX(4px);
	}

	.btn-text {
		position: relative;
		z-index: 1;
	}

	.btn-icon {
		position: relative;
		z-index: 1;
		transition: var(--transition);
		font-size: 1.2rem;
	}

	.btn-spinner {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		display: none;
	}

	.spinner {
		width: 20px;
		height: 20px;
		border: 3px solid rgba(255, 255, 255, 0.3);
		border-top-color: white;
		border-radius: 50%;
		animation: spin 0.8s linear infinite;
	}

	.submit-btn.loading .btn-text,
	.submit-btn.loading .btn-icon {
		opacity: 0;
	}

	.submit-btn.loading .btn-spinner {
		display: block;
	}

	.error-message {
		color: #ef4444;
		font-size: 0.85rem;
		margin-top: 6px;
		display: block;
		animation: shake 0.3s ease;
	}

	.form-input.error {
		border-color: #ef4444;
		animation: shake 0.3s ease;
	}

	@keyframes slideDown {
		from {
			opacity: 0;
			transform: translateY(-20px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	@keyframes slideUp {
		from {
			opacity: 0;
			transform: translateY(20px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	@keyframes fadeIn {
		from {
			opacity: 0;
			transform: translateY(30px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	@keyframes spin {
		to {
			transform: rotate(360deg);
		}
	}

	@keyframes shake {
		0%, 100% {
			transform: translateX(0);
		}
		25% {
			transform: translateX(-5px);
		}
		75% {
			transform: translateX(5px);
		}
	}

	@media (max-width: 768px) {
		.contact-header h1 {
			font-size: 2rem;
		}

		.contact-header p {
			font-size: 1rem;
		}

		.contact-container {
			padding: 30px 15px;
			margin-top: -30px;
		}

		.contact-form-card {
			padding: 30px 20px;
		}

		.form-grid {
			grid-template-columns: 1fr;
			gap: 0;
		}
	}
</style>
@endsection

@section('scripts')
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const contactForm = document.getElementById('contactForm');
		const submitBtn = contactForm?.querySelector('.submit-btn');

		if (contactForm) {
			contactForm.addEventListener('submit', async function(e) {
				e.preventDefault();

				if (!submitBtn) return;

				const formData = new FormData(this);
				const isValid = validateForm(formData);

				if (!isValid) return;

				submitBtn.classList.add('loading');
				submitBtn.disabled = true;

				try {
					const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

					const response = await fetch('{{ route("contact.send") }}', {
						method: 'POST',
						body: formData,
						headers: {
							'X-Requested-With': 'XMLHttpRequest',
							'X-CSRF-TOKEN': csrfToken
						}
					});

					if (!response.ok) {
						throw new Error(`Server error: ${response.status}`);
					}

					const result = await response.json();

					if (result.success) {
						showSuccess(result.message);
						contactForm.reset();
					} else {
						throw new Error(result.message || 'Failed to send message');
					}
				} catch (error) {
					showError(error.message);
				} finally {
					submitBtn.classList.remove('loading');
					submitBtn.disabled = false;
				}
			});
		}

		function validateForm(formData) {
			clearErrors();

			let isValid = true;

			const name = formData.get('name')?.trim();
			const email = formData.get('email')?.trim();
			const message = formData.get('message')?.trim();

			if (!name || name.length < 2) {
				showFieldError('name', 'Name must be at least 2 characters');
				isValid = false;
			}

			const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
			if (!email || !emailRegex.test(email)) {
				showFieldError('email', 'Please enter a valid email address');
				isValid = false;
			}

			if (!message || message.length < 10) {
				showFieldError('message', 'Message must be at least 10 characters');
				isValid = false;
			}

			return isValid;
		}

		function showFieldError(fieldId, message) {
			const field = document.getElementById(fieldId);
			if (!field) return;

			const group = field.closest('.form-group');
			if (!group) return;

			field.classList.add('error');

			let errorDiv = group.querySelector('.error-message');
			if (!errorDiv) {
				errorDiv = document.createElement('div');
				errorDiv.className = 'error-message';
				group.appendChild(errorDiv);
			}

			errorDiv.textContent = message;

			field.focus();
		}

		function clearErrors() {
			const errorFields = document.querySelectorAll('.form-input.error');
			errorFields.forEach(field => {
				field.classList.remove('error');
			});

			const errorMessages = document.querySelectorAll('.error-message');
			errorMessages.forEach(msg => {
				msg.remove();
			});
		}

		function showSuccess(message) {
			const alert = document.createElement('div');
			alert.className = 'success-alert';
			alert.innerHTML = `
				<div style="position: fixed; top: 20px; right: 20px; background: var(--primary); color: white; padding: 16px 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); z-index: 1000; animation: slideInRight 0.3s ease; max-width: 400px;">
					<strong>Success!</strong> ${message}
				</div>
			`;

			document.body.appendChild(alert);

			setTimeout(() => {
				alert.remove();
			}, 4000);

			const style = document.createElement('style');
			style.textContent = `
				@keyframes slideInRight {
					from {
						transform: translateX(100%);
						opacity: 0;
					}
					to {
						transform: translateX(0);
						opacity: 1;
					}
				}
			`;
			document.head.appendChild(style);
		}

		function showError(message) {
			const alert = document.createElement('div');
			alert.className = 'error-alert';
			alert.innerHTML = `
				<div style="position: fixed; top: 20px; right: 20px; background: #ef4444; color: white; padding: 16px 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); z-index: 1000; animation: slideInRight 0.3s ease; max-width: 400px;">
					<strong>Error!</strong> ${message}
				</div>
			`;

			document.body.appendChild(alert);

			setTimeout(() => {
				alert.remove();
			}, 4000);
		}

		const inputs = document.querySelectorAll('.form-input');
		inputs.forEach(input => {
			input.addEventListener('input', function() {
				if (this.classList.contains('error')) {
					this.classList.remove('error');
					const errorMsg = this.closest('.form-group')?.querySelector('.error-message');
					if (errorMsg) {
						errorMsg.remove();
					}
				}
			});
		});

		contactForm?.addEventListener('keypress', function(e) {
			if (e.key === 'Enter' && e.target.type !== 'textarea') {
				e.preventDefault();
			}
		});
	});
</script>
@endsection
