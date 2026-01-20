@extends('buyer.layouts.buyer_master')

@section('title', 'My Complaints')
@section('page-title', 'My Complaints')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/buyer/complaints-list.css') }}">
@endsection

@section('content')
<div class="complaints-container">
    <div class="complaints-header">
        <h1>
            <i class="fa-solid fa-file-contract"></i>
            My Complaints
        </h1>
        <p>View and manage all your filed complaints</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-content">
                <div class="stat-info">
                    <h3>{{ $totalComplaints }}</h3>
                    <p>Total Complaints</p>
                </div>
                <div class="stat-icon">
                    <i class="fa-solid fa-list-check"></i>
                </div>
            </div>
        </div>

        <div class="stat-card open">
            <div class="stat-content">
                <div class="stat-info">
                    <h3>{{ $openComplaints }}</h3>
                    <p>Open Complaints</p>
                </div>
                <div class="stat-icon">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
        </div>

        <div class="stat-card resolved">
            <div class="stat-content">
                <div class="stat-info">
                    <h3>{{ $resolvedComplaints }}</h3>
                    <p>Resolved Complaints</p>
                </div>
                <div class="stat-icon">
                    <i class="fa-solid fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="stat-card pending">
            <div class="stat-content">
                <div class="stat-info">
                    <h3>{{ $inProgressComplaints }}</h3>
                    <p>In Progress</p>
                </div>
                <div class="stat-icon">
                    <i class="fa-solid fa-spinner"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="complaints-table-container">
        <div class="table-header">
            <h3>
                <i class="fa-solid fa-table-list"></i>
                Complaints List
            </h3>
            <div class="table-actions">
                <a href="{{ route('buyer.dashboard') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back to Dashboard
                </a>
                <a href="{{ route('buyer.complaints.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    File New Complaint
                </a>
            </div>
        </div>

        @if($complaints->count() > 0)
        <div class="table-responsive">
            <table class="complaints-table">
                <thead>
                    <tr>
                        <th><i class="fa-solid fa-hashtag"></i> ID</th>
                        <th><i class="fa-solid fa-tag"></i> Type</th>
                        <th><i class="fa-solid fa-receipt"></i> Order</th>
                        <th><i class="fa-solid fa-calendar"></i> Date</th>
                        <th><i class="fa-solid fa-circle-info"></i> Status</th>
                        <th><i class="fa-solid fa-gears"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($complaints as $index => $complaint)
                        <tr style="animation-delay: {{ $index * 0.1 }}s" class="complaint-row">
                            <td data-label="ID">
                                <span class="complaint-id">#{{ $complaint->id }}</span>
                            </td>
                            <td data-label="Type">
                                <span class="complaint-type {{ str_replace('_', '-', $complaint->complaint_type) }}">
                                    <i class="fa-solid fa-{{ getComplaintIcon($complaint->complaint_type) }}"></i>
                                    {{ formatComplaintType($complaint->complaint_type) }}
                                </span>
                            </td>
                            <td data-label="Order">
                                @if($complaint->related_order_id)
                                <span class="order-info">
                                    <i class="fa-solid fa-receipt"></i>
                                    @if($complaint->order_number)
                                        Order #{{ $complaint->order_number }}
                                    @else
                                        Order #{{ $complaint->related_order_id }}
                                    @endif
                                </span>
                                @else
                                <span class="no-order">Not specified</span>
                                @endif
                            </td>
                            <td data-label="Date">
                                <div class="date-cell">
                                    @php
                                        $createdAt = \Carbon\Carbon::parse($complaint->created_at);
                                    @endphp
                                    <span class="date">{{ $createdAt->format('M d, Y') }}</span>
                                    <span class="time">{{ $createdAt->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td data-label="Status">
                                <span class="status-badge status-{{ $complaint->status }}">
                                    <i class="fa-solid fa-{{ getStatusIcon($complaint->status) }}"></i>
                                    {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                </span>
                            </td>
                            <td data-label="Actions">
                                <div class="action-buttons">
                                    <button class="action-btn view" onclick="viewComplaint({{ $complaint->id }})" title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    @if($complaint->status == 'new')
                                    <button class="action-btn edit" onclick="editComplaint({{ $complaint->id }})" title="Edit Complaint">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    @endif
                                    @if($complaint->status == 'new')
                                    <button class="action-btn delete" onclick="deleteComplaint({{ $complaint->id }})" title="Delete Complaint">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            <div class="pagination-info">
                Showing {{ $complaints->firstItem() }} to {{ $complaints->lastItem() }} of {{ $complaints->total() }} complaints
            </div>
            <div class="pagination">
                @if($complaints->onFirstPage())
                <a href="#" class="page-link disabled">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
                @else
                <a href="{{ $complaints->previousPageUrl() }}" class="page-link">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
                @endif

                @foreach(range(1, $complaints->lastPage()) as $page)
                @if($page == $complaints->currentPage())
                <a href="#" class="page-link active">{{ $page }}</a>
                @else
                <a href="{{ $complaints->url($page) }}" class="page-link">{{ $page }}</a>
                @endif
                @endforeach

                @if($complaints->hasMorePages())
                <a href="{{ $complaints->nextPageUrl() }}" class="page-link">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
                @else
                <a href="#" class="page-link disabled">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
                @endif
            </div>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fa-solid fa-file-circle-question"></i>
            </div>
            <h3>No Complaints Found</h3>
            <p>You haven't filed any complaints yet. File your first complaint to get started.</p>
            <a href="{{ route('buyer.complaints.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i>
                File Your First Complaint
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@php
function getComplaintIcon($type) {
    $icons = [
        'product_quality' => 'star-half-stroke',
        'wrong_location' => 'map-location-dot',
        'farmer_contact' => 'phone-slash',
        'availability_issue' => 'box-open',
        'payment_issue' => 'credit-card',
        'invoice_error' => 'file-invoice',
        'category_misclassification' => 'tags',
        'farmer_no_show' => 'user-slash',
        'product_photo_mismatch' => 'images',
        'request_ignored' => 'bell-slash',
        'filter_issue' => 'filter',
        'vague_instructions' => 'map',
        'payment_technical' => 'bug',
        'other' => 'ellipsis-h'
    ];
    return $icons[$type] ?? 'exclamation-circle';
}

function formatComplaintType($type) {
    $types = [
        'product_quality' => 'Product Quality',
        'wrong_location' => 'Wrong Location',
        'farmer_contact' => 'Farmer Contact',
        'availability_issue' => 'Availability Issue',
        'payment_issue' => 'Payment Issue',
        'invoice_error' => 'Invoice Error',
        'category_misclassification' => 'Wrong Category',
        'farmer_no_show' => 'Farmer No Show',
        'product_photo_mismatch' => 'Photo Mismatch',
        'request_ignored' => 'Request Ignored',
        'filter_issue' => 'Filter Issue',
        'vague_instructions' => 'Vague Instructions',
        'payment_technical' => 'Technical Glitch',
        'other' => 'Other Issue'
    ];
    return $types[$type] ?? ucfirst(str_replace('_', ' ', $type));
}

function getStatusIcon($status) {
    $icons = [
        'new' => 'circle-plus',
        'in_progress' => 'spinner',
        'resolved' => 'check-circle',
        'rejected' => 'times-circle'
    ];
    return $icons[$status] ?? 'circle-info';
}
@endphp

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function viewComplaint(complaintId) {
    Swal.fire({
        title: 'Loading Complaint Details',
        text: 'Please wait...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '{{ url("buyer/complaints/view") }}/' + complaintId,
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            Swal.close();
            if (response.success) {
                const complaint = response.complaint;
                const statusIcons = {
                    'new': 'fa-circle-plus',
                    'in_progress': 'fa-spinner fa-spin',
                    'resolved': 'fa-check-circle',
                    'rejected': 'fa-times-circle'
                };

                const complaintTypeIcons = {
                    'product_quality': 'fa-star-half-stroke',
                    'wrong_location': 'fa-map-location-dot',
                    'farmer_contact': 'fa-phone-slash',
                    'availability_issue': 'fa-box-open',
                    'payment_issue': 'fa-credit-card',
                    'invoice_error': 'fa-file-invoice',
                    'category_misclassification': 'fa-tags',
                    'farmer_no_show': 'fa-user-slash',
                    'product_photo_mismatch': 'fa-images',
                    'request_ignored': 'fa-bell-slash',
                    'filter_issue': 'fa-filter',
                    'vague_instructions': 'fa-map',
                    'payment_technical': 'fa-bug',
                    'other': 'fa-ellipsis-h'
                };

                let complaintTypeText = '';
                switch(complaint.complaint_type) {
                    case 'product_quality': complaintTypeText = 'Product quality does not match the description'; break;
                    case 'wrong_location': complaintTypeText = 'Pickup location information is inaccurate'; break;
                    case 'farmer_contact': complaintTypeText = 'Farmer contact details are incorrect'; break;
                    case 'availability_issue': complaintTypeText = 'Product availability issues'; break;
                    case 'payment_issue': complaintTypeText = 'Payment processed but order not confirmed'; break;
                    case 'invoice_error': complaintTypeText = 'Missing or incorrect invoice details'; break;
                    case 'category_misclassification': complaintTypeText = 'Product category misclassification'; break;
                    case 'farmer_no_show': complaintTypeText = 'Order pick-up failed due to farmer no-show'; break;
                    case 'product_photo_mismatch': complaintTypeText = 'Significant difference between product photo and actual item'; break;
                    case 'request_ignored': complaintTypeText = 'Buyer\'s product request feature is ignored'; break;
                    case 'filter_issue': complaintTypeText = 'Inability to filter/search for specific quality grades'; break;
                    case 'vague_instructions': complaintTypeText = 'Difficulty arranging transport due to vague pickup instructions'; break;
                    case 'payment_technical': complaintTypeText = 'Recurring technical glitches during payment'; break;
                    default: complaintTypeText = 'Other issue';
                }

                Swal.fire({
                    title: 'Complaint Details',
                    html: `
                        <div class="complaint-details-modal">
                            <div class="complaint-header-section">
                                <div class="complaint-title">
                                    <h3><i class="fa-solid fa-hashtag"></i> Complaint #${complaint.id}</h3>
                                    <span class="status-badge status-${complaint.status}">
                                        <i class="fa-solid ${statusIcons[complaint.status] || 'fa-circle-info'}"></i>
                                        ${complaint.status.replace('_', ' ')}
                                    </span>
                                </div>
                                <p class="complaint-date"><i class="fa-solid fa-calendar"></i> Filed on ${complaint.created_at_formatted}</p>
                            </div>

                            <div class="complaint-info-grid">
                                <div class="info-card">
                                    <div class="info-label"><i class="fa-solid fa-tag"></i> Type</div>
                                    <div class="info-value">
                                        <i class="fa-solid ${complaintTypeIcons[complaint.complaint_type] || 'fa-tag'}"></i>
                                        ${complaintTypeText}
                                    </div>
                                </div>

                                <div class="info-card">
                                    <div class="info-label"><i class="fa-solid fa-calendar"></i> Last Updated</div>
                                    <div class="info-value">
                                        <i class="fa-solid fa-clock"></i>
                                        ${complaint.updated_at_formatted}
                                    </div>
                                </div>
                            </div>

                            <div class="description-section">
                                <h4><i class="fa-solid fa-message"></i> Description</h4>
                                <div class="description-content">
                                    ${complaint.description}
                                </div>
                            </div>

                            ${complaint.resolved_by ? `
                            <div class="resolution-section">
                                <h4><i class="fa-solid fa-user-check"></i> Resolution Details</h4>
                                <div class="resolution-content">
                                    <div class="resolved-by">
                                        <strong>Resolved by:</strong> ${complaint.resolved_by}
                                    </div>
                                    <div class="resolution-date">
                                        <strong>Resolved on:</strong> ${complaint.updated_at_formatted}
                                    </div>
                                </div>
                            </div>
                            ` : ''}
                        </div>
                    `,
                    width: 700,
                    showCloseButton: true,
                    showConfirmButton: true,
                    confirmButtonText: 'Close',
                    confirmButtonColor: '#10B981',
                    customClass: {
                        popup: 'animate__animated animate__fadeInUp'
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to load complaint details',
                    confirmButtonColor: '#10B981'
                });
            }
        },
        error: function(xhr) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load complaint details. Please try again.',
                confirmButtonColor: '#10B981'
            });
        }
    });
}

function editComplaint(complaintId) {
    Swal.fire({
        title: 'Loading Complaint Details',
        text: 'Please wait...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '{{ url("buyer/complaints/view") }}/' + complaintId,
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            Swal.close();
            if (response.success) {
                const complaint = response.complaint;

                if (complaint.status !== 'new') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cannot Edit',
                        text: 'Only new complaints can be edited. This complaint status is already ' + complaint.status.replace('_', ' ') + '.',
                        confirmButtonColor: '#10B981'
                    });
                    return;
                }

                const complaintTypeOptions = [
                    {value: 'product_quality', text: 'Product Quality'},
                    {value: 'wrong_location', text: 'Wrong Location'},
                    {value: 'farmer_contact', text: 'Farmer Contact'},
                    {value: 'availability_issue', text: 'Availability Issue'},
                    {value: 'payment_issue', text: 'Payment Issue'},
                    {value: 'invoice_error', text: 'Invoice Error'},
                    {value: 'category_misclassification', text: 'Category Misclassification'},
                    {value: 'farmer_no_show', text: 'Farmer No Show'},
                    {value: 'product_photo_mismatch', text: 'Product Photo Mismatch'},
                    {value: 'request_ignored', text: 'Request Ignored'},
                    {value: 'filter_issue', text: 'Filter Issue'},
                    {value: 'vague_instructions', text: 'Vague Instructions'},
                    {value: 'payment_technical', text: 'Payment Technical'},
                    {value: 'other', text: 'Other'}
                ];

                let optionsHtml = '';
                complaintTypeOptions.forEach(option => {
                    const selected = option.value === complaint.complaint_type ? 'selected' : '';
                    optionsHtml += `<option value="${option.value}" ${selected}>${option.text}</option>`;
                });

                Swal.fire({
                    title: 'Edit Complaint',
                    html: `
                        <form id="editComplaintForm">
                            <div class="form-group">
                                <label for="edit_complaint_type" class="form-label">
                                    <i class="fa-solid fa-tag"></i> Complaint Type
                                </label>
                                <select id="edit_complaint_type" class="form-control" required>
                                    ${optionsHtml}
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_description" class="form-label">
                                    <i class="fa-solid fa-message"></i> Description
                                </label>
                                <textarea id="edit_description" class="form-control" rows="4" required>${complaint.description}</textarea>
                            </div>
                        </form>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Update Complaint',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6b7280',
                    preConfirm: () => {
                        const complaintType = document.getElementById('edit_complaint_type').value;
                        const description = document.getElementById('edit_description').value;

                        if (!complaintType || !description) {
                            Swal.showValidationMessage('Please fill in all fields');
                            return false;
                        }

                        if (description.length < 20) {
                            Swal.showValidationMessage('Description must be at least 20 characters');
                            return false;
                        }

                        if (description.length > 2000) {
                            Swal.showValidationMessage('Description must be less than 2000 characters');
                            return false;
                        }

                        return {
                            complaint_type: complaintType,
                            description: description
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateComplaint(complaintId, result.value);
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to load complaint details',
                    confirmButtonColor: '#10B981'
                });
            }
        },
        error: function(xhr) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load complaint details. Please try again.',
                confirmButtonColor: '#10B981'
            });
        }
    });
}

function updateComplaint(complaintId, data) {
    Swal.fire({
        title: 'Updating Complaint',
        text: 'Please wait...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '{{ url("buyer/complaints/update") }}/' + complaintId,
        type: 'PUT',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            Swal.close();
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message || 'Complaint updated successfully!',
                    confirmButtonColor: '#10B981',
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to update complaint',
                    confirmButtonColor: '#10B981'
                });
            }
        },
        error: function(xhr) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: xhr.responseJSON?.message || 'Failed to update complaint. Please try again.',
                confirmButtonColor: '#10B981'
            });
        }
    });
}

function deleteComplaint(complaintId) {
    Swal.fire({
        title: 'Delete Complaint',
        text: 'Are you sure you want to delete this complaint? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                url: '{{ url("buyer/complaints/delete") }}/' + complaintId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).then(response => {
                if (!response.success) {
                    throw new Error(response.message || 'Failed to delete complaint');
                }
                return response;
            }).catch(error => {
                Swal.showValidationMessage(
                    error.responseJSON?.message || error.statusText || 'Failed to delete complaint'
                );
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Deleted!',
                text: 'Complaint has been deleted successfully.',
                icon: 'success',
                confirmButtonColor: '#10B981',
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                location.reload();
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.2}s`;
        card.classList.add('animate__animated', 'animate__fadeInUp');
    });

    const complaintRows = document.querySelectorAll('.complaint-row');
    complaintRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 7px 15px rgba(15,23,36,0.08)';
        });

        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });
});
</script>
@endsection
