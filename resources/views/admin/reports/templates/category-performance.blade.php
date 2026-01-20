@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-chart-pie"></i> Product Category Performance
    </div>

    @if(count($data) > 0)
        @php
            $totalProducts = collect($data)->sum('total_products');
            $totalSold = collect($data)->sum('total_sold');
            $totalRevenue = collect($data)->sum('revenue');
            $bestCategory = collect($data)->sortByDesc('revenue')->first();
            $worstCategory = collect($data)->where('revenue', '>', 0)->sortBy('revenue')->first();
        @endphp

        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $totalProducts }}</div>
                <div class="stat-label">Total Products</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ number_format($totalSold, 2) }}</div>
                <div class="stat-label">Units Sold</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">Rs. {{ number_format($totalRevenue, 2) }}</div>
                <div class="stat-label">Total Revenue</div>
            </div>
            <div class="stat-card success">
                <div class="stat-value">{{ $bestCategory->category_name ?? 'N/A' }}</div>
                <div class="stat-label">Top Category</div>
            </div>
        </div>

        <div class="highlight info">
            <h4>📊 Category Performance Insights</h4>
            <p>
                🥇 <strong>Best Performer:</strong> {{ $bestCategory->category_name ?? 'N/A' }}
                (Rs. {{ number_format($bestCategory->revenue ?? 0, 2) }})<br>

                📉 <strong>Opportunity Area:</strong> {{ $worstCategory->category_name ?? 'N/A' }}
                (Rs. {{ number_format($worstCategory->revenue ?? 0, 2) }})<br>

                📈 <strong>Market Share:</strong> Top 3 categories generate
                {{ number_format((collect($data)->sortByDesc('revenue')->take(3)->sum('revenue') / max($totalRevenue, 1)) * 100, 1) }}% of revenue
            </p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Products</th>
                    <th>Units Sold</th>
                    <th>Revenue</th>
                    <th>Avg Price/Unit</th>
                    <th>Market Share</th>
                    <th>Performance</th>
                    <th>Recommendation</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $category)
                    @php
                        $marketShare = ($category->revenue / max($totalRevenue, 1)) * 100;
                        $avgPrice = $category->total_sold > 0 ? $category->revenue / $category->total_sold : 0;
                    @endphp
                    <tr>
                        <td>{{ $category->category_name }}</td>
                        <td>{{ $category->total_products }}</td>
                        <td>{{ number_format($category->total_sold, 2) }}</td>
                        <td class="numeric">Rs. {{ number_format($category->revenue, 2) }}</td>
                        <td class="numeric">Rs. {{ number_format($avgPrice, 2) }}</td>
                        <td>{{ number_format($marketShare, 1) }}%</td>
                        <td>
                            @if($marketShare >= 20)
                                <span class="success">⭐ Star</span>
                            @elseif($marketShare >= 10)
                                <span class="info">📈 Growing</span>
                            @elseif($marketShare >= 5)
                                <span class="info">📊 Stable</span>
                            @else
                                <span class="warning">📉 Needs Focus</span>
                            @endif
                        </td>
                        <td>
                            @if($marketShare >= 20)
                                <span class="success">Maintain</span>
                            @elseif($marketShare >= 10)
                                <span class="info">Promote</span>
                            @else
                                <span class="warning">Review</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Revenue by Product Category</div>
                <div class="chart-placeholder">
                    Chart: Category Performance Visualization
                </div>
            </div>
        </div>

        <div class="note">
            <i class="fas fa-lightbulb"></i>
            <p><strong>Strategic Recommendations:</strong>
                1. Invest in marketing for high-performing categories<br>
                2. Review inventory for underperforming categories<br>
                3. Consider bundle offers for complementary categories<br>
                4. Monitor seasonal trends in category performance
            </p>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-tags"></i>
            <h3>No Category Data</h3>
            <p>No product category performance data available</p>
        </div>
    @endif
@endsection
