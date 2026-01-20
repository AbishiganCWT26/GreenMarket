@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Inventory Reports')

@section('page-title', 'Inventory Reports')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-boxes me-2"></i> Inventory Reports
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Products</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $products->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-box fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Low Stock Products</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $lowStockProducts }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                In Stock Value</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                LKR {{ number_format($totalStockValue->total_value ?? 0, 2) }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Active Farmers</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $products->groupBy('farmer_id')->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <form method="GET" action="" class="row mb-4">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search Product</label>
                            <input type="text" name="search" id="search"
                                   class="form-control" value="{{ request('search') }}"
                                   placeholder="Product name...">
                        </div>
                        <div class="col-md-3">
                            <label for="category" class="form-label">Category</label>
                            <select name="category_id" id="category" class="form-control">
                                <option value="">All Categories</option>
                                <!-- Categories will be populated via AJAX -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="stock_status" class="form-label">Stock Status</label>
                            <select name="stock_status" id="stock_status" class="form-control">
                                <option value="">All</option>
                                <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low Stock (< 10)</option>
                                <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                                <option value="available" {{ request('stock_status') == 'available' ? 'selected' : '' }}>In Stock</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('lf.reports.inventory') }}" class="btn btn-secondary">
                                <i class="fas fa-sync me-1"></i> Reset
                            </a>
                        </div>
                    </form>

                    <!-- Inventory Table -->
                    <div class="card shadow">
                        <div class="card-header bg-light">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-list me-1"></i> Product Inventory
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($products->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Farmer</th>
                                            <th>Category</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                            <th>Price/Unit</th>
                                            <th>Total Value</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($products as $product)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($product->product_photo)
                                                        <img src="{{ asset('storage/product_photos/' . $product->product_photo) }}"
                                                             alt="{{ $product->product_name }}"
                                                             style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px; margin-right: 10px;">
                                                    @endif
                                                    <div>
                                                        <strong>{{ $product->product_name }}</strong><br>
                                                        <small class="text-muted">{{ $product->type_variant }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $product->farmer->name }}</td>
                                            <td>{{ $product->category->category_name }}</td>
                                            <td>
                                                <span class="{{ $product->quantity < 10 ? 'text-danger fw-bold' : '' }}">
                                                    {{ $product->quantity }}
                                                </span>
                                            </td>
                                            <td>{{ $product->unit_of_measure }}</td>
                                            <td>LKR {{ number_format($product->selling_price, 2) }}</td>
                                            <td>LKR {{ number_format($product->quantity * $product->selling_price, 2) }}</td>
                                            <td>
                                                @if($product->quantity == 0)
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                @elseif($product->quantity < 10)
                                                    <span class="badge bg-warning">Low Stock</span>
                                                @else
                                                    <span class="badge bg-success">In Stock</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr class="table-primary">
                                            <td colspan="6" class="text-end"><strong>Total Inventory Value:</strong></td>
                                            <td colspan="2">
                                                <strong>LKR {{ number_format($products->sum(function($p) { return $p->quantity * $p->selling_price; }), 2) }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-4">
                                <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No inventory data available</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Export Options -->
                    <div class="mt-4 text-center">
                        <button class="btn btn-success me-2" onclick="exportInventoryToExcel()">
                            <i class="fas fa-file-excel me-1"></i> Export to Excel
                        </button>
                        <button class="btn btn-danger" onclick="printInventoryReport()">
                            <i class="fas fa-print me-1"></i> Print Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportInventoryToExcel() {
    toastr.info('Excel export feature coming soon!');
}

function printInventoryReport() {
    window.print();
}

document.addEventListener('DOMContentLoaded', function() {
    // Load categories for filter
    fetchCategories();

    function fetchCategories() {
        fetch('{{ route("lf.getSubcategories", "all") }}'.replace('all', ''))
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('category');
                if (data && data.categories) {
                    data.categories.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.category_name;
                        if ('{{ request("category_id") }}' == category.id) {
                            option.selected = true;
                        }
                        select.appendChild(option);
                    });
                }
            });
    }
});
</script>
@endpush
