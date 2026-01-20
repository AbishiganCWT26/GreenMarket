@extends('admin.reports.templates_base')

@section('report-content')
    <div class="section-header">
        <i class="fas fa-database"></i> Data Quality Assessment
    </div>

    @if(isset($data) && is_array($data))
        @php
            $usersTotal = $data['users']->total ?? 0;
            $usersMissingEmail = $data['users']->missing_email ?? 0;
            $usersNeverLogged = $data['users']->never_logged_in ?? 0;

            $farmersTotal = $data['farmers']->total ?? 0;
            $farmersMissingNIC = $data['farmers']->missing_nic ?? 0;
            $farmersMissingMaps = $data['farmers']->missing_map_links ?? 0;
            $farmersMissingPayment = $data['farmers']->missing_payment_details ?? 0;

            $productsTotal = $data['products']->total ?? 0;
            $productsMissingPhotos = $data['products']->missing_photos ?? 0;
            $productsMissingMaps = $data['products']->missing_pickup_maps ?? 0;

            $overallQualityScore = 0;
            $totalRecords = $usersTotal + $farmersTotal + $productsTotal;
            $totalIssues = $usersMissingEmail + $usersNeverLogged + $farmersMissingNIC + $farmersMissingMaps + $farmersMissingPayment + $productsMissingPhotos + $productsMissingMaps;

            if($totalRecords > 0) {
                $overallQualityScore = 100 - (($totalIssues / ($totalRecords * 7)) * 100);
            }
        @endphp

        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-value">{{ $totalRecords }}</div>
                <div class="stat-label">Total Records</div>
            </div>
            <div class="stat-card {{ $totalIssues > 0 ? 'warning' : 'success' }}">
                <div class="stat-value">{{ $totalIssues }}</div>
                <div class="stat-label">Data Issues</div>
            </div>
            <div class="stat-card {{ $overallQualityScore >= 90 ? 'success' : ($overallQualityScore >= 75 ? 'info' : 'warning') }}">
                <div class="stat-value">{{ number_format($overallQualityScore, 1) }}%</div>
                <div class="stat-label">Quality Score</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value">{{ $farmersMissingNIC + $farmersMissingPayment }}</div>
                <div class="stat-label">Critical Issues</div>
            </div>
        </div>

        <div class="highlight {{ $overallQualityScore >= 90 ? 'success' : ($overallQualityScore >= 75 ? 'info' : 'warning') }}">
            <h4>📊 Data Quality Assessment</h4>
            <p>
                Overall Data Quality: <strong>{{ number_format($overallQualityScore, 1) }}%</strong><br>
                @if($overallQualityScore >= 90)
                    ✅ Excellent data quality
                @elseif($overallQualityScore >= 75)
                    ⚠️ Good, but improvements needed
                @else
                    ❌ Poor data quality requires immediate attention
                @endif
                <br>
                {{ $totalIssues }} data quality issues detected across {{ $totalRecords }} records
            </p>
        </div>

        <div class="section-header" style="margin-top: 30px;">
            <i class="fas fa-users"></i> User Data Quality
        </div>

        <div class="data-grid">
            <div class="grid-item">
                <div class="grid-label">Total Users</div>
                <div class="grid-value">{{ $usersTotal }}</div>
            </div>
            <div class="grid-item {{ $usersMissingEmail > 0 ? 'warning' : 'success' }}">
                <div class="grid-label">Missing Email</div>
                <div class="grid-value">{{ $usersMissingEmail }} ({{ $usersTotal > 0 ? number_format(($usersMissingEmail / $usersTotal) * 100, 1) : 0 }}%)</div>
            </div>
            <div class="grid-item {{ $usersNeverLogged > 0 ? 'warning' : 'success' }}">
                <div class="grid-label">Never Logged In</div>
                <div class="grid-value">{{ $usersNeverLogged }} ({{ $usersTotal > 0 ? number_format(($usersNeverLogged / $usersTotal) * 100, 1) : 0 }}%)</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">User Data Quality</div>
                <div class="grid-value">
                    @php
                        $userQuality = $usersTotal > 0 ? 100 - ((($usersMissingEmail + $usersNeverLogged) / ($usersTotal * 2)) * 100) : 100;
                    @endphp
                    {{ number_format($userQuality, 1) }}%
                </div>
            </div>
        </div>

        <div class="section-header" style="margin-top: 30px;">
            <i class="fas fa-user-tie"></i> Farmer Data Quality
        </div>

        <div class="data-grid">
            <div class="grid-item">
                <div class="grid-label">Total Farmers</div>
                <div class="grid-value">{{ $farmersTotal }}</div>
            </div>
            <div class="grid-item {{ $farmersMissingNIC > 0 ? 'warning' : 'success' }}">
                <div class="grid-label">Missing NIC (Critical)</div>
                <div class="grid-value">{{ $farmersMissingNIC }} ({{ $farmersTotal > 0 ? number_format(($farmersMissingNIC / $farmersTotal) * 100, 1) : 0 }}%)</div>
            </div>
            <div class="grid-item {{ $farmersMissingMaps > 0 ? 'info' : 'success' }}">
                <div class="grid-label">Missing Map Links</div>
                <div class="grid-value">{{ $farmersMissingMaps }} ({{ $farmersTotal > 0 ? number_format(($farmersMissingMaps / $farmersTotal) * 100, 1) : 0 }}%)</div>
            </div>
            <div class="grid-item {{ $farmersMissingPayment > 0 ? 'warning' : 'success' }}">
                <div class="grid-label">Missing Payment Details (Critical)</div>
                <div class="grid-value">{{ $farmersMissingPayment }} ({{ $farmersTotal > 0 ? number_format(($farmersMissingPayment / $farmersTotal) * 100, 1) : 0 }}%)</div>
            </div>
        </div>

        <div class="section-header" style="margin-top: 30px;">
            <i class="fas fa-box"></i> Product Data Quality
        </div>

        <div class="data-grid">
            <div class="grid-item">
                <div class="grid-label">Total Products</div>
                <div class="grid-value">{{ $productsTotal }}</div>
            </div>
            <div class="grid-item {{ $productsMissingPhotos > 0 ? 'info' : 'success' }}">
                <div class="grid-label">Missing Photos</div>
                <div class="grid-value">{{ $productsMissingPhotos }} ({{ $productsTotal > 0 ? number_format(($productsMissingPhotos / $productsTotal) * 100, 1) : 0 }}%)</div>
            </div>
            <div class="grid-item {{ $productsMissingMaps > 0 ? 'info' : 'success' }}">
                <div class="grid-label">Missing Pickup Maps</div>
                <div class="grid-value">{{ $productsMissingMaps }} ({{ $productsTotal > 0 ? number_format(($productsMissingMaps / $productsTotal) * 100, 1) : 0 }}%)</div>
            </div>
            <div class="grid-item">
                <div class="grid-label">Product Data Quality</div>
                <div class="grid-value">
                    @php
                        $productQuality = $productsTotal > 0 ? 100 - ((($productsMissingPhotos + $productsMissingMaps) / ($productsTotal * 2)) * 100) : 100;
                    @endphp
                    {{ number_format($productQuality, 1) }}%
                </div>
            </div>
        </div>

        <div class="note">
            <i class="fas fa-tasks"></i>
            <p><strong>Data Improvement Plan:</strong>
                1. Prioritize fixing critical issues (missing NIC, payment details)<br>
                2. Set up automated reminders for incomplete profiles<br>
                3. Implement validation rules during data entry<br>
                4. Schedule monthly data quality audits<br>
                5. Train users on importance of complete data entry
            </p>
        </div>

        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-title">Data Quality Metrics by Entity Type</div>
                <div class="chart-placeholder">
                    Chart: Quality Score Comparison
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            <i class="fas fa-database"></i>
            <h3>No Quality Data</h3>
            <p>No data quality information available for analysis</p>
        </div>
    @endif
@endsection
