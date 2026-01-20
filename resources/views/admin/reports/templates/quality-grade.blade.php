@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-star"></i> Quality Grade Performance Analysis
    </div>

    @if(count($data) > 0)
        @php
            $totalProducts = collect($data)->sum('total_products');
            $totalSold = collect($data)->sum('total_sold');
            $totalRevenue = collect($data)->sum(function($grade) { return $grade->total_sold * $grade->avg_price; });
            $popularGrade = collect($data)->sortByDesc('total_sold')->first();
            $premiumGrade = collect($data)->sortByDesc('avg_price')->first();
            $sellThroughRate = collect($data)->avg('sell_through_rate');
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
                <div class="stat-value">{{ $popularGrade->quality_grade ?? 'N/A' }}</div>
                <div class="stat-label">Most Popular</div>
            </div>
        </div>

        <div class="highlight info">
            <h4>⭐ Quality Insights</h4>
            <p>
                🏆 <strong>Best Seller:</strong> {{ $popularGrade->quality_grade ?? 'N/A' }}
                ({{ $popularGrade->total_sold ?? 0 }} units)<br>

                💎 <strong>Premium Tier:</strong> {{ $premiumGrade->quality_grade ?? 'N/A' }}
                (Rs. {{ number_format($premiumGrade->avg_price ?? 0, 2) }}/unit)<br>

                📈 <strong>Sell-Through Rate:</strong> {{ number_format($sellThroughRate, 1) }}%<br>

                💰 <strong>Revenue Distribution:</strong>
                @php
                    $topGrade = collect($data)->sortByDesc(function($grade) { return $grade->total_sold * $grade->avg_price; })->first();
                    $topGradeRevenue = $topGrade ? ($topGrade->total_sold * $topGrade->avg_price) : 0;
                    $topGradeShare = ($topGradeRevenue / max($totalRevenue, 1)) * 100;
                @endphp
                {{ $topGrade->quality_grade ?? 'N/A' }} generates {{ number_format($topGradeShare, 1) }}% of revenue
            </p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Quality Grade</th>
                    <th>Products</th>
                    <th>Units Sold</th>
                    <th>Avg Price/Unit</th>
                    <th>Revenue</th>
                    <th>Sell-Through Rate</th>
                    <th>Market Position</th>
                    <th>Profit Margin</th>
                    <th>Strategy</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $grade)
                    @php
                        $revenue = $grade->total_sold * $grade->avg_price;
                        $marketShare = ($revenue / max($totalRevenue, 1)) * 100;
                        $avgSellThrough = collect($data)->avg('sell_through_rate');
                    @endphp
                    <tr>
                        <td>
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= str_replace('Grade ', '', $grade->quality_grade))
                                    ⭐
                                @else
                                    ☆
                                @endif
                            @endfor
                            {{ $grade->quality_grade }}
                        </td>
                        <td>{{ $grade->total_products }}</td>
                        <td>{{ number_format($grade->total_sold, 2) }}</td>
                        <td class="numeric">Rs. {{ number_format($grade->avg_price, 2) }}</td>
                        <td class="numeric">Rs. {{ number_format($revenue, 2) }}</td>
                        <td>{{ number_format($grade->sell_through_rate, 1) }}%</td>
                        <td>
                            @if($marketShare >= 30)
                                <span class="success">Market Leader</span>
                            @elseif($marketShare >= 15)
                                <span class="info">Strong Performer</span>
                            @elseif($marketShare >= 5)
                                <span class="info">Niche Player</span>
                            @else
                                <span class="warning">Emerging</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $marginIndex = $grade->avg_price / collect($data)->avg('avg_price');
                            @endphp
                            @if($marginIndex >= 1.5)
                                <span class="success">Premium</span>
                            @elseif($marginIndex >= 1.0)
                                <span class="info">Standard</span>
                            @else
                                <span class="info">Economy</span>
                            @endif
                        </td>
                        <td>
                            @if($grade->sell_through_rate >= 80)
                                <span class="success">Expand</span>
                            @elseif($grade->sell_through_rate >= 50)
                                <span class="info">Maintain</span>
                            @else
                                <span class="warning">Review</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="data-grid">
            <div class="grid-item">
                <div class="grid-label">Avg Price Across Grades</div>
                <div class="grid-value">Rs. {{ number_format(collect($data)->avg('avg_price'), 2) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Price Range</div>
                <div class="grid-value">
                    Rs. {{ number_format(collect($data)->min('avg_price'), 2) }} -
                    Rs. {{ number_format(collect($data)->max('avg_price'), 2) }}
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Grade Diversity</div>
                <div class="grid-value">{{ count($data) }} grades</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Premium Adoption</div>
                <div class="grid-value">
                    @php
                        $premiumSold = collect($data)->where('avg_price', '>=', collect($data)->avg('avg_price') * 1.3)->sum('total_sold');
                        $premiumRate = ($premiumSold / max($totalSold, 1)) * 100;
                    @endphp
                    {{ number_format($premiumRate, 1) }}%
                </div>
            </div>
        </div>

        <div class="note">
            <i class="fas fa-chart-line"></i>
            <p><strong>Quality Strategy:</strong>
                1. Promote premium grades for higher margins<br>
                2. Bundle lower grades with popular items<br>
                3. Educate buyers on quality differences<br>
                4. Monitor sell-through rates for inventory planning<br>
                5. Consider introducing intermediate grades
            </p>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Price vs Sales Volume by Quality Grade</div>
                <div class="chart-placeholder">
                    Chart: Quality Grade Performance Matrix
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-star-half-alt"></i>
            <h3>No Quality Data</h3>
            <p>No quality grade performance data available</p>
        </div>
    @endif
@endsection
