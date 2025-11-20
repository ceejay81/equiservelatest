@props(['title', 'icon' => null])

<div class="form-section">
  <div class="form-section-header">
    @if($icon)
      <i class="fas fa-{{ $icon }} mr-2"></i>
    @endif
    <h6>{{ $title }}</h6>
  </div>
  <div class="form-section-body">
    {{ $slot }}
  </div>
</div>
