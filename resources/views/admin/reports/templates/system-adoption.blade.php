@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-chart-user"></i> System Adoption & User Statistics
    </div>

    @if(count($data) > 0)
        @php
            $totalUsers = collect($data)->sum('total_users');
            $activeUsers = collect($data)->sum('active_users');
            $newUsersWeek = collect($data)->sum('new_registrations_week');
            $activeRate = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 2) : 0;
        @endphp

        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $totalUsers }}</div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $activeUsers }}</div>
                <div class="stat-label">Active Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $newUsersWeek }}</div>
                <div class="stat-label">New Users (Week)</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $activeRate }}%</div>
                <div class="stat-label">Activation Rate</div>
            </div>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>User Role</th>
                    <th>Total Users</th>
                    <th>Active Users</th>
                    <th>New Users (Week)</th>
                    <th>Active Accounts</th>
                    <th>Activation Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $role)
                    @php
                        $roleActiveRate = $role->total_users > 0 ? round(($role->active_users / $role->total_users) * 100, 2) : 0;
                    @endphp
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $role->role)) }}</td>
                        <td>{{ $role->total_users }}</td>
                        <td>{{ $role->active_users }}</td>
                        <td>{{ $role->new_registrations_week }}</td>
                        <td>{{ $role->active_accounts }}</td>
                        <td>
                            @if($roleActiveRate < 50)
                                <span class="warning">{{ $roleActiveRate }}%</span>
                            @elseif($roleActiveRate < 80)
                                <span class="info">{{ $roleActiveRate }}%</span>
                            @else
                                <span class="success">{{ $roleActiveRate }}%</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="highlight">
            <h4>System Health Indicators</h4>
            <p>
                System adoption rate: {{ $activeRate }}%<br>
                Weekly growth: {{ $newUsersWeek }} new users<br>
                Overall system health:
                @if($activeRate >= 80)
                    <span class="success">Excellent</span>
                @elseif($activeRate >= 60)
                    <span class="info">Good</span>
                @else
                    <span class="warning">Needs Improvement</span>
                @endif
            </p>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">User Distribution by Role</div>
                <div class="chart-placeholder">
                    Chart: User Role Distribution
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-users"></i>
            <h3>No User Data</h3>
            <p>No user statistics available for the selected period</p>
        </div>
    @endif
@endsection
