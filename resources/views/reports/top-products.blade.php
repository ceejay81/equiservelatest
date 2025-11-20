@extends('layouts.app')

@section('content')
@include('reports.partials.header', ['breadcrumbTitle' => 'Top Products'])

<div class="content">
    <div class="container-fluid">
        @component('reports.partials.filter-card', [
            'action' => route('reports.top-products'),
            'resetUrl' => route('reports.top-products')
        ])
            @component('reports.partials.filter-input', [
                'label' => 'From Date',
                'type' => 'date',
                'name' => 'date_from',
                'value' => $filters['date_from'],
                'cols' => 4
            ])@endcomponent

            @component('reports.partials.filter-input', [
                'label' => 'To Date',
                'type' => 'date',
                'name' => 'date_to',
                'value' => $filters['date_to'],
                'cols' => 4
            ])@endcomponent
        @endcomponent

        <!-- Top Products by Revenue -->
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fas fa-trophy"></i> Top 10 Products by Revenue</h3>
            </div>
            <div class="card-body">
                <canvas id="topProductsChart" style="height: 300px;"></canvas>
            </div>
        </div>

        <!-- Simplified top products table with ranking icons -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Sales Details</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Quantity Sold</th>
                            <th>Total Revenue</th>
                            <th>Avg. Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $index => $product)
                            <tr>
                                <td>{!! \App\Helpers\ReportHelper::getRankingIcon($index) !!}</td>
                                <td>
                                    <a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a>
                                </td>
                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->category ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-info">{{ number_format($product->total_quantity) }}</span>
                                </td>
                                <td>
                                    <strong>{{ \App\Helpers\ReportHelper::formatCurrency($product->total_revenue) }}</strong>
                                </td>
                                <td>
                                    {{ \App\Helpers\ReportHelper::formatCurrency($product->total_quantity > 0 ? $product->total_revenue / $product->total_quantity : 0) }}
                                </td>
                            </tr>
                        @empty
                            @include('reports.partials.empty-state', ['columns' => 7])
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('topProductsChart').getContext('2d');
    
    const products = @json($products);
    const labels = products.map(p => p.name.length > 20 ? p.name.substring(0, 20) + '...' : p.name);
    const revenues = products.map(p => parseFloat(p.total_revenue));
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue',
                data: revenues,
                backgroundColor: [
                    '#ffc107', '#6c757d', '#cd7f32', '#007bff', '#28a745',
                    '#17a2b8', '#6610f2', '#e83e8c', '#fd7e14', '#20c997'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Top 10 Products by Revenue' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: ₱' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString('en-US');
                        }
                    }
                }
            }
        }
    });
});
</script>
<style>
.text-bronze { color: #cd7f32; }
</style>
@endpush
