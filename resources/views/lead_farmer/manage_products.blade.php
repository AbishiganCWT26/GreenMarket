@extends('layouts.lead_farmer')

@section('title', 'Manage Products')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Manage Products</h3>
                    <div class="card-tools">
                        <a href="{{ route('lf.addProduct') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Product
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Search..."
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
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-info">Filter</button>
                            <a href="{{ route('lf.manageProducts') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>

                    <!-- Products Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
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
                                @forelse($products as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($product->product_photo)
                                                <img src="{{ asset('storage/product_photos/' . $product->product_photo) }}"
                                                     alt="{{ $product->product_name }}"
                                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; margin-right: 10px;">
                                            @endif
                                            <div>
                                                <strong>{{ $product->product_name }}</strong><br>
                                                <small class="text-muted">{{ $product->type_variant }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $product->farmer->name }}</td>
                                    <td>{{ $product->category->category_name }}</td>
                                    <td>{{ $product->quantity }} {{ $product->unit_of_measure }}</td>
                                    <td>LKR {{ number_format($product->selling_price, 2) }}</td>
                                    <td>
                                        @if($product->is_available && $product->quantity > 0)
                                            <span class="badge badge-success">Available</span>
                                        @else
                                            <span class="badge badge-danger">Sold Out</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('lf.editProduct', $product->id) }}" class="btn btn-sm btn-info" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger delete-product"
                                                data-id="{{ $product->id }}"
                                                data-name="{{ $product->product_name }}"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <a href="#" class="btn btn-sm btn-secondary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No products found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Delete product with SweetAlert confirmation
    $('.delete-product').click(function() {
        const productId = $(this).data('id');
        const productName = $(this).data('name');

        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to delete product: " + productName,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('lf.deleteProduct', '') }}/" + productId,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            'Something went wrong. Please try again.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>
@endpush
