<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Users Report - GreenMarket</title>
	<style>
		body {
			font-family: 'DejaVu Sans', sans-serif;
			font-size: 12px;
			color: #333;
			margin: 0;
			padding: 20px;
		}
		.header {
			text-align: center;
			margin-bottom: 30px;
			border-bottom: 2px solid #10B981;
			padding-bottom: 20px;
		}
		.header h1 {
			color: #10B981;
			margin: 0;
			font-size: 24px;
		}
		.header p {
			color: #666;
			margin: 5px 0;
		}
		.info-box {
			background: #f8f9fa;
			border: 1px solid #dee2e6;
			border-radius: 5px;
			padding: 15px;
			margin-bottom: 20px;
		}
		.info-row {
			display: flex;
			justify-content: space-between;
			margin-bottom: 10px;
		}
		.info-label {
			font-weight: bold;
			color: #10B981;
		}
		.table {
			width: 100%;
			border-collapse: collapse;
			margin-top: 20px;
		}
		.table th {
			background: #10B981;
			color: white;
			padding: 10px;
			text-align: left;
			font-weight: bold;
			border: 1px solid #059669;
		}
		.table td {
			padding: 8px 10px;
			border: 1px solid #dee2e6;
		}
		.table tr:nth-child(even) {
			background: #f8f9fa;
		}
		.status-active {
			color: #10B981;
			font-weight: bold;
		}
		.status-inactive {
			color: #dc3545;
			font-weight: bold;
		}
		.role-badge {
			display: inline-block;
			padding: 3px 8px;
			border-radius: 3px;
			font-size: 11px;
			font-weight: bold;
		}
		.footer {
			margin-top: 40px;
			text-align: center;
			color: #666;
			font-size: 11px;
			border-top: 1px solid #dee2e6;
			padding-top: 10px;
		}
		.summary {
			display: flex;
			justify-content: space-around;
			margin: 20px 0;
			padding: 15px;
			background: #f8f9fa;
			border-radius: 5px;
		}
		.summary-item {
			text-align: center;
		}
		.summary-value {
			font-size: 24px;
			font-weight: bold;
			color: #10B981;
		}
		.summary-label {
			font-size: 12px;
			color: #666;
		}
	</style>
</head>
<body>
	<div class="header">
		<h1>GreenMarket - Users Report</h1>
		<p>Generated on: {{ $generatedAt->format('d M Y H:i:s') }}</p>
		<p>Report Type: {{ ucfirst($reportType) }} Report</p>
	</div>

	<div class="info-box">
		<div class="info-row">
			<span class="info-label">Total Users:</span>
			<span>{{ $users->count() }}</span>
		</div>
		<div class="info-row">
			<span class="info-label">Active Users:</span>
			<span>{{ $users->where('is_active', true)->count() }}</span>
		</div>
		<div class="info-row">
			<span class="info-label">Inactive Users:</span>
			<span>{{ $users->where('is_active', false)->count() }}</span>
		</div>
		<div class="info-row">
			<span class="info-label">Date Range:</span>
			<span>{{ $users->min('created_at')->format('d M Y') }} - {{ $users->max('created_at')->format('d M Y') }}</span>
		</div>
	</div>

	<table class="table">
		<thead>
			<tr>
				<th>ID</th>
				<th>Username</th>
				<th>Email</th>
				<th>Role</th>
				@if($includeContact)
				<th>Contact</th>
				@endif
				@if($includeLocation)
				<th>Location</th>
				@endif
				<th>Status</th>
				<th>Joined Date</th>
			</tr>
		</thead>
		<tbody>
			@foreach($users as $user)
			<tr>
				<td>{{ $user->id }}</td>
				<td>{{ $user->username }}</td>
				<td>{{ $user->email ?? 'N/A' }}</td>
				<td>
					@php
						$roleColor = match($user->role) {
							'farmer' => '#10B981',
							'lead_farmer' => '#3b82f6',
							'buyer' => '#f59e0b',
							'facilitator' => '#8b5cf6',
							default => '#6b7280'
						};
					@endphp
					<span class="role-badge" style="background: {{ $roleColor }}; color: white;">
						{{ ucwords(str_replace('_', ' ', $user->role)) }}
					</span>
				</td>
				@if($includeContact)
				<td>
					@php
						$contact = '';
						if($user->farmer) $contact = $user->farmer->primary_mobile ?? '';
						elseif($user->leadFarmer) $contact = $user->leadFarmer->primary_mobile ?? '';
						elseif($user->buyer) $contact = $user->buyer->primary_mobile ?? '';
						elseif($user->facilitator) $contact = $user->facilitator->primary_mobile ?? '';
					@endphp
					{{ $contact ?: 'N/A' }}
				</td>
				@endif
				@if($includeLocation)
				<td>
					@php
						$location = '';
						if($user->farmer) $location = $user->farmer->district ?? '';
						elseif($user->leadFarmer) $location = $user->leadFarmer->grama_niladhari_division ?? '';
						elseif($user->facilitator) $location = $user->facilitator->assigned_division ?? '';
					@endphp
					{{ $location ?: 'N/A' }}
				</td>
				@endif
				<td>
					@if($user->is_active)
					<span class="status-active">Active</span>
					@else
					<span class="status-inactive">Inactive</span>
					@endif
				</td>
				<td>{{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>

	<div class="footer">
		<p>GreenMarket - User Management System</p>
		<p>This report is confidential and intended for authorized personnel only.</p>
		<p>Page {{ $pdf->getPage() }} of {{ $pdf->getPageCount() }}</p>
	</div>
</body>
</html>
