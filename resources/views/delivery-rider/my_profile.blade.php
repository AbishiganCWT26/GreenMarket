@extends('delivery-rider.layouts.delivery_rider_master')

@section('title', '| My Profile')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <link rel="stylesheet" href="{{ asset('css/delivery-rider/my-profile.css') }}">
@endsection

@section('page-title')
    <i class="fa-solid fa-user-gear text-primary me-2"></i> My Profile Settings
@endsection

@section('content')
<div class="profile-container container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <div class="profile-card profile-side card-premium text-center p-4">
                <div class="avatar-wrapper mb-4">
                    <div class="avatar-container position-relative d-inline-block">
                        @php
                            $defaultAvatar = asset('assets/images/Profiles/default-avatar.png');
                            $userPhoto = Auth::user()->profile_photo;
                            $photoSrc = ($userPhoto && $userPhoto !== 'default-avatar.png') ? asset('uploads/profile_pictures/' . $userPhoto) . '?t=' . time() : $defaultAvatar;
                        @endphp
                        <img src="{{ $photoSrc }}"
                             id="photo-preview"
                             onerror="this.src='{{ $defaultAvatar }}'">
                        
                        <div class="avatar-actions">
                            <button class="avatar-edit-btn" onclick="document.getElementById('profile_photo').click()" title="Change Photo">
                                <i class="fa-solid fa-camera"></i>
                            </button>
                            @if($userPhoto && $userPhoto !== 'default-avatar.png')
                            <button class="avatar-remove-btn" onclick="removeProfilePhoto()" title="Remove Photo">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                    <h4 class="fw-bold mt-3 mb-1">{{ $rider->name ?? 'Rider Name' }}</h4>
                    <span class="badge bg-primary px-3 py-2 mt-1">Delivery Partner</span>
                    
                    <form action="{{ route('delivery-rider.profile.photo') }}" method="POST" enctype="multipart/form-data" id="photoForm" class="mt-3">
                        @csrf
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/jpeg,image/png,image/webp,image/heic,image/heif" hidden>
                        <input type="hidden" name="crop_x" id="crop_x">
                        <input type="hidden" name="crop_y" id="crop_y">
                        <input type="hidden" name="crop_width" id="crop_width">
                        <input type="hidden" name="crop_height" id="crop_height">
                        <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm" id="uploadBtn" style="display: none;">
                            <i class="fa-solid fa-cloud-arrow-up me-1"></i> Save Photo
                        </button>
                    </form>
                </div>

                <div class="info-list text-start mt-4 border-top pt-3">
                    <div class="info-row d-flex align-items-center mb-3">
                        <div class="info-icon bg-light text-primary rounded p-2 me-3">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <div class="info-content">
                            <span class="text-secondary d-block" style="font-size: 0.75rem;">NIC Number</span>
                            <span class="fw-bold text-dark">{{ $rider->nic_no ?? 'Not Provided' }}</span>
                        </div>
                    </div>
                    <div class="info-row d-flex align-items-center mb-3">
                        <div class="info-icon bg-light text-primary rounded p-2 me-3">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                        <div class="info-content">
                            <span class="text-secondary d-block" style="font-size: 0.75rem;">Registered At</span>
                            <span class="fw-bold text-dark">{{ $rider->created_at ? $rider->created_at->format('Y-m-d') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <button class="password-change-btn mt-3" onclick="changePassword()">
                    <i class="fa-solid fa-lock me-1 text-danger"></i> Change Password
                </button>
            </div>
        </div>

        <!-- Main Column -->
        <div class="col-lg-8">
            <div class="profile-card profile-main-content card-premium p-4">
                <div class="card-header border-bottom pb-3 mb-4 d-flex align-items-center">
                    <i class="fa-regular fa-pen-to-square text-primary fs-4 me-2"></i>
                    <h5 class="fw-bold mb-0">Personal & Vehicle Information</h5>
                </div>

                <form action="{{ route('delivery-rider.profile.update') }}" method="POST" id="profileForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-field">
                                <label class="form-label fw-semibold text-secondary"><i class="fa-regular fa-user me-1"></i> Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $rider->name ?? '' }}" placeholder="Enter full name" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-field">
                                <label class="form-label fw-semibold text-secondary"><i class="fa-solid fa-user-tag me-1"></i> Username</label>
                                <div class="input-group">
                                    <input type="text" name="username" id="username" class="form-control" value="{{ Auth::user()->username ?? '' }}" placeholder="Enter username" required>
                                    <button type="button" class="btn btn-outline-primary" id="verify_username_btn" style="display: none;" onclick="sendOTP('username')">Verify</button>
                                </div>
                                <div id="username_status" class="verification-status mt-1 text-success" style="font-size: 0.8rem;">
                                    <i class="fa-solid fa-circle-check"></i> Verified
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-field">
                                <label class="form-label fw-semibold text-secondary"><i class="fa-regular fa-envelope me-1"></i> Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ $rider->email ?? '' }}" placeholder="Enter email" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-field">
                                <label class="form-label fw-semibold text-secondary"><i class="fa-solid fa-phone me-1"></i> Primary Mobile</label>
                                <div class="input-group">
                                    <input type="text" name="primary_mobile" id="primary_mobile" class="form-control" value="{{ $rider->primary_mobile ?? '' }}" placeholder="Enter mobile number" required>
                                    <button type="button" class="btn btn-outline-primary" id="verify_primary_btn" style="display: none;" onclick="sendOTP('primary_mobile')">Verify</button>
                                </div>
                                <div id="primary_mobile_status" class="verification-status mt-1 text-success" style="font-size: 0.8rem;">
                                    <i class="fa-solid fa-circle-check"></i> Verified
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-field">
                                <label class="form-label fw-semibold text-secondary"><i class="fa-brands fa-whatsapp me-1"></i> WhatsApp Number</label>
                                <div class="input-group">
                                    <input type="text" name="whatsapp_number" id="whatsapp_number" class="form-control" value="{{ $rider->whatsapp_number ?? '' }}" placeholder="Enter WhatsApp number">
                                    <button type="button" class="btn btn-outline-primary" id="verify_whatsapp_btn" style="display: none;" onclick="sendOTP('whatsapp_number')">Verify</button>
                                </div>
                                <div id="whatsapp_number_status" class="verification-status mt-1 text-success" style="font-size: 0.8rem;">
                                    @if($rider->whatsapp_number)
                                        <i class="fa-solid fa-circle-check"></i> Verified
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-field">
                                <label class="form-label fw-semibold text-secondary"><i class="fa-solid fa-id-card me-1"></i> NIC Number <i class="fa-solid fa-lock text-muted ms-1" style="font-size: 0.65rem;" title="Only admin can edit"></i></label>
                                <input type="text" class="form-control bg-light" value="{{ $rider->nic_no ?? '' }}" disabled>
                                <small class="text-muted" style="font-size: 0.7rem;"><i class="fa-solid fa-shield-halved me-1"></i>Contact admin to change</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-field">
                                <label class="form-label fw-semibold text-secondary"><i class="fa-solid fa-car me-1"></i> Vehicle Type <i class="fa-solid fa-lock text-muted ms-1" style="font-size: 0.65rem;" title="Only admin can edit"></i></label>
                                <input type="text" class="form-control bg-light" value="{{ $rider->vehicle_type ?? 'Not Set' }}" disabled>
                                <small class="text-muted" style="font-size: 0.7rem;"><i class="fa-solid fa-shield-halved me-1"></i>Contact admin to change</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-field">
                                <label class="form-label fw-semibold text-secondary"><i class="fa-solid fa-hashtag me-1"></i> Vehicle Number <i class="fa-solid fa-lock text-muted ms-1" style="font-size: 0.65rem;" title="Only admin can edit"></i></label>
                                <input type="text" class="form-control bg-light" value="{{ $rider->vehicle_number ?? '' }}" disabled>
                                <small class="text-muted" style="font-size: 0.7rem;"><i class="fa-solid fa-shield-halved me-1"></i>Contact admin to change</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-field">
                                <label class="form-label fw-semibold text-secondary"><i class="fa-solid fa-weight-hanging me-1"></i> Max Capacity (KG) <i class="fa-solid fa-lock text-muted ms-1" style="font-size: 0.65rem;" title="Only admin can edit"></i></label>
                                <input type="text" class="form-control bg-light" value="{{ $rider->max_kg_capacity ?? '' }} KG" disabled>
                                <small class="text-muted" style="font-size: 0.7rem;"><i class="fa-solid fa-shield-halved me-1"></i>Contact admin to change</small>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-field">
                                <label class="form-label fw-semibold text-secondary"><i class="fa-solid fa-house me-1"></i> Residential Address</label>
                                <textarea name="residential_address" class="form-control" placeholder="Enter residential address" rows="3" required style="resize: none;">{{ $rider->residential_address ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-footer border-top pt-3 mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="fa-regular fa-floppy-disk me-1"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- Hidden form for removing photo -->
<form id="removePhotoForm" action="{{ route('delivery-rider.profile.photo.remove') }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script src="{{ asset('js/form-validation.js') }}"></script>
<script>
let cropper;
const profileInput = document.getElementById('profile_photo');
const photoForm = document.getElementById('photoForm');
const avatarContainer = document.querySelector('.avatar-container');

// Drag and drop logic
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    avatarContainer.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    avatarContainer.addEventListener(eventName, () => avatarContainer.classList.add('drag-active'), false);
});

['dragleave', 'drop'].forEach(eventName => {
    avatarContainer.addEventListener(eventName, () => avatarContainer.classList.remove('drag-active'), false);
});

avatarContainer.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    let dt = e.dataTransfer;
    let files = dt.files;
    if(files.length) {
        profileInput.files = files;
        handleFileSelect(files[0]);
    }
}

profileInput.addEventListener('change', function(e) {
    if (e.target.files.length > 0) {
        handleFileSelect(e.target.files[0]);
    }
});

function handleFileSelect(file) {
    const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/heic', 'image/heif'];
    if (!validTypes.includes(file.type) && !file.name.toLowerCase().match(/\.(jpg|jpeg|png|webp|heic)$/)) {
        Swal.fire({ title: 'Invalid File', text: 'Please select a valid image (JPG, PNG, WebP, HEIC).', icon: 'error', confirmButtonColor: '#10B981' });
        profileInput.value = '';
        return;
    }
    
    if (file.size > 5 * 1024 * 1024) {
        Swal.fire({ title: 'File Too Large', text: 'Maximum file size is 5MB.', icon: 'warning', confirmButtonColor: '#10B981' });
        profileInput.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        Swal.fire({
            title: 'Adjust Profile Photo',
            html: `
                <div class="img-container" style="max-height: 400px; width: 100%; margin: 0 auto; background-color: #f8fafc; overflow: hidden; border-radius: 8px;">
                    <img id="swalImageToCrop" src="${e.target.result}" alt="Picture" style="max-width: 100%; display: block;">
                </div>
                <p class="text-muted mt-2 mb-0" style="font-size: 0.75rem;">Scroll to zoom, drag to pan</p>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Save Photo',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#10B981',
            width: '600px',
            didOpen: () => {
                const image = document.getElementById('swalImageToCrop');
                cropper = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    restore: false,
                    guides: false,
                    center: false,
                    highlight: false,
                    cropBoxMovable: false,
                    cropBoxResizable: false,
                    toggleDragModeOnDblclick: false,
                });
            }
        }).then((result) => {
            if (result.isConfirmed && cropper) {
                const cropData = cropper.getData(true);
                document.getElementById('crop_x').value = cropData.x;
                document.getElementById('crop_y').value = cropData.y;
                document.getElementById('crop_width').value = cropData.width;
                document.getElementById('crop_height').value = cropData.height;
                
                cropper.destroy();
                cropper = null;
                
                Swal.fire({
                    title: 'Saving...',
                    text: 'Please wait while your profile photo is updated.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                const formData = new FormData(photoForm);
                fetch(photoForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async response => {
                    if (!response.ok) {
                        let errMsg = 'HTTP error ' + response.status;
                        try {
                            const errData = await response.json();
                            if (errData.message) errMsg = errData.message;
                        } catch(e) {}
                        throw new Error(errMsg);
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Profile photo updated successfully!',
                            confirmButtonColor: '#10B981'
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: data.message || 'Failed to upload photo',
                            confirmButtonColor: '#10B981'
                        });
                    }
                })
                .catch((err) => {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred: ' + err.message,
                        confirmButtonColor: '#10B981'
                    });
                });
            } else {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                profileInput.value = '';
            }
        });
    };
    reader.readAsDataURL(file);
}

function removeProfilePhoto() {
    Swal.fire({
        title: 'Remove Photo?',
        text: "Are you sure you want to remove your profile photo?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, remove it!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Removing...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            document.getElementById('removePhotoForm').submit();
        }
    });
}


document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const primaryMobile = document.getElementById('primary_mobile').value;
    const originalPrimary = "{{ $rider->primary_mobile }}";
    const whatsappNumber = document.getElementById('whatsapp_number').value;
    const originalWhatsapp = "{{ $rider->whatsapp_number }}";
    const usernameVal = document.getElementById('username').value;
    const originalUsername = "{{ Auth::user()->username }}";

    if (usernameVal !== originalUsername && !usernameVerified) {
        Swal.fire({
            icon: 'warning',
            title: 'Action Required',
            text: 'Please verify your new username first.',
            confirmButtonColor: '#10B981'
        });
        return;
    }

    if (primaryMobile !== originalPrimary && !primaryVerified) {
        Swal.fire({
            icon: 'warning',
            title: 'Action Required',
            text: 'Please verify your new primary mobile number first.',
            confirmButtonColor: '#10B981'
        });
        return;
    }

    if (whatsappNumber !== originalWhatsapp && whatsappNumber !== "" && !whatsappVerified) {
        Swal.fire({
            icon: 'warning',
            title: 'Action Required',
            text: 'Please verify your new WhatsApp number first.',
            confirmButtonColor: '#10B981'
        });
        return;
    }

    Swal.fire({
        title: 'Updating...',
        html: '<div class="spinner-border text-primary"></div>',
        showConfirmButton: false,
        allowOutsideClick: false
    });
    const formData = new FormData(this);
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message || 'Profile updated successfully!',
                confirmButtonColor: '#10B981'
            }).then(() => location.reload());
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Failed',
                text: data.message || 'Failed to update profile.',
                confirmButtonColor: '#10B981'
            });
        }
    })
    .catch(err => {
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while updating.',
            confirmButtonColor: '#10B981'
        });
    });
});

let primaryVerified = true;
let whatsappVerified = true;
let usernameVerified = true;

document.getElementById('username').addEventListener('input', function() {
    const current = this.value;
    const original = "{{ Auth::user()->username }}";
    const btn = document.getElementById('verify_username_btn');
    const status = document.getElementById('username_status');
    
    if (current !== original) {
        btn.style.display = 'block';
        status.style.display = 'none';
        usernameVerified = false;
    } else {
        btn.style.display = 'none';
        status.style.display = 'block';
        usernameVerified = true;
    }
});

document.getElementById('primary_mobile').addEventListener('input', function() {
    const current = this.value;
    const original = "{{ $rider->primary_mobile }}";
    const btn = document.getElementById('verify_primary_btn');
    const status = document.getElementById('primary_mobile_status');
    
    if (current !== original) {
        btn.style.display = 'block';
        status.style.display = 'none';
        primaryVerified = false;
    } else {
        btn.style.display = 'none';
        status.style.display = 'block';
        primaryVerified = true;
    }
});

document.getElementById('whatsapp_number').addEventListener('input', function() {
    const current = this.value;
    const original = "{{ $rider->whatsapp_number }}";
    const btn = document.getElementById('verify_whatsapp_btn');
    const status = document.getElementById('whatsapp_number_status');
    
    if (current !== original && current !== "") {
        btn.style.display = 'block';
        status.style.display = 'none';
        whatsappVerified = false;
    } else {
        btn.style.display = 'none';
        status.innerHTML = original ? '<i class="fa-solid fa-circle-check"></i> Verified' : '';
        status.style.display = 'block';
        whatsappVerified = true;
    }
});

function sendOTP(type) {
    const number = document.getElementById(type).value;
    if (number.length < 10) {
        Swal.fire({ title: 'Error', html: 'Invalid phone number', icon: 'error' });
        return;
    }

    Swal.fire({
        title: 'Sending OTP...',
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch("{{ route('delivery-rider.profile.send-otp') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ type: type, number: number })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Enter OTP',
                input: 'text',
                inputLabel: data.message || ('OTP sent to: ' + number),
                showCancelButton: true,
                confirmButtonText: 'Verify',
                confirmButtonColor: '#10B981',
                showLoaderOnConfirm: true,
                preConfirm: (otp) => {
                    return fetch("{{ route('delivery-rider.profile.verify-otp') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ type: type, otp: otp, number: number })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message);
                        }
                        return data;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
                }
            }).then(result => {
                if (result.isConfirmed) {
                    if (type === 'primary_mobile') {
                        primaryVerified = true;
                        document.getElementById('verify_primary_btn').style.display = 'none';
                        document.getElementById('primary_mobile_status').innerHTML = '<i class="fa-solid fa-circle-check"></i> Verified';
                        document.getElementById('primary_mobile_status').style.display = 'block';
                    } else if (type === 'whatsapp_number') {
                        whatsappVerified = true;
                        document.getElementById('verify_whatsapp_btn').style.display = 'none';
                        document.getElementById('whatsapp_number_status').innerHTML = '<i class="fa-solid fa-circle-check"></i> Verified';
                        document.getElementById('whatsapp_number_status').style.display = 'block';
                    } else if (type === 'username') {
                        usernameVerified = true;
                        document.getElementById('verify_username_btn').style.display = 'none';
                        document.getElementById('username_status').innerHTML = '<i class="fa-solid fa-circle-check"></i> Verified';
                        document.getElementById('username_status').style.display = 'block';
                    }
                    Swal.fire({ title: 'Verified!', html: 'Your number has been verified.', icon: 'success' });
                }
            });
        } else {
            Swal.fire({ title: 'Error', html: data.message, icon: 'error' });
        }
    })
    .catch(err => Swal.fire({ title: 'Error', html: 'Something went wrong', icon: 'error' }));
}

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

function changePassword() {
    Swal.fire({
        title: 'Change Password',
        html: `
            <div class="text-start">
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <div class="password-container" style="position: relative;">
                        <input type="password" class="form-control" id="newPassword" placeholder="New Password" required oninput="updateStrength(this.value)">
                        <i class="fa-regular fa-eye password-toggle" id="toggleNewPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;" onclick="togglePasswordVisibility('newPassword', 'toggleNewPassword')"></i>
                    </div>
                    <div class="password-strength mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small>Strength: <span id="strengthText">None</span></small>
                        </div>
                        <div class="strength-bar" id="strengthBar" style="height: 5px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
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
                    <label class="form-label">Confirm Password</label>
                    <div class="password-container" style="position: relative;">
                        <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm Password" required>
                        <i class="fa-regular fa-eye password-toggle" id="toggleConfirmPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;" onclick="togglePasswordVisibility('confirmPassword', 'toggleConfirmPassword')"></i>
                    </div>
                    <div id="passwordMatch" class="mt-3">
                        <small class="form-text text-success d-none">
                            <i class="fas fa-check-circle"></i> Passwords match
                        </small>
                        <small class="form-text text-danger d-none">
                            <i class="fas fa-times-circle"></i> Passwords don't match
                        </small>
                    </div>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Change',
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6b7280',
        showLoaderOnConfirm: true,
        didOpen: () => {
            const confirmInput = document.getElementById('confirmPassword');
            confirmInput.addEventListener('input', () => {
                const pass = document.getElementById('newPassword').value;
                const confirm = confirmInput.value;
                const matchIndicator = document.getElementById('passwordMatch');
                const success = matchIndicator.querySelector('.text-success');
                const error = matchIndicator.querySelector('.text-danger');
                if (pass && confirm) {
                    if (pass === confirm) {
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
            });
        },
        preConfirm: () => {
            const newPass = document.getElementById('newPassword').value;
            const confirm = document.getElementById('confirmPassword').value;

            if (newPass !== confirm) {
                Swal.showValidationMessage('Passwords do not match');
                return false;
            }
            
            const result = calculateStrength(newPass);
            if (!result.allValid) {
                Swal.showValidationMessage('Please meet all password requirements');
                return false;
            }

            return fetch("{{ route('delivery-rider.profile.password') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ new_password: newPass })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(json => { throw new Error(json.message || 'Failed to update password'); });
                }
                return response.json();
            })
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
            });
        }
    }).then(result => {
        if (result.isConfirmed) {
            Swal.fire({ icon: 'success', title: 'Success!', text: 'Password changed successfully.', confirmButtonColor: '#10B981' }).then(() => location.reload());
        }
    });
}

function calculateStrength(password) {
    const username = "{{ Auth::user()->username }}";
    const email = "{{ Auth::user()->email }}";
    
    const result = validateAdvancedPassword(password, { username, email });
    updatePasswordRuleFeedback(result);
    return result;
}

function updateStrength(password) {
    const result = calculateStrength(password);
    const strengthText = document.getElementById('strengthText');
    const strengthBar = document.getElementById('strengthBar');
    
    if (strengthText) {
        strengthText.textContent = result.strengthText;
        strengthText.style.color = result.color;
    }
    if (strengthBar) {
        const fill = strengthBar.querySelector('.strength-fill');
        if (fill) {
            fill.style.backgroundColor = result.color;
            fill.style.width = result.percent + '%';
        }
    }
}


</script>
@endsection
