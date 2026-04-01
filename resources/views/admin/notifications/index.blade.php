@extends('admin.layouts.admin_master')

@section('title', 'Admin Notifications')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/Admin/admin-notification.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">endsection
@section('content')
<div class="notification-wrapper">
	<div class="notification-header">
		<h2>Notifications</h2>
		<div class="header-actions">
			<button id="markAllRead">
				<span>Mark All Read</span>
			</button>
		</div>
	</div>

	<div class="send-box">
		<select id="user_id">
			<option value="">Select User</option>
			@foreach($users as $user)
				<option value="{{ $user->id }}">{{ $user->username }} ({{ $user->email }})</option>
			@endforeach
		</select>
		<input type="text" id="title" placeholder="Notification title">
		<textarea id="message" placeholder="Write your message..." rows="1"></textarea>
		<button id="sendNotification">
			<span>Send</span>
		</button>
	</div>

	<div class="view-toggle">
		<button class="view-btn active" data-view="card">
			<span>📱</span> Card View
		</button>
		<button class="view-btn" data-view="table">
			<span>📋</span> Table View
		</button>
	</div>

	<div id="notificationContainer">
		@include('admin.notifications.partials.notification-list', ['view' => 'card'])
	</div>

	<div id="paginationContainer" class="pagination"></div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>

<script>
let currentPage = 1;
let currentView = 'card';

document.addEventListener('DOMContentLoaded', function(){
	initializeEventListeners();
	initializePagination();
});

function initializeEventListeners(){
	const sendButton = document.getElementById('sendNotification');
	const markAllReadButton = document.getElementById('markAllRead');
	const viewButtons = document.querySelectorAll('.view-btn');

	sendButton.addEventListener('click', handleSendNotification);
	markAllReadButton.addEventListener('click', handleMarkAllRead);
	
	viewButtons.forEach(btn => {
		btn.addEventListener('click', function(){
			viewButtons.forEach(b => b.classList.remove('active'));
			this.classList.add('active');
			currentView = this.dataset.view;
			currentPage = 1;
			loadNotifications();
		});
	});
}



async function handleSendNotification(){
	const userId = document.getElementById('user_id').value;
	const title = document.getElementById('title').value;
	const message = document.getElementById('message').value;

	if(!userId || !title || !message){
		Swal.fire({
			@if(file_exists(public_path('assets/icons/Gif/Validation Error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Validation Error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
			title: 'Validation Error',
			text: 'All fields are required!',
			timer: 3000,
			showConfirmButton: true
		});
		return;
	}

	try{
		const response = await fetch("{{ route('admin.notifications.send') }}", {
			method: 'POST',
			headers: {
				'X-CSRF-TOKEN': '{{ csrf_token() }}',
				'Content-Type': 'application/json',
				'Accept': 'application/json'
			},
			body: JSON.stringify({ user_id: userId, title: title, message: message })
		});

		const data = await response.json();

		if(data.status === 'success'){
			Swal.fire({
				@if(file_exists(public_path('assets/icons/Gif/success2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
				title: 'Success!',
				text: 'Notification sent successfully',
				showConfirmButton: false,
				timer: 1500,
				background: '#ffffff',
				iconColor: '#10B981'
			}).then(() => {
				document.getElementById('title').value = '';
				document.getElementById('message').value = '';
				document.getElementById('user_id').value = '';
				loadNotifications();
			});
		} else {
			throw new Error('Failed to send');
		}
	} catch(error){
		Swal.fire({
			@if(file_exists(public_path('assets/icons/Gif/error3.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error3.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
			title: 'Error',
			text: 'Failed to send notification',
			confirmButtonColor: '#10B981'
		});
	}
}

async function handleMarkAllRead(){
	const result = await Swal.fire({
		title: 'Mark all as read?',
		text: 'This will mark all notifications as read',
		@if(file_exists(public_path('assets/icons/Gif/question2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/question2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
		showCancelButton: true,
		confirmButtonColor: '#10B981',
		cancelButtonColor: '#6b7280',
		confirmButtonText: 'Yes, mark all',
		cancelButtonText: 'Cancel'
	});

	if(result.isConfirmed){
		try{
			const response = await fetch("{{ route('admin.notifications.markAllRead') }}", {
				method: 'POST',
				headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
			});
			
			Swal.fire({
				@if(file_exists(public_path('assets/icons/Gif/success3.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success3.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
				title: 'Done!',
				text: 'All notifications marked as read',
				showConfirmButton: false,
				timer: 1500
			}).then(() => loadNotifications());
		} catch(error){
			Swal.fire({
				@if(file_exists(public_path('assets/icons/Gif/error3.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error3.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
				title: 'Error',
				text: 'Failed to mark all as read'
			});
		}
	}
}

async function handleMarkRead(id){
	try{
		const response = await fetch(`{{ url('admin/notifications/mark-read') }}/${id}`, {
			method: 'POST',
			headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
		});

		Swal.fire({
			@if(file_exists(public_path('assets/icons/Gif/success1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
			title: 'Marked as read',
			showConfirmButton: false,
			timer: 1000,
			toast: true,
			position: 'top-end'
		}).then(() => loadNotifications());
	} catch(error){
		Swal.fire({
			@if(file_exists(public_path('assets/icons/Gif/mark as read fail1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/mark as read fail1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
			title: 'Error',
			text: 'Failed to mark as read'
		});
	}
}

async function loadNotifications(){
	const container = document.getElementById('notificationContainer');
	const perPage = calculatePerPage();
	
	container.innerHTML = '<div style="text-align:center;padding:40px;"><div class="skeleton-loader">Loading notifications...</div></div>';
	
	try{
		const url = `{{ route('admin.notifications.index') }}?page=${currentPage}&view=${currentView}&per_page=${perPage}`;
		console.log('Loading notifications:', url);
		
		const response = await fetch(url, {
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			}
		});
		
		if (!response.ok) {
			throw new Error(`HTTP error! status: ${response.status}`);
		}
		
		const html = await response.text();
		
		if (!html || html.trim() === '') {
			throw new Error('Empty response received');
		}
		
		container.innerHTML = html;
		initializePagination();
		
		console.log('Notifications loaded successfully');
	} catch(error){
		console.error('Error loading notifications:', error);
		container.innerHTML = '<div class="empty-state"><p>Failed to load notifications. Please try again.</p></div>';
	}
}

function calculatePerPage(){
	const width = window.innerWidth;
	if(width <= 480) return currentView === 'card' ? 4 : 5;
	if(width <= 767) return currentView === 'card' ? 6 : 8;
	if(width <= 991) return currentView === 'card' ? 8 : 12;
	return currentView === 'card' ? 10 : 15;
}

function initializePagination(){
	const paginationContainer = document.getElementById('paginationContainer');
	const totalItems = parseInt(document.getElementById('totalNotificationsCount')?.value || 0);
	const perPage = calculatePerPage();
	const totalPages = Math.ceil(totalItems / perPage);

	if(totalPages <= 1){
		paginationContainer.innerHTML = '';
		return;
	}

	let paginationHtml = `
		<button class="pagination-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">
			<span>←</span> Prev
		</button>
		<div class="pagination-numbers">
	`;

	for(let i = 1; i <= totalPages; i++){
		if(i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)){
			paginationHtml += `<button class="pagination-number ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
		} else if(i === currentPage - 2 || i === currentPage + 2){
			paginationHtml += `<span style="padding:0 4px;">...</span>`;
		}
	}

	paginationHtml += `
		</div>
		<button class="pagination-btn" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">
			Next <span>→</span>
		</button>
	`;

	paginationContainer.innerHTML = paginationHtml;
}

function changePage(page){
	currentPage = page;
	loadNotifications();
	window.scrollTo({ top: 0, behavior: 'smooth' });
}

window.addEventListener('resize', debounce(function(){
	if(document.getElementById('notificationContainer').children.length > 0){
		loadNotifications();
	}
}, 250));

window.markRead = function(id){
	handleMarkRead(id);
};
</script>
@endsection
