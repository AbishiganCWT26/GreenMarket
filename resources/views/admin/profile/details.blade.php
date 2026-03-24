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
}

.profile-update-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.profile-header-card {
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

.profile-header-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.profile-header-card::after {
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
}

.profile-avatar:hover {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 12px 30px rgba(0,0,0,0.3);
}

.avatar-icon {
    font-size: 48px;
    color: var(--primary-green);
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

.profile-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.profile-card {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 25px;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
    animation: cardAppear 0.5s ease-out;
    animation-fill-mode: both;
    border: 1px solid rgba(229, 231, 235, 0.5);
}

.profile-card:nth-child(1) { animation-delay: 0.1s; }
.profile-card:nth-child(2) { animation-delay: 0.2s; }
.profile-card:nth-child(3) { animation-delay: 0.3s; }

@keyframes cardAppear {
    from { opacity: 0; transform: translateY(20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.profile-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary-green);
}

.card-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f3f4f6;
}

.card-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    transition: all 0.3s ease;
}

.profile-card:hover .card-icon {
    transform: rotate(15deg) scale(1.1);
}

.card-icon.personal { background: linear-gradient(135deg, var(--blue) 0%, #2563eb 100%); }
.card-icon.security { background: linear-gradient(135deg, var(--accent-amber) 0%, #d97706 100%); }
.card-icon.password { background: linear-gradient(135deg, var(--purple) 0%, #7c3aed 100%); }

.card-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: var(--text-color);
}

.form-group {
    margin-bottom: 20px;
    position: relative;
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

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: #f9fafb;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-green);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
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

.submit-btn {
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
    width: 100%;
    margin-top: 20px;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    letter-spacing: 1px;
}

.submit-btn:active:not(:disabled) {
    transform: translateY(-1px);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.submit-btn i {
    transition: all 0.3s ease;
}

.submit-btn:hover:not(:disabled) i {
    transform: rotate(20deg);
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

@media (max-width: 1199px) {
    .profile-cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 991px) {
    .profile-cards-grid {
        grid-template-columns: 1fr;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}

@media (max-width: 767px) {
    .profile-update-container {
        padding: 15px;
    }

    .profile-header-card {
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

    .avatar-icon {
        font-size: 36px;
    }

    .header-text h1 {
        font-size: 24px;
    }

    .profile-card {
        padding: 20px;
    }

    .card-header {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }

    .card-icon {
        width: 40px;
        height: 40px;
        font-size: 20px;
    }
}

@media (max-width: 480px) {
    .profile-update-container {
        padding: 10px;
    }

    .profile-header-card {
        padding: 15px;
    }

    .header-text h1 {
        font-size: 20px;
    }

    .profile-card {
        padding: 15px;
    }

    .form-control {
        padding: 10px 12px;
    }

    .submit-btn {
        padding: 12px 20px;
        font-size: 14px;
    }
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
</style>
@endsection

@section('content')
<div class="profile-update-container">
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

    <div class="profile-header-card">
        <div class="header-content">
            <div class="profile-avatar">
                <i class="fas fa-user-cog avatar-icon"></i>
            </div>
            <div class="header-text">
                <h1>Profile Details Update</h1>
                <p>Manage your admin profile information and security settings</p>
            </div>
        </div>
    </div>

    <div class="profile-cards-grid">
        <div class="profile-card">
            <div class="card-header">
                <div class="card-icon personal">
                    <i class="fas fa-user-edit"></i>
                </div>
                <h3>Personal Information</h3>
            </div>

            <form action="{{ route('admin.profile.updateDetails') }}" method="POST" id="personalForm">
                @csrf

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i> Full Name
                    </label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', Auth::user()->name ?? '') }}"
                           placeholder="Enter your full name" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-at"></i> Email Address
                        </label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', Auth::user()->email ?? '') }}"
                               placeholder="Enter your email" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone"></i> Phone Number
                        </label>
                        <input type="tel" name="phone" class="form-control"
                               value="{{ old('phone', Auth::user()->phone ?? '') }}"
                               placeholder="Enter your phone number">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user-tag"></i> Username
                    </label>
                    <input type="text" name="username" class="form-control"
                           value="{{ old('username', Auth::user()->username ?? '') }}"
                           placeholder="Enter username" required>
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        This username will be used for login
                    </div>
                </div>

                <button type="submit" class="submit-btn" id="personalSubmit">
                    <i class="fas fa-save"></i> Update Personal Info
                </button>
            </form>
        </div>

        <div class="profile-card">
            <div class="card-header">
                <div class="card-icon security">
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
                               class="form-control" placeholder="Enter current password" required>
                        <i class="fas fa-eye password-toggle" id="toggleCurrentPassword"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-fingerprint"></i> New Password
                    </label>
                    <div class="password-container">
                        <input type="password" name="new_password" id="new_password"
                               class="form-control" placeholder="Enter new password"
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
                </div>

                <div class="form-group">
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

                <button type="submit" class="submit-btn" id="securitySubmit" disabled>
                    <i class="fas fa-shield-alt"></i> Update Password
                </button>
            </form>
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

    const personalForm = document.getElementById('personalForm');
    const securityForm = document.getElementById('securityForm');

    personalForm.addEventListener('submit', function(e) {
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

    const cards = document.querySelectorAll('.profile-card');
    cards.forEach((card, index) => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-8px) scale(1.02)';
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0) scale(1)';
        });
    });
});

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
        
        submitBtn.disabled = true;
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

    const confirmPassword = document.getElementById('confirm_password').value;
    submitBtn.disabled = !result.isValid || (confirmPassword && password !== confirmPassword);

    checkPasswordMatch();
}

function showPasswordGuidelines() {
    Swal.fire({
        title: 'Security Guidelines',
        html: `
            <div style="text-align: left; padding: 15px;">
                <h4 style="color: var(--primary-green); margin-bottom: 15px;"><i class="fas fa-shield-alt"></i> Password Best Practices</h4>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 10px; padding-left: 25px; position: relative;">
                        <i class="fas fa-check-circle" style="color: #10B981; position: absolute; left: 0;"></i>
                        Use at least 12 characters for better security
                    </li>
                    <li style="margin-bottom: 10px; padding-left: 25px; position: relative;">
                        <i class="fas fa-check-circle" style="color: #10B981; position: absolute; left: 0;"></i>
                        Combine uppercase, lowercase, numbers, and symbols
                    </li>
                    <li style="margin-bottom: 10px; padding-left: 25px; position: relative;">
                        <i class="fas fa-check-circle" style="color: #10B981; position: absolute; left: 0;"></i>
                        Avoid using personal information
                    </li>
                    <li style="margin-bottom: 10px; padding-left: 25px; position: relative;">
                        <i class="fas fa-check-circle" style="color: #10B981; position: absolute; left: 0;"></i>
                        Change password every 90 days
                    </li>
                    <li style="margin-bottom: 10px; padding-left: 25px; position: relative;">
                        <i class="fas fa-check-circle" style="color: #10B981; position: absolute; left: 0;"></i>
                        Never share your password with anyone
                    </li>
                    <li style="margin-bottom: 10px; padding-left: 25px; position: relative;">
                        <i class="fas fa-check-circle" style="color: #10B981; position: absolute; left: 0;"></i>
                        Use different passwords for different accounts
                    </li>
                </ul>
            </div>
        `,
        width: 600,
        padding: '30px',
        background: 'var(--card-bg)',
        color: 'var(--text-color)',
        showCloseButton: true,
        showConfirmButton: true,
        confirmButtonText: 'Got it!',
        confirmButtonColor: '#10B981',
        customClass: {
            popup: 'animated__animated animated__fadeIn'
        }
    });
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
