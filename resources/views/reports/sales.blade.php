@extends('layouts.app')

@section('content')
@include('reports.partials.header', ['breadcrumbTitle' => 'Sales Report'])

<div class="content">
    <div class="container-fluid">
        <!-- Replaced filter card with reusable component -->
        @component('reports.partials.filter-card', [
            'action' => route('reports.sales'),
            'resetUrl' => route('reports.sales'),
            'exportUrl' => route('reports.sales.export', request()->all())
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
                'label' => 'Product',
                'type' => 'select',
                'name' => 'product_id',
                'value' => $filters['product_id'],
                'placeholder' => 'All Products',
                'options' => $products->pluck('name', 'id')
            ])@endcomponent
        @endcomponent

        <!-- Replaced summary boxes with reusable component -->
        <div class="row">
            @component('reports.partials.summary-box', [
                'value' => \App\Helpers\ReportHelper::formatCurrency($metrics['total_sales']),
                'label' => 'Total Sales',
                'icon' => 'fas fa-chart-line',
                'color' => 'info'
            ])@endcomponent

            @component('reports.partials.summary-box', [
                'value' => $metrics['total_transactions'],
                'label' => 'Total Transactions',
                'icon' => 'fas fa-shopping-cart',
                'color' => 'success'
            ])@endcomponent

            @component('reports.partials.summary-box', [
                'value' => \App\Helpers\ReportHelper::formatCurrency($metrics['average_value']),
                'label' => 'Average Value',
                'icon' => 'fas fa-calculator',
                'color' => 'warning'
            ])@endcomponent

            @component('reports.partials.summary-box', [
                'value' => $metrics['cash_count'] . ' / ' . $metrics['loan_count'],
                'label' => 'Cash / Loan Sales',
                'icon' => 'fas fa-money-bill-wave',
                'color' => 'danger'
            ])@endcomponent
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
                                    <h4>{{ \App\Helpers\ReportHelper::formatCurrency($metrics['cash_sales']) }}</h4>
                                    <p class="text-muted">Cash Sales</p>
                                </div>
                            </div>
                            <div class="col-6 text-center">
                                <div class="text-primary">
                                    <h4>{{ \App\Helpers\ReportHelper::formatCurrency($metrics['loan_sales']) }}</h4>
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
                                    <h4>{{ \App\Helpers\ReportHelper::formatCurrency($metrics['cash_mode_sales']) }}</h4>
                                    <p class="text-muted">Cash Mode</p>
                                </div>
                            </div>
                            <div class="col-6 text-center">
                                <div class="text-info">
                                    <h4>{{ \App\Helpers\ReportHelper::formatCurrency($metrics['online_mode_sales']) }}</h4>
                                    <p class="text-muted">Online Mode</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Simplified table using reusable component -->
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
                                    @php $badge = \App\Helpers\ReportHelper::getSaleTypeBadge($sale->sale_type); @endphp
                                    <span class="badge badge-{{ $badge }}">{{ ucfirst($sale->sale_type) }}</span>
                                </td>
                                <td>
                                    @php $mode = \App\Helpers\ReportHelper::getPaymentModeBadge($sale->payment_mode); @endphp
                                    <span class="badge badge-{{ $mode }}">{{ ucfirst($sale->payment_mode) }}</span>
                                </td>
                                <td>{{ \App\Helpers\ReportHelper::formatCurrency($sale->total_amount) }}</td>
                                <td>
                                    @if($sale->sale_type === 'cash')
                                        <span class="badge badge-success">Paid</span>
                                    @elseif($sale->loan)
                                        <span class="badge badge-{{ \App\Helpers\ReportHelper::getLoanStatusBadge($sale->loan->status) }}">
                                            {{ ucfirst($sale->loan->status) }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $sale->user->name ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            @include('reports.partials.empty-state', ['columns' => 8])
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
