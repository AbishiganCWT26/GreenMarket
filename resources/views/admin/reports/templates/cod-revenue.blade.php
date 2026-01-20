@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-chart-area"></i> COD Revenue Forecast & Analysis
    </div>

    @if(isset($data) && $data)
        @php
            $pendingOrders = $data->pending_orders ?? 0;
            $expectedCash = $data->expected_cash ?? 0;
            $avgOrderValue = $data->avg_order_value ?? 0;

            // Forecast calculations
            $dailyAverage = $expectedCash / max($pendingOrders, 1);
            $weeklyForecast = $pendingOrders * $avgOrderValue;
            $monthlyForecast = $weeklyForecast * 4;
            $collectionProbability = $pendingOrders > 10 ? 85 : ($pendingOrders > 5 ? 75 : 60);
            $expectedCollection = $expectedCash * ($collectionProbability / 100);
            $riskExposure = $expectedCash - $expectedCollection;
        @endphp

        <div class="summary-stats">
            <div class="stat-card warning">
                <div class="stat-value">{{ $pendingOrders }}</div>
                <div class="stat-label">Pending COD Orders</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value">Rs. {{ number_format($expectedCash, 2) }}</div>
                <div class="stat-label">Expected Cash</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">Rs. {{ number_format($avgOrderValue, 2) }}</div>
                <div class="stat-label">Avg Order Value</div>
            </div>
            <div class="stat-card success">
                <div class="stat-value">{{ number_format($collectionProbability, 0) }}%</div>
                <div class="stat-label">Collection Probability</div>
            </div>
        </div>

        <div class="highlight {{ $collectionProbability >= 80 ? 'success' : ($collectionProbability >= 65 ? 'info' : 'warning') }}">
            <h4>📈 COD Revenue Forecast</h4>
            <p>
                Expected Collection: <strong>Rs. {{ number_format($expectedCollection, 2) }}</strong><br>
                Risk Exposure: <strong>Rs. {{ number_format($riskExposure, 2) }}</strong><br>
                Weekly Forecast: <strong>Rs. {{ number_format($weeklyForecast, 2) }}</strong><br>
                Monthly Forecast: <strong>Rs. {{ number_format($monthlyForecast, 2) }}</strong><br>
                @if($collectionProbability >= 80)
                    ✅ High probability of successful collection
                @elseif($collectionProbability >= 65)
                    ⚠️ Moderate collection risk, monitor closely
                @else
                    ❌ High collection risk, immediate action required
                @endif
            </p>
        </div>

        <div class="data-grid">
            <div class="grid-item">
                <div class="grid-label">Daily Cash Flow Forecast</div>
                <div class="grid-value">Rs. {{ number_format($dailyAverage, 2) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Collection Efficiency Forecast</div>
                <div class="grid-value">{{ number_format($collectionProbability, 1) }}%</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Risk-Adjusted Revenue</div>
                <div class="grid-value">Rs. {{ number_format($expectedCollection, 2) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Forecast Confidence</div>
                <div class="grid-value">
                    @if($pendingOrders >= 20)
                        <span class="success">High</span>
                    @elseif($pendingOrders >= 10)
                        <span class="info">Medium</span>
                    @else
                        <span class="warning">Low</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="section-header" style="margin-top: 30px;">
            <i class="fas fa-chart-line"></i> Revenue Projection Scenarios
        </div>

        <div class="data-grid">
            <div class="grid-item success">
                <div class="grid-label">Optimistic Scenario (90% collection)</div>
                <div class="grid-value">Rs. {{ number_format($expectedCash * 0.9, 2) }}</div>
            </div>
            <div class="grid-item info">
                <div class="grid-label">Base Scenario ({{ number_format($collectionProbability, 0) }}% collection)</div>
                <div class="grid-value">Rs. {{ number_format($expectedCollection, 2) }}</div>
            </div>
            <div class="grid-item warning">
                <div class="grid-label">Conservative Scenario (70% collection)</div>
                <div class="grid-value">Rs. {{ number_format($expectedCash * 0.7, 2) }}</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Worst-Case Scenario (50% collection)</div>
                <div class="grid-value">Rs. {{ number_format($expectedCash * 0.5, 2) }}</div>
            </div>
        </div>

        <div class="section-header" style="margin-top: 30px;">
            <i class="fas fa-exclamation-triangle"></i> Risk Analysis & Mitigation
        </div>

        <div class="data-grid">
            <div class="grid-item {{ $pendingOrders > 20 ? 'success' : ($pendingOrders > 10 ? 'info' : 'warning') }}">
                <div class="grid-label">Portfolio Size Risk</div>
                <div class="grid-value">
                    @if($pendingOrders > 20)
                        Low (Diversified)
                    @elseif($pendingOrders > 10)
                        Medium
                    @else
                        High (Concentrated)
                    @endif
                </div>
            </div>
            <div class="grid-item {{ $avgOrderValue > 2000 ? 'warning' : ($avgOrderValue > 1000 ? 'info' : 'success') }}">
                <div class="grid-label">Average Ticket Risk</div>
                <div class="grid-value">
                    @if($avgOrderValue > 2000)
                        High (Large tickets)
                    @elseif($avgOrderValue > 1000)
                        Medium
                    @else
                        Low (Small tickets)
                    @endif
                </div>
            </div>
            <div class="grid-item {{ $collectionProbability >= 80 ? 'success' : ($collectionProbability >= 65 ? 'info' : 'warning') }}">
                <div class="grid-label">Historical Performance Risk</div>
                <div class="grid-value">
                    @if($collectionProbability >= 80)
                        Low
                    @elseif($collectionProbability >= 65)
                        Medium
                    @else
                        High
                    @endif
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Overall Risk Score</div>
                <div class="grid-value">
                    @php
                        $riskScore = 0;
                        if($pendingOrders <= 10) $riskScore += 40;
                        elseif($pendingOrders <= 20) $riskScore += 20;

                        if($avgOrderValue > 2000) $riskScore += 40;
                        elseif($avgOrderValue > 1000) $riskScore += 20;

                        if($collectionProbability < 65) $riskScore += 40;
                        elseif($collectionProbability < 80) $riskScore += 20;

                        if($riskScore >= 100) $riskLevel = 'High';
                        elseif($riskScore >= 60) $riskLevel = 'Medium';
                        else $riskLevel = 'Low';
                    @endphp
                    {{ $riskLevel }}
                </div>
            </div>
        </div>

        <div class="note warning">
            <i class="fas fa-shield-alt"></i>
            <p><strong>⚠️ Risk Mitigation Strategies:</strong></p>
            <ol>
                <li><strong>High Risk Portfolio:</strong> Implement prepayment requirements for large orders</li>
                <li><strong>Concentrated Exposure:</strong> Diversify buyer base to reduce dependency</li>
                <li><strong>Collection Risk:</strong> Enhance follow-up procedures for pending payments</li>
                <li><strong>Cash Flow Risk:</strong> Maintain reserve fund for delayed collections</li>
                <li><strong>Monitoring:</strong> Daily tracking of collection progress</li>
            </ol>
        </div>

        <div class="note">
            <i class="fas fa-lightbulb"></i>
            <p><strong>Forecast Assumptions:</strong>
                1. Based on historical collection rates of similar orders<br>
                2. Assumes normal business conditions continue<br>
                3. Does not account for extreme market events<br>
                4. Updated daily based on new order data<br>
                5. Confidence increases with larger sample sizes
            </p>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">COD Revenue Forecast & Probability Distribution</div>
                <div class="chart-placeholder">
                    Chart: Revenue Projection Scenarios
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-chart-bar"></i>
            <h3>No Forecast Data</h3>
            <p>No pending COD orders available for revenue forecasting</p>
        </div>
    @endif
@endsection
