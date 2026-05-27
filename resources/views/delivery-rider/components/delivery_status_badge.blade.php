@php
    $status = strtolower($dispatch->dispatch_status ?? 'unassigned');
    
    // Config mapping for status badge
    $config = [
        'assigned' => ['class' => 'status-assigned', 'icon' => 'fa-user-check', 'text' => 'Assigned'],
        'arrived_district' => ['class' => 'status-arrived', 'icon' => 'fa-location-dot', 'text' => 'Arrived District'],
        'delivered' => ['class' => 'status-delivered', 'icon' => 'fa-check-double', 'text' => 'Delivered'],
        'unassigned' => ['class' => 'status-unassigned', 'icon' => 'fa-satellite-dish', 'text' => 'Unassigned'],
        'crisis' => ['class' => 'status-crisis', 'icon' => 'fa-triangle-exclamation', 'text' => 'Crisis'],
        'in_transit' => ['class' => 'status-arrived', 'icon' => 'fa-truck-fast', 'text' => 'In Transit'],
    ];

    $current = $config[$status] ?? $config['unassigned'];
@endphp

<div class="delivery-status-badge {{ $current['class'] }}">
    <i class="fa-solid {{ $current['icon'] }}"></i>
    <span>{{ $current['text'] }}</span>
</div>
