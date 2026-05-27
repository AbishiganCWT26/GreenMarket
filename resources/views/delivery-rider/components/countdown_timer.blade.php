@php
    $eta = \Carbon\Carbon::parse($dispatch->estimated_arrival_time);
    $cutoff = $eta->copy()->subMinutes(45);
    $now = \Carbon\Carbon::now();
    $windowOpen = $now->lt($cutoff);
@endphp

<div class="countdown-wrap" id="countdown-wrap-{{ $dispatch->id }}">
    @if($windowOpen)
        <div class="countdown-label">
            <i class="fa-solid fa-hourglass-half"></i>
            Accept window closes in
        </div>
        <div class="countdown-timer" id="timer-{{ $dispatch->id }}"
             data-cutoff="{{ $cutoff->toIso8601String() }}"
             data-eta="{{ $eta->format('M d, h:i A') }}">
            <div class="time-block">
                <span class="time-num" id="h-{{ $dispatch->id }}">00</span>
                <span class="time-unit">h</span>
            </div>
            <span class="time-sep">:</span>
            <div class="time-block">
                <span class="time-num" id="m-{{ $dispatch->id }}">00</span>
                <span class="time-unit">m</span>
            </div>
            <span class="time-sep">:</span>
            <div class="time-block">
                <span class="time-num" id="s-{{ $dispatch->id }}">00</span>
                <span class="time-unit">s</span>
            </div>
        </div>
    @else
        <div class="window-closed">
            <i class="fa-solid fa-lock"></i>
            <span>ACCEPTANCE WINDOW CLOSED</span>
        </div>
    @endif
</div>
