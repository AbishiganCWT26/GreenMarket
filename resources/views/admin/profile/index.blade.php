@extends('admin.layouts.admin_master')

@section('title', 'Admin Profile')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

.profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.profile-header {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 30px;
    color: white;
    box-shadow: var(--shadow-md);
    animation: slideDown 0.6s ease-out;
    position: relative;
    overflow: hidden;
}

.profile-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.profile-header::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -10%;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-30px); }
    to { opacity: 1; transform: translateY(0); }
}

.header-content {
    display: flex;
    align-items: center;
    gap: 25px;
    position: relative;
    z-index: 2;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
    border: 4px solid rgba(255,255,255,0.3);
    overflow: hidden;
}

.profile-avatar:hover {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 12px 30px rgba(0,0,0,0.3);
}

.avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.header-text h1 {
    font-size: 28px;
    margin: 0 0 10px 0;
    font-weight: 700;
}

.header-text p {
    margin: 0;
    opacity: 0.9;
    font-size: 16px;
}

.profile-tabs {
    background: var(--card-bg);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    animation: cardAppear 0.5s ease-out;
    margin-bottom: 30px;
}

@keyframes cardAppear {
    from { opacity: 0; transform: translateY(20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.nav-tabs {
    display: flex;
    background: #f8fafc;
    border-bottom: 2px solid #e5e7eb;
    padding: 0 20px;
    overflow-x: auto;
}

.nav-tabs::-webkit-scrollbar {
    height: 4px;
}

.nav-tabs::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.nav-tabs::-webkit-scrollbar-thumb {
    background: var(--primary-green);
    border-radius: 4px;
}

.nav-link {
    padding: 20px 25px;
    background: none;
    border: none;
    font-size: 15px;
    font-weight: 500;
    color: var(--muted);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    white-space: nowrap;
    position: relative;
}

.nav-link:hover {
    color: var(--primary-green);
    transform: translateY(-2px);
}

.nav-link.active {
    color: var(--primary-green);
    background: white;
}

.nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100%;
    height: 3px;
    background: var(--primary-green);
    border-radius: 3px 3px 0 0;
}

.tab-content {
    padding: 30px;
}

.tab-pane {
    display: none;
    animation: fadeInUp 0.4s ease-out;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.tab-pane.active {
    display: block;
}

.form-section {
    margin-bottom: 30px;
}

.form-section h4 {
    font-size: 18px;
    color: var(--text-color);
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f3f4f6;
    display: flex;
    align-items: center;
    gap: 10px;
}

.row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.mb-3 {
    margin-bottom: 20px;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--text-color);
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-label i {
    transition: all 0.3s ease;
}

.form-group:focus-within .form-label {
    color: var(--primary-green);
}

.form-group:focus-within .form-label i {
    transform: scale(1.2);
    color: var(--primary-green);
}

.form-control, .form-select {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: #f9fafb;
}

.form-control:focus, .form-select:focus {
    outline: none;
    border-color: var(--primary-green);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    background: white;
}

.form-control:hover, .form-select:hover {
    border-color: #cbd5e1;
}

.form-control.is-invalid, .form-select.is-invalid {
    border-color: #ef4444;
}

.invalid-feedback {
    color: #ef4444;
    font-size: 12px;
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.password-container {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: var(--muted);
    font-size: 16px;
    padding: 5px;
    transition: all 0.3s ease;
    z-index: 10;
}

.password-toggle:hover {
    color: var(--primary-green);
    transform: translateY(-50%) scale(1.2);
}

.strength-meter {
    margin-top: 10px;
}

.strength-bar {
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 5px;
}

.strength-fill {
    height: 100%;
    width: 0%;
    border-radius: 3px;
    transition: all 0.3s ease;
}

.strength-label {
    font-size: 12px;
    color: var(--muted);
    display: flex;
    justify-content: space-between;
}

.strength-text {
    font-weight: 600;
}

.requirements {
    margin-top: 15px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 8px;
    border-left: 4px solid var(--primary-green);
    margin-bottom: 20px;
}

.requirements h5 {
    margin: 0 0 10px 0;
    color: var(--text-color);
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.requirements ul {
    margin: 0;
    padding-left: 20px;
    font-size: 13px;
    color: var(--muted);
}

.requirements li {
    margin-bottom: 5px;
}

.requirements li.valid {
    color: var(--primary-green);
}

.requirements li.invalid {
    color: #ef4444;
}

.btn-submit {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
    color: white;
    border: none;
    padding: 14px 30px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: auto;
    margin-top: 20px;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.btn-submit:hover:not(:disabled) {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    letter-spacing: 1px;
}

.btn-submit:active:not(:disabled) {
    transform: translateY(-1px);
}

.btn-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-submit i {
    transition: all 0.3s ease;
}

.btn-submit:hover:not(:disabled) i {
    transform: rotate(20deg);
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(4px);
}

.loading-overlay.active {
    display: flex;
    animation: fadeIn 0.3s ease;
}

.loader {
    width: 50px;
    height: 50px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid var(--primary-green);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.form-hint {
    font-size: 12px;
    color: var(--muted);
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.success-message {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: slideIn 0.5s ease-out;
    box-shadow: var(--shadow-sm);
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(-20px); }
    to { opacity: 1; transform: translateX(0); }
}

.error-message {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: slideIn 0.5s ease-out;
    box-shadow: var(--shadow-sm);
}

.input-error {
    border-color: #ef4444 !important;
}

.error-text {
    color: #ef4444;
    font-size: 12px;
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 5px;
}

@media (max-width: 1199px) {
    .profile-container {
        max-width: 100%;
    }
}

@media (max-width: 991px) {
    .row {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .nav-tabs {
        flex-wrap: wrap;
    }

    .nav-link {
        padding: 15px 20px;
        flex: 1;
        min-width: 120px;
        justify-content: center;
    }
}

@media (max-width: 767px) {
    .profile-container {
        padding: 15px;
    }

    .profile-header {
        padding: 20px;
    }

    .header-content {
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
    }

    .header-text h1 {
        font-size: 24px;
    }

    .tab-content {
        padding: 20px;
    }

    .nav-link {
        font-size: 14px;
        padding: 12px 15px;
    }

    .btn-submit {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .profile-container {
        padding: 10px;
    }

    .profile-header {
        padding: 15px;
    }

    .header-text h1 {
        font-size: 20px;
    }

    .tab-content {
        padding: 15px;
    }

    .form-control, .form-select {
        padding: 10px 12px;
    }

    .btn-submit {
        padding: 12px 20px;
        font-size: 14px;
    }
}
</style>
@endsection

@section('content')
<div class="profile-container">
    @if(session('success'))
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="profile-header">
        <div class="header-content">
            <div class="profile-avatar">
                @php
                    $user = Auth::user();
                    $photoPath = '';

                    if ($user->profile_photo && $user->profile_photo !== 'default-avatar.png') {
                        $filePath = public_path('uploads/profile_pictures/' . $user->profile_photo);
                        if (file_exists($filePath)) {
                            $photoPath = asset('uploads/profile_pictures/' . $user->profile_photo);
                        } else {
                            $photoPath = asset('assets/images/default-avatar.png');
                        }
                    } else {
                        $photoPath = asset('assets/images/default-avatar.png');
                    }
                @endphp
                <img src="{{ $photoPath }}" alt="Profile Photo" class="avatar-img" id="profileAvatar">
            </div>
            <div class="header-text">
                <h1>{{ $adminDetails->full_name ?? Auth::user()->username }}</h1>
                <p>{{ Auth::user()->role }} • {{ $adminDetails->zone_assigned_area ?? 'System Administrator' }}</p>
                <p><i class="fas fa-envelope"></i> {{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>

    <div class="profile-tabs">
        <div class="nav-tabs">
            <button class="nav-link active" data-tab="personal">
                <i class="fas fa-user-edit"></i> Personal Details
            </button>
            <button class="nav-link" data-tab="security">
                <i class="fas fa-lock"></i> Security Settings
            </button>
            <button class="nav-link" onclick="window.location.href='{{ route('admin.profile.photo') }}'">
                <i class="fas fa-camera"></i> Profile Photo
            </button>
        </div>

        <div class="tab-content">
            <div class="tab-pane active" id="personal">
                <form action="{{ route('admin.profile.updateDetails') }}" method="POST" id="profileForm" class="form-section">
                    @csrf
                    <h4><i class="fas fa-user-gear"></i> Personal Information</h4>
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-signature"></i> Full Name *
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name', $adminDetails->full_name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">
                                    <i class="fas fa-circle-exclamation"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-at"></i> Email *
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email', Auth::user()->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">
                                    <i class="fas fa-circle-exclamation"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-phone"></i> Phone Number
                            </label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                   name="phone" value="{{ old('phone', $adminDetails->phone_number ?? '') }}">
                            @error('phone')
                                <div class="invalid-feedback">
                                    <i class="fas fa-circle-exclamation"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-user-tag"></i> Username *
                            </label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                   name="username" value="{{ old('username', Auth::user()->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">
                                    <i class="fas fa-circle-exclamation"></i> {{ $message }}
                                </div>
                            @enderror
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                This username will be used for login
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-id-card"></i> NIC Number
                            </label>
                            <input type="text" class="form-control"
                                   value="{{ $adminDetails->nic_no ?? 'Not set' }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-map-marker-alt"></i> Assigned Zone
                            </label>
                            <input type="text" class="form-control"
                                   value="{{ $adminDetails->zone_assigned_area ?? 'Sri Lanka' }}" readonly>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Update Personal Information
                    </button>
                </form>
            </div>

            <div class="tab-pane" id="security">
                <form action="{{ route('admin.profile.updatePassword') }}" method="POST" id="securityForm" class="form-section">
                    @csrf

                    <div class="requirements">
                        <h5><i class="fas fa-lightbulb"></i> Password Requirements</h5>
                        <ul>
                            <li id="req-length" class="invalid">At least 8 characters</li>
                            <li id="req-uppercase" class="invalid">One uppercase letter</li>
                            <li id="req-lowercase" class="invalid">One lowercase letter</li>
                            <li id="req-number" class="invalid">One number</li>
                            <li id="req-special" class="invalid">One special character</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-unlock-alt"></i> Current Password
                        </label>
                        <div class="password-container">
                            <input type="password" name="current_password" id="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   placeholder="Enter current password" required>
                            <i class="fas fa-eye password-toggle" id="toggleCurrentPassword"></i>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback">
                                <i class="fas fa-circle-exclamation"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-fingerprint"></i> New Password
                        </label>
                        <div class="password-container">
                            <input type="password" name="new_password" id="new_password"
                                   class="form-control @error('new_password') is-invalid @enderror"
                                   placeholder="Enter new password"
                                   oninput="checkPasswordStrength(this.value)" required>
                            <i class="fas fa-eye password-toggle" id="toggleNewPassword"></i>
                        </div>

                        <div class="strength-meter">
                            <div class="strength-bar">
                                <div class="strength-fill" id="strengthFill"></div>
                            </div>
                            <div class="strength-label">
                                <span>Password Strength:</span>
                                <span class="strength-text" id="strengthText">None</span>
                            </div>
                        </div>
                        @error('new_password')
                            <div class="invalid-feedback">
                                <i class="fas fa-circle-exclamation"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-check-double"></i> Confirm New Password
                        </label>
                        <div class="password-container">
                            <input type="password" name="new_password_confirmation" id="confirm_password"
                                   class="form-control" placeholder="Confirm new password" required>
                            <i class="fas fa-eye password-toggle" id="toggleConfirmPassword"></i>
                        </div>
                        <div class="error-text" id="passwordMatchError" style="display: none;">
                            <i class="fas fa-exclamation-circle"></i> Passwords do not match
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" id="securitySubmit" disabled>
                        <i class="fas fa-shield-alt"></i> Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="loading-overlay" id="loadingOverlay">
    <div class="loader"></div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: 'linear-gradient(135deg, #10B981 0%, #059669 100%)',
            color: 'white',
            iconColor: 'white'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            background: '#ef4444',
            color: 'white',
            iconColor: 'white'
        });
    @endif

    const tabButtons = document.querySelectorAll('.nav-link');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.getAttribute('onclick')) return;

            const tabId = this.getAttribute('data-tab');

            tabButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.style.transform = '';
            });

            tabPanes.forEach(pane => {
                pane.classList.remove('active');
                pane.style.animation = '';
            });

            this.classList.add('active');
            this.style.transform = 'translateY(-2px)';

            const activePane = document.getElementById(tabId);
            if (activePane) {
                activePane.classList.add('active');
                activePane.style.animation = 'fadeInUp 0.4s ease-out';
            }
        });
    });

    const toggleCurrentPassword = document.getElementById('toggleCurrentPassword');
    const toggleNewPassword = document.getElementById('toggleNewPassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');

    toggleCurrentPassword.addEventListener('click', function() {
        togglePasswordVisibility('current_password', this);
    });

    toggleNewPassword.addEventListener('click', function() {
        togglePasswordVisibility('new_password', this);
    });

    toggleConfirmPassword.addEventListener('click', function() {
        togglePasswordVisibility('confirm_password', this);
    });

    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    newPasswordInput.addEventListener('input', function() {
        checkPasswordMatch();
    });

    confirmPasswordInput.addEventListener('input', function() {
        checkPasswordMatch();
    });

    const profileForm = document.getElementById('profileForm');
    const securityForm = document.getElementById('securityForm');

    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        showLoading();
        this.submit();
    });

    securityForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (newPassword !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Password Mismatch',
                text: 'New password and confirmation password do not match.',
                background: '#ef4444',
                color: 'white',
                iconColor: 'white',
                toast: true,
                position: 'top-end',
                timer: 3000,
                timerProgressBar: true
            });
            return;
        }

        if (currentStrength < 5) {
            Swal.fire({
                icon: 'warning',
                title: 'Weak Password',
                text: 'Please use a stronger password for better security.',
                background: '#f59e0b',
                color: 'white',
                iconColor: 'white',
                toast: true,
                position: 'top-end',
                timer: 3000,
                timerProgressBar: true
            });
            return;
        }

        showLoading();
        this.submit();
    });

    function togglePasswordVisibility(inputId, toggleIcon) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    function checkPasswordMatch() {
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const errorElement = document.getElementById('passwordMatchError');
        const submitBtn = document.getElementById('securitySubmit');

        if (confirmPassword && newPassword !== confirmPassword) {
            errorElement.style.display = 'flex';
            confirmPasswordInput.classList.add('input-error');
            submitBtn.disabled = true;
        } else {
            errorElement.style.display = 'none';
            confirmPasswordInput.classList.remove('input-error');
            submitBtn.disabled = currentStrength < 5;
        }
    }

    function showLoading() {
        document.getElementById('loadingOverlay').classList.add('active');
    }

    function hideLoading() {
        document.getElementById('loadingOverlay').classList.remove('active');
    }

    window.addEventListener('load', hideLoading);

    const formControls = document.querySelectorAll('.form-control, .form-select');
    formControls.forEach(control => {
        control.addEventListener('focus', function() {
            this.parentElement.style.transform = 'translateY(-5px)';
        });

        control.addEventListener('blur', function() {
            this.parentElement.style.transform = '';
        });

        control.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 5px 20px rgba(16, 185, 129, 0.15)';
        });

        control.addEventListener('mouseleave', function() {
            if (!this.matches(':focus')) {
                this.style.boxShadow = '';
            }
        });
    });

    const labels = document.querySelectorAll('.form-label');
    labels.forEach(label => {
        label.addEventListener('mouseenter', function() {
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.transform = 'scale(1.2) rotate(10deg)';
            }
        });

        label.addEventListener('mouseleave', function() {
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.transform = '';
            }
        });
    });

    const profileAvatar = document.getElementById('profileAvatar');
    if (profileAvatar) {
        profileAvatar.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
        });

        profileAvatar.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    }

    const submitButtons = document.querySelectorAll('.btn-submit');
    submitButtons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.letterSpacing = '1px';
        });

        btn.addEventListener('mouseleave', function() {
            this.style.letterSpacing = '0.5px';
        });
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth <= 767) {
            submitButtons.forEach(btn => {
                btn.style.width = '100%';
                btn.style.justifyContent = 'center';
            });
        } else {
            submitButtons.forEach(btn => {
                btn.style.width = '';
                btn.style.justifyContent = '';
            });
        }
    });
});

let currentStrength = 0;

function checkPasswordStrength(password) {
    let strength = 0;
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[@$!%*#?&]/.test(password)
    };

    Object.values(requirements).forEach(req => {
        if (req) strength++;
    });

    currentStrength = strength;

    const strengthText = document.getElementById('strengthText');
    const strengthFill = document.getElementById('strengthFill');
    const submitBtn = document.getElementById('securitySubmit');

    const strengthLevels = [
        { text: 'None', color: '#e5e7eb', width: '0%' },
        { text: 'Very Weak', color: '#ef4444', width: '20%' },
        { text: 'Weak', color: '#f59e0b', width: '40%' },
        { text: 'Fair', color: '#3b82f6', width: '60%' },
        { text: 'Good', color: '#8b5cf6', width: '80%' },
        { text: 'Strong', color: '#10B981', width: '100%' }
    ];

    const level = strengthLevels[strength];
    strengthText.textContent = level.text;
    strengthText.style.color = level.color;
    strengthFill.style.width = level.width;
    strengthFill.style.backgroundColor = level.color;

    Object.keys(requirements).forEach((key, index) => {
        const reqElement = document.getElementById(`req-${key}`);
        if (requirements[key]) {
            reqElement.classList.remove('invalid');
            reqElement.classList.add('valid');
        } else {
            reqElement.classList.remove('valid');
            reqElement.classList.add('invalid');
        }
    });

    const confirmPassword = document.getElementById('confirm_password').value;
    submitBtn.disabled = strength < 5 || (confirmPassword && password !== confirmPassword);

    checkPasswordMatch();
}
</script>
@endsection
