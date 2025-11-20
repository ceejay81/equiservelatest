@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $pageTitle }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
                    <li class="breadcrumb-item active">Top Products</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- Filters -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter"></i> Date Range</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.top-products') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Apply
                                </button>
                                <a href="{{ route('reports.top-products') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Top Products by Revenue -->
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fas fa-trophy"></i> Top 10 Products by Revenue</h3>
            </div>
            <div class="card-body">
                <canvas id="topProductsChart" style="height: 300px;"></canvas>
            </div>
        </div>

        <!-- Top Products Table -->
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
                                <td>
                                    @if($index == 0)
                                        <i class="fas fa-trophy text-warning"></i>
                                    @elseif($index == 1)
                                        <i class="fas fa-medal text-secondary"></i>
                                    @elseif($index == 2)
                                        <i class="fas fa-medal text-bronze"></i>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a>
                                </td>
                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->category ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-info">{{ number_format($product->total_quantity) }}</span>
                                </td>
                                <td>
                                    <strong>₱{{ number_format($product->total_revenue, 2) }}</strong>
                                </td>
                                <td>
                                    ₱{{ number_format($product->total_quantity > 0 ? $product->total_revenue / $product->total_quantity : 0, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No product sales found for the selected period</td>
                            </tr>
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
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Top 10 Products by Revenue'
                },
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
.text-bronze {
    color: #cd7f32;
}
</style>
@endpush
