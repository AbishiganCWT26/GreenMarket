@extends('admin.layouts.admin_master')

@section('title', 'Generate Custom Report')

@section('content')
<link rel="stylesheet" href="{{ asset('css/Admin/report-generate.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="page-header">
	<div class="header-content">
		<h2><i class="fas fa-chart-pie"></i> Generate Custom Report</h2>
		<p>Create custom reports with advanced filters</p>
	</div>
</div>

<div class="generate-report-container">
	<div class="report-grid">
		<div class="form-card">
			<div class="form-header">
				<h3><i class="fas fa-sliders-h"></i> Report Configuration</h3>
				<p>Select report type and configure filters</p>
			</div>
			<div class="form-body">
				<form id="customReportForm" action="{{ route('admin.reports.custom') }}" method="POST">
					@csrf
					<div class="form-section">
						<div class="section-title">
							<i class="fas fa-chart-bar"></i>
							<span>Report Type</span>
						</div>
						<div class="form-group">
							<label><i class="fas fa-file-contract"></i> Select Report</label>
							<select id="report_type" name="report_type" class="form-control" required>
								<option value="">-- Select Report Type --</option>
								<optgroup label="Sales & Orders">
									<option value="order-history">Order History Report</option>
									<option value="fulfillment-timeline">Fulfillment Timeline Report</option>
									<option value="regional-performance">Regional Performance Report</option>
								</optgroup>
								<optgroup label="Inventory">
									<option value="inventory-stock">Current Inventory Report</option>
									<option value="category-performance">Category Performance Report</option>
									<option value="stock-movement">Stock Movement Report</option>
									<option value="product-taxonomy">Product Category Report</option>
								</optgroup>
								<optgroup label="Users & Farmers">
									<option value="group-performance">Group Performance Report</option>
									<option value="farmer-registration">Farmer Registration Report</option>
									<option value="system-adoption">System Adoption Report</option>
									<option value="user-access">User Access Report</option>
									<option value="dispute-feedback">Dispute & Feedback Report</option>
								</optgroup>
								<optgroup label="Financial">
								</optgroup>
							</select>
						</div>
					</div>
					<div class="form-section">
						<div class="section-title">
							<i class="fas fa-calendar-alt"></i>
							<span>Date Range</span>
						</div>
						<div class="date-row">
							<div class="form-group">
								<label><i class="fas fa-calendar-plus"></i> From Date</label>
								<input type="date" id="from_date" name="from_date" class="form-control" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d', strtotime('-30 days')) }}">
							</div>
							<div class="form-group">
								<label><i class="fas fa-calendar-minus"></i> To Date</label>
								<input type="date" id="to_date" name="to_date" class="form-control" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
							</div>
						</div>
					</div>
					<div class="form-section">
						<div class="section-title">
							<i class="fas fa-filter"></i>
							<span>Advanced Filters</span>
						</div>
						<div class="filter-row">
							<div class="form-group">
								<label><i class="fas fa-tags"></i> Status</label>
								<select id="status_filter" name="status_filter" class="form-control">
									<option value="">All Statuses</option>
									<option value="active">Active</option>
									<option value="inactive">Inactive</option>
									<option value="pending">Pending</option>
									<option value="completed">Completed</option>
									<option value="cancelled">Cancelled</option>
								</select>
							</div>
							<div class="form-group">
								<label><i class="fas fa-users"></i> User Type</label>
								<select id="user_type" name="user_type" class="form-control">
									<option value="">All Types</option>
									<option value="farmer">Farmers</option>
									<option value="lead_farmer">Lead Farmers</option>
									<option value="buyer">Buyers</option>
									<option value="facilitator">Facilitators</option>
									<option value="admin">Admins</option>
								</select>
							</div>
							<div class="form-group">
								<label><i class="fas fa-credit-card"></i> Payment</label>
								<select id="payment_method" name="payment_method" class="form-control">
									<option value="">All Methods</option>
									<option value="COD">Cash on Delivery</option>
									<option value="online">Online Payment</option>
								</select>
							</div>
						</div>
					</div>
					<div class="form-section">
						<div class="section-title">
							<i class="fas fa-download"></i>
							<span>Output Settings</span>
						</div>
						<div class="form-group">
							<label><i class="fas fa-file-export"></i> Format</label>
							<div class="radio-group">
								<div class="radio-item">
									<input type="radio" name="format" value="view" id="format_view" checked>
									<label for="format_view"><i class="fas fa-eye"></i> Browser</label>
								</div>
								<div class="radio-item">
									<input type="radio" name="format" value="pdf" id="format_pdf">
									<label for="format_pdf"><i class="fas fa-file-pdf"></i> PDF</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label><i class="fas fa-heading"></i> Custom Title</label>
							<input type="text" id="report_title" name="report_title" class="form-control" placeholder="Enter report title (optional)">
						</div>
					</div>
					<div class="action-bar">
						<button type="button" class="btn btn-reset" onclick="resetForm()">
							<i class="fas fa-redo"></i> Reset
						</button>
						<button type="submit" class="btn btn-generate">
							<i class="fas fa-play"></i> Generate
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
	const today = new Date().toISOString().split('T')[0];
	document.getElementById('from_date').max = today;
	document.getElementById('to_date').max = today;
	
	const reportTypeSelect = document.getElementById('report_type');
	const fromDateInput = document.getElementById('from_date');
	const toDateInput = document.getElementById('to_date');
	const statusFilter = document.getElementById('status_filter');
	const userTypeSelect = document.getElementById('user_type');
	const paymentMethodSelect = document.getElementById('payment_method');
	
	fromDateInput.addEventListener('change', function() {
		toDateInput.min = this.value;
		if (toDateInput.value && toDateInput.value < this.value) {
			toDateInput.value = this.value;
		}
	});
	
	toDateInput.addEventListener('change', function() {
		if (this.value < fromDateInput.value) {
			Swal.fire({
				icon: 'warning',
				title: 'Date Error',
				text: 'To date cannot be before from date',
				confirmButtonColor: '#10B981',
				background: '#ffffff',
				color: '#0f1724',
				timer: 2000,
				showConfirmButton: true
			});
			this.value = fromDateInput.value;
		}
	});
	
	reportTypeSelect.addEventListener('change', function() {
		updateFilterOptions(this.value);
	});
});

function updateFilterOptions(reportType) {
	const statusFilter = document.getElementById('status_filter');
	const userTypeSelect = document.getElementById('user_type');
	const paymentMethodSelect = document.getElementById('payment_method');
	
	statusFilter.disabled = false;
	userTypeSelect.disabled = false;
	paymentMethodSelect.disabled = false;
	
	if (reportType.includes('order') || reportType.includes('sales')) {
		statusFilter.value = '';
		userTypeSelect.value = '';
		paymentMethodSelect.value = '';
	} else if (reportType.includes('inventory')) {
		statusFilter.value = 'active';
		userTypeSelect.value = '';
		paymentMethodSelect.value = '';
	} else if (reportType.includes('user') || reportType.includes('system')) {
		statusFilter.value = '';
		userTypeSelect.value = '';
		paymentMethodSelect.value = '';
	} else if (reportType.includes('cod') || reportType.includes('cash')) {
		statusFilter.value = '';
		userTypeSelect.value = '';
		paymentMethodSelect.value = 'COD';
	}
}

function getReportName(reportType) {
	const names = {
		'order-history': 'Order History Report',
		'inventory-stock': 'Current Inventory Report',
		'category-performance': 'Category Performance Report',
		'stock-movement': 'Stock Movement Report',
		'group-performance': 'Group Performance Report',
		'farmer-registration': 'Farmer Registration Report',
		'system-adoption': 'System Adoption Report',
		'user-access': 'User Access Report',
		'dispute-feedback': 'Dispute & Feedback Report',
		'regional-performance': 'Regional Performance Report',
		'fulfillment-timeline': 'Fulfillment Timeline Report',
		'product-taxonomy': 'Product Category Report'
	};
	return names[reportType] || 'Custom Report';
}

function resetForm() {
	document.getElementById('customReportForm').reset();
	const today = new Date().toISOString().split('T')[0];
	document.getElementById('from_date').value = "{{ date('Y-m-d', strtotime('-30 days')) }}";
	document.getElementById('to_date').value = today;
	
	Swal.fire({
		icon: 'success',
		title: 'Form Reset',
		text: 'All form fields have been reset',
		confirmButtonColor: '#10B981',
		background: '#ffffff',
		color: '#0f1724',
		timer: 1500,
		showConfirmButton: false
	});
}

document.getElementById('customReportForm').addEventListener('submit', function(e) {
	e.preventDefault();
	
	const reportType = document.getElementById('report_type').value;
	const fromDate = document.getElementById('from_date').value;
	const toDate = document.getElementById('to_date').value;
	const format = document.querySelector('input[name="format"]:checked').value;
	
	if (!reportType) {
		Swal.fire({
			icon: 'error',
			title: 'Missing Information',
			text: 'Please select a report type',
			confirmButtonColor: '#10B981',
			background: '#ffffff',
			color: '#0f1724'
		});
		return;
	}
	
	if (!fromDate || !toDate) {
		Swal.fire({
			icon: 'warning',
			title: 'Missing Dates',
			text: 'Please select both date ranges',
			confirmButtonColor: '#10B981',
			background: '#ffffff',
			color: '#0f1724'
		});
		return;
	}
	
	if (new Date(fromDate) > new Date(toDate)) {
		Swal.fire({
			icon: 'error',
			title: 'Invalid Date',
			text: 'From date cannot be after to date',
			confirmButtonColor: '#10B981',
			background: '#ffffff',
			color: '#0f1724'
		});
		return;
	}
	
	Swal.fire({
		title: 'Generating Report',
		text: 'Please wait while we generate your report...',
		allowOutsideClick: false,
		background: '#ffffff',
		color: '#0f1724',
		didOpen: () => {
			Swal.showLoading();
		}
	});
	
	setTimeout(() => {
		this.submit();
	}, 500);
});
</script>
@endsection