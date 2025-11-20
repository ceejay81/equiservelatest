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
                    <li class="breadcrumb-item active">Stock Movements</li>
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
                <form method="GET" action="{{ route('reports.stock-movements') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Movement Type</label>
                                <select name="type" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="sale" {{ $filters['type'] === 'sale' ? 'selected' : '' }}>Sales</option>
                                    <option value="adjustment" {{ $filters['type'] === 'adjustment' ? 'selected' : '' }}>Adjustments</option>
                                    <option value="receive" {{ $filters['type'] === 'receive' ? 'selected' : '' }}>Receives</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Product</label>
                                <select name="product_id" class="form-control">
                                    <option value="">All Products</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $filters['product_id'] == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Apply Filters
                            </button>
                            <a href="{{ route('reports.stock-movements') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stock Movements -->
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
                            @php
                                if ($movement->type === 'sale') {
                                    $badgeClass = 'danger';
                                    $icon = 'fas fa-arrow-down';
                                } elseif ($movement->type === 'receive') {
                                    $badgeClass = 'success';
                                    $icon = 'fas fa-arrow-up';
                                } else {
                                    $badgeClass = 'warning';
                                    $icon = 'fas fa-exchange-alt';
                                }
                            @endphp
                            <tr>
                                <td>{{ $movement->created_at->format('M d, Y h:i A') }}</td>
                                <td>{{ $movement->product->name ?? 'N/A' }}</td>
                                <td>{{ $movement->product->sku ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $badgeClass }}">
                                        <i class="{{ $icon }}"></i> {{ ucfirst($movement->type) }}
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
                            <tr>
                                <td colspan="7" class="text-center text-muted">No stock movements found for the selected period</td>
                            </tr>
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

        <!-- Legend -->
        <div class="card">
            <div class="card-body">
                <h5>Movement Types:</h5>
                <p class="mb-1">
                    <span class="badge badge-danger"><i class="fas fa-arrow-down"></i> Sale</span> - Stock decreased due to sale
                </p>
                <p class="mb-1">
                    <span class="badge badge-success"><i class="fas fa-arrow-up"></i> Receive</span> - Stock increased due to receiving inventory
                </p>
                <p class="mb-0">
                    <span class="badge badge-warning"><i class="fas fa-exchange-alt"></i> Adjustment</span> - Manual stock adjustment (increase or decrease)
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
