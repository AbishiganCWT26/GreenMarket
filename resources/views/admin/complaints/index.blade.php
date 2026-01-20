@extends('admin.layouts.admin_master')

@section('title', 'User Complaints')
@section('page-title', 'User Complaints Management')
@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
:root {
	--primary-green: #10B981;
	--dark-green: #059669;
	--body-bg: #f6f8fa;
	--card-bg: #ffffff;
	--text-color: #0f1724;
	--muted: #6b7280;
	--accent-amber: #f59e0b;
	--blue: #3b82f6;
	--purple: #8b5cf6;
	--yellow: #f59e0b;
	--shadow-sm: 0 1px 3px rgba(15,23,36,0.04);
	--shadow-md: 0 7px 15px rgba(15,23,36,0.08);
}

.complaints-container {
    padding: 25px;
    background: var(--body-bg);
    min-height: calc(100vh - 80px);
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 15px;
}

.header-section h1 {
    font-size: 28px;
    color: var(--text-color);
    margin: 0;
    font-weight: 700;
}

.header-section h1 i {
    color: var(--primary-green);
    margin-right: 12px;
}

.controls-section {
    display: flex;
    gap: 12px;
    align-items: center;
}

.bulk-actions {
    display: flex;
    gap: 10px;
}

.filter-section {
    background: linear-gradient(135deg, var(--card-bg) 0%, #f8fafc 100%);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: var(--shadow-md);
    border: 1px solid #e5e7eb;
    position: relative;
    overflow: hidden;
}

.filter-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(to bottom, var(--primary-green), var(--dark-green));
}

.filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.filter-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-color);
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-title i {
    color: var(--primary-green);
}

.filter-controls {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.filter-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-item label {
    font-size: 13px;
    color: var(--muted);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
}

.filter-item select, .filter-item input {
    padding: 12px 15px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    background: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    width: 100%;
}

.filter-item select:focus, .filter-item input:focus {
    outline: none;
    border-color: var(--primary-green);
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
    transform: translateY(-2px);
}

.filter-item select:hover, .filter-item input:hover {
    border-color: #cbd5e1;
}

.filter-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
    grid-column: 1 / -1;
}

.filter-btn {
    padding: 10px 20px;
    background: var(--primary-green);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.filter-btn:hover {
    background: var(--dark-green);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.clear-btn {
    padding: 10px 20px;
    background: #f1f5f9;
    color: var(--muted);
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.clear-btn:hover {
    background: #e5e7eb;
    transform: translateY(-2px);
}

.complaints-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 25px;
    margin-top: 20px;
}

.complaint-card {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 25px;
    box-shadow: var(--shadow-sm);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid transparent;
    position: relative;
    overflow: hidden;
    animation: slideUp 0.6s ease;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.complaint-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary-green);
}

.complaint-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--primary-green);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.complaint-card:hover::before {
    opacity: 1;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f1f5f9;
}

.complaint-meta {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.complaint-id {
    font-size: 12px;
    color: var(--muted);
    font-weight: 500;
}

.complaint-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-color);
    margin: 0;
    line-height: 1.4;
}

.status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.status-new { background: #dbeafe; color: #1d4ed8; }
.status-in_progress { background: #fef3c7; color: #92400e; }
.status-resolved { background: #d1fae5; color: #065f46; }
.status-rejected { background: #fee2e2; color: #991b1b; }

.status-badge:hover {
    transform: scale(1.05);
}

.complaint-details {
    margin: 20px 0;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
    padding: 10px;
    background: #f8fafc;
    border-radius: 8px;
    transition: background 0.3s ease;
}

.detail-row:hover {
    background: #f1f5f9;
}

.detail-label {
    font-weight: 600;
    color: var(--muted);
    font-size: 13px;
}

.detail-value {
    color: var(--text-color);
    font-weight: 500;
    text-align: right;
    max-width: 200px;
    word-break: break-word;
}

.complaint-description {
    background: #f8fafc;
    padding: 15px;
    border-radius: 10px;
    margin: 20px 0;
    border-left: 3px solid var(--primary-green);
}

.description-label {
    font-weight: 600;
    color: var(--muted);
    margin-bottom: 8px;
    font-size: 14px;
}

.description-text {
    color: var(--text-color);
    line-height: 1.6;
    font-size: 14px;
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #f1f5f9;
}

.timestamp {
    font-size: 12px;
    color: var(--muted);
    display: flex;
    align-items: center;
    gap: 6px;
}

.action-buttons {
    display: flex;
    gap: 10px;
}

.btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-view {
    background: var(--blue);
    color: white;
}

.btn-view:hover {
    background: #2563eb;
    transform: translateY(-2px);
}

.btn-status {
    background: var(--primary-green);
    color: white;
}

.btn-status:hover {
    background: var(--dark-green);
    transform: translateY(-2px);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    grid-column: 1 / -1;
}

.empty-state i {
    font-size: 64px;
    color: #cbd5e1;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: var(--muted);
    font-size: 20px;
    margin-bottom: 10px;
}

.empty-state p {
    color: #94a3b8;
    font-size: 14px;
}

.pagination-container {
    margin-top: 40px;
    display: flex;
    justify-content: center;
}

/* New Pagination Styles */
.pagination {
    font-weight: bold;
    font-size: 16px;
    font-family: 'helvetica neue', helvetica, arial, sans-serif;
    display: flex;
    gap: 8px;
    list-style: none;
    padding: 0;
}

.page-item {
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s ease;
}

.page-item:hover {
    transform: translateY(-2px);
}

.page-link {
    padding: 8px 16px;
    border: 1px solid rgba(0, 0, 0, 0.15);
    border-radius: 3px;
    background: linear-gradient(to bottom, #e0e0e0, #b8b8b8);
    box-shadow: 0 0 6px rgba(0, 0, 0, 0.6), inset 0 1px rgba(255, 255, 255, 0.4);
    color: var(--text-color);
    text-decoration: none;
    text-shadow: 0 1px 1px rgba(255, 255, 255, 0.72);
    display: block;
    font-weight: 600;
    transition: all 0.3s ease;
}

.page-link:hover {
    background: linear-gradient(to bottom,  #059669, #03422e);
    color: #ffffff;
    border-color: rgba(0, 0, 0, 0.08);
}

.page-item.active .page-link {
    background: linear-gradient(to bottom, #10B981, #059669);
    color: white;
    border-color: var(--primary-green);
    box-shadow: 0 0 6px rgba(0, 0, 0, 0.6), inset 0 1px rgba(255, 255, 255, 0.4);
}

.page-item.active .page-link:hover {
    background: linear-gradient(to bottom, #10B981, #059669);
    cursor: default;
}

.page-item.prev .page-link:before {
    content: "« ";
    font-weight: normal;
}

.page-item.next .page-link:after {
    content: " »";
    font-weight: normal;
}

.page-item.next .page-link:hover,
.page-item.prev .page-link:hover {
    border-color: rgba(0, 0, 0, 0.08);
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0.1));
    box-shadow: 0 0 6px rgba(0, 0, 0, 0.6), inset 0 1px rgba(255, 255, 255, 0.1);
    color: #f0f0f0;
    text-shadow: none;
}

.page-item.disabled .page-link {
    opacity: 0.5;
    cursor: not-allowed;
}

.page-item.disabled .page-link:hover {
    background: linear-gradient(to bottom, #e0e0e0, #b8b8b8);
    color: var(--text-color);
    transform: none;
}

.bulk-select {
    padding: 10px 15px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.bulk-select:hover {
    border-color: var(--primary-green);
}

.bulk-update-btn {
    padding: 10px 20px;
    background: var(--purple);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.bulk-update-btn:hover {
    background: #7c3aed;
    transform: translateY(-2px);
}

.status-selector {
    position: relative;
}

.status-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    border-radius: 8px;
    box-shadow: var(--shadow-md);
    padding: 10px;
    min-width: 180px;
    display: none;
    z-index: 1000;
    animation: fadeInUp 0.2s ease;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.status-dropdown.show {
    display: block;
}

.status-option {
    padding: 10px 15px;
    cursor: pointer;
    border-radius: 6px;
    margin: 2px 0;
    transition: background 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.status-option:hover {
    background: #f1f5f9;
}

.checkbox-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 15px;
}

.checkbox-wrapper input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.checkbox-wrapper label {
    cursor: pointer;
    color: var(--text-color);
    font-size: 14px;
}

@media (max-width: 1200px) {
    .complaints-grid {
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }
}

@media (max-width: 992px) {
    .complaints-container {
        padding: 20px;
    }

    .header-section {
        flex-direction: column;
        align-items: flex-start;
        gap: 20px;
    }

    .controls-section {
        width: 100%;
        justify-content: space-between;
    }

    .complaints-grid {
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    }

    .filter-controls {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
}

@media (max-width: 768px) {
    .complaints-grid {
        grid-template-columns: 1fr;
    }

    .filter-controls {
        grid-template-columns: 1fr;
    }

    .filter-item {
        width: 100%;
    }

    .filter-item select, .filter-item input {
        width: 100%;
    }

    .card-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }

    .card-footer {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }

    .action-buttons {
        width: 100%;
        justify-content: flex-end;
    }

    .filter-actions {
        flex-direction: column;
    }

    .filter-btn, .clear-btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .complaints-container {
        padding: 15px;
    }

    .header-section h1 {
        font-size: 22px;
    }

    .complaint-card {
        padding: 20px;
    }

    .complaint-title {
        font-size: 16px;
    }

    .btn {
        padding: 8px 16px;
        font-size: 13px;
    }

    .controls-section {
        flex-direction: column;
        gap: 10px;
    }

    .bulk-actions {
        width: 100%;
        justify-content: space-between;
    }

    .filter-section {
        padding: 20px 15px;
    }

    .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }

    .page-link {
        padding: 6px 12px;
        font-size: 14px;
    }
}

@media (min-width: 1000px) {
    .complaints-grid {
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    }

    .filter-controls {
        grid-template-columns: repeat(4, 1fr);
    }
}
</style>
@endsection

@section('content')
<div class="complaints-container">
    <div class="header-section">
        <h1><i class="fa-solid fa-flag"></i> User Complaints</h1>

        <div class="controls-section">
            <div class="bulk-actions">
                <div class="checkbox-wrapper">
                    <input type="checkbox" id="selectAllComplaints">
                    <label for="selectAllComplaints">Select All</label>
                </div>

                <div class="status-selector">
                    <button class="bulk-update-btn" id="bulkUpdateBtn">
                        <i class="fa-solid fa-layer-group"></i> Bulk Update
                    </button>
                    <div class="status-dropdown" id="bulkStatusDropdown">
                        <div class="status-option" data-status="new">
                            <i class="fa-solid fa-circle-plus" style="color: #1d4ed8;"></i> Mark as New
                        </div>
                        <div class="status-option" data-status="in_progress">
                            <i class="fa-solid fa-spinner" style="color: #92400e;"></i> Mark as In Progress
                        </div>
                        <div class="status-option" data-status="resolved">
                            <i class="fa-solid fa-check-circle" style="color: #065f46;"></i> Mark as Resolved
                        </div>
                        <div class="status-option" data-status="rejected">
                            <i class="fa-solid fa-times-circle" style="color: #991b1b;"></i> Mark as Rejected
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="filter-section">
        <div class="filter-header">
            <div class="filter-title">
                <i class="fa-solid fa-sliders"></i> Filter Complaints
            </div>
        </div>

        <div class="filter-controls">
            <div class="filter-item">
                <label><i class="fa-solid fa-filter"></i> Status</label>
                <select id="statusFilter">
                    <option value="">All Statuses</option>
                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div class="filter-item">
                <label><i class="fa-solid fa-calendar"></i> From Date</label>
                <input type="date" id="fromDateFilter" value="{{ request('fromDate') }}" max="{{ date('Y-m-d') }}">
            </div>

            <div class="filter-item">
                <label><i class="fa-solid fa-calendar"></i> To Date</label>
                <input type="date" id="toDateFilter" value="{{ request('toDate') }}" max="{{ date('Y-m-d') }}">
            </div>

            <div class="filter-item">
                <label><i class="fa-solid fa-exclamation-circle"></i> Complaint Type</label>
                <select id="typeFilter">
                    <option value="">All Types</option>
                    <option value="product_quality" {{ request('type') == 'product_quality' ? 'selected' : '' }}>Product Quality</option>
                    <option value="payment_issue" {{ request('type') == 'payment_issue' ? 'selected' : '' }}>Payment Issue</option>
                    <option value="wrong_location" {{ request('type') == 'wrong_location' ? 'selected' : '' }}>Wrong Location</option>
                    <option value="farmer_contact" {{ request('type') == 'farmer_contact' ? 'selected' : '' }}>Farmer Contact</option>
                    <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="filter-actions">
                <button class="filter-btn" id="applyFiltersBtn">
                    <i class="fa-solid fa-filter"></i> Apply Filters
                </button>
                <button class="clear-btn" id="clearFiltersBtn">
                    <i class="fa-solid fa-times"></i> Clear Filters
                </button>
            </div>
        </div>
    </div>

    @if($complaints->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-flag"></i>
            <h3>No Complaints Found</h3>
            <p>There are currently no user complaints to display.</p>
        </div>
    @else
        <div class="complaints-grid">
            @foreach($complaints as $complaint)
                <div class="complaint-card" data-complaint-id="{{ $complaint->id }}">
                    <div class="card-header">
                        <div class="complaint-meta">
                            <div class="complaint-id">#COMP-{{ str_pad($complaint->id, 6, '0', STR_PAD_LEFT) }}</div>
                            <h3 class="complaint-title">
                                <i class="fa-solid fa-user"></i>
                                {{ $complaint->complainant->username ?? 'Unknown User' }}
                            </h3>
                        </div>

                        <div class="status-selector">
                            <div class="status-badge status-{{ $complaint->status }}"
                                 data-status="{{ $complaint->status }}"
                                 data-complaint-id="{{ $complaint->id }}">
                                <i class="fa-solid fa-circle"></i>
                                {{ str_replace('_', ' ', ucfirst($complaint->status)) }}
                                <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>
                            </div>

                            <div class="status-dropdown">
                                <div class="status-option" data-status="new">
                                    <i class="fa-solid fa-circle-plus" style="color: #1d4ed8;"></i> New
                                </div>
                                <div class="status-option" data-status="in_progress">
                                    <i class="fa-solid fa-spinner" style="color: #92400e;"></i> In Progress
                                </div>
                                <div class="status-option" data-status="resolved">
                                    <i class="fa-solid fa-check-circle" style="color: #065f46;"></i> Resolved
                                </div>
                                <div class="status-option" data-status="rejected">
                                    <i class="fa-solid fa-times-circle" style="color: #991b1b;"></i> Rejected
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="complaint-details">
                        <div class="detail-row">
                            <span class="detail-label">Type</span>
                            <span class="detail-value">{{ str_replace('_', ' ', ucfirst($complaint->complaint_type)) }}</span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Against</span>
                            <span class="detail-value">
                                @if($complaint->against_user_id)
                                    {{ $complaint->againstUser->username ?? 'Unknown User' }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Order</span>
                            <span class="detail-value">
                                @if($complaint->related_order_id)
                                    #ORD-{{ str_pad($complaint->related_order_id, 6, '0', STR_PAD_LEFT) }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="complaint-description">
                        <div class="description-label">Description</div>
                        <div class="description-text">{{ $complaint->description }}</div>
                    </div>

                    <div class="card-footer">
                        <div class="timestamp">
                            <i class="fa-solid fa-clock"></i>
                            {{ $complaint->created_at->format('M d, Y h:i A') }}
                        </div>

                        <div class="action-buttons">
                            <button class="btn btn-view view-details-btn" data-id="{{ $complaint->id }}">
                                <i class="fa-solid fa-eye"></i> View Details
                            </button>

                            <label class="checkbox-wrapper bulk-checkbox">
                                <input type="checkbox" class="complaint-checkbox" value="{{ $complaint->id }}">
                                <span>Select</span>
                            </label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pagination-container">
            {{ $complaints->links('vendor.pagination.custom1') }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let selectedComplaints = new Set();

    // Set max date for date inputs to today
    const today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('input[type="date"]').forEach(input => {
        input.setAttribute('max', today);
    });

    // Date validation
    function validateDates() {
        const fromDate = document.getElementById('fromDateFilter').value;
        const toDate = document.getElementById('toDateFilter').value;

        if (fromDate && toDate && fromDate > toDate) {
            showWarning('From date cannot be later than To date');
            return false;
        }

        return true;
    }

    // Status update functionality
    document.querySelectorAll('.status-badge').forEach(badge => {
        badge.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = this.nextElementSibling;
            dropdown.classList.toggle('show');
        });
    });

    // Status option selection
    document.querySelectorAll('.status-option').forEach(option => {
        option.addEventListener('click', function() {
            const status = this.getAttribute('data-status');
            const complaintId = this.closest('.status-selector').querySelector('.status-badge').getAttribute('data-complaint-id');

            if (complaintId) {
                updateComplaintStatus(complaintId, status);
            }

            this.closest('.status-dropdown').classList.remove('show');
        });
    });

    // View details button
    document.querySelectorAll('.view-details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const complaintId = this.getAttribute('data-id');
            Swal.fire({
                title: 'Loading Details...',
                text: 'Please wait while we fetch complaint details',
                showConfirmButton: false,
                allowOutsideClick: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/admin/complaints/${complaintId}/details`)
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        showComplaintDetails(data.complaint);
                    } else {
                        showError('Failed to load complaint details');
                    }
                })
                .catch(error => {
                    Swal.close();
                    showError('Network error: ' + error.message);
                });
        });
    });

    // Bulk update functionality
    const bulkUpdateBtn = document.getElementById('bulkUpdateBtn');
    const bulkStatusDropdown = document.getElementById('bulkStatusDropdown');

    if (bulkUpdateBtn) {
        bulkUpdateBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (selectedComplaints.size === 0) {
                showWarning('Please select at least one complaint to update');
                return;
            }
            bulkStatusDropdown.classList.toggle('show');
        });
    }

    // Bulk status option selection
    document.querySelectorAll('#bulkStatusDropdown .status-option').forEach(option => {
        option.addEventListener('click', function() {
            const status = this.getAttribute('data-status');
            bulkUpdateStatus(status);
            bulkStatusDropdown.classList.remove('show');
        });
    });

    // Checkbox functionality
    const selectAllCheckbox = document.getElementById('selectAllComplaints');
    const complaintCheckboxes = document.querySelectorAll('.complaint-checkbox');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            complaintCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
                if (isChecked) {
                    selectedComplaints.add(checkbox.value);
                } else {
                    selectedComplaints.delete(checkbox.value);
                }
            });
        });
    }

    // Individual checkbox handling
    complaintCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                selectedComplaints.add(this.value);
            } else {
                selectedComplaints.delete(this.value);
                selectAllCheckbox.checked = false;
            }
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.status-selector')) {
            document.querySelectorAll('.status-dropdown').forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }

        if (!e.target.closest('.status-selector')) {
            bulkStatusDropdown.classList.remove('show');
        }
    });

    // Filter functionality
    const applyFiltersBtn = document.getElementById('applyFiltersBtn');
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');

    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            if (!validateDates()) {
                return;
            }
            applyFilters();
        });
    }

    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            clearFilters();
        });
    }

    function applyFilters() {
        const params = new URLSearchParams();

        const statusFilter = document.getElementById('statusFilter');
        const fromDateFilter = document.getElementById('fromDateFilter');
        const toDateFilter = document.getElementById('toDateFilter');
        const typeFilter = document.getElementById('typeFilter');

        if (statusFilter && statusFilter.value) {
            params.set('status', statusFilter.value);
        }

        if (fromDateFilter && fromDateFilter.value) {
            params.set('fromDate', fromDateFilter.value);
        }

        if (toDateFilter && toDateFilter.value) {
            params.set('toDate', toDateFilter.value);
        }

        if (typeFilter && typeFilter.value) {
            params.set('type', typeFilter.value);
        }

        window.location.href = `/admin/complaints?${params.toString()}`;
    }

    function clearFilters() {
        window.location.href = '/admin/complaints';
    }

    // Date inputs validation
    document.querySelectorAll('#fromDateFilter, #toDateFilter').forEach(input => {
        input.addEventListener('change', function() {
            if (!validateDates()) {
                this.value = '';
            }
        });
    });

    function updateComplaintStatus(complaintId, newStatus) {
        Swal.fire({
            title: 'Updating Status...',
            text: 'Please wait while we update the complaint status',
            showConfirmButton: false,
            allowOutsideClick: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(`/admin/complaints/${complaintId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                updateStatusUI(complaintId, newStatus);
                showSuccess(data.message);
            } else {
                showError(data.message);
            }
        })
        .catch(error => {
            Swal.close();
            showError('Network error: ' + error.message);
        });
    }

    function bulkUpdateStatus(newStatus) {
        Swal.fire({
            title: 'Updating Multiple Complaints',
            text: `Are you sure you want to update ${selectedComplaints.size} complaint(s) to "${newStatus.replace('_', ' ')}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, Update',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Updating...',
                    text: 'Please wait while we update the complaints',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('/admin/complaints/bulk-update-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        complaint_ids: Array.from(selectedComplaints),
                        status: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        // Update UI for selected complaints
                        selectedComplaints.forEach(id => {
                            updateStatusUI(id, newStatus);
                        });

                        // Clear selection
                        selectedComplaints.clear();
                        complaintCheckboxes.forEach(cb => cb.checked = false);
                        selectAllCheckbox.checked = false;

                        showSuccess(data.message);
                    } else {
                        showError(data.message);
                    }
                })
                .catch(error => {
                    Swal.close();
                    showError('Network error: ' + error.message);
                });
            }
        });
    }

    function updateStatusUI(complaintId, newStatus) {
        const badge = document.querySelector(`.status-badge[data-complaint-id="${complaintId}"]`);
        if (badge) {
            // Remove old status class
            badge.classList.remove('status-new', 'status-in_progress', 'status-resolved', 'status-rejected');

            // Add new status class
            badge.classList.add(`status-${newStatus}`);

            // Update text
            const statusText = newStatus.replace('_', ' ').charAt(0).toUpperCase() + newStatus.replace('_', ' ').slice(1);
            badge.innerHTML = `<i class="fa-solid fa-circle"></i> ${statusText} <i class="fa-solid fa-chevron-down" style="font-size: 10px;"></i>`;

            // Update data attribute
            badge.setAttribute('data-status', newStatus);
        }
    }

    function showComplaintDetails(complaint) {
        const detailsHtml = `
            <div style="text-align: left; max-width: 500px;">
                <h3 style="color: #10B981; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                    <i class="fa-solid fa-flag"></i> Complaint Details
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div style="background: #f8fafc; padding: 12px; border-radius: 8px;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Complaint ID</div>
                        <div style="font-weight: 600; color: #0f1724;">#COMP-${complaint.id.toString().padStart(6, '0')}</div>
                    </div>

                    <div style="background: #f8fafc; padding: 12px; border-radius: 8px;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Status</div>
                        <div style="font-weight: 600; color: #0f1724;">${complaint.status.replace('_', ' ')}</div>
                    </div>

                    <div style="background: #f8fafc; padding: 12px; border-radius: 8px;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Type</div>
                        <div style="font-weight: 600; color: #0f1724;">${complaint.complaint_type.replace('_', ' ')}</div>
                    </div>

                    <div style="background: #f8fafc; padding: 12px; border-radius: 8px;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">Created</div>
                        <div style="font-weight: 600; color: #0f1724;">${new Date(complaint.created_at).toLocaleDateString()}</div>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <div style="font-size: 14px; color: #6b7280; margin-bottom: 8px; font-weight: 600;">
                        <i class="fa-solid fa-user"></i> Complainant
                    </div>
                    <div style="background: #f8fafc; padding: 15px; border-radius: 8px;">
                        <div style="font-weight: 600; color: #0f1724;">${complaint.complainant?.username || 'Unknown User'}</div>
                        <div style="font-size: 13px; color: #6b7280; margin-top: 5px;">Role: ${complaint.complainant_role}</div>
                    </div>
                </div>

                ${complaint.against_user_id ? `
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 14px; color: #6b7280; margin-bottom: 8px; font-weight: 600;">
                        <i class="fa-solid fa-user-slash"></i> Complained Against
                    </div>
                    <div style="background: #f8fafc; padding: 15px; border-radius: 8px;">
                        <div style="font-weight: 600; color: #0f1724;">${complaint.against_user?.username || 'Unknown User'}</div>
                    </div>
                </div>
                ` : ''}

                ${complaint.related_order_id ? `
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 14px; color: #6b7280; margin-bottom: 8px; font-weight: 600;">
                        <i class="fa-solid fa-receipt"></i> Related Order
                    </div>
                    <div style="background: #f8fafc; padding: 15px; border-radius: 8px;">
                        <div style="font-weight: 600; color: #0f1724;">Order #${complaint.related_order_id}</div>
                    </div>
                </div>
                ` : ''}

                <div style="margin-bottom: 20px;">
                    <div style="font-size: 14px; color: #6b7280; margin-bottom: 8px; font-weight: 600;">
                        <i class="fa-solid fa-file-alt"></i> Description
                    </div>
                    <div style="background: #f8fafc; padding: 15px; border-radius: 8px; line-height: 1.6;">
                        ${complaint.description}
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px; border-top: 1px solid #f1f5f9; padding-top: 15px;">
                    <button onclick="updateComplaintStatus(${complaint.id}, 'in_progress')"
                            style="flex: 1; background: #f59e0b; color: white; border: none; padding: 10px; border-radius: 6px; cursor: pointer;">
                        <i class="fa-solid fa-spinner"></i> Mark In Progress
                    </button>
                    <button onclick="updateComplaintStatus(${complaint.id}, 'resolved')"
                            style="flex: 1; background: #10B981; color: white; border: none; padding: 10px; border-radius: 6px; cursor: pointer;">
                        <i class="fa-solid fa-check"></i> Mark Resolved
                    </button>
                </div>
            </div>
        `;

        Swal.fire({
            title: 'Complaint Details',
            html: detailsHtml,
            width: 600,
            showCloseButton: true,
            showConfirmButton: false
        });
    }

    function showSuccess(message) {
        Swal.fire({
            title: 'Success!',
            text: message,
            icon: 'success',
            confirmButtonColor: '#10B981',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true
        });
    }

    function showError(message) {
        Swal.fire({
            title: 'Error!',
            text: message,
            icon: 'error',
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'OK'
        });
    }

    function showWarning(message) {
        Swal.fire({
            title: 'Warning',
            text: message,
            icon: 'warning',
            confirmButtonColor: '#F59E0B',
            confirmButtonText: 'OK'
        });
    }

    // Make updateComplaintStatus available globally for the details modal
    window.updateComplaintStatus = updateComplaintStatus;
});
</script>
@endsection
