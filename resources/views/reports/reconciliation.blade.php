@extends('layouts.app')

@section('content')
@include('reports.partials.header', ['breadcrumbTitle' => 'Daily Reconciliation'])

<div class="content">
    <div class="container-fluid">
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

        <!-- Replaced summary boxes -->
        <div class="row">
            @component('reports.partials.summary-box', [
                'value' => \App\Helpers\ReportHelper::formatCurrency($totalCashReceived),
                'label' => 'Total Cash Received',
                'icon' => 'fas fa-money-bill',
                'color' => 'success'
            ])@endcomponent

            @component('reports.partials.summary-box', [
                'value' => \App\Helpers\ReportHelper::formatCurrency($totalOnlineReceived),
                'label' => 'Total Online Received',
                'icon' => 'fas fa-credit-card',
                'color' => 'info'
            ])@endcomponent

            @component('reports.partials.summary-box', [
                'value' => \App\Helpers\ReportHelper::formatCurrency($totalCollections),
                'label' => 'Total Collections',
                'icon' => 'fas fa-calculator',
                'color' => 'primary'
            ])@endcomponent
        </div>

        <!-- Simplified transaction tables -->
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
                                <td>{{ \App\Helpers\ReportHelper::formatCurrency($sale->total_amount) }}</td>
                            </tr>
                        @empty
                            @include('reports.partials.empty-state', ['columns' => 4])
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="3" class="text-right">Total Cash Sales:</td>
                            <td>{{ \App\Helpers\ReportHelper::formatCurrency($cashSales->sum('total_amount')) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

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
                                <td>{{ \App\Helpers\ReportHelper::formatCurrency($sale->total_amount) }}</td>
                            </tr>
                        @empty
                            @include('reports.partials.empty-state', ['columns' => 5])
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="4" class="text-right">Total Online Sales:</td>
                            <td>{{ \App\Helpers\ReportHelper::formatCurrency($onlineSales->sum('total_amount')) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

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
                                <td>{{ \App\Helpers\ReportHelper::formatCurrency($sale->loan ? $sale->loan->down_payment : 0) }}</td>
                            </tr>
                        @empty
                            @include('reports.partials.empty-state', ['columns' => 5])
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="4" class="text-right">Total Down Payments:</td>
                            <td>{{ \App\Helpers\ReportHelper::formatCurrency($loanSales->sum(function($s) { return $s->loan ? $s->loan->down_payment : 0; })) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

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
                                <td>{{ \App\Helpers\ReportHelper::formatCurrency($payment->amount_paid) }}</td>
                            </tr>
                        @empty
                            @include('reports.partials.empty-state', ['columns' => 5])
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="4" class="text-right">Total Loan Payments:</td>
                            <td>{{ \App\Helpers\ReportHelper::formatCurrency($loanPayments->sum('amount_paid')) }}</td>
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
                                <td class="text-right">{{ \App\Helpers\ReportHelper::formatCurrency($cashSales->sum('total_amount')) }}</td>
                            </tr>
                            <tr>
                                <td>Cash Down Payments</td>
                                <td class="text-right">{{ \App\Helpers\ReportHelper::formatCurrency($loanSales->where('payment_mode', 'cash')->sum(function($s) { return $s->loan ? $s->loan->down_payment : 0; })) }}</td>
                            </tr>
                            <tr>
                                <td>Cash Loan Payments</td>
                                <td class="text-right">{{ \App\Helpers\ReportHelper::formatCurrency($loanPayments->where('mode_of_payment', 'Cash')->sum('amount_paid')) }}</td>
                            </tr>
                            <tr class="font-weight-bold bg-success">
                                <td>Total Cash</td>
                                <td class="text-right">{{ \App\Helpers\ReportHelper::formatCurrency($totalCashReceived) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Online Collections</h5>
                        <table class="table table-sm">
                            <tr>
                                <td>Online Sales</td>
                                <td class="text-right">{{ \App\Helpers\ReportHelper::formatCurrency($onlineSales->sum('total_amount')) }}</td>
                            </tr>
                            <tr>
                                <td>Online Down Payments</td>
                                <td class="text-right">{{ \App\Helpers\ReportHelper::formatCurrency($loanSales->where('payment_mode', 'online')->sum(function($s) { return $s->loan ? $s->loan->down_payment : 0; })) }}</td>
                            </tr>
                            <tr>
                                <td>Online Loan Payments</td>
                                <td class="text-right">{{ \App\Helpers\ReportHelper::formatCurrency($loanPayments->where('mode_of_payment', '!=', 'Cash')->sum('amount_paid')) }}</td>
                            </tr>
                            <tr class="font-weight-bold bg-info">
                                <td>Total Online</td>
                                <td class="text-right">{{ \App\Helpers\ReportHelper::formatCurrency($totalOnlineReceived) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
