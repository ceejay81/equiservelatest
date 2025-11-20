@extends('layouts.app')

@section('content')
@include('reports.partials.header', ['breadcrumbTitle' => 'Stock Movements'])

<div class="content">
    <div class="container-fluid">
        @component('reports.partials.filter-card', [
            'action' => route('reports.stock-movements'),
            'resetUrl' => route('reports.stock-movements')
        ])
            @component('reports.partials.filter-input', [
                'label' => 'From Date',
                'type' => 'date',
                'name' => 'date_from',
                'value' => $filters['date_from']
            ])@endcomponent

            @component('reports.partials.filter-input', [
                'label' => 'To Date',
                'type' => 'date',
                'name' => 'date_to',
                'value' => $filters['date_to']
            ])@endcomponent

            @component('reports.partials.filter-input', [
                'label' => 'Movement Type',
                'type' => 'select',
                'name' => 'type',
                'value' => $filters['type'],
                'placeholder' => 'All Types',
                'options' => [
                    'sale' => 'Sales',
                    'adjustment' => 'Adjustments',
                    'receive' => 'Receives'
                ]
            ])@endcomponent

            @component('reports.partials.filter-input', [
                'label' => 'Product',
                'type' => 'select',
                'name' => 'product_id',
                'value' => $filters['product_id'],
                'placeholder' => 'All Products',
                'options' => $products->pluck('name', 'id')
            ])@endcomponent
        @endcomponent

        <!-- Simplified stock movements table using helper -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Stock Movement History</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Type</th>
                            <th>Quantity Change</th>
                            <th>Remarks</th>
                            <th>Performed By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                            @php $badge = \App\Helpers\ReportHelper::getMovementTypeBadge($movement->type); @endphp
                            <tr>
                                <td>{{ $movement->created_at->format('M d, Y h:i A') }}</td>
                                <td>{{ $movement->product->name ?? 'N/A' }}</td>
                                <td>{{ $movement->product->sku ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $badge['class'] }}">
                                        <i class="{{ $badge['icon'] }}"></i> {{ $badge['text'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="{{ $movement->quantity_change > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $movement->quantity_change > 0 ? '+' : '' }}{{ $movement->quantity_change }}
                                    </span>
                                </td>
                                <td>{{ $movement->remarks ?? 'N/A' }}</td>
                                <td>{{ $movement->user->name ?? 'System' }}</td>
                            </tr>
                        @empty
                            @include('reports.partials.empty-state', ['columns' => 7])
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($movements->hasPages())
                <div class="card-footer clearfix">
                    {{ $movements->links() }}
                </div>
            @endif
        </div>

        <!-- Replaced hardcoded legend with reusable component -->
        @include('reports.partials.legend-section', [
            'title' => 'Movement Types:',
            'items' => [
                [
                    'icon' => 'fas fa-arrow-down',
                    'colorClass' => 'text-danger',
                    'label' => 'Sale',
                    'description' => 'Stock decreased due to sale'
                ],
                [
                    'icon' => 'fas fa-arrow-up',
                    'colorClass' => 'text-success',
                    'label' => 'Receive',
                    'description' => 'Stock increased due to receiving inventory'
                ],
                [
                    'icon' => 'fas fa-exchange-alt',
                    'colorClass' => 'text-warning',
                    'label' => 'Adjustment',
                    'description' => 'Manual stock adjustment (increase or decrease)'
                ]
            ]
        ])
    </div>
</div>
@endsection
