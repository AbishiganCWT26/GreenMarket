@extends('admin.layouts.admin_master')

@section('title', 'All Products')

@section('content')
<h2>All Products</h2>

<div class="card">
    <div class="card-body">
        @if($products->count() == 0)
            <p>No products found.</p>
        @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Farmer</th>
                    <th>Lead Farmer</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th>Available</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->farmer->name ?? '-' }}</td>
                    <td>{{ $p->leadFarmer->name ?? '-' }}</td>
                    <td>{{ $p->product_name }}</td>
                    <td>{{ $p->category->category_name }}</td>
                    <td>{{ $p->quantity }}</td>
                    <td>{{ $p->unit_of_measure }}</td>
                    <td>Rs. {{ number_format($p->selling_price,2) }}</td>
                    <td>{{ $p->is_available ? 'Yes' : 'No' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
