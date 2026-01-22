<div class="product-details">
    <div class="row">
        <div class="col-md-4">
            @if($product->product_photo)
                <img src="{{ asset('uploads/product_images/' . $product->product_photo) }}" 
                     alt="{{ $product->product_name }}"
                     class="img-fluid rounded mb-3"
                     onerror="this.src='{{ asset('assets/images/default-product.jpg') }}'">
            @else
                <div class="text-center py-5 bg-light rounded mb-3">
                    <i class="fa-solid fa-seedling text-muted" style="font-size: 64px;"></i>
                    <p class="mt-2 text-muted">No image available</p>
                </div>
            @endif
        </div>
        <div class="col-md-8">
            <h4 class="mb-3">{{ $product->product_name }}</h4>
            
            @if($product->type_variant)
                <p class="text-muted mb-2"><strong>Type/Variant:</strong> {{ $product->type_variant }}</p>
            @endif
            
            @if($product->product_description)
                <p class="mb-3">{{ $product->product_description }}</p>
            @endif
            
            <div class="row">
                <div class="col-6">
                    <p><strong>Farmer:</strong> {{ $product->farmer->name ?? 'Unknown' }}</p>
                </div>
                <div class="col-6">
                    <p><strong>Category:</strong> {{ $product->category->category_name ?? 'Unknown' }}</p>
                </div>
                <div class="col-6">
                    <p><strong>Subcategory:</strong> {{ $product->subcategory->subcategory_name ?? 'Unknown' }}</p>
                </div>
                <div class="col-6">
                    <p><strong>Quantity:</strong> {{ number_format($product->quantity, 2) }} {{ $product->unit_of_measure }}</p>
                </div>
                <div class="col-6">
                    <p><strong>Quality Grade:</strong> {{ $product->quality_grade ?? 'Not specified' }}</p>
                </div>
                <div class="col-6">
                    <p><strong>Selling Price:</strong> LKR {{ number_format($product->selling_price, 2) }}</p>
                </div>
                <div class="col-6">
                    <p><strong>Availability Date:</strong> {{ \Carbon\Carbon::parse($product->expected_availability_date)->format('M d, Y') }}</p>
                </div>
                <div class="col-6">
                    <p><strong>Status:</strong> 
                        @if($product->is_available && $product->quantity > 0)
                            <span class="badge bg-success">Available</span>
                        @else
                            <span class="badge bg-danger">Sold Out</span>
                        @endif
                    </p>
                </div>
            </div>
            
            @if($product->pickup_address)
                <div class="mt-3">
                    <p class="mb-1"><strong>Pickup Address:</strong></p>
                    <p class="text-muted">{{ $product->pickup_address }}</p>
                </div>
            @endif
            
            @if($product->pickup_map_link)
                <div class="mt-2">
                    <a href="{{ $product->pickup_map_link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-map-location-dot"></i> View on Map
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>