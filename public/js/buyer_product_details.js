// E:\Laravel\Home_Gardens_Hub\Home-Gardens-Hub\public\js\buyer_product_details.js

document.addEventListener('DOMContentLoaded', function() {
    console.log('Buyer product details JS loaded');

    // Initialize variables
    const quantityInput = document.getElementById('productQuantity');
    const minusBtn = document.getElementById('minusBtn');
    const plusBtn = document.getElementById('plusBtn');
    const addToCartBtn = document.getElementById('addToCartBtn');
    const wishlistBtn = document.getElementById('wishlistBtn');

    // Check if elements exist
    if (!quantityInput || !minusBtn || !plusBtn || !addToCartBtn || !wishlistBtn) {
        console.error('Required elements not found');
        return;
    }

    // Check if meta tag exists to avoid null error
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    // Get max quantity from data attribute
    const maxQuantity = parseFloat(quantityInput.dataset.maxQuantity) || parseFloat(quantityInput.max) || 0;
    console.log('Max quantity:', maxQuantity);

    // Quantity button functionality
    minusBtn.addEventListener('click', function() {
        let currentValue = parseFloat(quantityInput.value) || 1;
        if (currentValue > 0.01) {
            quantityInput.value = (currentValue - 1).toFixed(2);
        }
        animateButtonClick(this);
    });

    plusBtn.addEventListener('click', function() {
        let currentValue = parseFloat(quantityInput.value) || 1;
        if (currentValue < maxQuantity) {
            quantityInput.value = (currentValue + 1).toFixed(2);
        } else {
            showAlert('Maximum quantity is ' + maxQuantity.toFixed(2), 'warning');
        }
        animateButtonClick(this);
    });

    // Quantity input validation
    quantityInput.addEventListener('change', function() {
        let value = parseFloat(this.value);
        if (isNaN(value) || value < 0.01) {
            this.value = 0.01;
        } else if (value > maxQuantity) {
            this.value = maxQuantity.toFixed(2);
            showAlert('Maximum available quantity is ' + maxQuantity.toFixed(2), 'info');
        }
    });

    quantityInput.addEventListener('input', function() {
        let value = parseFloat(this.value);
        if (value > maxQuantity) {
            this.value = maxQuantity.toFixed(2);
        }
    });

    // Add to cart functionality
    addToCartBtn.addEventListener('click', function() {
        const productId = this.dataset.productId;
        const quantity = parseFloat(quantityInput.value) || 0.01;
        const loginUrl = this.dataset.loginRoute;
        const routeTemplate = this.dataset.route;

        if (!routeTemplate) {
            showAlert('Cart functionality not properly configured', 'error');
            return;
        }

        // Replace :productId placeholder with actual product ID
        const routeUrl = routeTemplate.replace(':productId', productId);

        console.log('Adding to cart:', { productId, quantity, routeUrl });

        if (quantity < 0.01 || quantity > maxQuantity) {
            showAlert('Please enter a valid quantity between 0.01 and ' + maxQuantity.toFixed(2), 'warning');
            return;
        }

        const originalHTML = this.innerHTML;

        // Change button state
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        this.disabled = true;
        this.classList.add('loading');

        fetch(routeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                quantity: quantity
            })
        })
        .then(response => {
            if (response.status === 401) {
                window.location.href = loginUrl;
                throw new Error('Please login to add items to cart');
            }
            if (response.status === 422) {
                return response.json().then(data => {
                    throw new Error(data.errors?.quantity?.[0] || 'Validation failed');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                updateCartCount(data.cart_count);
                animateCartIcon();

                setTimeout(() => {
                    this.innerHTML = originalHTML;
                    this.disabled = false;
                    this.classList.remove('loading');
                }, 1000);
            } else {
                showAlert(data.message, 'error');
                this.innerHTML = originalHTML;
                this.disabled = false;
                this.classList.remove('loading');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (error.message !== 'Please login to add items to cart') {
                showAlert(error.message || 'Failed to add to cart. Please try again.', 'error');
            }
            this.innerHTML = originalHTML;
            this.disabled = false;
            this.classList.remove('loading');
        });
    });

    // Wishlist functionality
    wishlistBtn.addEventListener('click', function() {
        const productId = this.dataset.productId;
        const isInWishlist = this.dataset.inWishlist === 'true';
        const heartIcon = this.querySelector('i');
        const spanText = this.querySelector('span');
        const addRoute = this.dataset.addRoute;
        const removeRoute = this.dataset.removeRoute;

        if (!addRoute || !removeRoute) {
            showAlert('Wishlist functionality not properly configured', 'error');
            return;
        }

        const loginUrl = addToCartBtn.dataset.loginRoute;

        this.style.transform = 'scale(0.95)';
        this.disabled = true;

        // Use appropriate endpoint
        const endpoint = isInWishlist ? removeRoute : addRoute;
        const method = 'POST';

        console.log('Wishlist action:', { productId, isInWishlist, endpoint });

        fetch(endpoint, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: parseInt(productId)
            })
        })
        .then(response => {
            if (response.status === 401) {
                window.location.href = loginUrl;
                throw new Error('Unauthorized');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const newState = !isInWishlist;

                // Update button appearance
                if (newState) {
                    heartIcon.className = 'fas fa-heart';
                    spanText.textContent = 'In Wishlist';
                    this.classList.add('in-wishlist');
                    this.dataset.inWishlist = 'true';
                    animateWishlistAdd();
                } else {
                    heartIcon.className = 'far fa-heart';
                    spanText.textContent = 'Add to Wishlist';
                    this.classList.remove('in-wishlist');
                    this.dataset.inWishlist = 'false';
                }

                showAlert(data.message, 'success');
            } else {
                showAlert(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (error.message !== 'Unauthorized') {
                showAlert('Failed to update wishlist. Please try again.', 'error');
            }
        })
        .finally(() => {
            this.style.transform = '';
            this.disabled = false;
        });
    });

    // Helper functions
    function animateButtonClick(button) {
        button.style.transform = 'scale(0.95)';
        setTimeout(() => {
            button.style.transform = '';
        }, 150);
    }

    function showAlert(message, type) {
        // Ensure Swal is defined before using
        if (typeof Swal === 'undefined') {
            alert(message);
            return;
        }

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: type === 'success' ? '#d1fae5' :
                        type === 'error' ? '#fee2e2' :
                        type === 'warning' ? '#fef3c7' :
                        type === 'info' ? '#dbeafe' : '#f1f5f9',
            color: type === 'success' ? '#065f46' :
                    type === 'error' ? '#7f1d1d' :
                    type === 'warning' ? '#92400e' :
                    type === 'info' ? '#1e40af' : '#374151'
        });
    }

    function updateCartCount(count) {
        const cartBadges = document.querySelectorAll('.cart-badge');
        cartBadges.forEach(badge => {
            badge.textContent = count;
            badge.classList.add('pulse');
            setTimeout(() => {
                badge.classList.remove('pulse');
            }, 500);
        });

        if (typeof updateCartBadge === 'function') {
            updateCartBadge(count);
        }
    }

    function animateCartIcon() {
        const cartIcons = document.querySelectorAll('.fa-shopping-cart');
        cartIcons.forEach(icon => {
            icon.style.transform = 'scale(1.3)';
            icon.style.color = '#10B981';
            setTimeout(() => {
                icon.style.transform = '';
                icon.style.color = '';
            }, 300);
        });
    }

    function animateWishlistAdd() {
        const heartIcon = wishlistBtn.querySelector('i');
        if (heartIcon) {
            heartIcon.style.transform = 'scale(1.5)';
            heartIcon.style.color = '#ef4444';
            setTimeout(() => {
                heartIcon.style.transform = '';
                heartIcon.style.color = '';
            }, 300);
        }
    }

    // Tab functionality
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
