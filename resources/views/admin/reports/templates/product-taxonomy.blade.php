@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-tags"></i> Product Taxonomy & Hierarchy Analysis
    </div>

    @if(count($data) > 0)
        @php
            $totalCategories = collect($data)->unique('category_name')->count();
            $totalSubcategories = collect($data)->unique('subcategory_name')->count();
            $totalListings = collect($data)->sum('listings_count');
            $totalSales = collect($data)->sum('total_sales');
            $activeCategories = collect($data)->where('category_active', true)->unique('category_name')->count();
            $activeSubcategories = collect($data)->where('subcategory_active', true)->unique('subcategory_name')->count();
            $topCategory = collect($data)->groupBy('category_name')->map->sum('total_sales')->sortDesc()->first();
            $topCategoryName = collect($data)->groupBy('category_name')->map->sum('total_sales')->sortDesc()->keys()->first();
            $avgListingsPerSubcategory = $totalSubcategories > 0 ? $totalListings / $totalSubcategories : 0;
        @endphp

        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $totalCategories }}</div>
                <div class="stat-label">Main Categories</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $totalSubcategories }}</div>
                <div class="stat-label">Subcategories</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $totalListings }}</div>
                <div class="stat-label">Product Listings</div>
            </div>
            <div class="stat-card success">
                <div class="stat-value">Rs. {{ number_format($totalSales, 2) }}</div>
                <div class="stat-label">Total Sales</div>
            </div>
        </div>

        <div class="highlight success">
            <h4>🏆 Top Performing Category</h4>
            <p>
                <strong>{{ $topCategoryName ?? 'N/A' }}</strong><br>
                Sales: <strong>Rs. {{ number_format($topCategory ?? 0, 2) }}</strong><br>
                Market Share: {{ number_format(($topCategory ?? 0) / max($totalSales, 1) * 100, 1) }}%<br>
                Active Subcategories: {{ collect($data)->where('category_name', $topCategoryName)->where('subcategory_active', true)->unique('subcategory_name')->count() }}<br>
                Avg Listings per Subcategory: {{ number_format(collect($data)->where('category_name', $topCategoryName)->avg('listings_count'), 1) }}
            </p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Main Category</th>
                    <th>Category Status</th>
                    <th>Subcategory</th>
                    <th>Subcategory Status</th>
                    <th>Example Product</th>
                    <th>Listings</th>
                    <th>Sales Value</th>
                    <th>Avg Listing Value</th>
                    <th>Depth Utilization</th>
                    <th>Taxonomy Health</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                    @php
                        $avgListingValue = $item->listings_count > 0 ? $item->total_sales / $item->listings_count : 0;
                        $depthScore = 0;
                        if($item->listings_count >= 10) $depthScore += 40;
                        elseif($item->listings_count >= 5) $depthScore += 20;
                        if($item->total_sales > 0) $depthScore += 40;
                        if($item->category_active && $item->subcategory_active) $depthScore += 20;

                        if($depthScore >= 80) $depthUtilization = 'Excellent';
                        elseif($depthScore >= 60) $depthUtilization = 'Good';
                        elseif($depthScore >= 40) $depthUtilization = 'Fair';
                        else $depthUtilization = 'Poor';

                        $taxonomyHealth = '';
                        if(!$item->category_active) {
                            $taxonomyHealth = 'Inactive Category';
                        } elseif(!$item->subcategory_active) {
                            $taxonomyHealth = 'Inactive Subcategory';
                        } elseif($item->listings_count == 0) {
                            $taxonomyHealth = 'No Listings';
                        } elseif($item->total_sales == 0) {
                            $taxonomyHealth = 'No Sales';
                        } else {
                            $taxonomyHealth = 'Healthy';
                        }
                    @endphp
                    <tr>
                        <td>{{ $item->category_name }}</td>
                        <td>
                            @if($item->category_active)
                                <span class="success">✅ Active</span>
                            @else
                                <span class="warning">❌ Inactive</span>
                            @endif
                        </td>
                        <td>{{ $item->subcategory_name }}</td>
                        <td>
                            @if($item->subcategory_active)
                                <span class="success">✅ Active</span>
                            @else
                                <span class="warning">❌ Inactive</span>
                            @endif
                        </td>
                        <td>{{ $item->example_name ?? 'No example' }}</td>
                        <td>{{ $item->listings_count }}</td>
                        <td class="numeric">Rs. {{ number_format($item->total_sales, 2) }}</td>
                        <td class="numeric">Rs. {{ number_format($avgListingValue, 2) }}</td>
                        <td>
                            @if($depthUtilization == 'Excellent')
                                <span class="success">⭐⭐⭐⭐⭐</span>
                            @elseif($depthUtilization == 'Good')
                                <span class="success">⭐⭐⭐⭐</span>
                            @elseif($depthUtilization == 'Fair')
                                <span class="info">⭐⭐⭐</span>
                            @else
                                <span class="warning">⭐⭐</span>
                            @endif
                        </td>
                        <td>
                            @if($taxonomyHealth == 'Healthy')
                                <span class="success">✅ Healthy</span>
                            @elseif($taxonomyHealth == 'No Sales')
                                <span class="info">⚠️ No Sales</span>
                            @elseif($taxonomyHealth == 'No Listings')
                                <span class="warning">⚠️ No Listings</span>
                            @else
                                <span class="warning">❌ {{ $taxonomyHealth }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="data-grid">
            <div class="grid-item">
                <div class="grid-label">Active Categories</div>
                <div class="grid-value">{{ $activeCategories }}/{{ $totalCategories }} ({{ number_format(($activeCategories / max($totalCategories, 1)) * 100, 1) }}%)</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Active Subcategories</div>
                <div class="grid-value">{{ $activeSubcategories }}/{{ $totalSubcategories }} ({{ number_format(($activeSubcategories / max($totalSubcategories, 1)) * 100, 1) }}%)</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Avg Listings per Subcategory</div>
                <div class="grid-value">{{ number_format($avgListingsPerSubcategory, 1) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Taxonomy Coverage</div>
                <div class="grid-value">
                    @php
                        $categoriesWithSales = collect($data)->groupBy('category_name')->filter(function($cat) {
                            return $cat->sum('total_sales') > 0;
                        })->count();
                        $coverageRate = ($categoriesWithSales / max($totalCategories, 1)) * 100;
                    @endphp
                    {{ number_format($coverageRate, 1) }}%
                </div>
            </div>
        </div>

        <div class="note info">
            <i class="fas fa-sitemap"></i>
            <p><strong>📋 Taxonomy Optimization Recommendations:</strong></p>
            <ol>
                <li><strong>High-Performance Categories (Sales > Rs. 50,000):</strong> Expand subcategory depth and product variety</li>
                <li><strong>Underutilized Categories (Active but low sales):</strong> Review pricing, marketing, and product quality</li>
                <li><strong>Inactive Categories/Subcategories:</strong> Consider reactivation or consolidation</li>
                <li><strong>Categories with No Listings:</strong> Prioritize farmer onboarding in these areas</li>
                <li><strong>Emerging Opportunities:</strong> Identify gaps in taxonomy for new product introductions</li>
            </ol>
        </div>

        <div class="note">
            <i class="fas fa-lightbulb"></i>
            <p><strong>Taxonomy Management:</strong>
                1. Regular review of inactive taxonomy elements<br>
                2. Prune categories with no activity for 6+ months<br>
                3. Add new categories based on market demand<br>
                4. Ensure each category has sufficient product examples<br>
                5. Monitor category performance quarterly
            </p>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Taxonomy Hierarchy & Performance Mapping</div>
                <div class="chart-placeholder">
                    Chart: Category/Subcategory Performance Tree
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-tags"></i>
            <h3>No Taxonomy Data</h3>
            <p>No product taxonomy data available for analysis</p>
        </div>
    @endif
@endsection
