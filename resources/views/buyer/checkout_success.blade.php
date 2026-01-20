@extends('buyer.layouts.buyer_master')

@section('title', 'Order Confirmed')
@section('page-title', 'Order Confirmed')

@section('styles')
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
}

body {
    background: linear-gradient(135deg, #f6f8fa 0%, #eef2f7 100%);
    min-height: 100vh;
}

.success-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 3rem 1rem;
    text-align: center;
}

.success-icon {
    width: 100px;
    height: 100px;
    background: var(--primary-green);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
}

.order-details-card {
    background: var(--card-bg);
    border-radius: 16px;
    border-left: 4px solid var(--primary-green);
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(15,23,36,0.08);
}

.pickup-details-card {
    background: var(--card-bg);
    border-radius: 16px;
    border-left: 4px solid var(--accent-amber);
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(15,23,36,0.08);
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

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-success {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
}

.btn-outline-primary {
    border-color: var(--primary-green);
    color: var(--primary-green);
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
}

.btn-outline-primary:hover {
    background: var(--primary-green);
    color: white;
}

@media (max-width: 768px) {
    .success-container {
        padding: 1rem;
    }

    .success-icon {
        width: 80px;
        height: 80px;
    }

    .action-buttons {
        flex-direction: column;
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
</style>
@endsection

@section('content')
<div class="success-container">
    <div class="success-icon">
        <i class="fas fa-check fa-3x text-white"></i>
    </div>

    <h1 class="mb-3" style="color: var(--text-color);">Order Confirmed!</h1>
    <p class="lead text-muted mb-5">Your payment has been processed successfully.</p>

    @if(isset($order))
    <div class="card order-details-card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-4" style="color: var(--text-color);">
                <i class="fas fa-receipt me-2"></i>Order Details
            </h5>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted"><i class="fas fa-receipt me-2"></i>Order Number</h6>
                    <h4 class="text-primary" style="color: var(--primary-green) !important;">{{ $order->order_number }}</h4>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted"><i class="fas fa-calendar me-2"></i>Order Date</h6>
                    <h5 style="color: var(--text-color);">
                        {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y h:i A') }}
                    </h5>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted"><i class="fas fa-credit-card me-2"></i>Total Amount</h6>
                    <h4 style="color: var(--primary-green);">Rs. {{ number_format($order->total_amount, 2) }}</h4>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted"><i class="fas fa-info-circle me-2"></i>Order Status</h6>
                    <span class="badge bg-success" style="background: var(--primary-green) !important; padding: 0.5rem 1rem; font-size: 1rem;">
                        {{ ucfirst($order->order_status) }}
                    </span>
                </div>
            </div>

            <div class="mt-4">
                <h6 class="text-muted mb-3"><i class="fas fa-box-open me-2"></i>Order Items</h6>
                @if(isset($orderItems) && count($orderItems) > 0)
                    @foreach($orderItems as $item)
                    <div class="order-item">
                        <div class="order-item-image">
                            @if($item->product_image)
                                <img src="{{ asset('storage/' . $item->product_image) }}" alt="{{ $item->product_name_snapshot }}">
                            @else
                                <img src="{{ asset('assets\images\product-placeholder.png') }}" alt="No Image">
                            @endif
                        </div>
                        <div class="order-item-details">
                            <div class="order-item-name">{{ $item->product_name_snapshot }}</div>
                            <small class="text-muted">Quantity: {{ $item->quantity_ordered }}</small>
                        </div>
                        <div class="order-item-price">
                            <span class="price-amount">Rs. {{ number_format($item->unit_price_snapshot, 2) }}</span>
                            <span class="price-total">Rs. {{ number_format($item->item_total, 2) }}</span>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="card pickup-details-card mb-5">
        <div class="card-body">
            <h5 class="card-title mb-3" style="color: var(--text-color);">
                <i class="fas fa-map-marker-alt me-2"></i>Next Steps
            </h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted"><i class="fas fa-truck me-2"></i>Delivery Status</h6>
                    <p style="color: var(--text-color);">Your order is being processed. You will be notified when it's ready for pickup.</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted"><i class="fas fa-headset me-2"></i>Support</h6>
                    <p style="color: var(--text-color);">Need help? Contact our support team for any questions about your order.</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="action-buttons mt-5">
        <a href="{{ route('buyer.orders') }}" class="btn btn-success">
            <i class="fas fa-list me-2"></i>View All Orders
        </a>
        <a href="{{ route('buyer.dashboard') }}" class="btn btn-outline-primary">
            <i class="fas fa-home me-2"></i>Return to Dashboard
        </a>
        @if(isset($order))
        <a href="{{ route('buyer.order.invoice', $order->id) }}" class="btn btn-outline-primary">
            <i class="fas fa-file-invoice me-2"></i>Download Invoice
        </a>
        @endif
    </div>

    <div class="mt-4">
        <p class="text-muted">
            <i class="fas fa-info-circle me-2"></i>
            An order confirmation has been sent to your email address.
        </p>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if this is a redirect from successful payment
    const urlParams = new URLSearchParams(window.location.search);
    const paymentSuccess = urlParams.get('payment_success');

    if (paymentSuccess === 'true') {
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
            icon: 'success',
            title: 'Payment completed successfully!',
            background: '#d1fae5',
            color: '#065f46'
        });
    }
});
</script>
@endsection
