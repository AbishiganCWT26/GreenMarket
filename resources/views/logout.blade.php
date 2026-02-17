<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
* {
margin: 0;
padding: 0;
box-sizing: border-box;
}

body {
font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
background: #f0f2f5;
min-height: 100vh;
display: flex;
justify-content: center;
align-items: center;
}

.swal2-popup {
border-radius: 16px !important;
background: white !important;
box-shadow: 0 8px 30px rgba(0,0,0,0.12) !important;
padding: 1.25rem !important;
width: 320px !important;
}

.swal2-title {
font-size: 1.25rem !important;
font-weight: 600 !important;
color: #111827 !important;
margin: 0 !important;
padding: 0 !important;
line-height: 1.4 !important;
}

.swal2-html-container {
font-size: 0.9rem !important;
color: #4b5563 !important;
margin: 0.5rem 0 !important;
padding: 0 !important;
line-height: 1.4 !important;
}

.swal2-image {
margin: 0.5rem auto !important;
width: 64px !important;
height: 64px !important;
object-fit: contain !important;
}

.swal2-actions {
gap: 0.5rem !important;
margin: 0.75rem 0 0 0 !important;
}

.swal2-confirm {
background: #dc2626 !important;
border: none !important;
border-radius: 8px !important;
padding: 0.5rem 1rem !important;
font-weight: 500 !important;
font-size: 0.85rem !important;
box-shadow: none !important;
margin: 0 !important;
}

.swal2-cancel {
background: #059669 !important;
border: none !important;
border-radius: 8px !important;
padding: 0.5rem 1rem !important;
font-weight: 500 !important;
font-size: 0.85rem !important;
box-shadow: none !important;
margin: 0 !important;
}

.swal2-confirm:hover, .swal2-cancel:hover {
transform: scale(0.98) !important;
}

.swal2-timer-progress-bar {
background: #e5e7eb !important;
height: 3px !important;
}

.loading-overlay {
position: fixed;
top: 0;
left: 0;
right: 0;
bottom: 0;
background: rgba(255,255,255,0.9);
display: none;
justify-content: center;
align-items: center;
z-index: 9999;
}

.loading-content {
background: white;
padding: 1.25rem;
border-radius: 16px;
text-align: center;
box-shadow: 0 8px 30px rgba(0,0,0,0.12);
width: 200px;
}

.loading-spinner {
width: 32px;
height: 32px;
border: 3px solid #f3f4f6;
border-top: 3px solid #3b82f6;
border-radius: 50%;
animation: spin 0.8s linear infinite;
margin: 0 auto 0.5rem;
}

@keyframes spin {
0% { transform: rotate(0deg); }
100% { transform: rotate(360deg); }
}

.loading-text {
color: #374151;
font-weight: 500;
font-size: 0.85rem;
}
</style>
</head>
<body>
<div class="loading-overlay" id="loadingOverlay">
<div class="loading-content">
<div class="loading-spinner"></div>
<p class="loading-text">Logging out...</p>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
const loadingOverlay = document.getElementById('loadingOverlay');

Swal.fire({
title: 'Logout',
html: 'Are you sure you want to logout?',
background: 'white',
backdrop: 'rgba(0,0,0,0.3)',
color: '#111827',
imageUrl: '{{ asset("assets/images/logout btn.png") }}',
imageWidth: 64,
imageHeight: 64,
imageAlt: 'Logout',
showCancelButton: true,
confirmButtonColor: '#dc2626',
cancelButtonColor: '#059669',
confirmButtonText: 'Yes, logout',
cancelButtonText: 'Cancel',
timerProgressBar: true,
position: 'center',
customClass: {
popup: 'swal2-popup',
title: 'swal2-title',
htmlContainer: 'swal2-html-container',
confirmButton: 'swal2-confirm',
cancelButton: 'swal2-cancel'
}
}).then((result) => {
if (result.isConfirmed) {
loadingOverlay.style.display = 'flex';
setTimeout(() => {
const form = document.createElement('form');
form.method = 'post';
form.action = '{{ route("logout") }}';
const csrfToken = document.createElement('input');
csrfToken.type = 'hidden';
csrfToken.name = '_token';
csrfToken.value = '{{ csrf_token() }}';
form.appendChild(csrfToken);
document.body.appendChild(form);
form.submit();
}, 300);
} else if (result.dismiss === Swal.DismissReason.cancel) {
window.history.back();
} else if (result.dismiss === Swal.DismissReason.timer) {
window.history.back();
}
});
});

document.addEventListener('keydown', function(e) {
if (e.key === 'Escape') {
window.history.back();
}
});
</script>
</body>
</html>