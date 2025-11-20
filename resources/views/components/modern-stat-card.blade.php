@props([
    'title',
    'value',
    'icon',
    'color' => 'blue',
    'trend' => null,
    'trendDirection' => 'up',
    'footer' => null,
    'link' => null
])

@php
$colorClasses = [
    'blue' => 'bg-blue-50 text-blue-600',
    'green' => 'bg-green-50 text-green-600',
    'amber' => 'bg-amber-50 text-amber-600',
    'red' => 'bg-red-50 text-red-600',
    'purple' => 'bg-purple-50 text-purple-600',
];
@endphp

<div class="stat-card fade-in">
    @if($link)
    <a href="{{ $link }}" style="text-decoration: none; color: inherit;">
    @endif
    
    <div class="stat-card-header">
        <div>
            <div class="stat-card-label">{{ $title }}</div>
            <div class="stat-card-value">{{ $value }}</div>
        </div>
        <div class="stat-card-icon {{ $color }}">
            <i class="fas fa-{{ $icon }}"></i>
        </div>
    </div>
    
    @if($footer || $trend)
    <div class="stat-card-footer">
        @if($trend)
            <span class="stat-trend {{ $trendDirection }}">
                <i class="fas fa-arrow-{{ $trendDirection === 'up' ? 'up' : 'down' }}"></i>
                {{ $trend }}
            </span>
        @endif
        @if($footer)
            <span>{{ $footer }}</span>
        @endif
    </div>
    @endif
    
    @if($link)
    </a>
    @endif
</div>
