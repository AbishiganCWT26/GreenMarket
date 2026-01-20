@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-map-marked-alt"></i> Regional COD Performance
    </div>

    @if(count($data) > 0)
        @php
            $totalRegions = count($data);
            $totalFarmers = collect($data)->sum('total_farmers');
            $totalProducts = collect($data)->sum('total_products');
            $totalSales = collect($data)->sum('total_sales');
            $avgOrderValue = collect($data)->avg('avg_order_value');
            $topRegion = collect($data)->sortByDesc('total_sales')->first();
        @endphp

        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $totalRegions }}</div>
                <div class="stat-label">Regions</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $totalFarmers }}</div>
                <div class="stat-label">Farmers</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $totalProducts }}</div>
                <div class="stat-label">Products</div>
            </div>
            <div class="stat-card success">
                <div class="stat-value">Rs. {{ number_format($totalSales, 2) }}</div>
                <div class="stat-label">Total Sales</div>
            </div>
        </div>

        <div class="highlight success">
            <h4>📍 Top Performing Region</h4>
            <p>
                <strong>{{ $topRegion->district ?? 'N/A' }}</strong><br>
                Sales: Rs. {{ number_format($topRegion->total_sales ?? 0, 2) }}<br>
                Farmers: {{ $topRegion->total_farmers ?? 0 }}<br>
                Products: {{ $topRegion->total_products ?? 0 }}<br>
                Avg Order: Rs. {{ number_format($topRegion->avg_order_value ?? 0, 2) }}
            </p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Region</th>
                    <th>Farmers</th>
                    <th>Products</th>
                    <th>Total Sales</th>
                    <th>Avg Order Value</th>
                    <th>Market Share</th>
                    <th>Density Index</th>
                    <th>Growth Potential</th>
                    <th>Action Plan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $region)
                    @php
                        $marketShare = ($region->total_sales / max($totalSales, 1)) * 100;
                        $densityIndex = $region->total_farmers > 0 ? $region->total_sales / $region->total_farmers : 0;
                        $avgDensity = $totalFarmers > 0 ? $totalSales / $totalFarmers : 0;
                    @endphp
                    <tr>
                        <td>{{ $region->district }}</td>
                        <td>{{ $region->total_farmers }}</td>
                        <td>{{ $region->total_products }}</td>
                        <td class="numeric">Rs. {{ number_format($region->total_sales, 2) }}</td>
                        <td class="numeric">Rs. {{ number_format($region->avg_order_value, 2) }}</td>
                        <td>{{ number_format($marketShare, 1) }}%</td>
                        <td>
                            @if($densityIndex >= $avgDensity * 1.5)
                                <span class="success">⭐⭐⭐ High</span>
                            @elseif($densityIndex >= $avgDensity)
                                <span class="info">⭐⭐ Medium</span>
                            @else
                                <span class="warning">⭐ Low</span>
                            @endif
                        </td>
                        <td>
                            @if($marketShare < 5 && $region->total_farmers > 0)
                                <span class="success">High</span>
                            @elseif($marketShare < 10)
                                <span class="info">Medium</span>
                            @else
                                <span class="info">Mature</span>
                            @endif
                        </td>
                        <td>
                            @if($marketShare >= 15)
                                <span class="success">Maintain</span>
                            @elseif($region->total_farmers == 0)
                                <span class="warning">Expand</span>
                            @elseif($marketShare < 5)
                                <span class="info">Develop</span>
                            @else
                                <span class="info">Grow</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="data-grid">
            <div class="grid-item">
                <div class="grid-label">Avg Farmers per Region</div>
                <div class="grid-value">{{ number_format($totalFarmers / max($totalRegions, 1), 1) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Avg Products per Region</div>
                <div class="grid-value">{{ number_format($totalProducts / max($totalRegions, 1), 1) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Top 3 Regions Share</div>
                <div class="grid-value">
                    {{ number_format((collect($data)->sortByDesc('total_sales')->take(3)->sum('total_sales') / max($totalSales, 1)) * 100, 1) }}%
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Regional Coverage</div>
                <div class="grid-value">
                    @php
                        $coveredRegions = collect($data)->where('total_farmers', '>', 0)->count();
                        $coverageRate = ($coveredRegions / max($totalRegions, 1)) * 100;
                    @endphp
                    {{ number_format($coverageRate, 1) }}%
                </div>
            </div>
        </div>

        <div class="note">
            <i class="fas fa-expand-alt"></i>
            <p><strong>Regional Strategy:</strong>
                1. Focus on regions with high growth potential<br>
                2. Expand farmer network in underserved regions<br>
                3. Leverage top regions as success stories<br>
                4. Monitor regional performance monthly<br>
                5. Customize marketing for regional preferences
            </p>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Regional Sales Distribution Map</div>
                <div class="chart-placeholder">
                    Chart: Geographic Performance Visualization
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-map"></i>
            <h3>No Regional Data</h3>
            <p>No regional performance data available</p>
        </div>
    @endif
@endsection
