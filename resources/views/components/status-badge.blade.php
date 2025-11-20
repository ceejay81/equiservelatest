@props([
    'status',
    'type' => 'default'
])

@php
$statusMap = [
    'active' => ['class' => 'active', 'icon' => 'check-circle', 'label' => 'Active'],
    'overdue' => ['class' => 'overdue', 'icon' => 'exclamation-circle', 'label' => 'Overdue'],
    'pending' => ['class' => 'pending', 'icon' => 'clock', 'label' => 'Pending'],
    'completed' => ['class' => 'completed', 'icon' => 'check', 'label' => 'Completed'],
    'paid' => ['class' => 'completed', 'icon' => 'check-circle', 'label' => 'Paid'],
    'low-stock' => ['class' => 'low-stock', 'icon' => 'exclamation-triangle', 'label' => 'Low Stock'],
    'in-stock' => ['class' => 'in-stock', 'icon' => 'check', 'label' => 'In Stock'],
];

$config = $statusMap[$status] ?? ['class' => 'pending', 'icon' => 'circle', 'label' => ucfirst($status)];
@endphp

<span class="status-badge {{ $config['class'] }}" {{ $attributes }}>
    <i class="fas fa-{{ $config['icon'] }}"></i>
    {{ $config['label'] }}
</span>
