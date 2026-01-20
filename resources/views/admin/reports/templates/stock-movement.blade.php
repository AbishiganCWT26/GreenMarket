@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-warehouse"></i> Stock Movement Analysis
    </div>

    @if(count($data) > 0)
        @php
            $totalSales = collect($data)->sum('sales');
            $totalEnding = collect($data)->sum('ending_quantity');
            $fastMoving = collect($data)->sortByDesc('sales')->take(5);
            $slowMoving = collect($data)->where('sales', '<=', 0)->count();
        @endphp

        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ count($data) }}</div>
                <div class="stat-label">Products Tracked</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ number_format($totalSales, 2) }}</div>
                <div class="stat-label">Total Units Sold</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ number_format($totalEnding, 2) }}</div>
                <div class="stat-label">Current Stock</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value">{{ $slowMoving }}</div>
                <div class="stat-label">Slow Movers</div>
            </div>
        </div>

        <div class="highlight info">
            <h4>📦 Inventory Insights</h4>
            <p>
                🚀 <strong>Fastest Movers:</strong>
                @foreach($fastMoving as $product)
                    {{ $product->product_name }} ({{ $product->sales }} units)
                    @if(!$loop->last), @endif
                @endforeach<br>

                ⚠️ <strong>Stock Alert:</strong> {{ $slowMoving }} products have zero sales<br>

                📊 <strong>Turnover:</strong> Average sales per product: {{ number_format($totalSales / max(count($data), 1), 1) }} units
            </p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Ending Quantity</th>
                    <th>Units Sold</th>
                    <th>Last Movement</th>
                    <th>Stock Status</th>
                    <th>Turnover Rate</th>
                    <th>Replenishment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $product)
                    @php
                        $turnoverRate = $product->ending_quantity > 0 ? ($product->sales / $product->ending_quantity) * 100 : ($product->sales > 0 ? 100 : 0);
                        $daysSince = $product->movement_date ? Carbon\Carbon::parse($product->movement_date)->diffInDays(now()) : null;
                    @endphp
                    <tr>
                        <td>{{ $product->product_name }}</td>
                        <td>{{ number_format($product->ending_quantity, 2) }}</td>
                        <td>{{ number_format($product->sales, 2) }}</td>
                        <td>
                            @if($product->movement_date)
                                {{ date('M d, Y', strtotime($product->movement_date)) }}
                                @if($daysSince > 30)
                                    <br><small class="warning">({{ $daysSince }} days ago)</small>
                                @endif
                            @else
                                No movement
                            @endif
                        </td>
                        <td>
                            @if($product->ending_quantity <= 0)
                                <span class="warning">❌ Out of Stock</span>
                            @elseif($product->ending_quantity < 10)
                                <span class="warning">⚠️ Low Stock</span>
                            @elseif($product->sales == 0)
                                <span class="info">📦 No Sales</span>
                            @else
                                <span class="success">✅ In Stock</span>
                            @endif
                        </td>
                        <td>{{ number_format($turnoverRate, 1) }}%</td>
                        <td>
                            @if($product->ending_quantity <= 0)
                                <span class="warning">URGENT</span>
                            @elseif($product->ending_quantity < 10 && $product->sales > 0)
                                <span class="info">Soon</span>
                            @else
                                <span class="success">Adequate</span>
                            @endif
                        </td>
                        <td>
                            @if($product->sales == 0 && $product->ending_quantity > 0)
                                <span class="warning">Review Pricing</span>
                            @elseif($product->ending_quantity <= 0)
                                <span class="warning">Restock</span>
                            @else
                                <span class="success">Monitor</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Stock Movement Distribution</div>
                <div class="chart-placeholder">
                    Chart: Sales vs Stock Levels
                </div>
            </div>
        </div>

        <div class="note">
            <i class="fas fa-clipboard-check"></i>
            <p><strong>Inventory Management Tips:</strong>
                1. Restock products with zero inventory immediately<br>
                2. Review pricing for products with no sales but available stock<br>
                3. Create promotions for slow-moving items<br>
                4. Monitor fast-moving items for restocking opportunities
            </p>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-box-open"></i>
            <h3>No Stock Data</h3>
            <p>No stock movement data available for analysis</p>
        </div>
    @endif
@endsection
