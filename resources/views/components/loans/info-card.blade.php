@props(['icon', 'title', 'variant' => 'default'])

<div class="info-card info-card-{{ $variant }}">
  <h6><i class="fas fa-{{ $icon }} mr-1"></i> {{ $title }}</h6>
  {{ $slot }}
</div>
