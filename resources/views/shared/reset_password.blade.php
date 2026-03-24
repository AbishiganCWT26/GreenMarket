@extends('layouts.public_master')

@section('title','Reset Password')

@section('styles')
<script src="{{ asset('js/form-validation.js') }}"></script>
@endsection

@section('content')
<div style="max-width:480px;margin:24px auto;">
    <h2>Reset Password</h2>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ url('/password/reset') }}" id="resetForm">
        @csrf
        <input type="hidden" name="token" value="{{ $token ?? '' }}">

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required value="{{ $email ?? old('email') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">New password</label>
            <div class="password-container" style="position: relative;">
                <input type="password" name="password" id="password" class="form-control" required oninput="updateStrength(this.value)">
                <i class="fa-regular fa-eye password-toggle" id="togglePassword" style="position: absolute; right: 10px; top: 10px; cursor: pointer;" onclick="toggleVisibility('password', 'togglePassword')"></i>
            </div>
            <div class="strength-meter mt-2">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small>Strength: <span id="strength-text">None</span></small>
                </div>
                <div class="progress" style="height: 5px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                    <div id="strength-bar" class="progress-bar" style="width: 0%; height: 100%; transition: width 0.3s;"></div>
                </div>
            </div>
            <div class="requirements mt-3">
                <h6 class="mb-2" style="font-size: 0.9rem;">Requirements:</h6>
                <ul class="list-unstyled mb-0" style="font-size: 0.8rem; display: grid; grid-template-columns: 1fr 1fr; gap: 5px; padding: 0;">
                    <li id="rule-length" class="text-danger"><i class="fas fa-times me-1"></i> 8+ characters</li>
                    <li id="rule-number" class="text-danger"><i class="fas fa-times me-1"></i> 1+ number</li>
                    <li id="rule-capital" class="text-danger"><i class="fas fa-times me-1"></i> Uppercase</li>
                    <li id="rule-lowercase" class="text-danger"><i class="fas fa-times me-1"></i> Lowercase</li>
                    <li id="rule-special" class="text-danger"><i class="fas fa-times me-1"></i> Special char</li>
                    <li id="rule-no-space" class="text-danger"><i class="fas fa-times me-1"></i> No spaces</li>
                    <li id="rule-no-repeat" class="text-danger"><i class="fas fa-times me-1"></i> No repeated</li>
                    <li id="rule-no-sequence" class="text-danger"><i class="fas fa-times me-1"></i> No sequence</li>
                    <li id="rule-not-common" class="text-danger"><i class="fas fa-times me-1"></i> Not common</li>
                    <li id="rule-no-links" class="text-danger"><i class="fas fa-times me-1"></i> No links</li>
                    <li id="rule-no-personal" class="text-danger"><i class="fas fa-times me-1"></i> No Personal Info</li>
                </ul>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm password</label>
            <div class="password-container" style="position: relative;">
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                <i class="fa-regular fa-eye password-toggle" id="toggleConfirm" style="position: absolute; right: 10px; top: 10px; cursor: pointer;" onclick="toggleVisibility('password_confirmation', 'toggleConfirm')"></i>
            </div>
            <div id="match-status" class="mt-1" style="font-size: 0.8rem;"></div>
        </div>

        <div style="margin-top:20px;">
            <button class="btn btn-success w-100" type="submit" id="submitBtn" disabled>Reset password</button>
        </div>
    </form>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .text-success { color: #10B981 !important; }
    .text-danger { color: #ef4444 !important; }
    .list-unstyled { list-style: none; }
    .form-control { width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px; }
    .btn-success { background: #10B981; color: white; border: none; padding: 10px; cursor: pointer; border-radius: 4px; }
    .btn-success:disabled { background: #9ca3af; cursor: not-allowed; }
</style>

<script>
    function toggleVisibility(id, iconId) {
        const el = document.getElementById(id);
        const icon = document.getElementById(iconId);
        if (el.type === 'password') {
            el.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            el.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function calculateStrength(password) {
        const result = validateAdvancedPassword(password, {
            email: document.querySelector('input[name="email"]').value
        });

        Object.keys(result.rules).forEach(ruleId => {
            const el = document.getElementById('rule-' + ruleId);
            if (el) {
                const isValid = result.rules[ruleId];
                el.className = isValid ? 'text-success' : 'text-danger';
                el.querySelector('i').className = isValid ? 'fas fa-check me-1' : 'fas fa-times me-1';
            }
        });

        return result;
    }

    function updateStrength(password) {
        const result = calculateStrength(password);
        const strengthText = document.getElementById('strength-text');
        const strengthBar = document.getElementById('strength-bar');
        
        strengthText.textContent = result.strengthText;
        strengthText.style.color = result.color;
        strengthBar.style.backgroundColor = result.color;
        strengthBar.style.width = result.percent + '%';
        
        checkMatch();
    }

    function checkMatch() {
        const pass = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirmation').value;
        const status = document.getElementById('match-status');
        const submitBtn = document.getElementById('submitBtn');
        const result = calculateStrength(pass);

        if (confirm) {
            if (pass === confirm) {
                status.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Passwords match</span>';
            } else {
                status.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> Passwords mismatch</span>';
            }
        } else {
            status.innerHTML = '';
        }

        submitBtn.disabled = (result.isValid === false || pass !== confirm || !confirm);
    }

    document.getElementById('password_confirmation').addEventListener('input', checkMatch);
</script>
@endsection
