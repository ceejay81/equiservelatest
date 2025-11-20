@props(['title', 'type' => 'default', 'icon' => null])

<div class="info-card {{ $type !== 'default' ? $type : '' }}">
  @if($icon)
    <div class="info-card-title">
      <i class="fas fa-{{ $icon }} mr-1"></i>{{ $title }}
    </div>
  @else
    <div class="info-card-title">{{ $title }}</div>
  @endif
  
  {{ $slot }}
</div>
