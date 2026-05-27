@php
    $eta = \Carbon\Carbon::parse($dispatch->estimated_arrival_time);
    $cutoff = $eta->copy()->subMinutes(45);
    $now = \Carbon\Carbon::now();
    $windowOpen = $now->lt($cutoff);
@endphp

@if($windowOpen)
    <button class="btn-accept"
            id="accept-{{ $dispatch->id }}"
            data-dispatch-id="{{ $dispatch->id }}"
            data-bus="{{ $dispatch->bus_number }}"
            data-eta="{{ $eta->format('M d, h:i A') }}"
            onclick="event.stopPropagation();">
        <i class="fa-solid fa-circle-check"></i> Accept
    </button>
@else
    <button class="btn-accept disabled" disabled onclick="event.stopPropagation();">
        <i class="fa-solid fa-ban"></i> Closed
    </button>
@endif
