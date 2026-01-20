@extends('farmer.layouts.farmer_master')

@section('title', 'Security Settings')
@section('page-title', 'Security Settings')

@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/farmer/settings.css') }}">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<div class="settings-wrapper">
	<div class="settings-container">
		<header class="settings-header">
			<div class="header-icon-box">
				<i class="fas fa-user-shield"></i>
			</div>
			<div class="header-content">
				<h1>Security Center</h1>
				<p>Keep your account safe by managing your credentials</p>
			</div>
		</header>
            <div class="security-main-card">
                <div class="info-banner">
                    <div class="info-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="info-text">
                        <h3>Secure Password Tips</h3>
                        <p>Must be 8+ characters with uppercase, lowercase, numbers, and symbols (@$!%*#?&). <strong>Submission is only allowed when strength is "Strong".</strong></p>
                    </div>
                </div>

                <form action="{{ route('farmer.profile.settings.update-password') }}" method="POST" id="securityForm" class="security-form">
                    @csrf

                    <div class="form-section">
                        <div class="input-group">
                            <label for="current_password">
                                <i class="fas fa-unlock-alt"></i> Current Password
                            </label>
                            <div class="field-wrapper">
                                <input type="password" id="current_password" name="current_password" class="form-input" placeholder="Enter current password" required>
                                <button type="button" class="eye-toggle" onclick="toggleView('current_password', 'icon1')">
                                    <i class="far fa-eye" id="icon1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <hr class="form-divider">

                    <div class="input-grid">
                        <div class="input-group">
                            <label for="new_password">
                                <i class="fas fa-fingerprint"></i> New Password
                            </label>
                            <div class="field-wrapper">
                                <input type="password" id="new_password" name="new_password" class="form-input" placeholder="••••••••" oninput="updateStrength(this.value)" required>
                                <button type="button" class="eye-toggle" onclick="toggleView('new_password', 'icon2')">
                                    <i class="far fa-eye" id="icon2"></i>
                                </button>
                            </div>

                            <div class="strength-meter-box">
                                <div class="strength-meta">
                                    <span>Strength: <span id="strength-label">None</span></span>
                                </div>
                                <div class="meter-bg">
                                    <div id="meter-fill" class="meter-fill"></div>
                                </div>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="new_password_confirmation">
                                <i class="fas fa-check-double"></i> Confirm New Password
                            </label>
                            <div class="field-wrapper">
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-input" placeholder="••••••••" required>
                                <button type="button" class="eye-toggle" onclick="toggleView('new_password_confirmation', 'icon3')">
                                    <i class="far fa-eye" id="icon3"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="submit-btn" id="submitBtn" disabled>
                            <span class="btn-text">Update Security Credentials</span>
                            <i class="fas fa-shield-alt"></i>
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
    let currentStrength = 0;

    function toggleView(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'far fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'far fa-eye';
        }
    }

    function updateStrength(password) {
        let score = 0;
        const label = document.getElementById('strength-label');
        const fill = document.getElementById('meter-fill');
        const btn = document.getElementById('submitBtn');

        if (password.length >= 8) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[@$!%*#?&]/.test(password)) score++;

        currentStrength = score;

        const config = [
            { text: 'None', color: '#cbd5e1', width: '0%' },
            { text: 'Very Weak', color: '#ef4444', width: '20%' },
            { text: 'Weak', color: '#f59e0b', width: '40%' },
            { text: 'Fair', color: '#3b82f6', width: '60%' },
            { text: 'Good', color: '#8b5cf6', width: '80%' },
            { text: 'Strong', color: '#10B981', width: '100%' }
        ];

        const state = config[score];
        label.textContent = state.text;
        label.style.color = state.color;
        fill.style.width = state.width;
        fill.style.backgroundColor = state.color;

        btn.disabled = score < 5;
    }

    document.getElementById('securityForm').addEventListener('submit', async function(e)
    {
        e.preventDefault();

        if(currentStrength < 5) {
            toast('Password must be Strong to update', 'error');
            return;
        }

        const btn = document.getElementById('submitBtn');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Processing...';
        btn.disabled = true;

        try {
            // Debug: Log what's being sent
            const formData = new FormData(this);
            console.log('FormData entries:');
            for (let [key, value] of formData.entries()) {
                console.log(key, '=', value);
            }

            const response = await fetch("{{ route('farmer.profile.settings.update-password') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    // Remove Content-Type header when using FormData
                    // Let the browser set it automatically with boundary
                },
                body: formData
            });

            const data = await response.json();
            console.log('Response:', data);

            if (response.ok && data.success) {
                toast(data.message || 'Security updated successfully!', 'success');
                this.reset();
                updateStrength('');
            } else {
                const err = data.errors ? Object.values(data.errors).flat().join('<br>') : data.message;
                throw new Error(err || 'Update failed');
            }
        } catch (error) {
            console.error('Error:', error);
            toast(error.message, 'error');
        } finally {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    });

    function toast(msg, icon) {
        Swal.fire({
            icon: icon,
            title: icon === 'success' ? 'Success' : 'Error',
            html: msg,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            background: icon === 'success' ? '#10B981' : '#ef4444',
            color: '#fff',
            iconColor: '#fff'
        });
    }
</script>
@endsection
