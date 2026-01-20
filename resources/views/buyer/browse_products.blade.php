@extends('buyer.layouts.buyer_master')

@section('title', 'Browse Products')
@section('page-title', 'Browse Products')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/buyer/browse_products.css') }}">
@endsection

@section('content')
<div class="products-page">
    <div class="products-header">
        <h1>Browse Fresh Products</h1>
        <p>Discover fresh vegetables, fruits, spices, and more from local farmers</p>
        <div class="products-count">
            <span id="productsCount">{{ $products->total() ?? 0 }}</span> products available
        </div>
    </div>

    <!-- Advanced Filters Section -->
    <div class="advanced-filters">
        <div class="filter-group">
            <div class="product-search-bar">
                <input type="text" class="search-input" placeholder="Search products, farmers, categories..."
                       id="searchInput" value="{{ request('search') }}">
                <button class="search-btn" id="searchBtn"><i class="fa-solid fa-search"></i></button>
            </div>

            <div class="filter-row">
                <div class="filter-item">
                    <label for="categoryFilter">Category</label>
                    <select id="categoryFilter" class="filter-select">
                        <option value="all">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->category_name }}"
                                data-id="{{ $category->id }}"
                                {{ request('category') == $category->category_name ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <label for="subcategoryFilter">Subcategory</label>
                    <select id="subcategoryFilter" class="filter-select" {{ empty($allSubcategories) ? 'disabled' : '' }}>
                        <option value="">All Subcategories</option>
                        @if(!empty($allSubcategories))
                            @foreach($allSubcategories as $subcategory)
                            <option value="{{ $subcategory->subcategory_name }}"
                                    data-category-id="{{ $subcategory->category_id }}"
                                    {{ request('subcategory') == $subcategory->subcategory_name ? 'selected' : '' }}>
                                {{ $subcategory->subcategory_name }}
                            </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="filter-item">
                    <label for="districtFilter">District</label>
                    <select id="districtFilter" class="filter-select">
                        <option value="">All Districts</option>
                        @foreach($districts as $district)
                        <option value="{{ $district->district }}"
                                {{ request('district') == $district->district ? 'selected' : '' }}>
                            {{ $district->district }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <label for="gradeFilter">Quality Grade</label>
                    <select id="gradeFilter" class="filter-select">
                        <option value="">All Grades</option>
                        @foreach($grades as $grade)
                        <option value="{{ $grade->standard_value }}"
                                {{ request('grade') == $grade->standard_value ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $grade->standard_value)) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item price-range">
                    <label>Price Range (Rs.)</label>
                    <div class="price-inputs">
                        <input type="number" id="minPrice" placeholder="Min" value="{{ request('min_price') }}" min="0">
                        <span>to</span>
                        <input type="number" id="maxPrice" placeholder="Max" value="{{ request('max_price') }}" min="0">
                    </div>
                </div>

                <div class="filter-item">
                    <label for="sortSelect">Sort by</label>
                    <select class="filter-select" id="sortSelect">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name: A to Z</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button class="apply-btn" id="applyFilters">
                        <i class="fa-solid fa-filter"></i> Apply Filters
                    </button>
                    <button class="clear-btn" id="clearFilters">
                        <i class="fa-solid fa-times"></i> Clear All
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div id="productsGridContainer">
        @include('buyer.partials.products_grid', ['products' => $products])
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
    <div id="paginationContainer" class="pagination">
        @if(!$products->onFirstPage())
        <a href="{{ $products->url(1) }}" class="page-btn" title="First Page">
            <i class="fa-solid fa-angles-left"></i>
        </a>
        <a href="{{ $products->previousPageUrl() }}" class="page-btn" title="Previous Page">
            <i class="fa-solid fa-chevron-left"></i>
        </a>
        @else
        <span class="page-btn disabled">
            <i class="fa-solid fa-angles-left"></i>
        </span>
        <span class="page-btn disabled">
            <i class="fa-solid fa-chevron-left"></i>
        </span>
        @endif

        {{-- Show all page numbers --}}
        @for($i = 1; $i <= $products->lastPage(); $i++)
            @if($i == $products->currentPage())
            <span class="page-btn active">{{ $i }}</span>
            @else
            <a href="{{ $products->url($i) }}" class="page-btn">{{ $i }}</a>
            @endif
        @endfor

        @if($products->hasMorePages())
        <a href="{{ $products->nextPageUrl() }}" class="page-btn" title="Next Page">
            <i class="fa-solid fa-chevron-right"></i>
        </a>
        <a href="{{ $products->url($products->lastPage()) }}" class="page-btn" title="Last Page">
            <i class="fa-solid fa-angles-right"></i>
        </a>
        @else
        <span class="page-btn disabled">
            <i class="fa-solid fa-chevron-right"></i>
        </span>
        <span class="page-btn disabled">
            <i class="fa-solid fa-angles-right"></i>
        </span>
        @endif
    </div>
    @endif

    <!-- Empty State -->
    <div id="emptyState" class="empty-state" style="{{ $products->count() > 0 ? 'display: none;' : '' }}">
        <i class="fa-solid fa-search fa-3x"></i>
        <h3>No Products Found</h3>
        <p>We couldn't find any products matching your criteria. Try adjusting your search or filters.</p>
        <button class="refresh-btn" id="clearAllFilters">
            <i class="fa-solid fa-rotate-right"></i> Clear All Filters
        </button>
    </div>
</div>

<div class="loading-overlay" id="loadingOverlay" style="display: none;">
    <div class="loading-spinner"></div>
    <p>Loading products...</p>
</div>
@endsection

@section('scripts')
<script>
    // Store all subcategories in a JavaScript variable
    const allSubcategories = @json($allSubcategories ?? []);

    document.addEventListener('DOMContentLoaded', function() {
        // --- DOM Elements ---
        const searchInput = document.getElementById('searchInput');
        const searchBtn = document.getElementById('searchBtn');
        const categoryFilter = document.getElementById('categoryFilter');
        const subcategoryFilter = document.getElementById('subcategoryFilter');
        const districtFilter = document.getElementById('districtFilter');
        const gradeFilter = document.getElementById('gradeFilter');
        const minPrice = document.getElementById('minPrice');
        const maxPrice = document.getElementById('maxPrice');
        const sortSelect = document.getElementById('sortSelect');
        const applyFiltersBtn = document.getElementById('applyFilters');
        const clearFiltersBtn = document.getElementById('clearFilters');
        const clearAllFiltersBtn = document.getElementById('clearAllFilters');
        const productsGridContainer = document.getElementById('productsGridContainer');
        const paginationContainer = document.getElementById('paginationContainer');
        const emptyState = document.getElementById('emptyState');
        const productsCount = document.getElementById('productsCount');
        const loadingOverlay = document.getElementById('loadingOverlay');

        // --- Event Listeners ---

        // 1. Category Change: Update Subcategories using Local Data
        categoryFilter.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const categoryId = selectedOption.getAttribute('data-id');
            const categoryName = this.value;

            if (categoryId && categoryName !== 'all') {
                updateSubcategoryOptions(categoryId);
            } else {
                showAllSubcategories();
            }
        });

        // 2. Search Actions
        searchBtn.addEventListener('click', loadProducts);

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                loadProducts();
            }
        });

        // 3. Filter Buttons
        applyFiltersBtn.addEventListener('click', loadProducts);

        clearFiltersBtn.addEventListener('click', function() {
            resetFilters();
            loadProducts();
        });

        clearAllFiltersBtn.addEventListener('click', function() {
            resetFilters();
            loadProducts();
        });

        // --- Subcategory Logic ---

        function updateSubcategoryOptions(categoryId) {
            // Clear current options
            subcategoryFilter.innerHTML = '<option value="">All Subcategories</option>';

            // Filter subcategories by category_id
            const filteredSubcategories = allSubcategories.filter(subcat => subcat.category_id == categoryId);

            if (filteredSubcategories.length > 0) {
                filteredSubcategories.forEach(subcat => {
                    const option = document.createElement('option');
                    option.value = subcat.subcategory_name;
                    option.textContent = subcat.subcategory_name;
                    option.setAttribute('data-category-id', subcat.category_id);
                    subcategoryFilter.appendChild(option);
                });
                subcategoryFilter.disabled = false;
            } else {
                subcategoryFilter.disabled = false;
            }

            // Reset selection
            subcategoryFilter.value = '';
        }

        function showAllSubcategories() {
            subcategoryFilter.innerHTML = '<option value="">All Subcategories</option>';

            if (allSubcategories.length > 0) {
                allSubcategories.forEach(subcat => {
                    const option = document.createElement('option');
                    option.value = subcat.subcategory_name;
                    option.textContent = subcat.subcategory_name;
                    option.setAttribute('data-category-id', subcat.category_id);
                    subcategoryFilter.appendChild(option);
                });
                subcategoryFilter.disabled = false;
            } else {
                subcategoryFilter.disabled = true;
            }
        }

        // --- Product Loading Logic (AJAX) ---

        function loadProducts() {
            const params = getCurrentFilters();
            const url = buildUrl(params);

            // Update URL without reload
            window.history.pushState({}, '', url);

            loadProductsFromUrl(url);
        }

        function loadProductsFromUrl(url) {
            if (loadingOverlay) loadingOverlay.style.display = 'flex';

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    // Update products grid
                    productsGridContainer.innerHTML = data.products_html;

                    // Update pagination
                    if (paginationContainer) {
                        if (data.pagination_html) {
                            paginationContainer.innerHTML = data.pagination_html;
                            paginationContainer.style.display = 'flex';
                        } else {
                            paginationContainer.style.display = 'none';
                        }
                    }

                    // Update products count
                    if (productsCount) productsCount.textContent = data.count || 0;

                    // Show/hide empty state
                    if (emptyState) {
                        emptyState.style.display = (data.count > 0) ? 'none' : 'block';
                    }
                })
                .catch(error => {
                    console.error('Error loading products:', error);
                    alert('Error loading products. Please try again.');
                })
                .finally(() => {
                    if (loadingOverlay) loadingOverlay.style.display = 'none';
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
        }

        function getCurrentFilters() {
            return {
                search: searchInput.value.trim(),
                category: categoryFilter.value === 'all' ? '' : categoryFilter.value,
                subcategory: subcategoryFilter.value,
                district: districtFilter.value,
                grade: gradeFilter.value,
                min_price: minPrice.value,
                max_price: maxPrice.value,
                sort: sortSelect.value,
                per_page: 15 // Always show 15 products per page
            };
        }

        function buildUrl(params) {
            const url = new URL('{{ route("buyer.browseProducts") }}');

            Object.keys(params).forEach(key => {
                if (params[key]) {
                    url.searchParams.append(key, params[key]);
                }
            });

            return url.toString();
        }

        function resetFilters() {
            searchInput.value = '';
            categoryFilter.value = 'all';
            showAllSubcategories(); // Reset subcategories to ALL
            districtFilter.value = '';
            gradeFilter.value = '';
            minPrice.value = '';
            maxPrice.value = '';
            sortSelect.value = 'newest';
        }

        // --- Initialization & History Handling ---

        // Handle browser back/forward buttons
        window.addEventListener('popstate', function() {
            const urlParams = new URLSearchParams(window.location.search);

            // Update filter inputs
            searchInput.value = urlParams.get('search') || '';
            categoryFilter.value = urlParams.get('category') || 'all';
            districtFilter.value = urlParams.get('district') || '';
            gradeFilter.value = urlParams.get('grade') || '';
            minPrice.value = urlParams.get('min_price') || '';
            maxPrice.value = urlParams.get('max_price') || '';
            sortSelect.value = urlParams.get('sort') || 'newest';

            // Sync Subcategories based on the URL Category
            const selectedCategory = urlParams.get('category');
            if (selectedCategory && selectedCategory !== 'all') {
                const categoryOption = Array.from(categoryFilter.options).find(opt => opt.value === selectedCategory);
                if (categoryOption) {
                    const categoryId = categoryOption.getAttribute('data-id');
                    if (categoryId) updateSubcategoryOptions(categoryId);
                }
            } else {
                showAllSubcategories();
            }

            // Set subcategory if exists in URL
            const selectedSubcategory = urlParams.get('subcategory');
            if (selectedSubcategory) {
                // Wait a bit for options to be populated
                setTimeout(() => {
                    subcategoryFilter.value = selectedSubcategory;
                }, 50);
            }

            // Load products
            loadProductsFromUrl(window.location.href);
        });

        // Initialize on page load
        const urlParams = new URLSearchParams(window.location.search);
        const selectedCategory = urlParams.get('category');
        const selectedSubcategory = urlParams.get('subcategory');

        if (selectedCategory && selectedCategory !== 'all') {
            const categoryOption = Array.from(categoryFilter.options).find(opt => opt.value === selectedCategory);
            if (categoryOption) {
                const categoryId = categoryOption.getAttribute('data-id');
                if (categoryId) {
                    // Update options first
                    updateSubcategoryOptions(categoryId);
                    // Then set the value if a subcategory is also in the URL
                    if (selectedSubcategory) {
                        setTimeout(() => {
                            subcategoryFilter.value = selectedSubcategory;
                        }, 100);
                    }
                }
            }
        } else {
            showAllSubcategories();
        }
    });
</script>
@endsection
