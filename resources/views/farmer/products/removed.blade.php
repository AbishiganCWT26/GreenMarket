@extends('farmer.layouts.farmer_master')

@section('title', 'Removed Products')
@section('page-title', 'Removed Products')

@section('styles')
<link href="{{ asset('css/farmer/removed.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="products-container">
    <div class="page-header">
        <div class="header-content">
            <h2>Removed Products</h2>
            <p>View products that have been removed from the marketplace</p>
        </div>
        <a href="{{ route('farmer.products.my-products') }}" class="btn-add">
            <i class="fas fa-arrow-left"></i> Back to Products
        </a>
    </div>

    @if($removedProducts->count() > 0)
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-trash"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $removedCount }}</h3>
                <p>Removed Products</p>
            </div>
        </div>
    </div>

    <div class="products-section">
        <div class="products-list">
            @foreach($removedProducts as $product)
            @php
                // Check if product_status is not "have it"
                $shouldDisplay = !in_array($product->product_status, ['have it', 'Have it']);
            @endphp

            @if($shouldDisplay)
            <div class="product-item removed">
                <div class="product-thumb">
                    @if($product->product_photo)
                    <img src="{{ asset('uploads/product_images/' . $product->product_photo) }}"
                         alt="{{ $product->product_name }}"
                         onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
                    @else
                    <div class="thumb-placeholder">
                        <i class="fas fa-seedling"></i>
                    </div>
                    @endif
                    <span class="status-badge removed">
                        Removed
                    </span>
                </div>
                <div class="product-info">
                    <h4 class="product-title">{{ $product->product_name }}</h4>
                    <p class="product-category">
                        {{ $product->category->category_name ?? 'No Category' }} /
                        {{ $product->subcategory->subcategory_name ?? 'No Subcategory' }}
                    </p>
                    <div class="product-meta">
                        <div class="meta-item">
                            <i class="fas fa-weight"></i>
                            <span>{{ $product->quantity }} {{ $product->unit_of_measure }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-tag"></i>
                            <span>LKR {{ number_format($product->selling_price, 2) }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <span>{{ \Carbon\Carbon::parse($product->expected_availability_date)->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="removal-details">
                        <div class="removal-info">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>Removal Status:</strong>
                                @php
                                    $productStatus = $product->product_status;
                                    $displayStatus = match($productStatus) {
                                        'removed by lead farmer' => 'Removed by Lead Farmer',
                                        'removed by facilitator' => 'Removed by Facilitator',
                                        'removed by admin' => 'Removed by Administrator',
                                        default => ucfirst($productStatus)
                                    };
                                @endphp
                                {{ $displayStatus }}
                            </div>
                        </div>
                        @if($product->removed_by)
                        <div class="removal-info">
                            <i class="fas fa-user-times"></i>
                            <div>
                                <strong>Removed by:</strong>
                                @php
                                    $removedBy = $product->removed_by;
                                    $removedText = match($removedBy) {
                                        'lead_farmer' => 'Lead Farmer',
                                        'facilitator' => 'Facilitator',
                                        'admin' => 'Administrator',
                                        default => 'System'
                                    };
                                @endphp
                                {{ $removedText }}
                            </div>
                        </div>
                        @endif
                        @if($product->removal_reason)
                        <div class="removal-info">
                            <i class="fas fa-comment"></i>
                            <div>
                                <strong>Reason:</strong> {{ $product->removal_reason }}
                            </div>
                        </div>
                        @endif
                        <div class="removal-info">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Removed on:</strong> {{ $product->updated_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="product-actions">
                    <button class="action-btn view-btn" onclick="viewRemovedProduct({{ $product->id }})">
                        <i class="fas fa-eye"></i>
                        <span>View Details</span>
                    </button>
                </div>
            </div>
            @endif
            @endforeach

            @if($removedProducts->whereNotIn('product_status', ['have it', 'Have it'])->count() === 0)
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-trash-restore"></i>
                </div>
                <h3>No Removed Products</h3>
                <p>You don't have any removed products in your history</p>
                <a href="{{ route('farmer.products.my-products') }}" class="btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Products
                </a>
            </div>
            @endif
        </div>

        @if($removedProducts->whereNotIn('product_status', ['have it', 'Have it'])->count() > 0)
        <div class="pagination-section">
            <div class="pagination-info">
                Showing {{ $removedProducts->firstItem() }} to {{ $removedProducts->lastItem() }} of {{ $removedProducts->total() }} products
            </div>
            <div class="pagination">
                {{ $removedProducts->links('vendor.pagination.custom') }}
            </div>
        </div>
        @endif
    </div>
    @else
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-trash-restore"></i>
        </div>
        <h3>No Removed Products</h3>
        <p>You don't have any removed products in your history</p>
        <a href="{{ route('farmer.products.my-products') }}" class="btn-primary">
            <i class="fas fa-arrow-left"></i> Back to Products
        </a>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function viewRemovedProduct(productId) {
    // You can implement a detailed view for removed products here
    // For now, we'll show a basic alert with the product details
    Swal.fire({
        title: 'Removed Product Details',
        text: 'Detailed view for removed product with ID: ' + productId,
        icon: 'info',
        confirmButtonColor: '#10B981',
    });
}
</script>
@endsection
