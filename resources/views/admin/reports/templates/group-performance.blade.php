@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-users"></i> Lead Farmer Group Performance
    </div>

    @if(count($data) > 0)
        @php
            $totalGroups = count($data);
            $totalFarmers = collect($data)->sum('total_farmers_managed');
            $activeFarmers = collect($data)->sum('active_farmers');
            $totalRevenue = collect($data)->sum('total_revenue');
            $topGroup = collect($data)->sortByDesc('total_revenue')->first();
            $avgRevenuePerGroup = $totalRevenue / max($totalGroups, 1);
            $activeRate = ($activeFarmers / max($totalFarmers, 1)) * 100;
        @endphp

        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $totalGroups }}</div>
                <div class="stat-label">Total Groups</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $totalFarmers }}</div>
                <div class="stat-label">Total Farmers</div>
            </div>
            <div class="stat-card success">
                <div class="stat-value">{{ number_format($activeRate, 1) }}%</div>
                <div class="stat-label">Activation Rate</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">Rs. {{ number_format($totalRevenue, 2) }}</div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>

        <div class="highlight success">
            <h4>🏆 Top Performing Group</h4>
            <p>
                <strong>{{ $topGroup->lead_farmer_name ?? 'N/A' }}</strong> - {{ $topGroup->group_name ?? 'N/A' }}<br>
                Revenue: Rs. {{ number_format($topGroup->total_revenue ?? 0, 2) }}<br>
                Farmers: {{ $topGroup->total_farmers_managed ?? 0 }} ({{ $topGroup->active_farmers ?? 0 }} active)<br>
                Quantity Sold: {{ number_format($topGroup->total_quantity_sold ?? 0, 2) }} units
            </p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Lead Farmer</th>
                    <th>Group Name</th>
                    <th>Farmers</th>
                    <th>Active</th>
                    <th>Activation %</th>
                    <th>Quantity Sold</th>
                    <th>Revenue</th>
                    <th>Performance</th>
                    <th>Avg/Farmer</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $group)
                    @php
                        $activationRate = ($group->active_farmers / max($group->total_farmers_managed, 1)) * 100;
                        $avgPerFarmer = $group->total_farmers_managed > 0 ? $group->total_revenue / $group->total_farmers_managed : 0;
                        $avgPerActiveFarmer = $group->active_farmers > 0 ? $group->total_revenue / $group->active_farmers : 0;
                    @endphp
                    <tr>
                        <td>#{{ $index + 1 }}</td>
                        <td>{{ $group->lead_farmer_name }}</td>
                        <td>{{ $group->group_name }}</td>
                        <td>{{ $group->total_farmers_managed }}</td>
                        <td>{{ $group->active_farmers }}</td>
                        <td>{{ number_format($activationRate, 1) }}%</td>
                        <td>{{ number_format($group->total_quantity_sold, 2) }}</td>
                        <td class="numeric">Rs. {{ number_format($group->total_revenue, 2) }}</td>
                        <td>
                            @if($group->total_revenue >= $avgRevenuePerGroup * 1.5)
                                <span class="success">⭐ Excellent</span>
                            @elseif($group->total_revenue >= $avgRevenuePerGroup)
                                <span class="info">📈 Good</span>
                            @elseif($group->total_revenue >= $avgRevenuePerGroup * 0.5)
                                <span class="info">📊 Average</span>
                            @else
                                <span class="warning">📉 Needs Support</span>
                            @endif
                        </td>
                        <td class="numeric">Rs. {{ number_format($avgPerActiveFarmer, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="data-grid">
            <div class="grid-item">
                <div class="grid-label">Average Group Revenue</div>
                <div class="grid-value">Rs. {{ number_format($avgRevenuePerGroup, 2) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Average Farmers per Group</div>
                <div class="grid-value">{{ number_format($totalFarmers / max($totalGroups, 1), 1) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Revenue per Active Farmer</div>
                <div class="grid-value">Rs. {{ number_format($totalRevenue / max($activeFarmers, 1), 2) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Top 3 Groups Share</div>
                <div class="grid-value">
                    {{ number_format((collect($data)->sortByDesc('total_revenue')->take(3)->sum('total_revenue') / max($totalRevenue, 1)) * 100, 1) }}%
                </div>
            </div>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Group Performance Ranking</div>
                <div class="chart-placeholder">
                    Chart: Revenue Distribution by Group
                </div>
            </div>
        </div>

        <div class="note">
            <i class="fas fa-chart-line"></i>
            <p><strong>Performance Analysis:</strong>
                Groups with activation rates below 50% need support.
                Groups generating less than 25% of average revenue require intervention.
                Consider implementing mentorship programs between top and bottom performing groups.
            </p>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-users-slash"></i>
            <h3>No Group Data</h3>
            <p>No lead farmer group performance data available</p>
        </div>
    @endif
@endsection
