@extends('layouts.app')

@section('content')
@include('reports.partials.header', ['breadcrumbTitle' => 'Inventory Report'])

<div class="content">
    <div class="container-fluid">
        @component('reports.partials.filter-card', [
            'action' => route('reports.inventory'),
            'resetUrl' => route('reports.inventory'),
            'exportUrl' => route('reports.inventory.export', request()->all())
        ])
            @component('reports.partials.filter-input', [
                'label' => 'Stock Status',
                'type' => 'select',
                'name' => 'status',
                'value' => $filters['status'],
                'options' => [
                    'all' => 'All Products',
                    'low' => 'Low Stock',
                    'critical' => 'Critical Stock',
                    'negative' => 'Negative Stock',
                    'out' => 'Out of Stock'
                ]
            ])@endcomponent

            @component('reports.partials.filter-input', [
                'label' => 'Category',
                'type' => 'select',
                'name' => 'category',
                'value' => $filters['category'],
                'placeholder' => 'All Categories',
                'options' => $categories->mapWithKeys(fn($cat) => [$cat => $cat])
            ])@endcomponent
        @endcomponent

        <!-- Replaced summary boxes with helper function -->
        <div class="row">
            @component('reports.partials.summary-box', [
                'value' => $metrics['total_products'],
                'label' => 'Total Products',
                'icon' => 'fas fa-boxes',
                'color' => 'info'
            ])@endcomponent

            @component('reports.partials.summary-box', [
                'value' => \App\Helpers\ReportHelper::formatCurrency($metrics['total_value']),
                'label' => 'Total Value',
                'icon' => 'fas fa-dollar-sign',
                'color' => 'success'
            ])@endcomponent

            @component('reports.partials.summary-box', [
                'value' => $metrics['low_stock_count'],
                'label' => 'Low Stock Items',
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'warning'
            ])@endcomponent

            @component('reports.partials.summary-box', [
                'value' => $metrics['critical_stock_count'],
                'label' => 'Critical Stock Items',
                'icon' => 'fas fa-exclamation-circle',
                'color' => 'danger'
            ])@endcomponent
        </div>

        <!-- Additional Metrics -->
        <div class="row">
            @component('reports.partials.info-box', [
                'icon' => 'fas fa-ban',
                'color' => 'secondary',
                'title' => 'Out of Stock',
                'value' => $metrics['out_of_stock_count'] . ' items'
            ])@endcomponent

            @component('reports.partials.info-box', [
                'icon' => 'fas fa-minus-circle',
                'color' => 'danger',
                'title' => 'Negative Stock',
                'value' => $metrics['negative_stock_count'] . ' items'
            ])@endcomponent
        </div>

        <!-- Simplified product stock table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Stock Levels</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Current Stock</th>
                            <th>Unit Price</th>
                            <th>Stock Value</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            @php
                                $stockValue = $product->stock * $product->selling_price;
                                $status = \App\Helpers\ReportHelper::getStockStatus(
                                    $product->stock,
                                    $product->low_stock_threshold ?? 10,
                                    $product->critical_stock_threshold ?? 5
                                );
                            @endphp
                            <tr>
                                <td>{{ $product->sku }}</td>
                                <td>
                                    <a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a>
                                </td>
                                <td>{{ $product->category ?? 'N/A' }}</td>
                                <td>{{ $product->brand ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $status['class'] }}">{{ $product->stock }}</span>
                                </td>
                                <td>{{ \App\Helpers\ReportHelper::formatCurrency($product->selling_price) }}</td>
                                <td>{{ \App\Helpers\ReportHelper::formatCurrency($stockValue) }}</td>
                                <td>
                                    <i class="{{ $status['icon'] }} {{ $status['textClass'] }}"></i>
                                    {{ $status['text'] }}
                                </td>
                            </tr>
                        @empty
                            @include('reports.partials.empty-state', ['columns' => 8])
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($products->hasPages())
                <div class="card-footer clearfix">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

        <!-- Replaced hardcoded legend with helper-driven component -->
        @include('reports.partials.legend-section', [
            'title' => 'Status Legend:',
            'items' => [
                [
                    'icon' => 'fas fa-check-circle',
                    'colorClass' => 'text-success',
                    'label' => 'In Stock',
                    'description' => 'Stock above low threshold'
                ],
                [
                    'icon' => 'fas fa-exclamation-triangle',
                    'colorClass' => 'text-warning',
                    'label' => 'Low Stock',
                    'description' => 'Stock at or below low threshold (default: 10)'
                ],
                [
                    'icon' => 'fas fa-exclamation-circle',
                    'colorClass' => 'text-danger',
                    'label' => 'Critical',
                    'description' => 'Stock at or below critical threshold (default: 5)'
                ],
                [
                    'icon' => 'fas fa-ban',
                    'colorClass' => 'text-secondary',
                    'label' => 'Out of Stock',
                    'description' => 'Stock is zero'
                ],
                [
                    'icon' => 'fas fa-minus-circle',
                    'colorClass' => 'text-danger',
                    'label' => 'Negative',
                    'description' => 'Stock is below zero (oversold)'
                ]
            ]
        ])
    </div>
</div>
@endsection
