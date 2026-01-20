@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-user-clock"></i> Buyer Payment Behavior Analysis
    </div>

    @if(count($data) > 0)
        @php
            $totalBuyers = count($data);
            $totalOrders = collect($data)->sum('total_orders');
            $totalCODOrders = collect($data)->sum('cod_orders');
            $avgCompletionRate = collect($data)->avg('cod_completion_rate');
            $avgPaymentTime = collect($data)->avg('avg_payment_time');
            $reliableBuyers = collect($data)->where('cod_completion_rate', '>=', 90)->count();
            $riskBuyers = collect($data)->where('cod_completion_rate', '<', 70)->count();
            $codAdoptionRate = ($totalCODOrders / max($totalOrders, 1)) * 100;
        @endphp

        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $totalBuyers }}</div>
                <div class="stat-label">Active Buyers</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $totalOrders }}</div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card info">
                <div class="stat-value">{{ $totalCODOrders }}</div>
                <div class="stat-label">COD Orders</div>
            </div>
            <div class="stat-card success">
                <div class="stat-value">{{ number_format($avgCompletionRate, 1) }}%</div>
                <div class="stat-label">Avg Completion Rate</div>
            </div>
        </div>

        <div class="highlight {{ $avgCompletionRate >= 90 ? 'success' : ($avgCompletionRate >= 80 ? 'info' : 'warning') }}">
            <h4>💰 Payment Behavior Overview</h4>
            <p>
                COD Adoption Rate: <strong>{{ number_format($codAdoptionRate, 1) }}%</strong><br>
                Average Completion Rate: <strong>{{ number_format($avgCompletionRate, 1) }}%</strong><br>
                Average Payment Time: <strong>{{ number_format($avgPaymentTime, 1) }} days</strong><br>
                Buyer Segmentation: <strong>{{ $reliableBuyers }} Reliable</strong> | <strong>{{ $riskBuyers }} At-Risk</strong><br>
                @if($avgCompletionRate >= 90)
                    ✅ Excellent payment behavior across buyers
                @elseif($avgCompletionRate >= 80)
                    ⚠️ Good, but monitor at-risk buyers
                @else
                    ❌ Payment behavior needs improvement
                @endif
            </p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Buyer Name</th>
                    <th>Total Orders</th>
                    <th>COD Orders</th>
                    <th>COD Completion Rate</th>
                    <th>Avg Payment Time</th>
                    <th>COD Preference</th>
                    <th>Payment Reliability</th>
                    <th>Risk Category</th>
                    <th>Customer Value</th>
                    <th>Action Plan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $buyer)
                    @php
                        $codPreference = $buyer->total_orders > 0 ? ($buyer->cod_orders / $buyer->total_orders) * 100 : 0;
                        $customerValue = $buyer->total_orders * 1000; // Simplified value calculation

                        // Reliability assessment
                        if($buyer->cod_completion_rate >= 95) {
                            $reliability = 'Excellent';
                            $riskCategory = 'Low';
                        } elseif($buyer->cod_completion_rate >= 85) {
                            $reliability = 'Good';
                            $riskCategory = 'Low';
                        } elseif($buyer->cod_completion_rate >= 75) {
                            $reliability = 'Fair';
                            $riskCategory = 'Medium';
                        } else {
                            $reliability = 'Poor';
                            $riskCategory = 'High';
                        }

                        // Payment speed assessment
                        if($buyer->avg_payment_time <= 2) {
                            $paymentSpeed = 'Fast';
                        } elseif($buyer->avg_payment_time <= 5) {
                            $paymentSpeed = 'Average';
                        } else {
                            $paymentSpeed = 'Slow';
                        }
                    @endphp
                    <tr>
                        <td>{{ $buyer->name }}</td>
                        <td>{{ $buyer->total_orders }}</td>
                        <td>{{ $buyer->cod_orders }}</td>
                        <td>
                            @if($buyer->cod_completion_rate >= 95)
                                <span class="success">{{ number_format($buyer->cod_completion_rate, 1) }}%</span>
                            @elseif($buyer->cod_completion_rate >= 85)
                                <span class="info">{{ number_format($buyer->cod_completion_rate, 1) }}%</span>
                            @elseif($buyer->cod_completion_rate >= 75)
                                <span class="info">{{ number_format($buyer->cod_completion_rate, 1) }}%</span>
                            @else
                                <span class="warning">{{ number_format($buyer->cod_completion_rate, 1) }}%</span>
                            @endif
                        </td>
                        <td>
                            @if($buyer->avg_payment_time <= 2)
                                <span class="success">{{ number_format($buyer->avg_payment_time, 1) }} days</span>
                            @elseif($buyer->avg_payment_time <= 5)
                                <span class="info">{{ number_format($buyer->avg_payment_time, 1) }} days</span>
                            @else
                                <span class="warning">{{ number_format($buyer->avg_payment_time, 1) }} days</span>
                            @endif
                        </td>
                        <td>
                            @if($codPreference >= 80)
                                <span class="info">Heavy COD User</span>
                            @elseif($codPreference >= 50)
                                <span class="info">Mixed</span>
                            @else
                                <span class="success">Prefers Online</span>
                            @endif
                        </td>
                        <td>
                            @if($reliability == 'Excellent')
                                <span class="success">⭐⭐⭐⭐⭐</span>
                            @elseif($reliability == 'Good')
                                <span class="success">⭐⭐⭐⭐</span>
                            @elseif($reliability == 'Fair')
                                <span class="info">⭐⭐⭐</span>
                            @else
                                <span class="warning">⭐⭐</span>
                            @endif
                        </td>
                        <td>
                            @if($riskCategory == 'Low')
                                <span class="success">🟢 Low</span>
                            @elseif($riskCategory == 'Medium')
                                <span class="info">🟡 Medium</span>
                            @else
                                <span class="warning">🔴 High</span>
                            @endif
                        </td>
                        <td>
                            @if($customerValue >= 10000)
                                <span class="success">VIP</span>
                            @elseif($customerValue >= 5000)
                                <span class="info">Valued</span>
                            @elseif($customerValue >= 1000)
                                <span class="info">Regular</span>
                            @else
                                <span class="info">New</span>
                            @endif
                        </td>
                        <td>
                            @if($riskCategory == 'High')
                                <span class="warning">Restrict COD</span>
                            @elseif($riskCategory == 'Medium')
                                <span class="info">Monitor</span>
                            @elseif($customerValue >= 10000)
                                <span class="success">Reward</span>
                            @else
                                <span class="success">Standard</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="data-grid">
            <div class="grid-item">
                <div class="grid-label">COD Adoption Rate</div>
                <div class="grid-value">{{ number_format($codAdoptionRate, 1) }}%</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Reliable Buyers (>90% completion)</div>
                <div class="grid-value">{{ $reliableBuyers }} ({{ number_format(($reliableBuyers / max($totalBuyers, 1)) * 100, 1) }}%)</div>
            </div>
            <div class="grid-item warning">
                <div class="grid-label">At-Risk Buyers (<70% completion)</div>
                <div class="grid-value">{{ $riskBuyers }} ({{ number_format(($riskBuyers / max($totalBuyers, 1)) * 100, 1) }}%)</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Avg Orders per Buyer</div>
                <div class="grid-value">{{ number_format($totalOrders / max($totalBuyers, 1), 1) }}</div>
            </div>
        </div>

        <div class="note info">
            <i class="fas fa-chart-line"></i>
            <p><strong>📊 Buyer Segmentation Strategy:</strong></p>
            <ol>
                <li><strong>VIP Buyers (High value, high reliability):</strong> Offer premium benefits and priority service</li>
                <li><strong>Growth Buyers (Medium value, good reliability):</strong> Focus on increasing order frequency</li>
                <li><strong>At-Risk Buyers (Payment issues):</strong> Implement COD restrictions and closer monitoring</li>
                <li><strong>New Buyers (Limited history):</strong> Gradual credit limits with proven reliability</li>
            </ol>
        </div>

        <div class="note">
            <i class="fas fa-cog"></i>
            <p><strong>Risk Management:</strong>
                1. Implement tiered COD limits based on reliability scores<br>
                2. Automate payment reminders for slow-paying buyers<br>
                3. Offer incentives for early payments<br>
                4. Review buyer risk scores monthly<br>
                5. Escalate high-risk cases for manual review
            </p>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Buyer Payment Behavior Segmentation</div>
                <div class="chart-placeholder">
                    Chart: Reliability vs Payment Speed Matrix
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-users"></i>
            <h3>No Buyer Data</h3>
            <p>No buyer payment behavior data available</p>
        </div>
    @endif
@endsection
