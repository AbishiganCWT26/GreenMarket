@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-exclamation-triangle"></i> COD Exception Report
    </div>

    @if(count($data) > 0)
        @php
            $overpayments = collect($data)->where('variance', '<', 0)->count();
            $underpayments = collect($data)->where('variance', '>', 0)->count();
            $totalVariance = collect($data)->sum('variance');
            $avgVariance = collect($data)->avg('variance');
        @endphp

        <div class="summary-stats">
            <div class="stat-card warning">
                <div class="stat-value">{{ count($data) }}</div>
                <div class="stat-label">Total Exceptions</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value">{{ $underpayments }}</div>
                <div class="stat-label">Underpayments</div>
            </div>
            <div class="stat-card info">
                <div class="stat-value">{{ $overpayments }}</div>
                <div class="stat-label">Overpayments</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value">Rs. {{ number_format(abs($totalVariance), 2) }}</div>
                <div class="stat-label">Total Variance</div>
            </div>
        </div>

        <div class="highlight {{ $totalVariance > 0 ? 'warning' : 'info' }}">
            <h4>💰 Financial Discrepancy Alert</h4>
            <p>
                @if($totalVariance > 0)
                    ❗ <strong>Net Underpayment:</strong> Rs. {{ number_format($totalVariance, 2) }} less than expected<br>
                    {{ $underpayments }} orders underpaid, {{ $overpayments }} orders overpaid
                @else
                    ⚠️ <strong>Net Overpayment:</strong> Rs. {{ number_format(abs($totalVariance), 2) }} more than expected<br>
                    Verify payment recording accuracy
                @endif
            </p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Buyer Name</th>
                    <th>Farmer Name</th>
                    <th>Order Amount</th>
                    <th>Recorded Cash</th>
                    <th>Variance</th>
                    <th>Exception Type</th>
                    <th>Collection Date</th>
                    <th>Priority</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $order)
                    <tr>
                        <td>{{ $order->order_id }}</td>
                        <td>{{ $order->buyer_name }}</td>
                        <td>{{ $order->farmer_name }}</td>
                        <td class="numeric">Rs. {{ number_format($order->order_amount, 2) }}</td>
                        <td class="numeric">Rs. {{ number_format($order->recorded_cash, 2) }}</td>
                        <td class="numeric">
                            @if($order->variance > 0)
                                <span class="warning">-Rs. {{ number_format($order->variance, 2) }}</span>
                            @else
                                <span class="info">+Rs. {{ number_format(abs($order->variance), 2) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($order->variance > 0)
                                <span class="warning">Underpayment</span>
                            @else
                                <span class="info">Overpayment</span>
                            @endif
                        </td>
                        <td>{{ date('M d, Y', strtotime($order->collection_date)) }}</td>
                        <td>
                            @if(abs($order->variance) > 500)
                                <span class="warning">HIGH</span>
                            @elseif(abs($order->variance) > 100)
                                <span class="info">MEDIUM</span>
                            @else
                                <span class="success">LOW</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="note">
            <i class="fas fa-search-dollar"></i>
            <p><strong>Investigation Guidelines:</strong>
                1. Verify payment amounts with buyers<br>
                2. Check payment recording by lead farmers<br>
                3. Reconcile cash collection records<br>
                4. Update system records for accuracy
            </p>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Payment Variance Analysis</div>
                <div class="chart-placeholder">
                    Chart: Exception Distribution by Amount
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-check-double"></i>
            <h3>No COD Exceptions</h3>
            <p>All COD payments match order amounts perfectly</p>
        </div>
    @endif
@endsection
