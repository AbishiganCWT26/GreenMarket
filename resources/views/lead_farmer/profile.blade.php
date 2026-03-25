@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'My Profile')

@section('page-title', 'My Profile')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/lead_farmer/Profile.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
    <div class="profile-wrap">
        <div class="profile-container">
            <div class="profile-card">
                <div class="profile-head">
                    <div class="profile-avatar" onclick="window.location.href='{{ route('lf.profile.photo') }}'">
                        <img src="{{ Auth::user()->profile_photo ? asset('uploads/profile_pictures/' . Auth::user()->profile_photo) : asset('assets/images/default-avatar.png') }}" alt="Profile" id="profilePreview">
                        <div class="avatar-overlay">
                            <i class="fas fa-camera"></i>
                        </div>
                    </div>
                    <div class="profile-info">
                        <h2>{{ $leadFarmer->name ?? Auth::user()->username }}</h2>
                        <p><i class="fas fa-envelope"></i> {{ Auth::user()->email ?? 'Not set' }}</p>
                        <p><i class="fas fa-phone"></i> {{ $leadFarmer->primary_mobile ?? 'Not set' }}</p>
                    </div>
                </div>

                <div class="profile-stats">
                    <div class="stat-item">
                        <i class="fas fa-users"></i>
                        <div>
                            <span class="stat-value">{{ $leadFarmer->group_name ?? 'N/A' }}</span>
                            <span class="stat-label">Group Name</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-hashtag"></i>
                        <div>
                            <span class="stat-value">{{ $leadFarmer->group_number ?? 'N/A' }}</span>
                            <span class="stat-label">Group Number</span>
                        </div>
                    </div>
                </div>

                <div class="profile-tabs">
                    <div class="tabs-nav">
                        <button class="tab-btn active" data-tab="personal">
                            <i class="fas fa-user-edit"></i>
                            <span>Edit Profile</span>
                        </button>
                        <button class="tab-btn" data-tab="security">
                            <i class="fas fa-shield-alt"></i>
                            <span>Security</span>
                        </button>
                    </div>

                    <div class="tabs-content">
                        <div class="tab-pane active" id="personal">
                            <form action="{{ route('lf.profile.update') }}" method="POST" id="profileForm">
                                @csrf
                                @method('PUT')

                                <div class="form-section">
                                    <h3><i class="fas fa-id-card"></i> Personal Information</h3>
                                    <div class="form-row">
                                        <div class="form-field">
                                            <label><i class="fas fa-user"></i> Full Name <span class="required">*</span></label>
                                            <input type="text" name="name" value="{{ old('name', $leadFarmer->name) }}" required>
                                            @error('name')<span class="error">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="form-field">
                                            <label><i class="fas fa-id-card"></i> NIC Number</label>
                                            <div class="readonly-field">
                                                <input type="text" name="nic_no" value="{{ old('nic_no', $leadFarmer->nic_no) }}" readonly>
                                                <i class="fas fa-lock"></i>
                                            </div>
                                            @error('nic_no')<span class="error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h3><i class="fas fa-phone-alt"></i> Contact Details</h3>
                                    <div class="form-row">
                                        <div class="form-field">
                                            <label><i class="fas fa-mobile-alt"></i> Primary Mobile <span class="required">*</span></label>
                                            <input type="tel" name="primary_mobile" value="{{ old('primary_mobile', $leadFarmer->primary_mobile) }}" maxlength="10" required>
                                            @error('primary_mobile')<span class="error">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="form-field">
                                            <label><i class="fab fa-whatsapp"></i> WhatsApp Number</label>
                                            <input type="tel" name="whatsapp_number" value="{{ old('whatsapp_number', $leadFarmer->whatsapp_number) }}" maxlength="10">
                                            @error('whatsapp_number')<span class="error">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="form-field full-width">
                                            <label><i class="fas fa-envelope"></i> Email Address</label>
                                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}">
                                            @error('email')<span class="error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h3><i class="fas fa-map-marker-alt"></i> Location Details</h3>
                                    <div class="form-row">
                                        <div class="form-field full-width">
                                            <label><i class="fas fa-home"></i> Residential Address <span class="required">*</span></label>
                                            <textarea name="residential_address" rows="2" required>{{ old('residential_address', $leadFarmer->residential_address) }}</textarea>
                                            @error('residential_address')<span class="error">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="form-field">
                                            <label><i class="fas fa-map-marker-alt"></i> District</label>
                                            <div class="readonly-field">
                                                <input type="text" name="district" value="{{ old('district', $leadFarmer->district) }}" readonly>
                                                <i class="fas fa-lock"></i>
                                            </div>
                                        </div>
                                        <div class="form-field">
                                            <label><i class="fas fa-map-marked-alt"></i> Divisional Secretariat</label>
                                            <div class="readonly-field">
                                                <input type="text" name="divisional_secretariat" value="{{ old('divisional_secretariat', $leadFarmer->divisional_secretariat) }}" readonly>
                                                <i class="fas fa-lock"></i>
                                            </div>
                                        </div>
                                        <div class="form-field">
                                            <label><i class="fas fa-map-signs"></i> GN Division</label>
                                            <div class="readonly-field">
                                                <input type="text" name="grama_niladhari_division" value="{{ old('grama_niladhari_division', $leadFarmer->grama_niladhari_division) }}" readonly>
                                                <i class="fas fa-lock"></i>
                                            </div>
                                        </div>
                                        <div class="form-field">
                                            <label><i class="fas fa-barcode"></i> GN Code</label>
                                            <div class="readonly-field">
                                                <input type="text" name="gn_division_code" value="{{ old('gn_division_code', $leadFarmer->gn_division_code) }}" readonly>
                                                <i class="fas fa-lock"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h3><i class="fas fa-wallet"></i> Payment Details</h3>
                                    <div class="form-row">
                                        <div class="form-field">
                                            <label><i class="fas fa-user-tie"></i> Account Holder <span class="required">*</span></label>
                                            <input type="text" name="account_holder_name" value="{{ old('account_holder_name', $leadFarmer->account_holder_name) }}" required>
                                            @error('account_holder_name')<span class="error">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="form-field">
                                            <label><i class="fas fa-hashtag"></i> Account Number <span class="required">*</span></label>
                                            <input type="text" name="account_number" value="{{ old('account_number', $leadFarmer->account_number) }}" required>
                                            @error('account_number')<span class="error">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="form-field">
                                            <label><i class="fas fa-university"></i> Bank Name <span class="required">*</span></label>
                                            <input type="text" name="bank_name" value="{{ old('bank_name', $leadFarmer->bank_name) }}" required>
                                            @error('bank_name')<span class="error">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="form-field">
                                            <label><i class="fas fa-code-branch"></i> Bank Branch <span class="required">*</span></label>
                                            <input type="text" name="bank_branch" value="{{ old('bank_branch', $leadFarmer->bank_branch) }}" required>
                                            @error('bank_branch')<span class="error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="button" class="btn-reset" onclick="resetForm()">
                                        <i class="fas fa-undo-alt"></i>
                                        <span>Reset</span>
                                    </button>
                                    <button type="submit" class="btn-save">
                                        <i class="fas fa-save"></i>
                                        <span>Save Changes</span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane" id="security">
                            <div class="form-section">
                                <h3><i class="fas fa-key"></i> Password Management</h3>
                                <div class="security-info">
                                    <p><i class="fas fa-info-circle"></i> Change your password regularly to keep your account secure</p>
                                </div>
                                <button type="button" class="btn-password" onclick="showPasswordModal()">
                                    <i class="fas fa-lock"></i>
                                    <span>Change Password</span>
                                </button>
                            </div>

                            <div class="form-section">
                                <h3><i class="fas fa-user-tag"></i> Account Information</h3>
                                <div class="info-row">
                                    <div class="info-label">Username</div>
                                    <div class="info-value">{{ Auth::user()->username }}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Account Created</div>
                                    <div class="info-value">{{ Auth::user()->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="passwordModal">
        <div class="modal-container">
            <div class="modal-head">
                <h3><i class="fas fa-key"></i> Change Password</h3>
                <button class="modal-close" onclick="hidePasswordModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    @csrf
                    <div class="form-field">
                        <label><i class="fas fa-lock"></i> New Password <span class="required">*</span></label>
                        <div class="password-field">
                            <input type="password" name="new_password" id="newPassword" required>
                            <i class="fas fa-eye toggle-password" onclick="togglePassword('newPassword', this)"></i>
                        </div>
                    </div>
                    <div class="strength-meter">
                        <div class="strength-bar">
                            <div class="strength-progress" id="strengthProgress"></div>
                        </div>
                        <span class="strength-text" id="strengthText">Weak</span>
                    </div>
                    <div class="form-field">
                        <label><i class="fas fa-lock"></i> Confirm Password <span class="required">*</span></label>
                        <div class="password-field">
                            <input type="password" name="new_password_confirmation" id="confirmPassword" required>
                            <i class="fas fa-eye toggle-password" onclick="togglePassword('confirmPassword', this)"></i>
                        </div>
                        <span class="match-status" id="matchStatus"></span>
                    </div>
                    <div class="password-requirements">
                        <p><i class="fas fa-shield-alt"></i> Password must contain:</p>
                        <ul>
                            <li id="req-length"><i class="fas fa-times"></i> At least 8 characters</li>
                            <li id="req-number"><i class="fas fa-times"></i> At least 1 number</li>
                            <li id="req-upper"><i class="fas fa-times"></i> At least 1 uppercase letter</li>
                            <li id="req-lower"><i class="fas fa-times"></i> At least 1 lowercase letter</li>
                            <li id="req-special"><i class="fas fa-times"></i> At least 1 special character</li>
                        </ul>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn-cancel" onclick="hidePasswordModal()">
                            <i class="fas fa-times"></i>
                            <span>Cancel</span>
                        </button>
                        <button type="submit" class="btn-update" id="updatePasswordBtn" disabled>
                            <i class="fas fa-check"></i>
                            <span>Update</span>
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
            const tabBtns = document.querySelectorAll('.tab-btn');
            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    tabBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
                    document.getElementById(this.dataset.tab).classList.add('active');
                });
            });

            const profileForm = document.getElementById('profileForm');
            if (profileForm) {
                const originalData = new FormData(profileForm);
                window.resetForm = function() {
                    profileForm.reset();
                    showSweetAlert('info', 'Form Reset', 'All changes have been discarded');
                };

                profileForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    showSweetAlert('question', 'Update Profile', 'Are you sure you want to update your profile?').then(result => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            }

            const readonlyFields = document.querySelectorAll('.readonly-field');
            readonlyFields.forEach(field => {
                field.addEventListener('click', function() {
                    showSweetAlert('info', 'Restricted Field', 'Contact the Facilitator to edit this information');
                });
            });

            document.querySelectorAll('input[type="tel"]').forEach(input => {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);
                });
            });

            window.showPasswordModal = function() {
                document.getElementById('passwordModal').classList.add('active');
                document.body.classList.add('no-scroll');
            };

            window.hidePasswordModal = function() {
                document.getElementById('passwordModal').classList.remove('active');
                document.body.classList.remove('no-scroll');
                document.getElementById('changePasswordForm').reset();
                document.getElementById('updatePasswordBtn').disabled = true;
            };

            window.togglePassword = function(fieldId, icon) {
                const field = document.getElementById(fieldId);
                if (field.type === 'password') {
                    field.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    field.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            };

            const newPassword = document.getElementById('newPassword');
            const confirmPassword = document.getElementById('confirmPassword');
            const updateBtn = document.getElementById('updatePasswordBtn');
            const strengthProgress = document.getElementById('strengthProgress');
            const strengthText = document.getElementById('strengthText');

            function checkPasswordStrength(password) {
                let strength = 0;
                const checks = {
                    length: password.length >= 8,
                    number: /\d/.test(password),
                    upper: /[A-Z]/.test(password),
                    lower: /[a-z]/.test(password),
                    special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
                };

                const reqLength = document.getElementById('req-length');
                const reqNumber = document.getElementById('req-number');
                const reqUpper = document.getElementById('req-upper');
                const reqLower = document.getElementById('req-lower');
                const reqSpecial = document.getElementById('req-special');

                reqLength.className = checks.length ? 'valid' : 'invalid';
                reqNumber.className = checks.number ? 'valid' : 'invalid';
                reqUpper.className = checks.upper ? 'valid' : 'invalid';
                reqLower.className = checks.lower ? 'valid' : 'invalid';
                reqSpecial.className = checks.special ? 'valid' : 'invalid';

                reqLength.innerHTML = checks.length ? '<i class="fas fa-check"></i> At least 8 characters' : '<i class="fas fa-times"></i> At least 8 characters';
                reqNumber.innerHTML = checks.number ? '<i class="fas fa-check"></i> At least 1 number' : '<i class="fas fa-times"></i> At least 1 number';
                reqUpper.innerHTML = checks.upper ? '<i class="fas fa-check"></i> At least 1 uppercase letter' : '<i class="fas fa-times"></i> At least 1 uppercase letter';
                reqLower.innerHTML = checks.lower ? '<i class="fas fa-check"></i> At least 1 lowercase letter' : '<i class="fas fa-times"></i> At least 1 lowercase letter';
                reqSpecial.innerHTML = checks.special ? '<i class="fas fa-check"></i> At least 1 special character' : '<i class="fas fa-times"></i> At least 1 special character';

                if (checks.length) strength++;
                if (checks.number) strength++;
                if (checks.upper) strength++;
                if (checks.lower) strength++;
                if (checks.special) strength++;

                const percent = (strength / 5) * 100;
                strengthProgress.style.width = percent + '%';

                if (strength <= 2) {
                    strengthProgress.style.backgroundColor = '#ef4444';
                    strengthText.textContent = 'Weak';
                    strengthText.style.color = '#ef4444';
                } else if (strength <= 3) {
                    strengthProgress.style.backgroundColor = '#f59e0b';
                    strengthText.textContent = 'Medium';
                    strengthText.style.color = '#f59e0b';
                } else if (strength <= 4) {
                    strengthProgress.style.backgroundColor = '#10B981';
                    strengthText.textContent = 'Strong';
                    strengthText.style.color = '#10B981';
                } else {
                    strengthProgress.style.backgroundColor = '#10B981';
                    strengthText.textContent = 'Very Strong';
                    strengthText.style.color = '#10B981';
                }

                return strength === 5;
            }

            newPassword.addEventListener('input', function() {
                const isValid = checkPasswordStrength(this.value);
                checkPasswordMatch();
                updateBtn.disabled = !(isValid && confirmPassword.value === this.value);
            });

            function checkPasswordMatch() {
                const matchStatus = document.getElementById('matchStatus');
                if (confirmPassword.value) {
                    if (newPassword.value === confirmPassword.value) {
                        matchStatus.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match';
                        matchStatus.className = 'match-status success';
                        return true;
                    } else {
                        matchStatus.innerHTML = '<i class="fas fa-times-circle"></i> Passwords do not match';
                        matchStatus.className = 'match-status error';
                        return false;
                    }
                } else {
                    matchStatus.innerHTML = '';
                    return false;
                }
            }

            confirmPassword.addEventListener('input', function() {
                const matches = checkPasswordMatch();
                updateBtn.disabled = !(checkPasswordStrength(newPassword.value) && matches);
            });

            const passwordForm = document.getElementById('changePasswordForm');
            passwordForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const newPass = formData.get('new_password');
                const confirmPass = formData.get('new_password_confirmation');

                if (newPass !== confirmPass) {
                    showSweetAlert('error', 'Error', 'Passwords do not match');
                    return;
                }

                if (!checkPasswordStrength(newPass)) {
                    showSweetAlert('error', 'Error', 'Please meet all password requirements');
                    return;
                }

                showSweetAlert('question', 'Change Password', 'Are you sure you want to change your password?').then(result => {
                    if (result.isConfirmed) {
                        fetch('{{ route("lf.profile.update.password") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ new_password: newPass })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showSweetAlert('success', 'Success!', data.message).then(() => {
                                    hidePasswordModal();
                                    location.reload();
                                });
                            } else {
                                showSweetAlert('error', 'Error!', data.message);
                            }
                        })
                        .catch(() => {
                            showSweetAlert('error', 'Error!', 'Something went wrong. Please try again.');
                        });
                    }
                });
            });

            window.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal-overlay')) {
                    hidePasswordModal();
                }
            });

            window.showSweetAlert = function(type, title, message) {
                const icons = {
                    success: '{{ asset("assets/icons/success1.gif") }}',
                    error: '{{ asset("assets/icons/error1.gif") }}',
                    warning: '{{ asset("assets/icons/alert1.gif") }}',
                    info: '{{ asset("assets/icons/info1.gif") }}',
                    question: '{{ asset("assets/icons/question1.gif") }}'
                };

                const confirmColors = {
                    success: '#10B981',
                    error: '#ef4444',
                    warning: '#f59e0b',
                    info: '#3b82f6',
                    question: '#10B981'
                };

                return Swal.fire({
                    imageUrl: icons[type],
                    imageWidth: 60,
                    imageHeight: 60,
                    title: title,
                    text: message,
                    confirmButtonText: type === 'question' ? 'Yes, Proceed' : 'OK',
                    confirmButtonColor: confirmColors[type],
                    showCancelButton: type === 'question',
                    cancelButtonText: 'Cancel',
                    cancelButtonColor: '#6b7280',
                    background: '#ffffff',
                    color: '#0f1724',
                    customClass: {
                        popup: 'swal-compact',
                        title: 'swal-title',
                        htmlContainer: 'swal-text'
                    }
                });
            };

            @if(session('success'))
                showSweetAlert('success', 'Success!', '{{ session('success') }}');
            @endif

            @if(session('error'))
                showSweetAlert('error', 'Error!', '{{ session('error') }}');
            @endif

            @if($errors->any())
                showSweetAlert('error', 'Validation Error', '{!! implode('\\n', $errors->all()) !!}');
            @endif
        });
    </script>
@endsection