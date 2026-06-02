@extends('buyer.layouts.buyer_master')

@section('title', 'Shopping Cart')
@section('page-title', 'My Cart')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/buyer/cart.css') }}">

<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-shopping-cart me-2"></i> Shopping Cart</h4>
                        <span class="badge">{{ $cartCount ?? 0 }} items</span>
                    </div>
                </div>

                <div class="card-body">
                    @if(!empty($cartItems) && count($cartItems) > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 60px;"></th>
                                        <th scope="col">Product</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Total</th>
                                        <th scope="col" class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $cartTotal = 0; @endphp
                                    @foreach($cartItems as $item)
                                    @php
                                        // Calculate item total
                                        $itemTotal = $item->quantity * $item->selling_price_snapshot;
                                        $cartTotal += $itemTotal;
                                    @endphp
                                    <tr data-cart-id="{{ $item->cart_id }}">
                                        <td data-label="Image">
                                            <img src="{{ $item->product_image }}"
                                                alt="{{ $item->product_name }}"
                                                class="img-fluid rounded"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td data-label="Product">
                                            <div>
                                                <h6 class="mb-1">{{ $item->product_name }}</h6>
                                                <div class="text-muted small">
                                                    @if($item->quantity > $item->available_stock)
                                                        <span class="text-danger">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                                            Only {{ number_format($item->available_stock, 2) }} available
                                                        </span>
                                                    @else
                                                        <span class="text-success">
                                                            <i class="fas fa-check-circle me-1"></i>
                                                            {{ number_format($item->available_stock, 2) }} in stock
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Price">
                                            <strong class="text-success">Rs. {{ number_format($item->selling_price_snapshot, 2) }}</strong>
                                        </td>
                                        <td data-label="Quantity">
                                            <div class="quantity-selector d-flex align-items-center">
                                                <button class="btn btn-sm btn-outline-secondary qty-minus"
                                                        data-cart-id="{{ $item->cart_id }}">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number"
                                                       class="form-control form-control-sm qty-input mx-2"
                                                       value="{{ number_format($item->quantity, 2) }}"
                                                       min="0.01"
                                                       step="0.01"
                                                       max="{{ number_format($item->available_stock, 2) }}"
                                                       data-cart-id="{{ $item->cart_id }}"
                                                       style="width: 80px;">
                                                <button class="btn btn-sm btn-outline-secondary qty-plus"
                                                        data-cart-id="{{ $item->cart_id }}"
                                                        {{ $item->quantity >= $item->available_stock ? 'disabled' : '' }}>
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td data-label="Total">
                                            <strong class="text-primary item-total"
                                                    data-cart-id="{{ $item->cart_id }}"
                                                    data-unit-price="{{ $item->selling_price_snapshot }}">
                                                Rs. {{ number_format($itemTotal, 2) }}
                                            </strong>
                                        </td>
                                        <td data-label="Actions" class="text-end">
                                            <button class="btn btn-sm btn-outline-danger remove-cart-item"
                                                    data-cart-id="{{ $item->cart_id }}"
                                                    data-product-name="{{ $item->product_name }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('buyer.browseProducts') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                                    </a>
                                    <button class="btn btn-outline-warning" id="clearCartBtn">
                                        <i class="fas fa-trash-alt me-2"></i> Clear Cart
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <div class="delivery-method-selection mb-4">
                                            <label class="form-label fw-bold mb-3"><i class="fas fa-truck-loading me-2"></i>Select Delivery Method <span class="text-danger">*</span></label>
                                            <div class="row g-2">
                                                <div class="col-12">
                                                    <input type="radio" class="btn-check" name="delivery_method" id="method_pickup" value="Pickup" autocomplete="off" checked>
                                                    <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 selection-card active" for="method_pickup">
                                                        <i class="fas fa-store fa-2x mb-2"></i>
                                                        <span class="fw-bold">Cash on Pickup</span>
                                                        <small class="text-muted mt-1">Payment Method: Cash</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Subtotal:</span>
                                            <span class="fw-semibold">Rs. {{ number_format($cartTotal, 2) }}</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="h5 mb-0">Total:</span>
                                            <span class="h4 mb-0 text-primary" id="cartGrandTotal">
                                                Rs. {{ number_format($cartTotal, 2) }}
                                            </span>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-lg w-100" id="proceedToPaymentBtn">
                                            <i class="fas fa-check-circle me-2"></i> Place Order
                                        </button>
                                        <div class="text-center mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-shield-alt me-1"></i>
                                                Secure checkout · Buyer protection · Easy returns
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-shopping-cart fa-4x text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-3">Your cart is empty</h5>
                            <p class="text-muted mb-4">Add some amazing products to your cart!</p>
                            <a href="{{ route('buyer.browseProducts') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-bag me-2"></i> Start Shopping
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let isUpdating = false;

    // Function to show alert
    function showAlert(message, type = 'success') {
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

    // Function to update cart totals
    function updateCartTotals() {
        let newTotal = 0;
        document.querySelectorAll('.item-total').forEach(el => {
            const totalText = el.textContent.replace('Rs.', '').replace(/,/g, '').trim();
            const total = parseFloat(totalText);
            if (!isNaN(total)) {
                newTotal += total;
            }
        });

        const grandTotalElement = document.getElementById('cartGrandTotal');
        const subtotalElement = document.querySelector('.card-body .d-flex.justify-content-between.mb-2 .fw-semibold');

        if (grandTotalElement) {
            grandTotalElement.textContent = 'Rs. ' + newTotal.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        if (subtotalElement) {
            subtotalElement.textContent = 'Rs. ' + newTotal.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        return newTotal;
    }

    // Function to update cart count
    function updateCartCount() {
        // Get current cart count from badge
        const badge = document.querySelector('.badge');
        if (!badge) return;

        // Count items in cart
        const itemCount = document.querySelectorAll('tbody tr').length;
        badge.textContent = itemCount + ' items';

        // Update cart badge in navbar if exists
        const navBadges = document.querySelectorAll('.cart-badge');
        navBadges.forEach(badge => {
            badge.textContent = itemCount;
            badge.classList.add('success-pulse');
            setTimeout(() => badge.classList.remove('success-pulse'), 500);
        });
    }

    // Function to check if cart is empty
    function checkEmptyCart() {
        const tbody = document.querySelector('tbody');
        if (!tbody || tbody.children.length === 0) {
            const cardBody = document.querySelector('.card-body');
            if (cardBody) {
                cardBody.innerHTML = `
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-shopping-cart fa-4x text-muted"></i>
                        </div>
                        <h5 class="text-muted mb-3">Your cart is empty</h5>
                        <p class="text-muted mb-4">Add some amazing products to your cart!</p>
                        <a href="/buyer/browse-products" class="btn btn-primary">
                            <i class="fas fa-shopping-bag me-2"></i> Start Shopping
                        </a>
                    </div>
                `;
            }
            return true;
        }
        return false;
    }

    // Update quantity function
    async function updateQuantity(cartId, newQuantity) {
        if (isUpdating) return;

        const quantityInput = document.querySelector(`.qty-input[data-cart-id="${cartId}"]`);
        const plusButton = document.querySelector(`.qty-plus[data-cart-id="${cartId}"]`);
        const minusButton = document.querySelector(`.qty-minus[data-cart-id="${cartId}"]`);
        const itemTotalElement = document.querySelector(`.item-total[data-cart-id="${cartId}"]`);
        const row = document.querySelector(`tr[data-cart-id="${cartId}"]`);

        if (!quantityInput || !itemTotalElement || !row) return;

        const maxQuantity = parseFloat(quantityInput.max);
        const currentQuantity = parseFloat(quantityInput.value);

        // Get the selling price from data attribute or calculate from current total
        const pricePerUnit = parseFloat(itemTotalElement.getAttribute('data-unit-price') ||
            (parseFloat(itemTotalElement.textContent.replace('Rs.', '').replace(/,/g, '').trim()) / currentQuantity));

        if (newQuantity < 0.01) newQuantity = 0.01;
        if (newQuantity > maxQuantity) {
            newQuantity = maxQuantity;
            showAlert(`Maximum available stock is ${maxQuantity}`, 'warning');
        }

        // Update input value
        quantityInput.value = newQuantity.toFixed(2);

        // Update button states
        if (plusButton) {
            plusButton.disabled = newQuantity >= maxQuantity;
        }
        if (minusButton) {
            minusButton.disabled = newQuantity <= 0.01;
        }

        // Calculate new total
        const newTotal = (newQuantity * pricePerUnit);
        itemTotalElement.textContent = `Rs. ${newTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;

        isUpdating = true;

        try {
            const response = await fetch(`/buyer/cart/update/${cartId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    quantity: newQuantity
                })
            });

            const data = await response.json();

            if (data.success) {
                // Update grand total
                updateCartTotals();

                // Update cart count
                if (data.cart_count !== undefined) {
                    updateCartCount();
                }

                // If quantity is 0, remove row
                if (newQuantity <= 0) {
                    setTimeout(() => {
                        row.remove();
                        if (checkEmptyCart()) {
                            showAlert('Cart is now empty', 'info');
                        }
                        updateCartCount();
                    }, 500);
                }

                // Update the item total with the returned value
                if (data.item_total !== undefined) {
                    itemTotalElement.textContent = `Rs. ${parseFloat(data.item_total).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                }

                showAlert('Quantity updated successfully', 'success');
            } else {
                // Revert to original quantity on error
                quantityInput.value = currentQuantity.toFixed(2);
                itemTotalElement.textContent = `Rs. ${(currentQuantity * pricePerUnit).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                showAlert(data.message || 'Failed to update quantity', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            // Revert to original quantity on error
            quantityInput.value = currentQuantity.toFixed(2);
            itemTotalElement.textContent = `Rs. ${(currentQuantity * pricePerUnit).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            showAlert('Failed to update quantity. Please try again.', 'error');
        } finally {
            isUpdating = false;
        }
    }

    // Plus button event
    document.addEventListener('click', function(e) {
        if (e.target.closest('.qty-plus')) {
            const button = e.target.closest('.qty-plus');
            if (button.disabled) return;

            const cartId = button.dataset.cartId;
            const quantityInput = document.querySelector(`.qty-input[data-cart-id="${cartId}"]`);

            if (quantityInput) {
                const currentQuantity = parseFloat(quantityInput.value);
                const maxQuantity = parseFloat(quantityInput.max);
                const newQuantity = Math.min(currentQuantity + 1, maxQuantity);
                updateQuantity(cartId, newQuantity);
            }
        }
    });

    // Minus button event
    document.addEventListener('click', function(e) {
        if (e.target.closest('.qty-minus')) {
            const button = e.target.closest('.qty-minus');
            const cartId = button.dataset.cartId;
            const quantityInput = document.querySelector(`.qty-input[data-cart-id="${cartId}"]`);

            if (quantityInput) {
                const currentQuantity = parseFloat(quantityInput.value);
                const newQuantity = Math.max(currentQuantity - 1, 0.01);
                updateQuantity(cartId, newQuantity);
            }
        }
    });

    // Quantity input change event
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('qty-input')) {
            const input = e.target;
            const cartId = input.dataset.cartId;
            const newQuantity = parseFloat(input.value);

            if (!isNaN(newQuantity)) {
                updateQuantity(cartId, newQuantity);
            }
        }
    });

    // Remove item event
    document.addEventListener('click', async function(e) {
        if (e.target.closest('.remove-cart-item')) {
            const button = e.target.closest('.remove-cart-item');
            const cartId = button.dataset.cartId;
            const productName = button.dataset.productName;
            const row = button.closest('tr');

            const result = await Swal.fire({
                title: 'Remove item?',
                text: `Are you sure you want to remove "${productName}" from your cart?`,
                @if(file_exists(public_path('assets/icons/Gif/Delete Request Confirmation2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Delete Request Confirmation2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'warning' @endif,
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#ef4444'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/buyer/cart/remove/${cartId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        row.remove();
                        showAlert('Item removed from cart', 'success');

                        // Update totals
                        updateCartTotals();

                        // Update cart count
                        updateCartCount();

                        // Check if cart is empty
                        checkEmptyCart();
                    } else {
                        showAlert(data.message || 'Failed to remove item', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showAlert('Failed to remove item. Please try again.', 'error');
                }
            }
        }
    });

    // Clear cart button
    const clearCartBtn = document.getElementById('clearCartBtn');
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', async function() {
            const rows = document.querySelectorAll('tbody tr');
            if (rows.length === 0) {
                showAlert('Cart is already empty', 'info');
                return;
            }

            const result = await Swal.fire({
                title: 'Clear cart?',
                text: 'Are you sure you want to remove all items from your cart?',
                @if(file_exists(public_path('assets/icons/Gif/Delete Request Confirmation2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Delete Request Confirmation2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'warning' @endif,
                showCancelButton: true,
                confirmButtonText: 'Yes, clear cart',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#f59e0b'
            });

            if (result.isConfirmed) {
                const cartIds = [];
                rows.forEach(row => {
                    const cartId = row.dataset.cartId;
                    if (cartId) cartIds.push(cartId);
                });

                try {
                    // Remove all items
                    const deletePromises = cartIds.map(cartId =>
                        fetch(`/buyer/cart/remove/${cartId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                    );

                    await Promise.all(deletePromises);

                    // Clear the table
                    const tbody = document.querySelector('tbody');
                    if (tbody) tbody.innerHTML = '';

                    // Show empty cart state
                    checkEmptyCart();

                    // Update cart count
                    updateCartCount();

                    // Reset total
                    const grandTotalElement = document.getElementById('cartGrandTotal');
                    if (grandTotalElement) {
                        grandTotalElement.textContent = 'Rs. 0.00';
                    }

                    showAlert('Cart cleared successfully', 'success');
                } catch (error) {
                    console.error('Error:', error);
                    showAlert('Failed to clear cart. Please try again.', 'error');
                }
            }
        });
    }

    // Quantity input keyup event for real-time updates
    document.addEventListener('keyup', function(e) {
        if (e.target.classList.contains('qty-input')) {
            const input = e.target;
            const cartId = input.dataset.cartId;
            const newQuantity = parseFloat(input.value);

            if (!isNaN(newQuantity) && newQuantity >= 0.01) {
                // Update immediately on keyup
                updateQuantity(cartId, newQuantity);
            }
        }
    });

    // Prevent negative values on input
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input')) {
            const input = e.target;
            const value = parseFloat(input.value);

            if (value < 0.01) {
                input.value = 0.01;
            }

            const maxQuantity = parseFloat(input.max);
            if (value > maxQuantity) {
                input.value = maxQuantity;
            }
        }
    });

    // Handle Delivery Method Selection
    const methodRadios = document.querySelectorAll('input[name="delivery_method"]');
    const proceedToPaymentBtn = document.getElementById('proceedToPaymentBtn');

    methodRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (proceedToPaymentBtn) {
                proceedToPaymentBtn.disabled = false;

                // Add visual feedback to cards
                document.querySelectorAll('.selection-card').forEach(card => {
                    card.classList.remove('active');
                });
                const selectedLabel = document.querySelector(`label[for="${this.id}"]`);
                if (selectedLabel) selectedLabel.classList.add('active');
            }
        });
    });

    // Proceed to Payment button
    if (proceedToPaymentBtn) {
        proceedToPaymentBtn.addEventListener('click', async function() {
            const selectedMethod = document.querySelector('input[name="delivery_method"]:checked');

            if (!selectedMethod) {
                showAlert('Please select a delivery method first', 'warning');
                return;
            }

            const methodValue = 'Pickup';
            let confirmTitle = 'Order Confirmation';
            let confirmHtml = `
                <div class="text-start">
                    <center>
                        <p><strong>Order Pick-up process:</strong> <br>You will directly contact the Seller for pickup</p>
                        <p class="text-muted small">Payment Method: Cash on Pickup</p>
                    </center>
                </div>
            `;
            let paymentMethod = 'cod';


            const result = await Swal.fire({
                title: confirmTitle,
                html: confirmHtml,
                @if(file_exists(public_path('assets/icons/Gif/Order Confirmation1.gif')))
                    imageUrl: '{{ asset('assets/icons/Gif/Order Confirmation1.gif') }}',
                    imageWidth: 60,
                    imageHeight: 60
                @else
                    icon: 'question'
                @endif,
                showCancelButton: true,
                confirmButtonText: 'Confirm Order',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6B7280'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch('/buyer/checkout/place-order', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            order_type: methodValue,
                            payment_method: paymentMethod
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Show success message
                        Swal.fire({
                            title: 'Order Placed Successfully!',
                            html: `
                                <div class="text-center">
                                    @if(file_exists(public_path('assets/icons/Gif/Order Success2.gif')))
                                        <img src="{{ asset('assets/icons/Gif/Order Success2.gif') }}" width="150" height="150" class="mb-3" alt="Success">
                                    @else
                                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                    @endif
                                    <p class="h5">Contact the seller for the pickup</p>
                                    <p class="text-muted">You can track your order in your history.</p>
                                </div>
                            `,
                            confirmButtonText: 'Go to Order History',
                            confirmButtonColor: '#10B981'
                        }).then(() => {
                            if (data.redirect_url) {
                                window.location.href = data.redirect_url;
                            } else {
                                window.location.href = '/buyer/history';
                            }
                        });
                    } else {
                        showAlert(data.message || 'Failed to place order', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showAlert('Failed to place order. Please try again.', 'error');
                }
            }
        });
    }

    // Initialize cart count
    updateCartCount();
});
</script>

<style>
.selection-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
    background: #fff;
    cursor: pointer;
}

.selection-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.btn-check:checked + .selection-card {
    background-color: #f0fdf4;
    border-color: #10b981;
    color: #065f46;
}

.selection-card i {
    color: #6b7280;
    transition: color 0.3s ease;
}

.btn-check:checked + .selection-card i {
    color: #10b981;
}

.quantity-selector button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
@endsection
