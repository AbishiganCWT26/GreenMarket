@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'My Profile')

@section('page-title', 'My Profile')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lead_farmer/Profile.css') }}">
<link rel="stylesheet" href="{{ asset('css/lead_farmer/sweetalert_custom1.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<div class="profile-compact-container">
    <div class="compact-header">
        <div class="header-main">
            <h1><i class="fas fa-user-cog"></i>Profile Dashboard</h1>
            <p class="header-sub"><i class="fas fa-info-circle"></i>Manage your account details</p>
        </div>
    </div>

    <div class="compact-grid">
        <div class="grid-left">
            <div class="compact-card photo-section">
                <div class="card-top">
                    <h3><i class="fas fa-id-badge"></i>&ensp;Profile Photo</h3>
                </div>
                <div class="card-content">
                    <div class="compact-photo-wrapper" onclick="window.location.href='{{ route('lf.profile.photo') }}'">
                        <div class="compact-photo">
                            <img class="compact-profile-img" 
                                 src="{{ Auth::user()->profile_photo ? asset('uploads/profile_pictures/' . Auth::user()->profile_photo) : asset('assets/images/default-avatar.png') }}" 
                                 alt="Profile"
                                 id="profilePreview">
                            <div class="compact-photo-hover">
                                <i class="fas fa-camera"></i>
                            </div>
                        </div>
                    </div>
                    <div class="photo-actions">
                        <button class="compact-btn photo-btn" onclick="window.location.href='{{ route('lf.profile.photo') }}'">
                            <i class="fas fa-edit"></i>Change Photo
                        </button>
                    </div>
                </div>
            </div>

            <div class="compact-card group-section">
                <div class="card-top">
                    <h3><i class="fas fa-users"></i>&ensp;Group Info</h3>
                </div>
                <div class="card-content">
                    <div class="compact-info-row">
                        <div class="info-icon">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="info-details">
                            <div class="info-label">Group Name</div>
                            <div class="info-value">{{ $leadFarmer->group_name }}</div>
                        </div>
                    </div>
                    <div class="compact-info-row">
                        <div class="info-icon">
                            <i class="fas fa-hashtag"></i>
                        </div>
                        <div class="info-details">
                            <div class="info-label">Group Number</div>
                            <div class="info-value">{{ $leadFarmer->group_number }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="compact-card security-section">
                <div class="card-top">
                    <h3><i class="fas fa-shield-alt"></i>&ensp;Security</h3>
                </div>
                <div class="card-content">
                    <div class="compact-info-row">
                        <div class="info-icon">
                            <i class="fas fa-user-tag"></i>
                        </div>
                        <div class="info-details">
                            <div class="info-label">Username</div>
                            <div class="info-value">{{ Auth::user()->username }}</div>
                        </div>
                    </div>
                    <button class="compact-btn password-btn" onclick="showPasswordModal()">
                        <i class="fas fa-key"></i>Change Password
                    </button>
                </div>
            </div>
        </div>

        <div class="grid-right">
            <div class="compact-card details-section">
                <div class="card-top">
                    <h3><i class="fas fa-user-edit"></i>&ensp;Edit Profile Details</h3>
                </div>
                <div class="card-content">
                    <form action="{{ route('lf.profile.update') }}" method="POST" id="profileForm" class="compact-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-section">
                            <div class="section-heading">
                                <i class="fas fa-id-card"></i>
                                <span>Personal Information</span>
                            </div>
                            <div class="form-grid">
                                <div class="form-group compact">
                                    <label><i class="fas fa-user"></i>Full Name</label>
                                    <input type="text" name="name" class="form-input @error('name') error-input @enderror" 
                                           value="{{ old('name', $leadFarmer->name) }}" required>
                                    @error('name')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group compact">
                                    <label><i class="fas fa-id-card"></i>NIC Number</label>
                                    <input type="text" name="nic_no" class="form-input @error('nic_no') error-input @enderror" 
                                           value="{{ old('nic_no', $leadFarmer->nic_no) }}" required>
                                    @error('nic_no')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-heading">
                                <i class="fas fa-phone"></i>
                                <span>Contact Details</span>
                            </div>
                            <div class="form-grid">
                                <div class="form-group compact">
                                    <label><i class="fas fa-mobile-alt"></i>Primary Mobile</label>
                                    <input type="text" name="primary_mobile" class="form-input @error('primary_mobile') error-input @enderror" 
                                           value="{{ old('primary_mobile', $leadFarmer->primary_mobile) }}" required>
                                    @error('primary_mobile')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group compact">
                                    <label><i class="fab fa-whatsapp"></i>WhatsApp Number</label>
                                    <input type="text" name="whatsapp_number" class="form-input @error('whatsapp_number') error-input @enderror" 
                                           value="{{ old('whatsapp_number', $leadFarmer->whatsapp_number) }}">
                                    @error('whatsapp_number')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group compact full-width">
                                    <label><i class="fas fa-envelope"></i>Email Address</label>
                                    <input type="email" name="email" class="form-input @error('email') error-input @enderror" 
                                           value="{{ old('email', Auth::user()->email) }}">
                                    @error('email')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-heading">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Location Details</span>
                            </div>
                            <div class="form-grid">
                                <div class="form-group compact full-width">
                                    <label><i class="fas fa-home"></i>Residential Address</label>
                                    <textarea name="residential_address" class="form-input textarea-input @error('residential_address') error-input @enderror" 
                                              rows="2" required>{{ old('residential_address', $leadFarmer->residential_address) }}</textarea>
                                    @error('residential_address')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group compact full-width">
                                    <label><i class="fas fa-map-marker-alt"></i>District</label>
                                    <select name="district" class="form-input @error('district') error-input @enderror" required>
                                        <option value="" disabled selected>Select District</option>
                                        @foreach($districts as $district)
                                            <option value="{{ $district }}" {{ old('district', $leadFarmer->district) == $district ? 'selected' : '' }}>
                                                {{ $district }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('district')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group compact full-width">
                                    <label><i class="fas fa-map-signs"></i>Grama Niladhari Division</label>
                                    <input type="text" name="grama_niladhari_division" class="form-input @error('grama_niladhari_division') error-input @enderror" 
                                           value="{{ old('grama_niladhari_division', $leadFarmer->grama_niladhari_division) }}" required>
                                    @error('grama_niladhari_division')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-heading">
                                <i class="fas fa-wallet"></i>
                                <span>Bank Details</span>
                            </div>
                            <div class="form-grid">
                                <div class="form-group compact">
                                    <label><i class="fas fa-user-tie"></i>Account Holder</label>
                                    <input type="text" name="account_holder_name" class="form-input @error('account_holder_name') error-input @enderror" 
                                           value="{{ old('account_holder_name', $leadFarmer->account_holder_name) }}" required>
                                    @error('account_holder_name')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group compact">
                                    <label><i class="fas fa-hashtag"></i>Account Number</label>
                                    <input type="text" name="account_number" class="form-input @error('account_number') error-input @enderror" 
                                           value="{{ old('account_number', $leadFarmer->account_number) }}" required>
                                    @error('account_number')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group compact">
                                    <label><i class="fas fa-university"></i>Bank Name</label>
                                    <input type="text" name="bank_name" class="form-input @error('bank_name') error-input @enderror" 
                                           value="{{ old('bank_name', $leadFarmer->bank_name) }}" required>
                                    @error('bank_name')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group compact">
                                    <label><i class="fas fa-code-branch"></i>Bank Branch</label>
                                    <input type="text" name="bank_branch" class="form-input @error('bank_branch') error-input @enderror" 
                                           value="{{ old('bank_branch', $leadFarmer->bank_branch) }}" required>
                                    @error('bank_branch')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-actions compact">
                            <button type="button" class="action-btn cancel-btn" onclick="resetForm()">
                                <i class="fas fa-redo"></i>Reset
                            </button>
                            <button type="submit" class="action-btn save-btn">
                                <i class="fas fa-save"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="passwordModal">
    <div class="modal-compact">
        <div class="modal-header">
            <h3><i class="fas fa-key"></i>Change Password</h3>
            <button class="modal-close" onclick="hidePasswordModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="changePasswordForm" class="compact-form">
                @csrf
                <div class="form-group compact">
                    <label><i class="fas fa-lock"></i>Current Password</label>
                    <div class="password-field">
                        <input type="password" name="current_password" class="form-input" id="currentPassword" required>
                        <i class="fas fa-eye password-toggle" id="toggleCurrentPassword" onclick="togglePasswordVisibility('currentPassword', 'toggleCurrentPassword')"></i>
                    </div>
                </div>
                <div class="form-group compact">
                    <label><i class="fas fa-lock"></i>New Password</label>
                    <div class="password-field">
                        <input type="password" name="new_password" class="form-input" id="newPassword" required>
                        <i class="fas fa-eye password-toggle" id="toggleNewPassword" onclick="togglePasswordVisibility('newPassword', 'toggleNewPassword')"></i>
                    </div>
                </div>
                <div class="form-group compact">
                    <label><i class="fas fa-lock"></i>Confirm Password</label>
                    <div class="password-field">
                        <input type="password" name="new_password_confirmation" class="form-input" id="confirmPassword" required>
                        <i class="fas fa-eye password-toggle" id="toggleConfirmPassword" onclick="togglePasswordVisibility('confirmPassword', 'toggleConfirmPassword')"></i>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="action-btn cancel-btn" onclick="hidePasswordModal()">
                        <i class="fas fa-times"></i>Cancel
                    </button>
                    <button type="submit" class="action-btn save-btn">
                        <i class="fas fa-check"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profileForm');
    const changePasswordForm = document.getElementById('changePasswordForm');
    const originalFormData = profileForm ? new FormData(profileForm) : null;

    window.showPasswordModal = function() {
        document.getElementById('passwordModal').classList.add('active');
        document.body.classList.add('no-scroll');
    };

    window.hidePasswordModal = function() {
        document.getElementById('passwordModal').classList.remove('active');
        document.body.classList.remove('no-scroll');
        if (changePasswordForm) changePasswordForm.reset();
    };

    window.togglePasswordVisibility = function(passwordFieldId, toggleIconId) {
        const passwordField = document.getElementById(passwordFieldId);
        const toggleIcon = document.getElementById(toggleIconId);
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    };

    window.validatePassword = function(password) {
        const minLength = 8;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasNumber = /\d/.test(password);
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        
        return password.length >= minLength && hasUpperCase && hasNumber && hasSpecialChar;
    };

    window.resetForm = function() {
        if (!profileForm || !originalFormData) return;
        
        profileForm.reset();
        
        Swal.fire({
            icon: 'info',
            title: 'Form Reset',
            text: 'All changes have been discarded',
            timer: 2000,
            showConfirmButton: false,
            background: '#f6f8fa',
            color: '#0f1724'
        });
    };

    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Update Profile',
                text: 'Are you sure you want to update your profile?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Update',
                cancelButtonText: 'Cancel',
                background: '#f6f8fa',
                color: '#0f1724'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    }

    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const newPassword = formData.get('new_password');
            const confirmPassword = formData.get('new_password_confirmation');
            
            if (newPassword !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch',
                    text: 'New password and confirmation do not match',
                    background: '#f6f8fa',
                    color: '#0f1724'
                });
                return;
            }
            
            if (!validatePassword(newPassword)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Requirements',
                    html: 'Password must contain:<br>• At least 8 characters<br>• One uppercase letter<br>• One number<br>• One special character',
                    background: '#f6f8fa',
                    color: '#0f1724'
                });
                return;
            }
            
            fetch('{{ route("lf.profile.update.password") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        background: '#f6f8fa',
                        color: '#0f1724'
                    }).then(() => {
                        hidePasswordModal();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                        background: '#f6f8fa',
                        color: '#0f1724'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    background: '#f6f8fa',
                    color: '#0f1724'
                });
            });
        });
    }

    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            hidePasswordModal();
        }
    });

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false,
            background: '#f6f8fa',
            color: '#0f1724'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            timer: 3000,
            showConfirmButton: false,
            background: '#f6f8fa',
            color: '#0f1724'
        });
    @endif

    @if($errors->any())
        const errorMessages = [];
        @foreach($errors->all() as $error)
            errorMessages.push('{{ $error }}');
        @endforeach
        
        if (errorMessages.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: errorMessages.join('<br>'),
                background: '#f6f8fa',
                color: '#0f1724'
            });
        }
    @endif
});
</script>
@endsection