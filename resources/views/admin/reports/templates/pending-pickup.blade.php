@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-truck-loading"></i> Pending Pickup Orders
    </div>

    @if(count($data) > 0)
        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ count($data) }}</div>
                <div class="stat-label">Pending Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">Rs. {{ number_format(collect($data)->sum('total_amount'), 2) }}</div>
                <div class="stat-label">Total Amount</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ number_format(collect($data)->avg('days_since_paid'), 1) }}</div>
                <div class="stat-label">Avg Days Since Paid</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ collect($data)->where('days_since_paid', '>', 3)->count() }}</div>
                <div class="stat-label">Delayed Pickups</div>
            </div>
        </div>

        <div class="note">
            <i class="fas fa-exclamation-circle"></i>
            <p>These orders have been paid but are awaiting buyer pickup. Follow up with buyers for timely collection.</p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Buyer Name</th>
                    <th>Farmer Name</th>
                    <th>Products</th>
                    <th>Total Amount</th>
                    <th>Paid Date</th>
                    <th>Days Since Paid</th>
                    <th>Pickup Location</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $order)
                    <tr>
                        <td>{{ $order->order_id }}</td>
                        <td>{{ $order->buyer_name }}</td>
                        <td>{{ $order->farmer_name }}</td>
                        <td>{{ Str::limit($order->product_names, 30) }}</td>
                        <td class="numeric">Rs. {{ number_format($order->total_amount, 2) }}</td>
                        <td>{{ date('M d, Y', strtotime($order->paid_date)) }}</td>
                        <td>
                            @if($order->days_since_paid > 3)
                                <span class="warning">{{ $order->days_since_paid }} days</span>
                            @else
                                <span class="success">{{ $order->days_since_paid }} days</span>
                            @endif
                        </td>
                        <td>{{ Str::limit($order->pickup_location, 30) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="highlight">
            <h4>Action Required</h4>
            <p>Orders pending pickup for more than 3 days require immediate follow-up. Contact buyers to schedule pickup.</p>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-check-circle"></i>
            <h3>No Pending Pickups</h3>
            <p>All orders have been picked up successfully</p>
        </div>
    @endif
@endsection
