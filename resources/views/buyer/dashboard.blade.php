@extends('buyer.layouts.buyer_master')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/buyer/dashboard.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
<div class="dashboard-container">
    <div class="welcome-banner">
        <h2>Welcome back, {{ Auth::user()->name ?? 'Buyer' }}!</h2>
        <p>Here's what's happening with your orders and recommendations today.</p>
        <a href="{{ route('buyer.browseProducts') }}" class="btn btn-banner">
            <i class="fa-solid fa-store me-2"></i> Browse Products
        </a>
    </div>

    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $orders_count ?? 0 }}</h3>
                <p>Total Orders</p>
                <a href="{{ route('buyer.history') }}" class="btn btn-outline-primary mt-2">View Orders</a>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <div class="stat-content">
                <h3>{{ session('cart_count') ?? 0 }}</h3>
                <p>Items in Cart</p>
                <a href="{{ route('buyer.cart') }}" class="btn btn-primary mt-2">Open Cart</a>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <i class="fa-solid fa-heart"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $wishlist_count ?? 0 }}</h3>
                <p>Saved Items</p>
                <a href="{{ route('buyer.wishlist') }}" class="btn btn-outline-primary mt-2">View Wishlist</a>
            </div>
        </div>
    </div>

    <div class="search-filter-section">
        <div class="search-box">
            <i class="fa-solid fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search products, farmers, categories..." value="{{ request('search') }}">
            <button class="btn btn-search" onclick="applyFilters()">
                <i class="fa-solid fa-search me-1"></i> Search
            </button>
        </div>

        <div class="filter-section">
            <div class="filter-row">
                <div class="filter-group">
                    <label>Category</label>
                    <select id="categoryFilter" onchange="updateSubcategories()">
                        <option value="">All Categories</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->category_name }}"
                                {{ request('category') == $category->category_name ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label>Subcategory</label>
                    <select id="subcategoryFilter">
                        <option value="">All Subcategories</option>
                        @foreach($subcategories ?? [] as $subcategory)
                            <option value="{{ $subcategory->subcategory_name }}"
                                {{ request('subcategory') == $subcategory->subcategory_name ? 'selected' : '' }}>
                                {{ $subcategory->subcategory_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label>District</label>
                    <select id="districtFilter">
                        <option value="">All Districts</option>
                        @foreach($districts ?? [] as $district)
                            <option value="{{ $district->district }}"
                                {{ request('district') == $district->district ? 'selected' : '' }}>
                                {{ $district->district }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label>Quality Grade</label>
                    <select id="gradeFilter">
                        <option value="">All Grades</option>
                        @foreach($grades ?? [] as $grade)
                            <option value="{{ $grade->standard_value }}"
                                {{ request('grade') == $grade->standard_value ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $grade->standard_value)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group price-range">
                    <label>Price Range (Rs.)</label>
                    <div class="price-inputs">
                        <input type="number" id="minPrice" placeholder="Min" value="{{ request('min_price') }}">
                        <span class="price-to">to</span>
                        <input type="number" id="maxPrice" placeholder="Max" value="{{ request('max_price') }}">
                    </div>
                </div>
            </div>

            <div class="filter-actions">
                <div class="sort-group">
                    <label>Sort by</label>
                    <select id="sortFilter">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                    </select>
                </div>

                <div class="filter-buttons">
                    <button class="btn btn-apply" onclick="applyFilters()">
                        <i class="fa-solid fa-filter me-1"></i> Apply Filters
                    </button>
                    <button class="btn btn-clear" onclick="clearFilters()">
                        <i class="fa-solid fa-times me-1"></i> Clear All
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="product-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fa-solid fa-sparkles me-2"></i> Recommended For You</h4>
            <div>
                <a href="{{ route('buyer.browseProducts') }}" class="text-decoration-none text-success">
                    View All <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        @if(count($recommended ?? []) > 0)
        <div class="product-grid-container">
            <div class="product-grid-scroll">
                @foreach($recommended as $product)
                <div class="product-card">
                    <div class="product-img-container">
                        <img src="{{ $product->product_photo ? asset('uploads/product_images/' . $product->product_photo) : asset('assets/images/product-placeholder.png') }}"
                            alt="{{ $product->product_name }}"
                            onerror="this.src='{{ asset('assets/images/product-placeholder.png') }}'">
                    </div>
                    <div class="product-body">
                        <h5 class="product-title">{{ Str::limit($product->product_name, 50) }}</h5>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="product-price">Rs. {{ number_format($product->selling_price, 2) }}</span>
                            @if($product->is_available && $product->quantity > 0)
                                <span class="badge bg-success">In Stock</span>
                            @else
                                <span class="badge bg-danger">Out of Stock</span>
                            @endif
                        </div>
                        <div class="product-meta mb-2">
                            <small class="text-muted">
                                <i class="fa-solid fa-weight-scale me-1"></i> {{ $product->quantity }} {{ $product->unit_of_measure }}
                            </small>
                            <small class="text-muted ms-2">
                                <i class="fa-solid fa-star me-1"></i> {{ $product->quality_grade ?? 'Standard' }}
                            </small>
                        </div>
                        <a href="{{ route('buyer.productDetail', $product->id) }}" class="btn btn-view">
                            <i class="fa-solid fa-eye me-2"></i> View Details
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="empty-state">
            <i class="fa-solid fa-search fa-3x text-muted mb-3"></i>
            <p class="mb-3">No recommendations yet — browse products to get personalized suggestions.</p>
            <a href="{{ route('buyer.browseProducts') }}" class="btn btn-primary btn-lg">
                <i class="fa-solid fa-store me-2"></i> Browse Products
            </a>
        </div>
        @endif
    </div>

    <div class="product-section mt-4">
        <h4><i class="fa-solid fa-bolt me-2"></i> Quick Actions</h4>
        <div class="row mt-3">
            <div class="col-md-4 mb-3">
                <div class="quick-action-card">
                    <div class="quick-action-icon">
                        <i class="fa-solid fa-history"></i>
                    </div>
                    <h5>Order History</h5>
                    <p>View your past purchases and track current orders</p>
                    <a href="{{ route('buyer.history') }}" class="btn btn-outline-success">View Orders</a>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="quick-action-card">
                    <div class="quick-action-icon">
                        <i class="fa-solid fa-truck"></i>
                    </div>
                    <h5>Track Delivery</h5>
                    <p>Check the status of your recent orders</p>
                    <a href="{{ route('buyer.history') }}" class="btn btn-outline-success">Track Now</a>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="quick-action-card">
                    <div class="quick-action-icon">
                        <i class="fa-solid fa-user-edit"></i>
                    </div>
                    <h5>Update Profile</h5>
                    <p>Manage your account information and preferences</p>
                    <a href="{{ route('buyer.profile.profile') }}" class="btn btn-outline-success">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(!isset($fontAwesomeLoaded))
<script>
    if (!document.querySelector('link[href*="font-awesome"]')) {
        const fa = document.createElement('link');
        fa.rel = 'stylesheet';
        fa.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
        document.head.appendChild(fa);
    }
</script>
@endif

<script>
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false
    });
    @endif

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session('error') }}',
        timer: 4000,
        showConfirmButton: true
    });
    @endif

    @if($errors->any())
    Swal.fire({
        icon: 'error',
        title: 'Validation Error!',
        html: `@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach`,
        showConfirmButton: true
    });
    @endif

    document.addEventListener('DOMContentLoaded', function() {
        const productGrid = document.querySelector('.product-grid-container');

        if (productGrid) {
            productGrid.addEventListener('scroll', function() {
                const cards = document.querySelectorAll('.product-card');
                cards.forEach(card => {
                    const rect = card.getBoundingClientRect();
                    if (rect.top < window.innerHeight && rect.bottom > 0) {
                        card.classList.add('animated');
                    }
                });
            });
        }

        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });

        const filterElements = document.querySelectorAll('.filter-group select, .filter-group input');
        filterElements.forEach(element => {
            element.addEventListener('change', function() {
                this.style.backgroundColor = '#f0f9ff';
                setTimeout(() => {
                    this.style.backgroundColor = '';
                }, 500);
            });
        });
    });

    let allSubcategories = @json($allSubcategories ?? []);

    function updateSubcategories() {
        const categorySelect = document.getElementById('categoryFilter');
        const subcategorySelect = document.getElementById('subcategoryFilter');
        const selectedCategory = categorySelect.value;

        subcategorySelect.innerHTML = '<option value="">All Subcategories</option>';

        if (selectedCategory) {
            const category = @json($categories ?? []).find(cat => cat.category_name === selectedCategory);
            if (category) {
                const filteredSubcategories = allSubcategories.filter(sub => sub.category_id == category.id);

                filteredSubcategories.forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub.subcategory_name;
                    option.textContent = sub.subcategory_name;

                    if (sub.subcategory_name === "{{ request('subcategory') }}") {
                        option.selected = true;
                    }

                    subcategorySelect.appendChild(option);
                });
            }
        } else {
            allSubcategories.forEach(sub => {
                const option = document.createElement('option');
                option.value = sub.subcategory_name;
                option.textContent = sub.subcategory_name;

                if (sub.subcategory_name === "{{ request('subcategory') }}") {
                    option.selected = true;
                }

                subcategorySelect.appendChild(option);
            });
        }
    }

    function applyFilters() {
        const params = new URLSearchParams();

        const search = document.getElementById('searchInput').value;
        if (search) params.set('search', search);

        const category = document.getElementById('categoryFilter').value;
        if (category) params.set('category', category);

        const subcategory = document.getElementById('subcategoryFilter').value;
        if (subcategory) params.set('subcategory', subcategory);

        const district = document.getElementById('districtFilter').value;
        if (district) params.set('district', district);

        const grade = document.getElementById('gradeFilter').value;
        if (grade) params.set('grade', grade);

        const minPrice = document.getElementById('minPrice').value;
        if (minPrice) params.set('min_price', minPrice);

        const maxPrice = document.getElementById('maxPrice').value;
        if (maxPrice) params.set('max_price', maxPrice);

        const sort = document.getElementById('sortFilter').value;
        if (sort) params.set('sort', sort);

        const loadingAlert = Swal.fire({
            title: 'Applying Filters...',
            text: 'Please wait while we filter the products',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        setTimeout(() => {
            window.location.href = '{{ route("buyer.dashboard") }}?' + params.toString();
        }, 500);
    }

    function clearFilters() {
        Swal.fire({
            title: 'Clear all filters?',
            text: 'This will reset all search and filter settings',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, clear all',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('searchInput').value = '';
                document.getElementById('categoryFilter').value = '';
                document.getElementById('subcategoryFilter').value = '';
                document.getElementById('districtFilter').value = '';
                document.getElementById('gradeFilter').value = '';
                document.getElementById('minPrice').value = '';
                document.getElementById('maxPrice').value = '';
                document.getElementById('sortFilter').value = 'newest';

                updateSubcategories();

                window.location.href = '{{ route("buyer.dashboard") }}';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateSubcategories();

        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    });
</script>
@endsection
