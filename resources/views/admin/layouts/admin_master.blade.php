<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenMarket | @yield('title')</title>

    <link rel="stylesheet" href="{{ asset('css/admin-master.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @yield('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
@include('includes.loader')
<div class="dashboard-wrapper">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('assets/images/Logo-4.png') }}" class="logo">
            <h3>Admin Panel</h3>
            <button id="sidebar-close" class="sidebar-toggle">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <ul class="main-menu">
                <li>
                    <a href="{{ url('/admin/dashboard') }}" class="menu-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-house"></i><span>Dashboard</span>
                    </a>
                </li>

                <li class="menu-heading">MANAGEMENT & OVERSIGHT</li>

                <li><a href="{{ url('/admin/users') }}" class="menu-link {{ request()->is('admin/users*') ? 'active' : '' }}"><i class="fa-solid fa-users-gear"></i><span>User Management</span></a></li>
                <li><a href="{{ url('/admin/products') }}" class="menu-link {{ request()->is('admin/products*') ? 'active' : '' }}"><i class="fa-solid fa-box-open"></i><span>Product Oversight</span></a></li>
                <li><a href="{{ url('/admin/sales') }}" class="menu-link {{ request()->is('admin/sales*') ? 'active' : '' }}"><i class="fa-solid fa-receipt"></i><span>Sales</span></a></li>
                <li><a href="{{ url('/admin/complaints') }}" class="menu-link {{ request()->is('admin/complaints*') ? 'active' : '' }}"><i class="fa-solid fa-flag"></i><span>Users Complaints</span></a></li>
                <li><a href="{{ url('/admin/lead-farmer-groups') }}" class="menu-link {{ request()->is('admin/lead-farmer-groups*') ? 'active' : '' }}"><i class="fa-solid fa-users-between-lines"></i><span>Lead Farmer Groups</span></a></li>
                <li><a href="{{ url('/admin/buyer-requests') }}" class="menu-link {{ request()->is('admin/buyer-requests*') ? 'active' : '' }}"><i class="fa-solid fa-handshake"></i><span>Buyer Requests</span></a></li>

                <li class="menu-heading">SYSTEM TAXONOMY</li>

                <li><a href="{{ url('/admin/taxonomy') }}" class="menu-link {{ request()->is('admin/taxonomy*') ? 'active' : '' }}"><i class="fa-solid fa-seedling"></i><span>Taxonomy</span></a></li>
                <li><a href="{{ url('/admin/standards') }}" class="menu-link {{ request()->is('admin/standards*') ? 'active' : '' }}"><i class="fa-solid fa-ruler-combined"></i><span>Standards</span></a></li>

                <li class="menu-heading">REPORTS & ANALYTICS</li>

                <li><a href="{{ url('/admin/reports') }}" class="menu-link {{ request()->is('admin/reports*') ? 'active' : '' }}"><i class="fa-solid fa-chart-bar"></i><span>Reports</span></a></li>

                <li class="menu-heading">SYSTEM CONFIGURATION</li>

                <li><a href="{{ url('/admin/config') }}" class="menu-link {{ request()->is('admin/config*') ? 'active' : '' }}"><i class="fa-solid fa-gear"></i><span>Configuration</span></a></li>

                <li class="menu-heading">PROFILE & NOTIFICATIONS</li>

                <li><a href="{{ url('/admin/profile') }}" class="menu-link {{ request()->is('admin/profile*') ? 'active' : '' }}"><i class="fa-solid fa-user"></i><span>My Profile</span></a></li>
                <li><a href="{{ url('/admin/profile/photo') }}" class="menu-link {{ request()->is('admin/profile/photo*') ? 'active' : '' }}"><i class="fa-solid fa-camera"></i><span>Profile Photo</span></a></li>
                <li><a href="{{ url('/admin/notifications') }}" class="menu-link {{ request()->is('admin/notifications*') ? 'active' : '' }}"><i class="fa-solid fa-bell"></i><span>Notification</span></a></li>
            </ul>
        </nav>

        <div class="sidebar-footer">
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
                    <i class="fa-solid fa-gauge-high accent"></i>
                    @yield('page-title', 'Dashboard Overview')
                </h1>
            </div>

            <div class="header-right-group">
                <div class="notif-wrapper">
                    <div class="notif-btn" id="notifBtn" title="Notifications">
                        <i class="fa-regular fa-bell"></i>
                        @if(isset($unreadNotifications) && $unreadNotifications > 0)
                            <span class="notif-dot"></span>
                        @endif
                    </div>
                    <div class="notif-dropdown" id="notifDropdown" aria-hidden="true">
                        <div class="notif-header">
                            <span class="notif-title-icon"><i class="fa-solid fa-bell"></i> Notifications</span>
                            @if(isset($unreadNotifications) && $unreadNotifications > 0)
                                <button id="markAllRead" class="mark-read">Mark all read</button>
                            @endif
                        </div>
                        <div class="notif-list">
                            @if(!isset($recentNotifications) || count($recentNotifications) == 0)
                                <div class="notif-empty">
                                    <i class="fa-regular fa-bell-slash"></i>
                                    <span>No notifications</span>
                                </div>
                            @else
                                @foreach($recentNotifications as $n)
                                    <div class="notif-item {{ $n->is_read ? 'read' : 'unread' }}" data-id="{{ $n->id }}">
                                        <div class="notif-icon">
                                            @if(str_contains(strtolower($n->title), 'urgent'))
                                                <i class="fa-solid fa-exclamation-triangle urgent-icon"></i>
                                            @elseif(str_contains(strtolower($n->title), 'success'))
                                                <i class="fa-solid fa-check-circle success-icon"></i>
                                            @elseif(str_contains(strtolower($n->title), 'warning'))
                                                <i class="fa-solid fa-exclamation warning-icon"></i>
                                            @elseif(str_contains(strtolower($n->title), 'info'))
                                                <i class="fa-solid fa-info-circle info-icon"></i>
                                            @else
                                                <i class="fa-solid fa-envelope default-icon1"></i>
                                            @endif
                                        </div>
                                        <div class="notif-content">
                                            <div class="notif-title">{{ $n->title }}</div>
                                            <div class="notif-msg">{{ Str::limit($n->message, 80) }}</div>
                                            <div class="notif-time">{{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}</div>
                                        </div>
                                        <div class="notif-actions">
                                            @if(!$n->is_read)
                                                <button class="mark-single" data-id="{{ $n->id }}" title="Mark read">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="notif-footer">
                            <a href="{{ url('/admin/notifications') }}" class="view-all-link">
                                <i class="fa-solid fa-list"></i> View all notifications
                            </a>
                        </div>
                    </div>
                </div>

                <div class="header-user-meta">
                    <span class="role">{{ ucfirst(Auth::user()->role ?? 'Admin') }}</span>
                    <span class="username" id="user-display-name">{{ Auth::user()->username ?? Auth::user()->name ?? 'Administrator' }}</span>
                </div>

                <a href="{{ url('/admin/profile') }}" class="profile-photo-link" title="View Profile">
                    <img src="{{ asset('uploads/profile_pictures/' . (Auth::user()->profile_photo ?? 'default-avatar.png')) }}"
                         class="profile-photo"
                         onerror="this.src='{{ asset('uploads/profile_pictures/default-admin.png') }}'"
                         alt="Admin Profile">
                </a>

                <form id="logout-form-top" action="{{ url('/logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>
                <div class="logout-icon" id="logoutTop" title="Logout">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </div>
            </div>
        </header>

        <!-- Dynamic Content Section -->
        <section class="dashboard-body">
            @yield('content')
        </section>
    </main>
</div>

<div class="overlay" id="overlay"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@yield('scripts')
<script src="{{ asset('js/admin-master.js') }}?v={{ time() }}"></script>

<script>
    document.body.dataset.urlMarkAllRead = "{{ url('/admin/notifications/mark-all-read') }}";
    document.body.dataset.urlMarkSingleRead = "{{ url('/admin/notifications/mark-read') }}";
    document.body.dataset.urlAlertFacilitator = "{{ url('/admin/complaints/alert') }}";

    // The logic has been moved to admin-master.js

</script>

</body>
</html>
