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
                    <li class="breadcrumb-item active">Inventory Report</li>
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
                <h3 class="card-title"><i class="fas fa-filter"></i> Filters</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.inventory') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Stock Status</label>
                                <select name="status" class="form-control">
                                    <option value="all" {{ $filters['status'] === 'all' ? 'selected' : '' }}>All Products</option>
                                    <option value="low" {{ $filters['status'] === 'low' ? 'selected' : '' }}>Low Stock</option>
                                    <option value="critical" {{ $filters['status'] === 'critical' ? 'selected' : '' }}>Critical Stock</option>
                                    <option value="negative" {{ $filters['status'] === 'negative' ? 'selected' : '' }}>Negative Stock</option>
                                    <option value="out" {{ $filters['status'] === 'out' ? 'selected' : '' }}>Out of Stock</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Category</label>
                                <select name="category" class="form-control">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ $filters['category'] === $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Apply Filters
                                </button>
                                <a href="{{ route('reports.inventory') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <a href="{{ route('reports.inventory.export', request()->all()) }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $metrics['total_products'] }}</h3>
                        <p>Total Products</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>₱{{ number_format($metrics['total_value'], 2) }}</h3>
                        <p>Total Value</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $metrics['low_stock_count'] }}</h3>
                        <p>Low Stock Items</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $metrics['critical_stock_count'] }}</h3>
                        <p>Critical Stock Items</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Metrics -->
        <div class="row">
            <div class="col-md-6">
                <div class="info-box">
                    <span class="info-box-icon bg-secondary"><i class="fas fa-ban"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Out of Stock</span>
                        <span class="info-box-number">{{ $metrics['out_of_stock_count'] }} items</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-minus-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Negative Stock</span>
                        <span class="info-box-number">{{ $metrics['negative_stock_count'] }} items</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Stock Levels -->
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
                                $lowThreshold = $product->low_stock_threshold ?? 10;
                                $criticalThreshold = $product->critical_stock_threshold ?? 5;
                                
                                if ($product->stock < 0) {
                                    $statusClass = 'danger';
                                    $statusIcon = 'fas fa-minus-circle';
                                    $statusText = 'Negative';
                                } elseif ($product->stock == 0) {
                                    $statusClass = 'secondary';
                                    $statusIcon = 'fas fa-ban';
                                    $statusText = 'Out of Stock';
                                } elseif ($product->stock <= $criticalThreshold) {
                                    $statusClass = 'danger';
                                    $statusIcon = 'fas fa-exclamation-circle';
                                    $statusText = 'Critical';
                                } elseif ($product->stock <= $lowThreshold) {
                                    $statusClass = 'warning';
                                    $statusIcon = 'fas fa-exclamation-triangle';
                                    $statusText = 'Low Stock';
                                } else {
                                    $statusClass = 'success';
                                    $statusIcon = 'fas fa-check-circle';
                                    $statusText = 'In Stock';
                                }
                            @endphp
                            <tr>
                                <td>{{ $product->sku }}</td>
                                <td>
                                    <a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a>
                                </td>
                                <td>{{ $product->category ?? 'N/A' }}</td>
                                <td>{{ $product->brand ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $statusClass }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td>₱{{ number_format($product->selling_price, 2) }}</td>
                                <td>₱{{ number_format($stockValue, 2) }}</td>
                                <td>
                                    <i class="{{ $statusIcon }} text-{{ $statusClass }}"></i>
                                    {{ $statusText }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No products found</td>
                            </tr>
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

        <!-- Legend -->
        <div class="card">
            <div class="card-body">
                <h5>Status Legend:</h5>
                <p class="mb-1">
                    <i class="fas fa-check-circle text-success"></i> <strong>In Stock:</strong> Stock above low threshold
                </p>
                <p class="mb-1">
                    <i class="fas fa-exclamation-triangle text-warning"></i> <strong>Low Stock:</strong> Stock at or below low threshold (default: 10)
                </p>
                <p class="mb-1">
                    <i class="fas fa-exclamation-circle text-danger"></i> <strong>Critical:</strong> Stock at or below critical threshold (default: 5)
                </p>
                <p class="mb-1">
                    <i class="fas fa-ban text-secondary"></i> <strong>Out of Stock:</strong> Stock is zero
                </p>
                <p class="mb-0">
                    <i class="fas fa-minus-circle text-danger"></i> <strong>Negative:</strong> Stock is below zero (oversold)
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
