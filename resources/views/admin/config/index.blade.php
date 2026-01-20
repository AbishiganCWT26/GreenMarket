@extends('admin.layouts.admin_master')

@section('title', 'System Configuration')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/system-config.css') }}">
@endsection

@section('content')
<div class="dashboard-wrapper">
    <div class="main-content">
        <div class="content-header">
            <div class="header-left">
                <h1><i class="fas fa-cogs"></i> System Configuration & Content Management</h1>
                <nav class="breadcrumb">
                    <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>System Configuration</span>
                </nav>
            </div>
        </div>

        <div class="content-body">
            <div class="config-grid">
                <div class="config-card" onclick="window.location.href='{{ route('admin.config.manage', 'footer') }}'">
                    <div class="card-icon" style="background: linear-gradient(135deg, var(--primary-accent), var(--blue));">
                        <i class="fas fa-shoe-prints"></i>
                    </div>
                    <div class="card-content">
                        <h3>Footer Content</h3>
                        <p>Manage copyright, contact info, social links, and legal documents</p>
                    </div>
                    <div class="card-actions">
                        <span class="badge">Edit</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>

                <div class="config-card" onclick="window.location.href='{{ route('admin.config.manage', 'about_us') }}'">
                    <div class="card-icon" style="background: linear-gradient(135deg, var(--accent-amber), var(--purple));">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="card-content">
                        <h3>About Us Page</h3>
                        <p>Edit text, images, vision, mission, and story content</p>
                    </div>
                    <div class="card-actions">
                        <span class="badge">Edit</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>

                <div class="config-card" onclick="window.location.href='{{ route('admin.config.manage', 'how_it_works') }}'">
                    <div class="card-icon" style="background: linear-gradient(135deg, var(--purple), var(--blue));">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div class="card-content">
                        <h3>How It Works</h3>
                        <p>Manage buyer and farmer instructions with images</p>
                    </div>
                    <div class="card-actions">
                        <span class="badge">Edit</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>

                <div class="config-card" onclick="window.location.href='{{ route('admin.config.manage', 'general') }}'">
                    <div class="card-icon" style="background: linear-gradient(135deg, var(--success), var(--info));">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="card-content">
                        <h3>Admin Email</h3>
                        <p>Update system administrator email address</p>
                    </div>
                    <div class="card-actions">
                        <span class="badge">Edit</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>

                <div class="config-card backup-card">
                    <div class="card-icon" style="background: linear-gradient(135deg, var(--warning), var(--danger));">
                        <i class="fas fa-database"></i>
                    </div>
                    <div class="card-content">
                        <h3>Database Backup</h3>
                        <p>Download complete database backup in CSV or Text format</p>
                        <div class="backup-options">
                            <button onclick="downloadBackup('csv')" class="btn-backup csv">
                                <i class="fas fa-file-csv"></i> CSV Format
                            </button>
                            <button onclick="downloadBackup('txt')" class="btn-backup txt">
                                <i class="fas fa-file-alt"></i> Text Format
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
<script>
function downloadBackup(type) {
    const btn = event.target;
    const originalText = btn.innerHTML;

    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Preparing...';
    btn.disabled = true;

    fetch(`{{ route('admin.config.backup') }}?type=${type}`)
        .then(response => {
            if (!response.ok) throw new Error('Download failed');
            return response.blob();
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `db_backup_${new Date().toISOString().slice(0,19).replace(/:/g,'-')}.${type}`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);

            Swal.fire({
                icon: 'success',
                title: 'Backup Downloaded!',
                text: 'Database backup downloaded successfully.',
                timer: 2000,
                showConfirmButton: false,
                background: '#10B981',
                color: 'white',
                iconColor: 'white',
                timerProgressBar: true,
                position: 'top-end',
                toast: true
            });
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Download Failed',
                text: error.message || 'Failed to download backup',
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
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.config-card:not(.backup-card)');
    cards.forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endsection
