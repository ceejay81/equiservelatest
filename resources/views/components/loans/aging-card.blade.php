@props(['status', 'label', 'amount', 'percentage' => null])

<div class="aging-card aging-card-{{ $status }}">
  <div class="aging-label">{{ $label }}</div>
  <div class="aging-amount">₱{{ number_format($amount, 2) }}</div>
  @if($percentage !== null)
    <small class="aging-percentage">{{ number_format($percentage, 1) }}% of total</small>
  @endif
</div>
