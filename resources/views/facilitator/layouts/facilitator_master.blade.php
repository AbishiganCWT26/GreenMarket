<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧑‍💻 Facilitator Hub | @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/facilitator-master.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @yield('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .notif-meta { margin-top: 5px; display: flex; justify-content: space-between; align-items: center; }
        .mark-single-read { background: none; border: none; color: #10B981; cursor: pointer; padding: 2px 5px; border-radius: 4px; transition: all 0.2s; font-size: 0.75rem; font-weight: 500; }
        .mark-single-read:hover { background: #ecfdf5; color: #059669; text-decoration: underline; }
        .notif-item.read { opacity: 0.6; background-color: #f9fafb; }
        .read-status { font-size: 0.75rem; color: #6b7280; font-weight: 500; }
    </style>
</head>
<body>
@include('includes.loader')
<div class="dashboard-wrapper">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('assets/images/Logo Green Market.png') }}" class="logo" alt="Greenmarket">
            <h3>Facilitator Panel</h3>
            <button id="sidebar-close" class="sidebar-toggle">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <ul class="main-menu">
                <li>
                    <a href="{{ route('facilitator.dashboard') }}" class="menu-link {{ request()->routeIs('facilitator.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
                    </a>
                </li>

                <li class="menu-heading">TAXONOMY MANAGEMENT</li>

                <li>
                    <a href="{{ route('facilitator.taxonomy') }}" class="menu-link {{ request()->routeIs('facilitator.taxonomy') ? 'active' : '' }}">
                        <i class="fa-solid fa-layer-group"></i><span> Categories</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('facilitator.unit-of-measures') }}" class="menu-link {{ request()->routeIs('facilitator.unit-of-measures') ? 'active' : '' }}">
                        <i class="fa-solid fa-ruler-combined"></i><span> Unit of Measures</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('facilitator.quality-grades') }}" class="menu-link {{ request()->routeIs('facilitator.quality-grades') ? 'active' : '' }}">
                        <i class="fa-solid fa-medal"></i><span>Quality Grades</span>
                    </a>
                </li>

                <li class="menu-heading">USER MANAGEMENT</li>

                <li>
                    <a href="{{ route('facilitator.users') }}" class="menu-link {{ request()->routeIs('facilitator.users') ? 'active' : '' }}">
                        <i class="fa-solid fa-users"></i><span>Manage Users</span>
                        @if(isset($sharedCounts['pendingComplaints']) && $sharedCounts['pendingComplaints'] > 0)
                            <span class="badge bg-danger">{{ $sharedCounts['pendingComplaints'] }}</span>
                        @endif
                    </a>
                </li>

                <li class="menu-heading">COMPLAINTS</li>

                <li>
                    <a href="{{ route('facilitator.complaints') }}" class="menu-link {{ request()->routeIs('facilitator.complaints') ? 'active' : '' }}">
                        <i class="fa-solid fa-flag"></i><span>View Complaints</span>
                        @if(isset($sharedCounts['pendingComplaints']) && $sharedCounts['pendingComplaints'] > 0)
                            <span class="badge bg-warning">{{ $sharedCounts['pendingComplaints'] }}</span>
                        @endif
                    </a>
                </li>

                <li class="menu-heading">ACCOUNT</li>

                <li>
                    <a href="{{ route('facilitator.profile') }}" class="menu-link {{ request()->routeIs('facilitator.profile') ? 'active' : '' }}">
                        <i class="fa-solid fa-user"></i><span>My Profile</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('facilitator.notifications') }}" class="menu-link {{ request()->routeIs('facilitator.notifications') ? 'active' : '' }}">
                        <i class="fa-solid fa-bell"></i><span>Notifications</span>
                        @if(isset($sharedCounts['totalNotifications']) && $sharedCounts['totalNotifications'] > 0)
                            <span class="notif-dot"></span>
                        @endif
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <a href="{{ route('logout.confirmation') }}" id="nav-logout-link" class="logout-link">
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
                    <i class="fa-solid fa-hands-helping"></i>
                    @yield('page-title', 'Facilitator Dashboard')
                </h1>
            </div>

            <div class="header-right-group">
                <div class="notif-wrapper">
                    <div class="notif-btn" id="notifBtn">
                        <i class="fa-regular fa-bell"></i>
                        @if(isset($sharedCounts['totalNotifications']) && $sharedCounts['totalNotifications'] > 0)
                            <span class="notif-dot"></span>
                        @endif
                    </div>

                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <span>Notifications</span>
                            <button class="mark-all-read" id="markAllRead">Mark all read</button>
                        </div>

                        <div class="notif-list">
                            @if(isset($recentActivities) && count($recentActivities) > 0)
                                @foreach($recentActivities as $notification)
                                <div class="notif-item {{ $notification->is_read ? 'read' : '' }}" data-id="{{ $notification->id }}">
                                    <div class="notif-icon">
                                        @if($notification->notification_type == 'admin_alert')
                                            <i class="fa-solid fa-triangle-exclamation text-warning"></i>
                                        @else
                                            <i class="fa-solid fa-info-circle text-info"></i>
                                        @endif
                                    </div>
                                    <div class="notif-content">
                                        <div class="notif-title">{{ $notification->title }}</div>
                                        <div class="notif-msg">{{ Str::limit($notification->message, 80) }}</div>
                                        <div class="notif-meta">
                                            <small class="notif-time">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                                            @if($notification->is_read)
                                                <span class="read-status">Read</span>
                                            @else
                                                <button class="mark-single-read" data-id="{{ $notification->id }}">
                                                    Mark as read
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="notif-empty">No notifications</div>
                            @endif
                        </div>

                        <div class="notif-footer">
                            <a href="{{ route('facilitator.notifications') }}" id="viewAllNotifications">View all</a>
                        </div>
                    </div>
                </div>

                <div class="header-user-meta">
                    <span class="role">Facilitator</span>
                    <span class="username">
                        {{ Auth::user()->facilitator->name ?? Auth::user()->username ?? 'Facilitator' }}
                    </span>
                </div>

                <a href="{{ route('facilitator.profile') }}" class="profile-photo-link" id="headerProfilePhotoLink">
                    <img src="{{ asset('uploads/profile_pictures/' . (Auth::user()->profile_photo ?? 'default-avatar.png')) }}"
                         class="profile-photo"
                         onerror="this.src='{{ asset('assets/icons/facilitator-icon.svg') }}'">
                </a>

                <a href="{{ route('logout.confirmation') }}" class="logout-icon" id="header-logout-link" title="Logout">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </a>
            </div>
        </header>

        <section class="dashboard-body">
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

            @yield('content')
        </section>
    </main>
</div>

<div class="overlay" id="overlay"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@yield('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebarClose = document.getElementById('sidebar-close');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const notifBtn = document.getElementById('notifBtn');
        const notifDropdown = document.getElementById('notifDropdown');
        const pendingComplaintsAlert = document.getElementById('pendingComplaintsAlert');

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

        if (notifBtn && notifDropdown) {
            notifBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                notifDropdown.classList.toggle('show');
            });

            document.addEventListener('click', function(e) {
                if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
                    notifDropdown.classList.remove('show');
                }
            });
        }

        const logoutButtons = document.querySelectorAll('#nav-logout-link, #header-logout-link');
        logoutButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Ready to leave?',
                    text: 'You are about to log out of your account',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, logout',
                    cancelButtonText: 'Stay',
                    background: '#ffffff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("logout") }}';
                        form.innerHTML = '@csrf';
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });

        const markAllReadBtn = document.getElementById('markAllRead');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function() {
                fetch('{{ route("facilitator.notifications.mark-all-read") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('.notif-dot').remove();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'All notifications marked as read',
                            timer: 1500,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                        
                        $('.notif-item').addClass('read');
                        $('.mark-single-read').replaceWith('<span class="read-status">Read</span>');
                        $('#notifDropdown').removeClass('show');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to mark notifications as read',
                        confirmButtonColor: '#10B981'
                    });
                });
            });
        }

        // Single mark as read
        $(document).on('click', '.mark-single-read', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const btn = $(this);
            const notifId = btn.data('id');
            const notifItem = btn.closest('.notif-item');
            
            // Use route helper with a placeholder to get correct base URL
            let url = "{{ route('facilitator.notifications.mark-read', ':id') }}";
            url = url.replace(':id', notifId);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    notifItem.addClass('read');
                    btn.replaceWith('<span class="read-status">Read</span>');

                    // Check if there are any unread notifications left in the list
                    // (This is an approximation since we only show 5 in dropdown)
                    const remainingUnread = $('.notif-item').filter(function() {
                        return $(this).css('opacity') !== '0.4';
                    }).length;
                    
                    if (remainingUnread === 0) {
                        $('.notif-dot').remove();
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Marked as read',
                        timer: 1000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            })
            .catch(error => console.error('Error:', error));
        });

        if (pendingComplaintsAlert) {
            pendingComplaintsAlert.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = '{{ route("facilitator.complaints") }}';
            });
        }

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session("success") }}',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session("error") }}',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif

        @if(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: '{{ session("warning") }}',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: '{!! implode("<br>", $errors->all()) !!}',
                timer: 4000
            });
        @endif

        setTimeout(() => {
            document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>
</body>
</html>