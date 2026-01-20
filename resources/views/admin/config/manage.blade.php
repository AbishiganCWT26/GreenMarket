@extends('admin.layouts.admin_master')

@section('title', 'Manage ' . ucfirst(str_replace('_', ' ', $group)) . ' Configuration')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/system-config.css') }}">
@endsection

@section('content')
<div class="dashboard-wrapper">
    <div class="main-content">
        <div class="content-header">
            <div class="header-left">
                <h1><i class="fas fa-cogs"></i> Manage {{ ucfirst(str_replace('_', ' ', $group)) }}</h1>
                <nav class="breadcrumb">
                    <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
                    <i class="fas fa-chevron-right"></i>
                    <a href="{{ route('admin.config.index') }}">System Config</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>{{ ucfirst(str_replace('_', ' ', $group)) }}</span>
                </nav>
            </div>
            <div class="header-right">
                <a href="{{ route('admin.config.index') }}" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Config
                </a>
            </div>
        </div>

        <div class="content-body">
            <div class="config-manage-container">
                <div class="config-header-card">
                    <div class="header-icon">
                        @if($group === 'footer')
                            <i class="fas fa-shoe-prints"></i>
                        @elseif($group === 'about_us')
                            <i class="fas fa-info-circle"></i>
                        @elseif($group === 'how_it_works')
                            <i class="fas fa-play-circle"></i>
                        @else
                            <i class="fas fa-cog"></i>
                        @endif
                    </div>
                    <div class="header-content">
                        <h2>{{ ucfirst(str_replace('_', ' ', $group)) }} Configuration</h2>
                        <p>Update and manage all {{ str_replace('_', ' ', $group) }} related settings and content</p>
                    </div>
                    <div class="header-actions">
                        <button type="button" onclick="saveChanges()" class="btn btn-save">
                            <i class="fas fa-save"></i> Save All Changes
                        </button>
                    </div>
                </div>

                <form id="configForm" enctype="multipart/form-data">
                    @csrf
                    <div class="config-sections-grid">
                        @if($group === 'footer')
                            @include('admin.config.partials.footer')
                        @elseif($group === 'about_us')
                            @include('admin.config.partials.about')
                        @elseif($group === 'how_it_works')
                            @include('admin.config.partials.howitworks')
                        @elseif($group === 'general')
                            @include('admin.config.partials.general')
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
<script>
let isSaving = false;

// Update the saveChanges function to handle legal documents
function saveChanges() {
    if (isSaving) return;

    isSaving = true;
    const saveBtn = document.querySelector('.btn-save');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    saveBtn.disabled = true;

    const formData = new FormData(document.getElementById('configForm'));

    fetch("{{ route('admin.config.update', $group) }}", {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false,
                background: '#10B981',
                color: 'white',
                iconColor: 'white',
                timerProgressBar: true,
                position: 'top-end',
                toast: true
            });

            document.querySelectorAll('.config-input').forEach(input => {
                input.classList.add('saved-animation');
                setTimeout(() => input.classList.remove('saved-animation'), 1000);
            });

            document.querySelectorAll('.file-name').forEach(el => {
                el.innerHTML = '';
                el.style.display = 'none';
            });
        } else {
            throw new Error(data.message || 'Failed to save changes');
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message,
            timer: 3000,
            background: '#ef4444',
            color: 'white',
            iconColor: 'white',
            timerProgressBar: true,
            position: 'top-end',
            toast: true
        });
    })
    .finally(() => {
        isSaving = false;
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
}

function previewImage(inputId) {
    const input = document.getElementById(inputId);
    const previewId = inputId.replace('image_', 'preview_');
    const preview = document.getElementById(previewId);
    const removeBtn = document.getElementById('remove_' + inputId);

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (removeBtn) removeBtn.style.display = 'inline-flex';
        }

        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage(inputId) {
    const input = document.getElementById(inputId);
    const previewId = inputId.replace('image_', 'preview_');
    const preview = document.getElementById(previewId);
    const removeBtn = document.getElementById('remove_' + inputId);

    input.value = '';
    preview.src = '';
    preview.style.display = 'none';
    removeBtn.style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.config-input');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            this.classList.add('edited');
        });
    });
});

function previewLegalFile(inputId) {
    const input = document.getElementById(inputId);
    const previewId = 'preview_' + inputId;
    const preview = document.getElementById(previewId);

    if (input.files && input.files[0]) {
        const fileName = input.files[0].name;
        const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2); // MB

        preview.innerHTML = `
            <div class="file-info">
                <i class="fas fa-file"></i>
                <span class="name">${fileName}</span>
                <span class="size">(${fileSize} MB)</span>
            </div>
        `;
        preview.style.display = 'block';
    }
}
</script>
@endsection
