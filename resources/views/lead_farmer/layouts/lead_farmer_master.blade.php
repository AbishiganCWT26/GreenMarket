<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👨‍💼 Lead Farmer Hub | @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/lead_farmer-master.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @yield('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<div class="dashboard-wrapper">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('assets/images/logo-4.png') }}" class="logo">
            <h3>Lead Farmer Panel</h3>
            <button id="sidebar-close" class="sidebar-toggle">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <ul class="main-menu">
                <li>
                    <a href="{{ route('lf.dashboard') }}" class="menu-link {{ request()->routeIs('lf.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
                    </a>
                </li>

                <li class="menu-heading">FARMER MANAGEMENT</li>

                <li>
                    <a href="{{ route('lf.registerFarmer') }}" class="menu-link {{ request()->routeIs('lf.registerFarmer') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-plus"></i><span>Register Farmer</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('lf.manageFarmers') }}" class="menu-link {{ request()->routeIs('lf.manageFarmers') ? 'active' : '' }}">
                        <i class="fa-solid fa-users"></i><span>Manage Farmers</span>
                    </a>
                </li>

                <li class="menu-heading">PRODUCT MANAGEMENT</li>

                <li>
                    <a href="{{ route('lf.addProduct') }}" class="menu-link {{ request()->routeIs('lf.addProduct') ? 'active' : '' }}">
                        <i class="fa-solid fa-plus-circle"></i><span>Add New Product</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('lf.manageProducts') }}" class="menu-link {{ request()->routeIs('lf.manageProducts') ? 'active' : '' }}">
                        <i class="fa-solid fa-box-open"></i><span>Manage Products</span>
                        @if(isset($sharedCounts['lowStockProducts']) && $sharedCounts['lowStockProducts'] > 0)
                            <span class="badge bg-warning">{{ $sharedCounts['lowStockProducts'] }}</span>
                        @endif
                    </a>
                </li>

                <li class="menu-heading">ORDERS & SALES</li>

                <li>
                    <a href="{{ route('lf.orders') }}" class="menu-link {{ request()->routeIs('lf.orders') ? 'active' : '' }}">
                        <i class="fa-solid fa-shopping-cart"></i><span>View Orders</span>
                        @if(isset($sharedCounts['pendingOrders']) && $sharedCounts['pendingOrders'] > 0)
                            <span class="badge bg-info">{{ $sharedCounts['pendingOrders'] }}</span>
                        @endif
                    </a>
                </li>

                <li>
                    <a href="{{ route('lf.reports.sales') }}" class="menu-link {{ request()->routeIs('lf.reports.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-line"></i><span>Sales Reports</span>
                    </a>
                </li>

                <li class="menu-heading">ACCOUNT</li>

                <li>
                    <a href="{{ route('lf.profile') }}" class="menu-link {{ request()->routeIs('lf.profile') ? 'active' : '' }}">
                        <i class="fa-solid fa-user"></i><span>My Profile</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('lf.profile.photo') }}" class="menu-link {{ request()->routeIs('lf.profile.photo') ? 'active' : '' }}">
                        <i class="fa-solid fa-camera"></i><span>Profile Photo</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('lf.notifications') }}" class="menu-link {{ request()->routeIs('lf.notifications') ? 'active' : '' }}">
                        <i class="fa-solid fa-bell"></i><span>Notifications</span>
                        @if(isset($sharedCounts['unreadNotifications']) && $sharedCounts['unreadNotifications'] > 0)
                            <span class="notif-dot"></span>
                        @endif
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
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
                    <i class="fa-solid fa-seedling accent"></i>
                    @yield('page-title', 'Lead Farmer Dashboard')
                </h1>
            </div>

            <div class="header-right-group">
                <div class="notif-wrapper">
                    <div class="notif-btn" id="notifBtn">
                        <i class="fa-regular fa-bell"></i>
                        @if(isset($sharedCounts['unreadNotifications']) && $sharedCounts['unreadNotifications'] > 0)
                            <span class="notif-dot"></span>
                        @endif
                    </div>

                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <span>Notifications</span>
                            <button class="mark-all-read" id="markAllRead">Mark all read</button>
                        </div>

                        <div class="notif-list">
                            @if(isset($recentNotifications) && count($recentNotifications) > 0)
                                @foreach($recentNotifications as $notification)
                                <div class="notif-item" data-id="{{ $notification->id }}">
                                    <div class="notif-icon">
                                        @if($notification->notification_type == 'order_payment')
                                            <i class="fa-solid fa-money-bill-wave text-success"></i>
                                        @elseif($notification->notification_type == 'admin_alert')
                                            <i class="fa-solid fa-triangle-exclamation text-warning"></i>
                                        @else
                                            <i class="fa-solid fa-info-circle text-info"></i>
                                        @endif
                                    </div>
                                    <div class="notif-content">
                                        <div class="notif-title">{{ $notification->title }}</div>
                                        <div class="notif-msg">{{ Str::limit($notification->message, 80) }}</div>
                                        <small class="notif-time">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="notif-empty">No notifications</div>
                            @endif
                        </div>

                        <div class="notif-footer">
                            <a href="{{ route('lf.notifications') }}" id="viewAllNotifications">View all notifications</a>
                        </div>
                    </div>
                </div>

                <div class="header-user-meta">
                    <span class="role">Lead Farmer</span>
                    <span class="username">
                        {{ Auth::user()->leadFarmer->name ?? Auth::user()->username ?? 'Lead Farmer' }}
                    </span>
                </div>

                <a href="{{ route('lf.profile') }}" class="profile-photo-link" id="headerProfilePhotoLink">
                    <img src="{{ asset('uploads/profile_pictures/' . (Auth::user()->profile_photo ?? 'lead-farmer-icon.svg')) }}"
                         class="profile-photo"
                         onerror="this.src='{{ asset('assets/icons/lead-farmer-icon.svg') }}'">
                </a>

                <form id="logout-form-top" action="{{ route('logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>

                <div class="logout-icon" id="logoutTop" title="Logout">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
@yield('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebarClose = document.getElementById('sidebar-close');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        // Sidebar toggle functionality
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function() {
                sidebar.classList.add('open');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        }

        if (sidebarClose) {
            sidebarClose.addEventListener('click', function() {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        }

        // Logout functionality
        const logoutButton = document.getElementById('logout-button');
        const logoutTop = document.getElementById('logoutTop');
        const logoutForm = document.getElementById('logout-form');
        const logoutFormTop = document.getElementById('logout-form-top');

        function confirmLogout(formElement) {
            Swal.fire({
                title: 'Logout?',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, logout!'
            }).then((result) => {
                if (result.isConfirmed) {
                    formElement.submit();
                }
            });
        }

        if (logoutButton) {
            logoutButton.addEventListener('click', function(e) {
                e.preventDefault();
                confirmLogout(logoutFormTop);
            });
        }

        if (logoutTop) {
            logoutTop.addEventListener('click', function(e) {
                e.preventDefault();
                confirmLogout(logoutFormTop);
            });
        }

        // Notification dropdown
        const notifBtn = document.getElementById('notifBtn');
        const notifDropdown = document.getElementById('notifDropdown');

        if (notifBtn && notifDropdown) {
            notifBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                notifDropdown.classList.toggle('show');
            });

            const markAllReadBtn = document.getElementById('markAllRead');
            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    fetch('{{ route("lf.notifications.mark-all-read") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelectorAll('.notif-dot').forEach(dot => dot.remove());
                            toastr.success('All notifications marked as read');
                            notifDropdown.classList.remove('show');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('Failed to mark notifications as read');
                    });
                });
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
                    notifDropdown.classList.remove('show');
                }
            });
        }

        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Responsive sidebar
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    // Enhanced lead farmer master functionality
    const leadFarmerMaster = {
        init: function() {
            this.bindEvents();
            this.initTooltips();
            this.autoHideAlerts();
            this.initCounters();
        },

        bindEvents: function() {
            this.bindSidebarToggle();
            this.bindLogout();
            this.bindNotificationDropdown();
            this.bindMenuHoverEffects();
            this.bindCardHoverEffects();
            this.bindQuickActions();
        },

        bindSidebarToggle: function() {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebarClose = document.getElementById('sidebar-close');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            if (mobileMenuBtn && sidebar) {
                mobileMenuBtn.addEventListener('click', () => {
                    sidebar.classList.add('open');
                    overlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                });
            }

            if (sidebarClose && sidebar) {
                sidebarClose.addEventListener('click', () => {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                });
            }

            if (overlay) {
                overlay.addEventListener('click', () => {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                });
            }
        },

        bindLogout: function() {
            const logoutButtons = ['logout-button', 'logoutTop'];

            logoutButtons.forEach(buttonId => {
                const button = document.getElementById(buttonId);
                if (button) {
                    button.addEventListener('click', (e) => {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Logout?',
                            text: 'Are you sure you want to logout?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, logout!',
                            cancelButtonText: 'Cancel',
                            reverseButtons: true,
                            background: '#ffffff',
                            color: '#0f1724',
                            width: '400px'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const formId = buttonId === 'logout-button' ? 'logout-form' : 'logout-form-top';
                                document.getElementById(formId).submit();
                            }
                        });
                    });
                }
            });
        },

        bindNotificationDropdown: function() {
            const notifBtn = document.getElementById('notifBtn');
            const notifDropdown = document.getElementById('notifDropdown');

            if (notifBtn && notifDropdown) {
                notifBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    notifDropdown.classList.toggle('show');
                });

                document.addEventListener('click', (e) => {
                    if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
                        notifDropdown.classList.remove('show');
                    }
                });
            }
        },

        bindMenuHoverEffects: function() {
            const menuLinks = document.querySelectorAll('.menu-link');
            menuLinks.forEach(link => {
                link.addEventListener('mouseenter', () => {
                    link.style.transform = 'translateX(5px)';
                    const icon = link.querySelector('i');
                    if (icon) {
                        icon.style.transform = 'rotate(10deg) scale(1.2)';
                    }
                });

                link.addEventListener('mouseleave', () => {
                    link.style.transform = '';
                    const icon = link.querySelector('i');
                    if (icon) {
                        icon.style.transform = '';
                    }
                });
            });
        },

        bindCardHoverEffects: function() {
            const cards = document.querySelectorAll('.dashboard-card, .stat-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-8px) scale(1.02)';
                    card.style.boxShadow = '0 15px 30px rgba(15,23,36,0.15)';
                });

                card.addEventListener('mouseleave', () => {
                    card.style.transform = '';
                    card.style.boxShadow = '';
                });
            });
        },

        bindQuickActions: function() {
            // Quick action buttons for lead farmer
            const quickAddProduct = document.getElementById('quickAddProduct');
            const quickViewOrders = document.getElementById('quickViewOrders');
            const quickRegisterFarmer = document.getElementById('quickRegisterFarmer');

            if (quickAddProduct) {
                quickAddProduct.addEventListener('click', () => {
                    window.location.href = '{{ route("lf.addProduct") }}';
                });
            }

            if (quickViewOrders) {
                quickViewOrders.addEventListener('click', () => {
                    window.location.href = '{{ route("lf.orders") }}';
                });
            }

            if (quickRegisterFarmer) {
                quickRegisterFarmer.addEventListener('click', () => {
                    window.location.href = '{{ route("lf.registerFarmer") }}';
                });
            }
        },

        initTooltips: function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map((tooltipTriggerEl) => {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        },

        autoHideAlerts: function() {
            setTimeout(() => {
                document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        },

        initCounters: function() {
            // Animate counter values if present
            const counters = document.querySelectorAll('.counter-value');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-count'));
                const duration = 2000;
                const increment = target / (duration / 16);
                let current = 0;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    counter.textContent = Math.round(current);
                }, 16);
            });
        }
    };

    // Initialize lead farmer master
    document.addEventListener('DOMContentLoaded', () => {
        leadFarmerMaster.init();
    });
</script>

</body>
</html>
