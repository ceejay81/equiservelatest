@extends('layouts.app')

@section('content')
@include('reports.partials.header', ['breadcrumbTitle' => 'Collection Report'])

<div class="content">
    <div class="container-fluid">
        @component('reports.partials.filter-card', [
            'action' => route('reports.collections'),
            'resetUrl' => route('reports.collections'),
            'exportUrl' => route('reports.collections.export', request()->all())
        ])
            @component('reports.partials.filter-input', [
                'label' => 'From Date',
                'type' => 'date',
                'name' => 'date_from',
                'value' => $filters['date_from']
            ])@endcomponent

            @component('reports.partials.filter-input', [
                'label' => 'To Date',
                'type' => 'date',
                'name' => 'date_to',
                'value' => $filters['date_to']
            ])@endcomponent

            @component('reports.partials.filter-input', [
                'label' => 'Customer',
                'type' => 'select',
                'name' => 'customer_id',
                'value' => $filters['customer_id'],
                'placeholder' => 'All Customers',
                'options' => $customers->pluck('full_name', 'id')
            ])@endcomponent

            @component('reports.partials.filter-input', [
                'label' => 'Loan Status',
                'type' => 'select',
                'name' => 'status',
                'value' => $filters['status'],
                'options' => [
                    'all' => 'All',
                    'active' => 'Active',
                    'overdue' => 'Overdue',
                    'completed' => 'Completed'
                ]
            ])@endcomponent
        @endcomponent

        <!-- Replaced summary boxes -->
        <div class="row">
            @component('reports.partials.summary-box', [
                'value' => \App\Helpers\ReportHelper::formatCurrency($metrics['total_collected']),
                'label' => 'Total Collected',
                'icon' => 'fas fa-money-bill-wave',
                'color' => 'info'
            ])@endcomponent

            @component('reports.partials.summary-box', [
                'value' => \App\Helpers\ReportHelper::formatCurrency($metrics['cash_collected']),
                'label' => 'Cash Collected',
                'icon' => 'fas fa-money-bill',
                'color' => 'success'
            ])@endcomponent

            @component('reports.partials.summary-box', [
                'value' => \App\Helpers\ReportHelper::formatCurrency($metrics['online_collected']),
                'label' => 'Online Collected',
                'icon' => 'fas fa-credit-card',
                'color' => 'primary'
            ])@endcomponent
        </div>

        <!-- Aging Analysis -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-bar"></i> Aging Analysis (Accounts Receivable)</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @component('reports.partials.info-box', [
                        'icon' => 'fas fa-check',
                        'color' => 'success',
                        'title' => 'Current (Not Due)',
                        'value' => \App\Helpers\ReportHelper::formatCurrency($aging['current']['amount']),
                        'description' => $aging['current']['count'] . ' loan(s)'
                    ])@endcomponent

                    @component('reports.partials.info-box', [
                        'icon' => 'fas fa-clock',
                        'color' => 'warning',
                        'title' => '1-30 Days Overdue',
                        'value' => \App\Helpers\ReportHelper::formatCurrency($aging['overdue_1_30']['amount']),
                        'description' => $aging['overdue_1_30']['count'] . ' loan(s)'
                    ])@endcomponent

                    @component('reports.partials.info-box', [
                        'icon' => 'fas fa-exclamation-triangle',
                        'color' => 'orange',
                        'title' => '31-60 Days Overdue',
                        'value' => \App\Helpers\ReportHelper::formatCurrency($aging['overdue_31_60']['amount']),
                        'description' => $aging['overdue_31_60']['count'] . ' loan(s)'
                    ])@endcomponent

                    @component('reports.partials.info-box', [
                        'icon' => 'fas fa-exclamation-circle',
                        'color' => 'danger',
                        'title' => '60+ Days Overdue',
                        'value' => \App\Helpers\ReportHelper::formatCurrency($aging['overdue_60_plus']['amount']),
                        'description' => $aging['overdue_60_plus']['count'] . ' loan(s)'
                    ])@endcomponent
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="callout callout-info">
                            <h5><i class="fas fa-info-circle"></i> Total Accounts Receivable</h5>
                            <p class="mb-0">{{ \App\Helpers\ReportHelper::formatCurrency($aging['total_receivable']) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Simplified loan and payment tables -->
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
                            @php $isOverdue = $loan->next_due_date < now() && $loan->status !== 'completed'; @endphp
                            <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                <td>{{ $loan->sale->customer->full_name ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('sales.show', $loan->sale_id) }}">{{ $loan->sale->sale_number ?? 'N/A' }}</a>
                                </td>
                                <td>{{ \App\Helpers\ReportHelper::formatCurrency($loan->loan_amount) }}</td>
                                <td>{{ \App\Helpers\ReportHelper::formatCurrency($loan->balance) }}</td>
                                <td>{{ \App\Helpers\ReportHelper::formatCurrency($loan->monthly_amount) }}</td>
                                <td>
                                    {{ $loan->next_due_date ? $loan->next_due_date->format('M d, Y') : 'N/A' }}
                                    @if($isOverdue)
                                        <span class="badge badge-danger">Overdue</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ \App\Helpers\ReportHelper::getLoanStatusBadge($loan->status) }}">
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
                            @include('reports.partials.empty-state', ['columns' => 8])
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
                                <td>{{ \App\Helpers\ReportHelper::formatCurrency($payment->amount_paid) }}</td>
                                <td>
                                    <span class="badge badge-{{ $payment->mode_of_payment === 'Cash' ? 'success' : 'info' }}">
                                        {{ $payment->mode_of_payment }}
                                    </span>
                                </td>
                                <td>{{ $payment->reference_number ?? 'N/A' }}</td>
                                <td>{{ $payment->received_by ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            @include('reports.partials.empty-state', ['columns' => 7])
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
