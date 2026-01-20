@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-exclamation-circle"></i> Farmer Payment Delay Risk Assessment
    </div>

    @if(count($data) > 0)
        @php
            $totalFarmers = count($data);
            $totalOutstanding = collect($data)->sum('outstanding_amount');
            $avgPaymentDelay = collect($data)->avg('avg_payment_delay');
            $highRiskFarmers = collect($data)->where('avg_payment_delay', '>', 14)->count();
            $mediumRiskFarmers = collect($data)->whereBetween('avg_payment_delay', [8, 14])->count();
            $lowRiskFarmers = collect($data)->where('avg_payment_delay', '<', 8)->count();
            $totalSales = collect($data)->sum('total_sales');
            $riskExposure = ($totalOutstanding / max($totalSales, 1)) * 100;
        @endphp

        <div class="summary-stats">
            <div class="stat-card warning">
                <div class="stat-value">{{ $totalFarmers }}</div>
                <div class="stat-label">At-Risk Farmers</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value">Rs. {{ number_format($totalOutstanding, 2) }}</div>
                <div class="stat-label">Outstanding Amount</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ number_format($avgPaymentDelay, 1) }}</div>
                <div class="stat-label">Avg Delay Days</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value">{{ number_format($riskExposure, 1) }}%</div>
                <div class="stat-label">Risk Exposure</div>
            </div>
        </div>

        <div class="highlight {{ $riskExposure <= 5 ? 'success' : ($riskExposure <= 10 ? 'info' : 'warning') }}">
            <h4>⚠️ Payment Delay Risk Analysis</h4>
            <p>
                Total Outstanding: <strong>Rs. {{ number_format($totalOutstanding, 2) }}</strong><br>
                Average Payment Delay: <strong>{{ number_format($avgPaymentDelay, 1) }} days</strong><br>
                Risk Distribution: <strong>{{ $highRiskFarmers }} High</strong> | <strong>{{ $mediumRiskFarmers }} Medium</strong> | <strong>{{ $lowRiskFarmers }} Low</strong><br>
                @if($riskExposure <= 5)
                    ✅ Low financial risk exposure
                @elseif($riskExposure <= 10)
                    ⚠️ Moderate risk, monitor closely
                @else
                    ❌ High risk exposure requires immediate action
                @endif
            </p>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Farmer Name</th>
                    <th>Total Sales</th>
                    <th>Avg Payment Delay</th>
                    <th>Last Payment Date</th>
                    <th>Outstanding Amount</th>
                    <th>Days Since Last Payment</th>
                    <th>Risk Level</th>
                    <th>Payment Pattern</th>
                    <th>Priority</th>
                    <th>Action Plan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $farmer)
                    @php
                        $daysSincePayment = $farmer->last_payment_date ? now()->diffInDays(Carbon\Carbon::parse($farmer->last_payment_date)) : null;
                        $outstandingPercent = $farmer->total_sales > 0 ? ($farmer->outstanding_amount / $farmer->total_sales) * 100 : 0;

                        // Risk assessment
                        $riskScore = 0;
                        if($farmer->avg_payment_delay > 14) $riskScore += 40;
                        elseif($farmer->avg_payment_delay > 7) $riskScore += 20;

                        if($outstandingPercent > 20) $riskScore += 40;
                        elseif($outstandingPercent > 10) $riskScore += 20;

                        if($daysSincePayment > 30) $riskScore += 20;

                        if($riskScore >= 80) $riskLevel = 'High';
                        elseif($riskScore >= 50) $riskLevel = 'Medium';
                        else $riskLevel = 'Low';
                    @endphp
                    <tr>
                        <td>{{ $farmer->name }}</td>
                        <td class="numeric">Rs. {{ number_format($farmer->total_sales, 2) }}</td>
                        <td>
                            @if($farmer->avg_payment_delay > 14)
                                <span class="warning">{{ number_format($farmer->avg_payment_delay, 1) }} days</span>
                            @elseif($farmer->avg_payment_delay > 7)
                                <span class="info">{{ number_format($farmer->avg_payment_delay, 1) }} days</span>
                            @else
                                <span class="success">{{ number_format($farmer->avg_payment_delay, 1) }} days</span>
                            @endif
                        </td>
                        <td>
                            @if($farmer->last_payment_date)
                                {{ date('M d, Y', strtotime($farmer->last_payment_date)) }}
                            @else
                                <span class="warning">No payments</span>
                            @endif
                        </td>
                        <td class="numeric">
                            @if($farmer->outstanding_amount > 0)
                                <span class="warning">Rs. {{ number_format($farmer->outstanding_amount, 2) }}</span>
                            @else
                                <span class="success">Rs. 0.00</span>
                            @endif
                        </td>
                        <td>
                            @if($daysSincePayment === null)
                                <span class="warning">N/A</span>
                            @elseif($daysSincePayment > 30)
                                <span class="warning">{{ $daysSincePayment }} days</span>
                            @elseif($daysSincePayment > 14)
                                <span class="info">{{ $daysSincePayment }} days</span>
                            @else
                                <span class="success">{{ $daysSincePayment }} days</span>
                            @endif
                        </td>
                        <td>
                            @if($riskLevel == 'High')
                                <span class="warning">🔴 High Risk</span>
                            @elseif($riskLevel == 'Medium')
                                <span class="info">🟡 Medium Risk</span>
                            @else
                                <span class="success">🟢 Low Risk</span>
                            @endif
                        </td>
                        <td>
                            @if($farmer->avg_payment_delay > 14)
                                <span class="warning">Consistently Late</span>
                            @elseif($farmer->avg_payment_delay > 7)
                                <span class="info">Occasionally Late</span>
                            @else
                                <span class="success">Generally On Time</span>
                            @endif
                        </td>
                        <td>
                            @if($riskLevel == 'High')
                                <span class="warning">URGENT</span>
                            @elseif($riskLevel == 'Medium')
                                <span class="info">HIGH</span>
                            @else
                                <span class="success">MONITOR</span>
                            @endif
                        </td>
                        <td>
                            @if($riskLevel == 'High')
                                <span class="warning">Immediate Follow-up</span>
                            @elseif($riskLevel == 'Medium')
                                <span class="info">Schedule Call</span>
                            @else
                                <span class="success">Standard Process</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="data-grid">
            <div class="grid-item warning">
                <div class="grid-label">High Risk Farmers</div>
                <div class="grid-value">{{ $highRiskFarmers }}</div>
            </div>
            <div class="grid-item warning">
                <div class="grid-label">High Risk Exposure</div>
                <div class="grid-value">
                    @php
                        $highRiskExposure = collect($data)->where('avg_payment_delay', '>', 14)->sum('outstanding_amount');
                    @endphp
                    Rs. {{ number_format($highRiskExposure, 2) }}
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Avg Outstanding per Farmer</div>
                <div class="grid-value">Rs. {{ number_format($totalOutstanding / max($totalFarmers, 1), 2) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Collections at Risk</div>
                <div class="grid-value">{{ number_format($riskExposure, 1) }}% of sales</div>
            </div>
        </div>

        <div class="note warning">
            <i class="fas fa-exclamation-triangle"></i>
            <p><strong>⚠️ Risk Mitigation Plan:</strong></p>
            <ol>
                <li><strong>Immediate Action:</strong> Contact high-risk farmers within 24 hours</li>
                <li><strong>Payment Plans:</strong> Offer structured payment plans for large outstanding amounts</li>
                <li><strong>Early Intervention:</strong> Contact farmers at first sign of delay (after 7 days)</li>
                <li><strong>Documentation:</strong> Maintain records of all payment follow-ups</li>
                <li><strong>Escalation:</strong> Escalate to management for delays exceeding 30 days</li>
            </ol>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Payment Delay Risk Distribution</div>
                <div class="chart-placeholder">
                    Chart: Risk Level Analysis
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-check-circle"></i>
            <h3>No Payment Delays</h3>
            <p>All farmers are receiving payments on time</p>
        </div>
    @endif
@endsection
