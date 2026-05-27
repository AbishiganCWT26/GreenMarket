@extends('delivery-rider.layouts.delivery_rider_master')

@section('title', '| Notifications')
@section('page-title', 'Notifications Center')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/delivery-rider/notifications.css') }}">
@endsection

@section('content')
<div class="notif-page-container">
    <div class="notif-card">
        <div class="notif-card-header">
            <h2>
                <i class="fa-solid fa-bell"></i>
                All Notifications
                @if($unreadCount > 0)
                    <span class="badge bg-danger ms-2" style="position: static; transform: none;">{{ $unreadCount }} New</span>
                @endif
            </h2>
            
            <div class="notif-actions">
                @if($notifications->where('is_read', false)->count() > 0)
                    <button class="btn-mark-all" id="pageMarkAllRead">
                        <i class="fa-solid fa-check-double"></i>
                        Mark All as Read
                    </button>
                @endif
            </div>
        </div>

        <div class="notif-list-full">
            @forelse($notifications as $notification)
                <div class="notif-full-item {{ $notification->is_read ? '' : 'unread' }}" data-id="{{ $notification->id }}">
                    <div class="notif-full-icon {{ $notification->notification_type == 'admin_alert' ? 'admin-alert' : 'info' }}">
                        @if($notification->notification_type == 'admin_alert')
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        @else
                            <i class="fa-solid fa-info-circle"></i>
                        @endif
                    </div>
                    
                    <div class="notif-full-body">
                        <div class="notif-full-title-row">
                            <h4 class="notif-full-title">{{ $notification->title }}</h4>
                            <span class="notif-full-time">
                                <i class="fa-regular fa-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                            </span>
                        </div>
                        
                        <p class="notif-full-message">{{ $notification->message }}</p>
                        
                        @if(!$notification->is_read)
                            <div class="notif-full-footer">
                                <button class="btn-mark-single mark-page-notif" data-id="{{ $notification->id }}">
                                    Mark as read
                                </button>
                            </div>
                        @else
                           <div class="notif-full-footer">
                                <span class="badge bg-light text-dark" style="position: static; transform: none; font-weight: normal; font-size: 0.7rem;">Read</span>
                           </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="notif-empty-state">
                    <i class="fa-solid fa-bell-slash"></i>
                    <h3>No notifications yet</h3>
                    <p>When you receive notifications, they will appear here.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Mark all as read from page
        $('#pageMarkAllRead').on('click', function() {
            Swal.fire({
                title: 'Mark all as read?',
                text: "All your notifications will be marked as read.",
                @if(file_exists(public_path('assets/icons/Gif/question3.gif'))) 
                    imageUrl: '{{ asset('assets/icons/Gif/question3.gif') }}', imageWidth: 60, imageHeight: 60 
                @else 
                    icon: 'question' 
                @endif,
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, mark all'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("delivery-rider.notifications.mark-all-read") }}', {
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
                            $('.notif-full-item.unread').removeClass('unread');
                            $('.btn-mark-single').fadeOut();
                            $('.btn-mark-all').fadeOut();
                            $('.notif-dot').remove();
                            $('.badge.bg-danger').fadeOut();
                            
                            Swal.fire({
                                @if(file_exists(public_path('assets/icons/Gif/mark as read2.gif'))) 
                                    imageUrl: '{{ asset('assets/icons/Gif/mark as read2.gif') }}', imageWidth: 60, imageHeight: 60 
                                @else 
                                    icon: 'success' 
                                @endif,
                                title: 'Success',
                                text: 'All notifications marked as read',
                                timer: 1500,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            }).then(() => {
                                location.reload();
                            });
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        });

        // Single mark as read from page
        $('.mark-page-notif').on('click', function() {
            const btn = $(this);
            const notifId = btn.data('id');
            const notifItem = btn.closest('.notif-full-item');
            
            let url = "{{ route('delivery-rider.notifications.mark-read', ':id') }}";
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
                    notifItem.removeClass('unread');
                    btn.fadeOut();
                    
                    Swal.fire({
                        @if(file_exists(public_path('assets/icons/Gif/mark as read2.gif'))) 
                            imageUrl: '{{ asset('assets/icons/Gif/mark as read2.gif') }}', imageWidth: 60, imageHeight: 60 
                        @else 
                            icon: 'success' 
                        @endif,
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
    });
</script>
@endsection
