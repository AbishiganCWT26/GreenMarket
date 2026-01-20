@extends('buyer.layouts.buyer_master')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')

<div class="notif-page-wrapper">

    @if(isset($notifications) && count($notifications) > 0)

        @foreach($notifications as $n)
            <div class="notif-row {{ $n->is_read ? 'read' : 'unread' }}">

                <div class="notif-left">
                    <h4>{{ $n->title }}</h4>
                    <p>{{ $n->message }}</p>
                </div>

                <span class="time">
                    {{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}
                </span>

            </div>
        @endforeach

    @else
        <div class="empty-msg">No notifications found.</div>
    @endif

</div>

@endsection
