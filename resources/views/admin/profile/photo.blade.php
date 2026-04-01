@extends('admin.layouts.admin_master')

@section('title', 'Change Profile Photo')
@section('page-title', 'Change Profile Photo')

@section('styles')

<style>
:root {
    --primary-green: #10B981;
    --dark-green: #059669;
    --body-bg: #f6f8fa;
    --card-bg: #ffffff;
    --text-color: #0f1724;
    --muted: #6b7280;
    --shadow-sm: 0 1px 3px rgba(15,23,36,0.04);
    --shadow-md: 0 7px 15px rgba(15,23,36,0.08);
}

.photo-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.photo-card {
    background: var(--card-bg);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--shadow-md);
    animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.photo-header {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
    padding: 25px 30px;
    color: white;
}

.photo-header h4 {
    margin: 0;
    font-size: 22px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 12px;
}

.photo-body {
    padding: 30px;
}

.photo-preview-container {
    text-align: center;
    margin-bottom: 30px;
}

.photo-preview-wrapper {
    width: 200px;
    height: 200px;
    margin: 0 auto 20px;
    border-radius: 50%;
    border: 4px solid var(--primary-green);
    padding: 8px;
    background: white;
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.2);
    transition: all 0.3s ease;
    overflow: hidden;
}

.photo-preview-wrapper:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 30px rgba(16, 185, 129, 0.3);
}

#photo-preview {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.photo-preview-wrapper:hover #photo-preview {
    transform: scale(1.1);
}

.photo-upload-area {
    border: 3px dashed #cbd5e1;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 30px;
    position: relative;
}

.photo-upload-area:hover {
    border-color: var(--primary-green);
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    transform: translateY(-5px) scale(1.02);
}

.upload-icon {
    font-size: 60px;
    color: var(--primary-green);
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.photo-upload-area:hover .upload-icon {
    transform: scale(1.2) rotate(10deg);
}

.photo-upload-area h5 {
    font-size: 20px;
    color: var(--text-color);
    margin: 0 0 10px 0;
}

.photo-upload-area p {
    color: var(--muted);
    margin: 0 0 20px 0;
    font-size: 14px;
}

.btn-select-photo {
    background: var(--primary-green);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.btn-select-photo:hover {
    background: var(--dark-green);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
}

#profile_photo {
    display: none;
}

.photo-guidelines {
    background: #f8fafc;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
    border-left: 4px solid var(--primary-green);
}

.photo-guidelines h6 {
    font-size: 16px;
    color: var(--text-color);
    margin: 0 0 15px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.photo-guidelines ul {
    margin: 0;
    padding-left: 20px;
    font-size: 14px;
    color: var(--muted);
}

.photo-guidelines li {
    margin-bottom: 8px;
    line-height: 1.5;
}

.photo-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.btn-update, .btn-remove, .btn-back {
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
    text-decoration: none;
    border: none;
    flex: 1;
    min-width: 200px;
}

.btn-update {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.btn-update:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
}

.btn-remove {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
}

.btn-remove:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
}

.btn-back {
    background: #6b7280;
    color: white;
    box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
}

.btn-back:hover {
    background: #4b5563;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(107, 114, 128, 0.4);
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
    flex-direction: column;
}

.loading-overlay.active {
    display: flex;
    animation: fadeIn 0.3s ease;
}

.loading-spinner {
    width: 60px;
    height: 60px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid var(--primary-green);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-text {
    font-size: 16px;
    color: var(--text-color);
    font-weight: 500;
}

.pulse-animation {
    animation: pulse 0.5s ease-in-out;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.shake-animation {
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

@media (max-width: 991px) {
    .photo-container {
        max-width: 100%;
    }

    .photo-actions {
        flex-direction: column;
    }

    .btn-update, .btn-remove, .btn-back {
        width: 100%;
        min-width: auto;
    }
}

@media (max-width: 767px) {
    .photo-container {
        padding: 15px;
    }

    .photo-header {
        padding: 20px;
    }

    .photo-header h4 {
        font-size: 20px;
    }

    .photo-body {
        padding: 20px;
    }

    .photo-preview-wrapper {
        width: 150px;
        height: 150px;
    }

    .photo-upload-area {
        padding: 25px;
    }

    .upload-icon {
        font-size: 50px;
    }

    .photo-upload-area h5 {
        font-size: 18px;
    }
}

@media (max-width: 480px) {
    .photo-container {
        padding: 10px;
    }

    .photo-header {
        padding: 15px;
    }

    .photo-header h4 {
        font-size: 18px;
    }

    .photo-body {
        padding: 15px;
    }

    .photo-preview-wrapper {
        width: 120px;
        height: 120px;
    }

    .photo-upload-area {
        padding: 20px;
    }

    .btn-update, .btn-remove, .btn-back {
        padding: 12px 20px;
        font-size: 14px;
    }
}
</style>
@endsection

@section('content')
<div class="photo-container">
    <div class="photo-card">
        <div class="photo-header">
            <h4><i class="fas fa-camera-retro"></i> Update Profile Photo</h4>
        </div>

        <div class="photo-body">
            <div class="photo-preview-container">
                <div class="photo-preview-wrapper">
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
                    <img id="photo-preview"
                         src="{{ $photoPath }}"
                         alt="Profile Photo Preview"
                         onerror="this.src='{{ asset('assets/images/default-avatar.png') }}'">
                </div>
                <p class="text-muted">Live preview of your profile photo</p>
            </div>

            <form action="{{ route('admin.profile.updatePhoto') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                @csrf

                <div class="photo-upload-area" onclick="document.getElementById('profile_photo').click()">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-arrow-up"></i>
                    </div>
                    <h5>Upload New Profile Photo</h5>
                    <p>Click to browse or drag & drop your image here</p>
                    <button type="button" class="btn-select-photo">
                        <i class="fas fa-folder-open"></i> Choose Photo
                    </button>
                </div>

                <input type="file" name="profile_photo" id="profile_photo"
                       class="form-control-file" accept="image/*" onchange="previewPhoto(event)">

                <div class="photo-guidelines">
                    <h6><i class="fas fa-circle-info"></i> Photo Requirements:</h6>
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
                        <i class="fas fa-cloud-upload-alt"></i> Upload & Save
                    </button>

                    @php
                        $user = Auth::user();
                        $hasCustomPhoto = $user->profile_photo &&
                                         $user->profile_photo !== 'default-avatar.png' &&
                                         file_exists(public_path('uploads/profile_pictures/' . $user->profile_photo));
                    @endphp

                    @if($hasCustomPhoto)
                    <button type="button" class="btn-remove" onclick="confirmDelete()">
                        <i class="fas fa-trash-alt"></i> Remove Current Photo
                    </button>
                    @endif

                    <a href="{{ route('admin.profile.index') }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Back to Profile
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

<script>
    function previewPhoto(event) {
        const file = event.target.files[0];
        if (!file) return;

        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        if (!validTypes.includes(file.type)) {
            Swal.fire({
                @if(file_exists(public_path('assets/icons/Gif/Validation Error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Validation Error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
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

        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({
                @if(file_exists(public_path('assets/icons/Gif/error2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
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
            text: 'Your current profile photo will be removed and default avatar will be restored.',
            @if(file_exists(public_path('assets/icons/Gif/Delete Request Confirmation1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Delete Request Confirmation1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'warning' @endif,
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

                fetch('{{ route("admin.profile.deletePhoto") }}', {
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
                            @if(file_exists(public_path('assets/icons/Gif/Delete Success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Delete Success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
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
                            const preview = document.getElementById('photo-preview');
                            if (preview) {
                                preview.src = '{{ asset("assets/images/default-avatar.png") }}';
                            }

                            const removeBtn = document.querySelector('.btn-remove');
                            if (removeBtn) {
                                removeBtn.style.display = 'none';
                            }
                        });
                    } else {
                        Swal.fire({
                            @if(file_exists(public_path('assets/icons/Gif/error4.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error4.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
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
                        @if(file_exists(public_path('assets/icons/Gif/Delete Unsuccess1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Delete Unsuccess1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
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
                @if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
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
                @if(file_exists(public_path('assets/icons/Gif/error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
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
                        @if(file_exists(public_path('assets/icons/Gif/alert1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/alert1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
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

