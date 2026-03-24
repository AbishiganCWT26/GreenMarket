@extends('admin.layouts.admin_master')

@section('title', 'Profile Details Update')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="{{ asset('js/form-validation.js') }}"></script>
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
    --shadow-lg: 0 15px 30px rgba(15,23,36,0.12);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.profile-update-wrapper {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.profile-header {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 30px;
    color: white;
    box-shadow: var(--shadow-md);
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
    animation: float 6s ease-in-out infinite;
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
    animation: float 8s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
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
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    transition: all 0.4s ease;
    border: 4px solid rgba(255,255,255,0.4);
    overflow: hidden;
}

.profile-avatar:hover {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 15px 40px rgba(0,0,0,0.4);
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-icon {
    font-size: 48px;
    color: var(--primary-green);
}

.header-text h1 {
    font-size: 28px;
    margin-bottom: 8px;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.header-text p {
    font-size: 16px;
    opacity: 0.9;
    margin: 0;
}

.profile-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.profile-card {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 30px;
    box-shadow: var(--shadow-sm);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 1px solid rgba(229, 231, 235, 0.5);
    animation: cardAppear 0.6s ease-out;
    animation-fill-mode: both;
}

.profile-card:nth-child(1) { animation-delay: 0.1s; }
.profile-card:nth-child(2) { animation-delay: 0.2s; }

@keyframes cardAppear {
    from { opacity: 0; transform: translateY(30px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.profile-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-green);
}

.card-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f3f4f6;
}

.card-icon {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: white;
    transition: all 0.4s ease;
    background: linear-gradient(135deg, var(--blue) 0%, #2563eb 100%);
}

.profile-card:nth-child(2) .card-icon {
    background: linear-gradient(135deg, var(--purple) 0%, #7c3aed 100%);
}

.profile-card:hover .card-icon {
    transform: rotate(20deg) scale(1.1);
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
}

.card-header h3 {
    margin: 0;
    font-size: 22px;
    font-weight: 600;
    color: var(--text-color);
    flex: 1;
}

.form-group {
    margin-bottom: 24px;
    position: relative;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    font-weight: 500;
    color: var(--text-color);
    font-size: 15px;
    transition: all 0.3s ease;
}

.form-label i {
    transition: all 0.3s ease;
    width: 20px;
    text-align: center;
}

.form-group:focus-within .form-label {
    color: var(--primary-green);
}

.form-group:focus-within .form-label i {
    transform: scale(1.2);
    color: var(--primary-green);
}

.form-control {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s ease;
    background: #f9fafb;
    font-family: 'Poppins', sans-serif;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-green);
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
    background: white;
}

.form-control:hover {
    border-color: #cbd5e1;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.password-container {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 18px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: var(--muted);
    font-size: 18px;
    padding: 8px;
    transition: all 0.3s ease;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.password-toggle:hover {
    color: var(--primary-green);
    background: rgba(16, 185, 129, 0.1);
    transform: translateY(-50%) scale(1.2);
}

.submit-btn {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
    color: white;
    border: none;
    padding: 16px 35px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    width: 100%;
    margin-top: 25px;
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(16, 185, 129, 0.4);
    letter-spacing: 1px;
}

.submit-btn:active:not(:disabled) {
    transform: translateY(-2px);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}

.submit-btn i {
    transition: all 0.4s ease;
}

.submit-btn:hover:not(:disabled) i {
    transform: rotate(20deg) scale(1.2);
}

.strength-meter {
    margin-top: 15px;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.strength-bar {
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 8px;
    position: relative;
}

.strength-fill {
    height: 100%;
    width: 0%;
    border-radius: 4px;
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
}

.strength-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg,
        transparent 0%,
        rgba(255,255,255,0.3) 50%,
        transparent 100%);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.strength-label {
    font-size: 13px;
    color: var(--muted);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.strength-text {
    font-weight: 600;
    font-size: 14px;
}

.requirements {
    margin-top: 20px;
    padding: 20px;
    background: #f8fafc;
    border-radius: 12px;
    border-left: 4px solid var(--primary-green);
    animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(-20px); }
    to { opacity: 1; transform: translateX(0); }
}

.requirements h5 {
    margin: 0 0 15px 0;
    color: var(--text-color);
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.requirements ul {
    margin: 0;
    padding-left: 20px;
    font-size: 14px;
    color: var(--muted);
}

.requirements li {
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
}

.requirements li i {
    font-size: 12px;
    width: 16px;
}

.requirements li.valid {
    color: var(--primary-green);
}

.requirements li.invalid {
    color: #ef4444;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.95);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(8px);
    animation: fadeIn 0.3s ease;
}

.loading-overlay.active {
    display: flex;
}

.loader {
    width: 60px;
    height: 60px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid var(--primary-green);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.form-hint {
    font-size: 13px;
    color: var(--muted);
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding-left: 30px;
}

.form-hint i {
    font-size: 14px;
}

.success-message {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%);
    color: white;
    padding: 20px 25px;
    border-radius: 12px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 15px;
    animation: slideInRight 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: var(--shadow-md);
}

@keyframes slideInRight {
    from { opacity: 0; transform: translateX(-30px); }
    to { opacity: 1; transform: translateX(0); }
}

.error-message {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    padding: 20px 25px;
    border-radius: 12px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 15px;
    animation: slideInRight 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: var(--shadow-md);
}

.input-error {
    border-color: #ef4444 !important;
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

.error-text {
    color: #ef4444;
    font-size: 13px;
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    animation: fadeIn 0.3s ease;
}

.role-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    background: linear-gradient(135deg, var(--accent-amber) 0%, #d97706 100%);
    color: white;
    margin-left: 15px;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

.success-badge {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
}

/* Responsive Styles */
@media (max-width: 1199px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }

    .profile-update-wrapper {
        padding: 15px;
    }
}

@media (max-width: 991px) {
    .header-content {
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }

    .profile-avatar {
        width: 90px;
        height: 90px;
    }

    .header-text h1 {
        font-size: 24px;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}

@media (max-width: 767px) {
    .profile-update-wrapper {
        padding: 10px;
    }

    .profile-header {
        padding: 20px;
        margin-bottom: 20px;
    }

    .profile-card {
        padding: 20px;
    }

    .card-header {
        flex-direction: column;
        text-align: center;
        gap: 12px;
    }

    .card-icon {
        width: 50px;
        height: 50px;
        font-size: 22px;
    }

    .card-header h3 {
        font-size: 20px;
    }

    .form-control {
        padding: 12px 15px;
        font-size: 14px;
    }

    .submit-btn {
        padding: 14px 25px;
        font-size: 15px;
    }
}

@media (max-width: 480px) {
    .profile-header {
        padding: 15px;
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
    }

    .header-text h1 {
        font-size: 20px;
    }

    .header-text p {
        font-size: 14px;
    }

    .profile-card {
        padding: 15px;
    }

    .card-icon {
        width: 45px;
        height: 45px;
        font-size: 20px;
    }

    .form-label {
        font-size: 14px;
    }

    .submit-btn {
        padding: 12px 20px;
        font-size: 14px;
    }

    .requirements {
        padding: 15px;
    }

    .requirements h5 {
        font-size: 15px;
    }

    .requirements ul {
        font-size: 13px;
    }
}

@media (min-width: 1200px) {
    .profile-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 768px) and (max-width: 1199px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
}

@media (min-width: 992px) and (max-width: 1199px) {
    .profile-update-wrapper {
        max-width: 900px;
    }
}

/* Print Styles */
@media print {
    .profile-header {
        background: #fff !important;
        color: #000 !important;
        box-shadow: none !important;
    }

    .profile-card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        page-break-inside: avoid;
    }

    .submit-btn {
        display: none !important;
    }

    .password-toggle {
        display: none !important;
    }
}
</style>
@endsection

@section('content')
<div class="profile-update-wrapper">
    @if(session('success'))
        <div class="success-message">
            <i class="fas fa-check-circle fa-2x"></i>
            <div>
                <h4 style="margin: 0 0 5px 0; font-size: 18px;">Success!</h4>
                <p style="margin: 0; opacity: 0.9;">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="error-message">
            <i class="fas fa-exclamation-circle fa-2x"></i>
            <div>
                <h4 style="margin: 0 0 5px 0; font-size: 18px;">Error!</h4>
                <p style="margin: 0; opacity: 0.9;">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="profile-header">
        <div class="header-content">
            @php
                $admin = Auth::user();
                $photoPath = $admin->profile_photo ? asset('uploads/profile_pictures/' . $admin->profile_photo) : asset('assets/icons/admin-icon.svg');
            @endphp
            <div class="profile-avatar">
                <img src="{{ $photoPath }}" alt="Profile Photo"
                     onerror="this.src='{{ asset('assets/icons/admin-icon.svg') }}'">
            </div>
            <div class="header-text">
                <h1>
                    {{ $admin->name ?? 'Administrator' }}
                    <span class="role-badge {{ $admin->role === 'admin' ? 'success-badge' : '' }}">
                        <i class="fas fa-{{ $admin->role === 'admin' ? 'crown' : 'user-shield' }}"></i>
                        {{ ucfirst($admin->role) }}
                    </span>
                </h1>
                <p>Update and manage your administrative profile information</p>
            </div>
        </div>
    </div>

    <div class="profile-grid">
        <div class="profile-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <h3>Personal Information</h3>
            </div>

            <form action="{{ route('admin.profile.updateDetails') }}" method="POST" id="personalForm">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i> Full Name
                    </label>
                    <input type="text" name="full_name" class="form-control @error('full_name') input-error @enderror"
                           value="{{ old('full_name', $admin->full_name ?? '') }}"
                           placeholder="Enter your full name" required>
                    @error('full_name')
                        <div class="error-text">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-id-card"></i> NIC Number
                        </label>
                        <input type="text" name="nic_no" class="form-control @error('nic_no') input-error @enderror"
                               value="{{ old('nic_no', $admin->nic_no ?? '') }}"
                               placeholder="Enter NIC number" required>
                        @error('nic_no')
                            <div class="error-text">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone"></i> Phone Number
                        </label>
                        <input type="tel" name="phone_number" class="form-control @error('phone_number') input-error @enderror"
                               value="{{ old('phone_number', $admin->phone_number ?? '') }}"
                               placeholder="Enter phone number" required>
                        @error('phone_number')
                            <div class="error-text">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-at"></i> Email Address
                    </label>
                    <input type="email" name="email" class="form-control @error('email') input-error @enderror"
                           value="{{ old('email', $admin->email ?? '') }}"
                           placeholder="Enter your email" required>
                    @error('email')
                        <div class="error-text">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                @php
                    $adminDetails = $admin->adminDetails ?? null;
                    $role = $adminDetails->role ?? 'admin';
                    $zone = $adminDetails->zone_assigned_area ?? 'Sri Lanka';
                @endphp

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user-tag"></i> Role
                        </label>
                        <select name="role" id="role" class="form-control @error('role') input-error @enderror" required>
                            <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="subadmin" {{ $role === 'subadmin' ? 'selected' : '' }}>Sub Administrator</option>
                        </select>
                        @error('role')
                            <div class="error-text">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group" id="zone-field" style="{{ $role === 'admin' ? 'display: none;' : '' }}">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Zone Assigned Area
                        </label>
                        <input type="text" name="zone_assigned_area"
                               class="form-control @error('zone_assigned_area') input-error @enderror"
                               value="{{ old('zone_assigned_area', $role === 'subadmin' ? $zone : 'Sri Lanka') }}"
                               placeholder="Enter assigned zone area">
                        @error('zone_assigned_area')
                            <div class="error-text">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="submit-btn" id="personalSubmit">
                    <i class="fas fa-save"></i> Update Personal Information
                </button>
            </form>
        </div>

        <div class="profile-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Account Security</h3>
            </div>

            <form action="{{ route('admin.profile.updatePassword') }}" method="POST" id="securityForm">
                @csrf

                <div class="requirements">
                    <h5><i class="fas fa-lightbulb"></i> Password Requirements</h5>
                    <ul>
                        <li id="rule-length" class="rule-item invalid"><i class="fas fa-times-circle"></i> Minimum 8 characters</li>
                        <li id="rule-number" class="rule-item invalid"><i class="fas fa-times-circle"></i> At least 1 number (0–9)</li>
                        <li id="rule-capital" class="rule-item invalid"><i class="fas fa-times-circle"></i> At least 1 capital letter (A–Z)</li>
                        <li id="rule-lowercase" class="rule-item invalid"><i class="fas fa-times-circle"></i> At least 1 lowercase letter (a–z)</li>
                        <li id="rule-special" class="rule-item invalid"><i class="fas fa-times-circle"></i> At least 1 special character</li>
                        <li id="rule-no-space" class="rule-item invalid"><i class="fas fa-times-circle"></i> No spaces allowed</li>
                        <li id="rule-no-repeat" class="rule-item invalid"><i class="fas fa-times-circle"></i> No consecutive repeated characters</li>
                        <li id="rule-no-sequence" class="rule-item invalid"><i class="fas fa-times-circle"></i> No sequential characters</li>
                        <li id="rule-not-common" class="rule-item invalid"><i class="fas fa-times-circle"></i> No common passwords</li>
                        <li id="rule-no-links" class="rule-item invalid"><i class="fas fa-times-circle"></i> No links or URLs</li>
                        <li id="rule-no-personal" class="rule-item invalid"><i class="fas fa-times-circle"></i> No personal info</li>
                    </ul>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-unlock-alt"></i> Current Password
                    </label>
                    <div class="password-container">
                        <input type="password" name="current_password" id="current_password"
                               class="form-control @error('current_password') input-error @enderror"
                               placeholder="Enter current password" required>
                        <i class="fas fa-eye password-toggle" id="toggleCurrentPassword"></i>
                    </div>
                    @error('current_password')
                        <div class="error-text">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-fingerprint"></i> New Password
                    </label>
                    <div class="password-container">
                        <input type="password" name="new_password" id="new_password"
                               class="form-control @error('new_password') input-error @enderror"
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
                        <div class="error-text">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-check-double"></i> Confirm New Password
                    </label>
                    <div class="password-container">
                        <input type="password" name="new_password_confirmation" id="confirm_password"
                               class="form-control @error('new_password_confirmation') input-error @enderror"
                               placeholder="Confirm new password" required>
                        <i class="fas fa-eye password-toggle" id="toggleConfirmPassword"></i>
                    </div>
                    <div class="error-text" id="passwordMatchError" style="display: none;">
                        <i class="fas fa-exclamation-circle"></i> Passwords do not match
                    </div>
                </div>

                <button type="submit" class="submit-btn" id="securitySubmit" disabled>
                    <i class="fas fa-shield-alt"></i> Update Password
                </button>
            </form>
        </div>
    </div>

    <div class="profile-card" style="margin-top: 25px;">
        <div class="card-header">
            <div class="card-icon" style="background: linear-gradient(135deg, var(--accent-amber) 0%, #d97706 100%);">
                <i class="fas fa-camera"></i>
            </div>
            <h3>Profile Photo</h3>
        </div>
        <div style="text-align: center; padding: 20px;">
            <p style="color: var(--muted); margin-bottom: 20px;">
                <i class="fas fa-info-circle"></i> Update your profile photo for better identification
            </p>
            <a href="{{ route('admin.profile.photo') }}" class="submit-btn" style="width: auto; padding: 12px 30px;">
                <i class="fas fa-camera-retro"></i> Change Profile Photo
            </a>
        </div>
    </div>
</div>

<div class="loading-overlay" id="loadingOverlay">
    <div class="loader"></div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/form-validation.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleCurrentPassword = document.getElementById('toggleCurrentPassword');
    const toggleNewPassword = document.getElementById('toggleNewPassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const roleSelect = document.getElementById('role');
    const zoneField = document.getElementById('zone-field');
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    toggleCurrentPassword.addEventListener('click', function() {
        togglePasswordVisibility('current_password', this);
    });

    toggleNewPassword.addEventListener('click', function() {
        togglePasswordVisibility('new_password', this);
    });

    toggleConfirmPassword.addEventListener('click', function() {
        togglePasswordVisibility('confirm_password', this);
    });

    roleSelect.addEventListener('change', function() {
        if (this.value === 'admin') {
            zoneField.style.display = 'none';
            zoneField.querySelector('input').value = 'Sri Lanka';
        } else {
            zoneField.style.display = 'block';
        }
    });

    newPasswordInput.addEventListener('input', function() {
        checkPasswordMatch();
    });

    confirmPasswordInput.addEventListener('input', function() {
        checkPasswordMatch();
    });

    const personalForm = document.getElementById('personalForm');
    const securityForm = document.getElementById('securityForm');

    personalForm.addEventListener('submit', function(e) {
        e.preventDefault();
        showLoading();

        Swal.fire({
            title: 'Updating Profile...',
            text: 'Please wait while we update your information',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

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

        const passwordValidationResult = validateAdvancedPassword(newPassword, {
            username: "{{ Auth::user()->username }}",
            email: "{{ Auth::user()->email }}"
        });

        if (!passwordValidationResult.isValid) {
            Swal.fire({
                icon: 'warning',
                title: 'Weak Password',
                text: 'Please use a stronger password that meets all requirements for better security.',
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

        Swal.fire({
            title: 'Updating Password...',
            text: 'Please wait while we update your security credentials',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

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

        const passwordValidationResult = validateAdvancedPassword(newPassword, {
            username: "{{ Auth::user()->username }}",
            email: "{{ Auth::user()->email }}"
        });

        if (confirmPassword && newPassword !== confirmPassword) {
            errorElement.style.display = 'flex';
            confirmPasswordInput.classList.add('input-error');
            submitBtn.disabled = true;
        } else {
            errorElement.style.display = 'none';
            confirmPasswordInput.classList.remove('input-error');
            submitBtn.disabled = !passwordValidationResult.isValid;
        }
    }

    function showLoading() {
        document.getElementById('loadingOverlay').classList.add('active');
    }

    function hideLoading() {
        document.getElementById('loadingOverlay').classList.remove('active');
    }

    window.addEventListener('load', hideLoading);

    const cards = document.querySelectorAll('.profile-card');
    cards.forEach((card, index) => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-10px) scale(1.02)';
            const icon = card.querySelector('.card-icon');
            if (icon) {
                icon.style.transform = 'rotate(20deg) scale(1.1)';
            }
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0) scale(1)';
            const icon = card.querySelector('.card-icon');
            if (icon) {
                icon.style.transform = 'rotate(0) scale(1)';
            }
        });
    });

    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'translateY(-5px)';
        });

        input.addEventListener('blur', function() {
            this.parentElement.style.transform = '';
        });
    });

    const buttons = document.querySelectorAll('.submit-btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });

        button.addEventListener('mouseleave', function() {
            if (!this.disabled) {
                this.style.transform = 'translateY(0)';
            }
        });
    });

    if (window.innerWidth <= 767) {
        document.querySelectorAll('.form-row').forEach(row => {
            if (row.style.gridTemplateColumns === '1fr 1fr') {
                row.style.gridTemplateColumns = '1fr';
            }
        });
    }

    window.addEventListener('resize', function() {
        if (window.innerWidth <= 767) {
            document.querySelectorAll('.form-row').forEach(row => {
                if (row.style.gridTemplateColumns === '1fr 1fr') {
                    row.style.gridTemplateColumns = '1fr';
                }
            });
        } else {
            document.querySelectorAll('.form-row').forEach(row => {
                row.style.gridTemplateColumns = '1fr 1fr';
            });
        }
    });

    const successMessage = document.querySelector('.success-message');
    const errorMessage = document.querySelector('.error-message');

    if (successMessage) {
        setTimeout(() => {
            successMessage.style.animation = 'slideOutRight 0.5s ease-out forwards';
            setTimeout(() => successMessage.remove(), 500);
        }, 5000);
    }

    if (errorMessage) {
        setTimeout(() => {
            errorMessage.style.animation = 'slideOutRight 0.5s ease-out forwards';
            setTimeout(() => errorMessage.remove(), 500);
        }, 5000);
    }
});

let currentStrength = 0;

function checkPasswordStrength(password) {
    if (!password) {
        // Reset UI if password is empty
        const strengthText = document.getElementById('strengthText');
        const strengthFill = document.getElementById('strengthFill');
        const submitBtn = document.getElementById('securitySubmit');
        strengthText.textContent = 'None';
        strengthText.style.color = '#e5e7eb';
        strengthFill.style.width = '0%';
        strengthFill.style.backgroundColor = '#e5e7eb';
        
        // Reset all 11 rules
        updatePasswordRuleFeedback({
            rules: {
                'length': false, 'number': false, 'capital': false, 'lowercase': false,
                'special': false, 'no-space': false, 'no-repeat': false, 'no-sequence': false,
                'not-common': false, 'no-links': false, 'no-personal': false
            }
        });
        
        if (submitBtn) submitBtn.disabled = true;
        checkPasswordMatch();
        return;
    }

    const result = validateAdvancedPassword(password, {
        username: "{{ Auth::user()->username }}",
        email: "{{ Auth::user()->email }}"
    });

    updatePasswordRuleFeedback(result);

    const strengthText = document.getElementById('strengthText');
    const strengthFill = document.getElementById('strengthFill');
    const submitBtn = document.getElementById('securitySubmit');

    strengthText.textContent = result.strengthText;
    strengthText.style.color = result.color;
    strengthFill.style.width = result.percent + '%';
    strengthFill.style.backgroundColor = result.color;

    const confirmPasswordInput = document.getElementById('confirm_password');
    const confirmPassword = confirmPasswordInput ? confirmPasswordInput.value : '';
    if (submitBtn) submitBtn.disabled = !result.isValid || (confirmPassword && password !== confirmPassword);

    checkPasswordMatch();
}

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
</script>
@endsection
