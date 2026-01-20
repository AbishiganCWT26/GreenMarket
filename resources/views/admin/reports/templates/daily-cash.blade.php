@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-cash-register"></i> Daily Cash Position (COD)
    </div>

    @if(count($data) > 0)
        @php
            $totalCodOrders = collect($data)->sum('total_cod_orders');
            $totalCollected = collect($data)->sum('collected_amount');
            $totalOutstanding = collect($data)->sum('outstanding_amount');
            $collectionRate = $totalCodOrders > 0 ? round(($totalCollected / ($totalCollected + $totalOutstanding)) * 100, 2) : 0;
        @endphp

        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $totalCodOrders }}</div>
                <div class="stat-label">Total COD Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">Rs. {{ number_format($totalCollected, 2) }}</div>
                <div class="stat-label">Collected Amount</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">Rs. {{ number_format($totalOutstanding, 2) }}</div>
                <div class="stat-label">Outstanding Amount</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $collectionRate }}%</div>
                <div class="stat-label">Collection Rate</div>
            </div>
        </div>

        @if($totalOutstanding > 0)
        <div class="highlight warning">
            <h4>Attention Required</h4>
            <p>There are Rs. {{ number_format($totalOutstanding, 2) }} in outstanding COD payments that require follow-up.</p>
        </div>
        @endif

        <table class="report-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>COD Orders</th>
                    <th>Cash Collected</th>
                    <th>Outstanding</th>
                    <th>Collection Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $day)
                    @php
                        $dayOutstanding = $day->outstanding_amount;
                        $dayCollectionRate = ($day->collected_amount + $dayOutstanding) > 0
                            ? round(($day->collected_amount / ($day->collected_amount + $dayOutstanding)) * 100, 2)
                            : 0;
                    @endphp
                    <tr>
                        <td>{{ date('M d, Y', strtotime($day->date)) }}</td>
                        <td>{{ $day->total_cod_orders }}</td>
                        <td class="numeric">Rs. {{ number_format($day->collected_amount, 2) }}</td>
                        <td class="numeric">
                            @if($dayOutstanding > 0)
                                <span class="warning">Rs. {{ number_format($dayOutstanding, 2) }}</span>
                            @else
                                Rs. {{ number_format($dayOutstanding, 2) }}
                            @endif
                        </td>
                        <td>
                            @if($dayCollectionRate < 80)
                                <span class="warning">{{ $dayCollectionRate }}%</span>
                            @else
                                <span class="success">{{ $dayCollectionRate }}%</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Daily Cash Collection Trend</div>
                <div class="chart-placeholder">
                    Chart: Cash Collection Trend
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-money-bill-wave"></i>
            <h3>No COD Transactions</h3>
            <p>No cash on delivery transactions found for the selected period</p>
        </div>
    @endif
@endsection
