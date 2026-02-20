@extends('buyer.layouts.buyer_master')

@section('title', 'Order History')
@section('page-title', 'Order History')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
    background: var(--body-bg);
    min-height: 100vh;
}

.history-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1.5rem 1rem;
}

.page-header {
    text-align: center;
    animation: slideDown 0.6s ease;
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

.page-header h1 {
    color: var(--text-color);
    font-weight: 700;
    font-size: 1.8rem;
    margin-bottom: 0.25rem;
}

.page-header p {
    color: var(--muted);
    font-size: 0.95rem;
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: var(--card-bg);
    border-radius: 10px;
    padding: 1rem;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
    text-align: center;
    border-left: 3px solid var(--primary-green);
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-green), var(--dark-green));
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.stat-card:hover::before {
    transform: scaleX(1);
}

.stat-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
    color: white;
    font-size: 1rem;
}

.stat-number {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--text-color);
    margin-bottom: 0.25rem;
}

.stat-label {
    color: var(--muted);
    font-size: 0.8rem;
}

.filter-section {
    background: var(--card-bg);
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-sm);
    animation: fadeIn 0.5s ease 0.2s both;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.filter-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    color: var(--text-color);
    font-weight: 600;
    font-size: 0.9rem;
}

.filter-header i {
    color: var(--primary-green);
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 0.75rem;
}

.filter-group {
    margin-bottom: 0;
}

.filter-label {
    display: block;
    margin-bottom: 0.375rem;
    color: var(--text-color);
    font-weight: 500;
    font-size: 0.85rem;
}

.filter-select,
.filter-input {
    width: 100%;
    padding: 0.625rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 6px;
    background: white;
    color: var(--text-color);
    font-size: 0.85rem;
    transition: all 0.3s ease;
}

.filter-select:focus,
.filter-input:focus {
    outline: none;
    border-color: var(--primary-green);
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.1);
}

.apply-btn {
    background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
    color: white;
    border: none;
    padding: 0.625rem 1.25rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1rem;
}

.apply-btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.orders-wrapper {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.empty-state {
    text-align: center;
    padding: 3rem 1.5rem;
    background: var(--card-bg);
    border-radius: 10px;
    box-shadow: var(--shadow-sm);
    animation: bounceIn 0.6s ease;
}

@keyframes bounceIn {
    0% {
        opacity: 0;
        transform: scale(0.8);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.empty-icon {
    font-size: 2.5rem;
    color: var(--muted);
    margin-bottom: 1rem;
    opacity: 0.5;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.order-card {
    background: var(--card-bg);
    border-radius: 10px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    transition: all 0.3s ease;
    animation: slideUp 0.5s ease;
    border: 1px solid #e2e8f0;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.order-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary-green);
}

.order-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.order-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.order-number {
    font-weight: 600;
    color: var(--text-color);
    font-size: 0.95rem;
}

.order-date {
    color: var(--muted);
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 15px;
    font-weight: 600;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.status-badge.pending {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.paid {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.completed {
    background: linear-gradient(135deg, #10B981, #059669);
    color: white;
}

.order-body {
    padding: 1rem;
}

.order-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-label {
    color: var(--muted);
    font-size: 0.8rem;
    margin-bottom: 0.25rem;
}

.info-value {
    color: var(--text-color);
    font-weight: 500;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.farmer-info {
    background: #f8fafc;
    border-radius: 6px;
    padding: 0.75rem;
    margin-bottom: 1rem;
    border-left: 3px solid var(--accent-amber);
}

.farmer-name {
    font-weight: 600;
    color: var(--text-color);
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.farmer-detail {
    color: var(--muted);
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
    margin-bottom: 0.125rem;
}

.order-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
}

.action-btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
    transition: all 0.3s ease;
    cursor: pointer;
    text-decoration: none;
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
    color: white;
}

.btn-primary:hover {
    opacity: 0.9;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.2);
}

.btn-secondary {
    background: var(--blue);
    color: white;
}

.btn-secondary:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.pagination {
    display: flex;
    justify-content: center;
    margin-top: 1.5rem;
    gap: 0.375rem;
}

.pagination-btn {
    padding: 0.375rem 0.75rem;
    border: 1px solid #e2e8f0;
    background: white;
    border-radius: 5px;
    color: var(--text-color);
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.pagination-btn:hover {
    background: #f8fafc;
    border-color: var(--primary-green);
}

.pagination-btn.active {
    background: var(--primary-green);
    color: white;
    border-color: var(--primary-green);
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(3px);
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid rgba(255,255,255,0.3);
    border-top: 3px solid var(--primary-green);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.invoice-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 10000;
    backdrop-filter: blur(5px);
    animation: fadeIn 0.3s ease;
}

.invoice-content {
    background: white;
    border-radius: 12px;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
    margin: 2rem auto;
    box-shadow: var(--shadow-lg);
    animation: slideUp 0.4s ease;
}

.invoice-header {
    background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
    color: white;
    padding: 1.5rem;
    border-radius: 12px 12px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.invoice-title {
    font-size: 1.2rem;
    font-weight: 700;
}

.close-invoice {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.close-invoice:hover {
    background: rgba(255,255,255,0.3);
    transform: rotate(90deg);
}

.invoice-body {
    padding: 0.3rem;
}

.invoice-header {
    padding-bottom: 5px !important;
    margin-bottom: 5px !important;
}

.invoice-company {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.invoice-logo {
    width: 100px;
    height: auto;
}

.company-info h3 {
    color: var(--text-color);
    margin-bottom: 0.25rem;
    font-size: 1.3rem;
}

.company-info p {
    color: var(--muted);
    font-size: 0.9rem;
    margin-bottom: 0.125rem;
}

.invoice-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 8px;
}

.detail-item h4 {
    color: var(--text-color);
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.detail-item p {
    color: var(--muted);
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.invoice-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 2rem;
}

.invoice-table th {
    background: #f1f5f9;
    padding: 0.75rem;
    text-align: left;
    color: var(--text-color);
    font-weight: 600;
    font-size: 0.9rem;
    border-bottom: 2px solid #e2e8f0;
}

.invoice-table td {
    padding: 0.75rem;
    border-bottom: 1px solid #e2e8f0;
    color: var(--text-color);
    font-size: 0.9rem;
}

.invoice-table tr:hover {
    background: #f8fafc;
}

.invoice-totals {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 8px;
    max-width: 300px;
    margin-left: auto;
}

.total-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}

.total-row:last-child {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--text-color);
    border-top: 2px solid #e2e8f0;
    padding-top: 0.75rem;
    margin-top: 0.75rem;
}

.invoice-footer {
    display: flex;
    gap: 1rem;
    justify-content: center;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 0 0 12px 12px;
}

@media (max-width: 1200px) {
    .history-container {
        max-width: 1000px;
    }
}

@media (max-width: 992px) {
    .history-container {
        max-width: 800px;
    }

    .stats-cards {
        grid-template-columns: repeat(2, 1fr);
    }

    .filter-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .invoice-content {
        margin: 1rem;
        max-height: 85vh;
    }
}

@media (max-width: 768px) {
    .history-container {
        padding: 1rem;
    }

    .page-header h1 {
        font-size: 1.5rem;
    }

    .stats-cards {
        grid-template-columns: 1fr;
    }

    .filter-grid {
        grid-template-columns: 1fr;
    }

    .order-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .order-info-grid {
        grid-template-columns: 1fr;
    }

    .order-actions {
        flex-direction: column;
    }

    .action-btn {
        width: 100%;
        justify-content: center;
    }

    .invoice-details-grid {
        grid-template-columns: 1fr;
    }

    .invoice-totals {
        max-width: 100%;
    }

    .invoice-footer {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .history-container {
        padding: 0.75rem;
    }

    .page-header h1 {
        font-size: 1.3rem;
    }

    .stat-card {
        padding: 0.75rem;
    }

    .stat-number {
        font-size: 1.2rem;
    }

    .filter-section {
        padding: 0.75rem;
    }

    .order-body {
        padding: 0.75rem;
    }

    .invoice-body {
        padding: 1rem;
    }
}
</style>
@endsection

@section('content')
<div class="history-container">
    <div class="page-header">
        <h1><i class="fas fa-shopping-bag me-2"></i>Order History</h1>
        <p>View and manage your past purchases</p>
    </div>

    @if(isset($orders) && count($orders) > 0)
    <div class="stats-cards">
        @php
            $totalOrders = count($orders);
            $totalAmount = $orders->sum('total_amount');
            $completedOrders = $orders->where('order_status', 'completed')->count();
            $pendingOrders = $orders->whereIn('order_status', ['pending', 'confirmed', 'paid', 'ready_for_pickup'])->count();
        @endphp

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-number">{{ $totalOrders }}</div>
            <div class="stat-label">Total Orders</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-rupee-sign"></i>
            </div>
            <div class="stat-number">Rs. {{ number_format($totalAmount, 2) }}</div>
            <div class="stat-label">Total Spent</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number">{{ $completedOrders }}</div>
            <div class="stat-label">Completed</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number">{{ $pendingOrders }}</div>
            <div class="stat-label">Active</div>
        </div>
    </div>

    <div class="filter-section">
        <div class="filter-header">
            <i class="fas fa-filter"></i>
            Filter Orders
        </div>
        <div class="filter-grid">
            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select class="filter-select" id="statusFilter">
                    <option value="all">All Status</option>
                    <option value="paid">Paid</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">From Date</label>
                <input type="text" class="filter-input" id="fromDate" placeholder="Select date">
            </div>

            <div class="filter-group">
                <label class="filter-label">To Date</label>
                <input type="text" class="filter-input" id="toDate" placeholder="Select date">
            </div>

            <div class="filter-group">
                <label class="filter-label">Sort By</label>
                <select class="filter-select" id="sortFilter">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="amount_high">Amount (High to Low)</option>
                    <option value="amount_low">Amount (Low to High)</option>
                </select>
            </div>
        </div>

        <button class="apply-btn" id="applyFilters">
            <i class="fas fa-check"></i>
            Apply Filters
        </button>
    </div>

    <div class="orders-wrapper" id="ordersContainer">
        @foreach($orders as $order)
            <div class="order-card" data-status="{{ $order->order_status }}" data-date="{{ $order->created_at }}" data-amount="{{ $order->total_amount }}">
                <div class="order-header">
                    <div class="order-title">
                        <div class="order-number">
                            <i class="fas fa-hashtag me-1"></i>
                            {{ $order->order_number }}
                        </div>
                        <div class="order-date">
                            <i class="far fa-calendar me-1"></i>
                            {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}
                        </div>
                    </div>

                    <div class="status-badge {{ $order->order_status }}">
                        @switch($order->order_status)
                            @case('pending')
                                <i class="fas fa-clock"></i>
                                @break
                            @case('confirmed')
                                <i class="fas fa-check-circle"></i>
                                @break
                            @case('paid')
                                <i class="fas fa-credit-card"></i>
                                @break
                            @case('ready_for_pickup')
                                <i class="fas fa-truck"></i>
                                @break
                            @case('completed')
                                <i class="fas fa-check-double"></i>
                                @break
                            @case('cancelled')
                                <i class="fas fa-times-circle"></i>
                                @break
                            @case('refunded')
                                <i class="fas fa-undo"></i>
                                @break
                        @endswitch
                        {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                    </div>
                </div>

                <div class="order-body">
                    <div class="order-info-grid">
                        <div class="info-item">
                            <span class="info-label">Total Amount</span>
                            <span class="info-value">
                                <i class="fas fa-rupee-sign"></i>
                                Rs. {{ number_format($order->total_amount, 2) }}
                            </span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Order Date</span>
                            <span class="info-value">
                                <i class="far fa-calendar-alt"></i>
                                {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}
                            </span>
                        </div>

                        @if($order->paid_date)
                        <div class="info-item">
                            <span class="info-label">Paid On</span>
                            <span class="info-value">
                                <i class="fas fa-calendar-check"></i>
                                {{ \Carbon\Carbon::parse($order->paid_date)->format('M d, Y') }}
                            </span>
                        </div>
                        @endif
                    </div>

                    <div class="farmer-info">
                        <div class="farmer-name">
                            <i class="fas fa-user-tie"></i>
                            Lead Farmer: {{ $order->lead_farmer_name }}
                        </div>
                        <div class="farmer-detail">
                            <i class="fas fa-user"></i>
                            Farmer: {{ $order->farmer_name }}
                        </div>
                    </div>

                    <div class="order-actions">
                        <button class="action-btn btn-primary view-invoice-btn" data-order-id="{{ $order->id }}">
                            <i class="fas fa-file-invoice"></i>
                            View Invoice
                        </button>

                        @if($order->order_status == 'ready_for_pickup')
                            <button class="action-btn btn-secondary">
                                <i class="fas fa-map-marker-alt"></i>
                                Track Pickup
                            </button>
                        @endif

                        @if($order->order_status == 'completed')
                            <button class="action-btn btn-secondary feedback-btn" data-order-id="{{ $order->id }}">
                                <i class="fas fa-star"></i>
                                Rate Order
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @else
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-shopping-basket"></i>
        </div>
        <h3 style="color: var(--text-color); margin-bottom: 0.75rem; font-size: 1.2rem;">No Orders Found</h3>
        <p style="color: var(--muted); margin-bottom: 1rem; font-size: 0.9rem;">You haven't placed any orders yet.</p>
        <a href="{{ route('buyer.browseProducts') }}" class="action-btn btn-primary" style="width: auto; display: inline-flex;">
            <i class="fas fa-store me-2"></i>
            Start Shopping
        </a>
    </div>
    @endif
</div>

<div class="loading-overlay" id="loadingOverlay" style="display: none;">
    <div class="loading-spinner"></div>
</div>

<div class="invoice-modal" id="invoiceModal">
    <div class="invoice-content">
        <div class="invoice-header">
            <div class="invoice-title">
                <i class="fas fa-file-invoice me-2"></i>
                Order Invoice
            </div>
            <button class="close-invoice" id="closeInvoice">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="invoice-body" id="invoiceBody">
        </div>
        <div class="invoice-footer">
            <button class="action-btn btn-secondary" id="downloadInvoice">
                <i class="fas fa-download me-2"></i>
                Download PDF
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    const applyFiltersBtn = document.getElementById('applyFilters');
    const ordersContainer = document.getElementById('ordersContainer');
    const statusFilter = document.getElementById('statusFilter');
    const sortFilter = document.getElementById('sortFilter');
    const fromDateInput = document.getElementById('fromDate');
    const toDateInput = document.getElementById('toDate');
    const invoiceModal = document.getElementById('invoiceModal');
    const invoiceBody = document.getElementById('invoiceBody');
    const closeInvoiceBtn = document.getElementById('closeInvoice');
    const downloadInvoiceBtn = document.getElementById('downloadInvoice');

    // Initialize date pickers
    if (fromDateInput) {
        flatpickr(fromDateInput, {
            dateFormat: "Y-m-d",
            maxDate: "today",
            onChange: function(selectedDates, dateStr) {
                if (dateStr && toDateInput._flatpickr) {
                    toDateInput._flatpickr.set("minDate", dateStr);
                }
            }
        });
    }

    if (toDateInput) {
        flatpickr(toDateInput, {
            dateFormat: "Y-m-d",
            maxDate: "today",
            onChange: function(selectedDates, dateStr) {
                if (dateStr && fromDateInput._flatpickr) {
                    fromDateInput._flatpickr.set("maxDate", dateStr);
                }
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
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
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

    function filterOrders() {
        const status = statusFilter ? statusFilter.value : 'all';
        const sort = sortFilter ? sortFilter.value : 'newest';
        const fromDate = fromDateInput ? fromDateInput.value : '';
        const toDate = toDateInput ? toDateInput.value : '';

        const orderCards = document.querySelectorAll('.order-card');
        let filteredCards = Array.from(orderCards);

        if (status !== 'all') {
            filteredCards = filteredCards.filter(card => card.dataset.status === status);
        }

        if (fromDate) {
            const from = new Date(fromDate);
            filteredCards = filteredCards.filter(card => {
                const orderDate = new Date(card.dataset.date);
                return orderDate >= from;
            });
        }

        if (toDate) {
            const to = new Date(toDate);
            to.setHours(23, 59, 59, 999);
            filteredCards = filteredCards.filter(card => {
                const orderDate = new Date(card.dataset.date);
                return orderDate <= to;
            });
        }

        filteredCards.sort((a, b) => {
            switch (sort) {
                case 'oldest':
                    return new Date(a.dataset.date) - new Date(b.dataset.date);
                case 'amount_high':
                    return parseFloat(b.dataset.amount) - parseFloat(a.dataset.amount);
                case 'amount_low':
                    return parseFloat(a.dataset.amount) - parseFloat(b.dataset.amount);
                case 'newest':
                default:
                    return new Date(b.dataset.date) - new Date(a.dataset.date);
            }
        });

        if (ordersContainer) {
            ordersContainer.innerHTML = '';

            if (filteredCards.length === 0) {
                ordersContainer.innerHTML = `
                    <div class="empty-state" style="margin-top: 1rem;">
                        <div class="empty-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 style="color: var(--text-color); margin-bottom: 0.75rem; font-size: 1.2rem;">No Orders Found</h3>
                        <p style="color: var(--muted);">Try adjusting your filters</p>
                    </div>
                `;
            } else {
                filteredCards.forEach(card => {
                    card.style.animation = 'slideUp 0.3s ease';
                    ordersContainer.appendChild(card);
                });
            }

            showToast('Filters applied', 'success');
        }
    }

    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', filterOrders);
    }

    async function loadInvoice(orderId) {
        if (loadingOverlay) {
            loadingOverlay.style.display = 'flex';
        }

        try {
            // Get CSRF token from meta tag or input
            let csrfToken = '';
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            if (metaToken) {
                csrfToken = metaToken.getAttribute('content');
            } else {
                const inputToken = document.querySelector('input[name="_token"]');
                if (inputToken) {
                    csrfToken = inputToken.value;
                }
            }

            const response = await fetch(`/buyer/invoice/data/${orderId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to load invoice');
            }

            const itemsHtml = data.items.map(item => `
                <tr>
                    <td>${item.product_name}</td>
                    <td>${item.quantity} ${item.unit_of_measure || ''}</td>
                    <td>Rs. ${item.unit_price}</td>
                    <td>Rs. ${item.total}</td>
                </tr>
            `).join('');

            // Create pickup information HTML
            let pickupInfoHtml = '';
            if (data.products_pickup_address) {
                pickupInfoHtml = `
                    <div style="background: #e8f5e9; border-radius: 6px; padding: 10px; margin: 10px 0; border-left: 3px solid #4CAF50;">
                        <div style="font-weight: 600; color: #2e7d32; margin-bottom: 5px; font-size: 0.95rem;">
                            <i class="fas fa-map-marker-alt" style="margin-right: 5px;"></i>
                            Pickup Information
                        </div>
                        <div style="color: #555; font-size: 0.85rem; line-height: 1.4;">
                            <div style="margin-bottom: 3px;"><strong>Address:</strong> ${data.products_pickup_address}</div>
                            ${data.products_pickup_map_link ? `<div><strong>Map Link:</strong> <a href="${data.products_pickup_map_link}" target="_blank" style="color: #3b82f6; text-decoration: none;">${data.products_pickup_map_link}</a></div>` : ''}
                        </div>
                    </div>
                `;
            }

            // Create payment details HTML
            let paymentDetailsHtml = '';
            if (data.paid_date) {
                paymentDetailsHtml = `
                    <div class="detail-item">
                        <h4>Payment Details</h4>
                        <p><strong>Payment Method:</strong> ${data.payment_method || 'Credit Card'}</p>
                        <p><strong>Paid Date:</strong> ${data.paid_date}</p>
                        <p><strong>Status:</strong> ${data.payment_status}</p>
                    </div>
                `;
            }

            const invoiceHtml = `
                <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 11px; line-height: 1.4; color: #0f1724; background-color: #f1f5f9; margin: 0; padding: 5px;">
                    <div style="background: #e9f1dc; border-radius: 8px; padding: 10px; max-width: 1100px; margin: 0 auto;">
                        <!-- ================= HEADER ================= -->
                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                            <tr>
                                <td style="width: 65%; vertical-align: middle;">
                                    <div style="display: table;">
                                        <div style="display: table-cell; width: 140px; height: 80px; background-color: #4d7c32; border-radius: 40px; text-align: center; vertical-align: middle;">
                                            <img src="/assets/images/Logo Green Market.png" alt="GreenMarket Logo" style="max-width: 135px; max-height: 75px;" onerror="this.src='https://via.placeholder.com/135x75?text=Logo'">
                                        </div>
                                        <span style="display: table-cell; padding-left: 18px; font-size: 28px; font-weight: 900; color: #3e7033; vertical-align: middle; font-family: 'Georgia', serif;">
                                            GreenMarket
                                        </span>
                                    </div>
                                </td>

                                <td style="width: 20%; vertical-align: middle; text-align: left; font-size: 13px;">
                                    <div><strong>Invoice No.</strong> : ${data.invoice_number}</div>
                                    <div><strong>Order No.</strong> : ${data.order_number}</div>
                                    <div><strong>Date</strong> : ${data.order_date}</div>
                                </td>
                            </tr>
                        </table>

                        <div style="height: 5px; background-color: #4d7c32; margin-bottom: 25px;"></div>

                        <!-- ================= INVOICE DETAILS ================= -->
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; background: #f8fafc; padding: 1.5rem; border-radius: 8px;">
                            <div class="detail-item">
                                <h4 style="color: #0f1724; font-size: 0.9rem; margin-bottom: 0.5rem; font-weight: 600;">Buyer Details</h4>
                                <p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.25rem;"><strong>Name:</strong> ${data.buyer_name}</p>
                                <p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.25rem;"><strong>Contact:</strong> ${data.buyer_contact}</p>
                                <p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.25rem;"><strong>Address:</strong> ${data.buyer_address || 'Not provided'}</p>
                            </div>

                            <div class="detail-item">
                                <h4 style="color: #0f1724; font-size: 0.9rem; margin-bottom: 0.5rem; font-weight: 600;">Seller Details</h4>
                                <p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.25rem;"><strong>Name:</strong> ${data.lead_farmer_name}</p>
                                <p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.25rem;"><strong>Contact:</strong> ${data.lead_farmer_contact}</p>
                                <p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.25rem;"><strong>Grama Niladari Division:</strong> ${data.lead_farmer_GNdivision || 'Not provided'}</p>
                            </div>

                            ${paymentDetailsHtml}
                        </div>

                        <!-- ================= PICKUP INFORMATION ================= -->
			            ${pickupInfoHtml}

                        <!-- ================= ORDER ITEMS TABLE ================= -->
                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem;">
                            <thead>
                                <tr>
                                    <th style="background: #f1f5f9; padding: 0.75rem; text-align: left; color: #0f1724; font-weight: 600; font-size: 0.9rem; border-bottom: 2px solid #e2e8f0;">Product</th>
                                    <th style="background: #f1f5f9; padding: 0.75rem; text-align: left; color: #0f1724; font-weight: 600; font-size: 0.9rem; border-bottom: 2px solid #e2e8f0;">Quantity</th>
                                    <th style="background: #f1f5f9; padding: 0.75rem; text-align: left; color: #0f1724; font-weight: 600; font-size: 0.9rem; border-bottom: 2px solid #e2e8f0;">Unit Price</th>
                                    <th style="background: #f1f5f9; padding: 0.75rem; text-align: left; color: #0f1724; font-weight: 600; font-size: 0.9rem; border-bottom: 2px solid #e2e8f0;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                        </table>

                        <!-- ================= TOTALS ================= -->
                        <div style="background: #f8fafc; padding: 1.5rem; border-radius: 8px; max-width: 300px; margin-left: auto;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                                <span>Subtotal:</span>
                                <span>Rs. ${data.subtotal}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.1rem; color: #0f1724; border-top: 2px solid #e2e8f0; padding-top: 0.75rem; margin-top: 0.75rem;">
                                <span>Grand Total:</span>
                                <span>Rs. ${data.total_amount}</span>
                            </div>
                        </div>

                        <!-- ================= FOOTER NOTES ================= -->
                        <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #e2e8f0; color: #6b7280; font-size: 0.85rem;">
                            <p><strong>Notes:</strong> Thank you for your purchase! Please contact the farmer for pickup arrangements.</p>
                        </div>
                    </div>
                </div>
            `;

            if (invoiceBody) {
                invoiceBody.innerHTML = invoiceHtml;
            }

            if (invoiceModal) {
                invoiceModal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }

        } catch (error) {
            console.error('Error loading invoice:', error);
            showToast('Failed to load invoice. Please try again.', 'error');
        } finally {
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }
        }
    }

    // Attach event listeners to view invoice buttons
    document.querySelectorAll('.view-invoice-btn').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            if (orderId) {
                loadInvoice(orderId);
            } else {
                showToast('Invalid order ID', 'error');
            }
        });
    });

    if (closeInvoiceBtn) {
        closeInvoiceBtn.addEventListener('click', function() {
            if (invoiceModal) {
                invoiceModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }

    if (invoiceModal) {
        invoiceModal.addEventListener('click', function(e) {
            if (e.target === invoiceModal) {
                invoiceModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }


    if (downloadInvoiceBtn) {
        downloadInvoiceBtn.addEventListener('click', async function() {
            if (!invoiceBody || !invoiceBody.innerHTML.trim()) {
                showToast('No invoice content to download', 'error');
                return;
            }

            showToast('Preparing PDF download...', 'info');

            try {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                });

                const canvas = await html2canvas(invoiceBody, {
                    scale: 2,
                    useCORS: true,
                    logging: false,
                    backgroundColor: '#ffffff'
                });

                const imgData = canvas.toDataURL('image/png');
                const imgWidth = 200;
                const pageHeight = 280;
                const imgHeight = canvas.height * imgWidth / canvas.width;

                doc.addImage(imgData, 'PNG', 5, 5, imgWidth, imgHeight);

                // Add page number if content is too long
                let heightLeft = imgHeight;
                let position = 0;

                while (heightLeft >= pageHeight) {
                    position = heightLeft - pageHeight;
                    doc.addPage();
                    doc.addImage(imgData, 'PNG', 10, -position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }

                const fileName = `invoice-${Date.now()}.pdf`;
                doc.save(fileName);
                showToast('PDF downloaded successfully!', 'success');
            } catch (error) {
                console.error('Error generating PDF:', error);
                showToast('Failed to generate PDF. Please try printing instead.', 'error');
            }
        });
    }

    // Feedback button functionality
    document.querySelectorAll('.feedback-btn').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            if (!orderId) {
                showToast('Invalid order ID', 'error');
                return;
            }

            Swal.fire({
                title: 'Rate Your Order',
                html: `
                    <div style="text-align: center;">
                        <div style="display: flex; gap: 0.5rem; justify-content: center; margin: 1rem 0 2rem;" id="ratingStars">
                            ${[1,2,3,4,5].map(star => `
                                <i class="fas fa-star" data-value="${star}" style="font-size: 2rem; color: #e2e8f0; cursor: pointer; transition: color 0.2s;"></i>
                            `).join('')}
                        </div>
                        <input type="hidden" id="ratingValue" value="5">
                        <textarea id="feedbackComment" rows="3" style="width: 100%; padding: 0.75rem; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 0.9rem;" placeholder="Share your experience (optional)"></textarea>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Submit Rating',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6b7280',
                reverseButtons: true,
                preConfirm: () => {
                    const rating = document.getElementById('ratingValue').value;
                    const comment = document.getElementById('feedbackComment').value;

                    if (!rating) {
                        Swal.showValidationMessage('Please select a rating');
                        return false;
                    }

                    return { rating: parseInt(rating), comment: comment };
                },
                didOpen: () => {
                    const stars = document.querySelectorAll('#ratingStars .fa-star');
                    const ratingValue = document.getElementById('ratingValue');

                    stars.forEach(star => {
                        star.addEventListener('mouseenter', function() {
                            const value = parseInt(this.getAttribute('data-value'));
                            stars.forEach((s, index) => {
                                s.style.color = index < value ? '#f59e0b' : '#e2e8f0';
                            });
                        });

                        star.addEventListener('click', function() {
                            const value = parseInt(this.getAttribute('data-value'));
                            ratingValue.value = value;
                            stars.forEach((s, index) => {
                                s.style.color = index < value ? '#f59e0b' : '#e2e8f0';
                            });
                        });
                    });

                    // Set default rating to 5 stars
                    ratingValue.value = '5';
                    stars.forEach((star, index) => {
                        star.style.color = index < 5 ? '#f59e0b' : '#e2e8f0';
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit feedback via AJAX
                    fetch(`/buyer/order/${orderId}/feedback`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify(result.value)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast(data.message || 'Thank you for your feedback!', 'success');
                        } else {
                            showToast(data.message || 'Failed to submit feedback', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error submitting feedback:', error);
                        showToast('Failed to submit feedback. Please try again.', 'error');
                    });
                }
            });
        });
    });

    // Add animation delay to order cards
    document.querySelectorAll('.order-card').forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });

    // Check for URL parameters to auto-apply filters
    const urlParams = new URLSearchParams(window.location.search);
    const statusParam = urlParams.get('status');
    if (statusParam && statusFilter) {
        statusFilter.value = statusParam;
        setTimeout(() => filterOrders(), 100);
    }
});
</script>
