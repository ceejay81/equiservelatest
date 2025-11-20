@extends('layouts.app')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/loan-components.css') }}">
  <link rel="stylesheet" href="{{ asset('css/loans.css') }}">
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Amortization Schedule</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('loans.index') }}">Loans</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('loans.show', $loan) }}">Loan #{{ $loan->id }}</a></li>
                    <li class="breadcrumb-item active">Amortization Schedule</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- Loan Information Card -->
        <div class="card">
            <div class="card-header card-header-primary">
                <h3 class="card-title"><i class="fas fa-file-invoice"></i> Loan Information</h3>
                <div class="card-tools">
                    <a href="{{ route('loans.amortization.print', $loan) }}" target="_blank" class="btn btn-sm btn-light">
                        <i class="fas fa-print"></i> Print
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <x-info-card title="Borrower Information">
                            <x-detail-row label="Customer" :value="$customer->full_name ?? 'N/A'" />
                            <x-detail-row label="Loan Number" :value="'LN-' . str_pad($loan->id, 6, '0', STR_PAD_LEFT)" />
                            <x-detail-row label="Loan Date" :value="$loan->start_date->format('F d, Y')" />
                            <x-detail-row label="Maturity Date" :value="$loan->maturity_date ? $loan->maturity_date->format('F d, Y') : 'N/A'" />
                        </x-info-card>
                    </div>
                    <div class="col-md-6">
                        <x-info-card title="Loan Terms">
                            <x-detail-row label="Total Amount" :value="'₱' . number_format($loan->loan_amount, 2)" />
                            <x-detail-row label="Down Payment" :value="'₱' . number_format($loan->down_payment, 2)" />
                            <x-detail-row label="Principal" :value="'₱' . number_format($summary['principal_amount'], 2)" />
                            <x-detail-row label="Monthly Payment" :value="'₱' . number_format($loan->monthly_amount, 2)" />
                        </x-info-card>
                    </div>
                </div>
            </div>
        </div>

        <!-- Penalty Terms Card -->
        <div class="card">
            <div class="card-header card-header-warning">
                <h3 class="card-title"><i class="fas fa-percent"></i> Penalty Terms</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <small class="text-muted">Grace Period</small>
                        <h5>{{ $loan->grace_period_days }} days</h5>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Late Payment Penalty</small>
                        <h5>{{ number_format($loan->late_penalty_rate, 2) }}%</h5>
                        <small class="text-muted">₱{{ number_format($loan->calculateLatePaymentPenalty($loan->monthly_amount), 2) }} per late payment</small>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Maturity Penalty</small>
                        <h5>{{ number_format($loan->maturity_penalty_rate, 2) }}%</h5>
                        <small class="text-muted">₱{{ number_format($summary['maturity_penalty_amount'], 2) }} if unpaid by maturity</small>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Available Rebates</small>
                        <h5 class="text-success">₱{{ number_format($total_rebate_balance, 2) }}</h5>
                        <small class="text-muted">{{ $available_rebates->count() }} rebate(s)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Schedule Table -->
        <div class="card">
            <div class="card-header card-header-info">
                <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Payment Schedule</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Due Date</th>
                                <th>Grace Until</th>
                                <th class="text-right">Payment</th>
                                <th class="text-right">Available Rebate</th>
                                <th class="text-right">After Rebate</th>
                                <th class="text-right">If Late</th>
                                <th class="text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedule as $payment)
                            <tr class="{{ $payment['is_maturity'] ? 'table-warning' : '' }}">
                                <td class="text-center">
                                    <strong>{{ $payment['month'] }}</strong>
                                    @if($payment['is_maturity'])
                                        <br><span class="badge badge-warning">Final</span>
                                    @endif
                                </td>
                                <td>{{ $payment['due_date']->format('M d, Y') }}</td>
                                <td>{{ $payment['grace_period_end']->format('M d, Y') }}</td>
                                <td class="text-right"><strong>₱{{ number_format($payment['payment'], 2) }}</strong></td>
                                <td class="text-right text-success">
                                    @if($payment['available_rebate'] > 0)
                                        <strong>-₱{{ number_format($payment['available_rebate'], 2) }}</strong>
                                    @else
                                        <span class="text-muted">₱0.00</span>
                                    @endif
                                </td>
                                <td class="text-right">₱{{ number_format($payment['amount_after_rebate'], 2) }}</td>
                                <td class="text-right text-danger">₱{{ number_format($payment['amount_if_late'], 2) }}</td>
                                <td class="text-right">₱{{ number_format($payment['balance'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background: #F1F5F9;">
                            <tr>
                                <th colspan="3" class="text-right">TOTALS:</th>
                                <th class="text-right">₱{{ number_format($summary['total_payments'], 2) }}</th>
                                <th class="text-right text-success">-₱{{ number_format($total_rebate_balance, 2) }}</th>
                                <th class="text-right text-success">₱{{ number_format($summary['best_case_total'], 2) }}</th>
                                <th class="text-right text-danger">₱{{ number_format($summary['worst_case_total'], 2) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-success">
                    <div class="card-body">
                        <h5 class="text-white"><i class="fas fa-check-circle"></i> Best Case</h5>
                        <h3 class="text-white">₱{{ number_format($summary['best_case_total'], 2) }}</h3>
                        <p class="text-white mb-0">On-time + Rebates</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning">
                    <div class="card-body">
                        <h5 class="text-white"><i class="fas fa-calculator"></i> Regular</h5>
                        <h3 class="text-white">₱{{ number_format($summary['total_payments'], 2) }}</h3>
                        <p class="text-white mb-0">On-time, No Rebates</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger">
                    <div class="card-body">
                        <h5 class="text-white"><i class="fas fa-exclamation-triangle"></i> Worst Case</h5>
                        <h3 class="text-white">₱{{ number_format($summary['worst_case_total'], 2) }}</h3>
                        <p class="text-white mb-0">All Late + Maturity</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Important Notes -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle"></i> Important Notes</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Penalty Information</h5>
                        <ul>
                            <li><strong>Grace:</strong> {{ $loan->grace_period_days }} days after due date (no penalty)</li>
                            <li><strong>Late:</strong> {{ number_format($loan->late_penalty_rate, 2) }}% penalty applies</li>
                            <li><strong>Maturity:</strong> {{ number_format($loan->maturity_penalty_rate, 2) }}% if unpaid by maturity</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>Rebate Information</h5>
                        <ul>
                            <li><strong>Available:</strong> ₱{{ number_format($total_rebate_balance, 2) }}</li>
                            <li><strong>Rules:</strong> On-time payments only</li>
                            <li><strong>Savings:</strong> Up to ₱{{ number_format($summary['potential_savings'], 2) }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mb-4">
            <a href="{{ route('loans.show', $loan) }}" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> Back to Loan Details
            </a>
            <a href="{{ route('loans.amortization.print', $loan) }}" target="_blank" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Schedule
            </a>
        </div>
    </div>
</div>
@endsection
