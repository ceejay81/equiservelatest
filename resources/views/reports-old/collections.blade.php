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
                    <li class="breadcrumb-item active">Collection Report</li>
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
                <form method="GET" action="{{ route('reports.collections') }}">
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
                                <label>Loan Status</label>
                                <select name="status" class="form-control">
                                    <option value="all" {{ $filters['status'] === 'all' ? 'selected' : '' }}>All</option>
                                    <option value="active" {{ $filters['status'] === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="overdue" {{ $filters['status'] === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                    <option value="completed" {{ $filters['status'] === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Apply Filters
                            </button>
                            <a href="{{ route('reports.collections') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                            <a href="{{ route('reports.collections.export', request()->all()) }}" class="btn btn-success float-right">
                                <i class="fas fa-file-excel"></i> Export to Excel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Collections Summary -->
        <div class="row">
            <div class="col-lg-4 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>₱{{ number_format($metrics['total_collected'], 2) }}</h3>
                        <p>Total Collected</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>₱{{ number_format($metrics['cash_collected'], 2) }}</h3>
                        <p>Cash Collected</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>₱{{ number_format($metrics['online_collected'], 2) }}</h3>
                        <p>Online Collected</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aging Analysis -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-bar"></i> Aging Analysis (Accounts Receivable)</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Current (Not Due)</span>
                                <span class="info-box-number">₱{{ number_format($aging['current']['amount'], 2) }}</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 100%"></div>
                                </div>
                                <span class="progress-description">
                                    {{ $aging['current']['count'] }} loan(s)
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">1-30 Days Overdue</span>
                                <span class="info-box-number">₱{{ number_format($aging['overdue_1_30']['amount'], 2) }}</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 100%"></div>
                                </div>
                                <span class="progress-description">
                                    {{ $aging['overdue_1_30']['count'] }} loan(s)
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-orange">
                            <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">31-60 Days Overdue</span>
                                <span class="info-box-number">₱{{ number_format($aging['overdue_31_60']['amount'], 2) }}</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 100%"></div>
                                </div>
                                <span class="progress-description">
                                    {{ $aging['overdue_31_60']['count'] }} loan(s)
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-danger">
                            <span class="info-box-icon"><i class="fas fa-exclamation-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">60+ Days Overdue</span>
                                <span class="info-box-number">₱{{ number_format($aging['overdue_60_plus']['amount'], 2) }}</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 100%"></div>
                                </div>
                                <span class="progress-description">
                                    {{ $aging['overdue_60_plus']['count'] }} loan(s)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="callout callout-info">
                            <h5><i class="fas fa-info-circle"></i> Total Accounts Receivable</h5>
                            <p class="mb-0">₱{{ number_format($aging['total_receivable'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Loans -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Active Loans</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Sale Number</th>
                            <th>Loan Amount</th>
                            <th>Balance</th>
                            <th>Monthly Amount</th>
                            <th>Next Due Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                            @php
                                $isOverdue = $loan->next_due_date < now() && $loan->status !== 'completed';
                            @endphp
                            <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                <td>{{ $loan->sale->customer->full_name ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('sales.show', $loan->sale_id) }}">{{ $loan->sale->sale_number ?? 'N/A' }}</a>
                                </td>
                                <td>₱{{ number_format($loan->loan_amount, 2) }}</td>
                                <td>₱{{ number_format($loan->balance, 2) }}</td>
                                <td>₱{{ number_format($loan->monthly_amount, 2) }}</td>
                                <td>
                                    {{ $loan->next_due_date ? $loan->next_due_date->format('M d, Y') : 'N/A' }}
                                    @if($isOverdue)
                                        <span class="badge badge-danger">Overdue</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $loan->status === 'active' ? 'primary' : ($loan->status === 'overdue' ? 'danger' : 'secondary') }}">
                                        {{ ucfirst($loan->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No loans found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($loans->hasPages())
                <div class="card-footer clearfix">
                    {{ $loans->links() }}
                </div>
            @endif
        </div>

        <!-- Payment History -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Payment History</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Loan Number</th>
                            <th>Amount Paid</th>
                            <th>Payment Mode</th>
                            <th>Reference Number</th>
                            <th>Received By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td>{{ $payment->loan->sale->customer->full_name ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('sales.show', $payment->loan->sale_id) }}">
                                        {{ $payment->loan->sale->sale_number ?? 'N/A' }}
                                    </a>
                                </td>
                                <td>₱{{ number_format($payment->amount_paid, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $payment->mode_of_payment === 'Cash' ? 'success' : 'info' }}">
                                        {{ $payment->mode_of_payment }}
                                    </span>
                                </td>
                                <td>{{ $payment->reference_number ?? 'N/A' }}</td>
                                <td>{{ $payment->received_by ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No payments found for the selected period</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
                <div class="card-footer clearfix">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
