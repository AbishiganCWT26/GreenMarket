@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Update Profile Photo')

@section('page-title', 'Update Profile Photo')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lead_farmer/Profile_photo.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<link rel="stylesheet" href="{{ asset('css/lead_farmer/Profile.css') }}">
<div class="profile-container">
    <div class="profile-header">
        <h2><i class="fas fa-camera me-2"></i>Update Profile Photo</h2>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8">
            <div class="profile-card photo-update-card">
                <div class="card-header">
                    <h3><i class="fas fa-image me-2"></i>Change Profile Photo</h3>
                </div>
                <div class="card-body text-center">
                    <div class="current-photo-section mb-5">
                        <div class="current-photo-wrapper">
                            <img class="current-profile-photo" 
                                 src="{{ Auth::user()->profile_photo ? asset('uploads/profile_pictures/' . Auth::user()->profile_photo) : asset('assets/images/default-avatar.png') }}" 
                                 alt="Current Profile Photo"
                                 id="profilePreview">
                        </div>
                        <p class="current-photo-label"><i class="fas fa-user me-1"></i>Current Photo</p>
                    </div>

                    <form action="{{ route('lf.profile.photo.update') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                        @csrf
                        <div class="upload-area" id="uploadArea">
                            <div class="upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <p class="upload-title">Drag & drop or click to upload</p>
                            <p class="upload-subtitle">Supported formats: JPG, PNG, GIF (Max 5MB)</p>
                            <input type="file" name="profile_photo" class="d-none" id="photoInput" accept="image/*" required>
                            <button type="button" class="btn-upload" onclick="document.getElementById('photoInput').click()">
                                <i class="fas fa-folder-open me-2"></i>Choose Image
                            </button>
                            <div id="fileName" class="file-name-display"></div>
                        </div>

                        @error('profile_photo')
                            <div class="alert alert-danger mt-3">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
                            </div>
                        @enderror

                        <div class="preview-section mt-4" id="previewSection" style="display: none;">
                            <h4><i class="fas fa-eye me-2"></i>Preview</h4>
                            <div class="preview-image-container">
                                <img id="imagePreview" class="preview-image" alt="Image Preview">
                            </div>
                        </div>

                        <div class="form-actions mt-5">
                            <a href="{{ route('lf.profile') }}" class="btn-cancel">
                                <i class="fas fa-arrow-left me-2"></i>Back to Profile
                            </a>
                            <button type="submit" class="btn-save" id="submitBtn" disabled>
                                <i class="fas fa-upload me-2"></i>Update Photo
                            </button>
                        </div>
                    </form>
                    
                    <div class="upload-info mt-4 pt-4 border-top">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Recommendations:</strong> 
                        <br> 
                        Use a square image (1:1 ratio) for best results. 
                        <br> 
                        Recommended size: 512x512 pixels. 
                        <br> 
                        Max file size: 5MB.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                @if(file_exists(public_path('assets/icons/Gif/success6.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success6.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                @if(file_exists(public_path('assets/icons/Gif/error5.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error5.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
                title: 'Error!',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        const photoInput = document.getElementById('photoInput');
        const uploadArea = document.getElementById('uploadArea');
        const previewSection = document.getElementById('previewSection');
        const imagePreview = document.getElementById('imagePreview');
        const submitBtn = document.getElementById('submitBtn');
        const fileNameDisplay = document.getElementById('fileName');
        const photoForm = document.getElementById('photoForm');

        photoInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB in bytes
                
                if (file.size > maxSize) {
                    Swal.fire({
                        @if(file_exists(public_path('assets/icons/Gif/File Too Large1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/File Too Large1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
                        title: 'File Too Large',
                        text: 'Please select an image smaller than 5MB',
                        timer: 3000
                    });
                    this.value = '';
                    return;
                }

                if (!file.type.match('image.*')) {
                    Swal.fire({
                        @if(file_exists(public_path('assets/icons/Gif/Invalid File1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Invalid File1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
                        title: 'Invalid File',
                        text: 'Please select an image file (JPG, PNG, GIF)',
                        timer: 3000
                    });
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(re) {
                    imagePreview.src = re.target.result;
                    previewSection.style.display = 'block';
                    uploadArea.classList.add('has-file');
                }
                reader.readAsDataURL(file);
                
                fileNameDisplay.textContent = 'Selected: ' + file.name;
                submitBtn.disabled = false;
            }
        });

        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                photoInput.files = files;
                photoInput.dispatchEvent(new Event('change'));
            }
        });

        photoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Update Profile Photo',
                text: 'Are you sure you want to update your profile photo?',
                @if(file_exists(public_path('assets/icons/Gif/question2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/question2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endsection
