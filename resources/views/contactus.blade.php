@extends('public_master')

@section('title', 'Contact Us')

@section('content')
<div class="contact-page">
	<div class="page-header">
		<div class="container">
			<h1 class="page-title">Contact Us</h1>
			<p class="page-subtitle">We'd love to hear from you</p>
		</div>
	</div>

	<div class="container">
		<div class="form-wrapper">
			<div class="form-container">
				<div class="form-card">
					<div class="form-header">
						<h2 class="form-heading">Send Message</h2>
						<p class="form-info">We'll get back within 24h</p>
					</div>

					<form id="contactForm" method="POST" action="{{ route('contact.send') }}">
						@csrf

						<div class="field-group">
							<label for="name" class="field-label">
								<i class="fas fa-user"></i>
								<span>Full Name *</span>
							</label>
							<input type="text" id="name" name="name" class="field-input" value="{{ old('name') }}" placeholder="John Doe" required>
						</div>

						<div class="field-group">
							<label for="email" class="field-label">
								<i class="fas fa-envelope"></i>
								<span>Email *</span>
							</label>
							<input type="email" id="email" name="email" class="field-input" value="{{ old('email') }}" placeholder="john@example.com" required>
						</div>

						<div class="field-group">
							<label for="subject" class="field-label">
								<i class="fas fa-tag"></i>
								<span>Subject</span>
							</label>
							<input type="text" id="subject" name="subject" class="field-input" value="{{ old('subject') }}" placeholder="How can we help?">
						</div>

						<div class="field-group">
							<label for="message" class="field-label">
								<i class="fas fa-comment"></i>
								<span>Message *</span>
							</label>
							<textarea id="message" name="message" class="field-input field-textarea" rows="3" placeholder="Your message..." required>{{ old('message') }}</textarea>
						</div>

						<button type="submit" class="send-btn" id="submitBtn">
							<span class="btn-text">Send</span>
							<i class="fas fa-paper-plane btn-icon"></i>
							<div class="btn-loader">
								<div class="loader-spin"></div>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="{{ asset('css/contactus.css') }}">
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
	const contactForm = document.getElementById('contactForm');
	const submitBtn = document.getElementById('submitBtn');

	@if(session('success'))
		Swal.fire({
			icon: 'success',
			title: 'Success!',
			text: '{{ session('success') }}',
			confirmButtonColor: '#10B981',
			timer: 5000,
			showConfirmButton: true
		});
	@endif

	@if(session('error'))
		Swal.fire({
			icon: 'error',
			title: 'Error!',
			text: '{{ session('error') }}',
			confirmButtonColor: '#10B981'
		});
	@endif

	@if($errors->any())
		Swal.fire({
			icon: 'error',
			title: 'Validation Error',
			html: `
				<div style="text-align: left;">
					<ul style="margin-left: 20px; color: #ef4444;">
						@foreach($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			`,
			confirmButtonColor: '#10B981'
		});
	@endif

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

				const result = await response.json();

				if (!response.ok) {
					if (response.status === 422) {
						const errors = result.errors || {};
						const errorList = Object.values(errors).flat().join('<br>');
						throw { type: 'validation', message: errorList, errors: errors };
					} else if (response.status === 429) {
						throw { type: 'rate', message: 'Too many requests. Please wait a moment.' };
					} else {
						throw { type: 'server', message: result.message || 'Server error occurred' };
					}
				}

				if (result.success) {
					Swal.fire({
						icon: 'success',
						title: 'Sent!',
						text: undefined,
						html: result.message || 'We\'ll reply soon.',
						confirmButtonColor: '#10B981',
						timer: 7500,
						showConfirmButton: false
					});
					contactForm.reset();
				} else {
					throw { type: 'unknown', message: result.message || 'Failed to send message' };
				}
			} catch (error) {
				let errorTitle = 'Error';
				let errorMessage = error.message || 'Something went wrong';
				let errorIcon = 'error';

				if (error.type === 'validation') {
					errorTitle = 'Validation Failed';
					errorIcon = 'warning';
					if (error.errors) {
						let errorHtml = '<div style="text-align: left;"><ul style="margin-left: 20px;">';
						Object.keys(error.errors).forEach(field => {
							error.errors[field].forEach(msg => {
								errorHtml += `<li style="color: #ef4444; margin-bottom: 5px;">${msg}</li>`;
							});
						});
						errorHtml += '</ul></div>';
						errorMessage = errorHtml;
					}
				} else if (error.type === 'rate') {
					errorTitle = 'Too Many Requests';
					errorMessage = 'Please wait a moment before trying again.';
				} else if (error.type === 'server') {
					errorTitle = 'Server Error';
					errorMessage = 'Unable to process your request. Please try again later.';
				}

				Swal.fire({
					icon: errorIcon,
					title: errorTitle,
					html: errorMessage,
					confirmButtonColor: '#10B981'
				});
			} finally {
				submitBtn.classList.remove('loading');
				submitBtn.disabled = false;
			}
		});
	}

	function validateForm(formData) {
		clearErrors();

		let isValid = true;
		const errors = [];

		const name = formData.get('name')?.trim();
		const email = formData.get('email')?.trim();
		const message = formData.get('message')?.trim();

		if (!name || name.length < 2) {
			showFieldError('name', 'Name must be at least 2 characters');
			errors.push('Name is too short');
			isValid = false;
		} else if (name.length > 100) {
			showFieldError('name', 'Name is too long');
			errors.push('Name exceeds maximum length');
			isValid = false;
		}

		const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		if (!email) {
			showFieldError('email', 'Email is required');
			errors.push('Email is required');
			isValid = false;
		} else if (!emailRegex.test(email)) {
			showFieldError('email', 'Please enter a valid email address');
			errors.push('Invalid email format');
			isValid = false;
		}

		if (!message) {
			showFieldError('message', 'Message is required');
			errors.push('Message is required');
			isValid = false;
		} else if (message.length < 5) {
			showFieldError('message', 'Message must be at least 5 characters');
			errors.push('Message is too short');
			isValid = false;
		} else if (message.length > 1000) {
			showFieldError('message', 'Message is too long (max 1000 characters)');
			errors.push('Message exceeds maximum length');
			isValid = false;
		}

		if (!isValid) {
			let errorHtml = '<div style="text-align: left;"><ul style="margin-left: 20px;">';
			errors.forEach(err => {
				errorHtml += `<li style="color: #ef4444; margin-bottom: 5px;">${err}</li>`;
			});
			errorHtml += '</ul></div>';

			Swal.fire({
				icon: 'warning',
				title: 'Please Check Your Input',
				html: errorHtml,
				confirmButtonColor: '#10B981'
			});
		}

		return isValid;
	}

	function showFieldError(fieldId, message) {
		const field = document.getElementById(fieldId);
		if (!field) return;

		const group = field.closest('.field-group');
		if (!group) return;

		field.classList.add('error');

		let errorDiv = group.querySelector('.error-msg');
		if (!errorDiv) {
			errorDiv = document.createElement('span');
			errorDiv.className = 'error-msg';
			group.appendChild(errorDiv);
		}

		errorDiv.textContent = message;
	}

	function clearErrors() {
		document.querySelectorAll('.field-input.error').forEach(f => f.classList.remove('error'));
		document.querySelectorAll('.error-msg').forEach(e => e.remove());
	}

	document.querySelectorAll('.field-input').forEach(input => {
		input.addEventListener('input', function() {
			if (this.classList.contains('error')) {
				this.classList.remove('error');
				this.closest('.field-group')?.querySelector('.error-msg')?.remove();
			}
		});
	});

	contactForm?.addEventListener('keypress', function(e) {
		if (e.key === 'Enter' && e.target.type !== 'textarea') {
			e.preventDefault();
		}
	});

	@if(session('validation_errors'))
		const validationErrors = @json(session('validation_errors'));
		let errorHtml = '<div style="text-align: left;"><ul style="margin-left: 20px;">';
		Object.keys(validationErrors).forEach(field => {
			validationErrors[field].forEach(msg => {
				errorHtml += `<li style="color: #ef4444; margin-bottom: 5px;">${msg}</li>`;
			});
		});
		errorHtml += '</ul></div>';

		Swal.fire({
			icon: 'error',
			title: 'Validation Failed',
			html: errorHtml,
			confirmButtonColor: '#10B981'
		});
	@endif
});
</script>
@endsection
