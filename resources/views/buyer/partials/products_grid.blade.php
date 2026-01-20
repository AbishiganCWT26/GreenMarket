@if($products->count() > 0)
<div class="products-grid">
    @foreach($products as $product)
    <!-- CHANGE THIS LINE: Use buyer.productDetail instead of buyer.product -->
    <a href="{{ route('buyer.productDetail', $product->id) }}" class="product-card">
        @if($product->quality_grade)
        <span class="quality-grade">
            <i class="fa-solid fa-award"></i> {{ ucfirst(str_replace('_', ' ', $product->quality_grade)) }}
        </span>
        @endif

        <img src="{{ $product->product_photo ? asset('uploads/product_images/' . $product->product_photo) : asset('assets/images/product-placeholder.png') }}"
            alt="{{ $product->product_name }}"
            onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">

        <div class="product-content">
            <h4>{{ $product->product_name }}</h4>

            @if($product->product_description)
            <p class="product-description">{{ Str::limit($product->product_description, 100) }}</p>
            @endif

            <div class="product-meta">
                <div class="price">{{ number_format($product->selling_price, 2) }}</div>
                <div class="quantity-badge">
                    <i class="fa-solid fa-weight-scale"></i> {{ $product->quantity }} {{ $product->unit_of_measure }}
                </div>
            </div>

            <div class="farmer-details">
                <div class="farmer-name-row">
                    <i class="fa-solid fa-user"></i>
                    <span class="farmer-name-label">Farmer:</span>
                    <span class="farmer-name-value">{{ $product->farmer_name ?? 'Local Farmer' }}</span>
                </div>
                <br>
                <div class="farmer-info-grid">
                    <div class="farmer-info-item">
                        <i class="fa-solid fa-credit-card"></i>
                        <span class="info-label">Payment:</span>
                        <span class="info-value">{{ ucfirst($product->preferred_payment ?? 'bank') }}</span>
                    </div>
                    <br>
                    <div class="farmer-info-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <span class="info-label">District:</span>
                        <span class="info-value">{{ $product->district ?? 'Colombo' }}</span>
                    </div>
                </div>
            </div>

            <div class="stock-status">
                @if($product->is_available && $product->quantity > 0)
                <span class="stock-badge stock-in">
                    <i class="fa-solid fa-check-circle"></i> In Stock
                </span>
                @else
                <span class="stock-badge stock-out">
                    <i class="fa-solid fa-times-circle"></i> Out of Stock
                </span>
                @endif
                <span class="view-details">
                    View Details <i class="fa-solid fa-arrow-right"></i>
                </span>
            </div>
        </div>
    </a>
    @endforeach
</div>
@else
<div class="empty-state">
    <i class="fa-solid fa-search fa-3x"></i>
    <h3>No Products Found</h3>
    <p>We couldn't find any products matching your criteria. Try adjusting your search or filters.</p>
</div>
@endif
