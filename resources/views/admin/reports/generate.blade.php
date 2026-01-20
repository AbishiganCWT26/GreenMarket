@extends('admin.layouts.admin_master')

@section('title', 'Generate Custom Report')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h2><i class="fas fa-file-alt"></i> Generate Custom Report</h2>
        <p>Create custom reports with advanced filters</p>
    </div>
</div>

<div class="generate-report-container">
    <div class="report-form-section">
        <div class="form-card">
            <div class="form-header">
                <h3><i class="fas fa-cogs"></i> Report Configuration</h3>
                <p>Select report type and configure filters</p>
            </div>

            <form id="customReportForm" action="{{ route('admin.reports.custom') }}" method="POST">
                @csrf

                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-chart-bar"></i>
                        <h4>Report Type</h4>
                    </div>
                    <div class="form-group">
                        <label for="report_type"><i class="fas fa-file-contract"></i> Select Report</label>
                        <select id="report_type" name="report_type" class="form-control" required>
                            <option value="">-- Select Report Type --</option>
                            <optgroup label="Sales & Orders">
                                <option value="order-history">Order History Report</option>
                                <option value="pending-pickup">Pending Pickup/Delivery Report</option>
                                <option value="sales-volume">Sales Volume & Value Report</option>
                                <option value="sales-payment">Sales & Payment Reconciliation</option>
                                <option value="system-financial">System Financial Summary</option>
                            </optgroup>
                            <optgroup label="Cash on Delivery">
                                <option value="daily-cash">Daily Cash Position Report</option>
                                <option value="cash-collection-delay">Cash Collection Delay Report</option>
                                <option value="cod-exception">COD Exception Report</option>
                                <option value="cod-payment">COD Payment Reconciliation</option>
                                <option value="cod-revenue">COD Revenue Forecast</option>
                                <option value="farmer-payment-delay">Farmer Payment Delay Risk</option>
                            </optgroup>
                            <optgroup label="Inventory & Products">
                                <option value="inventory-stock">Current Inventory / Stock Report</option>
                                <option value="category-performance">Product Category Performance</option>
                                <option value="stock-movement">Stock Movement Report</option>
                                <option value="product-taxonomy">Product Taxonomy Report</option>
                                <option value="quality-grade">Quality Grade Performance</option>
                            </optgroup>
                            <optgroup label="Users & System">
                                <option value="system-adoption">System Adoption & User Count</option>
                                <option value="user-access">User Access & Role Management</option>
                                <option value="group-performance">Group Farmer Performance</option>
                                <option value="farmer-registration">Farmer Registration Status</option>
                                <option value="buyer-payment-behavior">Buyer Payment Behavior</option>
                                <option value="data-quality">Data Quality Report</option>
                                <option value="dispute-feedback">Dispute & Feedback Log</option>
                            </optgroup>
                            <optgroup label="Geographic Analysis">
                                <option value="regional-cod">Regional Performance Report</option>
                                <option value="geographic-sales">Geographic Sales Density</option>
                            </optgroup>
                            <optgroup label="Financial & Audit">
                                <option value="financial-audit">Financial Audit & Transaction</option>
                                <option value="inventory-cash-reconciliation">Inventory vs Cash Reconciliation</option>
                                <option value="order-fulfillment">Order Fulfillment Timeline</option>
                            </optgroup>
                        </select>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-calendar-alt"></i>
                        <h4>Date Range</h4>
                    </div>
                    <div class="date-range-grid">
                        <div class="form-group">
                            <label for="from_date"><i class="fas fa-calendar-plus"></i> From Date</label>
                            <input type="date" id="from_date" name="from_date" class="form-control" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                        </div>
                        <div class="form-group">
                            <label for="to_date"><i class="fas fa-calendar-minus"></i> To Date</label>
                            <input type="date" id="to_date" name="to_date" class="form-control" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-filter"></i>
                        <h4>Advanced Filters</h4>
                    </div>
                    <div class="advanced-filters">
                        <div class="form-group">
                            <label for="status_filter"><i class="fas fa-tags"></i> Status Filter</label>
                            <select id="status_filter" name="status_filter" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="active">Active Only</option>
                                <option value="inactive">Inactive Only</option>
                                <option value="pending">Pending Only</option>
                                <option value="completed">Completed Only</option>
                                <option value="cancelled">Cancelled Only</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="user_type"><i class="fas fa-users"></i> User Type</label>
                            <select id="user_type" name="user_type" class="form-control">
                                <option value="">All User Types</option>
                                <option value="farmer">Farmers</option>
                                <option value="lead_farmer">Lead Farmers</option>
                                <option value="buyer">Buyers</option>
                                <option value="facilitator">Facilitators</option>
                                <option value="admin">Admins</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="payment_method"><i class="fas fa-credit-card"></i> Payment Method</label>
                            <select id="payment_method" name="payment_method" class="form-control">
                                <option value="">All Methods</option>
                                <option value="COD">Cash on Delivery</option>
                                <option value="online">Online Payment</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-download"></i>
                        <h4>Output Settings</h4>
                    </div>
                    <div class="output-settings">
                        <div class="form-group">
                            <label><i class="fas fa-file-export"></i> Output Format</label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="format" value="view" checked>
                                    <span><i class="fas fa-eye"></i> View in Browser</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="format" value="pdf">
                                    <span><i class="fas fa-file-pdf"></i> Download PDF</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="include_charts"><i class="fas fa-chart-bar"></i> Include Charts</label>
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="include_charts" name="include_charts" checked>
                                    <span>Include visual charts in report</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="report_title"><i class="fas fa-heading"></i> Custom Report Title</label>
                            <input type="text" id="report_title" name="report_title" class="form-control" placeholder="Enter custom report title (optional)">
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-reset" onclick="resetForm()">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                    <button type="submit" class="btn-generate">
                        <i class="fas fa-play-circle"></i> Generate
                    </button>
                    <button type="button" class="btn-preview" onclick="previewReport()">
                        <i class="fas fa-search"></i> Preview
                    </button>
                </div>
            </form>
        </div>

        <div class="preview-section">
            <div class="preview-card">
                <div class="preview-header">
                    <h3><i class="fas fa-eye"></i> Report Preview</h3>
                    <div class="preview-actions">
                        <button class="btn-refresh" onclick="updatePreview()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="preview-content">
                    <div class="preview-placeholder">
                        <i class="fas fa-chart-line"></i>
                        <h4>Report Preview</h4>
                        <p>Configure settings and click "Preview"</p>
                    </div>
                    <div class="preview-data" id="previewData"></div>
                </div>
            </div>
        </div>
    </div>
</div>

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
        updatePreview();
    });

    toDateInput.addEventListener('change', function() {
        if (this.value < fromDateInput.value) {
            Swal.fire({
                icon: 'warning',
                title: 'Date Error',
                text: 'To date cannot be before from date',
                confirmButtonColor: '#10B981',
                background: 'var(--card-bg)',
                color: 'var(--text-dark)'
            });
            this.value = fromDateInput.value;
        }
        updatePreview();
    });

    reportTypeSelect.addEventListener('change', function() {
        updateFilterOptions(this.value);
        updatePreview();
    });

    [statusFilter, userTypeSelect, paymentMethodSelect].forEach(select => {
        select.addEventListener('change', updatePreview);
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

function updatePreview() {
    const reportType = document.getElementById('report_type').value;
    const fromDate = document.getElementById('from_date').value;
    const toDate = document.getElementById('to_date').value;
    const statusFilter = document.getElementById('status_filter').value;
    const userType = document.getElementById('user_type').value;
    const paymentMethod = document.getElementById('payment_method').value;

    if (!reportType) {
        return;
    }

    const previewData = document.getElementById('previewData');
    previewData.innerHTML = `
        <div class="preview-loading">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Generating preview...</p>
        </div>
    `;

    setTimeout(() => {
        const reportName = getReportName(reportType);
        previewData.innerHTML = `
            <div class="preview-sample">
                <div class="sample-header">
                    <h5>${reportName}</h5>
                    <span class="sample-badge">Preview</span>
                </div>
                <div class="sample-period">
                    <i class="fas fa-calendar"></i>
                    <span>${fromDate} to ${toDate}</span>
                </div>
                <div class="sample-stats">
                    <div class="stat-item">
                        <div class="stat-value">24</div>
                        <div class="stat-label">Records</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">Rs. 125,450</div>
                        <div class="stat-label">Total Value</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">18</div>
                        <div class="stat-label">Active Items</div>
                    </div>
                </div>
                <div class="sample-filters">
                    <h6>Applied Filters:</h6>
                    <div class="filter-tags">
                        ${statusFilter ? `<span class="filter-tag">Status: ${statusFilter}</span>` : ''}
                        ${userType ? `<span class="filter-tag">User Type: ${userType}</span>` : ''}
                        ${paymentMethod ? `<span class="filter-tag">Payment: ${paymentMethod}</span>` : ''}
                        ${!statusFilter && !userType && !paymentMethod ? '<span class="filter-tag">No filters applied</span>' : ''}
                    </div>
                </div>
                <div class="sample-table">
                    <div class="table-row header">
                        <div class="table-cell">Sample Column 1</div>
                        <div class="table-cell">Sample Column 2</div>
                        <div class="table-cell">Sample Column 3</div>
                    </div>
                    <div class="table-row">
                        <div class="table-cell">Sample Data 1</div>
                        <div class="table-cell">Sample Data 2</div>
                        <div class="table-cell">Rs. 1,250</div>
                    </div>
                    <div class="table-row">
                        <div class="table-cell">Sample Data 3</div>
                        <div class="table-cell">Sample Data 4</div>
                        <div class="table-cell">Rs. 2,500</div>
                    </div>
                </div>
                <div class="sample-note">
                    <i class="fas fa-info-circle"></i>
                    <p>This is a preview. Actual report will contain real data.</p>
                </div>
            </div>
        `;
    }, 800);
}

function getReportName(reportType) {
    const reportNames = {
        'order-history': 'Order History Report',
        'pending-pickup': 'Pending Pickup/Delivery Report',
        'sales-volume': 'Sales Volume & Value Report',
        'sales-payment': 'Sales & Payment Reconciliation',
        'system-financial': 'System Financial Summary',
        'daily-cash': 'Daily Cash Position Report',
        'cash-collection-delay': 'Cash Collection Delay Report',
        'cod-exception': 'COD Exception Report',
        'inventory-stock': 'Current Inventory / Stock Report',
        'category-performance': 'Product Category Performance',
        'stock-movement': 'Stock Movement Report',
        'group-performance': 'Group Farmer Performance',
        'farmer-registration': 'Farmer Registration Status',
        'system-adoption': 'System Adoption & User Count',
        'user-access': 'User Access & Role Management',
        'data-quality': 'Data Quality Report',
        'dispute-feedback': 'Dispute & Feedback Log',
        'regional-cod': 'Regional Performance Report',
        'quality-grade': 'Quality Grade Performance',
        'order-fulfillment': 'Order Fulfillment Timeline',
        'financial-audit': 'Financial Audit & Transaction',
        'inventory-cash-reconciliation': 'Inventory vs Cash Reconciliation',
        'farmer-payment-delay': 'Farmer Payment Delay Risk',
        'geographic-sales': 'Geographic Sales Density',
        'buyer-payment-behavior': 'Buyer Payment Behavior',
        'product-taxonomy': 'Product Taxonomy Report',
        'cod-payment': 'COD Payment Reconciliation',
        'cod-revenue': 'COD Revenue Forecast'
    };

    return reportNames[reportType] || 'Custom Report';
}

function previewReport() {
    const reportType = document.getElementById('report_type').value;
    const fromDate = document.getElementById('from_date').value;
    const toDate = document.getElementById('to_date').value;

    if (!reportType) {
        Swal.fire({
            icon: 'warning',
            title: 'Select Report',
            text: 'Please select a report type first',
            confirmButtonColor: '#10B981',
            background: 'var(--card-bg)',
            color: 'var(--text-dark)'
        });
        return;
    }

    if (!fromDate || !toDate) {
        Swal.fire({
            icon: 'warning',
            title: 'Select Dates',
            text: 'Please select both from and to dates',
            confirmButtonColor: '#10B981',
            background: 'var(--card-bg)',
            color: 'var(--text-dark)'
        });
        return;
    }

    updatePreview();

    Swal.fire({
        icon: 'success',
        title: 'Preview Generated',
        text: 'Report preview updated with current settings',
        confirmButtonColor: '#10B981',
        background: 'var(--card-bg)',
        color: 'var(--text-dark)',
        timer: 1500
    });
}

function resetForm() {
    document.getElementById('customReportForm').reset();
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('from_date').value = "{{ date('Y-m-d', strtotime('-30 days')) }}";
    document.getElementById('to_date').value = today;
    document.getElementById('previewData').innerHTML = '';

    Swal.fire({
        icon: 'success',
        title: 'Form Reset',
        text: 'All form fields have been reset',
        confirmButtonColor: '#10B981',
        background: 'var(--card-bg)',
        color: 'var(--text-dark)',
        timer: 1500
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
            background: 'var(--card-bg)',
            color: 'var(--text-dark)'
        });
        return;
    }

    if (!fromDate || !toDate) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Dates',
            text: 'Please select both date ranges',
            confirmButtonColor: '#10B981',
            background: 'var(--card-bg)',
            color: 'var(--text-dark)'
        });
        return;
    }

    if (new Date(fromDate) > new Date(toDate)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Date',
            text: 'From date cannot be after to date',
            confirmButtonColor: '#10B981',
            background: 'var(--card-bg)',
            color: 'var(--text-dark)'
        });
        return;
    }

    Swal.fire({
        title: 'Generating Report',
        text: 'Please wait while we generate your report...',
        allowOutsideClick: false,
        background: 'var(--card-bg)',
        color: 'var(--text-dark)',
        didOpen: () => {
            Swal.showLoading();
        }
    });

    this.submit();
});
</script>

<style>
.generate-report-container {
    padding: 12px;
    animation: fadeIn 0.3s ease;
}

.report-form-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

@media (max-width: 992px) {
    .report-form-section {
        grid-template-columns: 1fr;
        gap: 16px;
    }
}

.form-card, .preview-card {
    background: var(--card-bg);
    border-radius: 8px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    transition: transform 0.3s ease;
}

.form-card:hover, .preview-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.form-header {
    padding: 16px;
    background: linear-gradient(135deg, var(--primary-accent), var(--primary-dark));
    color: white;
}

.form-header h3 {
    font-size: 16px;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
}

.form-header p {
    opacity: 0.9;
    font-size: 12px;
}

.form-section {
    padding: 16px;
    border-bottom: 1px solid #f3f4f6;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 16px;
}

.section-header i {
    color: var(--primary-accent);
    font-size: 14px;
}

.section-header h4 {
    color: var(--text-dark);
    font-size: 14px;
    margin: 0;
    font-weight: 600;
}

.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    color: var(--text-dark);
    font-weight: 500;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 13px;
    transition: all 0.3s ease;
    background: var(--card-bg);
    color: var(--text-dark);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-accent);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.form-control:disabled {
    background: #f9fafb;
    cursor: not-allowed;
}

.date-range-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.advanced-filters {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 12px;
}

.output-settings {
    display: grid;
    gap: 16px;
}

.radio-group {
    display: flex;
    gap: 16px;
}

.radio-label {
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    color: var(--text-dark);
    font-size: 13px;
    padding: 8px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.radio-label:hover {
    border-color: var(--primary-accent);
    background: rgba(16, 185, 129, 0.05);
}

.radio-label input[type="radio"] {
    margin: 0;
}

.checkbox-group {
    display: flex;
    gap: 8px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    color: var(--text-dark);
    font-size: 13px;
}

.checkbox-label input[type="checkbox"] {
    margin: 0;
}

.form-actions {
    padding: 16px;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    background: #f9fafb;
}

.btn-reset, .btn-generate, .btn-preview {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-reset {
    background: var(--card-bg);
    color: var(--text-dark);
    border: 1px solid #e5e7eb;
}

.btn-reset:hover {
    background: #f3f4f6;
    transform: translateY(-1px);
}

.btn-generate {
    background: var(--primary-accent);
    color: white;
}

.btn-generate:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.btn-preview {
    background: var(--blue);
    color: white;
}

.btn-preview:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.preview-header {
    padding: 16px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.preview-header h3 {
    color: var(--text-dark);
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    font-weight: 600;
}

.btn-refresh {
    padding: 6px 12px;
    border: 1px solid #e5e7eb;
    background: var(--card-bg);
    color: var(--text-dark);
    border-radius: 6px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-refresh:hover {
    background: var(--primary-accent);
    color: white;
    border-color: var(--primary-accent);
    transform: translateY(-1px);
}

.preview-content {
    padding: 16px;
    min-height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.preview-placeholder {
    text-align: center;
    color: var(--muted);
}

.preview-placeholder i {
    font-size: 36px;
    margin-bottom: 12px;
    color: #e5e7eb;
}

.preview-placeholder h4 {
    color: var(--text-dark);
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 600;
}

.preview-placeholder p {
    font-size: 13px;
    max-width: 250px;
    margin: 0 auto;
}

.preview-loading {
    text-align: center;
    color: var(--muted);
}

.preview-loading i {
    font-size: 20px;
    margin-bottom: 8px;
    animation: spin 1s linear infinite;
}

.preview-sample {
    width: 100%;
}

.sample-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.sample-header h5 {
    color: var(--text-dark);
    font-size: 14px;
    margin: 0;
    font-weight: 600;
}

.sample-badge {
    background: var(--accent-amber);
    color: white;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
}

.sample-period {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--muted);
    font-size: 12px;
    margin-bottom: 16px;
}

.sample-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 16px;
}

.stat-item {
    background: #f9fafb;
    padding: 12px;
    border-radius: 6px;
    text-align: center;
    border: 1px solid #f3f4f6;
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.stat-value {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 4px;
}

.stat-label {
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.sample-filters {
    margin-bottom: 16px;
}

.sample-filters h6 {
    color: var(--text-dark);
    font-size: 13px;
    margin-bottom: 8px;
    font-weight: 600;
}

.filter-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.filter-tag {
    background: var(--primary-accent);
    color: white;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
}

.sample-table {
    border: 1px solid #f3f4f6;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 16px;
}

.table-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    border-bottom: 1px solid #f3f4f6;
}

.table-row:last-child {
    border-bottom: none;
}

.table-row.header {
    background: #f9fafb;
    font-weight: 600;
}

.table-cell {
    padding: 8px 12px;
    font-size: 12px;
    color: var(--text-dark);
}

.table-row.header .table-cell {
    color: var(--text-dark);
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.sample-note {
    background: #fef3c7;
    border: 1px solid #f59e0b;
    border-radius: 6px;
    padding: 12px;
    display: flex;
    gap: 8px;
    align-items: flex-start;
}

.sample-note i {
    color: #f59e0b;
    font-size: 14px;
    margin-top: 2px;
}

.sample-note p {
    color: #92400e;
    font-size: 12px;
    margin: 0;
    line-height: 1.5;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .date-range-grid,
    .advanced-filters {
        grid-template-columns: 1fr;
    }

    .radio-group {
        flex-direction: column;
        gap: 8px;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn-reset, .btn-generate, .btn-preview {
        width: 100%;
        justify-content: center;
    }

    .sample-stats {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .generate-report-container {
        padding: 8px;
    }

    .form-section {
        padding: 12px;
    }

    .form-actions {
        padding: 12px;
    }

    .preview-content {
        padding: 12px;
        min-height: 250px;
    }
}
</style>
@endsection
