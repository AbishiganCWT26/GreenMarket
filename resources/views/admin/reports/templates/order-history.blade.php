@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-history"></i> Order History Details
    </div>

    @if(count($data) > 0)
        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ count($data) }}</div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">Rs. {{ number_format(collect($data)->sum('total_amount'), 2) }}</div>
                <div class="stat-label">Total Revenue</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ collect($data)->where('order_status', 'completed')->count() }}</div>
                <div class="stat-label">Completed Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ collect($data)->where('order_status', 'pending')->count() }}</div>
                <div class="stat-label">Pending Orders</div>
            </div>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Number</th>
                    <th>Buyer Name</th>
                    <th>Farmer Name</th>
                    <th>Status</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $order)
                    <tr>
                        <td>{{ $order->order_id }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->buyer_name }}</td>
                        <td>{{ $order->farmer_name }}</td>
                        <td>
                            <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $order->order_status)) }}">
                                {{ ucfirst($order->order_status) }}
                            </span>
                        </td>
                        <td>{{ date('M d, Y', strtotime($order->created_at)) }}</td>
                        <td class="numeric">Rs. {{ number_format($order->total_amount, 2) }}</td>
                        <td>{{ $order->payment_method ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <i class="fas fa-database"></i>
            <h3>No Order Data Available</h3>
            <p>No orders found for the selected period</p>
        </div>
    @endif
@endsection
