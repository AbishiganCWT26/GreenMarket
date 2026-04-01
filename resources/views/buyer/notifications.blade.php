@extends('buyer.layouts.buyer_master')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/buyer/notifications.css') }}">
@endsection

@section('content')
    <div class="notifications-wrapper">
        <div class="notifications-container">
            <div class="notifications-header">
                <div class="header-title">
                    <i class="fas fa-bell"></i>
                    <h2>Notifications</h2>
                </div>
                <div class="header-actions">
                    <button type="button" class="action-btn refresh-btn" onclick="refreshNotifications()">
                        <i class="fas fa-sync-alt"></i>
                        <span>Refresh</span>
                    </button>
                    <button type="button" class="action-btn markall-btn" onclick="markAllRead()">
                        <i class="fas fa-check-double"></i>
                        <span>Mark all read</span>
                    </button>
                </div>
            </div>
            <div class="notifications-list">
                @if(isset($notifications) && count($notifications) > 0)
                    @foreach($notifications as $n)
                        <div class="notification-item {{ $n->is_read ? 'read' : 'unread' }}" id="notif-{{ $n->id }}">
                            <div class="notification-icon {{ $n->is_read ? 'icon-read' : 'icon-unread' }}">
                                <i class="fas {{ $n->is_read ? 'fa-circle-info' : 'fa-bell' }}"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-top">
                                    <h4 class="notification-title">{{ $n->title }}</h4>
                                    @if(!$n->is_read)
                                        <span class="notification-badge">New</span>
                                    @endif
                                </div>
                                <p class="notification-message">{{ $n->message }}</p>
                                <div class="notification-meta">
                                    <span class="notification-time">
                                        <i class="far fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}
                                    </span>
                                    @if(!$n->is_read)
                                        <button class="mark-read-btn" onclick="markAsRead({{ $n->id }})" title="Mark as read">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fas fa-bell-slash"></i>
                        <h3>No notifications</h3>
                        <p>You're all caught up! New notifications will appear here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function refreshNotifications() {
            Swal.fire({
                title: 'Refreshing',
                text: 'Checking for new notifications...',
                @if(file_exists(public_path('assets/icons/Gif/info1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/info1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'info' @endif,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        }

        function markAllRead() {
            Swal.fire({
                title: 'Mark all as read?',
                text: 'All notifications will be marked as read.',
                @if(file_exists(public_path('assets/icons/Gif/question1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/question1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, mark all'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'All notifications marked as read.',
                        @if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    let items = document.querySelectorAll('.notification-item.unread');
                    items.forEach(item => {
                        item.classList.remove('unread');
                        item.classList.add('read');
                        let badge = item.querySelector('.notification-badge');
                        if (badge) badge.remove();
                        let iconDiv = item.querySelector('.notification-icon');
                        if (iconDiv) {
                            iconDiv.classList.remove('icon-unread');
                            iconDiv.classList.add('icon-read');
                            let icon = iconDiv.querySelector('i');
                            if (icon) icon.classList.remove('fa-bell');
                            if (icon) icon.classList.add('fa-circle-info');
                        }
                        let markBtn = item.querySelector('.mark-read-btn');
                        if (markBtn) markBtn.remove();
                    });
                }
            });
        }

        function markAsRead(id) {
            Swal.fire({
                title: 'Marked as read',
                text: 'Notification has been marked as read.',
                @if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
                timer: 1500,
                showConfirmButton: false
            });
            let item = document.getElementById('notif-' + id);
            if (item) {
                item.classList.remove('unread');
                item.classList.add('read');
                let badge = item.querySelector('.notification-badge');
                if (badge) badge.remove();
                let iconDiv = item.querySelector('.notification-icon');
                if (iconDiv) {
                    iconDiv.classList.remove('icon-unread');
                    iconDiv.classList.add('icon-read');
                    let icon = iconDiv.querySelector('i');
                    if (icon) icon.classList.remove('fa-bell');
                    if (icon) icon.classList.add('fa-circle-info');
                }
                let markBtn = item.querySelector('.mark-read-btn');
                if (markBtn) markBtn.remove();
            }
        }
    </script>
@endsection