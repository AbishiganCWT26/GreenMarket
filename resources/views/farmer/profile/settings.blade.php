@extends('farmer.layouts.farmer_master')

@section('title', 'Security Settings')
@section('page-title', 'Security Settings')

@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/farmer/settings.css') }}">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="{{ asset('js/form-validation.js') }}"></script>
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

                            <div class="password-requirements-grid mt-3">
                                <style>
                                    .password-requirements-grid {
                                        display: grid;
                                        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                                        gap: 10px;
                                        background: #f8fafc;
                                        padding: 15px;
                                        border-radius: 8px;
                                        border: 1px solid #e2e8f0;
                                    }
                                    .rule-item {
                                        font-size: 0.75rem;
                                        display: flex;
                                        align-items: center;
                                        gap: 8px;
                                        color: #64748b;
                                        transition: all 0.3s ease;
                                    }
                                    .rule-item.valid { color: #10B981; }
                                    .rule-item.invalid { color: #ef4444; }
                                    .rule-item i { font-size: 0.8rem; width: 12px; }
                                </style>
                                <div class="rule-item" id="rule-length"><i class="fas fa-circle"></i> 8+ Characters</div>
                                <div class="rule-item" id="rule-number"><i class="fas fa-circle"></i> 1+ Number</div>
                                <div class="rule-item" id="rule-capital"><i class="fas fa-circle"></i> 1+ Capital</div>
                                <div class="rule-item" id="rule-lowercase"><i class="fas fa-circle"></i> 1+ Lowercase</div>
                                <div class="rule-item" id="rule-special"><i class="fas fa-circle"></i> 1+ Special</div>
                                <div class="rule-item" id="rule-no-space"><i class="fas fa-circle"></i> No Spaces</div>
                                <div class="rule-item" id="rule-no-repeat"><i class="fas fa-circle"></i> No 3x Repeat</div>
                                <div class="rule-item" id="rule-no-sequence"><i class="fas fa-circle"></i> No Sequence</div>
                                <div class="rule-item" id="rule-not-common"><i class="fas fa-circle"></i> Not Common</div>
                                <div class="rule-item" id="rule-no-links"><i class="fas fa-circle"></i> No Links</div>
                                <div class="rule-item" id="rule-no-personal"><i class="fas fa-circle"></i> No Personal Info</div>
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
        const result = validateAdvancedPassword(password, {
            username: "{{ Auth::user()->username }}",
            email: "{{ Auth::user()->email }}"
        });

        const label = document.getElementById('strength-label');
        const fill = document.getElementById('meter-fill');
        const btn = document.getElementById('submitBtn');

        // Update overall meter
        label.textContent = result.strengthText;
        label.style.color = result.color;
        fill.style.width = result.percent + '%';
        fill.style.backgroundColor = result.color;

        // Update individual rules
        updatePasswordRuleFeedback(result);

        currentStrength = result.isValid ? 5 : 0; // Maintain internal logic for submit check
        btn.disabled = !result.isValid;
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
