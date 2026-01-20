<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👩‍🌾 Farmer Hub | @yield('title')</title>

    <link rel="stylesheet" href="{{ asset('css/farmer-master.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @yield('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<div class="dashboard-wrapper">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('assets/images/logo-4.png') }}" class="logo">
            <h3>Farmer Panel</h3>
            <button id="sidebar-close" class="sidebar-toggle">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <ul class="main-menu">
                <li>
                    <a href="{{ route('farmer.dashboard') }}" class="menu-link {{ request()->routeIs('farmer.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
                    </a>
                </li>

                <li class="menu-heading">PRODUCTS</li>

                <li>
                    <a href="{{ route('farmer.products.my-products') }}" class="menu-link {{ request()->routeIs('farmer.products.my-products') ? 'active' : '' }}">
                        <i class="fa-solid fa-seedling"></i><span>My Products</span>
                        {{-- Updated to check the shared array --}}
                        @if(isset($sharedCounts['productCount']) && $sharedCounts['productCount'] > 0)
                            <span class="badge bg-success">{{ $sharedCounts['productCount'] }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('farmer.productRequests') }}" class="menu-link {{ request()->routeIs('farmer.productRequests') ? 'active' : '' }}">
                        <i class="fa-solid fa-handshake"></i><span>Buyer Requests</span>
                    </a>
                </li>

                @if(Auth::user()->farmer && Auth::user()->farmer->lead_farmer_id)
                <li>
                    <a href="{{ route('farmer.products.add') }}" class="menu-link {{ request()->routeIs('farmer.products.add') ? 'active' : '' }}">
                        <i class="fa-solid fa-plus-circle"></i><span>Add Product Directly</span>
                    </a>
                </li>
                @endif

                <li class="menu-heading">ORDERS</li>

                <li>
                    <a href="{{ route('farmer.orders.active') }}" class="menu-link {{ request()->routeIs('farmer.orders.active') ? 'active' : '' }}">
                        <i class="fa-solid fa-clipboard-list"></i><span>Active Orders</span>
                        {{-- Updated to check the shared array --}}
                        @if(isset($sharedCounts['pendingOrders']) && $sharedCounts['pendingOrders'] > 0)
                            <span class="badge bg-warning">{{ $sharedCounts['pendingOrders'] }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('farmer.orders.history') }}" class="menu-link {{ request()->routeIs('farmer.orders.history') ? 'active' : '' }}">
                        <i class="fa-solid fa-history"></i><span>Order History</span>
                    </a>
                </li>

                <li class="menu-heading">COMPLAINTS</li>

                <li>
                    <a href="{{ route('farmer.complaints.create') }}" class="menu-link {{ request()->routeIs('farmer.complaints.create') ? 'active' : '' }}">
                        <i class="fa-solid fa-flag"></i><span>File Complaint</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('farmer.complaints.list') }}" class="menu-link {{ request()->routeIs('farmer.complaints.list') ? 'active' : '' }}">
                        <i class="fa-solid fa-list-check"></i><span>My Complaints</span>
                        {{-- Updated to check the shared array --}}
                        @if(isset($sharedCounts['openComplaints']) && $sharedCounts['openComplaints'] > 0)
                            <span class="badge bg-danger">{{ $sharedCounts['openComplaints'] }}</span>
                        @endif
                    </a>
                </li>

                <li class="menu-heading">ACCOUNT</li>

                <li>
                    <a href="{{ route('farmer.profile.profile') }}" class="menu-link {{ request()->routeIs('farmer.profile.profile') ? 'active' : '' }}">
                        <i class="fa-solid fa-user"></i><span>My Profile</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('farmer.profile.photo') }}" class="menu-link {{ request()->routeIs('farmer.profile.photo') ? 'active' : '' }}">
                        <i class="fa-solid fa-camera"></i><span>Profile Photo</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('farmer.profile.settings') }}" class="menu-link {{ request()->routeIs('farmer.profile.settings') ? 'active' : '' }}">
                        <i class="fa-solid fa-gear"></i><span>Account Settings</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('farmer.profile.payment') }}" class="menu-link {{ request()->routeIs('farmer.profile.payment') ? 'active' : '' }}">
                        <i class="fas fa-money-check-alt"></i><span>Payment Preferences</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('farmer.notifications') }}" class="menu-link {{ request()->routeIs('farmer.notifications') ? 'active' : '' }}">
                        <i class="fa-solid fa-bell"></i><span>Notifications</span>
                        @if(isset($unreadNotifications) && $unreadNotifications > 0)
                            <span class="notif-dot"></span>
                        @endif
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            @if(Auth::user()->farmer && Auth::user()->farmer->lead_farmer_id)
            <div class="lead-farmer-info">
                <small>Linked to Lead Farmer:</small>
                <strong>{{ Auth::user()->farmer->leadFarmer->name ?? 'Not Assigned' }}</strong>
            </div>
            @endif

            <form id="logout-form" action="{{ url('/logout') }}" method="POST">@csrf</form>
            <a href="#" id="logout-button" class="logout-link">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="main-content">
        <header class="navbar">
            <div class="left-header">
                <button id="mobile-menu-btn" class="mobile-menu-btn">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1 class="page-title">
                    <i class="fa-solid fa-tractor accent"></i>
                    @yield('page-title', 'Farmer Dashboard')
                </h1>
            </div>

            <div class="header-right-group">
                <!-- Order Status Alert -->
                @if(isset($pendingPickups) && $pendingPickups > 0)
                <div class="alert-badge" id="pendingPickupAlert">
                    <i class="fa-solid fa-truck-fast"></i>
                    <span>{{ $pendingPickups }} Pending Pickup</span>
                </div>
                @endif

                <div class="notif-wrapper">
                    <div class="notif-btn" id="notifBtn">
                        <i class="fa-regular fa-bell"></i>
                        @if(isset($unreadNotifications) && $unreadNotifications > 0)
                            <span class="notif-dot"></span>
                        @endif
                    </div>

                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <span>Notifications</span>
                            <button class="mark-all-read" id="markAllRead">Mark all read</button>
                        </div>

                        <div class="notif-list">
                            @if(!isset($notifications) || count($notifications) == 0)
                                <div class="notif-empty">No notifications</div>
                            @else
                                @foreach($notifications as $n)
                                <div class="notif-item {{ $n->is_read ? 'read' : 'unread' }}" data-id="{{ $n->id }}">
                                    <div class="notif-icon">
                                        @if($n->notification_type == 'order_payment')
                                            <i class="fa-solid fa-money-bill-wave text-success"></i>
                                        @elseif($n->notification_type == 'admin_alert')
                                            <i class="fa-solid fa-triangle-exclamation text-warning"></i>
                                        @else
                                            <i class="fa-solid fa-info-circle text-info"></i>
                                        @endif
                                    </div>
                                    <div class="notif-content">
                                        <div class="notif-title">{{ $n->title }}</div>
                                        <div class="notif-msg">{{ Str::limit($n->message, 80) }}</div>
                                        <small class="notif-time">{{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}</small>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="notif-footer">
                            <a href="{{ route('farmer.notifications') }}">View all notifications</a>
                        </div>
                    </div>
                </div>

                <div class="header-user-meta">
                    <span class="role">Farmer</span>
                    <span class="username">
                        {{ Auth::user()->farmer->name ?? Auth::user()->username ?? 'Farmer' }}
                    </span>
                    @if(Auth::user()->farmer && Auth::user()->farmer->lead_farmer_id)
                    <small class="text-muted">
                        Group: {{ Auth::user()->farmer->leadFarmer->group_name ?? 'No Group' }}
                    </small>
                    @endif
                </div>

                <a href="{{ route('farmer.profile.photo') }}" class="profile-photo-link">
                    <img src="{{ asset('uploads/profile_pictures/' . (Auth::user()->profile_photo ?? 'default-avatar.png')) }}"
                         class="profile-photo"
                         onerror="this.src='{{ asset('assets/icons/farmer-icon.svg') }}'">
                </a>

                <form id="logout-form-top" action="{{ url('/logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>

                <div class="logout-icon" id="logoutTop" title="Logout">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </div>
            </div>
        </header>

        <section class="dashboard-body">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Main Content -->
            @yield('content')
        </section>
    </main>
</div>

<div class="overlay" id="overlay"></div>

<!-- Bootstrap & Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
@yield('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebarClose = document.getElementById('sidebar-close');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function() {
                sidebar.classList.add('open');
                overlay.classList.add('active');
            });
        }

        if (sidebarClose) {
            sidebarClose.addEventListener('click', function() {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            });
        }

        const logoutButton = document.getElementById('logout-button');
        const logoutTop = document.getElementById('logoutTop');
        const logoutForm = document.getElementById('logout-form');
        const logoutFormTop = document.getElementById('logout-form-top');

        function showLogoutConfirmation(form) {
            Swal.fire({
                title: 'Ready to leave?',
                text: 'Are you sure you want to logout?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, logout!',
                cancelButtonText: 'Cancel',
                background: '#ffffff',
                color: '#0f1724',
                backdrop: 'rgba(15, 23, 36, 0.4)',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        if (logoutButton) {
            logoutButton.addEventListener('click', function(e) {
                e.preventDefault();
                showLogoutConfirmation(logoutForm);
            });
        }

        if (logoutTop) {
            logoutTop.addEventListener('click', function(e) {
                e.preventDefault();
                showLogoutConfirmation(logoutFormTop);
            });
        }

        const profilePhotoInput = document.getElementById('profile_photo');
        if (profilePhotoInput) {
            profilePhotoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                    if (!validTypes.includes(file.type)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid File Type',
                            text: 'Please upload only JPEG, PNG, JPG or GIF images.',
                            confirmButtonColor: '#10B981',
                            background: '#ffffff',
                            color: '#0f1724'
                        });
                        this.value = '';
                        return;
                    }

                    if (file.size > 2 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Too Large',
                            text: 'Image size should be less than 2MB.',
                            confirmButtonColor: '#10B981',
                            background: '#ffffff',
                            color: '#0f1724'
                        });
                        this.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('photo-preview');
                        if (preview) {
                            preview.src = e.target.result;
                            Swal.fire({
                                icon: 'success',
                                title: 'Image Loaded',
                                text: 'Profile photo preview updated!',
                                timer: 1500,
                                showConfirmButton: false,
                                background: '#ffffff',
                                color: '#0f1724'
                            });
                        }
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        const notifBtn = document.getElementById('notifBtn');
        const notifDropdown = document.getElementById('notifDropdown');

        if (notifBtn && notifDropdown) {
            notifBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();

                notifDropdown.classList.toggle('show');
                const isHidden = notifDropdown.classList.contains('show') ? 'false' : 'true';
                notifDropdown.setAttribute('aria-hidden', isHidden);
            });

            const markAllReadBtn = document.getElementById('markAllRead');
            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', function() {
                    fetch('{{ route("farmer.notifications.mark-all-read") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelectorAll('.notif-item.unread').forEach(item => {
                                item.classList.remove('unread');
                                item.classList.add('read');
                            });
                            const notifDot = document.querySelector('.notif-dot');
                            if (notifDot) notifDot.remove();

                            Swal.fire({
                                icon: 'success',
                                title: 'Notifications Read',
                                text: 'All notifications marked as read',
                                timer: 1500,
                                showConfirmButton: false,
                                background: '#ffffff',
                                color: '#0f1724'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: 'Could not mark notifications as read',
                                confirmButtonColor: '#10B981',
                                background: '#ffffff',
                                color: '#0f1724'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Network error occurred',
                            confirmButtonColor: '#10B981',
                            background: '#ffffff',
                            color: '#0f1724'
                        });
                    });
                });
            }

            document.querySelectorAll('.notif-item.unread').forEach(item => {
                item.addEventListener('click', function() {
                    const notifId = this.getAttribute('data-id');
                    if (notifId) {
                        fetch('{{ route("farmer.notifications.mark-read") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ id: notifId })
                        })
                        .then(() => {
                            this.classList.remove('unread');
                            this.classList.add('read');
                        })
                        .catch(error => {
                            console.error('Error marking notification as read:', error);
                        });
                    }
                });
            });

            document.addEventListener('click', function(e) {
                if (notifDropdown && notifBtn && !notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
                    notifDropdown.classList.remove('show');
                    notifDropdown.setAttribute('aria-hidden', 'true');
                }
            });
        }

        const pendingPickupAlert = document.getElementById('pendingPickupAlert');
        if (pendingPickupAlert) {
            pendingPickupAlert.addEventListener('click', function() {
                window.location.href = '{{ route("farmer.orders.active") }}';
            });
        }

        setTimeout(() => {
            document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        const successMessage = "{{ session('success') }}";
        const errorMessage = "{{ session('error') }}";
        const warningMessage = "{{ session('warning') }}";

        if (successMessage) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: successMessage,
                timer: 3000,
                showConfirmButton: false,
                background: '#ffffff',
                color: '#0f1724',
                toast: true,
                position: 'top-end'
            });
        }

        if (errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: errorMessage,
                timer: 3000,
                showConfirmButton: false,
                background: '#ffffff',
                color: '#0f1724',
                toast: true,
                position: 'top-end'
            });
        }

        if (warningMessage) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: warningMessage,
                timer: 3000,
                showConfirmButton: false,
                background: '#ffffff',
                color: '#0f1724',
                toast: true,
                position: 'top-end'
            });
        }
    });
</script>

</body>
</html>
