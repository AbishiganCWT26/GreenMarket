@extends('buyer.layouts.buyer_master')

@section('title', $product->product_name . ' | GreenMarket')
@section('page-title', 'Product Details')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/buyer/Product_Detail.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
@php
    // Image handling logic directly in the blade file
    function getProductImage($imageName) {
        if (!$imageName) {
            return asset('assets/images/product-placeholder.png');
        }

        // Check if image exists in the uploads/product_images directory
        $imagePath = 'uploads/product_images/' . $imageName;
        $fullPath = public_path($imagePath);

        if (file_exists($fullPath)) {
            return asset($imagePath);
        }

        // If image doesn't exist, use placeholder
        return asset('assets/images/product-placeholder.png');
    }

    // Get main product image
    $productMainImage = getProductImage($product->product_photo);

    // Process related products images
    $processedRelatedProducts = [];
    foreach ($relatedProducts as $related) {
        $related->display_image = getProductImage($related->product_photo);
        $processedRelatedProducts[] = $related;
    }
@endphp

<div class="product-detail-container">
    <div class="product-breadcrumb">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('buyer.dashboard') }}"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('buyer.browseProducts') }}">Browse Products</a></li>
                <li class="breadcrumb-item"><a href="#">{{ $product->category_name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->product_name }}</li>
            </ol>
        </nav>
    </div>

    <div class="product-main">
        <div class="product-gallery">
            <div class="main-image">
                <img src="{{ $productMainImage }}"
                    alt="{{ $product->product_name }}"
                    id="mainProductImage"
                    class="img-fluid animated"
                    onmouseenter="this.classList.add('animated')"
                    onmouseleave="this.classList.remove('animated')">
            </div>
            <div class="product-badge-container">
                @if($product->quantity <= 10)
                <span class="product-badge low-stock">Low Stock</span>
                @endif
                @if($product->quality_grade == 'grade_a')
                <span class="product-badge premium">Premium Grade</span>
                @elseif($product->quality_grade == 'organic')
                <span class="product-badge organic">Organic</span>
                @endif
            </div>
        </div>

        <div class="product-info">
            <h1 class="product-title">{{ $product->product_name }}</h1>

            <div class="product-category">
                <span class="badge category-badge">{{ $product->category_name }}</span>
                <span class="badge subcategory-badge">{{ $product->subcategory_name }}</span>
            </div>

            <div class="product-price-section">
                <div class="current-price">Rs. {{ number_format($product->selling_price, 2) }}</div>
                <div class="price-unit">per {{ $product->unit_of_measure }}</div>
            </div>

            <div class="product-rating">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <span class="rating-text">4.5 (24 reviews)</span>
            </div>

            <div class="product-stock">
                <div class="stock-info">
                    <i class="fas fa-box-open"></i>
                    <span class="stock-label">Available Stock:</span>
                    <span class="stock-value">{{ number_format($product->quantity, 2) }} {{ $product->unit_of_measure }}</span>
                </div>
                <div class="stock-bar">
                    <div class="stock-progress" style="width: {{ min(100, ($product->quantity/100)*100) }}%"></div>
                </div>
            </div>

            <div class="product-description">
                <h3><i class="fas fa-info-circle"></i> Product Description</h3>
                <p>{{ $product->product_description ?? 'No description available for this product.' }}</p>
            </div>

            <div class="product-quality">
                <h3><i class="fas fa-medal"></i> Quality Information</h3>
                <div class="quality-details">
                    <div class="quality-item">
                        <span class="quality-label">Grade:</span>
                        <span class="quality-value badge quality-{{ str_replace('_', '-', $product->quality_grade) }}">
                            {{ ucfirst(str_replace('_', ' ', $product->quality_grade)) }}
                        </span>
                    </div>
                    <div class="quality-item">
                        <span class="quality-label">Type:</span>
                        <span class="quality-value">{{ ucfirst($product->type_variant) }}</span>
                    </div>
                </div>
            </div>

            <div class="product-actions">
                <div class="quantity-selector">
                    <button class="qty-btn minus" type="button" id="minusBtn"><i class="fas fa-minus"></i></button>
                    <input type="number"
                           id="productQuantity"
                           value="1"
                           min="0.01"
                           step="0.01"
                           max="{{ $product->quantity }}"
                           data-max-quantity="{{ $product->quantity }}">
                    <button class="qty-btn plus" type="button" id="plusBtn"><i class="fas fa-plus"></i></button>
                </div>

                <div class="action-buttons">
                    <button class="btn btn-primary add-to-cart-btn"
                            data-product-id="{{ $product->id }}"
                            data-route="{{ route('buyer.addToCart', ':productId') }}"
                            data-login-route="{{ route('login') }}"
                            type="button"
                            id="addToCartBtn">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Add to Cart</span>
                    </button>

                    <button class="btn btn-outline-wishlist {{ $isInWishlist ? 'in-wishlist' : '' }}"
                            id="wishlistBtn"
                            data-product-id="{{ $product->id }}"
                            data-in-wishlist="{{ $isInWishlist ? 'true' : 'false' }}"
                            data-add-route="{{ route('buyer.addToWishlist') }}"
                            data-remove-route="{{ route('buyer.removeFromWishlist') }}"
                            type="button">
                        <i class="{{ $isInWishlist ? 'fas' : 'far' }} fa-heart"></i>
                        <span>{{ $isInWishlist ? 'In Wishlist' : 'Add to Wishlist' }}</span>
                    </button>
                </div>
            </div>

            <div class="product-meta">
                <div class="meta-item">
                    <i class="fas fa-shipping-fast"></i>
                    <span>Free pickup from farmer</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Quality guaranteed</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-undo"></i>
                    <span>Easy returns</span>
                </div>
            </div>
        </div>
    </div>

    <div class="product-details-tabs">
        <ul class="nav nav-tabs" id="productTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="farmer-tab" data-bs-toggle="tab" data-bs-target="#farmer" type="button" role="tab" aria-controls="farmer" aria-selected="true">
                    <i class="fas fa-user-tie"></i> Farmer Details
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button" role="tab" aria-controls="specs" aria-selected="false">
                    <i class="fas fa-clipboard-list"></i> Specifications
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pickup-tab" data-bs-toggle="tab" data-bs-target="#pickup" type="button" role="tab" aria-controls="pickup" aria-selected="false">
                    <i class="fas fa-map-marker-alt"></i> Pickup Information
                </button>
            </li>
        </ul>
        <div class="tab-content" id="productTabsContent">
            <div class="tab-pane fade show active" id="farmer" role="tabpanel" aria-labelledby="farmer-tab">
                <div class="farmer-profile">
                    <div class="farmer-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="farmer-info">
                        <h4>{{ $product->farmer_name }}</h4>
                        <div class="farmer-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ $product->district }} District</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-landmark"></i>
                                <span>{{ $product->grama_niladhari_division }}</span>
                            </div>
                        </div>
                        <div class="farmer-stats">
                            <div class="stat">
                                <span class="stat-value">4.8</span>
                                <span class="stat-label">Rating</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value">120+</span>
                                <span class="stat-label">Products Sold</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value">98%</span>
                                <span class="stat-label">Positive Reviews</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="farmer-badges">
                    <span class="farmer-badge verified"><i class="fas fa-check-circle"></i> Verified Farmer</span>
                    <span class="farmer-badge active"><i class="fas fa-seedling"></i> Active Member</span>
                </div>
            </div>

            <div class="tab-pane fade" id="specs" role="tabpanel" aria-labelledby="specs-tab">
                <div class="specs-grid">
                    <div class="spec-item">
                        <span class="spec-label">Product Name</span>
                        <span class="spec-value">{{ $product->product_name }}</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Category</span>
                        <span class="spec-value">{{ $product->category_name }}</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Subcategory</span>
                        <span class="spec-value">{{ $product->subcategory_name }}</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Quality Grade</span>
                        <span class="spec-value">{{ ucfirst(str_replace('_', ' ', $product->quality_grade)) }}</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Type Variant</span>
                        <span class="spec-value">{{ ucfirst($product->type_variant) }}</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Unit of Measure</span>
                        <span class="spec-value">{{ ucfirst($product->unit_of_measure) }}</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Available Quantity</span>
                        <span class="spec-value">{{ number_format($product->quantity, 2) }} {{ $product->unit_of_measure }}</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Price</span>
                        <span class="spec-value">Rs. {{ number_format($product->selling_price, 2) }} per {{ $product->unit_of_measure }}</span>
                    </div>
                    @if($product->expected_availability_date)
                    <div class="spec-item">
                        <span class="spec-label">Next Available</span>
                        <span class="spec-value">{{ date('M d, Y', strtotime($product->expected_availability_date)) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="tab-pane fade" id="pickup" role="tabpanel" aria-labelledby="pickup-tab">
                <div class="pickup-info">
                    <div class="pickup-header">
                        <i class="fas fa-truck-pickup"></i>
                        <h4>Pickup Information</h4>
                    </div>
                    <div class="pickup-details">
                        <div class="pickup-instructions">
                            <h5><i class="fas fa-clipboard-check"></i> Pickup Instructions:</h5>
                            <ul>
                                <li>Bring your order confirmation receipt</li>
                                <li>Pickup available during farmer's working hours</li>
                                <li>Please check product quality before leaving</li>
                                <li>Contact farmer before visiting for confirmation</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($processedRelatedProducts && count($processedRelatedProducts) > 0)
    <div class="related-products">
        <div class="section-header">
            <h3><i class="fas fa-seedling"></i> More from this Farmer</h3>
            <a href="{{ route('buyer.browseProducts') }}?farmer_id={{ $product->farmer_id }}" class="view-all-link">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="products-grid">
            @foreach($processedRelatedProducts as $related)
            <div class="product-card">
                <div class="product-image">
                    <img src="{{ $related->display_image }}"
                         alt="{{ $related->product_name }}">
                    @if($related->quality_grade == 'grade_a')
                    <span class="card-badge premium">Premium</span>
                    @endif
                </div>
                <div class="product-card-body">
                    <h4 class="product-name">{{ $related->product_name }}</h4>
                    <div class="product-price">Rs. {{ number_format($related->selling_price, 2) }}</div>
                    <div class="product-meta">
                        <span class="stock-info">{{ number_format($related->quantity, 2) }} {{ $related->unit_of_measure }}</span>
                        <span class="grade-badge">{{ ucfirst(str_replace('_', ' ', $related->quality_grade)) }}</span>
                    </div>
                    <a href="{{ route('buyer.productDetail', $related->id) }}" class="btn btn-sm btn-outline-primary">
                        View Details
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/buyer_product_details.js') }}"></script>

<style>
.add-to-cart-btn.loading {
    opacity: 0.8;
    cursor: not-allowed;
}

.cart-badge.pulse {
    animation: pulse 0.5s ease-in-out;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}
</style>
@endsection
