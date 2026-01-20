@extends('farmer.layouts.farmer_master')

@section('title', 'My Products')
@section('page-title', 'My Products')

@section('styles')
<link href="{{ asset('css/farmer/my-products.blade.css') }}" rel="stylesheet">
@endsection


@section('content')
<div class="products-container">
    <div class="page-header">
        <div class="header-content">
            <h2>My Products</h2>
            <p>Manage your farm products</p>
        </div>
        <div class="header-actions">
            <button class="btn-add" id="addProductBtn">
                <i class="fas fa-plus"></i> Add Product
            </button>
            <a href="{{ route('farmer.products.removed') }}" class="btn-removed">
                <i class="fas fa-trash-restore"></i> Removed Products
            </a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="stat-details">
                <h3>{{ $productCount ?? 0 }}</h3>
                <p>Total Products</p>
            </div>
        </div>
        <div class="stat-box">
            <div class="stat-icon available">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-details">
                <h3>{{ $availableCount ?? 0 }}</h3>
                <p>Available</p>
            </div>
        </div>
        <div class="stat-box">
            <div class="stat-icon sold">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-details">
                <h3>{{ $soldCount ?? 0 }}</h3>
                <p>Sold Out</p>
            </div>
        </div>
        <div class="stat-box">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-details">
                <h3>{{ $waitingCount ?? 0 }}</h3>
                <p>Waiting</p>
            </div>
        </div>
    </div>

    <div class="controls-section">
        <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search products...">
        </div>
        <div class="filter-container">
            <select id="statusFilter">
                <option value="all" {{ ($filter ?? 'all') == 'all' ? 'selected' : '' }}>All Products</option>
                <option value="available" {{ ($filter ?? '') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="sold" {{ ($filter ?? '') == 'sold' ? 'selected' : '' }}>Sold Out</option>
                <option value="waiting" {{ ($filter ?? '') == 'waiting' ? 'selected' : '' }}>Waiting for Cultivation/Preparation</option>
            </select>
        </div>
    </div>

    @if($products->count() > 0)
    <div class="products-grid" id="productsGrid">
        @foreach($products as $product)
        @php
            $currentDate = now()->format('Y-m-d');
            $expectedDate = $product->expected_availability_date;

            // Calculate total ordered quantity
            $totalOrdered = \App\Models\OrderItem::where('product_id', $product->id)
                ->whereHas('order', function($q) {
                    $q->whereNotIn('order_status', ['cancelled', 'refunded']);
                })
                ->sum('quantity_ordered');

            // Calculate available quantity
            $availableQuantity = $product->quantity - $totalOrdered;

            // Determine product status
            if ($product->product_status === 'removed') {
                $status = 'removed';
                $displayStatus = 'removed';
                $isWaiting = false;
            } elseif ($expectedDate > $currentDate) {
                // Future date = waiting
                $status = 'waiting';
                $displayStatus = 'waiting';
                $isWaiting = true;
            } elseif ($availableQuantity > 0 && $expectedDate <= $currentDate) {
                // Available quantity > 0 and date is today or earlier = available
                $status = 'available';
                $displayStatus = 'available';
                $isWaiting = false;
            } else {
                // Available quantity <= 0 and date is today or earlier = sold
                $status = 'sold';
                $displayStatus = 'sold';
                $isWaiting = false;
            }
        @endphp

        <div class="product-card"
             data-status="{{ $status }}"
             data-id="{{ $product->id }}">
            <div class="product-image">
                @if($product->product_photo)
                <img src="{{ asset('uploads/product_images/' . $product->product_photo) }}"
                     alt="{{ $product->product_name }}"
                     onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
                @else
                <div class="image-placeholder">
                    <i class="fas fa-seedling"></i>
                </div>
                @endif

                @if($displayStatus === 'removed')
                <span class="status-badge removed">
                    Removed
                </span>
                @elseif($displayStatus === 'waiting')
                <span class="status-badge waiting">
                    Waiting
                </span>
                @else
                <span class="status-badge {{ $displayStatus === 'available' ? 'available' : 'sold' }}">
                    {{ $displayStatus === 'available' ? 'Available' : 'Sold Out' }}
                </span>
                @endif
            </div>
            <div class="product-content">
                <h4 class="product-name">{{ $product->product_name }}</h4>
                <p class="product-category">
                    {{ $product->category->category_name ?? 'No Category' }} /
                    {{ $product->subcategory->subcategory_name ?? 'No Subcategory' }}
                </p>
                <div class="product-details">
                    <div class="detail-item">
                        <i class="fas fa-weight"></i>
                        <span>Stock: {{ number_format($product->quantity, 2) }} {{ $product->unit_of_measure }}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Ordered: {{ number_format($totalOrdered, 2) }} {{ $product->unit_of_measure }}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-box-open"></i>
                        <span>Available: {{ number_format($availableQuantity, 2) }} {{ $product->unit_of_measure }}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-tag"></i>
                        <span>LKR {{ number_format($product->selling_price, 2) }}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-calendar"></i>
                        <span>{{ \Carbon\Carbon::parse($product->expected_availability_date)->format('d/m/Y') }}</span>
                    </div>
                </div>
                @if($product->product_status === 'removed')
                <div class="removed-notice">
                    <i class="fas fa-info-circle"></i>
                    <span>Removed by: {{ ucfirst(str_replace('_', ' ', $product->removed_by ?? 'system')) }}</span>
                </div>
                @elseif($isWaiting)
                <div class="waiting-notice">
                    <i class="fas fa-clock"></i>
                    <span>Available from: {{ \Carbon\Carbon::parse($product->expected_availability_date)->format('d/m/Y') }}</span>
                </div>
                @endif
            </div>
            <div class="product-actions">
                <button class="btn-view" data-id="{{ $product->id }}">
                    <i class="fas fa-eye"></i>
                    <span>View</span>
                </button>
                @if($product->product_status !== 'removed')
                <button class="btn-contact" onclick="showLeadFarmerContact()">
                    <i class="fas fa-headset"></i>
                    <span>Contact for Changes</span>
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="pagination-wrapper">
        <div class="pagination-info">
            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
        </div>
        <div class="pagination">
            @if($products->onFirstPage())
            <span class="page-link disabled">
                <i class="fas fa-angle-double-left"></i>
            </span>
            <span class="page-link disabled">
                <i class="fas fa-chevron-left"></i>
            </span>
            @else
            <a href="{{ $products->url(1) . ($filter ? '?filter=' . $filter : '') }}" class="page-link">
                <i class="fas fa-angle-double-left"></i>
            </a>
            <a href="{{ $products->previousPageUrl() . ($filter ? '&filter=' . $filter : '') }}" class="page-link">
                <i class="fas fa-chevron-left"></i>
            </a>
            @endif

            @php
            $current = $products->currentPage();
            $last = $products->lastPage();
            $start = max(1, $current - 2);
            $end = min($last, $current + 2);
            @endphp

            @if($start > 1)
            <a href="{{ $products->url(1) . ($filter ? '?filter=' . $filter : '') }}" class="page-link">1</a>
            @if($start > 2)<span class="page-dots">...</span>@endif
            @endif

            @for($i = $start; $i <= $end; $i++)
                @if($i == $current)
                <span class="page-link active">{{ $i }}</span>
                @else
                <a href="{{ $products->url($i) . ($filter ? '?filter=' . $filter : '') }}" class="page-link">{{ $i }}</a>
                @endif
            @endfor

            @if($end < $last)
            @if($end < $last - 1)<span class="page-dots">...</span>@endif
            <a href="{{ $products->url($last) . ($filter ? '?filter=' . $filter : '') }}" class="page-link">{{ $last }}</a>
            @endif

            @if($products->hasMorePages())
            <a href="{{ $products->nextPageUrl() . ($filter ? '&filter=' . $filter : '') }}" class="page-link">
                <i class="fas fa-chevron-right"></i>
            </a>
            <a href="{{ $products->url($last) . ($filter ? '?filter=' . $filter : '') }}" class="page-link">
                <i class="fas fa-angle-double-right"></i>
            </a>
            @else
            <span class="page-link disabled">
                <i class="fas fa-chevron-right"></i>
            </span>
            <span class="page-link disabled">
                <i class="fas fa-angle-double-right"></i>
            </span>
            @endif
        </div>
    </div>
    @else
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-seedling"></i>
        </div>
        <h3>No Products Found</h3>
        <p>No products found for the selected filter.</p>
        <button class="btn-primary" onclick="window.location.href='{{ route('farmer.products.my-products') }}'">
            <i class="fas fa-redo"></i> View All Products
        </button>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewButtons = document.querySelectorAll('.btn-view');
    const searchInput = document.getElementById('searchInput');
    const filterSelect = document.getElementById('statusFilter');
    const productCards = document.querySelectorAll('.product-card');
    const addProductBtn = document.getElementById('addProductBtn');

    const leadFarmer = @json($leadFarmer ?? null);

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
            confirmButtonColor: '#10B981',
            timer: 2000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonColor: '#EF4444'
        });
    @endif

    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            viewProduct(productId);
        });
    });

    if (addProductBtn) {
        addProductBtn.addEventListener('click', function() {
            showAddProductInfo();
        });
    }

    const contactButtons = document.querySelectorAll('.btn-contact');
    contactButtons.forEach(button => {
        button.addEventListener('click', function() {
            showLeadFarmerContact();
        });
    });

    // Filter by status using select change (server-side)
    filterSelect.addEventListener('change', function() {
        const filter = this.value;
        window.location.href = '{{ route("farmer.products.my-products") }}?filter=' + filter;
    });

    // Client-side search
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        filterProducts(searchTerm);
    });

    async function viewProduct(productId) {
        try {
            Swal.fire({
                title: 'Loading...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const response = await fetch(`/farmer/products/view/${productId}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            Swal.close();

            if (data.success && data.product) {
                const product = data.product;
                const leadFarmer = data.lead_farmer;

                let htmlContent = `
                    <div style="text-align: left; max-height: 500px; overflow-y: auto;">
                        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                            <div style="flex-shrink: 0;">
                                ${product.product_photo ?
                                    `<img src="/uploads/product_images/${product.product_photo}"
                                          alt="${product.product_name}"
                                          style="width: 140px; height: 140px; object-fit: cover; border-radius: 10px;"
                                          onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">` :
                                    `<div style="width: 140px; height: 140px; background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
                                      border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-seedling" style="font-size: 40px; color: #10B981;"></i>
                                    </div>`
                                }
                                <div style="margin-top: 10px; text-align: center;">
                                    <span style="padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
                                          background: ${product.is_available ? '#10B981' : '#EF4444'}; color: white;">
                                        ${product.is_available ? 'Available' : 'Sold Out'}
                                    </span>
                                </div>
                            </div>
                            <div style="flex: 1;">
                                <h3 style="margin: 0 0 10px 0; color: #0f1724; font-size: 20px;">${product.product_name}</h3>
                                <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
                                    ${product.category?.category_name || 'No Category'} / ${product.subcategory?.subcategory_name || 'No Subcategory'}
                                </p>
                                <p style="margin: 0 0 15px 0; color: #374151; font-size: 15px; line-height: 1.5;">
                                    ${product.product_description || 'No description available'}
                                </p>
                            </div>
                        </div>

                        <div style="background: #f8fafc; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                            <h4 style="margin: 0 0 15px 0; font-size: 16px; color: #0f1724; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-info-circle" style="color: #10B981;"></i> Product Details
                            </h4>
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                                <div>
                                    <span style="font-size: 14px; color: #6b7280; display: block; margin-bottom: 5px;">Quantity:</span>
                                    <p style="margin: 0; font-weight: 600; color: #0f1724; font-size: 16px;">
                                        ${product.quantity} ${product.unit_of_measure}
                                    </p>
                                </div>
                                <div>
                                    <span style="font-size: 14px; color: #6b7280; display: block; margin-bottom: 5px;">Price:</span>
                                    <p style="margin: 0; font-weight: 600; color: #0f1724; font-size: 16px;">
                                        LKR ${parseFloat(product.selling_price).toFixed(2)}
                                    </p>
                                </div>
                                <div>
                                    <span style="font-size: 14px; color: #6b7280; display: block; margin-bottom: 5px;">Quality Grade:</span>
                                    <p style="margin: 0; font-weight: 600; color: #0f1724; font-size: 16px;">
                                        ${product.quality_grade || 'Not specified'}
                                    </p>
                                </div>
                                <div>
                                    <span style="font-size: 14px; color: #6b7280; display: block; margin-bottom: 5px;">Available Date:</span>
                                    <p style="margin: 0; font-weight: 600; color: #0f1724; font-size: 16px;">
                                        ${new Date(product.expected_availability_date).toLocaleDateString('en-GB')}
                                    </p>
                                </div>
                            </div>
                        </div>`;

                if (leadFarmer) {
                    htmlContent += `
                        <div style="background: #fef3c7; padding: 20px; border-radius: 10px; border-left: 4px solid #f59e0b;">
                            <h4 style="margin: 0 0 10px 0; font-size: 16px; color: #92400e; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-exclamation-circle"></i> Need Changes?
                            </h4>
                            <p style="margin: 0 0 15px 0; color: #92400e; font-size: 14px; line-height: 1.5;">
                                If any changes or alterations are needed, please contact your Lead Farmer.
                            </p>
                            <div style="background: white; padding: 15px; border-radius: 8px;">
                                <h5 style="margin: 0 0 10px 0; font-size: 15px; color: #0f1724;">Lead Farmer Contact Details</h5>
                                <div style="display: flex; flex-direction: column; gap: 8px; font-size: 14px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <i class="fas fa-user" style="color: #10B981; width: 16px;"></i>
                                        <span><strong>Name:</strong> ${leadFarmer.name}</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <i class="fas fa-phone" style="color: #10B981; width: 16px;"></i>
                                        <span><strong>Phone:</strong> ${leadFarmer.primary_mobile}</span>
                                    </div>
                                    ${leadFarmer.email ? `
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <i class="fas fa-envelope" style="color: #10B981; width: 16px;"></i>
                                        <span><strong>Email:</strong> ${leadFarmer.email}</span>
                                    </div>` : ''}
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <i class="fas fa-users" style="color: #10B981; width: 16px;"></i>
                                        <span><strong>Group:</strong> ${leadFarmer.group_name}</span>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                }

                htmlContent += `</div>`;

                Swal.fire({
                    title: 'Product Details',
                    html: htmlContent,
                    width: 650,
                    padding: '25px',
                    showCloseButton: true,
                    confirmButtonText: 'Close',
                    confirmButtonColor: '#10B981',
                    customClass: {
                        popup: 'product-details-modal'
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to load product details.',
                    confirmButtonColor: '#10B981',
                });
            }
        } catch (error) {
            console.error('Error viewing product:', error);
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong. Please try again.',
                confirmButtonColor: '#10B981',
            });
        }
    }

    function showAddProductInfo() {
        if (leadFarmer) {
            Swal.fire({
                title: 'Add New Product',
                html: `
                    <div style="text-align: left;">
                        <p style="margin-bottom: 15px; color: #6b7280; font-size: 15px; line-height: 1.5;">
                            <strong>Contact your Lead Farmer to add new products:</strong>
                        </p>
                        <div style="background: #f8fafc; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                            <h4 style="margin: 0 0 15px 0; color: #0f1724; font-size: 18px;">${leadFarmer.name}</h4>
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fas fa-phone" style="color: #10B981; font-size: 16px;"></i>
                                    <div>
                                        <div style="font-weight: 600; color: #374151; font-size: 14px;">Primary Phone</div>
                                        <div style="color: #6b7280; font-size: 15px;">${leadFarmer.primary_mobile}</div>
                                    </div>
                                </div>
                                ${leadFarmer.whatsapp_number ? `
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fab fa-whatsapp" style="color: #25D366; font-size: 16px;"></i>
                                    <div>
                                        <div style="font-weight: 600; color: #374151; font-size: 14px;">WhatsApp</div>
                                        <div style="color: #6b7280; font-size: 15px;">${leadFarmer.whatsapp_number}</div>
                                    </div>
                                </div>` : ''}
                                ${leadFarmer.email ? `
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fas fa-envelope" style="color: #EF4444; font-size: 16px;"></i>
                                    <div>
                                        <div style="font-weight: 600; color: #374151; font-size: 14px;">Email</div>
                                        <div style="color: #6b7280; font-size: 15px;">${leadFarmer.email}</div>
                                    </div>
                                </div>` : ''}
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fas fa-users" style="color: #F59E0B; font-size: 16px;"></i>
                                    <div>
                                        <div style="font-weight: 600; color: #374151; font-size: 14px;">Group</div>
                                        <div style="color: #6b7280; font-size: 15px;">${leadFarmer.group_name}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="background: #f0f9ff; padding: 15px; border-radius: 8px; border-left: 4px solid #3b82f6;">
                            <p style="margin: 0; color: #0369a1; font-size: 14px; line-height: 1.5;">
                                <i class="fas fa-lightbulb" style="margin-right: 8px;"></i>
                                Have your product details ready when contacting your Lead Farmer for faster processing.
                            </p>
                        </div>
                    </div>
                `,
                width: 550,
                confirmButtonText: 'Got it',
                confirmButtonColor: '#10B981',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                cancelButtonColor: '#6b7280'
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'No Lead Farmer Assigned',
                html: `
                    <div style="text-align: left;">
                        <p style="margin-bottom: 15px; color: #6b7280; font-size: 15px;">
                            You are not currently assigned to a Lead Farmer.
                        </p>
                        <p style="color: #374151; font-size: 14px;">
                            Please contact system administrator or support team for assistance.
                        </p>
                    </div>
                `,
                confirmButtonColor: '#10B981',
            });
        }
    }

    function showLeadFarmerContact() {
        if (leadFarmer) {
            Swal.fire({
                title: 'Contact Lead Farmer',
                html: `
                    <div style="text-align: left;">
                        <p style="margin-bottom: 15px; color: #6b7280; font-size: 15px; line-height: 1.5;">
                            For any product changes, deletions, or modifications, please contact:
                        </p>
                        <div style="background: #f8fafc; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                            <h4 style="margin: 0 0 15px 0; color: #0f1724; font-size: 18px;">${leadFarmer.name}</h4>
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fas fa-phone" style="color: #10B981; font-size: 16px;"></i>
                                    <div>
                                        <div style="font-weight: 600; color: #374151; font-size: 14px;">Primary Phone</div>
                                        <div style="color: #6b7280; font-size: 15px;">${leadFarmer.primary_mobile}</div>
                                    </div>
                                </div>
                                ${leadFarmer.whatsapp_number ? `
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fab fa-whatsapp" style="color: #25D366; font-size: 16px;"></i>
                                    <div>
                                        <div style="font-weight: 600; color: #374151; font-size: 14px;">WhatsApp</div>
                                        <div style="color: #6b7280; font-size: 15px;">${leadFarmer.whatsapp_number}</div>
                                    </div>
                                </div>` : ''}
                                ${leadFarmer.email ? `
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fas fa-envelope" style="color: #EF4444; font-size: 16px;"></i>
                                    <div>
                                        <div style="font-weight: 600; color: #374151; font-size: 14px;">Email</div>
                                        <div style="color: #6b7280; font-size: 15px;">${leadFarmer.email}</div>
                                    </div>
                                </div>` : ''}
                            </div>
                        </div>
                        <div style="background: #fef3c7; padding: 15px; border-radius: 8px;">
                            <p style="margin: 0; color: #92400e; font-size: 14px; line-height: 1.5;">
                                <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
                                Please have your product details ready when contacting for modifications.
                            </p>
                        </div>
                    </div>
                `,
                width: 500,
                confirmButtonText: 'Close',
                confirmButtonColor: '#10B981',
                showCloseButton: true
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'No Lead Farmer Assigned',
                text: 'You are not currently assigned to a Lead Farmer. Please contact support for assistance.',
                confirmButtonColor: '#10B981',
            });
        }
    }

    function filterProducts(searchTerm) {
        productCards.forEach(card => {
            const productName = card.querySelector('.product-name').textContent.toLowerCase();
            const productCategory = card.querySelector('.product-category').textContent.toLowerCase();
            const productDetails = card.querySelectorAll('.detail-item span');
            let detailsText = '';
            productDetails.forEach(detail => {
                detailsText += detail.textContent.toLowerCase() + ' ';
            });

            const matchesSearch = productName.includes(searchTerm) ||
                                productCategory.includes(searchTerm) ||
                                detailsText.includes(searchTerm);

            if (matchesSearch || searchTerm === '') {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    if (typeof gsap !== 'undefined') {
        gsap.from('.stat-box', {
            duration: 0.6,
            y: 20,
            opacity: 0,
            stagger: 0.1,
            ease: 'power2.out',
            delay: 0.3
        });

        gsap.from('.product-card', {
            duration: 0.5,
            y: 20,
            opacity: 0,
            stagger: 0.05,
            ease: 'power2.out',
            delay: 0.5
        });
    }
});
</script>
@endsection

