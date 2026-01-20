@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-money-check-alt"></i> Sales & Payment Reconciliation
    </div>

    @if(isset($data) && $data)
        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $data->total_orders ?? 0 }}</div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">Rs. {{ number_format($data->total_sales_value ?? 0, 2) }}</div>
                <div class="stat-label">Total Sales Value</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">Rs. {{ number_format($data->total_amount_received ?? 0, 2) }}</div>
                <div class="stat-label">Amount Received</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">Rs. {{ number_format($data->avg_order_value ?? 0, 2) }}</div>
                <div class="stat-label">Avg Order Value</div>
            </div>
        </div>

        @php
            $variance = ($data->total_sales_value ?? 0) - ($data->total_amount_received ?? 0);
            $reconciliationRate = ($data->total_sales_value ?? 0) > 0
                ? round((($data->total_amount_received ?? 0) / ($data->total_sales_value ?? 0)) * 100, 2)
                : 0;
        @endphp

        <div class="data-grid">
            <div class="grid-item {{ $variance == 0 ? 'success' : 'warning' }}">
                <div class="grid-label">Reconciliation Status</div>
                <div class="grid-value">
                    @if($variance == 0)
                        ✅ Fully Reconciled
                    @elseif(abs($variance) < 100)
                        ⚠️ Minor Discrepancy
                    @else
                        ❌ Significant Variance
                    @endif
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Variance Amount</div>
                <div class="grid-value">Rs. {{ number_format(abs($variance), 2) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Reconciliation Rate</div>
                <div class="grid-value">{{ $reconciliationRate }}%</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Collection Efficiency</div>
                <div class="grid-value">
                    @if($reconciliationRate >= 95)
                        <span class="success">Excellent</span>
                    @elseif($reconciliationRate >= 85)
                        <span class="info">Good</span>
                    @else
                        <span class="warning">Needs Attention</span>
                    @endif
                </div>
            </div>
        </div>

        @if($variance != 0)
        <div class="highlight {{ $variance > 0 ? 'warning' : 'success' }}">
            <h4>Reconciliation Alert</h4>
            <p>
                @if($variance > 0)
                    ❗ Outstanding Amount: Rs. {{ number_format($variance, 2) }} not yet received
                @else
                    ⚠️ Overpayment: Rs. {{ number_format(abs($variance), 2) }} received in excess
                @endif
                <br>
                Investigation required for payment matching.
            </p>
        </div>
        @endif

        <div class="note">
            <i class="fas fa-info-circle"></i>
            <p>This report compares total sales value against actual payments received. Perfect reconciliation occurs when variance is zero.</p>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Sales vs Payments Comparison</div>
                <div class="chart-placeholder">
                    Chart: Sales vs Payments Visualization
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-balance-scale"></i>
            <h3>No Reconciliation Data</h3>
            <p>No sales or payment data available for reconciliation</p>
        </div>
    @endif
@endsection
