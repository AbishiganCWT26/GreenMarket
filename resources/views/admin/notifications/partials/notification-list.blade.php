<div class="notification-grid">
@foreach($notifications as $notification)
	<div class="notification-card {{ $notification->is_read ? 'read' : 'unread' }}">
		<div class="card-top">
			<span class="type">{{ ucfirst(str_replace('_',' ',$notification->notification_type)) }}</span>
			<span class="time">{{ $notification->created_at }}</span>
		</div>

		<h4>{{ $notification->title }}</h4>
		<p>{{ $notification->message }}</p>

		<div class="card-bottom">
			<span>{{ $notification->user?->username ?? 'System' }}</span>
			@if(!$notification->is_read)
				<button onclick="markRead({{ $notification->id }})">Mark Read</button>
			@endif
		</div>
	</div>
@endforeach
</div>

<script>
function markRead(id){
	fetch(`{{ url('admin/notifications/mark-read') }}/${id}`,{
		method:'POST',
		headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}
	}).then(()=>location.reload())
}
</script>
