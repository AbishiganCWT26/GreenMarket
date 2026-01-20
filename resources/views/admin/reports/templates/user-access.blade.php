@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-user-shield"></i> User Access & Role Management
    </div>

    @if(count($data) > 0)
        @php
            $totalUsers = count($data);
            $activeUsers = collect($data)->where('is_active', true)->count();
            $inactiveUsers = collect($data)->where('is_active', false)->count();
            $neverLoggedIn = collect($data)->where('last_login', null)->count();
            $recentActive = collect($data)->where('last_login', '>=', now()->subDays(7))->count();
            $roleDistribution = collect($data)->groupBy('role')->map->count();
        @endphp

        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $totalUsers }}</div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card success">
                <div class="stat-value">{{ $activeUsers }}</div>
                <div class="stat-label">Active Users</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value">{{ $inactiveUsers }}</div>
                <div class="stat-label">Inactive Users</div>
            </div>
            <div class="stat-card info">
                <div class="stat-value">{{ $recentActive }}</div>
                <div class="stat-label">Active (7 days)</div>
            </div>
        </div>

        <div class="highlight {{ $neverLoggedIn > ($totalUsers * 0.1) ? 'warning' : 'success' }}">
            <h4>🔐 Access Control Status</h4>
            <p>
                Active user rate: <strong>{{ number_format(($activeUsers / max($totalUsers, 1)) * 100, 1) }}%</strong><br>
                Never logged in: <strong>{{ $neverLoggedIn }} users</strong>
                ({{ number_format(($neverLoggedIn / max($totalUsers, 1)) * 100, 1) }}%)<br>
                @if($neverLoggedIn > ($totalUsers * 0.1))
                    ⚠️ High number of dormant accounts requires review
                @else
                    ✅ Good account activation rate
                @endif
            </p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Last Login</th>
                    <th>Login Count</th>
                    <th>Account Status</th>
                    <th>Days Since Login</th>
                    <th>Access Frequency</th>
                    <th>Security Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $user)
                    @php
                        $daysSinceLogin = $user->last_login ? now()->diffInDays(Carbon\Carbon::parse($user->last_login)) : null;
                        $loginFrequency = $user->login_count > 0 ? $user->login_count / max($daysSinceLogin ?? 1, 1) : 0;
                    @endphp
                    <tr>
                        <td>{{ $user->username }}</td>
                        <td>
                            <span class="status-badge status-{{ str_replace('_', '-', $user->role) }}">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </td>
                        <td>
                            @if($user->last_login)
                                {{ date('M d, Y', strtotime($user->last_login)) }}
                            @else
                                <span class="warning">Never</span>
                            @endif
                        </td>
                        <td>{{ $user->login_count }}</td>
                        <td>
                            @if($user->is_active)
                                <span class="success">✅ Active</span>
                            @else
                                <span class="warning">❌ Inactive</span>
                            @endif
                        </td>
                        <td>
                            @if($daysSinceLogin === null)
                                <span class="warning">N/A</span>
                            @elseif($daysSinceLogin > 90)
                                <span class="warning">{{ $daysSinceLogin }} days</span>
                            @elseif($daysSinceLogin > 30)
                                <span class="info">{{ $daysSinceLogin }} days</span>
                            @else
                                <span class="success">{{ $daysSinceLogin }} days</span>
                            @endif
                        </td>
                        <td>
                            @if($loginFrequency >= 0.5)
                                <span class="success">Frequent</span>
                            @elseif($loginFrequency >= 0.1)
                                <span class="info">Regular</span>
                            @elseif($user->login_count > 0)
                                <span class="info">Occasional</span>
                            @else
                                <span class="warning">None</span>
                            @endif
                        </td>
                        <td>
                            @if($daysSinceLogin > 90 && $user->is_active)
                                <span class="warning">Review Access</span>
                            @elseif($user->last_login === null && $user->is_active)
                                <span class="warning">Verify Account</span>
                            @else
                                <span class="success">Secure</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="data-grid">
            <div class="grid-item">
                <div class="grid-label">Role Distribution</div>
                <div class="grid-value">
                    @foreach($roleDistribution as $role => $count)
                        {{ ucfirst($role) }}: {{ $count }}
                        @if(!$loop->last)<br>@endif
                    @endforeach
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Avg Logins per User</div>
                <div class="grid-value">{{ number_format(collect($data)->avg('login_count'), 1) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Dormant Accounts (>90 days)</div>
                <div class="grid-value">
                    {{ collect($data)->where('last_login', '<', now()->subDays(90))->count() }}
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Account Health Score</div>
                <div class="grid-value">
                    @php
                        $healthScore = (
                            ($activeUsers / $totalUsers * 40) +
                            ($recentActive / $totalUsers * 30) +
                            (1 - ($neverLoggedIn / $totalUsers) * 30)
                        );
                    @endphp
                    {{ number_format($healthScore, 1) }}%
                </div>
            </div>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">User Activity & Access Patterns</div>
                <div class="chart-placeholder">
                    Chart: Login Frequency Analysis
                </div>
            </div>
        </div>

        <div class="note">
            <i class="fas fa-shield-alt"></i>
            <p><strong>Security Recommendations:</strong>
                1. Review accounts inactive for >90 days<br>
                2. Verify accounts that have never logged in<br>
                3. Implement periodic password rotation for admin accounts<br>
                4. Monitor login patterns for unusual activity<br>
                5. Consider multi-factor authentication for high-privilege roles
            </p>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-user-lock"></i>
            <h3>No User Data</h3>
            <p>No user access data available</p>
        </div>
    @endif
@endsection
