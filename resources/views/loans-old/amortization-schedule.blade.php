@extends('layouts.app')

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
            <div class="card-header bg-primary">
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
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Customer:</strong></td>
                                <td>{{ $customer->full_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Loan Number:</strong></td>
                                <td>LN-{{ str_pad($loan->id, 6, '0', STR_PAD_LEFT) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Loan Date:</strong></td>
                                <td>{{ $loan->start_date->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Maturity Date:</strong></td>
                                <td>{{ $loan->maturity_date ? $loan->maturity_date->format('F d, Y') : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Total Amount:</strong></td>
                                <td>₱{{ number_format($loan->loan_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Down Payment:</strong></td>
                                <td>₱{{ number_format($loan->down_payment, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Principal:</strong></td>
                                <td>₱{{ number_format($summary['principal_amount'], 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Monthly Payment:</strong></td>
                                <td>₱{{ number_format($loan->monthly_amount, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Penalty Terms Card -->
        <div class="card">
            <div class="card-header bg-warning">
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
            <div class="card-header bg-info">
                <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Payment Schedule</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Due Date</th>
                                <th>Grace Until</th>
                                <th class="text-right">Payment</th>
                                <th class="text-right">Available Rebate</th>
                                <th class="text-right">After Rebate</th>
                                <th class="text-right">If Late (+{{ number_format($loan->late_penalty_rate, 2) }}%)</th>
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
                                <td>
                                    <strong>{{ $payment['due_date']->format('M d, Y') }}</strong>
                                    <br><small class="text-muted">{{ $payment['due_date']->format('l') }}</small>
                                </td>
                                <td>
                                    {{ $payment['grace_period_end']->format('M d, Y') }}
                                    <br><small class="text-muted">{{ $payment['grace_period_end']->format('l') }}</small>
                                </td>
                                <td class="text-right">
                                    <strong>₱{{ number_format($payment['payment'], 2) }}</strong>
                                </td>
                                <td class="text-right text-success">
                                    @if($payment['available_rebate'] > 0)
                                        <strong>-₱{{ number_format($payment['available_rebate'], 2) }}</strong>
                                    @else
                                        <span class="text-muted">₱0.00</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($payment['available_rebate'] > 0)
                                        <strong class="text-success">₱{{ number_format($payment['amount_after_rebate'], 2) }}</strong>
                                    @else
                                        <span class="text-muted">₱{{ number_format($payment['payment'], 2) }}</span>
                                    @endif
                                </td>
                                <td class="text-right text-danger">
                                    <strong>₱{{ number_format($payment['amount_if_late'], 2) }}</strong>
                                    <br><small class="text-muted">+₱{{ number_format($payment['late_penalty'], 2) }}</small>
                                </td>
                                <td class="text-right">
                                    ₱{{ number_format($payment['balance'], 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
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
                        <h5 class="text-white"><i class="fas fa-check-circle"></i> Best Case Scenario</h5>
                        <h3 class="text-white">₱{{ number_format($summary['best_case_total'], 2) }}</h3>
                        <p class="text-white mb-0">All payments on time with rebates applied</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning">
                    <div class="card-body">
                        <h5 class="text-white"><i class="fas fa-calculator"></i> Regular Scenario</h5>
                        <h3 class="text-white">₱{{ number_format($summary['total_payments'], 2) }}</h3>
                        <p class="text-white mb-0">All payments on time without rebates</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger">
                    <div class="card-body">
                        <h5 class="text-white"><i class="fas fa-exclamation-triangle"></i> Worst Case Scenario</h5>
                        <h3 class="text-white">₱{{ number_format($summary['worst_case_total'], 2) }}</h3>
                        <p class="text-white mb-0">All payments late + maturity penalty</p>
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
                            <li><strong>Grace Period:</strong> You have {{ $loan->grace_period_days }} days after the due date to pay without penalty</li>
                            <li><strong>Late Payment:</strong> {{ number_format($loan->late_penalty_rate, 2) }}% penalty applies after grace period</li>
                            <li><strong>Maturity Penalty:</strong> {{ number_format($loan->maturity_penalty_rate, 2) }}% of remaining balance if unpaid by {{ $loan->maturity_date ? $loan->maturity_date->format('F d, Y') : 'maturity date' }}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>Rebate Information</h5>
                        <ul>
                            <li><strong>Available Rebates:</strong> ₱{{ number_format($total_rebate_balance, 2) }}</li>
                            <li><strong>Rebate Rules:</strong> Can only be applied to on-time payments</li>
                            <li><strong>Savings:</strong> Pay on time to save up to ₱{{ number_format($summary['potential_savings'], 2) }}</li>
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
