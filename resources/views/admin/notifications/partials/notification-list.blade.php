@php
	$perPage = request()->get('per_page', request()->input('view') === 'card' ? 10 : 15);
	$totalCount = method_exists($notifications, 'total') ? $notifications->total() : $notifications->count();
	$view = request()->get('view', 'card');
@endphp

<input type="hidden" id="totalNotificationsCount" value="{{ $totalCount }}">

@if($view === 'card')
	<div class="notification-grid">
		@forelse($notifications as $notification)
			<div class="notification-card {{ $notification->is_read ? 'read' : 'unread' }}">
				<div class="card-top">
					<span class="type">{{ ucfirst(str_replace('_',' ',$notification->notification_type ?? 'system')) }}</span>
					<span class="time">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</span>
				</div>
				<h4>{{ $notification->title }}</h4>
				<p>{{ Str::limit($notification->message, 100) }}</p>
				<div class="card-bottom">
					<span>{{ $notification->user?->username ?? 'System' }}</span>
					@if(!$notification->is_read)
						<button class="mark-read-btn" onclick="window.markRead({{ $notification->id }})">
							Mark Read
						</button>
					@else
						<span style="color:var(--dark-green);">✓ Read</span>
					@endif
				</div>
			</div>
		@empty
			<div class="empty-state" style="grid-column:1/-1;">
				<p>No notifications found</p>
			</div>
		@endforelse
	</div>
@else
	<div class="notification-table">
		<table>
			<thead>
				<tr>
					<th>Status</th>
					<th>Type</th>
					<th>Title</th>
					<th>Message</th>
					<th>User</th>
					<th>Time</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				@forelse($notifications as $notification)
					<tr class="{{ !$notification->is_read ? 'unread-row' : '' }}">
						<td>
							<span class="status-badge {{ $notification->is_read ? 'read' : 'unread' }}">
								{{ $notification->is_read ? 'Read' : 'Unread' }}
							</span>
						</td>
						<td>{{ ucfirst(str_replace('_',' ',$notification->notification_type ?? 'system')) }}</td>
						<td style="font-weight: {{ !$notification->is_read ? '600' : '400' }};">
							{{ Str::limit($notification->title, 30) }}
						</td>
						<td>{{ Str::limit($notification->message, 50) }}</td>
						<td>{{ $notification->user?->username ?? 'System' }}</td>
						<td>{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</td>
						<td>
							@if(!$notification->is_read)
								<button class="mark-read-btn" onclick="window.markRead({{ $notification->id }})" style="padding:4px 12px;">
									✓ Mark Read
								</button>
							@else
								<span style="color:var(--dark-green);font-size:12px;">✓ Read</span>
							@endif
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="7" style="text-align:center;padding:40px;">
							<div class="empty-state">
								<p>No notifications found</p>
							</div>
						</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>
@endif