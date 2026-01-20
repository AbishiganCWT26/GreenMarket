@extends('admin.layouts.admin_master')

@section('title', 'User Management')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/index-user-management.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<div class="user-management-container">
    <div class="header-section">
        <div class="header-content">
            <h1><i class="fas fa-users-cog"></i> User Management</h1>
            <p>Manage all user accounts with complete control</p>
        </div>
        <button class="btn-add-user" id="add-user-btn">
            <i class="fas fa-user-plus"></i>
            <span>Add User</span>
        </button>
    </div>

    <div class="filters-section">
        <div class="filter-group">
            <div class="filter-item filter-search">
                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="search-user" placeholder="Search users by name, email, NIC, username...">
                    <button type="button" id="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="users-table-section">
        <div class="table-header">
            <div class="table-stats">
                <i class="fas fa-users"></i>
                <span>Total Users: <span class="users-count" id="total-users">{{ $totalUsers }}</span></span>
            </div>
        </div>

        <div id="users-container">
            @include('admin.users.partials.user_cards', ['users' => $users])
        </div>

        @if($paginator->hasPages())
        <div class="pagination-wrapper" id="main-pagination">
            {{ $paginator->links('vendor.pagination.simple-unique') }}
        </div>
        @endif
    </div>
</div>

<div class="modal-overlay" id="otpModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>OTP Verification</h3>
                <button class="btn-close" id="cancelOtp">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p class="otp-description">Enter the 6-digit OTP sent to user's mobile number</p>

                <div class="otp-input-group">
                    <input type="text" maxlength="1" class="otp-digit" data-index="1">
                    <input type="text" maxlength="1" class="otp-digit" data-index="2">
                    <input type="text" maxlength="1" class="otp-digit" data-index="3">
                    <input type="text" maxlength="1" class="otp-digit" data-index="4">
                    <input type="text" maxlength="1" class="otp-digit" data-index="5">
                    <input type="text" maxlength="1" class="otp-digit" data-index="6">
                </div>

                <div class="otp-timer">
                    <i class="fas fa-clock"></i>
                    <span>OTP expires in: <strong id="otpTimer">05:00</strong></span>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" id="resendOtp">
                    <i class="fas fa-redo"></i> Resend OTP
                </button>
                <button class="btn-primary" id="verifyOtp">
                    <i class="fas fa-check"></i> Verify
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="addUserModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3>Add New User</h3>
                <button class="btn-close" id="closeAddModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div class="form-section">
                        <label class="form-label">
                            <i class="fas fa-user-tag"></i> User Type
                        </label>
                        <select name="user_type" id="userType" class="form-select" required>
                            <option value="">Select User Type</option>
                            <option value="farmer">Farmer</option>
                            <option value="lead_farmer">Lead Farmer</option>
                            <option value="buyer">Buyer</option>
                            <option value="facilitator">Facilitator</option>
                            <option value="admin">Administrator</option>
                            <option value="subadmin">Sub Administrator</option>
                        </select>
                    </div>

                    <div id="userFields">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user"></i> Full Name *
                                </label>
                                <input type="text" name="name" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-at"></i> Username *
                                </label>
                                <input type="text" name="username" class="form-input" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input type="email" name="email" class="form-input">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-lock"></i> Password *
                                </label>
                                <input type="password" name="password" class="form-input" required minlength="8">
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-lock"></i> Confirm Password *
                                </label>
                                <input type="password" name="password_confirmation" class="form-input" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="closeModal">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let currentUserId = null;
    let currentAction = null;
    let otpTimer = null;
    let timeLeft = 300;
    let currentPage = 1;

    function showLoading() {
        $('#loadingOverlay').css('display', 'flex').hide().fadeIn();
    }

    function hideLoading() {
        $('#loadingOverlay').fadeOut();
    }

    function showAlert(icon, title, text) {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            confirmButtonColor: '#10B981',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true
        });
    }

    function startOtpTimer() {
        clearInterval(otpTimer);
        timeLeft = 300;

        otpTimer = setInterval(function() {
            timeLeft--;
            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;

            $('#otpTimer').text(`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);

            if (timeLeft <= 0) {
                clearInterval(otpTimer);
                showAlert('error', 'OTP Expired', 'Please request a new OTP');
            }
        }, 1000);
    }

    $('#add-user-btn').click(function() {
        $('#userType').val('');
        $('#userFields').html(`
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i> Full Name *
                    </label>
                    <input type="text" name="name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-at"></i> Username *
                    </label>
                    <input type="text" name="username" class="form-input" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input type="email" name="email" class="form-input">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock"></i> Password *
                    </label>
                    <input type="password" name="password" class="form-input" required minlength="8">
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock"></i> Confirm Password *
                    </label>
                    <input type="password" name="password_confirmation" class="form-input" required>
                </div>
            </div>
        `);
        $('#addUserModal').fadeIn();
    });

    $('#userType').change(function() {
        const type = $(this).val();
        let fields = '';

        if (type === 'farmer' || type === 'lead_farmer') {
            fields = `
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-id-card"></i> NIC Number *
                        </label>
                        <input type="text" name="nic_no" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone"></i> Mobile Number *
                        </label>
                        <input type="tel" name="primary_mobile" class="form-input" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Grama Niladhari Division *
                        </label>
                        <input type="text" name="grama_niladhari_division" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-whatsapp"></i> WhatsApp Number
                        </label>
                        <input type="tel" name="whatsapp_number" class="form-input">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-home"></i> Residential Address *
                    </label>
                    <textarea name="residential_address" class="form-input" rows="2" required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-credit-card"></i> Preferred Payment Method
                    </label>
                    <select name="preferred_payment" class="form-select">
                        <option value="bank">Bank Transfer</option>
                        <option value="ezcash">Ez Cash</option>
                        <option value="mcash">mCash</option>
                        <option value="all">All Methods</option>
                    </select>
                </div>
            `;

            if (type === 'lead_farmer') {
                fields += `
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-users"></i> Group Name *
                            </label>
                            <input type="text" name="group_name" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-hashtag"></i> Group Number *
                            </label>
                            <input type="text" name="group_number" class="form-input" required>
                        </div>
                    </div>
                `;
            }
        } else if (type === 'buyer') {
            fields = `
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-phone"></i> Mobile Number *
                    </label>
                    <input type="tel" name="primary_mobile" class="form-input" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-briefcase"></i> Business Name
                        </label>
                    <input type="text" name="business_name" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-building"></i> Business Type
                        </label>
                        <select name="business_type" class="form-select">
                            <option value="individual">Individual</option>
                            <option value="restaurant">Restaurant</option>
                            <option value="hotel">Hotel</option>
                            <option value="retailer">Retailer</option>
                            <option value="wholesaler">Wholesaler</option>
                        </select>
                    </div>
                </div>
            `;
        } else if (type === 'facilitator') {
            fields = `
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-id-card"></i> NIC Number *
                        </label>
                        <input type="text" name="nic_no" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone"></i> Mobile Number *
                        </label>
                        <input type="tel" name="primary_mobile" class="form-input" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-map-pin"></i> Assigned Division *
                    </label>
                    <input type="text" name="assigned_division" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-whatsapp"></i> WhatsApp Number
                    </label>
                    <input type="tel" name="whatsapp_number" class="form-input">
                </div>
            `;
        } else if (type === 'admin' || type === 'subadmin') {
        }

        $('#userFields').find('.dynamic-field').remove();
        if (fields) {
            $('#userFields').append(`<div class="dynamic-field">${fields}</div>`);
        }
    });

    $('#addUserForm').submit(function(e) {
        e.preventDefault();

        showLoading();

        const formData = $(this).serialize();

        $.ajax({
            url: '{{ route("admin.users.store") }}',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showAlert('success', 'User Created!', 'User account has been created successfully');
                    $('#addUserModal').fadeOut();
                    $('#addUserForm')[0].reset();
                    filterUsers();
                } else {
                    showAlert('error', 'Creation Failed', response.message);
                }
            },
            error: function(xhr) {
                hideLoading();
                let errorMessage = 'Failed to create user';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).join('<br>');
                }
                showAlert('error', 'Creation Failed', errorMessage);
            }
        });
    });

    $(document).on('click', '.view-user', function() {
        const userId = $(this).data('id');
        window.location.href = '/admin/users/' + userId;
    });

    $(document).on('click', '.edit-user', function() {
        const userId = $(this).data('id');
        const userCard = $(this).closest('.user-card');
        const userRole = userCard.data('role');

        if (userRole === 'farmer' || userRole === 'lead_farmer') {
            currentUserId = userId;
            currentAction = 'edit_payment';

            Swal.fire({
                title: 'OTP Verification Required',
                text: 'Updating payment details requires OTP verification. We will send an OTP to the user\'s mobile number.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Send OTP',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.users.sendOtp") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            user_id: userId,
                            action: 'edit_payment'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#otpModal').fadeIn();
                                $('.otp-digit').val('');
                                startOtpTimer();
                            } else {
                                showAlert('error', 'Failed', response.message);
                            }
                        },
                        error: function(xhr) {
                            showAlert('error', 'Failed', 'Failed to send OTP');
                        }
                    });
                }
            });
        } else {
            window.location.href = '/admin/users/' + userId + '/edit';
        }
    });

    $(document).on('click', '.promote-user', function() {
        const userId = $(this).data('id');
        const userName = $(this).closest('.user-card').find('.user-name').text();

        Swal.fire({
            title: 'Promote to Lead Farmer?',
            html: `Promote <strong>${userName}</strong> to Lead Farmer?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Promote',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                $.ajax({
                    url: '/admin/users/' + userId + '/promote',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        hideLoading();
                        if (response.success) {
                            showAlert('success', 'Promoted!', 'User is now a Lead Farmer');
                            filterUsers();
                        } else {
                            showAlert('error', 'Failed', response.message);
                        }
                    },
                    error: function(xhr) {
                        hideLoading();
                        showAlert('error', 'Failed', 'Failed to promote user');
                    }
                });
            }
        });
    });

    $(document).on('click', '.delete-user', function() {
        const userId = $(this).data('id');
        const userName = $(this).closest('.user-card').find('.user-name').text();

        Swal.fire({
            title: 'Deactivate User?',
            html: `Are you sure you want to deactivate <strong>${userName}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Deactivate',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                $.ajax({
                    url: '/admin/users/' + userId + '/deactivate',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        hideLoading();
                        if (response.success) {
                            showAlert('success', 'Deactivated!', 'User has been deactivated');
                            filterUsers();
                        } else {
                            showAlert('error', 'Failed', response.message);
                        }
                    },
                    error: function(xhr) {
                        hideLoading();
                        showAlert('error', 'Failed', 'Failed to deactivate user');
                    }
                });
            }
        });
    });

    $(document).on('click', '.suspend-user', function() {
        const userId = $(this).data('id');
        const userName = $(this).closest('.user-card').find('.user-name').text();

        Swal.fire({
            title: 'Suspend User?',
            html: `Suspend <strong>${userName}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Suspend',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                $.ajax({
                    url: '/admin/users/' + userId + '/suspend',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        hideLoading();
                        if (response.success) {
                            showAlert('success', 'Suspended!', 'User has been suspended');
                            filterUsers();
                        } else {
                            showAlert('error', 'Failed', response.message);
                        }
                    },
                    error: function(xhr) {
                        hideLoading();
                        showAlert('error', 'Failed', 'Failed to suspend user');
                    }
                });
            }
        });
    });

    $(document).on('click', '.activate-user', function() {
        const userId = $(this).data('id');

        showLoading();
        $.ajax({
            url: '/admin/users/' + userId + '/activate',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showAlert('success', 'Activated!', 'User has been activated');
                    filterUsers();
                } else {
                    showAlert('error', 'Failed', response.message);
                }
            },
            error: function(xhr) {
                hideLoading();
                showAlert('error', 'Failed', 'Failed to activate user');
            }
        });
    });

    $(document).on('click', '.make-subadmin', function() {
        const userId = $(this).data('id');
        const userName = $(this).closest('.user-card').find('.user-name').text();

        Swal.fire({
            title: 'Make Sub-Administrator?',
            html: `Make <strong>${userName}</strong> a Sub-Administrator?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Make Sub-Admin',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                $.ajax({
                    url: '/admin/users/' + userId + '/make-subadmin',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        hideLoading();
                        if (response.success) {
                            showAlert('success', 'Success!', 'User is now a Sub-Admin');
                            filterUsers();
                        } else {
                            showAlert('error', 'Failed', response.message);
                        }
                    },
                    error: function(xhr) {
                        hideLoading();
                        showAlert('error', 'Failed', 'Failed to update user role');
                    }
                });
            }
        });
    });

    $('.otp-digit').on('input', function() {
        const index = parseInt($(this).data('index'));
        const value = $(this).val();

        if (value.length === 1 && index < 6) {
            $(`.otp-digit[data-index="${index + 1}"]`).focus();
        }
    });

    $('.otp-digit').on('keydown', function(e) {
        if (e.key === 'Backspace' && $(this).val() === '') {
            const index = parseInt($(this).data('index'));
            if (index > 1) {
                $(`.otp-digit[data-index="${index - 1}"]`).focus();
            }
        }
    });

    $('#verifyOtp').click(function() {
        const otp = $('.otp-digit').map(function() {
            return $(this).val();
        }).get().join('');

        if (otp.length !== 6) {
            showAlert('error', 'Invalid OTP', 'Please enter the complete 6-digit OTP');
            return;
        }

        showLoading();
        $.ajax({
            url: '{{ route("admin.users.verifyOtp") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                user_id: currentUserId,
                otp: otp,
                action: currentAction
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    clearInterval(otpTimer);
                    $('#otpModal').fadeOut();
                    window.location.href = '/admin/users/' + currentUserId + '/edit';
                } else {
                    showAlert('error', 'Verification Failed', response.message);
                }
            },
            error: function(xhr) {
                hideLoading();
                showAlert('error', 'Verification Failed', 'Invalid OTP');
            }
        });
    });

    $('#resendOtp').click(function() {
        showLoading();
        $.ajax({
            url: '{{ route("admin.users.resendOtp") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                user_id: currentUserId
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showAlert('success', 'OTP Resent!', 'New OTP has been sent');
                    startOtpTimer();
                } else {
                    showAlert('error', 'Failed', response.message);
                }
            },
            error: function() {
                hideLoading();
                showAlert('error', 'Failed', 'Failed to resend OTP');
            }
        });
    });

    $('#cancelOtp, #closeModal, #closeAddModal, .btn-close').click(function() {
        $('#otpModal').fadeOut();
        $('#addUserModal').fadeOut();
        clearInterval(otpTimer);
    });

    $('#search-btn').click(function() {
        currentPage = 1;
        filterUsers();
    });

    $('#search-user').on('keyup', function(e) {
        if (e.key === 'Enter') {
            currentPage = 1;
            filterUsers();
        }
    });

    function filterUsers(page = currentPage) {
        showLoading();

        const search = $('#search-user').val();

        $.ajax({
            url: '{{ route("admin.users.index") }}',
            method: 'GET',
            data: {
                q: search,
                page: page
            },
            success: function(response) {
                hideLoading();

                if (typeof response === 'object' && response.html) {
                    $('#users-container').html(response.html);
                    $('#total-users').text(response.total);

                    if (response.pagination) {
                        $('#main-pagination').html(response.pagination).show();
                    } else {
                        $('#main-pagination').html('').hide();
                    }

                    const urlParams = new URLSearchParams(window.location.search);
                    currentPage = urlParams.get('page') || 1;
                } else {
                    $('#users-container').html(response);
                }
            },
            error: function(xhr) {
                hideLoading();
                showAlert('error', 'Error', 'Failed to filter users');
            }
        });
    }

    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();

        const url = $(this).attr('href');

        const urlParams = new URLSearchParams(url.split('?')[1] || '');
        const pageNumber = urlParams.get('page') || 1;

        currentPage = pageNumber;
        filterUsers(pageNumber);
    });

    $(window).click(function(e) {
        if ($(e.target).hasClass('modal-overlay')) {
            $(e.target).fadeOut();
            clearInterval(otpTimer);
        }
    });
});
</script>
@endsection
