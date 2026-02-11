@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-globe-asia"></i> Geographic Sales Density Analysis
    </div>

    @if(count($data) > 0)
        @php
            $totalRegions = count($data);
            $totalFarmers = collect($data)->sum('active_farmers');
            $totalBuyers = collect($data)->sum('active_buyers');
            $totalSales = collect($data)->sum('total_sales');
            $totalOrders = collect($data)->sum('number_of_orders');
            $topRegion = collect($data)->sortByDesc('total_sales')->first();
            $avgSalesPerRegion = $totalSales / max($totalRegions, 1);
            $avgOrdersPerRegion = $totalOrders / max($totalRegions, 1);
            $marketPenetration = ($totalRegions / 25) * 100; // Assuming 25 districts in Sri Lanka
        @endphp

        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $totalRegions }}</div>
                <div class="stat-label">Active Regions</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $totalFarmers }}</div>
                <div class="stat-label">Active Farmers</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $totalBuyers }}</div>
                <div class="stat-label">Active Buyers</div>
            </div>
            <div class="stat-card success">
                <div class="stat-value">Rs. {{ number_format($totalSales, 2) }}</div>
                <div class="stat-label">Total Sales</div>
            </div>
        </div>

        <div class="highlight success">
            <h4>📍 Top Performing Region</h4>
            <p>
                <strong>{{ $topRegion->region ?? 'N/A' }}</strong><br>
                Sales: <strong>Rs. {{ number_format($topRegion->total_sales ?? 0, 2) }}</strong><br>
                Farmers: {{ $topRegion->active_farmers ?? 0 }} | Buyers: {{ $topRegion->active_buyers ?? 0 }}<br>
                Orders: {{ $topRegion->number_of_orders ?? 0 }}<br>
                Market Share: {{ number_format(($topRegion->total_sales ?? 0) / max($totalSales, 1) * 100, 1) }}%
            </p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Region</th>
                    <th>Active Farmers</th>
                    <th>Active Buyers</th>
                    <th>Total Sales</th>
                    <th>Orders</th>
                    <th>Avg Order Value</th>
                    <th>Market Share</th>
                    <th>Density Index</th>
                    <th>Growth Potential</th>
                    <th>Strategy</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $region)
                    @php
                        $marketShare = ($region->total_sales / max($totalSales, 1)) * 100;
                        $avgOrderValue = $region->number_of_orders > 0 ? $region->total_sales / $region->number_of_orders : 0;
                        $densityIndex = $region->active_farmers > 0 ? $region->total_sales / $region->active_farmers : 0;
                        $avgDensity = $totalFarmers > 0 ? $totalSales / $totalFarmers : 0;
                        $buyerPenetration = $region->active_farmers > 0 ? $region->active_buyers / $region->active_farmers : 0;
                        $avgPenetration = $totalFarmers > 0 ? $totalBuyers / $totalFarmers : 0;
                    @endphp
                    <tr>
                        <td>{{ $region->region }}</td>
                        <td>{{ $region->active_farmers }}</td>
                        <td>{{ $region->active_buyers }}</td>
                        <td class="numeric">Rs. {{ number_format($region->total_sales, 2) }}</td>
                        <td>{{ $region->number_of_orders }}</td>
                        <td class="numeric">Rs. {{ number_format($avgOrderValue, 2) }}</td>
                        <td>{{ number_format($marketShare, 1) }}%</td>
                        <td>
                            @if($densityIndex >= $avgDensity * 1.5)
                                <span class="success">⭐⭐⭐⭐⭐</span>
                            @elseif($densityIndex >= $avgDensity * 1.2)
                                <span class="success">⭐⭐⭐⭐</span>
                            @elseif($densityIndex >= $avgDensity)
                                <span class="info">⭐⭐⭐</span>
                            @elseif($densityIndex >= $avgDensity * 0.8)
                                <span class="info">⭐⭐</span>
                            @else
                                <span class="warning">⭐</span>
                            @endif
                        </td>
                        <td>
                            @if($buyerPenetration >= $avgPenetration * 1.5)
                                <span class="success">Mature</span>
                            @elseif($buyerPenetration >= $avgPenetration)
                                <span class="info">Growing</span>
                            @else
                                <span class="warning">Emerging</span>
                            @endif
                        </td>
                        <td>
                            @if($marketShare >= 15)
                                <span class="success">Optimize</span>
                            @elseif($marketShare >= 5)
                                <span class="info">Expand</span>
                            @elseif($region->active_farmers == 0)
                                <span class="warning">Enter</span>
                            @else
                                <span class="info">Develop</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="data-grid">
            <div class="grid-item">
                <div class="grid-label">National Coverage</div>
                <div class="grid-value">{{ number_format($marketPenetration, 1) }}%</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Avg Sales per Region</div>
                <div class="grid-value">Rs. {{ number_format($avgSalesPerRegion, 2) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Top 3 Regions Share</div>
                <div class="grid-value">
                    {{ number_format((collect($data)->sortByDesc('total_sales')->take(3)->sum('total_sales') / max($totalSales, 1)) * 100, 1) }}%
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Buyer:Farmer Ratio</div>
                <div class="grid-value">{{ number_format($totalFarmers > 0 ? $totalBuyers / $totalFarmers : 0, 2) }}:1</div>
            </div>
        </div>

        <div class="note info">
            <i class="fas fa-map-marked-alt"></i>
            <p><strong>📈 Geographic Expansion Strategy:</strong></p>
            <ol>
                <li><strong>Core Markets (Market Share > 10%):</strong> Focus on retention and upselling</li>
                <li><strong>Growth Markets (Market Share 5-10%):</strong> Increase marketing and farmer onboarding</li>
                <li><strong>Emerging Markets (Market Share < 5%):</strong> Targeted campaigns and partnerships</li>
                <li><strong>Untapped Markets (No presence):</strong> Strategic entry with pilot programs</li>
            </ol>
        </div>

        <div class="note">
            <i class="fas fa-bullseye"></i>
            <p><strong>Regional Tactics:</strong>
                1. Use top-performing regions as case studies<br>
                2. Customize marketing for regional preferences<br>
                3. Leverage local influencers in key regions<br>
                4. Monitor regional competition monthly<br>
                5. Adjust pricing strategy by region based on density
            </p>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Geographic Sales Distribution Heat Map</div>
                <div class="chart-placeholder">
                    Chart: Regional Performance Visualization
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-map"></i>
            <h3>No Geographic Data</h3>
            <p>No geographic sales distribution data available</p>
        </div>
    @endif
@endsection
