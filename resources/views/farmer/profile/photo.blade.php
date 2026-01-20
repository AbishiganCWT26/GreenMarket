@extends('farmer.layouts.farmer_master')

@section('title', 'Change Profile Photo')
@section('page-title', 'Change Profile Photo')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="{{ asset('css/farmer/profile-photo.css') }}">
@endsection

@section('content')
<div class="photo-container">
    <div class="photo-card">
        <div class="photo-header">
            <h4><i class="fa-solid fa-camera-retro"></i> Update Profile Photo</h4>
        </div>

        <div class="photo-body">
            <div class="photo-preview-container">
                <div class="photo-preview-wrapper">
                    @php
                        $user = Auth::user();
                        $photoPath = '';

                        // Check if user has a profile photo and it's not the default
                        if ($user->profile_photo && $user->profile_photo !== 'farmer-icon.svg') {
                            // Check if the file actually exists in storage
                            $filePath = public_path('uploads/profile_pictures/' . $user->profile_photo);
                            if (file_exists($filePath)) {
                                // Custom uploaded photo exists
                                $photoPath = asset('uploads/profile_pictures/' . $user->profile_photo);
                            } else {
                                // File doesn't exist, use default farmer icon
                                $photoPath = asset('assets/icons/farmer-icon.svg');
                            }
                        } else {
                            // Default farmer icon
                            $photoPath = asset('assets/icons/farmer-icon.svg');
                        }
                    @endphp
                    <img id="photo-preview"
                         src="{{ $photoPath }}"
                         alt="Profile Photo Preview"
                         onerror="this.src='{{ asset('assets/icons/farmer-icon.svg') }}'">
                </div>
                <p class="text-muted">Live preview of your profile photo</p>
            </div>

            <form action="{{ route('farmer.profile.photo.update') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                @csrf

                <div class="photo-upload-area" onclick="document.getElementById('profile_photo').click()">
                    <div class="upload-icon">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <h5>Upload New Profile Photo</h5>
                    <p>Click to browse or drag & drop your image here</p>
                    <button type="button" class="btn-select-photo">
                        <i class="fa-solid fa-folder-open"></i> Choose Photo
                    </button>
                </div>

                <input type="file" name="profile_photo" id="profile_photo"
                       class="form-control-file" accept="image/*" onchange="previewPhoto(event)">

                <div class="photo-guidelines">
                    <h6><i class="fa-solid fa-circle-info"></i> Photo Requirements:</h6>
                    <ul>
                        <li>Maximum file size: 5MB (for faster loading)</li>
                        <li>Supported formats: JPG, PNG, GIF only</li>
                        <li>Optimal size: 400×400 pixels</li>
                        <li>Clear, well-lit photos work best</li>
                        <li>Square or portrait photos recommended</li>
                    </ul>
                </div>

                <div class="photo-actions">
                    <button type="submit" class="btn-update">
                        <i class="fa-solid fa-cloud-upload-alt"></i> Upload & Save
                    </button>

                    @php
                        $user = Auth::user();
                        $hasCustomPhoto = $user->profile_photo &&
                                         $user->profile_photo !== 'farmer-icon.svg' &&
                                         file_exists(public_path('uploads/profile_pictures/' . $user->profile_photo));
                    @endphp

                    @if($hasCustomPhoto)
                    <button type="button" class="btn-remove" onclick="confirmDelete()">
                        <i class="fa-solid fa-trash-alt"></i> Remove Current Photo
                    </button>
                    @endif

                    <a href="{{ route('farmer.profile.profile') }}" class="btn-back">
                        <i class="fa-solid fa-arrow-left"></i> Back to Profile
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
    <div class="loading-text">Processing your photo...</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function previewPhoto(event) {
        const file = event.target.files[0];
        if (!file) return;

        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        if (!validTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File Type',
                text: 'Please select JPG, PNG or GIF image only',
                timer: 3000,
                timerProgressBar: true,
                background: '#ef4444',
                color: 'white',
                iconColor: 'white',
                toast: true,
                position: 'top-end'
            });
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                text: 'Maximum file size is 5MB',
                timer: 3000,
                timerProgressBar: true,
                background: '#ef4444',
                color: 'white',
                iconColor: 'white',
                toast: true,
                position: 'top-end'
            });
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('photo-preview');
            const wrapper = preview.parentElement;

            if (preview) {
                preview.src = e.target.result;
                wrapper.classList.add('pulse-animation');

                setTimeout(() => {
                    wrapper.classList.remove('pulse-animation');
                }, 1000);
            }
        };
        reader.readAsDataURL(file);
    }

    function confirmDelete() {
        Swal.fire({
            title: 'Remove Profile Photo?',
            text: 'Your current profile photo will be removed and default farmer icon will be restored.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Remove It',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            background: 'var(--card-bg)',
            color: 'var(--text-color)',
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('loadingOverlay').classList.add('active');

                fetch('{{ route("farmer.profile.photo.delete") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Photo Removed!',
                            text: data.message,
                            timer: 3000,
                            timerProgressBar: true,
                            background: 'linear-gradient(135deg, #10B981 0%, #059669 100%)',
                            color: 'white',
                            iconColor: 'white',
                            toast: true,
                            position: 'top-end'
                        }).then(() => {
                            // Update the preview image to farmer icon
                            const preview = document.getElementById('photo-preview');
                            if (preview) {
                                preview.src = '{{ asset("assets/icons/farmer-icon.svg") }}';
                            }

                            // Hide the remove button
                            const removeBtn = document.querySelector('.btn-remove');
                            if (removeBtn) {
                                removeBtn.style.display = 'none';
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                            timer: 4000,
                            timerProgressBar: true
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to remove photo. Please try again.',
                        timer: 4000,
                        timerProgressBar: true
                    });
                })
                .finally(() => {
                    document.getElementById('loadingOverlay').classList.remove('active');
                });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const photoForm = document.getElementById('photoForm');
        const uploadArea = document.querySelector('.photo-upload-area');

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 3000,
                timerProgressBar: true,
                background: 'linear-gradient(135deg, #10B981 0%, #059669 100%)',
                color: 'white',
                iconColor: 'white',
                toast: true,
                position: 'top-end'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                timer: 4000,
                timerProgressBar: true,
                background: '#ef4444',
                color: 'white',
                iconColor: 'white',
                toast: true,
                position: 'top-end'
            });
        @endif

        if (photoForm) {
            photoForm.addEventListener('submit', function(e) {
                const fileInput = document.getElementById('profile_photo');
                if (!fileInput.files[0]) {
                    e.preventDefault();

                    Swal.fire({
                        icon: 'error',
                        title: 'No Photo Selected',
                        text: 'Please select a photo to upload',
                        timer: 3000,
                        timerProgressBar: true,
                        background: '#ef4444',
                        color: 'white',
                        iconColor: 'white',
                        toast: true,
                        position: 'top-end'
                    }).then(() => {
                        uploadArea.classList.add('shake-animation');
                        setTimeout(() => {
                            uploadArea.classList.remove('shake-animation');
                        }, 500);
                    });
                    return;
                }

                document.getElementById('loadingOverlay').classList.add('active');
            });
        }

        if (uploadArea) {
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.style.borderColor = 'var(--primary-green)';
                this.style.background = 'linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%)';
                this.style.transform = 'translateY(-5px) scale(1.02)';
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.style.borderColor = '#cbd5e1';
                this.style.background = 'linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%)';
                this.style.transform = 'translateY(0) scale(1)';
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.style.borderColor = '#cbd5e1';
                this.style.background = 'linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%)';
                this.style.transform = 'translateY(0) scale(1)';

                const fileInput = document.getElementById('profile_photo');
                if (e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    previewPhoto({ target: fileInput });
                }
            });
        }

        const photoPreview = document.getElementById('photo-preview');
        if (photoPreview) {
            photoPreview.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.1)';
            });

            photoPreview.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        }

        const buttons = document.querySelectorAll('.btn-update, .btn-remove, .btn-back, .btn-select-photo');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
            });

            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        window.addEventListener('resize', function() {
            const container = document.querySelector('.photo-container');
            if (window.innerWidth <= 767) {
                container.classList.add('mobile-view');
            } else {
                container.classList.remove('mobile-view');
            }
        });

        if (window.innerWidth <= 767) {
            document.querySelector('.photo-container').classList.add('mobile-view');
        }
    });

    window.onbeforeunload = function() {
        if (document.getElementById('profile_photo').files.length > 0) {
            document.getElementById('loadingOverlay').classList.add('active');
        }
    };
</script>
@endsection
