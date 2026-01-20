@extends('facilitator.layouts.facilitator_master')

@section('title', 'Standards / Unit of Measures')
@section('page-title', 'Standards / Unit of Measures')

@section('styles')
<style>
    .standards-container {
        background: #ffffff;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(15,23,36,0.08);
        margin-bottom: 25px;
    }

    .standard-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        border-left: 5px solid #4e73df;
        transition: all 0.3s ease;
        margin-bottom: 15px;
    }

    .standard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(78,115,223,0.15);
    }

    .standard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .standard-name {
        font-weight: 600;
        color: #2d3748;
        font-size: 1.1rem;
    }

    .standard-status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .status-active {
        background: #d1fae5;
        color: #065f46;
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .standard-description {
        color: #6b7280;
        margin-bottom: 15px;
        font-size: 0.95rem;
    }

    .standard-meta {
        display: flex;
        gap: 15px;
        font-size: 0.85rem;
        color: #9ca3af;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .btn-sm {
        padding: 5px 15px;
        font-size: 0.85rem;
    }

    .section-title {
        font-weight: 600;
        color: #374151;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e5e7eb;
    }

    .add-new-btn {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .add-new-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(78,115,223,0.3);
    }

    .empty-state {
        text-align: center;
        padding: 50px 20px;
        color: #6b7280;
    }

    .empty-state i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 15px;
    }

    .empty-state h4 {
        color: #9ca3af;
        margin-bottom: 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-0">
                        <i class="fa-solid fa-ruler-combined text-primary me-2"></i>
                        Standards / Unit of Measures
                    </h2>
                    <p class="text-muted mb-0">Manage system standards and measurement units</p>
                </div>
                <button class="btn btn-primary add-new-btn" data-bs-toggle="modal" data-bs-target="#addStandardModal">
                    <i class="fa-solid fa-plus me-2"></i>Add New Standard
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="standards-container">
                <h4 class="section-title">
                    <i class="fa-solid fa-check-circle text-success me-2"></i>
                    Active Unit of Measures
                </h4>

                @if($units->count() > 0)
                    <div class="row">
                        @foreach($units as $unit)
                        <div class="col-md-6 col-lg-4">
                            <div class="standard-card">
                                <div class="standard-header">
                                    <div class="standard-name">{{ $unit->standard_value }}</div>
                                    <span class="standard-status status-active">
                                        <i class="fa-solid fa-circle-check me-1"></i>Active
                                    </span>
                                </div>
                                @if($unit->description)
                                <div class="standard-description">
                                    {{ $unit->description }}
                                </div>
                                @endif
                                <div class="standard-meta">
                                    <span>
                                        <i class="fa-solid fa-sort-numeric-up me-1"></i>
                                        Order: {{ $unit->display_order }}
                                    </span>
                                    <span>
                                        <i class="fa-solid fa-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($unit->created_at)->format('M d, Y') }}
                                    </span>
                                </div>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline-primary edit-btn"
                                            data-id="{{ $unit->id }}"
                                            data-value="{{ $unit->standard_value }}"
                                            data-description="{{ $unit->description }}"
                                            data-order="{{ $unit->display_order }}">
                                        <i class="fa-solid fa-edit me-1"></i>Edit
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fa-solid fa-ruler-combined"></i>
                        <h4>No active unit of measures found</h4>
                        <p class="text-muted">Add your first unit of measure to get started</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Standard Modal -->
<div class="modal fade" id="addStandardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa-solid fa-plus-circle me-2"></i>
                    Add New Unit of Measure
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addStandardForm" action="{{ route('facilitator.unit-of-measures.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Standard Value <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="standard_value" required
                               placeholder="e.g., Kilogram, Liter, Piece">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"
                                  placeholder="Optional description for this unit"></textarea>
                    </div>
                    <input type="hidden" name="standard_type" value="unit_of_measure">
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Display order will be assigned automatically based on the next available position.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Standard</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Standard Modal -->
<div class="modal fade" id="editStandardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa-solid fa-edit me-2"></i>
                    Edit Unit of Measure
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStandardForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">Standard Value <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="standard_value" id="edit_standard_value" required
                               placeholder="e.g., Kilogram, Liter, Piece">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"
                                  placeholder="Optional description for this unit"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" class="form-control" name="display_order" id="edit_display_order" min="1">
                        <small class="text-muted">Enter position number (1 for first position)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Standard</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Handle edit button click
        $(document).on('click', '.edit-btn', function() {
            const id = $(this).data('id');
            const value = $(this).data('value');
            const description = $(this).data('description');
            const order = $(this).data('order');

            $('#edit_id').val(id);
            $('#edit_standard_value').val(value);
            $('#edit_description').val(description || '');
            $('#edit_display_order').val(order);

            // Set form action
            $('#editStandardForm').attr('action', `/facilitator/unit-of-measures/${id}/update`);

            $('#editStandardModal').modal('show');
        });

        // Handle form submission for adding new standard
        $('#addStandardForm').submit(function(e) {
            e.preventDefault();
            const formData = $(this).serialize();
            const submitBtn = $(this).find('button[type="submit"]');

            // Disable submit button and show loading
            submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i>Adding...');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Something went wrong!';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        title: 'Error!',
                        html: errorMessage,
                        icon: 'error'
                    });
                },
                complete: function() {
                    // Re-enable submit button
                    submitBtn.prop('disabled', false).html('Add Standard');
                }
            });
        });

        // Handle form submission for editing standard
        $('#editStandardForm').submit(function(e) {
            e.preventDefault();
            const formData = $(this).serialize();
            const submitBtn = $(this).find('button[type="submit"]');

            // Disable submit button and show loading
            submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i>Updating...');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                headers: {
                    'X-HTTP-Method-Override': 'PUT'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $('#editStandardModal').modal('hide');
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Something went wrong!';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        title: 'Error!',
                        html: errorMessage,
                        icon: 'error'
                    });
                },
                complete: function() {
                    // Re-enable submit button
                    submitBtn.prop('disabled', false).html('Update Standard');
                }
            });
        });

        // Clear form when modal is hidden
        $('#addStandardModal, #editStandardModal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
        });
    });
</script>
@endsection
