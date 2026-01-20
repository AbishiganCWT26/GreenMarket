@extends('buyer.layouts.buyer_master')

@section('title', 'Checkout')
@section('page-title', 'Checkout')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
:root {
    --primary-green: #10B981;
    --dark-green: #059669;
    --body-bg: #f6f8fa;
    --card-bg: #ffffff;
    --text-color: #0f1724;
    --muted: #6b7280;
    --accent-amber: #f59e0b;
    --blue: #3b82f6;
    --shadow-sm: 0 1px 3px rgba(15,23,36,0.04);
    --shadow-md: 0 7px 15px rgba(15,23,36,0.08);
    --shadow-lg: 0 15px 30px rgba(15,23,36,0.12);
}

body {
    background: linear-gradient(135deg, #f6f8fa 0%, #eef2f7 100%);
    color: var(--text-color);
    min-height: 100vh;
}

.checkout-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.checkout-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 2rem;
}

.checkout-header {
    margin-bottom: 2rem;
    text-align: center;
}

.checkout-header h1 {
    color: var(--text-color);
    font-weight: 800;
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    background: linear-gradient(45deg, var(--primary-green), var(--dark-green));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.checkout-header p {
    font-size: 1.1rem;
    color: var(--muted);
}

.checkout-steps {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.step {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    background: white;
    box-shadow: var(--shadow-sm);
    position: relative;
    overflow: hidden;
}

.step::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, var(--primary-green), var(--dark-green));
    opacity: 0;
    z-index: 0;
}

.step.active {
    color: white;
}

.step.active::before {
    opacity: 1;
}

.step-content {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.step-number {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.9rem;
}

.step.active .step-number {
    background: rgba(255,255,255,0.3);
}

.step.inactive {
    opacity: 0.6;
}

.card {
    background: var(--card-bg);
    border-radius: 16px;
    border: none;
    box-shadow: var(--shadow-sm);
    margin-bottom: 1.5rem;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-bottom: 1px solid #e2e8f0;
    padding: 1.25rem 1.5rem;
    font-weight: 700;
    font-size: 1.125rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.card-header i {
    color: var(--primary-green);
    font-size: 1.2rem;
}

.card-body {
    padding: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: var(--text-color);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-control {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.875rem 1rem;
    font-size: 0.95rem;
    background: #f8fafc;
}

.form-control:focus {
    border-color: var(--primary-green);
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
    background: white;
}

.input-group-text {
    background: var(--primary-green);
    border: 2px solid var(--primary-green);
    color: white;
}

.order-summary-item {
    display: flex;
    justify-content: space-between;
    padding: 0.875rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.order-summary-total {
    display: flex;
    justify-content: space-between;
    padding: 1.25rem 0;
    border-top: 2px solid #e2e8f0;
    font-size: 1.375rem;
    font-weight: 800;
    color: var(--text-color);
}

.order-summary-total span:last-child {
    color: var(--primary-green);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
    border: none;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1.1rem;
    width: 100%;
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
    cursor: pointer;
    transition: opacity 0.3s ease;
}

.btn-primary:hover:not(:disabled) {
    opacity: 0.9;
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-spinner {
    width: 60px;
    height: 60px;
    border: 4px solid rgba(255,255,255,0.3);
    border-top: 4px solid var(--primary-green);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.order-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
    gap: 1rem;
}

.order-item:last-child {
    border-bottom: none;
}

.order-item-image {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
}

.order-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.order-item-details {
    flex: 1;
    min-width: 0;
}

.order-item-name {
    font-weight: 600;
    color: var(--text-color);
    margin-bottom: 0.25rem;
    font-size: 1rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.order-item-price {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
    flex-shrink: 0;
}

.price-amount {
    font-weight: 700;
    color: var(--primary-green);
    font-size: 1rem;
}

.price-quantity {
    font-size: 0.875rem;
    color: var(--muted);
    background: #f1f5f9;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-weight: 600;
}

.price-total {
    font-weight: 800;
    color: var(--text-color);
    font-size: 1.125rem;
    background: #fef3c7;
    padding: 0.5rem 1rem;
    border-radius: 10px;
}

.secure-payment-badge {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
    color: white;
    border-radius: 12px;
    padding: 1.25rem;
    margin-top: 1rem;
}

.secure-payment-badge h6 {
    color: white;
    font-weight: 700;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.1rem;
}

.secure-payment-badge p {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    opacity: 0.9;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.secure-payment-badge p i {
    color: #86efac;
}

.empty-cart-message {
    text-align: center;
    padding: 3rem;
    color: var(--muted);
}

.empty-cart-message i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.bank-payment-badge {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.form-control.is-invalid {
    border-color: #ef4444;
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
}

.invalid-feedback {
    display: block;
    color: #ef4444;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

@media (max-width: 992px) {
    .checkout-grid {
        grid-template-columns: 1fr;
    }

    .checkout-container {
        padding: 1rem;
    }

    .checkout-steps {
        flex-direction: column;
        align-items: center;
    }

    .step {
        width: 100%;
        max-width: 300px;
    }
}

@media (max-width: 768px) {
    .checkout-header h1 {
        font-size: 2rem;
    }

    .card-body {
        padding: 1.25rem;
    }

    .order-item {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
        gap: 0.75rem;
    }

    .order-item-image {
        width: 80px;
        height: 80px;
        margin: 0 auto;
    }

    .order-item-price {
        align-items: center;
        flex-direction: row;
        justify-content: center;
        gap: 1rem;
    }
}

@media (max-width: 480px) {
    .checkout-container {
        padding: 0.75rem;
    }

    .checkout-header h1 {
        font-size: 1.75rem;
    }

    .form-control {
        padding: 0.75rem;
    }

    .btn-primary {
        padding: 0.875rem 1.5rem;
        font-size: 1rem;
    }
}

@media (min-width: 1200px) {
    .checkout-container {
        max-width: 1400px;
    }
}
</style>
@endsection

@section('content')
<div class="checkout-container">
    <div class="checkout-header">
        <h1><i class="fas fa-lock me-2"></i>Complete Your Purchase</h1>
        <p class="text-muted">Secure payment gateway • Instant confirmation • Buyer protection</p>
    </div>

    <div class="checkout-steps">
        <div class="step active">
            <div class="step-content">
                <div class="step-number">1</div>
                <span>Cart Review</span>
            </div>
        </div>
        <div class="step active">
            <div class="step-content">
                <div class="step-number">2</div>
                <span>Payment Details</span>
            </div>
        </div>
        <div class="step inactive">
            <div class="step-content">
                <div class="step-number">3</div>
                <span>Confirmation</span>
            </div>
        </div>
    </div>

    <div class="checkout-grid">
        <div class="left-column">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-credit-card me-2"></i>Bank Card Payment
                    <span class="bank-payment-badge ms-auto">
                        <i class="fas fa-university"></i> Secure Banking
                    </span>
                </div>
                <div class="card-body">
                    <form id="paymentForm">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-credit-card"></i> Card Number
                                </label>
                                <div class="input-group">
                                    <input type="text"
                                           class="form-control"
                                           id="cardNumber"
                                           placeholder="1234 5678 9012 3456"
                                           maxlength="19"
                                           required>
                                    <span class="input-group-text">
                                        <i class="fas fa-credit-card"></i>
                                    </span>
                                </div>
                                <div class="invalid-feedback" id="cardNumberError"></div>
                                <div class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i> 16-digit card number
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user"></i> Card Holder Name
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="cardHolder"
                                       placeholder="John Doe"
                                       required>
                                <div class="invalid-feedback" id="cardHolderError"></div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar"></i> Expiry Month
                                </label>
                                <select class="form-control" id="expiryMonth" required>
                                    <option value="">MM</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                                            {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                        </option>
                                    @endfor
                                </select>
                                <div class="invalid-feedback" id="expiryMonthError"></div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Expiry Year
                                </label>
                                <select class="form-control" id="expiryYear" required>
                                    <option value="">YYYY</option>
                                    @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                <div class="invalid-feedback" id="expiryYearError"></div>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="form-label">
                                    <i class="fas fa-lock"></i> CVV
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control"
                                           id="cvv"
                                           placeholder="123"
                                           maxlength="4"
                                           required>
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                </div>
                                <div class="invalid-feedback" id="cvvError"></div>
                            </div>

                            <div class="col-md-8 mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="saveCard">
                                    <label class="form-check-label" for="saveCard">
                                        <i class="fas fa-save me-2"></i>Save card for future purchases
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-box-open me-2"></i>Order Items
                    <span class="badge bg-primary rounded-pill ms-2">{{ count($cartItems) }} items</span>
                </div>
                <div class="card-body">
                    @if(!empty($cartItems) && count($cartItems) > 0)
                        @foreach($cartItems as $item)
                        <div class="order-item">
                            <div class="order-item-image">
                                <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}">
                            </div>
                            <div class="order-item-details">
                                <div class="order-item-name">{{ $item->product_name }}</div>
                            </div>
                            <div class="order-item-price">
                                <span class="price-amount">Rs. {{ number_format($item->selling_price_snapshot, 2) }}</span>
                                <span class="price-quantity">× {{ $item->quantity }}</span>
                                <span class="price-total">Rs. {{ number_format($item->item_total, 2) }}</span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="empty-cart-message">
                            <i class="fas fa-shopping-cart"></i>
                            <h5>No items in cart</h5>
                            <p>Add products to your cart to proceed with checkout</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="right-column">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header">
                    <i class="fas fa-receipt me-2"></i>Order Summary
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        @if(!empty($cartItems) && count($cartItems) > 0)
                            <div class="order-summary-item">
                                <span>Subtotal</span>
                                <span class="fw-semibold">Rs. {{ number_format($orderTotal, 2) }}</span>
                            </div>
                            <div class="order-summary-total">
                                <span>Total Amount</span>
                                <span class="text-primary">Rs. {{ number_format($orderTotal, 2) }}</span>
                            </div>
                        @else
                            <div class="order-summary-total">
                                <span>Total Amount</span>
                                <span class="text-primary">Rs. 0.00</span>
                            </div>
                        @endif
                    </div>

                    <div class="secure-payment-badge">
                        <h6><i class="fas fa-shield-alt"></i>Secure Payment Guaranteed</h6>
                        <p><i class="fas fa-check-circle"></i> SSL Encrypted Connection</p>
                        <p><i class="fas fa-lock"></i> PCI DSS Compliant</p>
                        <p><i class="fas fa-bolt"></i> Instant Order Processing</p>
                    </div>

                    <div class="mt-4">
                        @if(!empty($cartItems) && count($cartItems) > 0)
                            <button type="button" class="btn btn-primary btn-lg" id="payNowBtn">
                                <i class="fas fa-lock me-2"></i>Pay Rs. {{ number_format($orderTotal, 2) }}
                            </button>
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Your card will be charged immediately
                                </small>
                            </div>
                        @else
                            <button type="button" class="btn btn-secondary btn-lg" disabled>
                                <i class="fas fa-shopping-cart me-2"></i>Add Items to Cart
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="loading-overlay" id="loadingOverlay" style="display: none;">
    <div class="loading-spinner"></div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const payNowBtn = document.getElementById('payNowBtn');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const paymentForm = document.getElementById('paymentForm');
    const cardNumberInput = document.getElementById('cardNumber');
    const cvvInput = document.getElementById('cvv');

    function formatCardNumber(value) {
        return value.replace(/\s/g, '').replace(/(\d{4})/g, '$1 ').trim();
    }

    function validateCardNumber(number) {
        const cleaned = number.replace(/\s/g, '');
        return /^\d{16}$/.test(cleaned);
    }

    function validateCVV(cvv) {
        return /^\d{3,4}$/.test(cvv);
    }

    function validateExpiry(month, year) {
        const currentYear = new Date().getFullYear();
        const currentMonth = new Date().getMonth() + 1;

        if (!month || !year) return false;
        if (parseInt(year) < currentYear) return false;
        if (parseInt(year) === currentYear && parseInt(month) < currentMonth) return false;
        return true;
    }

    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorElement = document.getElementById(fieldId + 'Error');

        if (field) {
            field.classList.add('is-invalid');
        }

        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }

    function clearFieldErrors() {
        document.querySelectorAll('.form-control').forEach(field => {
            field.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(error => {
            error.textContent = '';
            error.style.display = 'none';
        });
    }

    function showAlert(title, message, type = 'success') {
        Swal.fire({
            title: title,
            text: message,
            icon: type,
            confirmButtonText: 'OK',
            confirmButtonColor: type === 'success' ? '#10B981' :
                               type === 'error' ? '#ef4444' :
                               type === 'warning' ? '#f59e0b' : '#3b82f6',
            customClass: {
                popup: 'animated fadeIn'
            }
        });
    }

    function showToast(message, type = 'success') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        Toast.fire({
            icon: type,
            title: message,
            background: type === 'success' ? '#d1fae5' :
                        type === 'error' ? '#fee2e2' :
                        type === 'warning' ? '#fef3c7' : '#f1f5f9',
            color: type === 'success' ? '#065f46' :
                    type === 'error' ? '#7f1d1d' :
                    type === 'warning' ? '#92400e' : '#374151'
        });
    }

    cardNumberInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.substring(0, 16);
        e.target.value = formatCardNumber(value);
    });

    cvvInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
    });

    payNowBtn.addEventListener('click', async function() {
        clearFieldErrors();

        const cardNumber = cardNumberInput.value.trim();
        const cardHolder = document.getElementById('cardHolder').value.trim();
        const expiryMonth = document.getElementById('expiryMonth').value;
        const expiryYear = document.getElementById('expiryYear').value;
        const cvv = cvvInput.value.trim();
        const saveCard = document.getElementById('saveCard').checked;

        let hasError = false;

        if (!cardNumber) {
            showFieldError('cardNumber', 'Card number is required');
            hasError = true;
        } else if (!validateCardNumber(cardNumber)) {
            showFieldError('cardNumber', 'Please enter a valid 16-digit card number');
            hasError = true;
        }

        if (!cardHolder) {
            showFieldError('cardHolder', 'Card holder name is required');
            hasError = true;
        }

        if (!expiryMonth) {
            showFieldError('expiryMonth', 'Expiry month is required');
            hasError = true;
        }

        if (!expiryYear) {
            showFieldError('expiryYear', 'Expiry year is required');
            hasError = true;
        } else if (expiryMonth && expiryYear && !validateExpiry(expiryMonth, expiryYear)) {
            showFieldError('expiryMonth', 'Card has expired');
            showFieldError('expiryYear', 'Card has expired');
            hasError = true;
        }

        if (!cvv) {
            showFieldError('cvv', 'CVV is required');
            hasError = true;
        } else if (!validateCVV(cvv)) {
            showFieldError('cvv', 'Please enter a valid 3 or 4 digit CVV');
            hasError = true;
        }

        if (hasError) {
            showToast('Please fix the errors in the form', 'error');
            return;
        }

        const confirmResult = await Swal.fire({
            title: 'Confirm Payment',
            html: `
                <div class="text-center">
                    <div class="bank-payment-badge mb-3" style="display: inline-flex;">
                        <i class="fas fa-university"></i> Bank Card Payment
                    </div>
                    <h4 class="mb-3">Rs. {{ number_format($orderTotal, 2) }}</h4>
                    <p class="text-muted">Your card ending with ${cardNumber.slice(-4)} will be charged</p>
                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Payment will be processed securely
                    </small>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Confirm Payment',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
            customClass: {
                popup: 'animated fadeIn'
            }
        });

        if (!confirmResult.isConfirmed) return;

        loadingOverlay.style.display = 'flex';
        payNowBtn.disabled = true;

        try {
            const response = await fetch('{{ route("buyer.processPayment") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    payment_method: 'credit_card',
                    card_number: cardNumber,
                    card_holder: cardHolder,
                    expiry_month: expiryMonth,
                    expiry_year: expiryYear,
                    cvv: cvv,
                    save_card: saveCard
                })
            });

            const contentType = response.headers.get("content-type");
            let data;

            if (contentType && contentType.includes("application/json")) {
                data = await response.json();
            } else {
                throw new Error('Server returned non-JSON response');
            }

            if (data.success) {
                await Swal.fire({
                    title: 'Payment Successful!',
                    html: `
                        <div class="text-center">
                            <div style="width: 80px; height: 80px; background: #10B981; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                                <i class="fas fa-check fa-2x text-white"></i>
                            </div>
                            <h4 class="mb-2">Order #${data.order_number}</h4>
                            <p class="text-muted mb-4">Your payment has been processed successfully</p>
                            <div class="d-grid gap-2">
                                <a href="/buyer/checkout/success/${data.order_id}"
                                   class="btn btn-success">
                                    <i class="fas fa-receipt me-2"></i>View Invoice & Details
                                </a>
                            </div>
                        </div>
                    `,
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 5000,
                    willClose: () => {
                        window.location.href = `/buyer/checkout/success/${data.order_id}`;
                    }
                });
            } else {
                throw new Error(data.message || 'Payment processing failed. Please try again.');
            }
        } catch (error) {
            console.error('Payment error:', error);

            let errorMessage = 'Payment failed. Please try again.';

            if (error.message.includes('Server returned non-JSON response')) {
                errorMessage = 'Server error. Please try again later.';
            } else if (error.message.includes('NetworkError') || error.message.includes('Failed to fetch')) {
                errorMessage = 'Network error. Please check your internet connection and try again.';
            } else {
                errorMessage = error.message;
            }

            await Swal.fire({
                title: 'Payment Failed',
                html: `
                    <div class="text-center">
                        <div style="width: 80px; height: 80px; background: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                            <i class="fas fa-times fa-2x text-white"></i>
                        </div>
                        <p class="text-muted">${errorMessage}</p>
                        <div class="d-grid gap-2 mt-4">
                            <button onclick="window.location.reload()" class="btn btn-warning">
                                <i class="fas fa-redo me-2"></i>Try Again
                            </button>
                            <a href="{{ route('buyer.cart') }}" class="btn btn-outline-primary">
                                <i class="fas fa-shopping-cart me-2"></i>Back to Cart
                            </a>
                        </div>
                    </div>
                `,
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#ef4444'
            });
        } finally {
            loadingOverlay.style.display = 'none';
            payNowBtn.disabled = false;
        }
    });

});
</script>
@endsection
