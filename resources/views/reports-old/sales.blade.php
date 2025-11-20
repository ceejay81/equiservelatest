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
                    <li class="breadcrumb-item active">Sales Report</li>
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
                <form method="GET" action="{{ route('reports.sales') }}">
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
                                <label>Customer</label>
                                <select name="customer_id" class="form-control">
                                    <option value="">All Customers</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ $filters['customer_id'] == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->full_name }}
                                        </option>
                                    @endforeach
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
                            <a href="{{ route('reports.sales') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                            <a href="{{ route('reports.sales.export', request()->all()) }}" class="btn btn-success float-right">
                                <i class="fas fa-file-excel"></i> Export to Excel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>₱{{ number_format($metrics['total_sales'], 2) }}</h3>
                        <p>Total Sales</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $metrics['total_transactions'] }}</h3>
                        <p>Total Transactions</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>₱{{ number_format($metrics['average_value'], 2) }}</h3>
                        <p>Average Value</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $metrics['cash_count'] }} / {{ $metrics['loan_count'] }}</h3>
                        <p>Cash / Loan Sales</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Breakdown -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sales by Type</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 text-center">
                                <div class="text-success">
                                    <h4>₱{{ number_format($metrics['cash_sales'], 2) }}</h4>
                                    <p class="text-muted">Cash Sales</p>
                                </div>
                            </div>
                            <div class="col-6 text-center">
                                <div class="text-primary">
                                    <h4>₱{{ number_format($metrics['loan_sales'], 2) }}</h4>
                                    <p class="text-muted">Loan Sales</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sales by Payment Mode</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 text-center">
                                <div class="text-warning">
                                    <h4>₱{{ number_format($metrics['cash_mode_sales'], 2) }}</h4>
                                    <p class="text-muted">Cash Mode</p>
                                </div>
                            </div>
                            <div class="col-6 text-center">
                                <div class="text-info">
                                    <h4>₱{{ number_format($metrics['online_mode_sales'], 2) }}</h4>
                                    <p class="text-muted">Online Mode</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Transactions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sales Transactions</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Sale Number</th>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Payment Mode</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Processed By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td>{{ $sale->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('sales.show', $sale->id) }}">{{ $sale->sale_number }}</a>
                                </td>
                                <td>{{ $sale->customer->full_name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $sale->sale_type === 'cash' ? 'success' : 'primary' }}">
                                        {{ ucfirst($sale->sale_type) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $sale->payment_mode === 'cash' ? 'warning' : 'info' }}">
                                        {{ ucfirst($sale->payment_mode) }}
                                    </span>
                                </td>
                                <td>₱{{ number_format($sale->total_amount, 2) }}</td>
                                <td>
                                    @if($sale->sale_type === 'cash')
                                        <span class="badge badge-success">Paid</span>
                                    @elseif($sale->loan)
                                        <span class="badge badge-{{ $sale->loan->status === 'active' ? 'primary' : ($sale->loan->status === 'overdue' ? 'danger' : 'secondary') }}">
                                            {{ ucfirst($sale->loan->status) }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $sale->user->name ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No sales found for the selected period</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($sales->hasPages())
                <div class="card-footer clearfix">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
