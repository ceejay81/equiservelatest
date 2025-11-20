@props(['icon', 'color' => 'blue', 'label', 'value', 'meta' => null])

<div class="stat-card">
  <div class="stat-icon {{ $color }}">
    <i class="fas fa-{{ $icon }}"></i>
  </div>
  <div class="stat-label">{{ $label }}</div>
  <div class="stat-value">{{ $value }}</div>
  @if($meta)
    <div class="stat-meta">{{ $meta }}</div>
  @endif
</div>
