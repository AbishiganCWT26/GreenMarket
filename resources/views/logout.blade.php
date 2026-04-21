<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Confirmation - GreenMarket</title>
    
    <style>
        body {
            margin: 0;
            font-family: 'Inter', Arial, sans-serif;
            background: url('https://png.pngtree.com/background/20230517/original/pngtree-abstract-green-background-with-wavy-design-and-hexagon-texture-dynamic-shadow-picture-image_2640998.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        /* Blur background effect */
        .blur-bg {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            backdrop-filter: blur(2px);
            background: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }
        .swal2-popup {
            border-radius: 20px !important;
            padding: 1.5rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        }

        .swal2-image {
            margin: 0em auto 0em !important;
        }

        div:where(.swal2-container) h2:where(.swal2-title) {
            padding: 0em 0em 0 !important;
        }
    </style>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                html: `
                    <div style="display: flex; justify-content: center; margin-bottom: 20px;">
                        <img src="{{ asset('assets/icons/Gif/logout2.gif') }}" 
                             onerror="this.onerror=null; this.src='{{ asset('assets/icons/Gif/logout1.gif') }}';" 
                             alt="Logout Icon" 
                             style="width: 80px; height: 80px;">
                    </div>
                    <p style="font-size: 1.1rem; color: #374151; margin: 0;">Are you sure you want to Logout?</p>
                `,
                showCancelButton: true,
                confirmButtonText: 'Yes, Logout',
                cancelButtonText: 'No, Go Back',
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
