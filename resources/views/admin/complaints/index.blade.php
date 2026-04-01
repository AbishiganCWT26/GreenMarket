@extends('admin.layouts.admin_master')

@section('title', 'Complaints Management')
@section('page-title', 'Complaints Dashboard')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/Admin/complaints-index.css') }}">
<style>
    .swal2-image {
		margin: 0em auto 0em !important;
	}
</style>

@endsection

@section('content')
<div class="complaint-dashboard">
    <!-- Header Section -->
    <div class="complaint-header">
        <div class="header-left">
            <div class="header-icon">
                <i class="fas fa-message"></i>
            </div>
            <div class="header-text">
                <h1>Complaints Management</h1>
                <p>Manage and track user complaints efficiently</p>
            </div>
        </div>
        
        <!-- Bulk Actions -->
        <div class="bulk-actions">
            <label class="select-all">
                <input type="checkbox" id="selectAllComplaints">
                <span>Select All</span>
            </label>
            <div class="bulk-selector">
                <button class="btn-bulk" id="bulkUpdateBtn">
                    <i class="fas fa-layer-group"></i>
                    <span>Bulk Actions</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="bulk-dropdown" id="bulkStatusDropdown">
                    <button class="bulk-option" data-status="new">
                        <i class="fas fa-circle-plus" style="color: #4361ee;"></i>
                        Mark as New
                    </button>
                    <button class="bulk-option" data-status="in_progress">
                        <i class="fas fa-spinner" style="color: #ffb703;"></i>
                        Mark as In Progress
                    </button>
                    <button class="bulk-option" data-status="resolved">
                        <i class="fas fa-check-circle" style="color: #06d6a0;"></i>
                        Mark as Resolved
                    </button>
                    <button class="bulk-option" data-status="rejected">
                        <i class="fas fa-times-circle" style="color: #e63946;"></i>
                        Mark as Rejected
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="search-section">
        <div class="search-wrapper">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search by ID, user, description..." value="{{ request('search') }}">
            </div>
            <button class="btn-bulk" id="toggleFilterBtn">
                <i class="fas fa-sliders-h"></i>
                <span>Filters</span>
            </button>
        </div>

        <!-- Filter Chips -->
        <div class="filter-chips">
            <button class="filter-chip {{ !request('status') ? 'active' : '' }}" data-filter="status" data-value="">
                <i class="fas fa-list"></i> All
            </button>
            <button class="filter-chip {{ request('status') == 'new' ? 'active' : '' }}" data-filter="status" data-value="new">
                <i class="fas fa-circle-plus"></i> New
            </button>
            <button class="filter-chip {{ request('status') == 'in_progress' ? 'active' : '' }}" data-filter="status" data-value="in_progress">
                <i class="fas fa-spinner"></i> In Progress
            </button>
            <button class="filter-chip {{ request('status') == 'resolved' ? 'active' : '' }}" data-filter="status" data-value="resolved">
                <i class="fas fa-check-circle"></i> Resolved
            </button>
            <button class="filter-chip {{ request('status') == 'rejected' ? 'active' : '' }}" data-filter="status" data-value="rejected">
                <i class="fas fa-times-circle"></i> Rejected
            </button>
        </div>

        <!-- Advanced Filter Panel -->
        <div class="filter-panel {{ request()->has('fromDate') || request()->has('type') ? 'show' : '' }}" id="filterPanel">
            <div class="filter-grid">
                <div class="filter-group">
                    <label><i class="fas fa-calendar"></i> From Date</label>
                    <input type="date" id="fromDateFilter" value="{{ request('fromDate') }}" max="{{ date('Y-m-d') }}">
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-calendar"></i> To Date</label>
                    <input type="date" id="toDateFilter" value="{{ request('toDate') }}" max="{{ date('Y-m-d') }}">
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-tag"></i> Complaint Type</label>
                    <select id="typeFilter">
                        <option value="">All Types</option>
                        <option value="product_quality" {{ request('type') == 'product_quality' ? 'selected' : '' }}>Product Quality</option>
                        <option value="payment_issue" {{ request('type') == 'payment_issue' ? 'selected' : '' }}>Payment Issue</option>
                        <option value="wrong_location" {{ request('type') == 'wrong_location' ? 'selected' : '' }}>Wrong Location</option>
                        <option value="farmer_contact" {{ request('type') == 'farmer_contact' ? 'selected' : '' }}>Farmer Contact</option>
                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>
            <div class="filter-actions">
                <button class="btn-bulk" id="applyFiltersBtn">
                    <i class="fas fa-check"></i> Apply Filters
                </button>
                <button class="btn-bulk" id="clearFiltersBtn">
                    <i class="fas fa-times"></i> Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Complaints Grid -->
    @if($complaints->isEmpty())
    <div class="empty-state">
        <div class="empty-state-icon">
            <i class="fas fa-flag"></i>
        </div>
        <h3>No Complaints Found</h3>
        <p>There are no complaints matching your criteria. Try adjusting your filters or check back later.</p>
    </div>
    @else
    <div class="complaints-grid">
        @foreach($complaints as $complaint)
        <div class="complaint-card" data-id="{{ $complaint->id }}" data-status="{{ $complaint->status }}">
            <div class="card-header">
                <div class="card-badge">
                    <span class="complaint-id">#{{ str_pad($complaint->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="checkbox-wrapper">
                    <input type="checkbox" class="complaint-checkbox" value="{{ $complaint->id }}">
                </div>
            </div>

            <div class="card-content">
                <!-- User Info -->
                <div class="user-info-row">
                    <div class="user-avatar">
                        {{ substr($complaint->complainant->username ?? 'U', 0, 1) }}
                    </div>
                    <div class="user-details-compact">
                        <div class="user-name-compact">{{ $complaint->complainant->username ?? 'Unknown User' }}</div>
                        <div class="user-meta">
                            <i class="fas fa-circle"></i>
                            <span>{{ $complaint->complainant_role ?? 'User' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Type Badge -->
                <div class="type-badge">
                    <i class="fas fa-tag"></i>
                    <span>{{ str_replace('_', ' ', ucfirst($complaint->complaint_type)) }}</span>
                </div>

                <!-- Description -->
                <div class="complaint-description">
                    "{{ Str::limit($complaint->description, 80) }}"
                </div>

                <!-- Meta Grid -->
                <div class="meta-grid">
                    <div class="meta-item-compact" data-tooltip="Against User">
                        <i class="fas fa-user-slash"></i>
                        <span>{{ $complaint->againstUser->username ?? 'N/A' }}</span>
                    </div>
                    <div class="meta-item-compact" data-tooltip="Order ID">
                        <i class="fas fa-receipt"></i>
                        <span>{{ $complaint->related_order_id ? '#ORD-'.str_pad($complaint->related_order_id, 6, '0', STR_PAD_LEFT) : 'N/A' }}</span>
                    </div>
                    <div class="meta-item-compact" data-tooltip="Created Date">
                        <i class="fas fa-clock"></i>
                        <span>{{ $complaint->created_at->format('d M, Y') }}</span>
                    </div>
                    <div class="meta-item-compact" data-tooltip="Last Updated">
                        <i class="fas fa-history"></i>
                        <span>{{ $complaint->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <!-- Status Dropdown -->
                <div class="status-wrapper">
                    <button class="status-badge-compact {{ $complaint->status }}" data-id="{{ $complaint->id }}">
                        <i class="fas fa-circle"></i>
                        <span>{{ str_replace('_', ' ', ucfirst($complaint->status)) }}</span>
                        <i class="fas fa-chevron-up"></i>
                    </button>
                    <div class="status-dropdown-compact" data-id="{{ $complaint->id }}">
                        <button class="status-option-compact" data-status="new">
                            <i class="fas fa-circle-plus" style="color: #4361ee;"></i>
                            New
                        </button>
                        <button class="status-option-compact" data-status="in_progress">
                            <i class="fas fa-spinner" style="color: #ffb703;"></i>
                            In Progress
                        </button>
                        <button class="status-option-compact" data-status="resolved">
                            <i class="fas fa-check-circle" style="color: #06d6a0;"></i>
                            Resolved
                        </button>
                        <button class="status-option-compact" data-status="rejected">
                            <i class="fas fa-times-circle" style="color: #e63946;"></i>
                            Rejected
                        </button>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="btn-icon" onclick="viewDetails({{ $complaint->id }})" data-tooltip="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    @if($complaint->status != 'resolved')
                    <button class="btn-icon alert" onclick="alertFacilitator({{ $complaint->id }})" data-tooltip="Alert Facilitator">
                        <i class="fas fa-bell"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="pagination-container">
        {{ $complaints->links('vendor.pagination.modern') }}
    </div>
    @endif
</div>
@endsection

@section('scripts')

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
let selectedComplaints = new Set();
let searchTimeout;

document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    initializeSearch();
    initializeStats();
});

function initializeEventListeners() {
    // Toggle filter panel
    document.getElementById('toggleFilterBtn')?.addEventListener('click', function() {
        document.getElementById('filterPanel').classList.toggle('show');
    });

    // Filter chips
    document.querySelectorAll('.filter-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            const filter = this.dataset.filter;
            const value = this.dataset.value;
            
            document.querySelectorAll(`.filter-chip[data-filter="${filter}"]`).forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            
            if (filter === 'status') {
                updateUrlParam('status', value);
            }
        });
    });

    // Status dropdowns
    document.querySelectorAll('.status-badge-compact').forEach(badge => {
        badge.addEventListener('click', function(e) {
            e.stopPropagation();
            const id = this.dataset.id;
            
            // Close all other dropdowns
            document.querySelectorAll('.status-dropdown-compact').forEach(d => {
                if (d.dataset.id !== id) d.classList.remove('show');
            });
            
            // Toggle current dropdown
            const dropdown = document.querySelector(`.status-dropdown-compact[data-id="${id}"]`);
            dropdown.classList.toggle('show');
        });
    });

    // Status options
    document.querySelectorAll('.status-option-compact').forEach(option => {
        option.addEventListener('click', function() {
            const status = this.dataset.status;
            const id = this.closest('.status-dropdown-compact').dataset.id;
            updateStatus(id, status);
            this.closest('.status-dropdown-compact').classList.remove('show');
        });
    });

    // Close dropdowns on click outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.status-wrapper')) {
            document.querySelectorAll('.status-dropdown-compact').forEach(d => d.classList.remove('show'));
        }
        if (!e.target.closest('.bulk-selector')) {
            document.getElementById('bulkStatusDropdown')?.classList.remove('show');
        }
    });

    // Select all functionality
    const selectAll = document.getElementById('selectAllComplaints');
    const checkboxes = document.querySelectorAll('.complaint-checkbox');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
                if (this.checked) {
                    selectedComplaints.add(cb.value);
                } else {
                    selectedComplaints.delete(cb.value);
                }
            });
            updateBulkButtonState();
        });
    }

    // Individual checkboxes
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            if (this.checked) {
                selectedComplaints.add(this.value);
            } else {
                selectedComplaints.delete(this.value);
                if (selectAll) selectAll.checked = false;
            }
            updateBulkButtonState();
        });
    });

    // Bulk actions
    const bulkBtn = document.getElementById('bulkUpdateBtn');
    if (bulkBtn) {
        bulkBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (selectedComplaints.size === 0) {
                showWarning('Please select at least one complaint');
                return;
            }
            document.getElementById('bulkStatusDropdown').classList.toggle('show');
        });
    }

    // Bulk options
    document.querySelectorAll('.bulk-option').forEach(option => {
        option.addEventListener('click', function() {
            const status = this.dataset.status;
            bulkUpdate(status);
            document.getElementById('bulkStatusDropdown').classList.remove('show');
        });
    });

    // Apply filters
    document.getElementById('applyFiltersBtn')?.addEventListener('click', applyFilters);
    document.getElementById('clearFiltersBtn')?.addEventListener('click', clearFilters);
}

function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                updateUrlParam('search', this.value);
            }, 500);
        });
    }
}

function initializeStats() {
    // Animate stats numbers
    document.querySelectorAll('.stat-content h3').forEach(stat => {
        const value = parseInt(stat.innerText);
        animateValue(stat, 0, value, 1000);
    });
}

function animateValue(element, start, end, duration) {
    const range = end - start;
    const increment = range / (duration / 10);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= end) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current);
    }, 10);
}

function updateBulkButtonState() {
    const bulkBtn = document.getElementById('bulkUpdateBtn');
    if (bulkBtn) {
        const count = selectedComplaints.size;
        bulkBtn.innerHTML = `<i class="fas fa-layer-group"></i><span>Bulk Actions ${count > 0 ? `(${count})` : ''}</span><i class="fas fa-chevron-down"></i>`;
    }
}

function updateUrlParam(key, value) {
    const url = new URL(window.location);
    if (value) {
        url.searchParams.set(key, value);
    } else {
        url.searchParams.delete(key);
    }
    window.location.href = url.toString();
}

function validateDates() {
    const from = document.getElementById('fromDateFilter').value;
    const to = document.getElementById('toDateFilter').value;
    
    if (from && to && from > to) {
        showWarning('From date cannot be later than To date');
        return false;
    }
    return true;
}

function applyFilters() {
    if (!validateDates()) return;
    
    const params = new URLSearchParams();
    const status = document.querySelector('.filter-chip.active[data-filter="status"]')?.dataset.value;
    const from = document.getElementById('fromDateFilter').value;
    const to = document.getElementById('toDateFilter').value;
    const type = document.getElementById('typeFilter').value;
    const search = document.getElementById('searchInput').value;
    
    if (status) params.set('status', status);
    if (from) params.set('fromDate', from);
    if (to) params.set('toDate', to);
    if (type) params.set('type', type);
    if (search) params.set('search', search);
    
    window.location.href = `/admin/complaints?${params.toString()}`;
}

function clearFilters() {
    window.location.href = '/admin/complaints';
}

function updateStatus(id, status) {
    const toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });

    fetch(`/admin/complaints/${id}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ status: status })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            updateStatusUI(id, status);
            toast.fire({
                @if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
                title: data.message
            });
            updateStats();
        } else {
            showError(data.message);
        }
    })
    .catch(() => {
        showError('Network error occurred');
    });
}

function bulkUpdate(status) {
    Swal.fire({
        title: `Update ${selectedComplaints.size} Complaints?`,
        html: `<p>Set status to <strong>${status.replace('_', ' ')}</strong></p>`,
        @if(file_exists(public_path('assets/icons/Gif/question2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/question2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
        showCancelButton: true,
        confirmButtonColor: '#4361ee',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, update',
        cancelButtonText: 'Cancel'
    }).then(result => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Updating...',
                html: 'Please wait',
                @if(file_exists(public_path('assets/icons/Gif/loading1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/loading1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/admin/complaints/bulk-update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    complaint_ids: Array.from(selectedComplaints),
                    status: status
                })
            })
            .then(res => res.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    selectedComplaints.forEach(id => updateStatusUI(id, status));
                    selectedComplaints.clear();
                    document.querySelectorAll('.complaint-checkbox').forEach(cb => cb.checked = false);
                    document.getElementById('selectAllComplaints').checked = false;
                    updateBulkButtonState();
                    
                    Swal.fire({
                        @if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
                        title: 'Success!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    updateStats();
                } else {
                    showError(data.message);
                }
            })
            .catch(() => {
                Swal.close();
                showError('Network error occurred');
            });
        }
    });
}

function updateStatusUI(id, status) {
    const card = document.querySelector(`.complaint-card[data-id="${id}"]`);
    if (card) {
        card.dataset.status = status;
        
        const badge = document.querySelector(`.status-badge-compact[data-id="${id}"]`);
        if (badge) {
            badge.className = `status-badge-compact ${status}`;
            badge.innerHTML = `<i class="fas fa-circle"></i><span>${status.replace('_', ' ')}</span><i class="fas fa-chevron-up"></i>`;
        }
    }
}

function updateStats() {
    fetch('/admin/complaints/stats')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const stats = data.stats;
                document.querySelector('.stat-card:nth-child(1) h3').textContent = stats.new;
                document.querySelector('.stat-card:nth-child(2) h3').textContent = stats.in_progress;
                document.querySelector('.stat-card:nth-child(3) h3').textContent = stats.resolved;
                document.querySelector('.stat-card:nth-child(4) h3').textContent = stats.rejected;
            }
        })
        .catch(console.error);
}

function viewDetails(id) {
    Swal.fire({
        title: 'Loading...',
        @if(file_exists(public_path('assets/icons/Gif/loading1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/loading1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(`/admin/complaints/${id}/details`)
        .then(res => res.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                showDetailsModal(data.complaint);
            } else {
                showError('Failed to load details');
            }
        })
        .catch(() => {
            Swal.close();
            showError('Network error occurred');
        });
}

function showDetailsModal(complaint) {
    const date = new Date(complaint.created_at).toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });

    Swal.fire({
        title: 'Complaint Details',
        html: `
            <div class="detail-modal">
                <div class="detail-section">
                    <div class="detail-title">
                        <i class="fas fa-info-circle"></i>
                        Basic Information
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">ID:</span>
                        <span class="detail-value">#${complaint.id.toString().padStart(6, '0')}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value">
                            <span class="status-badge-compact ${complaint.status}" style="display: inline-flex; padding: 4px 12px;">
                                ${complaint.status.replace('_', ' ')}
                            </span>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Type:</span>
                        <span class="detail-value">${complaint.complaint_type.replace('_', ' ')}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Created:</span>
                        <span class="detail-value">${date}</span>
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-title">
                        <i class="fas fa-users"></i>
                        Involved Parties
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">From:</span>
                        <span class="detail-value">${complaint.complainant?.username || 'Unknown'} (${complaint.complainant_role || 'User'})</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Against:</span>
                        <span class="detail-value">${complaint.against_user?.username || 'N/A'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Order:</span>
                        <span class="detail-value">${complaint.related_order_id ? '#ORD-'+complaint.related_order_id.toString().padStart(6,'0') : 'N/A'}</span>
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-title">
                        <i class="fas fa-align-left"></i>
                        Description
                    </div>
                    <div class="detail-description">
                        ${complaint.description}
                    </div>
                </div>
                
                ${complaint.admin_notes ? `
                <div class="detail-section">
                    <div class="detail-title">
                        <i class="fas fa-sticky-note"></i>
                        Admin Notes
                    </div>
                    <div class="detail-description" style="border-left-color: #ffb703;">
                        ${complaint.admin_notes}
                    </div>
                </div>
                ` : ''}
                
                <div class="detail-section">
                    <div class="detail-title">
                        <i class="fas fa-clock"></i>
                        Timeline
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Created:</span>
                        <span class="detail-value">${new Date(complaint.created_at).toLocaleString()}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Updated:</span>
                        <span class="detail-value">${new Date(complaint.updated_at).toLocaleString()}</span>
                    </div>
                </div>
            </div>
        `,
        @if(file_exists(public_path('assets/icons/Gif/Complaint Details1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Complaint Details1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
        width: 600,
        showConfirmButton: false,
        showCloseButton: true,
        showCancelButton: true,
        cancelButtonText: 'Close',
        showDenyButton: true,
        denyButtonText: 'Take Action',
        denyButtonColor: '#4361ee',
        didOpen: () => {
            // Add custom styles for modal
            const style = document.createElement('style');
            style.textContent = `
                .detail-modal {
                    text-align: left;
                    max-height: 70vh;
                    overflow-y: auto;
                    padding-right: 10px;
                }
                .detail-modal::-webkit-scrollbar {
                    width: 6px;
                }
                .detail-modal::-webkit-scrollbar-track {
                    background: #f1f5f9;
                    border-radius: 10px;
                }
                .detail-modal::-webkit-scrollbar-thumb {
                    background: #4361ee;
                    border-radius: 10px;
                }
            `;
            document.head.appendChild(style);
        }
    }).then((result) => {
        if (result.isDenied) {
            // Open quick action menu
            showQuickActions(complaint);
        }
    });
}

function showQuickActions(complaint) {
    Swal.fire({
        title: 'Quick Actions',
        html: `
            <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px;">
                <button class="quick-action-btn" onclick="updateStatus(${complaint.id}, 'in_progress')">
                    <i class="fas fa-spinner" style="color: #ffb703;"></i>
                    Mark In Progress
                </button>
                <button class="quick-action-btn" onclick="updateStatus(${complaint.id}, 'resolved')">
                    <i class="fas fa-check-circle" style="color: #06d6a0;"></i>
                    Mark Resolved
                </button>
                <button class="quick-action-btn" onclick="updateStatus(${complaint.id}, 'rejected')">
                    <i class="fas fa-times-circle" style="color: #e63946;"></i>
                    Mark Rejected
                </button>
                <button class="quick-action-btn" onclick="alertFacilitator(${complaint.id})">
                    <i class="fas fa-bell" style="color: #4361ee;"></i>
                    Alert Facilitator
                </button>
            </div>
            <style>
                .quick-action-btn {
                    width: 100%;
                    padding: 12px;
                    border: 1px solid var(--border);
                    border-radius: 8px;
                    background: white;
                    cursor: pointer;
                    font-size: 14px;
                    font-weight: 500;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    transition: all 0.2s;
                }
                .quick-action-btn:hover {
                    background: #f8fafc;
                    transform: translateX(5px);
                    border-color: #4361ee;
                }
            </style>
        `,
        @if(file_exists(public_path('assets/icons/Gif/Quick Action1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Quick Action1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
        showConfirmButton: false,
        showCloseButton: true,
        showCancelButton: true,
        cancelButtonText: 'Close'
    });
}

function alertFacilitator(id) {
    // Get facilitators list from data attribute or fetch it
    const facilitators = @json($facilitatorsList ?? []);
    
    if (!facilitators || facilitators.length === 0) {
        showWarning('No facilitators available');
        return;
    }
    
    // Create options object for select
    let options = {};
    facilitators.forEach(f => {
        options[f.user_id] = `${f.name} ${f.assigned_division ? `(${f.assigned_division})` : ''}`;
    });

    Swal.fire({
        title: 'Alert Facilitator',
        html: `
            <div style="margin: 20px 0;">
                <p style="margin-bottom: 10px; color: #64748b;">Select a facilitator to notify:</p>
                <select id="facilitatorSelect" class="swal2-select" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <option value="">Choose facilitator...</option>
                    ${Object.entries(options).map(([id, name]) => `<option value="${id}">${name}</option>`).join('')}
                </select>
            </div>
        `,
        @if(file_exists(public_path('assets/icons/Gif/Notification Aleart1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Notification Aleart1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
        showCancelButton: true,
        confirmButtonText: 'Send Alert',
        confirmButtonColor: '#4361ee',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const facilitatorId = document.getElementById('facilitatorSelect').value;
            
            if (!facilitatorId) {
                Swal.showValidationMessage('Please select a facilitator');
                return false;
            }
            
            return fetch('/admin/complaints/alert', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    id: id,
                    facilitator_id: facilitatorId,
                    send_notification: true
                })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message);
                }
                return data;
            })
            .catch(error => {
                Swal.showValidationMessage(error.message);
            });
        }
    }).then(result => {
        if (result.isConfirmed && result.value.success) {
            Swal.fire({
                @if(file_exists(public_path('assets/icons/Gif/Send successfully1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Send successfully1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
                title: 'Alert Sent!',
                text: result.value.message,
                timer: 1500,
                showConfirmButton: false
            });
            
            // Log the action
            console.log(`Alert sent for complaint #${id} to facilitator`);
        }
    });
}

// Notification functions
function showSuccess(message) {
    Swal.fire({
        @if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
        title: 'Success',
        text: message,
        timer: 2000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

function showError(message) {
    Swal.fire({
        @if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
        title: 'Error',
        text: message,
        confirmButtonColor: '#4361ee',
        confirmButtonText: 'OK'
    });
}

function showWarning(message) {
    Swal.fire({
        @if(file_exists(public_path('assets/icons/Gif/alert1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/alert1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'warning' @endif,
        title: 'Warning',
        text: message,
        confirmButtonColor: '#4361ee',
        confirmButtonText: 'Got it'
    });
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + F to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        document.getElementById('searchInput')?.focus();
    }
    
    // Esc to clear selections
    if (e.key === 'Escape') {
        selectedComplaints.clear();
        document.querySelectorAll('.complaint-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('selectAllComplaints').checked = false;
        updateBulkButtonState();
    }
    
    // Ctrl/Cmd + A to select all (when not in input)
    if ((e.ctrlKey || e.metaKey) && e.key === 'a' && !e.target.matches('input, textarea')) {
        e.preventDefault();
        if (document.querySelectorAll('.complaint-checkbox').length > 0) {
            const selectAll = document.getElementById('selectAllComplaints');
            if (selectAll) {
                selectAll.click();
            }
        }
    }
});

// Auto-refresh functionality (optional)
let autoRefreshInterval;

function startAutoRefresh(interval = 30000) {
    stopAutoRefresh();
    autoRefreshInterval = setInterval(() => {
        // Only refresh if page is visible and no modals are open
        if (!document.hidden && !Swal.isVisible()) {
            refreshData();
        }
    }, interval);
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
}

function refreshData() {
    // Show subtle loading indicator
    const refreshIndicator = document.createElement('div');
    refreshIndicator.className = 'refresh-indicator';
    refreshIndicator.innerHTML = '<i class="fas fa-sync-alt fa-spin"></i> Updating...';
    document.body.appendChild(refreshIndicator);
    
    fetch(window.location.href)
        .then(res => res.text())
        .then(html => {
            // Parse and update only the complaints grid
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newGrid = doc.querySelector('.complaints-grid');
            const newStats = doc.querySelector('.stats-grid');
            
            if (newGrid) {
                document.querySelector('.complaints-grid').innerHTML = newGrid.innerHTML;
            }
            if (newStats) {
                document.querySelector('.stats-grid').innerHTML = newStats.innerHTML;
            }
            
            // Reinitialize event listeners
            initializeEventListeners();
            
            setTimeout(() => {
                refreshIndicator.remove();
            }, 1000);
        })
        .catch(() => {
            refreshIndicator.remove();
        });
}


window.addEventListener('beforeunload', function() {
    stopAutoRefresh();
});

</script>
@endsection
