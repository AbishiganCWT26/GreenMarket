@extends('buyer.layouts.buyer_master')

@section('title', 'My Wishlist')
@section('page-title', 'Wishlist')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/buyer/wishlist.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')

<div class="wishlist-container">
    <div class="wishlist-header-section">
        <h1 class="wishlist-main-title">
            <i class="fas fa-heart"></i> My Wishlist
            <span class="item-count-badge">{{ count($wishlistItems) }} items</span>
        </h1>
        <p class="wishlist-subtitle">Your saved garden favorites ready for purchase</p>
    </div>

    @if($wishlistItems->count() > 0)
        <div class="wishlist-grid">
            @foreach($wishlistItems as $item)
            <div class="wishlist-item-card">
                <div class="item-card-header">
                    <h3 class="item-card-title">{{ $item->product_name }}</h3>
                    @if($item->quality_grade)
                    <span class="item-priority {{ $item->quality_grade == 'premium' ? 'high' : 'medium' }}">
                        {{ ucfirst(str_replace('_', ' ', $item->quality_grade)) }}
                    </span>
                    @endif
                </div>

                <div class="item-card-body">
                    <div class="item-image">
                        @php
                            $productImageName = $item->product_photo;
                            $imagePath = public_path('uploads/product_images/' . $productImageName);
                            if ($productImageName && file_exists($imagePath)) {
                                $imageSrc = asset('uploads/product_images/' . $productImageName);
                            } else {
                                $imageSrc = asset('assets/images/product-placeholder.png');
                            }
                        @endphp
                        <img src="{{ $imageSrc }}" alt="{{ $item->product_name }}">
                    </div>

                    <div class="item-details">
                        <h3 class="item-name">{{ $item->product_name }}</h3>
                        @if($item->product_description)
                        <p class="item-description">{{ Str::limit($item->product_description, 100) }}</p>
                        @endif

                        <div class="item-meta">
                            <div class="meta-item">
                                <i class="fas fa-user"></i>
                                <span>Farmer:</span>
                                <span class="meta-value">{{ $item->farmer_name ?? 'Local Farmer' }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-weight-scale"></i>
                                <span>Quantity:</span>
                                <span class="meta-value">{{ number_format($item->quantity, 2) }} {{ $item->unit_of_measure }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Location:</span>
                                <span class="meta-value">{{ $item->district ?? 'Colombo' }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-landmark"></i>
                                <span>Grama Niladhari Division:</span>
                                <span class="meta-value">{{ ucfirst($item->grama_niladhari_division ?? 'N/A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="item-card-footer">
                    <div class="item-price">Rs. {{ number_format($item->selling_price, 2) }}</div>
                    <div class="item-stock">
                        @if($item->is_available && $item->quantity > 0)
                        <span class="stock-badge stock-in">
                            <i class="fas fa-check-circle"></i> In Stock
                        </span>
                        <span class="stock-qty">{{ number_format($item->quantity, 2) }} {{ $item->unit_of_measure }} left</span>
                        @else
                        <span class="stock-badge stock-out">
                            <i class="fas fa-times-circle"></i> Out of Stock
                        </span>
                        @endif
                    </div>
                    <div class="item-actions">
                        <a href="{{ route('buyer.productDetail', $item->id) }}" class="btn btn-primary">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        <!-- FIX: Changed data-id to use wishlists.id -->
                        <button class="btn btn-outline btn-icon delete-trigger" data-id="{{ $item->wishlist_id ?? $item->id }}" data-product-name="{{ $item->product_name }}" title="Remove from Wishlist">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        <!-- FIX: Changed route parameter to wishlist ID -->
                        <form action="{{ route('buyer.removeFromWishlistById', $item->wishlist_id ?? $item->id) }}" method="POST" class="delete-form" id="delete-form-{{ $item->wishlist_id ?? $item->id }}">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="wishlist-footer">
            <a href="{{ route('buyer.browseProducts') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-bag"></i> Continue Shopping
            </a>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-heart-broken fa-4x"></i>
            </div>
            <h3>Your wishlist is empty</h3>
            <p>Explore our marketplace and save your favorite garden items here.</p>
            <a href="{{ route('buyer.browseProducts') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-search"></i> Browse Products
            </a>
        </div>
    @endif
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Show SweetAlert notification for session messages
    @if(session('success'))
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#d1fae5',
            color: '#065f46'
        });
    }
    @endif

    @if(session('error'))
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: "{{ session('error') }}",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#fee2e2',
            color: '#7f1d1d'
        });
    }
    @endif

    // Delete button functionality
    const deleteButtons = document.querySelectorAll('.delete-trigger');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const itemId = this.getAttribute('data-id');
            const productName = this.getAttribute('data-product-name') || 'this item';
            const form = document.getElementById(`delete-form-${itemId}`);

            if (!form) {
                console.error('Delete form not found for item:', itemId);
                Swal.fire({
                    title: 'Error',
                    text: 'Delete form not found. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
                return;
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Remove from wishlist?',
                    text: `Are you sure you want to remove "${productName}" from your wishlist?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, remove it',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        const originalHTML = button.innerHTML;
                        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                        button.disabled = true;

                        // Submit the form
                        form.submit();
                    }
                });
            } else {
                // Fallback to browser confirm if SweetAlert is not available
                if (confirm(`Are you sure you want to remove "${productName}" from your wishlist?`)) {
                    form.submit();
                }
            }
        });
    });
});
</script>

<style>
.delete-trigger:hover {
    background-color: #fee2e2;
    border-color: #ef4444;
    color: #ef4444;
}

.delete-trigger.loading {
    opacity: 0.7;
    cursor: not-allowed;
}
</style>
@endsection
