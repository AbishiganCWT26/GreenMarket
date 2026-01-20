<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛒 Buyer Hub | @yield('title')</title>

    <link rel="stylesheet" href="{{ asset('css/buyer-master.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @yield('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<div class="dashboard-wrapper">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('assets/images/logo-4.png') }}" class="logo">
            <h3>Buyer Panel</h3>
            <button id="sidebar-close" class="sidebar-toggle">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <ul class="main-menu">
                <li>
                    <a href="{{ route('buyer.dashboard') }}" class="menu-link {{ request()->routeIs('buyer.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
                    </a>
                </li>

                <li class="menu-heading">SHOP</li>

                <li>
                    <a href="{{ route('buyer.browseProducts') }}" class="menu-link {{ request()->routeIs('buyer.browseProducts') ? 'active' : '' }}">
                        <i class="fa-solid fa-store"></i><span>Browse Products</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('buyer.cart') }}" class="menu-link {{ request()->routeIs('buyer.cart') ? 'active' : '' }}">
                        <i class="fa-solid fa-cart-shopping"></i><span>Shopping Cart</span>
                        @if(session('cart_count', 0) > 0)
                            <span class="cart-badge">{{ session('cart_count') }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('buyer.wishlist') }}" class="menu-link {{ request()->routeIs('buyer.wishlist') ? 'active' : '' }}">
                        <i class="fa-solid fa-heart"></i><span>Wishlist</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('buyer.history') }}" class="menu-link {{ request()->routeIs('buyer.history') ? 'active' : '' }}">
                        <i class="fa-solid fa-clock-rotate-left"></i><span>Order History</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('buyer.productRequest.create') }}" class="menu-link {{ request()->routeIs('buyer.productRequest.create') ? 'active' : '' }}">
                        <i class="fa-solid fa-plus-circle"></i><span>Request Product</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('buyer.productRequests.my') }}" class="menu-link {{ request()->routeIs('buyer.productRequests.my') ? 'active' : '' }}">
                        <i class="fa-solid fa-list-check"></i><span>My Requests</span>
                    </a>
                </li>

                <li class="menu-heading">ACCOUNT</li>

                <li>
                    <a href="{{ route('buyer.profile.profile') }}" class="menu-link {{ request()->routeIs('buyer.profile.profile') ? 'active' : '' }}">
                        <i class="fa-solid fa-user"></i><span>My Profile</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('buyer.profile.photo') }}" class="menu-link {{ request()->routeIs('buyer.profile.photo') ? 'active' : '' }}">
                        <i class="fa-solid fa-camera"></i><span>Profile Photo</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('buyer.notifications') }}" class="menu-link {{ request()->routeIs('buyer.notifications') ? 'active' : '' }}">
                        <i class="fa-solid fa-bell"></i><span>Notifications</span>
                        @if(isset($unreadNotifications) && $unreadNotifications > 0)
                            <span class="notif-dot"></span>
                        @endif
                    </a>
                </li>

                <li class="menu-heading">COMPLAINTS</li>

                <li>
                    <a href="{{ route('buyer.complaints.create') }}" class="menu-link {{ request()->routeIs('buyer.complaints.create') ? 'active' : '' }}">
                        <i class="fa-solid fa-flag"></i><span>File Complaint</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('buyer.complaints.list') }}" class="menu-link {{ request()->routeIs('buyer.complaints.list') ? 'active' : '' }}">
                        <i class="fa-solid fa-list-check"></i><span>My Complaints</span>
                        @php
                            $sharedCounts = session('sharedCounts', ['openComplaints' => 0]);
                        @endphp
                        @if($sharedCounts['openComplaints'] > 0)
                            <span class="badge bg-danger">{{ $sharedCounts['openComplaints'] }}</span>
                        @endif
                    </a>
                </li>

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
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>

            <div class="header-right-group">
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
                        </div>

                        <div class="notif-list">
                            @if(!isset($notifications) || count($notifications) == 0)
                                <div class="notif-empty">No notifications</div>
                            @else
                                @foreach($notifications as $n)
                                <div class="notif-item {{ $n->is_read ? 'read' : 'unread' }}">
                                    <div class="notif-left">
                                        <div class="notif-title">{{ $n->title }}</div>
                                        <div class="notif-msg">{{ Str::limit($n->message, 80) }}</div>
                                    </div>
                                    <div class="notif-actions">
                                        <small>{{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}</small>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="notif-footer">
                            <a href="{{ route('buyer.notifications') }}">View all</a>
                        </div>
                    </div>
                </div>

                <div class="header-user-meta">
                    <span class="role">Buyer</span>
                    <span class="username">
                        {{ Auth::user()->username ?? Auth::user()->name ?? 'Buyer' }}
                    </span>
                </div>

                <a href="{{ route('buyer.profile.photo') }}" class="profile-photo-link">
                    <img src="{{ asset('uploads/profile_pictures/' . (Auth::user()->profile_photo ?? 'default-avatar.png')) }}"
                         class="profile-photo"
                         onerror="this.src='{{ asset('uploads/profile_pictures/default-buyer.png') }}'">
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
            @yield('content')
        </section>
    </main>
</div>

<div class="overlay" id="overlay"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@yield('scripts')
<script src="{{ asset('js/admin-master.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Sidebar Logic ---
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

        // --- Logout Logic ---
        const logoutButton = document.getElementById('logout-button');
        const logoutTop = document.getElementById('logoutTop');
        const logoutForm = document.getElementById('logout-form');
        const logoutFormTop = document.getElementById('logout-form-top');

        if (logoutButton) {
            logoutButton.addEventListener('click', function(e) {
                e.preventDefault();
                logoutForm.submit();
            });
        }

        if (logoutTop) {
            logoutTop.addEventListener('click', function(e) {
                e.preventDefault();
                logoutFormTop.submit();
            });
        }

        // --- Notification Dropdown Logic ---
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

            document.addEventListener('click', function(e) {
                if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
                    notifDropdown.classList.remove('show');
                    notifDropdown.setAttribute('aria-hidden', 'true');
                }
            });
        }
    });
</script>

</body>
</html>
