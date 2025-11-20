@props(['label', 'value', 'highlight' => false])

<div class="detail-row">
  <span class="detail-label">{{ $label }}</span>
  <span class="detail-value {{ $highlight ? 'text-highlight' : '' }}">{{ $value }}</span>
</div>
