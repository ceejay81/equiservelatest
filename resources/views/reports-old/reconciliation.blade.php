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
                    <li class="breadcrumb-item active">Daily Reconciliation</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- Date Selection -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar"></i> Select Date</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.reconciliation') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="date" class="form-control" value="{{ $date->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> View Reconciliation
                                </button>
                                <button type="button" onclick="window.print()" class="btn btn-secondary">
                                    <i class="fas fa-print"></i> Print
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary -->
        <div class="row">
            <div class="col-lg-4">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>₱{{ number_format($totalCashReceived, 2) }}</h3>
                        <p>Total Cash Received</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>₱{{ number_format($totalOnlineReceived, 2) }}</h3>
                        <p>Total Online Received</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>₱{{ number_format($totalCollections, 2) }}</h3>
                        <p>Total Collections</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cash Sales -->
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title">Cash Sales (Cash Mode)</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Sale Number</th>
                            <th>Customer</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cashSales as $sale)
                            <tr>
                                <td>{{ $sale->created_at->format('h:i A') }}</td>
                                <td><a href="{{ route('sales.show', $sale->id) }}">{{ $sale->sale_number }}</a></td>
                                <td>{{ $sale->customer->full_name ?? 'N/A' }}</td>
                                <td>₱{{ number_format($sale->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No cash sales</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="3" class="text-right">Total Cash Sales:</td>
                            <td>₱{{ number_format($cashSales->sum('total_amount'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Online Sales -->
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title">Online Sales (Online Mode)</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Sale Number</th>
                            <th>Customer</th>
                            <th>Reference Number</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($onlineSales as $sale)
                            <tr>
                                <td>{{ $sale->created_at->format('h:i A') }}</td>
                                <td><a href="{{ route('sales.show', $sale->id) }}">{{ $sale->sale_number }}</a></td>
                                <td>{{ $sale->customer->full_name ?? 'N/A' }}</td>
                                <td>{{ $sale->reference_number ?? 'N/A' }}</td>
                                <td>₱{{ number_format($sale->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No online sales</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="4" class="text-right">Total Online Sales:</td>
                            <td>₱{{ number_format($onlineSales->sum('total_amount'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Loan Sales (Down Payments) -->
        <div class="card">
            <div class="card-header bg-warning">
                <h3 class="card-title">Loan Sales (Down Payments Received)</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Sale Number</th>
                            <th>Customer</th>
                            <th>Payment Mode</th>
                            <th>Down Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loanSales as $sale)
                            <tr>
                                <td>{{ $sale->created_at->format('h:i A') }}</td>
                                <td><a href="{{ route('sales.show', $sale->id) }}">{{ $sale->sale_number }}</a></td>
                                <td>{{ $sale->customer->full_name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $sale->payment_mode === 'cash' ? 'success' : 'info' }}">
                                        {{ ucfirst($sale->payment_mode) }}
                                    </span>
                                </td>
                                <td>₱{{ number_format($sale->loan ? $sale->loan->down_payment : 0, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No loan sales</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="4" class="text-right">Total Down Payments:</td>
                            <td>₱{{ number_format($loanSales->sum(function($sale) { return $sale->loan ? $sale->loan->down_payment : 0; }), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Loan Payments -->
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title">Loan Installment Payments</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Customer</th>
                            <th>Loan Number</th>
                            <th>Payment Mode</th>
                            <th>Amount Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loanPayments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('h:i A') }}</td>
                                <td>{{ $payment->loan->sale->customer->full_name ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('sales.show', $payment->loan->sale_id) }}">
                                        {{ $payment->loan->sale->sale_number ?? 'N/A' }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $payment->mode_of_payment === 'Cash' ? 'success' : 'info' }}">
                                        {{ $payment->mode_of_payment }}
                                    </span>
                                </td>
                                <td>₱{{ number_format($payment->amount_paid, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No loan payments</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="4" class="text-right">Total Loan Payments:</td>
                            <td>₱{{ number_format($loanPayments->sum('amount_paid'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Summary Breakdown -->
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Collections Summary</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Cash Collections</h5>
                        <table class="table table-sm">
                            <tr>
                                <td>Cash Sales</td>
                                <td class="text-right">₱{{ number_format($cashSales->sum('total_amount'), 2) }}</td>
                            </tr>
                            <tr>
                                <td>Cash Down Payments</td>
                                <td class="text-right">₱{{ number_format($loanSales->where('payment_mode', 'cash')->sum(function($s) { return $s->loan ? $s->loan->down_payment : 0; }), 2) }}</td>
                            </tr>
                            <tr>
                                <td>Cash Loan Payments</td>
                                <td class="text-right">₱{{ number_format($loanPayments->where('mode_of_payment', 'Cash')->sum('amount_paid'), 2) }}</td>
                            </tr>
                            <tr class="font-weight-bold bg-success">
                                <td>Total Cash</td>
                                <td class="text-right">₱{{ number_format($totalCashReceived, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Online Collections</h5>
                        <table class="table table-sm">
                            <tr>
                                <td>Online Sales</td>
                                <td class="text-right">₱{{ number_format($onlineSales->sum('total_amount'), 2) }}</td>
                            </tr>
                            <tr>
                                <td>Online Down Payments</td>
                                <td class="text-right">₱{{ number_format($loanSales->where('payment_mode', 'online')->sum(function($s) { return $s->loan ? $s->loan->down_payment : 0; }), 2) }}</td>
                            </tr>
                            <tr>
                                <td>Online Loan Payments</td>
                                <td class="text-right">₱{{ number_format($loanPayments->where('mode_of_payment', '!=', 'Cash')->sum('amount_paid'), 2) }}</td>
                            </tr>
                            <tr class="font-weight-bold bg-info">
                                <td>Total Online</td>
                                <td class="text-right">₱{{ number_format($totalOnlineReceived, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
