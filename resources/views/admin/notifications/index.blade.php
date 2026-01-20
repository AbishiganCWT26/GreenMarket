@extends('admin.layouts.admin_master')

@section('title', 'Admin Notifications')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-notification.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')

<div class="notification-wrapper">
	<div class="notification-header">
		<h2>Notifications</h2>

		<div class="header-actions">
			<input type="text" id="searchInput" placeholder="Search notifications">
			<button id="markAllRead">Mark All Read</button>
		</div>
	</div>

	<div class="send-box">
		<select id="user_id">
			@foreach($users as $user)
				<option value="{{ $user->id }}">{{ $user->username }} ({{ $user->email }})</option>
			@endforeach
		</select>

		<input type="text" id="title" placeholder="Title">
		<textarea id="message" placeholder="Message"></textarea>
		<button id="sendNotification">Send</button>
	</div>

	<div id="notificationList">
		@include('admin.notifications.partials.notification-list')
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('searchInput').addEventListener('keyup', function(){
	fetch(`{{ route('admin.notifications.search') }}?q=${this.value}`)
	.then(res => res.text())
	.then(html => document.getElementById('notificationList').innerHTML = html)
})

document.getElementById('sendNotification').onclick = function(){
	fetch("{{ route('admin.notifications.send') }}",{
		method:'POST',
		headers:{
			'X-CSRF-TOKEN':'{{ csrf_token() }}',
			'Content-Type':'application/json'
		},
		body:JSON.stringify({
			user_id:document.getElementById('user_id').value,
			title:document.getElementById('title').value,
			message:document.getElementById('message').value
		})
	}).then(res=>res.json()).then(data=>{
		if(data.status==='success'){
			Swal.fire('Success','Notification Sent','success').then(()=>location.reload())
		}else{
			Swal.fire('Error','Failed','error')
		}
	})
}

document.getElementById('markAllRead').onclick = function(){
	fetch("{{ route('admin.notifications.markAllRead') }}",{
		method:'POST',
		headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}
	}).then(()=>location.reload())
}
</script>

@endsection
