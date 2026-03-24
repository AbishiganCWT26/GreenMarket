@extends('layouts.app')

@section('title', 'Account Settings')

@section('styles')
<style>
    .strength-bar { height: 5px; background: #e2e8f0; border-radius: 3px; margin-top: 5px; overflow: hidden; }
    .strength-fill { height: 100%; width: 0; transition: all 0.3s; }
    .requirements li { margin-bottom: 5px; transition: all 0.3s; font-size: 0.85rem; list-style: none; }
    .text-success { color: #10B981 !important; }
    .text-danger { color: #ef4444 !important; }
    .password-container { position: relative; }
    .password-toggle { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; }
</style>
@endsection

@section('content')

<h2><i class="fas fa-cog"></i> Account Settings</h2>

<div class="card-panel">

    <form action="{{ route('buyer.profile.password') }}" method="POST" id="passwordForm">
        @csrf

        <h4>Change Password</h4>
        <hr>


        <div class="mb-3">
            <label>New Password</label>
            <div class="password-container">
                <input type="password" name="new_password" id="new_password" class="form-control" required>
                <i class="fa-regular fa-eye password-toggle" id="password-toggle-icon" onclick="togglePasswordVisibility('new_password', 'password-toggle-icon')"></i>
            </div>
            <div class="password-strength mt-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small>Strength: <span id="strengthText">None</span></small>
                </div>
                <div class="strength-bar" id="strengthBar">
                    <div class="strength-fill" style="width: 0%; height: 100%; transition: width 0.3s;"></div>
                </div>
            </div>
            <div class="requirements mt-3">
                <h6 class="mb-2" style="font-size: 0.9rem;">Requirements:</h6>
                <ul class="list-unstyled mb-0" style="font-size: 0.8rem; display: grid; grid-template-columns: 1fr 1fr; gap: 5px;">
                    <li id="rule-length" class="text-danger"><i class="fas fa-times me-1"></i> 8+ chars</li>
                    <li id="rule-number" class="text-danger"><i class="fas fa-times me-1"></i> Number</li>
                    <li id="rule-capital" class="text-danger"><i class="fas fa-times me-1"></i> Capital</li>
                    <li id="rule-lowercase" class="text-danger"><i class="fas fa-times me-1"></i> Lowercase</li>
                    <li id="rule-special" class="text-danger"><i class="fas fa-times me-1"></i> Special</li>
                    <li id="rule-no-space" class="text-danger"><i class="fas fa-times me-1"></i> No spaces</li>
                    <li id="rule-no-repeat" class="text-danger"><i class="fas fa-times me-1"></i> No repeat</li>
                    <li id="rule-no-sequence" class="text-danger"><i class="fas fa-times me-1"></i> No sequence</li>
                    <li id="rule-not-common" class="text-danger"><i class="fas fa-times me-1"></i> Not common</li>
                    <li id="rule-no-links" class="text-danger"><i class="fas fa-times me-1"></i> No links</li>
                    <li id="rule-no-personal" class="text-danger"><i class="fas fa-times me-1"></i> No Personal Info</li>
                </ul>
            </div>
        </div>

        <div class="mb-3">
            <label>Confirm Password</label>
            <div class="password-container">
                <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
                <i class="fa-regular fa-eye password-toggle" id="confirm-password-toggle-icon" onclick="togglePasswordVisibility('new_password_confirmation', 'confirm-password-toggle-icon')"></i>
            </div>
            <div id="passwordMatch" class="mt-3">
                <small class="text-success d-none">
                    <i class="fas fa-check-circle"></i> Passwords match
                </small>
                <small class="text-danger d-none">
                    <i class="fas fa-times-circle"></i> Passwords don't match
                </small>
            </div>
        </div>

        <button type="submit" class="btn btn-success mt-3" id="submitBtn" disabled>
            <i class="fas fa-key"></i> Update Password
        </button>
    </form>

</div>

@endsection

@section('scripts')
<script src="{{ asset('js/form-validation.js') }}"></script>
<script>
    function togglePasswordVisibility(fieldId, iconId) {
        const passwordField = document.getElementById(fieldId);
        const toggleIcon = document.getElementById(iconId);
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const password = document.getElementById('new_password');
        const confirmPassword = document.getElementById('new_password_confirmation');
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        const submitBtn = document.getElementById('submitBtn');

        function validatePasswordStrength() {
            const passwordValue = password.value;
            if (!passwordValue) {
                strengthText.textContent = 'None';
                strengthText.style.color = '#cbd5e1';
                strengthBar.querySelector('.strength-fill').style.width = '0%';
                return false;
            }

            const result = validateAdvancedPassword(passwordValue, {
                username: "{{ Auth::user()->username }}",
                email: "{{ Auth::user()->email }}"
            });

            updatePasswordRuleFeedback(result);

            strengthText.textContent = result.strengthText;
            strengthText.style.color = result.color;
            const fill = strengthBar.querySelector('.strength-fill');
            fill.style.backgroundColor = result.color;
            fill.style.width = result.percent + '%';

            return result.isValid;
        }

        function validatePasswordMatch() {
            const match = password.value === confirmPassword.value;
            const matchIndicator = document.getElementById('passwordMatch');
            const success = matchIndicator.querySelector('.text-success');
            const error = matchIndicator.querySelector('.text-danger');
            
            if (password.value && confirmPassword.value) {
                if (match) {
                    success.classList.remove('d-none');
                    error.classList.add('d-none');
                } else {
                    success.classList.add('d-none');
                    error.classList.remove('d-none');
                }
            } else {
                success.classList.add('d-none');
                error.classList.add('d-none');
            }
            return match;
        }

        function updateSubmitButton() {
            const isStrengthValid = validatePasswordStrength();
            const isMatchValid = validatePasswordMatch();
            submitBtn.disabled = !(isStrengthValid && isMatchValid);
        }

        password.addEventListener('input', updateSubmitButton);
        confirmPassword.addEventListener('input', updateSubmitButton);
    });
</script>
@endsection
