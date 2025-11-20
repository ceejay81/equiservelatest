@props(['icon', 'color' => 'blue', 'label', 'value', 'meta' => null])

<div class="stat-card stat-card-{{ $color }}">
  <div class="stat-icon">
    <i class="fas fa-{{ $icon }}"></i>
  </div>
  <div class="stat-label">{{ $label }}</div>
  <div class="stat-value">{{ $value }}</div>
  @if($meta)
    <small class="stat-meta">{{ $meta }}</small>
  @endif
</div>
