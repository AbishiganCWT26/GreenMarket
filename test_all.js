
document.addEventListener("DOMContentLoaded", () => {
    const loader = document.getElementById("page-loader");
    const percentageEl = document.getElementById("percentage");

    const images = Array.from(document.images);
    const totalImages = images.length;
    let loadedImages = 0;
    let startTime = Date.now();
    let isFinished = false;

    // Maximum loading time fallback (5 seconds)
    setTimeout(() => {
        finishLoading();
    }, 5000);

    if (totalImages === 0) {
        finishLoading();
        return;
    }

    function markLoaded(img) {
        if (img.dataset.counted) return; // prevent double counting
        img.dataset.counted = "true";

        loadedImages++;
        updateProgress();
    }

    function updateProgress() {
        const percent = Math.round((loadedImages / totalImages) * 100);
        percentageEl.textContent = percent + "%";

        if (loadedImages >= totalImages && !isFinished) {
            // Wait at least 1 second before hiding the loader
            const elapsedTime = Date.now() - startTime;
            const remainingTime = Math.max(0, 1000 - elapsedTime);
            
            if (remainingTime > 0) {
                // Wait for the remaining time before finishing
                setTimeout(() => {
                    finishLoading();
                }, remainingTime);
            } else {
                finishLoading();
            }
        }
    }

    images.forEach(img => {
        // Already loaded or already failed
        if (img.complete) {
            markLoaded(img);
        } else {
            img.addEventListener("load", () => markLoaded(img), { once: true });
            img.addEventListener("error", () => markLoaded(img), { once: true });
        }
    });

    function finishLoading() {
        if (isFinished) return;
        isFinished = true;
        
        percentageEl.textContent = "100%";

        loader.style.transition = "opacity 0.8s ease, transform 0.8s ease";
        loader.style.opacity = "0";
        loader.style.transform = "scale(0.95)";

        setTimeout(() => {
            loader.remove();
        }, 800);
    }
});


		document.addEventListener('DOMContentLoaded', function() {
			const mobileMenuBtn = document.getElementById('mobileMenuBtn');
			const mobileNav = document.getElementById('mobileNav');
			const userToggle = document.getElementById('userToggle');
			const dropdownMenu = document.getElementById('dropdownMenu');
			const mobileNavItems = document.querySelectorAll('.mobile-nav-item');

			mobileMenuBtn.addEventListener('click', function(e) {
				e.stopPropagation();
				mobileNav.classList.toggle('active');
				const icon = this.querySelector('i');
				icon.classList.toggle('fa-bars');
				icon.classList.toggle('fa-times');
			});

			if (userToggle && dropdownMenu) {
				userToggle.addEventListener('click', function(e) {
					e.stopPropagation();
					dropdownMenu.classList.toggle('active');
					const icon = this.querySelector('.fa-chevron-down');
					if (icon) {
						icon.style.transform = dropdownMenu.classList.contains('active') ? 'rotate(180deg)' : 'rotate(0)';
					}
				});

				document.addEventListener('click', function(e) {
					if (!userToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
						dropdownMenu.classList.remove('active');
						const icon = userToggle.querySelector('.fa-chevron-down');
						if (icon) {
							icon.style.transform = 'rotate(0)';
						}
					}
				});
			}

			mobileNavItems.forEach(item => {
				item.addEventListener('click', function() {
					if (window.innerWidth <= 991) {
						mobileNav.classList.remove('active');
						mobileMenuBtn.querySelector('i').classList.remove('fa-times');
						mobileMenuBtn.querySelector('i').classList.add('fa-bars');
					}
				});
			});

			window.addEventListener('resize', function() {
				if (window.innerWidth > 991) {
					mobileNav.classList.remove('active');
					if (mobileMenuBtn.querySelector('i')) {
						mobileMenuBtn.querySelector('i').classList.remove('fa-times');
						mobileMenuBtn.querySelector('i').classList.add('fa-bars');
					}
				}
			});

			setTimeout(function() {
				const gadget = document.querySelector('.goog-te-gadget-simple');
				if (!gadget) return;

				let previousLangText = gadget.innerText || 'en';

				const observer = new MutationObserver(function(mutations) {
					mutations.forEach(function(mut) {
						if (mut.type === 'characterData' || mut.type === 'childList') {
							const currentText = gadget.innerText || '';
							if (currentText !== previousLangText && currentText.trim() !== '') {
								previousLangText = currentText;
							}
						}
					});
				});

				if (gadget) {
					observer.observe(gadget, { childList: true, subtree: true, characterData: true });
				}
			}, 800);

			window.addEventListener('error', function(e) {
				if (e.target && (e.target.src || '').includes('translate.google')) {
					e.preventDefault();
					Swal.fire({
						 imageUrl: 'http://localhost:8000/assets/icons/Gif/error2.gif', imageWidth: 60, imageHeight: 60 ,
						title: 'translation error',
						text: 'google translate failed to load. please refresh.',
						confirmButtonColor: '#059669',
						background: '#ffffff',
						iconColor: '#f59e0b'
					});
				}
			}, true);

			
			
					});

	

document.addEventListener('DOMContentLoaded', function() {
	const backToTop = document.getElementById('backToTop');
	const scrollHandler = () => {
		backToTop.classList.toggle('visible', window.pageYOffset > 300);
	};
	window.addEventListener('scroll', scrollHandler);
	scrollHandler();
	backToTop.addEventListener('click', (e) => {
		e.preventDefault();
		window.scrollTo({ top: 0, behavior: 'smooth' });
	});
	const socialBtns = document.querySelectorAll('.social-btn');
	socialBtns.forEach(btn => {
		btn.addEventListener('click', function(e) {
			const href = this.getAttribute('href');
			const platform = this.dataset.platform;
			if (href === '#') {
				e.preventDefault();
				Swal.fire({
					 imageUrl: 'http://localhost:8000/assets/icons/Gif/info1.gif', imageWidth: 60, imageHeight: 60 ,
					title: platform,
					text: `Follow us on ${platform} for updates!`,
					confirmButtonColor: '#10B981',
					timer: 2000,
					showConfirmButton: true
				});
			} else if (!href.startsWith('http')) {
				e.preventDefault();
				Swal.fire({
					 imageUrl: 'http://localhost:8000/assets/icons/Gif/error6.gif', imageWidth: 60, imageHeight: 60 ,
					title: 'Invalid Link',
					text: 'This social media link needs configuration.',
					confirmButtonColor: '#ef4444'
				});
			}
		});
	});
	const legalLinks = document.querySelectorAll('.legal-link');
	legalLinks.forEach(link => {
		link.addEventListener('click', function(e) {
			const href = this.getAttribute('href');
			const text = this.textContent;
			if (href === '#') {
				e.preventDefault();
				Swal.fire({
					 imageUrl: 'http://localhost:8000/assets/icons/Gif/info1.gif', imageWidth: 60, imageHeight: 60 ,
					title: text,
					text: `${text} Policy is being updated. Check back soon!`,
					confirmButtonColor: '#10B981'
				});
			} else if (href.includes('.pdf')) {
				e.preventDefault();
				Swal.fire({
					 imageUrl: 'http://localhost:8000/assets/icons/Gif/download confirmation1.gif', imageWidth: 60, imageHeight: 60 ,
					title: 'Download',
					text: `Download ${text} Policy?`,
					showCancelButton: true,
					confirmButtonColor: '#10B981',
					cancelButtonColor: '#6b7280',
					confirmButtonText: 'Download'
				}).then((result) => {
					if (result.isConfirmed) {
						window.open(href, '_blank');
						Swal.fire({
							 imageUrl: 'http://localhost:8000/assets/icons/Gif/success4.gif', imageWidth: 60, imageHeight: 60 ,
							title: 'Success',
							text: 'Download started!',
							showConfirmButton: false,
							timer: 1500
						});
					}
				});
			}
		});
	});
});


document.addEventListener('DOMContentLoaded', function() {
	const contactForm = document.getElementById('contactForm');
	const submitBtn = document.getElementById('submitBtn');

	
	
	
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

				const response = await fetch('http://localhost:8000/contact-us/send', {
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
						let serverMsg = result.message || 'Server error occurred';
						if (result.error) {
							serverMsg += '<br><br><small style="color:red;">Error: ' + result.error + '</small>';
						}
						throw { type: 'server', message: serverMsg };
					}
				}

				if (result.success) {
					Swal.fire({
						 imageUrl: 'http://localhost:8000/assets/icons/Gif/Send successfully1.gif', imageWidth: 60, imageHeight: 60 ,
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
					title: errorTitle,
					html: errorMessage,
					confirmButtonColor: '#10B981',
					 imageUrl: 'http://localhost:8000/assets/icons/Gif/error3.gif', imageWidth: 60, imageHeight: 60 				});
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
				 imageUrl: 'http://localhost:8000/assets/icons/Gif/alert1.gif', imageWidth: 60, imageHeight: 60 ,
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

	});
