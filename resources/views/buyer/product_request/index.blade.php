@extends('farmer.layouts.farmer_master')

@section('title', 'Buyer Product Requests')

@section('page-title', 'Buyer Product Requests')

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

.requests-wrapper {
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

.requests-header {
    margin-bottom: 40px;
    text-align: center;
}

.requests-header h2 {
    color: var(--text-color);
    font-size: 2.5rem;
    margin-bottom: 15px;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.requests-header p {
    color: var(--muted);
    font-size: 1.2rem;
    max-width: 800px;
    margin: 0 auto;
    line-height: 1.6;
}

.search-filters {
    display: flex;
    gap: 15px;
    margin-bottom: 30px;
    flex-wrap: wrap;
    justify-content: center;
}

.search-box {
    flex: 1;
    max-width: 400px;
    position: relative;
}

.search-box input {
    width: 100%;
    padding: 14px 20px 14px 45px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: var(--card-bg);
}

.search-box input:focus {
    outline: none;
    border-color: var(--primary-green);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.search-box i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--muted);
    font-size: 1.2rem;
}

.filter-select {
    padding: 14px 20px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    background: var(--card-bg);
    color: var(--text-color);
    font-size: 1rem;
    min-width: 180px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-select:focus {
    outline: none;
    border-color: var(--primary-green);
}

.requests-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
}

.request-card {
    background: var(--card-bg);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--shadow-md);
    border: 1px solid rgba(15,23,36,0.05);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
}

.request-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 25px 50px rgba(15,23,36,0.15);
}

.card-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    z-index: 2;
}

.badge-active {
    background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
    color: white;
}

.badge-urgent {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.card-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-bottom: 1px solid #e5e7eb;
}

.card-content {
    padding: 25px;
}

.card-header {
    margin-bottom: 20px;
}

.product-name {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--text-color);
    margin-bottom: 10px;
    line-height: 1.3;
}

.product-description {
    color: var(--muted);
    font-size: 1rem;
    line-height: 1.5;
    margin-bottom: 15px;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-bottom: 25px;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: rgba(16, 185, 129, 0.05);
    border-radius: 10px;
    transition: all 0.3s ease;
}

.detail-item:hover {
    background: rgba(16, 185, 129, 0.1);
    transform: translateX(5px);
}

.detail-icon {
    width: 36px;
    height: 36px;
    background: var(--primary-green);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}

.detail-text h4 {
    font-size: 0.85rem;
    color: var(--muted);
    margin: 0;
    font-weight: 500;
}

.detail-text p {
    font-size: 1.1rem;
    color: var(--text-color);
    margin: 4px 0 0;
    font-weight: 600;
}

.date-warning {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 15px;
    background: rgba(245, 158, 11, 0.1);
    border-radius: 10px;
    margin-bottom: 20px;
    color: var(--accent-amber);
    font-weight: 500;
}

.date-warning i {
    font-size: 1.2rem;
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.requested-date {
    font-size: 0.9rem;
    color: var(--muted);
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-view-details {
    background: linear-gradient(135deg, var(--blue), var(--purple));
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-view-details:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2);
}

.no-requests {
    text-align: center;
    padding: 80px 20px;
    grid-column: 1 / -1;
}

.no-requests-icon {
    font-size: 5rem;
    color: var(--muted);
    margin-bottom: 25px;
    opacity: 0.3;
}

.no-requests h3 {
    color: var(--text-color);
    font-size: 2rem;
    margin-bottom: 15px;
}

.no-requests p {
    color: var(--muted);
    font-size: 1.2rem;
    margin-bottom: 30px;
}

.loading {
    text-align: center;
    padding: 60px;
    grid-column: 1 / -1;
}

.spinner {
    width: 50px;
    height: 50px;
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

@media (max-width: 1200px) {
    .requests-container {
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    }
}

@media (max-width: 992px) {
    .requests-wrapper {
        padding: 15px;
    }

    .requests-header h2 {
        font-size: 2rem;
    }

    .search-filters {
        flex-direction: column;
        align-items: stretch;
    }

    .search-box {
        max-width: 100%;
    }

    .filter-select {
        width: 100%;
    }
}

@media (max-width: 768px) {
    .requests-container {
        grid-template-columns: 1fr;
    }

    .details-grid {
        grid-template-columns: 1fr;
    }

    .card-footer {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }

    .btn-view-details {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .requests-wrapper {
        padding: 10px;
    }

    .requests-header h2 {
        font-size: 1.8rem;
    }

    .card-content {
        padding: 20px;
    }

    .product-name {
        font-size: 1.4rem;
    }
}
</style>
@endsection

@section('content')
<div class="requests-wrapper">
    <div class="requests-header">
        <h2>Buyer Product Requests</h2>
        <p>Browse product requests from buyers and find opportunities to fulfill their needs</p>
    </div>

    <div class="search-filters">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search products...">
        </div>

        <select class="filter-select" id="statusFilter">
            <option value="all">All Status</option>
            <option value="active">Active</option>
            <option value="urgent">Urgent (within 3 days)</option>
        </select>

        <select class="filter-select" id="sortFilter">
            <option value="newest">Newest First</option>
            <option value="nearest_date">Nearest Date First</option>
            <option value="highest_quantity">Highest Quantity</option>
        </select>
    </div>

    <div class="requests-container" id="requestsContainer">
        @if($requests->isEmpty())
        <div class="no-requests">
            <div class="no-requests-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h3>No Requests Available</h3>
            <p>There are currently no product requests from buyers. Check back later!</p>
        </div>
        @else
        @foreach($requests as $request)
        @php
            $daysLeft = \Carbon\Carbon::parse($request->needed_date)->diffInDays(now());
            $isUrgent = $daysLeft <= 3;
            $buyerInfo = DB::table('buyers')->where('id', $request->buyer_id)->first();
        @endphp

        <div class="request-card"
             data-product="{{ strtolower($request->product_name) }}"
             data-status="{{ $isUrgent ? 'urgent' : 'active' }}"
             data-date="{{ $request->needed_date }}"
             data-quantity="{{ $request->needed_quantity }}">
            <div class="card-badge {{ $isUrgent ? 'badge-urgent' : 'badge-active' }}">
                {{ $isUrgent ? 'Urgent' : 'Active' }}
            </div>

            @if($request->product_image)
            <img src="{{ asset('uploads/buyer_product_requests/' . $request->product_image) }}"
                 alt="{{ $request->product_name }}"
                 class="card-image"
                 onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
            @else
            <div class="card-image" style="background: linear-gradient(135deg, #f6f8fa, #e5e7eb);
                 display: flex; align-items: center; justify-content: center; color: var(--muted);">
                <i class="fas fa-image" style="font-size: 3rem;"></i>
            </div>
            @endif

            <div class="card-content">
                <div class="card-header">
                    <h3 class="product-name">{{ $request->product_name }}</h3>
                    @if($request->description)
                    <p class="product-description">{{ Str::limit($request->description, 100) }}</p>
                    @endif
                </div>

                <div class="details-grid">
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <div class="detail-text">
                            <h4>Quantity Needed</h4>
                            <p>{{ number_format($request->needed_quantity, 2) }} {{ $request->unit_of_measure }}</p>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="detail-text">
                            <h4>Needed By</h4>
                            <p>{{ \Carbon\Carbon::parse($request->needed_date)->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div class="detail-text">
                            <h4>Expected Price</h4>
                            <p>
                                @if($request->unit_price)
                                Rs. {{ number_format($request->unit_price, 2) }}
                                @else
                                Negotiable
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="detail-text">
                            <h4>Buyer Type</h4>
                            <p>{{ ucfirst($buyerInfo->business_type ?? 'Individual') }}</p>
                        </div>
                    </div>
                </div>

                @if($isUrgent)
                <div class="date-warning">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Urgent: Needed in {{ $daysLeft }} day{{ $daysLeft != 1 ? 's' : '' }}</span>
                </div>
                @endif

                <div class="card-footer">
                    <div class="requested-date">
                        <i class="fas fa-clock"></i>
                        <span>Requested {{ \Carbon\Carbon::parse($request->created_at)->diffForHumans() }}</span>
                    </div>

                    <button class="btn-view-details" onclick="viewRequestDetails({{ $request->id }})">
                        <i class="fas fa-eye"></i>
                        View Details
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
async function viewRequestDetails(requestId) {
    try {
        const response = await fetch(`/farmer/product-requests/${requestId}/details`);
        const data = await response.json();

        if (data.success) {
            const request = data.request;
            const buyer = data.buyer;

            const daysLeft = Math.ceil((new Date(request.needed_date) - new Date()) / (1000 * 60 * 60 * 24));
            const isUrgent = daysLeft <= 3;

            let imageHtml = '<div style="text-align: center; padding: 20px; background: #f6f8fa; border-radius: 10px; margin: 15px 0;">' +
                          '<i class="fas fa-image" style="font-size: 4rem; color: #6b7280;"></i>' +
                          '<p style="margin-top: 10px; color: #6b7280;">No image provided</p></div>';

            if (request.product_image) {
                imageHtml = `<img src="${request.image_url}" alt="${request.product_name}" style="width: 100%; max-height: 300px; object-fit: cover; border-radius: 10px; margin: 15px 0;">`;
            }

            const buyerInfo = buyer ? `
                <div style="background: rgba(16, 185, 129, 0.05); padding: 15px; border-radius: 10px; margin: 15px 0;">
                    <h4 style="margin: 0 0 10px 0; color: #10B981; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-user"></i> Buyer Information
                    </h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div>
                            <small style="color: #6b7280;">Name</small>
                            <p style="margin: 5px 0; font-weight: 600;">${buyer.name}</p>
                        </div>
                        <div>
                            <small style="color: #6b7280;">Business Type</small>
                            <p style="margin: 5px 0; font-weight: 600;">${buyer.business_type ? buyer.business_type.charAt(0).toUpperCase() + buyer.business_type.slice(1) : 'Individual'}</p>
                        </div>
                        ${buyer.primary_mobile ? `<div>
                            <small style="color: #6b7280;">Contact</small>
                            <p style="margin: 5px 0; font-weight: 600;">${buyer.primary_mobile}</p>
                        </div>` : ''}
                        ${buyer.residential_address ? `<div style="grid-column: 1 / -1;">
                            <small style="color: #6b7280;">Address</small>
                            <p style="margin: 5px 0; font-weight: 600;">${buyer.residential_address}</p>
                        </div>` : ''}
                    </div>
                </div>
            ` : '<p style="color: #6b7280; font-style: italic;">Buyer information not available</p>';

            Swal.fire({
                title: request.product_name,
                html: `
                    <div style="text-align: left;">
                        ${imageHtml}

                        <div style="margin: 20px 0;">
                            ${request.description ? `<p style="color: #4b5563; line-height: 1.6; background: #f9fafb; padding: 15px; border-radius: 8px; border-left: 4px solid #10B981;">${request.description}</p>` : ''}
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin: 20px 0;">
                            <div style="background: white; padding: 15px; border-radius: 10px; border: 1px solid #e5e7eb;">
                                <small style="color: #6b7280; display: block;">Quantity Needed</small>
                                <p style="margin: 5px 0; font-size: 1.2rem; font-weight: 700; color: #0f1724;">
                                    ${parseFloat(request.needed_quantity).toFixed(2)} ${request.unit_of_measure}
                                </p>
                            </div>

                            <div style="background: white; padding: 15px; border-radius: 10px; border: 1px solid #e5e7eb;">
                                <small style="color: #6b7280; display: block;">Needed By</small>
                                <p style="margin: 5px 0; font-size: 1.2rem; font-weight: 700; color: ${isUrgent ? '#ef4444' : '#0f1724'};">
                                    ${new Date(request.needed_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                                    ${isUrgent ? `<br><small style="color: #ef4444; font-size: 0.8rem;">(${daysLeft} day${daysLeft !== 1 ? 's' : ''} left)</small>` : ''}
                                </p>
                            </div>

                            <div style="background: white; padding: 15px; border-radius: 10px; border: 1px solid #e5e7eb;">
                                <small style="color: #6b7280; display: block;">Expected Price</small>
                                <p style="margin: 5px 0; font-size: 1.2rem; font-weight: 700; color: #0f1724;">
                                    ${request.unit_price ? `Rs. ${parseFloat(request.unit_price).toFixed(2)}/${request.unit_of_measure}` : 'Negotiable'}
                                </p>
                            </div>

                            <div style="background: white; padding: 15px; border-radius: 10px; border: 1px solid #e5e7eb;">
                                <small style="color: #6b7280; display: block;">Requested On</small>
                                <p style="margin: 5px 0; font-size: 1.2rem; font-weight: 700; color: #0f1724;">
                                    ${new Date(request.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                                </p>
                            </div>
                        </div>

                        ${buyerInfo}
                    </div>
                `,
                width: '800px',
                padding: '30px',
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    popup: 'animated zoomIn',
                    title: 'request-modal-title'
                }
            });

        } else {
            throw new Error(data.message || 'Failed to load request details');
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

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const sortFilter = document.getElementById('sortFilter');
    const requestCards = document.querySelectorAll('.request-card');

    function filterRequests() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const sortValue = sortFilter.value;

        let visibleCards = Array.from(requestCards);

        visibleCards = visibleCards.filter(card => {
            const productName = card.getAttribute('data-product');
            const status = card.getAttribute('data-status');
            const matchesSearch = productName.includes(searchTerm);
            const matchesStatus = statusValue === 'all' ||
                                 (statusValue === 'urgent' && status === 'urgent') ||
                                 (statusValue === 'active' && status === 'active');

            return matchesSearch && matchesStatus;
        });

        visibleCards.sort((a, b) => {
            if (sortValue === 'newest') {
                return 0;
            } else if (sortValue === 'nearest_date') {
                const dateA = new Date(a.getAttribute('data-date'));
                const dateB = new Date(b.getAttribute('data-date'));
                return dateA - dateB;
            } else if (sortValue === 'highest_quantity') {
                const qtyA = parseFloat(a.getAttribute('data-quantity'));
                const qtyB = parseFloat(b.getAttribute('data-quantity'));
                return qtyB - qtyA;
            }
            return 0;
        });

        requestCards.forEach(card => {
            card.style.display = 'none';
            card.style.order = '0';
        });

        visibleCards.forEach((card, index) => {
            card.style.display = 'block';
            card.style.order = index;
        });

        if (visibleCards.length === 0) {
            const container = document.getElementById('requestsContainer');
            const noResults = document.createElement('div');
            noResults.className = 'no-requests';
            noResults.innerHTML = `
                <div class="no-requests-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>No Matching Requests</h3>
                <p>Try adjusting your search criteria</p>
            `;

            if (!document.querySelector('.no-requests')) {
                container.appendChild(noResults);
            }
        } else {
            const noResults = document.querySelector('.no-requests:not(.permanent)');
            if (noResults) {
                noResults.remove();
            }
        }
    }

    searchInput.addEventListener('input', filterRequests);
    statusFilter.addEventListener('change', filterRequests);
    sortFilter.addEventListener('change', filterRequests);

    requestCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.zIndex = '10';
        });

        card.addEventListener('mouseleave', function() {
            this.style.zIndex = '1';
        });
    });
});
</script>
@endsection
