@php
    $icon = $icon ?? 'fa-box-open';
    $title = $title ?? 'No Items Found';
    $message = $message ?? 'There are no items to display at this time.';
    $actionHint = $actionHint ?? '';
    $actionUrl = $actionUrl ?? '';
    $actionText = $actionText ?? 'Back to Dashboard';
@endphp

<div class="empty-state">
    <div class="empty-icon-wrap">
        @if(str_contains($icon, '.svg') || str_contains($icon, '.png'))
            <img src="{{ asset($icon) }}" alt="Empty state" class="empty-illustration">
        @else
            <i class="fa-solid {{ $icon }}"></i>
        @endif
    </div>
    
    <h3 class="empty-title">{{ $title }}</h3>
    
    <p class="empty-message">
        {{ $message }}
        @if($actionHint)
            <br><span class="empty-hint">{{ $actionHint }}</span>
        @endif
    </p>
    
    @if($actionUrl)
        <a href="{{ $actionUrl }}" class="empty-action-link">
            <i class="fa-solid fa-arrow-left"></i> {{ $actionText }}
        </a>
    @endif
</div>
