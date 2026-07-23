@extends('lead_farmer.layouts.lead_farmer_master')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lead_farmer/notification.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<div class="ntf-wrap">
	<div class="ntf-container">
		<div class="ntf-head">
			<div class="ntf-head-left">
				<i class="fa-solid fa-bell"></i>
				<span class="ntf-head-title">Notifications</span>
				<span class="ntf-count">{{ $notifications->total() }}</span>
			</div>
			<div class="ntf-head-right">
				<button class="ntf-mark-all" id="markAllAsRead">
					<i class="fa-solid fa-check-double"></i>
					<span>Mark all read</span>
				</button>
			</div>
		</div>

		<div id="ntfListContainer">
			@php
				$view = request()->get('view', 'table');
				$perPage = request()->get('per_page', 15);
			@endphp

			@if($view === 'table')
				<div class="ntf-table-wrap">
					<table class="ntf-table">
						<thead>
							<tr>
								<th class="ntf-th-icon"></th>
								<th>Title</th>
								<th>Message</th>
								<th>Time</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@forelse($notifications as $notification)
								<tr class="{{ !$notification->is_read ? 'unread' : '' }}">
									<td>
										<div class="ntf-table-icon">
											@if($notification->notification_type == 'order_payment')
												<i class="fa-solid fa-credit-card"></i>
											@elseif($notification->notification_type == 'admin_alert')
												<i class="fa-solid fa-shield-exclamation"></i>
											@else
												<i class="fa-solid fa-circle-info"></i>
											@endif
										</div>
									</td>
									<td class="ntf-table-title">{{ $notification->title }}</td>
									<td class="ntf-table-msg">{{ Str::limit($notification->message, 50) }}</td>
									<td class="ntf-table-time">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</td>
									<td>
										@if(!$notification->is_read)
											<span class="ntf-table-badge">New</span>
										@else
											<span class="ntf-table-read">Read</span>
										@endif
									</td>
									<td>
										<div class="ntf-table-actions">
											<button class="ntf-table-view btn-view" data-id="{{ $notification->id }}" data-title="{{ $notification->title }}" data-message="{{ $notification->message }}" data-time="{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}" data-unread="{{ !$notification->is_read ? 'true' : 'false' }}">
												<i class="fa-solid fa-eye"></i> View
											</button>
											@if(!$notification->is_read)
												<button class="ntf-table-mark" data-id="{{ $notification->id }}">Mark read</button>
											@endif
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="6" class="ntf-empty-cell">
										<div class="ntf-empty">
											<i class="fa-regular fa-bell-slash"></i>
											<h3>No notifications</h3>
											<p>You’re all caught up</p>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			@else
				<div class="ntf-card-grid" id="ntfCardGrid">
					@forelse($notifications as $notification)
						<div class="ntf-card {{ $notification->is_read ? 'read' : 'unread' }}" id="notification-{{ $notification->id }}">
							<div class="ntf-card-icon">
								@if($notification->notification_type == 'order_payment')
									<i class="fa-solid fa-credit-card"></i>
								@elseif($notification->notification_type == 'admin_alert')
									<i class="fa-solid fa-shield-exclamation"></i>
								@else
									<i class="fa-solid fa-circle-info"></i>
								@endif
							</div>
							<div class="ntf-card-body">
								<div class="ntf-card-title">{{ $notification->title }}</div>
								<div class="ntf-card-msg">{{ $notification->message }}</div>
								<div class="ntf-card-time">
									<i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
								</div>
							</div>
							<div class="ntf-card-actions">
									<button type="button" class="ntf-table-view btn-view" data-id="{{ $notification->id }}" data-title="{{ e($notification->title) }}" data-message="{{ e($notification->message) }}" data-time="{{ e(\Carbon\Carbon::parse($notification->created_at)->diffForHumans()) }}" data-unread="{{ !$notification->is_read ? 'true' : 'false' }}">
										<i class="fa-solid fa-eye"></i> View
									</button>
									@if(!$notification->is_read)
										<button type="button" class="ntf-table-mark" data-id="{{ $notification->id }}">Mark read</button>
									@endif
							</div>
							@if(!$notification->is_read)
								<span class="ntf-card-dot"></span>
							@endif
						</div>
					@empty
						<div class="ntf-empty">
							<i class="fa-regular fa-bell-slash"></i>
							<h3>No notifications</h3>
							<p>You’re all caught up</p>
						</div>
					@endforelse
				</div>
			@endif

		</div>

		<div class="ntf-pagination" id="ntfPagination">
			@if($notifications->hasPages())
				{{ $notifications->appends(['view' => $view, 'per_page' => $perPage])->links('vendor.pagination.simple-unique') }}
			@endif
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
	let currentView = 'table';
	let currentPerPage = 15;
	let currentPage = 1;

	function determineViewAndPerPage() {
		const w = window.innerWidth;
		if (w > 800) {
			currentView = 'table';
			currentPerPage = 15;
		} else if (w >= 600) {
			currentView = 'card';
			currentPerPage = 10;
		} else if (w >= 400) {
			currentView = 'card';
			currentPerPage = 8;
		} else {
			currentView = 'card';
			currentPerPage = 5;
		}
	}

	determineViewAndPerPage();

	const listContainer = document.getElementById('ntfListContainer');
	const paginationContainer = document.getElementById('ntfPagination');

	function showLoading() {
		listContainer.innerHTML = '<div class="ntf-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>';
	}

	function fetchNotifications() {
		showLoading();
		fetch(`{{ route('lf.notifications') }}?view=${currentView}&page=${currentPage}&per_page=${currentPerPage}`, {
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			}
		})
		.then(response => response.text())
		.then(html => {
			const temp = document.createElement('div');
			temp.innerHTML = html;
			const newList = temp.querySelector('#ntfListContainer');
			const newPagination = temp.querySelector('#ntfPagination');
			if (newList) {
				listContainer.innerHTML = newList.innerHTML;
			} else {
				listContainer.innerHTML = html;
			}
			if (newPagination) {
				paginationContainer.innerHTML = newPagination.innerHTML;
				attachPaginationEvents();
			} else {
				paginationContainer.innerHTML = '';
			}
			attachCardEvents();
			attachTableEvents();
		})
		.catch(() => {
			listContainer.innerHTML = '<div class="ntf-empty"><i class="fa-regular fa-bell-slash"></i><h3>Error loading</h3><p>Please try again.</p></div>';
			Swal.fire({
				icon: 'error',
				title: 'Error',
				text: 'Unable to load notifications',
				confirmButtonColor: '#0f9d70',
				customClass: { popup: 'swal-compact' }
			});
		});
	}

	function attachPaginationEvents() {
		document.querySelectorAll('#ntfPagination .page-link').forEach(link => {
			link.addEventListener('click', function (e) {
				e.preventDefault();
				const url = new URL(this.href);
				const page = url.searchParams.get('page');
				if (page) {
					currentPage = parseInt(page);
					fetchNotifications();
				}
			});
		});
	}

	function attachCardEvents() {
		document.querySelectorAll('.ntf-card-mark').forEach(btn => {
			btn.addEventListener('click', function () {
				markAsRead(this.dataset.id);
			});
		});
		document.querySelectorAll('.ntf-card-view').forEach(btn => {
			btn.addEventListener('click', function () {
				showNotificationDetails(this);
			});
		});
	}

	function attachTableEvents() {
		document.querySelectorAll('.ntf-table-mark').forEach(btn => {
			btn.addEventListener('click', function () {
				markAsRead(this.dataset.id);
			});
		});
		document.querySelectorAll('.ntf-table-view').forEach(btn => {
			btn.addEventListener('click', function () {
				showNotificationDetails(this);
			});
		});
	}

	function showNotificationDetails(btn) {
		const id = btn.dataset.id;
		const title = btn.dataset.title;
		const message = btn.dataset.message;
		const time = btn.dataset.time;
		const isUnread = btn.dataset.unread === 'true';

		Swal.fire({
			title: title,
			html: `<div class="ntf-swal-details">
					<p class="ntf-swal-message">${message}</p>
					<span class="ntf-swal-time"><i class="fa-regular fa-clock"></i> ${time}</span>
				   </div>`,
			icon: 'info',
			confirmButtonText: 'Close',
			confirmButtonColor: '#0f9d70',
			customClass: { popup: 'swal-compact' }
		}).then(() => {
			if (isUnread) {
				markAsRead(id);
			}
		});
	}

	function markAsRead(id) {
		fetch(`/lead-farmer/notifications/${id}/read`, {
			method: 'POST',
			headers: {
				'X-CSRF-TOKEN': '{{ csrf_token() }}',
				'Content-Type': 'application/json'
			}
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				const card = document.getElementById(`notification-${id}`);
				if (card) {
					card.classList.remove('unread');
					card.classList.add('read');
					const dot = card.querySelector('.ntf-card-dot');
					if (dot) dot.remove();
					const actions = card.querySelector('.ntf-card-actions');
					if (actions) actions.remove();
				}
				const rowBtn = document.querySelector(`.ntf-table-mark[data-id="${id}"]`);
				if (rowBtn) {
					const row = rowBtn.closest('tr');
					row.classList.remove('unread');
					const badge = row.querySelector('.ntf-table-badge');
					if (badge) badge.outerHTML = '<span class="ntf-table-read">Read</span>';
					rowBtn.outerHTML = '<span class="ntf-table-dot">•</span>';
				}
				Swal.fire({
					icon: 'success',
					title: 'Marked as read',
					timer: 1200,
					showConfirmButton: false,
					customClass: { popup: 'swal-toast' }
				});
			} else {
				Swal.fire({
					icon: 'error',
					title: 'Failed',
					text: 'Could not mark as read',
					confirmButtonColor: '#0f9d70',
					customClass: { popup: 'swal-compact' }
				});
			}
		})
		.catch(() => {
			Swal.fire({
				icon: 'error',
				title: 'Error',
				text: 'Network error',
				confirmButtonColor: '#0f9d70',
				customClass: { popup: 'swal-compact' }
			});
		});
	}

	document.getElementById('markAllAsRead').addEventListener('click', function () {
		Swal.fire({
			title: 'Mark all as read?',
			text: 'This will clear all unread notifications.',
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#0f9d70',
			cancelButtonColor: '#94a3b8',
			confirmButtonText: 'Yes',
			cancelButtonText: 'Cancel',
			customClass: { popup: 'swal-compact' }
		}).then((result) => {
			if (result.isConfirmed) {
				fetch('{{ route("lf.notifications.mark-all-read") }}', {
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': '{{ csrf_token() }}',
						'Content-Type': 'application/json'
					}
				})
				.then(res => res.json())
				.then(data => {
					if (data.success) {
						fetchNotifications();
						Swal.fire({
							icon: 'success',
							title: 'All marked read',
							timer: 1400,
							showConfirmButton: false,
							customClass: { popup: 'swal-toast' }
						});
					} else {
						Swal.fire({
							icon: 'error',
							title: 'Error',
							text: 'Could not complete',
							confirmButtonColor: '#0f9d70',
							customClass: { popup: 'swal-compact' }
						});
					}
				})
				.catch(() => {
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: 'Network failure',
						confirmButtonColor: '#0f9d70',
						customClass: { popup: 'swal-compact' }
					});
				});
			}
		});
	});

	let resizeTimer;
	window.addEventListener('resize', function () {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(function () {
			const prevView = currentView;
			const prevPerPage = currentPerPage;
			determineViewAndPerPage();
			if (currentView !== prevView || currentPerPage !== prevPerPage) {
				currentPage = 1;
				fetchNotifications();
			}
		}, 200);
	});

	// Check if initial server-rendered view matches client screen size. If not, fetch immediately.
	const serverView = '{{ $view }}';
	const serverPerPage = parseInt('{{ $perPage }}', 10);
	if (serverView !== currentView || serverPerPage !== currentPerPage) {
		fetchNotifications();
	} else {
		attachPaginationEvents();
		attachCardEvents();
		attachTableEvents();
	}
});
</script>
@endsection
