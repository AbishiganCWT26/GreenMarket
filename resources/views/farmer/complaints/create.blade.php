@extends('farmer.layouts.farmer_master')

@section('title', 'File Complaint')
@section('page-title', 'File a Complaint')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/farmer/complaints.css') }}">
@endsection

@section('content')
<div class="complaint-container">
    <div class="complaint-header">
        <h1>
            <i class="fa-solid fa-headset"></i>
            File a Complaint
        </h1>
        <p>Report any issues you're facing with buyers, lead farmers, or facilitators. Your complaint will be reviewed by our admin team.</p>
    </div>

    <div class="complaint-card">
        <form id="complaintForm" class="complaint-form" method="POST" action="{{ route('farmer.complaints.store') }}">
            @csrf

            <div class="form-section">
                <div class="form-header">
                    <i class="fa-solid fa-exclamation-circle"></i>
                    <h3>Complaint Details</h3>
                </div>

                <div class="form-group">
                    <label>
                        <i class="fa-solid fa-tag"></i>
                        Complaint Type <span class="required">*</span>
                    </label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="payment_delay" name="complaint_type" value="payment_delay" required>
                            <label for="payment_delay">
                                <i class="fa-solid fa-clock"></i>
                                Payment Delay
                            </label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="payment_missing" name="complaint_type" value="payment_missing" required>
                            <label for="payment_missing">
                                <i class="fa-solid fa-money-bill-wave"></i>
                                Missing Payment
                            </label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="wrong_data_entry" name="complaint_type" value="wrong_data_entry" required>
                            <label for="wrong_data_entry">
                                <i class="fa-solid fa-database"></i>
                                Wrong Data Entry
                            </label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="other" name="complaint_type" value="other" required>
                            <label for="other">
                                <i class="fa-solid fa-ellipsis-h"></i>
                                Other Issue
                            </label>
                        </div>
                    </div>
                    <span class="help-text">
                        <i class="fa-solid fa-info-circle"></i>
                        Select the type of complaint you want to file
                    </span>
                </div>


                <div class="form-group">
                    <label>
                        <i class="fa-solid fa-shopping-cart"></i>
                        Related Order (Optional)
                    </label>
                    <div class="select-wrapper">
                        <select name="related_order_id" id="related_order_id" class="form-control">
                            <option value="">Select Order</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->id }}">
                                    Order #{{ $order->order_number }} - {{ $order->buyer->name ?? 'Customer' }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <span class="help-text">
                        <i class="fa-solid fa-info-circle"></i>
                        Select the order related to this complaint (if applicable)
                    </span>
                </div>
            </div>

            <div class="form-section">
                <div class="form-header">
                    <i class="fa-solid fa-align-left"></i>
                    <h3>Complaint Description</h3>
                </div>

                <div class="form-group">
                    <label>
                        <i class="fa-solid fa-file-alt"></i>
                        Description <span class="required">*</span>
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        class="form-control"
                        placeholder="Please provide detailed information about your complaint. Include dates, amounts, and any relevant details."
                        minlength="10"
                        maxlength="1000"
                        required
                    ></textarea>
                    <span class="help-text">
                        <i class="fa-solid fa-info-circle"></i>
                        Minimum 10 characters, maximum 1000 characters
                    </span>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('farmer.complaints.list') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-times"></i>
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fa-solid fa-paper-plane"></i>
                    Submit Complaint
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#complaintForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();

        submitBtn.prop('disabled', true);
        submitBtn.html('<span class="loading"></span> Submitting...');

        Swal.fire({
            title: 'Submitting Complaint',
            text: 'Please wait while we process your complaint...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.close();

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Complaint Submitted!',
                        html: `
                            <div style="text-align: center; padding: 1rem;">
                                <i class="fa-solid fa-check-circle" style="font-size: 4rem; color: var(--primary-green); margin-bottom: 1rem;"></i>
                                <h3 style="color: var(--text-color); margin-bottom: 1rem;">Thank You!</h3>
                                <p style="color: var(--muted);">Your complaint has been successfully submitted. Our admin team will review it shortly.</p>
                                <div style="background: #f0f9ff; padding: 1rem; border-radius: 8px; margin-top: 1rem;">
                                    <p style="margin: 0; color: var(--text-color);">
                                        <i class="fa-solid fa-bell"></i>
                                        A notification has been sent to the admin team.
                                    </p>
                                </div>
                            </div>
                        `,
                        showConfirmButton: true,
                        confirmButtonText: 'View Complaints',
                        confirmButtonColor: 'var(--primary-green)',
                        showCancelButton: true,
                        cancelButtonText: 'File Another'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route("farmer.complaints.list") }}';
                        } else if (result.isDismissed) {
                            form[0].reset();
                            submitBtn.html(originalText);
                            submitBtn.prop('disabled', false);
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Failed',
                        text: response.message || 'Failed to submit complaint. Please try again.',
                        confirmButtonColor: 'var(--primary-green)'
                    });
                    submitBtn.html(originalText);
                    submitBtn.prop('disabled', false);
                }
            },
            error: function(xhr) {
                Swal.close();

                let errorMessage = 'An error occurred while submitting your complaint.';

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors)[0][0];
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Submission Error',
                    html: `
                        <div style="text-align: center; padding: 1rem;">
                            <i class="fa-solid fa-exclamation-triangle" style="font-size: 4rem; color: #ef4444; margin-bottom: 1rem;"></i>
                            <h3 style="color: var(--text-color); margin-bottom: 1rem;">Error Occurred</h3>
                            <p style="color: var(--muted);">${errorMessage}</p>
                        </div>
                    `,
                    confirmButtonColor: 'var(--primary-green)'
                });

                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });

    $('.form-control').on('focus', function() {
        $(this).parent().addClass('focused');
    }).on('blur', function() {
        $(this).parent().removeClass('focused');
    });

    $('.radio-option label').on('click', function() {
        $(this).closest('.radio-group').find('label').removeClass('active');
        $(this).addClass('active');
    });

    $('#description').on('input', function() {
        const charCount = $(this).val().length;
        const minLength = 10;
        const maxLength = 1000;

        if (charCount < minLength) {
            $(this).css('border-color', '#f59e0b');
        } else if (charCount > maxLength) {
            $(this).css('border-color', '#ef4444');
        } else {
            $(this).css('border-color', '#10B981');
        }
    });
});
</script>
@endsection
