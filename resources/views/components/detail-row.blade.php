@props(['label', 'value', 'valueClass' => ''])

<div class="detail-row">
  <span class="detail-label">{{ $label }}</span>
  <span class="detail-value {{ $valueClass }}">{{ $value }}</span>
</div>
