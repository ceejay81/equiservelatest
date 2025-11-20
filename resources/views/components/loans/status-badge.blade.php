@props(['status', 'isOverdue' => false])

@php
  $statusConfig = [
    'active' => ['badge-primary', 'fa-clock', 'Active'],
    'completed' => ['badge-success', 'fa-check-circle', 'Completed'],
    'overdue' => ['badge-danger', 'fa-exclamation-triangle', 'Overdue'],
  ];
  [$class, $icon, $text] = $statusConfig[$status] ?? ['badge-secondary', 'fa-question-circle', ucfirst($status)];
@endphp

<span class="badge {{ $class }} badge-status">
  <i class="fas {{ $icon }} mr-1"></i>{{ $text }}
</span>
@if($isOverdue)
  <span class="badge badge-danger badge-status ml-2">
    <i class="fas fa-exclamation-triangle mr-1"></i> Overdue
  </span>
@endif
