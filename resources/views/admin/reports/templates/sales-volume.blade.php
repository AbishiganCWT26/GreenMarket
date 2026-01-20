@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-chart-line"></i> Sales Volume & Value Analysis
    </div>

    @if(count($data) > 0)
        @php
            $totalOrders = collect($data)->sum('total_orders');
            $totalQuantity = collect($data)->sum('total_quantity');
            $totalSales = collect($data)->sum('total_sales');
            $avgOrderValue = collect($data)->avg('avg_order_value');
        @endphp

        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $totalOrders }}</div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ number_format($totalQuantity, 2) }}</div>
                <div class="stat-label">Total Quantity Sold</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">Rs. {{ number_format($totalSales, 2) }}</div>
                <div class="stat-label">Total Sales Value</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">Rs. {{ number_format($avgOrderValue, 2) }}</div>
                <div class="stat-label">Avg Order Value</div>
            </div>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Orders</th>
                    <th>Quantity Sold</th>
                    <th>Total Sales</th>
                    <th>Avg Order Value</th>
                    <th>Daily Trend</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $period)
                    <tr>
                        <td>{{ date('M d, Y', strtotime($period->period)) }}</td>
                        <td>{{ $period->total_orders }}</td>
                        <td>{{ number_format($period->total_quantity, 2) }}</td>
                        <td class="numeric">Rs. {{ number_format($period->total_sales, 2) }}</td>
                        <td class="numeric">Rs. {{ number_format($period->avg_order_value, 2) }}</td>
                        <td>
                            @if($period->total_sales > $avgOrderValue)
                                <span class="success">↑ High</span>
                            @elseif($period->total_sales < ($avgOrderValue * 0.5))
                                <span class="warning">↓ Low</span>
                            @else
                                <span class="info">→ Normal</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Monthly Sales Trend</div>
                <div class="chart-placeholder">
                    Chart: Sales Volume Over Time
                </div>
            </div>
        </div>

        <div class="highlight">
            <h4>Performance Insights</h4>
            <p>
                Average daily sales: Rs. {{ number_format($totalSales / max(count($data), 1), 2) }}<br>
                Best performing day: {{ date('M d', strtotime(collect($data)->sortByDesc('total_sales')->first()->period)) }}<br>
                Sales growth trend:
                @php
                    $firstPeriod = collect($data)->first();
                    $lastPeriod = collect($data)->last();
                    $growth = $lastPeriod && $firstPeriod ? (($lastPeriod->total_sales - $firstPeriod->total_sales) / max($firstPeriod->total_sales, 1)) * 100 : 0;
                @endphp
                @if($growth > 0)
                    <span class="success">+{{ number_format($growth, 2) }}% growth</span>
                @elseif($growth < 0)
                    <span class="warning">{{ number_format($growth, 2) }}% decline</span>
                @else
                    <span class="info">Stable</span>
                @endif
            </p>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-chart-line"></i>
            <h3>No Sales Data</h3>
            <p>No sales transactions found for the selected period</p>
        </div>
    @endif
@endsection
