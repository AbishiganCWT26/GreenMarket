@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-file-invoice-dollar"></i> System Financial Summary
    </div>

    @if(isset($data) && $data)
        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $data->total_orders ?? 0 }}</div>
                <div class="stat-label">Total Transactions</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">Rs. {{ number_format($data->total_revenue ?? 0, 2) }}</div>
                <div class="stat-label">Total Revenue</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $data->active_buyers ?? 0 }}</div>
                <div class="stat-label">Active Buyers</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $data->active_farmers ?? 0 }}</div>
                <div class="stat-label">Active Farmers</div>
            </div>
        </div>

        <div class="data-grid">
            <div class="grid-item">
                <div class="grid-label">Average Order Value</div>
                <div class="grid-value">Rs. {{ number_format($data->avg_order_value ?? 0, 2) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Revenue per Buyer</div>
                <div class="grid-value">
                    @if($data->active_buyers > 0)
                        Rs. {{ number_format(($data->total_revenue ?? 0) / $data->active_buyers, 2) }}
                    @else
                        N/A
                    @endif
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Revenue per Farmer</div>
                <div class="grid-value">
                    @if($data->active_farmers > 0)
                        Rs. {{ number_format(($data->total_revenue ?? 0) / $data->active_farmers, 2) }}
                    @else
                        N/A
                    @endif
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Transaction Frequency</div>
                <div class="grid-value">
                    @if($data->active_buyers > 0)
                        {{ number_format(($data->total_orders ?? 0) / max($data->active_buyers, 1), 1) }} per buyer
                    @else
                        N/A
                    @endif
                </div>
            </div>
        </div>

        <div class="highlight info">
            <h4>Financial Health Indicators</h4>
            <p>
                💰 <strong>Revenue Growth:</strong>
                @php
                    $avgDailyRevenue = ($data->total_revenue ?? 0) / max($data->total_orders ?? 1, 1);
                @endphp
                Average daily revenue: Rs. {{ number_format($avgDailyRevenue, 2) }}<br>

                👥 <strong>Market Penetration:</strong>
                {{ $data->active_buyers ?? 0 }} buyers engaging with {{ $data->active_farmers ?? 0 }} farmers<br>

                📈 <strong>System Efficiency:</strong>
                @if(($data->avg_order_value ?? 0) > 1000)
                    <span class="success">High-value transactions</span>
                @else
                    <span class="info">Standard transaction range</span>
                @endif
            </p>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Revenue Distribution</div>
                <div class="chart-placeholder">
                    Chart: Financial Performance Metrics
                </div>
            </div>
        </div>

        <div class="note">
            <i class="fas fa-lightbulb"></i>
            <p><strong>Key Insights:</strong> Monitor average order value trends to identify opportunities for upselling. Track active user ratios to measure system adoption success.</p>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-chart-pie"></i>
            <h3>No Financial Data</h3>
            <p>No financial transactions recorded in the selected period</p>
        </div>
    @endif
@endsection
