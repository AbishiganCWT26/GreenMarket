@extends('buyer.layouts.buyer_master')

@section('title', 'Order History')
@section('page-title', 'Order History')

@section('content')
<div class="container">
    <h2><i class="fas fa-history"></i> Order History</h2>

    <div class="card-panel">
        @if(!isset($orders) || $orders->isEmpty())
            <p class="muted">You have no orders yet.</p>
            <a href="{{ route('buyer.browseProducts') }}" class="btn btn-primary">Start Shopping</a>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Total (LKR)</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $o)
                    <tr>
                        <td>{{ $o->order_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($o->created_at ?? $o->order_date ?? now())->format('Y-m-d') }}</td>
                        <td>Rs. {{ number_format($o->total_amount ?? 0,2) }}</td>
                        <td><span class="badge">{{ ucfirst($o->order_status ?? ($o->status ?? 'pending')) }}</span></td>
                        <td>
                            <a href="{{ route('buyer.invoice.download', $o->id ?? $o->order_id ?? $o->id) }}" class="btn btn-sm btn-primary">PDF</a>
                            <a href="{{ route('buyer.order.view', $o->id ?? $o->order_id ?? $o->id) }}" class="btn btn-sm btn-secondary">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if(method_exists($orders, 'links'))
            <div class="mt-3">{{ $orders->links() }}</div>
            @endif
        @endif
    </div>
</div>
@endsection
