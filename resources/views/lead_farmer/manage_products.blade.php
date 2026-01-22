@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Manage Products')
@section('page-title', 'Manage Products')

@section('styles')
<style>
.products-container {
    padding: 20px;
}

.products-card {
    background: var(--card-bg);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
    border: 1px solid rgba(16, 185, 129, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}

.products-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
    border-color: var(--primary-green);
}

.card-header {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.05));
    padding: 20px 25px;
    border-bottom: 2px solid var(--primary-green);
}

.card-header h3 {
    color: var(--text-color);
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-header h3 i {
    color: var(--primary-green);
    transition: all 0.3s ease;
}

.card-header:hover h3 i {
    color: var(--dark-green);
    transform: rotate(15deg);
}

.add-product-btn {
    background: var(--primary-green);
    border-color: var(--primary-green);
    color: white;
    font-weight: 600;
    padding: 10px 20px;
    border-radius: 8px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.add-product-btn:hover {
    background: var(--dark-green);
    border-color: var(--dark-green);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.filter-form {
    background: #f8fafc;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 25px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.filter-form:hover {
    border-color: var(--primary-green);
    box-shadow: var(--shadow-sm);
}

.filter-form .form-control {
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    padding: 10px 15px;
    transition: all 0.3s ease;
}

.filter-form .form-control:focus {
    border-color: var(--primary-green);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.filter-btn {
    background: var(--primary-green);
    border-color: var(--primary-green);
    color: white;
    font-weight: 600;
    padding: 10px 20px;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.filter-btn:hover {
    background: var(--dark-green);
    border-color: var(--dark-green);
    transform: translateY(-2px);
}

.reset-btn {
    background: #64748b;
    border-color: #64748b;
    color: white;
    font-weight: 600;
    padding: 10px 20px;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.reset-btn:hover {
    background: #475569;
    border-color: #475569;
    transform: translateY(-2px);
}

.products-table {
    width: 100%;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--shadow-xs);
}

.products-table thead {
    background: var(--primary-green);
}

.products-table thead th {
    color: white;
    font-weight: 600;
    padding: 15px;
    border: none;
    text-transform: uppercase;
    font-size: 13px;
    letter-spacing: 0.5px;
}

.products-table tbody tr {
    transition: all 0.3s ease;
    border-bottom: 1px solid rgba(16, 185, 129, 0.1);
}

.products-table tbody tr:hover {
    background: rgba(16, 185, 129, 0.04);
    transform: translateX(3px);
}

.products-table tbody td {
    padding: 15px;
    vertical-align: middle;
    border: none;
    color: var(--text-color);
}

.product-image {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    object-fit: cover;
    border: 2px solid var(--primary-green);
    transition: all 0.3s ease;
}

.product-image:hover {
    transform: scale(1.05);
    box-shadow: var(--shadow-sm);
}

.product-info {
    display: flex;
    flex-direction: column;
}

.product-name {
    font-weight: 600;
    color: var(--text-color);
    margin-bottom: 3px;
    font-size: 15px;
}

.product-variant {
    font-size: 12px;
    color: var(--muted);
}

.farmer-name {
    font-weight: 500;
    color: var(--text-color);
}

.farmer-unknown {
    font-style: italic;
    color: #ef4444;
    font-size: 13px;
}

.category-name {
    background: rgba(16, 185, 129, 0.1);
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 13px;
    font-weight: 500;
    color: var(--primary-green);
    display: inline-block;
}

.quantity-badge {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    padding: 5px 10px;
    border-radius: 4px;
    font-weight: 600;
    font-size: 14px;
}

.price-badge {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
    padding: 5px 10px;
    border-radius: 4px;
    font-weight: 600;
    font-size: 14px;
}

.status-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 12px;
    display: inline-block;
}

.status-available {
    background: rgba(16, 185, 129, 0.15);
    color: var(--primary-green);
}

.status-sold-out {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.action-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    cursor: pointer;
    font-size: 14px;
}

.edit-btn {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.edit-btn:hover {
    background: #3b82f6;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.delete-btn {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.delete-btn:hover {
    background: #ef4444;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.view-btn {
    background: rgba(16, 185, 129, 0.1);
    color: var(--primary-green);
}

.view-btn:hover {
    background: var(--primary-green);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.no-products {
    text-align: center;
    padding: 50px 20px;
    color: var(--muted);
}

.no-products i {
    font-size: 48px;
    color: #cbd5e1;
    margin-bottom: 15px;
}

.no-products h4 {
    font-weight: 600;
    color: var(--text-color);
    margin-bottom: 10px;
}

.no-products p {
    margin-bottom: 20px;
}

.empty-state-btn {
    background: var(--primary-green);
    border-color: var(--primary-green);
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.empty-state-btn:hover {
    background: var(--dark-green);
    border-color: var(--dark-green);
    transform: translateY(-2px);
}

@media (max-width: 1200px) {
    .filter-form .row {
        gap: 15px;
    }
    
    .filter-form .col-md-3,
    .filter-form .col-md-2 {
        width: 100%;
    }
}

@media (max-width: 768px) {
    .products-container {
        padding: 15px;
    }
    
    .card-header {
        flex-direction: column;
        text-align: center;
        gap: 15px;
        padding: 15px;
    }
    
    .products-table {
        display: block;
        overflow-x: auto;
    }
    
    .action-buttons {
        flex-wrap: wrap;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .products-container {
        padding: 10px;
    }
    
    .filter-form {
        padding: 15px;
    }
    
    .product-image {
        width: 50px;
        height: 50px;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        font-size: 12px;
    }
}
</style>
@endsection

@section('content')
<div class="products-container">
    <div class="products-card animate__animated animate__fadeIn">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>
                <i class="fa-solid fa-box-open"></i>
                Manage Products
            </h3>
            <a href="{{ route('lf.addProduct') }}" class="add-product-btn">
                <i class="fa-solid fa-plus"></i>
                Add New Product
            </a>
        </div>
        
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" class="filter-form">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search products..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="farmer_id" class="form-control">
                            <option value="">All Farmers</option>
                            @foreach($farmers as $farmer)
                                <option value="{{ $farmer->id }}" {{ request('farmer_id') == $farmer->id ? 'selected' : '' }}>
                                    {{ $farmer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="category_id" class="form-control">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="sold_out" {{ request('status') == 'sold_out' ? 'selected' : '' }}>Sold Out</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="filter-btn flex-grow-1">
                            <i class="fa-solid fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('lf.manageProducts') }}" class="reset-btn">
                            <i class="fa-solid fa-rotate-left"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Products Table -->
            @if($products->count() > 0)
            <div class="table-responsive">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Farmer</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if($product->product_photo)
                                        <img src="{{ asset('uploads/product_images/' . $product->product_photo) }}"
                                             alt="{{ $product->product_name }}"
                                             class="product-image"
                                             onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
                                    @else
                                        <div class="product-image d-flex align-items-center justify-content-center bg-light">
                                            <i class="fa-solid fa-seedling text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="product-info">
                                        <span class="product-name">{{ $product->product_name }}</span>
                                        @if($product->type_variant)
                                            <span class="product-variant">{{ $product->type_variant }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($product->farmer)
                                    <span class="farmer-name">{{ $product->farmer->name }}</span>
                                @else
                                    <span class="farmer-unknown">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Farmer Not Found
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($product->category)
                                    <span class="category-name">{{ $product->category->category_name }}</span>
                                @else
                                    <span class="category-name">Unknown</span>
                                @endif
                            </td>
                            <td>
                                <span class="quantity-badge">
                                    {{ number_format($product->quantity, 2) }} {{ $product->unit_of_measure }}
                                </span>
                            </td>
                            <td>
                                <span class="price-badge">
                                    LKR {{ number_format($product->selling_price, 2) }}
                                </span>
                            </td>
                            <td>
                                @if($product->is_available && $product->quantity > 0)
                                    <span class="status-badge status-available">
                                        <i class="fa-solid fa-check"></i> Available
                                    </span>
                                @else
                                    <span class="status-badge status-sold-out">
                                        <i class="fa-solid fa-xmark"></i> Sold Out
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('lf.editProduct', $product->id) }}" 
                                       class="action-btn edit-btn"
                                       title="Edit Product">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <button class="action-btn delete-btn delete-product"
                                            data-id="{{ $product->id }}"
                                            data-name="{{ $product->product_name }}"
                                            title="Delete Product">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                    <button class="action-btn view-btn view-product-details"
                                            data-id="{{ $product->id }}"
                                            title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="no-products">
                <i class="fa-solid fa-box-open"></i>
                <h4>No Products Found</h4>
                <p>You haven't added any products yet, or no products match your filters.</p>
                <a href="{{ route('lf.addProduct') }}" class="empty-state-btn">
                    <i class="fa-solid fa-plus"></i> Add Your First Product
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Delete product with SweetAlert confirmation
    $(document).on('click', '.delete-product', function() {
        const productId = $(this).data('id');
        const productName = $(this).data('name');

        Swal.fire({
            title: 'Delete Product?',
            html: `<div class="text-center">
                <i class="fa-solid fa-trash text-danger mb-3" style="font-size: 48px;"></i>
                <h5>Are you sure?</h5>
                <p class="mb-3">You are about to delete product: <strong>${productName}</strong></p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            background: 'var(--card-bg)',
            color: 'var(--text-color)',
            width: '450px'
        }).then((result) => {
            if (result.isConfirmed) {
                // Correct way to construct the URL
                const deleteUrl = `/lead-farmer/delete-product/${productId}`;
                
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Deleting...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                confirmButtonColor: '#10B981',
                                timer: 2000,
                                timerProgressBar: true
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message,
                                confirmButtonColor: '#10B981'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.',
                            confirmButtonColor: '#10B981'
                        });
                    }
                });
            }
        });
    });

    // View product details
    $(document).on('click', '.view-product-details', function() {
        const productId = $(this).data('id');
        
        $.ajax({
            url: `/lead-farmer/product-details/${productId}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Product Details',
                        html: response.html,
                        showCloseButton: true,
                        showConfirmButton: false,
                        width: '600px',
                        background: 'var(--card-bg)',
                        color: 'var(--text-color)'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load product details',
                    confirmButtonColor: '#10B981'
                });
            }
        });
    });

    // Quick search with debounce
    let searchTimeout;
    $('input[name="search"]').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            $('.filter-form').submit();
        }, 500);
    });

    // Filter on select change
    $('select[name="farmer_id"], select[name="category_id"], select[name="status"]').on('change', function() {
        $('.filter-form').submit();
    });
});
</script>
@endsection