@extends('facilitator.layouts.facilitator_master')

@section('title', 'Quality Grades')
@section('page-title', 'Quality Grades')

@section('styles')
<style>
    .grade-container {
        background: #ffffff;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(15,23,36,0.08);
        margin-bottom: 25px;
    }

    .grade-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        border-left: 5px solid #10b981;
        transition: all 0.3s ease;
        margin-bottom: 15px;
    }

    .grade-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(16,185,129,0.15);
    }

    .grade-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .grade-name {
        font-weight: 600;
        color: #2d3748;
        font-size: 1.1rem;
    }

    .grade-status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .status-active {
        background: #d1fae5;
        color: #065f46;
    }

    .grade-description {
        color: #6b7280;
        margin-bottom: 15px;
        font-size: 0.95rem;
    }

    .grade-meta {
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
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .add-new-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(16,185,129,0.3);
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

    .grade-level {
        display: inline-block;
        padding: 4px 12px;
        background: #e0f2fe;
        color: #0369a1;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 10px;
    }

    .modal-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border-radius: 0;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
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
                        <i class="fa-solid fa-medal text-success me-2"></i>
                        Quality Grades
                    </h2>
                    <p class="text-muted mb-0">Manage product quality grades and standards</p>
                </div>
                <button class="btn btn-success add-new-btn" data-bs-toggle="modal" data-bs-target="#addGradeModal">
                    <i class="fa-solid fa-plus me-2"></i>Add New Grade
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="grade-container">
                <h4 class="section-title">
                    <i class="fa-solid fa-check-circle text-success me-2"></i>
                    Active Quality Grades
                </h4>

                @if($grades->count() > 0)
                    <div class="row">
                        @foreach($grades as $grade)
                        <div class="col-md-6 col-lg-4">
                            <div class="grade-card">
                                <div class="grade-header">
                                    <div class="grade-name">{{ $grade->standard_value }}</div>
                                    <span class="grade-status status-active">
                                        <i class="fa-solid fa-circle-check me-1"></i>Active
                                    </span>
                                </div>
                                @if($grade->description)
                                <div class="grade-description">
                                    {{ $grade->description }}
                                </div>
                                @endif
                                <div class="grade-meta">
                                    <span>
                                        <i class="fa-solid fa-sort-numeric-up me-1"></i>
                                        Order: {{ $grade->display_order }}
                                    </span>
                                    <span>
                                        <i class="fa-solid fa-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($grade->created_at)->format('M d, Y') }}
                                    </span>
                                </div>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline-primary edit-grade-btn"
                                            data-id="{{ $grade->id }}"
                                            data-name="{{ $grade->standard_value }}"
                                            data-description="{{ $grade->description }}">
                                        <i class="fa-solid fa-edit me-1"></i>Edit
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fa-solid fa-medal"></i>
                        <h4>No active quality grades found</h4>
                        <p class="text-muted">Add your first quality grade to get started</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Grade Modal -->
<div class="modal fade" id="addGradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa-solid fa-plus-circle me-2"></i>
                    Add New Quality Grade
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addGradeForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Grade Name *</label>
                        <input type="text" class="form-control" name="standard_value" required
                               placeholder="e.g., Premium, Grade A, Standard">
                        <div class="invalid-feedback" id="standard_value_error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"
                                  placeholder="Describe this quality grade (optional)"></textarea>
                    </div>
                    <input type="hidden" name="standard_type" value="quality_grade">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Add Grade
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Grade Modal -->
<div class="modal fade" id="editGradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa-solid fa-edit me-2"></i>
                    Edit Quality Grade
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editGradeForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_grade_id" name="id">
                    <div class="mb-3">
                        <label class="form-label">Grade Name *</label>
                        <input type="text" class="form-control" id="edit_grade_name" name="standard_value" required
                               placeholder="e.g., Premium, Grade A, Standard">
                        <div class="invalid-feedback" id="edit_standard_value_error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="edit_grade_description" name="description" rows="3"
                                  placeholder="Describe this quality grade (optional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Update Grade
                    </button>
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
        $('.edit-grade-btn').click(function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const description = $(this).data('description') || '';

            $('#edit_grade_id').val(id);
            $('#edit_grade_name').val(name);
            $('#edit_grade_description').val(description);

            // Clear previous validation errors
            $('#edit_grade_name').removeClass('is-invalid');
            $('#edit_standard_value_error').text('');

            $('#editGradeModal').modal('show');
        });

        // Handle edit form submission
        $('#editGradeForm').submit(function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const spinner = submitBtn.find('.spinner-border');

            // Show loading state
            submitBtn.prop('disabled', true);
            spinner.removeClass('d-none');

            const formData = form.serialize();
            const gradeId = $('#edit_grade_id').val();

            $.ajax({
                url: `/facilitator/quality-grades/${gradeId}/update`,
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
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
                    if (xhr.status === 422) {
                        // Validation errors
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $(`#edit_${key}`).addClass('is-invalid');
                            $(`#edit_${key}_error`).text(value[0]);
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Something went wrong',
                            icon: 'error'
                        });
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        });

        // Handle add form submission
        $('#addGradeForm').submit(function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const spinner = submitBtn.find('.spinner-border');

            // Show loading state
            submitBtn.prop('disabled', true);
            spinner.removeClass('d-none');

            const formData = form.serialize();

            $.ajax({
                url: '{{ route("facilitator.quality-grades.store") }}',
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#addGradeModal').modal('hide');
                            form[0].reset();
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
                    if (xhr.status === 422) {
                        // Validation errors
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $(`[name="${key}"]`).addClass('is-invalid');
                            $(`#${key}_error`).text(value[0]);
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Something went wrong',
                            icon: 'error'
                        });
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        });

        // Clear validation errors when modal is hidden
        $('#addGradeModal, #editGradeModal').on('hidden.bs.modal', function() {
            $(this).find('.is-invalid').removeClass('is-invalid');
            $(this).find('.invalid-feedback').text('');
        });
    });
</script>
@endsection
