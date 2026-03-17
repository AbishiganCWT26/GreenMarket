<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Confirmation - GreenMarket</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Inter', Arial, sans-serif;
            background: #f3f4f6;
        }
        /* Blur background effect */
        .blur-bg {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            backdrop-filter: blur(8px);
            background: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }
        .swal2-popup {
            border-radius: 20px !important;
            padding: 1.5rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        }
    </style>
</head>
<body>
    <div class="blur-bg"></div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({
                title: 'Confirm Logout',
                text: 'Are you sure you want to end your session?',
                imageUrl: '{{ asset("assets/images/logout btn.png") }}',
                imageWidth: 80,
                imageHeight: 80,
                showCancelButton: true,
                confirmButtonText: 'Yes, Logout',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#059669',
                reverseButtons: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                background: '#ffffff',
                customClass: {
                    confirmButton: 'swal2-confirm',
                    cancelButton: 'swal2-cancel'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                } else {
                    // Try to go back, if not possible go to home
                    if (window.history.length > 1) {
                        window.history.back();
                    } else {
                        window.location.href = "{{ url('/') }}";
                    }
                }
            });
        });
    </script>
</body>
</html>