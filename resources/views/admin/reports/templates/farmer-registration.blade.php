@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-user-plus"></i> Farmer Registration Status Report
    </div>

    @if(count($data) > 0)
        @php
            $totalFarmers = count($data);
            $completeProfiles = collect($data)->where('profile_status', 'Complete')->count();
            $incompleteProfiles = collect($data)->where('profile_status', 'Incomplete')->count();
            $activeFarmers = collect($data)->where('is_active', true)->count();
            $recentRegistrations = collect($data)->where('registration_date', '>=', now()->subDays(7))->count();
            $completionRate = ($completeProfiles / max($totalFarmers, 1)) * 100;
        @endphp

        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $totalFarmers }}</div>
                <div class="stat-label">Total Farmers</div>
            </div>
            <div class="stat-card success">
                <div class="stat-value">{{ $completeProfiles }}</div>
                <div class="stat-label">Complete Profiles</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value">{{ $incompleteProfiles }}</div>
                <div class="stat-label">Incomplete Profiles</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $recentRegistrations }}</div>
                <div class="stat-label">New (7 days)</div>
            </div>
        </div>

        <div class="highlight {{ $completionRate >= 80 ? 'success' : 'warning' }}">
            <h4>📋 Profile Completion Status</h4>
            <p>
                Overall completion rate: <strong>{{ number_format($completionRate, 1) }}%</strong><br>
                @if($completionRate >= 80)
                    ✅ Excellent profile completion rate
                @elseif($completionRate >= 60)
                    ⚠️ Good, but room for improvement
                @else
                    ❌ Low completion rate requires attention
                @endif
                <br>
                {{ $activeFarmers }} active farmers ({{ number_format(($activeFarmers / max($totalFarmers, 1)) * 100, 1) }}% activation)
            </p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Farmer Name</th>
                    <th>Registration Date</th>
                    <th>Profile Status</th>
                    <th>Product Listings</th>
                    <th>Account Status</th>
                    <th>Days Registered</th>
                    <th>Profile Score</th>
                    <th>Action Required</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $farmer)
                    @php
                        $daysRegistered = now()->diffInDays(Carbon\Carbon::parse($farmer->registration_date));
                        $profileScore = 0;
                        if ($farmer->profile_status == 'Complete') $profileScore += 50;
                        if ($farmer->product_listings > 0) $profileScore += 30;
                        if ($farmer->is_active) $profileScore += 20;
                    @endphp
                    <tr>
                        <td>{{ $farmer->name }}</td>
                        <td>{{ date('M d, Y', strtotime($farmer->registration_date)) }}</td>
                        <td>
                            @if($farmer->profile_status == 'Complete')
                                <span class="success">✅ Complete</span>
                            @else
                                <span class="warning">❌ Incomplete</span>
                            @endif
                        </td>
                        <td>{{ $farmer->product_listings }}</td>
                        <td>
                            @if($farmer->is_active)
                                <span class="success">✅ Active</span>
                            @else
                                <span class="warning">❌ Inactive</span>
                            @endif
                        </td>
                        <td>{{ $daysRegistered }} days</td>
                        <td>
                            @if($profileScore >= 80)
                                <span class="success">{{ $profileScore }}%</span>
                            @elseif($profileScore >= 60)
                                <span class="info">{{ $profileScore }}%</span>
                            @else
                                <span class="warning">{{ $profileScore }}%</span>
                            @endif
                        </td>
                        <td>
                            @if($farmer->profile_status == 'Incomplete' && $daysRegistered > 7)
                                <span class="warning">Complete Profile</span>
                            @elseif($farmer->product_listings == 0 && $daysRegistered > 14)
                                <span class="info">Add Products</span>
                            @elseif(!$farmer->is_active)
                                <span class="warning">Activate Account</span>
                            @else
                                <span class="success">No Action</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="data-grid">
            <div class="grid-item">
                <div class="grid-label">Average Days Registered</div>
                <div class="grid-value">{{ number_format(collect($data)->avg(function($f) { return now()->diffInDays(Carbon\Carbon::parse($f->registration_date)); }), 1) }} days</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Average Product Listings</div>
                <div class="grid-value">{{ number_format(collect($data)->avg('product_listings'), 1) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">New Farmers This Month</div>
                <div class="grid-value">{{ collect($data)->where('registration_date', '>=', now()->subDays(30))->count() }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Engagement Rate</div>
                <div class="grid-value">
                    @php
                        $engaged = collect($data)->where('product_listings', '>', 0)->where('is_active', true)->count();
                        $engagementRate = ($engaged / max($totalFarmers, 1)) * 100;
                    @endphp
                    {{ number_format($engagementRate, 1) }}%
                </div>
            </div>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Registration Trend & Profile Completion</div>
                <div class="chart-placeholder">
                    Chart: Registration Analysis
                </div>
            </div>
        </div>

        <div class="note">
            <i class="fas fa-bullhorn"></i>
            <p><strong>Onboarding Recommendations:</strong>
                1. Send reminder emails to farmers with incomplete profiles after 3 days<br>
                2. Offer training sessions for new farmers with zero product listings<br>
                3. Create incentives for complete profile submission within 7 days<br>
                4. Review inactive accounts monthly for reactivation opportunities
            </p>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-user-friends"></i>
            <h3>No Farmer Data</h3>
            <p>No farmer registration data available</p>
        </div>
    @endif
@endsection
