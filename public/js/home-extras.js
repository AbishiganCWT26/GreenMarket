document.addEventListener('DOMContentLoaded', function() {
	const cart = JSON.parse(localStorage.getItem('cart')) || [];
	const cartCountElement = document.getElementById('cartCount');
	const notificationToast = document.getElementById('notificationToast');
	const toastMessage = document.getElementById('toastMessage');

	function updateCartCount() {
		const cartCount = cart.reduce((sum, item) => sum + item.quantity, 0);
		if (cartCountElement) cartCountElement.textContent = cartCount;
		localStorage.setItem('cart', JSON.stringify(cart));
	}

	function showToast(message, type = 'success') {
		if (!notificationToast || !toastMessage) return;

		toastMessage.textContent = message;
		notificationToast.className = `toast-notification ${type}`;
		notificationToast.classList.add('show');

		setTimeout(() => {
			notificationToast.classList.remove('show');
		}, 3000);
	}

	function showSwalAlert(title, text, icon, confirmText) {
		Swal.fire({
			title: title,
			text: text,
			icon: icon,
			confirmButtonText: confirmText,
			buttonsStyling: false,
			customClass: {
				confirmButton: 'btn btn-primary'
			}
		});
	}

	function animateCounters() {
		const counters = document.querySelectorAll('.stat-number');

		counters.forEach(counter => {
			const target = parseInt(counter.getAttribute('data-count'));
			const increment = target / 100;
			let current = 0;

			const updateCounter = () => {
				if (current < target) {
					current += increment;
					counter.textContent = Math.ceil(current);
					setTimeout(updateCounter, 20);
				} else {
					counter.textContent = target.toLocaleString();
				}
			};

			const observer = new IntersectionObserver((entries) => {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						updateCounter();
						observer.unobserve(entry.target);
					}
				});
			}, { threshold: 0.5 });

			observer.observe(counter);
		});
	}

	function initCategoryHover() {
		const categoryCards = document.querySelectorAll('.category-card');

		categoryCards.forEach(card => {
			card.addEventListener('mouseenter', function() {
				this.style.transform = 'translateY(-8px) scale(1.03)';
			});

			card.addEventListener('mouseleave', function() {
				this.style.transform = 'translateY(0) scale(1)';
			});
		});
	}

	function initDistrictAnimation() {
		const districtItems = document.querySelectorAll('.district-item');

		districtItems.forEach((item, index) => {
			item.style.opacity = '0';
			item.style.transform = 'translateX(-15px)';

			setTimeout(() => {
				item.style.transition = 'all 0.4s ease';
				item.style.opacity = '1';
				item.style.transform = 'translateX(0)';
			}, index * 50);
		});
	}

	function initStandardsAnimation() {
		const standardCards = document.querySelectorAll('.standard-card');

		standardCards.forEach((card, index) => {
			card.style.opacity = '0';
			card.style.transform = 'translateY(15px)';

			setTimeout(() => {
				card.style.transition = 'all 0.5s ease';
				card.style.opacity = '1';
				card.style.transform = 'translateY(0)';
			}, index * 100);
		});
	}

	function initIntersectionObserver() {
		const sections = document.querySelectorAll('section:not(.hero-section)');

		const observer = new IntersectionObserver((entries) => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					entry.target.classList.add('in-view');

					if (entry.target.classList.contains('statistics-section')) {
						animateCounters();
					}

					if (entry.target.classList.contains('coverage-section')) {
						initDistrictAnimation();
					}

					if (entry.target.classList.contains('standards-section')) {
						initStandardsAnimation();
					}

					observer.unobserve(entry.target);
				}
			});
		}, { threshold: 0.1 });

		sections.forEach(section => {
			observer.observe(section);
		});
	}

	window.addToCart = function(productData) {
		const { id, name, price, unit, image, category } = productData;
		const existingItemIndex = cart.findIndex(item => item.id === id);

		if (existingItemIndex > -1) {
			cart[existingItemIndex].quantity += 1;
		} else {
			cart.push({
				id: id,
				name: name,
				price: price,
				unit: unit,
				image: image,
				category: category,
				quantity: 1
			});
		}

		updateCartCount();
		showToast(`${name} added to cart!`, 'success');
		return true;
	}

	window.showQuickView = function(productId) {
		Swal.fire({
			title: 'Quick View',
			text: `Product ID: ${productId} - Feature coming soon!`,
			icon: 'info',
			confirmButtonText: 'OK'
		});
	}

	function init() {
		updateCartCount();
		initCategoryHover();
		initIntersectionObserver();

		const toastCloseBtn = notificationToast?.querySelector('.toast-close');
		if (toastCloseBtn) {
			toastCloseBtn.addEventListener('click', function() {
				notificationToast.classList.remove('show');
			});
		}

		document.querySelectorAll('a[href^="#"]').forEach(anchor => {
			anchor.addEventListener('click', function(e) {
				e.preventDefault();

				const targetId = this.getAttribute('href');
				if (targetId === '#') return;

				const targetElement = document.querySelector(targetId);
				if (targetElement) {
					window.scrollTo({
						top: targetElement.offsetTop - 80,
						behavior: 'smooth'
					});
				}
			});
		});

		window.addEventListener('scroll', function() {
			const cartFloatingBtn = document.querySelector('.cart-fab');
			if (cartFloatingBtn) {
				if (window.scrollY > 100) {
					cartFloatingBtn.classList.add('scrolled');
				} else {
					cartFloatingBtn.classList.remove('scrolled');
				}
			}
		});
	}

	window.CartManager = {
		addToCart: window.addToCart,
		showQuickView: window.showQuickView,
		getCart: () => cart,
		getCartCount: () => cart.reduce((sum, item) => sum + item.quantity, 0)
	};

	init();
});
