@extends('buyer.layouts.buyer_master')

@section('title', 'My Product Requests')

@section('page-title', 'My Product Requests')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
    --purple: #8b5cf6;
    --yellow: #f59e0b;
    --shadow-sm: 0 1px 3px rgba(15,23,36,0.04);
    --shadow-md: 0 7px 15px rgba(15,23,36,0.08);
}

.my-requests-wrapper {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

.requests-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 15px;
}

.requests-header h2 {
    color: var(--text-color);
    font-size: 2.5rem;
    margin: 0;
    font-weight: 700;
}

.btn-new-request {
    background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-new-request:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);
}

.status-filters {
    display: flex;
    gap: 10px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.status-filter-btn {
    padding: 8px 16px;
    border-radius: 20px;
    border: 2px solid #e5e7eb;
    background: white;
    color: var(--muted);
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.status-filter-btn:hover {
    border-color: var(--primary-green);
    color: var(--primary-green);
}

.status-filter-btn.active {
    background: var(--primary-green);
    color: white;
    border-color: var(--primary-green);
}

.requests-grid {
    display: grid;
    gap: 25px;
}

.request-card {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 25px;
    box-shadow: var(--shadow-md);
    border: 1px solid rgba(15,23,36,0.05);
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
}

.request-card:hover {
    transform: translateY(-5px) scale(1.01);
    box-shadow: 0 15px 35px rgba(15,23,36,0.15);
}

.request-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 5px;
    height: 100%;
}

.request-card.active::before {
    background: var(--primary-green);
}

.request-card.fulfilled::before {
    background: var(--blue);
}

.request-card.expired::before {
    background: var(--muted);
}

.request-card.cancelled::before {
    background: #ef4444;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.product-info {
    flex: 1;
}

.product-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-color);
    margin-bottom: 5px;
}

.product-description {
    color: var(--muted);
    font-size: 1rem;
    margin-bottom: 15px;
}

.product-image {
    width: 120px;
    height: 120px;
    border-radius: 12px;
    object-fit: cover;
    border: 2px solid #e5e7eb;
}

.card-body {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.detail-icon {
    width: 40px;
    height: 40px;
    background: rgba(16, 185, 129, 0.1);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-green);
    font-size: 1.2rem;
}

.detail-content h4 {
    font-size: 0.9rem;
    color: var(--muted);
    margin: 0;
    font-weight: 500;
}

.detail-content p {
    font-size: 1.2rem;
    color: var(--text-color);
    margin: 5px 0 0;
    font-weight: 600;
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
    flex-wrap: wrap;
    gap: 15px;
}

.status-badge {
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-active {
    background: rgba(16, 185, 129, 0.1);
    color: var(--primary-green);
}

.status-fulfilled {
    background: rgba(59, 130, 246, 0.1);
    color: var(--blue);
}

.status-expired {
    background: rgba(107, 114, 128, 0.1);
    color: var(--muted);
}

.status-cancelled {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.days-left {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-left: 8px;
}

.days-left.warning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--accent-amber);
}

.days-left.danger {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.days-left.normal {
    background: rgba(16, 185, 129, 0.1);
    color: var(--primary-green);
}

.actions {
    display: flex;
    gap: 10px;
}

.action-btn {
    padding: 8px 16px;
    border-radius: 8px;
    border: none;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-edit {
    background: rgba(16, 185, 129, 0.1);
    color: var(--primary-green);
}

.btn-edit:hover {
    background: var(--primary-green);
    color: white;
}

.btn-delete {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.btn-delete:hover {
    background: #ef4444;
    color: white;
}

.no-requests {
    text-align: center;
    padding: 60px 20px;
    background: var(--card-bg);
    border-radius: 16px;
    box-shadow: var(--shadow-sm);
}

.no-requests-icon {
    font-size: 4rem;
    color: var(--muted);
    margin-bottom: 20px;
    opacity: 0.5;
}

.no-requests h3 {
    color: var(--text-color);
    font-size: 1.8rem;
    margin-bottom: 15px;
}

.no-requests p {
    color: var(--muted);
    font-size: 1.1rem;
    margin-bottom: 25px;
}

.loading {
    text-align: center;
    padding: 40px;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e5e7eb;
    border-top: 4px solid var(--primary-green);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.status-select {
    padding: 6px 12px;
    border-radius: 8px;
    border: 2px solid #e5e7eb;
    background: white;
    color: var(--text-color);
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.status-select:focus {
    outline: none;
    border-color: var(--primary-green);
}

.expired-date {
    color: #ef4444;
    font-weight: 600;
}

@media (max-width: 1200px) {
    .requests-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 992px) {
    .my-requests-wrapper {
        padding: 15px;
    }

    .requests-header h2 {
        font-size: 2rem;
    }

    .card-body {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .card-header {
        flex-direction: column;
    }

    .product-image {
        width: 100%;
        height: 200px;
    }

    .card-body {
        grid-template-columns: 1fr;
    }

    .card-footer {
        flex-direction: column;
        align-items: stretch;
    }

    .actions {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .my-requests-wrapper {
        padding: 10px;
    }

    .requests-header h2 {
        font-size: 1.75rem;
    }

    .request-card {
        padding: 20px;
    }

    .status-filters {
        justify-content: center;
    }

    .action-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection

@section('content')
<div class="my-requests-wrapper">
    <div class="requests-header">
        <h2>My Product Requests</h2>
        <a href="{{ route('buyer.productRequest.create') }}" class="btn-new-request">
            <i class="fas fa-plus-circle"></i>
            New Request
        </a>
    </div>

    <div class="status-filters">
        <button class="status-filter-btn active" data-status="all">All Requests</button>
        <button class="status-filter-btn" data-status="active">Active</button>
        <button class="status-filter-btn" data-status="fulfilled">Fulfilled</button>
        <button class="status-filter-btn" data-status="expired">Expired</button>
        <button class="status-filter-btn" data-status="cancelled">Cancelled</button>
    </div>

    <div class="requests-grid" id="requestsContainer">
        @if($requests->isEmpty())
        <div class="no-requests">
            <div class="no-requests-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <h3>No Requests Yet</h3>
            <p>You haven't made any product requests. Create your first request to let farmers know what you need.</p>
            <a href="{{ route('buyer.productRequest.create') }}" class="btn-new-request">
                <i class="fas fa-plus-circle"></i>
                Make Your First Request
            </a>
        </div>
        @else
        @foreach($requests as $request)
        @php
            $daysLeft = 0;
            $daysLeftClass = 'normal';
            $daysLeftText = '';

            if ($request->status == 'active') {
                $neededDate = \Carbon\Carbon::parse($request->needed_date);
                $today = \Carbon\Carbon::now();

                if ($neededDate->isPast()) {
                    $daysLeft = 0;
                    $daysLeftClass = 'danger';
                    $daysLeftText = 'Overdue';
                } else {
                    $daysLeft = $today->diffInDays($neededDate, false);

                    if ($daysLeft <= 3 && $daysLeft > 0) {
                        $daysLeftClass = 'warning';
                        $daysLeftText = $daysLeft . ' day' . ($daysLeft != 1 ? 's' : '') . ' left';
                    } elseif ($daysLeft > 3) {
                        $daysLeftClass = 'normal';
                        $daysLeftText = $daysLeft . ' day' . ($daysLeft != 1 ? 's' : '') . ' left';
                    }
                }
            } elseif ($request->status == 'expired') {
                $daysLeftClass = 'danger';
                $daysLeftText = 'Expired';
            }
        @endphp

        <div class="request-card {{ $request->status }}" data-status="{{ $request->status }}">
            <div class="card-header">
                <div class="product-info">
                    <h3 class="product-name">{{ $request->product_name }}</h3>
                    @if($request->description)
                    <p class="product-description">{{ $request->description }}</p>
                    @endif
                </div>
                @if($request->product_image)
                <img src="{{ asset('uploads/buyer_product_requests/' . $request->product_image) }}"
                     alt="{{ $request->product_name }}"
                     class="product-image"
                     onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
                @endif
            </div>

            <div class="card-body">
                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div class="detail-content">
                        <h4>Quantity Needed</h4>
                        <p>{{ number_format($request->needed_quantity, 2) }} {{ $request->unit_of_measure }}</p>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="detail-content">
                        <h4>Needed By</h4>
                        <p>
                            {{ \Carbon\Carbon::parse($request->needed_date)->format('M d, Y') }}
                            @if($request->status == 'active' && $daysLeftText)
                            <span class="days-left {{ $daysLeftClass }}">
                                <i class="fas fa-clock"></i>
                                {{ $daysLeftText }}
                            </span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <div class="detail-content">
                        <h4>Expected Price</h4>
                        <p>
                            @if($request->unit_price)
                            Rs. {{ number_format($request->unit_price, 2) }}/{{ $request->unit_of_measure }}
                            @else
                            <span style="color: var(--muted);">Not specified</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="detail-content">
                        <h4>Requested On</h4>
                        <p>{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y - h:i A') }}</p>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div>
                    <span class="status-badge status-{{ $request->status }}">
                        {{ ucfirst($request->status) }}
                    </span>
                </div>

                <div class="actions">
                    <select class="status-select" data-request-id="{{ $request->id }}"
                            onchange="updateStatus(this, {{ $request->id }})">
                        <option value="active" {{ $request->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="fulfilled" {{ $request->status == 'fulfilled' ? 'selected' : '' }}>Fulfilled</option>
                        <option value="expired" {{ $request->status == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="cancelled" {{ $request->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>

                    <button class="action-btn btn-delete" onclick="deleteRequest({{ $request->id }})">
                        <i class="fas fa-trash"></i>
                        Delete
                    </button>
                </div>
            </div>
        </div>
        @endforeach
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
async function updateStatus(select, requestId) {
    const newStatus = select.value;
    const originalStatus = select.getAttribute('data-original-value');

    const result = await Swal.fire({
        title: 'Update Status?',
        text: `Change request status to "${newStatus}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, update it!',
        cancelButtonText: 'Cancel'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/buyer/product-request/${requestId}/status`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            });

            const data = await response.json();

            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: data.message,
                    confirmButtonColor: '#10B981'
                });

                location.reload();
            } else {
                throw new Error(data.message);
            }

        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message,
                confirmButtonColor: '#ef4444'
            });
            select.value = originalStatus;
        }
    } else {
        select.value = originalStatus;
    }
}

async function deleteRequest(requestId) {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: "This request will be permanently deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/buyer/product-request/${requestId}/delete`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: data.message,
                    confirmButtonColor: '#10B981'
                });

                location.reload();
            } else {
                throw new Error(data.message);
            }

        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message,
                confirmButtonColor: '#ef4444'
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.status-filter-btn');
    const requestCards = document.querySelectorAll('.request-card');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const status = this.getAttribute('data-status');

            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            requestCards.forEach(card => {
                if (status === 'all' || card.getAttribute('data-status') === status) {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 10);
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            });
        });
    });

    document.querySelectorAll('.status-select').forEach(select => {
        select.setAttribute('data-original-value', select.value);
    });

    autoUpdateExpiredRequests();
});

async function autoUpdateExpiredRequests() {
    try {
        const response = await fetch('/buyer/product-requests/check-expired', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.updated > 0) {
            console.log(`Auto-updated ${data.updated} expired requests`);
        }
    } catch (error) {
        console.error('Failed to auto-update expired requests:', error);
    }
}
</script>
@endsection
