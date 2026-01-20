@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Notifications')

@section('page-title', 'Notifications')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-bell me-2"></i> Notifications
                    </h5>
                    <div>
                        <button class="btn btn-light btn-sm" id="markAllAsRead">
                            <i class="fas fa-check-double me-1"></i> Mark All as Read
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                    <div class="list-group">
                        @foreach($notifications as $notification)
                        <div class="list-group-item list-group-item-action {{ $notification->is_read ? '' : 'bg-light' }}"
                             id="notification-{{ $notification->id }}">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="d-flex align-items-start">
                                    <div class="me-3">
                                        @if($notification->notification_type == 'order_payment')
                                            <div class="rounded-circle bg-success p-2 text-white">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </div>
                                        @elseif($notification->notification_type == 'admin_alert')
                                            <div class="rounded-circle bg-warning p-2 text-white">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </div>
                                        @else
                                            <div class="rounded-circle bg-info p-2 text-white">
                                                <i class="fas fa-info-circle"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $notification->title }}</h6>
                                        <p class="mb-1">{{ $notification->message }}</p>
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    @if(!$notification->is_read)
                                    <span class="badge bg-danger">New</span>
                                    @endif
                                    <button class="btn btn-sm btn-outline-secondary mark-as-read"
                                            data-id="{{ $notification->id }}"
                                            title="Mark as read">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $notifications->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No notifications</h5>
                        <p class="text-muted">You're all caught up!</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark single notification as read
    document.querySelectorAll('.mark-as-read').forEach(button => {
        button.addEventListener('click', function() {
            const notificationId = this.getAttribute('data-id');
            markAsRead(notificationId);
        });
    });

    // Mark all notifications as read
    document.getElementById('markAllAsRead').addEventListener('click', function() {
        Swal.fire({
            title: 'Mark all as read?',
            text: 'All notifications will be marked as read.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, mark all',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
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
                        // Remove "New" badges and update UI
                        document.querySelectorAll('.badge.bg-danger').forEach(badge => badge.remove());
                        document.querySelectorAll('.list-group-item.bg-light').forEach(item => {
                            item.classList.remove('bg-light');
                        });
                        toastr.success('All notifications marked as read');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Failed to mark notifications as read');
                });
            }
        });
    });

    function markAsRead(notificationId) {
        fetch(`/lead-farmer/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const notificationElement = document.getElementById(`notification-${notificationId}`);
                if (notificationElement) {
                    // Remove "New" badge
                    const badge = notificationElement.querySelector('.badge.bg-danger');
                    if (badge) badge.remove();

                    // Remove highlight
                    notificationElement.classList.remove('bg-light');

                    // Update button
                    const button = notificationElement.querySelector('.mark-as-read');
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-check text-success"></i>';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('Failed to mark notification as read');
        });
    }
});
</script>
@endpush
