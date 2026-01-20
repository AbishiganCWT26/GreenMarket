@extends('farmer.layouts.farmer_master')

@section('title', 'Order History')
@section('page-title', 'Order History')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/farmer/history.css') }}">
@endsection

@section('content')
<div class="container-fluid py-3">
    <div class="page-header">
        <div class="header-title">
            <h1>
                <i class="fa-solid fa-history"></i>
                Order History
            </h1>
            <p>View completed and cancelled orders</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('farmer.orders.active') }}" class="refresh-btn">
                <i class="fa-solid fa-clock"></i>
                Active Orders
            </a>
        </div>
    </div>

    <div class="filters-bar">
        <div class="search-wrapper">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="searchInput" placeholder="Search orders...">
        </div>
        <div class="filter-tabs">
            <button class="tab-btn active" data-filter="all">
                <i class="fa-solid fa-layer-group"></i>
                All
            </button>
            <button class="tab-btn" data-filter="completed">
                <i class="fa-solid fa-check"></i>
                Completed
            </button>
            <button class="tab-btn" data-filter="paid">
                <i class="fa-solid fa-money-bill"></i>
                Paid
            </button>
            <button class="tab-btn" data-filter="ready">
                <i class="fa-solid fa-truck"></i>
                Ready
            </button>
        </div>
    </div>

    <div class="orders-container">
        @if($orders->count() > 0)
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product</th>
                    <th>Customer</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                @php
                    $statusClass = '';
                    if($order->order_status == 'completed' || $order->order_status == 'delivered') {
                        $statusClass = 'status-completed';
                    } elseif($order->order_status == 'cancelled') {
                        $statusClass = 'status-cancelled';
                    } elseif($order->order_status == 'ready_for_pickup') {
                        $statusClass = 'status-ready';
                    } elseif($order->order_status == 'paid') {
                        $statusClass = 'status-paid';
                    }
                @endphp
                <tr data-status="{{ $order->order_status }}">
                    <td data-label="Order ID">
                        <div class="order-id">
                            {{ $order->order_number }}
                            @if($order->created_at->diffInHours(now()) < 24)
                            <span class="badge-new">NEW</span>
                            @endif
                        </div>
                    </td>
                    <td data-label="Product" class="product-name-cell">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <img src="{{ $order->product_image }}" alt="{{ $order->product_name }}" class="product-image">
                            <span>{{ $order->product_name }}</span>
                        </div>
                    </td>
                    <td data-label="Customer">
                        <div class="customer-info">
                            <span class="customer-name">{{ $order->buyer->name ?? 'Customer' }}</span>
                            <span class="customer-phone">{{ $order->buyer->primary_mobile ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td data-label="Quantity" class="quantity-cell">
                        {{ $order->formatted_quantity }}
                    </td>
                    <td data-label="Amount" class="order-amount">
                        LKR {{ number_format($order->total_amount, 2) }}
                    </td>
                    <td data-label="Status">
                        <span class="status-tag {{ $statusClass }}">
                            {{ str_replace('_', ' ', ucfirst($order->order_status)) }}
                        </span>
                    </td>
                    <td data-label="Actions">
                        <button class="action-btn" onclick="viewOrderHistory({{ $order->id }})" title="View Details">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <h3>No Order History</h3>
            <p>You haven't completed any orders yet.</p>
            <a href="{{ route('farmer.orders.active') }}" class="tab-btn active" style="text-decoration: none;">
                <i class="fa-solid fa-clock"></i>
                View Active Orders
            </a>
        </div>
        @endif

        @if($orders->count() > 0)
        <div class="pagination-bar">
            <div class="pagination-info">
                Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} orders
            </div>
            <div class="pagination-nav">
                @if($orders->onFirstPage())
                <a href="#" class="pagination-btn disabled">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
                @else
                <a href="{{ $orders->previousPageUrl() }}" class="pagination-btn">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
                @endif

                @foreach(range(1, $orders->lastPage()) as $page)
                @if($page == $orders->currentPage())
                <a href="#" class="pagination-btn active">{{ $page }}</a>
                @else
                <a href="{{ $orders->url($page) }}" class="pagination-btn">{{ $page }}</a>
                @endif
                @endforeach

                @if($orders->hasMorePages())
                <a href="{{ $orders->nextPageUrl() }}" class="pagination-btn">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
                @else
                <a href="#" class="pagination-btn disabled">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function viewOrderHistory(orderId) {
        Swal.fire({
            title: 'Loading Order Details',
            text: 'Please wait...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '{{ route("farmer.orders.view", ":id") }}'.replace(':id', orderId),
            type: 'GET',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            success: function(response) {
                Swal.close();
                if (response.success) {
                    const order = response.order;

                    // Build the HTML content for the modal
                    let htmlContent = `
                        <div style="text-align: left;">
                            <h5 style="color: var(--primary-green); margin-bottom: 1rem; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.5rem;">
                                <i class="fa-solid fa-hashtag"></i> ${order.order_number}
                            </h5>

                            <div class="order-details-grid">
                                <div class="order-details-section">
                                    <span class="order-details-label">Customer Details:</span>
                                    <div class="order-details-value">
                                        <strong>${order.buyer ? order.buyer.name : 'N/A'}</strong><br>
                                        <small>Phone: ${order.buyer ? order.buyer.primary_mobile : 'N/A'}</small>
                                    </div>
                                </div>

                                <div class="order-details-section">
                                    <span class="order-details-label">Order Details:</span>
                                    <div class="order-details-value">
                                        <strong>Amount: LKR ${parseFloat(order.total_amount).toFixed(2)}</strong><br>
                                        <small>Status: ${order.order_status.replace(/_/g, ' ').toUpperCase()}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="order-details-section">
                                <span class="order-details-label">Order Items:</span>`;

                    // Add each order item
                    order.order_items.forEach((item, index) => {
                        const product = item.product;
                        htmlContent += `
                            <div style="margin: 1rem 0; padding: 1rem; background: #fff; border-radius: 5px; border: 1px solid #eee;">
                                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                                    <img src="${item.product_image || '{{ asset('assets/images/product-placeholder.png') }}'}"
                                         alt="${item.product_name_snapshot}"
                                         style="width: 80px; height: 80px; object-fit: cover; border-radius: 5px;">
                                    <div>
                                        <strong style="font-size: 1.1rem;">${item.product_name_snapshot}</strong><br>
                                        <small>Quantity: ${item.formatted_quantity || item.quantity_ordered + ' units'}</small><br>
                                        <small>Unit Price: LKR ${parseFloat(item.unit_price_snapshot).toFixed(2)}</small><br>
                                        <small>Item Total: LKR ${parseFloat(item.item_total).toFixed(2)}</small>
                                    </div>
                                </div>`;

                        // Add product details if available
                        if (product) {
                            htmlContent += `
                                <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ddd;">
                                    <span class="order-details-label">Product Details:</span>
                                    <div class="order-details-value">
                                        <small><strong>Description:</strong> ${product.product_description || 'No description available'}</small><br>
                                        <small><strong>Quality Grade:</strong> ${product.quality_grade || 'N/A'}</small><br>
                                        <small><strong>Pickup Address:</strong> ${product.pickup_address || 'N/A'}</small>
                                    </div>
                                </div>`;
                        }

                        htmlContent += `</div>`;
                    });

                    htmlContent += `
                            </div>

                            <div class="order-details-section">
                                <span class="order-details-label">Order Summary:</span>
                                <div class="order-details-value">
                                    <small><strong>Items Total:</strong> LKR ${parseFloat(order.items_total || order.total_amount).toFixed(2)}</small><br>
                                    <small><strong>Delivery Fee:</strong> LKR ${parseFloat(order.delivery_fee || 0).toFixed(2)}</small><br>
                                    <small><strong>Grand Total:</strong> LKR ${parseFloat(order.total_amount).toFixed(2)}</small>
                                </div>
                            </div>

                            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 2px solid #f0f0f0; color: #666; font-size: 0.9rem;">
                                <i class="fa-solid fa-calendar"></i>
                                Order Date: ${new Date(order.created_at).toLocaleDateString()} ${new Date(order.created_at).toLocaleTimeString()}
                            </div>
                        </div>`;

                    Swal.fire({
                        title: 'Order Details',
                        html: htmlContent,
                        width: '700px',
                        showConfirmButton: true,
                        confirmButtonText: 'Close',
                        showCancelButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load order details.'
                });
            }
        });
    }

    $(document).ready(function() {
        $('#searchInput').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('.orders-table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        $('.tab-btn').on('click', function() {
            const filter = $(this).data('filter');

            $('.tab-btn').removeClass('active');
            $(this).addClass('active');

            if (filter === 'all') {
                $('.orders-table tbody tr').show();
            } else {
                $('.orders-table tbody tr').each(function() {
                    const status = $(this).data('status');
                    if (filter === 'completed' && (status === 'completed' || status === 'delivered')) {
                        $(this).show();
                    } else if (filter === 'paid' && status === 'paid') {
                        $(this).show();
                    } else if (filter === 'ready' && status === 'ready_for_pickup') {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });
    });
</script>
@endsection
