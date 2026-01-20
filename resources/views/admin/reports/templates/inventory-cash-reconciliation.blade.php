@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-balance-scale"></i> Inventory vs Cash Reconciliation
    </div>

    @if(count($data) > 0)
        @php
            $totalProducts = count($data);
            $totalQuantitySold = collect($data)->sum('quantity_sold');
            $totalCashExpected = collect($data)->sum('cash_expected');
            $totalCashReceived = collect($data)->sum('cash_received');
            $totalVariance = collect($data)->sum('variance');
            $perfectMatches = collect($data)->where('variance', 0)->count();
            $variances = collect($data)->where('variance', '!=', 0);
            $significantVariances = $variances->where('variance', '>', 100)->count();
            $reconciliationRate = ($perfectMatches / max($totalProducts, 1)) * 100;
        @endphp

        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $totalProducts }}</div>
                <div class="stat-label">Products</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ number_format($totalQuantitySold, 2) }}</div>
                <div class="stat-label">Quantity Sold</div>
            </div>
            <div class="stat-card success">
                <div class="stat-value">Rs. {{ number_format($totalCashExpected, 2) }}</div>
                <div class="stat-label">Expected Cash</div>
            </div>
            <div class="stat-card {{ $totalVariance == 0 ? 'success' : 'warning' }}">
                <div class="stat-value">Rs. {{ number_format(abs($totalVariance), 2) }}</div>
                <div class="stat-label">Total Variance</div>
            </div>
        </div>

        <div class="highlight {{ $reconciliationRate >= 95 ? 'success' : ($reconciliationRate >= 85 ? 'info' : 'warning') }}">
            <h4>⚖️ Reconciliation Status</h4>
            <p>
                Reconciliation Rate: <strong>{{ number_format($reconciliationRate, 1) }}%</strong><br>
                Total Expected Cash: <strong>Rs. {{ number_format($totalCashExpected, 2) }}</strong><br>
                Total Cash Received: <strong>Rs. {{ number_format($totalCashReceived, 2) }}</strong><br>
                Net Variance: <strong>
                    @if($totalVariance > 0)
                        <span class="warning">-Rs. {{ number_format($totalVariance, 2) }}</span> (Short)
                    @elseif($totalVariance < 0)
                        <span class="info">+Rs. {{ number_format(abs($totalVariance), 2) }}</span> (Excess)
                    @else
                        <span class="success">Perfect Match</span>
                    @endif
                </strong>
            </p>
        </div>

        @if($variances->count() > 0)
        <div class="highlight warning">
            <h4>⚠️ Reconciliation Issues Detected</h4>
            <p>
                {{ $variances->count() }} products with cash reconciliation issues<br>
                {{ $significantVariances }} products with variances > Rs. 100<br>
                Total amount requiring investigation: <strong>Rs. {{ number_format(abs($variances->sum('variance')), 2) }}</strong><br>
                Immediate investigation recommended for top 10 variances
            </p>
        </div>
        @endif

        <table class="report-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity Sold</th>
                    <th>Expected Cash</th>
                    <th>Cash Received</th>
                    <th>Variance</th>
                    <th>Variance %</th>
                    <th>Match Status</th>
                    <th>Risk Level</th>
                    <th>Investigation Priority</th>
                    <th>Action Required</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $product)
                    @php
                        $variancePercent = $product->cash_expected > 0 ? abs($product->variance / $product->cash_expected) * 100 : 0;
                    @endphp
                    <tr>
                        <td>{{ $product->product_name }}</td>
                        <td>{{ number_format($product->quantity_sold, 2) }}</td>
                        <td class="numeric">Rs. {{ number_format($product->cash_expected, 2) }}</td>
                        <td class="numeric">Rs. {{ number_format($product->cash_received, 2) }}</td>
                        <td class="numeric">
                            @if($product->variance > 0)
                                <span class="warning">-Rs. {{ number_format($product->variance, 2) }}</span>
                            @elseif($product->variance < 0)
                                <span class="info">+Rs. {{ number_format(abs($product->variance), 2) }}</span>
                            @else
                                <span class="success">Rs. 0.00</span>
                            @endif
                        </td>
                        <td>{{ number_format($variancePercent, 2) }}%</td>
                        <td>
                            @if($product->variance == 0)
                                <span class="success">✅ Perfect Match</span>
                            @elseif(abs($product->variance) <= 10)
                                <span class="info">⚠️ Minor Variance</span>
                            @elseif(abs($product->variance) <= 100)
                                <span class="warning">⚠️ Moderate Variance</span>
                            @else
                                <span class="warning">❌ Major Variance</span>
                            @endif
                        </td>
                        <td>
                            @if($variancePercent <= 1)
                                <span class="success">Low</span>
                            @elseif($variancePercent <= 5)
                                <span class="info">Medium</span>
                            @else
                                <span class="warning">High</span>
                            @endif
                        </td>
                        <td>
                            @if(abs($product->variance) > 500)
                                <span class="warning">URGENT</span>
                            @elseif(abs($product->variance) > 100)
                                <span class="warning">HIGH</span>
                            @elseif(abs($product->variance) > 10)
                                <span class="info">MEDIUM</span>
                            @else
                                <span class="success">LOW</span>
                            @endif
                        </td>
                        <td>
                            @if($product->variance > 0)
                                <span class="warning">Collect Shortfall</span>
                            @elseif($product->variance < 0)
                                <span class="info">Verify Overpayment</span>
                            @else
                                <span class="success">Verified</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="data-grid">
            <div class="grid-item">
                <div class="grid-label">Perfect Reconciliation Rate</div>
                <div class="grid-value">{{ number_format($reconciliationRate, 1) }}%</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Avg Variance per Product</div>
                <div class="grid-value">Rs. {{ number_format(collect($data)->avg('variance'), 2) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Cash Collection Efficiency</div>
                <div class="grid-value">
                    @php
                        $collectionEfficiency = ($totalCashReceived / max($totalCashExpected, 1)) * 100;
                    @endphp
                    {{ number_format($collectionEfficiency, 1) }}%
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Top Variance Products</div>
                <div class="grid-value">
                    @php
                        $topVariances = collect($data)->sortByDesc(function($p) { return abs($p->variance); })->take(3);
                    @endphp
                    @foreach($topVariances as $product)
                        {{ $product->product_name }} (Rs. {{ number_format(abs($product->variance), 2) }})
                        @if(!$loop->last)<br>@endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="note">
            <i class="fas fa-search"></i>
            <p><strong>Investigation Guidelines:</strong>
                1. Verify sales quantities with inventory records<br>
                2. Cross-check cash receipts with payment records<br>
                3. Investigate variances > Rs. 100 immediately<br>
                4. Update system records after verification<br>
                5. Document all reconciliation findings
            </p>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Cash Reconciliation Analysis by Product</div>
                <div class="chart-placeholder">
                    Chart: Expected vs Received Cash Comparison
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-check-double"></i>
            <h3>Perfect Reconciliation</h3>
            <p>All inventory sales match cash receipts perfectly</p>
        </div>
    @endif
@endsection
