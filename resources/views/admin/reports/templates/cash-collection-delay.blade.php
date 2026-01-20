@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-clock"></i> Cash Collection Delay Report
    </div>

    @if(count($data) > 0)
        @php
            $delayedOrders = collect($data)->where('delay_status', '!=', 'On Time')->count();
            $noPaymentOrders = collect($data)->where('delay_status', 'No Payment Recorded')->count();
            $avgDelayDays = collect($data)->avg('days_delayed');
            $totalDelayedAmount = collect($data)->where('delay_status', '!=', 'On Time')->sum('cod_amount');
        @endphp

        <div class="summary-stats">
            <div class="stat-card warning">
                <div class="stat-value">{{ $delayedOrders }}</div>
                <div class="stat-label">Delayed Orders</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value">{{ $noPaymentOrders }}</div>
                <div class="stat-label">No Payment Recorded</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ number_format($avgDelayDays, 1) }}</div>
                <div class="stat-label">Avg Delay Days</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value">Rs. {{ number_format($totalDelayedAmount, 2) }}</div>
                <div class="stat-label">Delayed Amount</div>
            </div>
        </div>

        @if($delayedOrders > 0)
        <div class="highlight warning">
            <h4>⚠️ Collection Delays Detected</h4>
            <p>
                {{ $delayedOrders }} orders with cash collection delays totaling Rs. {{ number_format($totalDelayedAmount, 2) }}<br>
                {{ $noPaymentOrders }} orders have no payment recorded at all<br>
                Immediate follow-up required with buyers and lead farmers
            </p>
        </div>
        @endif

        <table class="report-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Buyer Name</th>
                    <th>Farmer Name</th>
                    <th>COD Amount</th>
                    <th>Order Date</th>
                    <th>Days Delayed</th>
                    <th>Delay Status</th>
                    <th>Action Required</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $order)
                    <tr>
                        <td>{{ $order->order_id }}</td>
                        <td>{{ $order->buyer_name }}</td>
                        <td>{{ $order->farmer_name }}</td>
                        <td class="numeric">Rs. {{ number_format($order->cod_amount, 2) }}</td>
                        <td>{{ date('M d, Y', strtotime($order->created_at)) }}</td>
                        <td>
                            @if($order->days_delayed > 7)
                                <span class="warning">{{ $order->days_delayed }} days</span>
                            @elseif($order->days_delayed > 3)
                                <span class="info">{{ $order->days_delayed }} days</span>
                            @else
                                <span class="success">{{ $order->days_delayed }} days</span>
                            @endif
                        </td>
                        <td>
                            @if($order->delay_status == 'No Payment Recorded')
                                <span class="warning">❌ No Payment</span>
                            @elseif($order->delay_status == 'Delayed Payment')
                                <span class="warning">⚠️ Delayed</span>
                            @else
                                <span class="success">✅ On Time</span>
                            @endif
                        </td>
                        <td>
                            @if($order->delay_status == 'No Payment Recorded')
                                <span class="warning">URGENT: Contact Buyer</span>
                            @elseif($order->delay_status == 'Delayed Payment')
                                <span class="info">Follow-up Required</span>
                            @else
                                <span class="success">No Action</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="note">
            <i class="fas fa-exclamation-triangle"></i>
            <p><strong>Risk Assessment:</strong> Orders delayed beyond 7 days are at high risk of becoming bad debt. Orders with no payment recorded after 3 days require immediate intervention.</p>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Collection Delay Pattern Analysis</div>
                <div class="chart-placeholder">
                    Chart: Delay Duration Distribution
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-check-circle"></i>
            <h3>No Collection Delays</h3>
            <p>All cash collections are on time for the selected period</p>
        </div>
    @endif
@endsection
