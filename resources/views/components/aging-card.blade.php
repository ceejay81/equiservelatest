@props(['type' => 'current', 'title', 'amount', 'percentage' => null])

<div class="aging-card {{ $type }}">
  <div class="aging-card-title" @class(['text-success' => $type === 'current', 'text-warning' => $type === 'warning', 'text-danger' => $type !== 'current' && $type !== 'warning'])>
    {{ $title }}
  </div>
  <div class="aging-card-value">{{ $amount }}</div>
  @if($percentage !== null)
    <div class="aging-card-meta">{{ $percentage }}% of total</div>
  @endif
</div>
