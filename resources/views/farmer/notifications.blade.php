@extends('farmer.layouts.farmer_master')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/farmer/notifications.css') }}">
@endsection

@section('content')
<div class="notifications-container">
    <div class="notifications-header">
        <h2>
            <i class="fa-solid fa-bell"></i>
            Notifications Center
        </h2>

        <div class="notifications-controls">
            <button class="btn-refresh" id="refreshNotifications">
                <i class="fa-solid fa-arrows-rotate"></i>
                Refresh
            </button>

            <button class="btn-mark-all" id="markAllReadBtn">
                <i class="fa-solid fa-check-double"></i>
                Mark All Read
            </button>
        </div>
    </div>

    <div class="notifications-stats">
        <div class="stat-card">
            <div class="stat-icon icon-unread">
                <i class="fa-solid fa-envelope"></i>
            </div>
            <div class="stat-info">
                <h3 id="unreadCount">{{ $unreadNotifications }}</h3>
                <p>Unread Notifications</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-total">
                <i class="fa-solid fa-inbox"></i>
            </div>
            <div class="stat-info">
                <h3 id="totalCount">{{ $notifications->total() }}</h3>
                <p>Total Notifications</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-read">
                <i class="fa-solid fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3 id="readCount">{{ $notifications->total() - $unreadNotifications }}</h3>
                <p>Read Notifications</p>
            </div>
        </div>
    </div>

    <div class="notifications-list">
        <div class="notifications-filter">
            <button class="filter-btn active" data-filter="all">
                <i class="fa-solid fa-layer-group"></i>
                All
            </button>
            <button class="filter-btn" data-filter="unread">
                <i class="fa-solid fa-envelope"></i>
                Unread
            </button>
            <button class="filter-btn" data-filter="order">
                <i class="fa-solid fa-shopping-cart"></i>
                Orders
            </button>
            <button class="filter-btn" data-filter="system">
                <i class="fa-solid fa-gear"></i>
                System
            </button>
        </div>

        @if($notifications->count() > 0)
            <div class="notifications-items" id="notificationsItems">
                @foreach($notifications as $notification)
                    @php
                        $iconClass = 'icon-info';
                        $typeClass = 'type-system';
                        $typeLabel = 'System';

                        if($notification->notification_type == 'order_payment') {
                            $iconClass = 'icon-order';
                            $typeClass = 'type-order';
                            $typeLabel = 'Order';
                        } elseif($notification->notification_type == 'admin_alert') {
                            $iconClass = 'icon-admin';
                            $typeClass = 'type-admin';
                            $typeLabel = 'Admin';
                        }
                    @endphp

                    <div class="notification-item {{ $notification->is_read ? 'read' : 'unread' }}"
                         data-type="{{ $notification->notification_type }}"
                         data-id="{{ $notification->id }}"
                         data-read="{{ $notification->is_read }}">
                        <div class="notification-icon {{ $iconClass }}">
                            @if($notification->notification_type == 'order_payment')
                                <i class="fa-solid fa-shopping-cart"></i>
                            @elseif($notification->notification_type == 'admin_alert')
                                <i class="fa-solid fa-shield-halved"></i>
                            @else
                                <i class="fa-solid fa-info-circle"></i>
                            @endif
                        </div>

                        <div class="notification-content">
                            <div class="notification-header">
                                <h4 class="notification-title">
                                    {{ $notification->title }}
                                    @if(!$notification->is_read)
                                        <span class="unread-dot"></span>
                                    @endif
                                </h4>
                                <span class="notification-type {{ $typeClass }}">
                                    {{ $typeLabel }}
                                </span>
                            </div>

                            <p class="notification-message">
                                {{ $notification->message }}
                            </p>

                            <div class="notification-footer">
                                <span class="notification-time">
                                    <i class="fa-solid fa-clock"></i>
                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                </span>

                                <div class="notification-actions">
                                    @if(!$notification->is_read)
                                        <button class="action-btn mark-read-btn" title="Mark as Read">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($notifications->hasPages())
                <div class="pagination-wrapper">
                    <nav>
                        <ul class="pagination">
                            @if($notifications->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $notifications->previousPageUrl() }}">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            @foreach(range(1, $notifications->lastPage()) as $page)
                                @if($page == $notifications->currentPage())
                                    <li class="page-item active">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $notifications->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            @if($notifications->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $notifications->nextPageUrl() }}">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            @endif
        @else
            <div class="notifications-empty">
                <div class="empty-icon">
                    <i class="fa-regular fa-bell-slash"></i>
                </div>
                <h3>No Notifications</h3>
                <p>You're all caught up! When you get new notifications, they'll appear here.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Function to update statistics
    function updateStats() {
        const unreadItems = document.querySelectorAll('.notification-item.unread').length;
        const totalItems = document.querySelectorAll('.notification-item').length;
        const readItems = totalItems - unreadItems;

        document.getElementById('unreadCount').textContent = unreadItems;
        document.getElementById('totalCount').textContent = totalItems;
        document.getElementById('readCount').textContent = readItems;
    }

    // Refresh button
    const refreshBtn = document.getElementById('refreshNotifications');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            refreshBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Refreshing';
            refreshBtn.disabled = true;

            setTimeout(() => {
                window.location.reload();
            }, 1000);
        });
    }

    // Mark all as read button
    const markAllBtn = document.getElementById('markAllReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'Mark All as Read?',
                text: 'This will mark all notifications as read.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, mark all',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("farmer.notifications.mark-all-read") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update all notification items
                            document.querySelectorAll('.notification-item').forEach(item => {
                                item.classList.remove('unread');
                                item.classList.add('read');
                                item.setAttribute('data-read', '1');

                                const dot = item.querySelector('.unread-dot');
                                if (dot) dot.remove();

                                const markBtn = item.querySelector('.mark-read-btn');
                                if (markBtn) markBtn.remove();
                            });

                            // Update statistics
                            updateStats();

                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'All notifications marked as read!',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to mark notifications as read.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to mark notifications as read.'
                        });
                    });
                }
            });
        });
    }

    // Filter buttons
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');

            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            document.querySelectorAll('.notification-item').forEach(item => {
                const type = item.getAttribute('data-type');
                const isUnread = item.classList.contains('unread');

                let showItem = true;

                if (filter === 'unread' && !isUnread) {
                    showItem = false;
                } else if (filter === 'order' && type !== 'order_payment') {
                    showItem = false;
                } else if (filter === 'system' && type !== 'admin_alert') {
                    showItem = false;
                }

                if (showItem) {
                    item.style.display = 'flex';
                    item.style.animation = 'fadeInItem 0.5s ease';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // Handle mark as read button clicks using event delegation
    document.addEventListener('click', function(e) {
        // Mark as read button
        if (e.target.closest('.mark-read-btn')) {
            e.preventDefault();
            e.stopPropagation();

            const markBtn = e.target.closest('.mark-read-btn');
            const item = markBtn.closest('.notification-item');
            const notificationId = item.getAttribute('data-id');

            if (!notificationId) return;

            fetch('{{ route("farmer.notifications.mark-read") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id: notificationId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the notification item
                    item.classList.remove('unread');
                    item.classList.add('read');
                    item.setAttribute('data-read', '1');

                    const dot = item.querySelector('.unread-dot');
                    if (dot) dot.remove();

                    markBtn.remove();

                    // Update statistics
                    updateStats();

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Notification marked as read!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to mark notification as read.'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to mark notification as read.'
                });
            });
        }

        // Click on notification item (mark as read if unread)
        if (e.target.closest('.notification-item') && !e.target.closest('.notification-actions')) {
            const item = e.target.closest('.notification-item');
            if (item.classList.contains('unread')) {
                const notificationId = item.getAttribute('data-id');
                const markBtn = item.querySelector('.mark-read-btn');

                if (notificationId) {
                    fetch('{{ route("farmer.notifications.mark-read") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ id: notificationId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            item.classList.remove('unread');
                            item.classList.add('read');
                            item.setAttribute('data-read', '1');

                            const dot = item.querySelector('.unread-dot');
                            if (dot) dot.remove();

                            if (markBtn) markBtn.remove();

                            updateStats();
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            }
        }
    });

    // Initialize statistics
    updateStats();
});
</script>
@endsection
