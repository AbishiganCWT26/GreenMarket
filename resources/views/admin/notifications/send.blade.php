@extends('admin.layouts.admin_master')

@section('title', 'Send Notification')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin-notification.css') }}">
@endsection

@section('content')
<div class="send-notification-container">
    <div class="send-notification-header">
        <h1><i class="fas fa-paper-plane"></i> Send Notification</h1>
        <a href="{{ route('admin.notifications.index') }}" class="back-to-list">
            <i class="fas fa-arrow-left"></i> Back to Notifications
        </a>
    </div>

    <div class="send-notification-card">
        <form id="sendNotificationForm">
            @csrf
            <div class="form-section">
                <h3><i class="fas fa-users"></i> Recipient Details</h3>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="recipient_type"><i class="fas fa-bullseye"></i> Recipient Type *</label>
                        <select id="recipient_type" name="recipient_type" class="form-control" required>
                            <option value="">Select Recipient Type</option>
                            @foreach($recipientTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width" id="userRecipientField" style="display: none;">
                        <label for="recipient_id"><i class="fas fa-user"></i> Select User *</label>
                        <select id="recipient_id" name="recipient_id" class="form-control">
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->username }} ({{ $user->email }}) - {{ ucfirst($user->role) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group full-width" id="addressRecipientField" style="display: none;">
                        <label for="recipient_address"><i class="fas fa-address-book"></i> Recipient Address *</label>
                        <input type="text" id="recipient_address" name="recipient_address" class="form-control" placeholder="Enter email or mobile number">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3><i class="fas fa-cog"></i> Notification Details</h3>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="notification_type"><i class="fas fa-tag"></i> Notification Type *</label>
                        <select id="notification_type" name="notification_type" class="form-control" required>
                            <option value="">Select Type</option>
                            @foreach($notificationTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="title"><i class="fas fa-heading"></i> Title *</label>
                        <input type="text" id="title" name="title" class="form-control" placeholder="Enter notification title" required maxlength="255">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="message"><i class="fas fa-comment-alt"></i> Message *</label>
                        <textarea id="message" name="message" class="form-control" rows="6" placeholder="Enter notification message" required></textarea>
                        <div class="char-count">
                            <span id="charCount">0</span> characters
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" id="previewNotification" class="preview-btn">
                    <i class="fas fa-eye"></i> Preview
                </button>
                <button type="submit" id="sendNotificationBtn" class="send-btn">
                    <i class="fas fa-paper-plane"></i> Send Notification
                </button>
                <button type="button" id="cancelBtn" class="cancel-btn">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });

    function showSuccess(message) {
        Toast.fire({
            icon: 'success',
            title: message,
            background: '#f0f9f4',
            color: '#065f46'
        });
    }

    function showError(message) {
        Toast.fire({
            icon: 'error',
            title: message,
            background: '#fef2f2',
            color: '#991b1b'
        });
    }

    const recipientType = document.getElementById('recipient_type');
    const userRecipientField = document.getElementById('userRecipientField');
    const addressRecipientField = document.getElementById('addressRecipientField');
    const messageTextarea = document.getElementById('message');
    const charCount = document.getElementById('charCount');

    recipientType.addEventListener('change', function() {
        const value = this.value;

        userRecipientField.style.display = 'none';
        addressRecipientField.style.display = 'none';

        document.getElementById('recipient_id').removeAttribute('required');
        document.getElementById('recipient_address').removeAttribute('required');

        if (value === 'user') {
            userRecipientField.style.display = 'block';
            document.getElementById('recipient_id').setAttribute('required', 'required');
        } else if (value === 'farmer_mobile' || value === 'farmer_email') {
            addressRecipientField.style.display = 'block';
            document.getElementById('recipient_address').setAttribute('required', 'required');
        }
    });

    messageTextarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    document.getElementById('previewNotification').addEventListener('click', function() {
        const title = document.getElementById('title').value;
        const message = document.getElementById('message').value;
        const recipientType = document.getElementById('recipient_type').value;
        const notificationType = document.getElementById('notification_type').value;

        if (!title || !message || !recipientType || !notificationType) {
            showError('Please fill all required fields before preview');
            return;
        }

        let recipientInfo = '';
        if (recipientType === 'user') {
            const userId = document.getElementById('recipient_id').value;
            const selectedOption = document.querySelector(`#recipient_id option[value="${userId}"]`);
            recipientInfo = selectedOption ? selectedOption.text : 'Specific User';
        } else if (recipientType === 'system_wide') {
            recipientInfo = 'All Users (System Wide)';
        } else {
            recipientInfo = document.getElementById('recipient_address').value;
        }

        Swal.fire({
            title: 'Notification Preview',
            html: `
                <div class="preview-container">
                    <div class="preview-header">
                        <h4>${title}</h4>
                        <span class="preview-badge">${notificationType.replace('_', ' ').toUpperCase()}</span>
                    </div>
                    <div class="preview-body">
                        <p>${message}</p>
                    </div>
                    <div class="preview-footer">
                        <div><strong>To:</strong> ${recipientInfo}</div>
                        <div><strong>Type:</strong> ${recipientType.replace('_', ' ')}</div>
                        <div><strong>Time:</strong> Now</div>
                    </div>
                </div>
            `,
            showCancelButton: false,
            confirmButtonText: 'OK',
            confirmButtonColor: '#10B981',
            width: '600px'
        });
    });

    document.getElementById('sendNotificationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        Swal.fire({
            title: 'Send Notification?',
            text: 'Are you sure you want to send this notification?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, send it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const sendBtn = document.getElementById('sendNotificationBtn');
                const originalText = sendBtn.innerHTML;
                sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                sendBtn.disabled = true;

                fetch('{{ route("admin.notifications.send.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccess(data.message);

                        setTimeout(() => {
                            window.location.href = '{{ route("admin.notifications.index") }}';
                        }, 1500);
                    } else {
                        showError(data.message);
                        sendBtn.innerHTML = originalText;
                        sendBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('An error occurred. Please try again.');
                    sendBtn.innerHTML = originalText;
                    sendBtn.disabled = false;
                });
            }
        });
    });

    document.getElementById('cancelBtn').addEventListener('click', function() {
        Swal.fire({
            title: 'Cancel Notification?',
            text: 'Are you sure you want to cancel? Any unsaved changes will be lost.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, cancel',
            cancelButtonText: 'Continue editing'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route("admin.notifications.index") }}';
            }
        });
    });

    const style = document.createElement('style');
    style.textContent = `
        .preview-container {
            text-align: left;
            padding: 10px;
        }
        .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        .preview-header h4 {
            margin: 0;
            color: #0f1724;
            font-size: 18px;
        }
        .preview-badge {
            background: #10B981;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .preview-body {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #10B981;
        }
        .preview-body p {
            margin: 0;
            color: #475569;
            line-height: 1.6;
        }
        .preview-footer {
            background: #f1f5f9;
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
        }
        .preview-footer div {
            margin-bottom: 5px;
        }
        .preview-footer strong {
            color: #0f1724;
        }
    `;
    document.head.appendChild(style);
});
</script>
@endsection
