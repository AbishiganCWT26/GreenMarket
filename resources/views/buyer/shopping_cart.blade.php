@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')

<h2><i class="fas fa-shopping-cart"></i> My Cart</h2>

<div class="card-panel">

    @if(count($cart_items) === 0)
        <p>Your cart is empty.</p>
    @else

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price (LKR)</th>
                <th>Total (LKR)</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            @foreach($cart_items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>

                <td>
                    <form method="POST" action="{{ route('buyer.cart.update', $item->id) }}">
                        @csrf
                        <input type="number" class="qty-input" name="quantity" value="{{ $item->quantity }}" min="1">
                        <button class="btn btn-sm btn-secondary">Update</button>
                    </form>
                </td>

                <td>{{ number_format($item->product->price,2) }}</td>
                <td>{{ number_format($item->product->price * $item->quantity, 2) }}</td>

                <td>
                    <a href="{{ route('buyer.cart.remove', $item->id) }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>

    <div class="text-right">
        <h3>Total: LKR {{ number_format($cart_total, 2) }}</h3>

        <a href="{{ route('buyer.checkout') }}" class="btn btn-primary btn-lg mt-3">
            Proceed to Checkout
        </a>
    </div>

    @endif
</div>

@endsection
