@extends('facilitator.layouts.facilitator_master')

@section('title', 'Facilitator Dashboard')
@section('page-title', 'Facilitator Dashboard')

@section('content')
<div class="row mb-4">
	<div class="col-12">
		<div class="welcome-card">
			<div class="welcome-content">
				<h2>Welcome back, {{ $facilitator->name ?? 'Facilitator' }}! 👋</h2>
				<p class="text-muted mb-0">Field Officer Dashboard - Manage system standards and user support</p>
			</div>
			<div class="welcome-icon">
				<i class="fa-solid fa-hands-helping"></i>
			</div>
		</div>
	</div>
</div>

<div class="row mb-4">
	<div class="col-xl-3 col-md-6 mb-4">
		<div class="stat-card" style="background: linear-gradient(135deg, #10B981, #059669);">
			<div class="stat-icon">
				<i class="fa-solid fa-layer-group"></i>
			</div>
			<div class="stat-content">
				<h3>{{ $totalCategories ?? 0 }}</h3>
				<p>Active Categories</p>
			</div>
			<div class="stat-arrow">
				<i class="fa-solid fa-arrow-up"></i>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6 mb-4">
		<div class="stat-card" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
			<div class="stat-icon">
				<i class="fa-solid fa-users"></i>
			</div>
			<div class="stat-content">
				<h3>{{ $totalUsers ?? 0 }}</h3>
				<p>Total Users</p>
			</div>
			<div class="stat-arrow">
				<i class="fa-solid fa-users"></i>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6 mb-4">
		<div class="stat-card" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
			<div class="stat-icon">
				<i class="fa-solid fa-flag"></i>
			</div>
			<div class="stat-content">
				<h3>{{ $pendingComplaints ?? 0 }}</h3>
				<p>Pending Complaints</p>
			</div>
			<div class="stat-arrow">
				<i class="fa-solid fa-exclamation-circle"></i>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6 mb-4">
		<div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
			<div class="stat-icon">
				<i class="fa-solid fa-chart-line"></i>
			</div>
			<div class="stat-content">
				<h3>{{ $systemStandards['units'] ?? 0 }}</h3>
				<p>System Standards</p>
			</div>
			<div class="stat-arrow">
				<i class="fa-solid fa-chart-bar"></i>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-8 mb-4">
		<div class="dashboard-card">
			<div class="card-header">
				<h4><i class="fa-solid fa-chart-bar me-2"></i> Quick Actions</h4>
				<button class="btn-refresh" id="refreshStats">
					<i class="fa-solid fa-rotate"></i>
				</button>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6 mb-3">
						<div class="quick-action-card" onclick="window.location.href='{{ route('facilitator.taxonomy') }}'">
							<div class="action-icon" style="background: rgba(16,185,129,0.1);">
								<i class="fa-solid fa-layer-group" style="color: #10B981;"></i>
							</div>
							<div class="action-content">
								<h5>Manage Taxonomy</h5>
								<p>Add/edit product categories and subcategories</p>
							</div>
							<div class="action-arrow">
								<i class="fa-solid fa-arrow-right"></i>
							</div>
						</div>
					</div>

					<div class="col-md-6 mb-3">
						<div class="quick-action-card" onclick="window.location.href='{{ route('facilitator.users') }}'">
							<div class="action-icon" style="background: rgba(59,130,246,0.1);">
								<i class="fa-solid fa-user-gear" style="color: #3b82f6;"></i>
							</div>
							<div class="action-content">
								<h5>Manage Users</h5>
								<p>View and manage all system users</p>
							</div>
							<div class="action-arrow">
								<i class="fa-solid fa-arrow-right"></i>
							</div>
						</div>
					</div>

					<div class="col-md-6 mb-3">
						<div class="quick-action-card" onclick="showComingSoon('System Standards')">
							<div class="action-icon" style="background: rgba(139,92,246,0.1);">
								<i class="fa-solid fa-ruler-combined" style="color: #8b5cf6;"></i>
							</div>
							<div class="action-content">
								<h5>System Standards</h5>
								<p>Manage units of measure and quality grades</p>
							</div>
							<div class="action-arrow">
								<i class="fa-solid fa-arrow-right"></i>
							</div>
						</div>
					</div>

					<div class="col-md-6 mb-3">
						<div class="quick-action-card" onclick="showComingSoon('Reports')">
							<div class="action-icon" style="background: rgba(245,158,11,0.1);">
								<i class="fa-solid fa-file-chart-column" style="color: #f59e0b;"></i>
							</div>
							<div class="action-content">
								<h5>Generate Reports</h5>
								<p>View system performance reports</p>
							</div>
							<div class="action-arrow">
								<i class="fa-solid fa-arrow-right"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-4 mb-4">
		<div class="dashboard-card">
			<div class="card-header">
				<h4><i class="fa-solid fa-bell me-2"></i> Recent Activities</h4>
			</div>
			<div class="card-body">
				<div class="activity-list">
					@if(isset($recentActivities) && count($recentActivities) > 0)
						@foreach($recentActivities as $activity)
						<div class="activity-item">
							<div class="activity-icon">
								@if($activity->notification_type == 'admin_alert')
									<i class="fa-solid fa-triangle-exclamation text-warning"></i>
								@else
									<i class="fa-solid fa-info-circle text-info"></i>
								@endif
							</div>
							<div class="activity-content">
								<h6>{{ $activity->title }}</h6>
								<p class="text-muted">{{ Str::limit($activity->message, 60) }}</p>
								<small class="text-muted">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</small>
							</div>
						</div>
						@endforeach
					@else
						<div class="text-center py-4">
							<i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
							<p class="text-muted">No recent activities</p>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

<style>
.welcome-card {
	background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
	border-radius: 16px;
	padding: 2rem;
	display: flex;
	justify-content: space-between;
	align-items: center;
	box-shadow: var(--shadow-md);
	border: 1px solid #e0f2fe;
	transition: var(--transition);
	position: relative;
	overflow: hidden;
}

.welcome-card:hover {
	transform: translateY(-5px);
	box-shadow: 0 20px 40px rgba(15,23,36,0.12);
}

.welcome-card::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	height: 4px;
	background: linear-gradient(90deg, #10B981, #3b82f6, #8b5cf6);
}

.welcome-content h2 {
	font-weight: 700;
	color: var(--text-color);
	margin-bottom: 0.5rem;
	font-size: 1.8rem;
}

.welcome-icon {
	width: 80px;
	height: 80px;
	background: linear-gradient(135deg, #10B981, #0ea5e9);
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	color: white;
	font-size: 2.5rem;
	box-shadow: 0 8px 20px rgba(16,185,129,0.3);
	transition: var(--transition);
}

.welcome-card:hover .welcome-icon {
	transform: rotate(15deg) scale(1.1);
}

.stat-card {
	border-radius: 16px;
	padding: 1.5rem;
	color: white;
	display: flex;
	align-items: center;
	justify-content: space-between;
	box-shadow: var(--shadow-md);
	transition: var(--transition);
	cursor: pointer;
	position: relative;
	overflow: hidden;
}

.stat-card::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: rgba(255,255,255,0.1);
	opacity: 0;
	transition: var(--transition);
}

.stat-card:hover {
	transform: translateY(-8px);
	box-shadow: 0 15px 30px rgba(0,0,0,0.2);
}

.stat-card:hover::before {
	opacity: 1;
}

.stat-icon {
	width: 60px;
	height: 60px;
	background: rgba(255,255,255,0.2);
	border-radius: 12px;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 1.8rem;
	backdrop-filter: blur(10px);
	transition: var(--transition);
}

.stat-card:hover .stat-icon {
	transform: scale(1.1) rotate(10deg);
	background: rgba(255,255,255,0.3);
}

.stat-content h3 {
	font-size: 2.2rem;
	font-weight: 700;
	margin-bottom: 0.25rem;
	text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.stat-content p {
	margin: 0;
	opacity: 0.9;
	font-size: 0.95rem;
}

.stat-arrow i {
	font-size: 1.5rem;
	opacity: 0.8;
	transition: var(--transition);
}

.stat-card:hover .stat-arrow i {
	transform: translateX(5px);
	opacity: 1;
}

.dashboard-card {
	background: var(--card-bg);
	border-radius: 16px;
	box-shadow: var(--shadow-md);
	transition: var(--transition);
	height: 100%;
	border: 1px solid #f1f5f9;
	overflow: hidden;
}

.dashboard-card:hover {
	box-shadow: 0 20px 40px rgba(15,23,36,0.1);
	transform: translateY(-3px);
}

.card-header {
	padding: 1.5rem;
	border-bottom: 1px solid #f1f5f9;
	display: flex;
	justify-content: space-between;
	align-items: center;
	background: linear-gradient(90deg, #ffffff, #f8fafc);
}

.card-header h4 {
	margin: 0;
	font-weight: 600;
	color: var(--text-color);
	display: flex;
	align-items: center;
}

.card-header h4 i {
	color: var(--primary-green);
}

.btn-refresh {
	width: 36px;
	height: 36px;
	border-radius: 10px;
	border: 1px solid #e2e8f0;
	background: white;
	color: var(--muted);
	display: flex;
	align-items: center;
	justify-content: center;
	cursor: pointer;
	transition: var(--transition);
}

.btn-refresh:hover {
	background: var(--primary-green);
	border-color: var(--primary-green);
	color: white;
	transform: rotate(180deg);
}

.card-body {
	padding: 1.5rem;
}

.quick-action-card {
	background: white;
	border-radius: 12px;
	padding: 1.25rem;
	display: flex;
	align-items: center;
	gap: 1rem;
	box-shadow: var(--shadow-sm);
	transition: var(--transition);
	cursor: pointer;
	border: 1px solid #f1f5f9;
	position: relative;
	overflow: hidden;
}

.quick-action-card:hover {
	transform: translateY(-5px) translateX(5px);
	box-shadow: 0 10px 25px rgba(15,23,36,0.15);
	border-color: var(--primary-green);
}

.quick-action-card::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	height: 3px;
	background: linear-gradient(90deg, #10B981, #3b82f6);
	opacity: 0;
	transition: var(--transition);
}

.quick-action-card:hover::before {
	opacity: 1;
}

.action-icon {
	width: 50px;
	height: 50px;
	border-radius: 10px;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 1.5rem;
	flex-shrink: 0;
	transition: var(--transition);
}

.quick-action-card:hover .action-icon {
	transform: scale(1.1) rotate(5deg);
}

.action-content {
	flex: 1;
}

.action-content h5 {
	font-size: 1rem;
	font-weight: 600;
	color: var(--text-color);
	margin-bottom: 0.25rem;
}

.action-content p {
	font-size: 0.85rem;
	color: var(--muted);
	margin: 0;
	line-height: 1.4;
}

.action-arrow {
	color: var(--muted);
	transition: var(--transition);
}

.quick-action-card:hover .action-arrow {
	color: var(--primary-green);
	transform: translateX(5px);
}

.activity-list {
	max-height: 300px;
	overflow-y: auto;
}

.activity-item {
	display: flex;
	gap: 1rem;
	padding: 1rem 0;
	border-bottom: 1px solid #f1f5f9;
	transition: var(--transition);
}

.activity-item:last-child {
	border-bottom: none;
}

.activity-item:hover {
	background: #f8fafc;
	border-radius: 8px;
	padding: 1rem;
	margin: 0 -1rem;
}

.activity-icon {
	width: 40px;
	height: 40px;
	border-radius: 10px;
	background: #f0f9ff;
	display: flex;
	align-items: center;
	justify-content: center;
	flex-shrink: 0;
	transition: var(--transition);
}

.activity-item:hover .activity-icon {
	transform: scale(1.1);
	background: #e0f2fe;
}

.activity-content h6 {
	font-size: 0.95rem;
	font-weight: 600;
	color: var(--text-color);
	margin-bottom: 0.25rem;
}

.activity-content p {
	font-size: 0.85rem;
	color: var(--muted);
	margin-bottom: 0.25rem;
	line-height: 1.4;
}

@media (max-width: 767px) {
	.welcome-card {
		flex-direction: column;
		text-align: center;
		gap: 1.5rem;
		padding: 1.5rem;
	}

	.welcome-content h2 {
		font-size: 1.5rem;
	}

	.welcome-icon {
		width: 70px;
		height: 70px;
		font-size: 2rem;
	}

	.stat-content h3 {
		font-size: 1.8rem;
	}

	.quick-action-card {
		padding: 1rem;
	}

	.action-icon {
		width: 40px;
		height: 40px;
		font-size: 1.2rem;
	}
}

@media (max-width: 480px) {
	.card-header {
		flex-direction: column;
		gap: 1rem;
		text-align: center;
	}

	.quick-action-card {
		flex-direction: column;
		text-align: center;
		gap: 0.75rem;
	}

	.action-content {
		text-align: center;
	}

	.action-arrow {
		display: none;
	}
}
</style>

<script>
function showComingSoon(feature) {
	Swal.fire({
		title: `${feature} Coming Soon!`,
		text: 'This feature is currently under development.',
		icon: 'info',
		confirmButtonColor: '#10B981',
		background: '#ffffff',
		color: '#0f1724',
		width: '400px',
		customClass: {
			popup: 'animate__animated animate__fadeIn'
		}
	});
}

document.getElementById('refreshStats').addEventListener('click', function() {
	const btn = this;
	btn.style.transform = 'rotate(180deg)';

	setTimeout(() => {
		btn.style.transform = '';
		toastr.info('Stats refreshed!', '', {
			positionClass: 'toast-top-right',
			progressBar: true,
			timeOut: 2000,
			showMethod: 'slideDown',
			hideMethod: 'slideUp'
		});
	}, 500);
});
</script>
@endsection
