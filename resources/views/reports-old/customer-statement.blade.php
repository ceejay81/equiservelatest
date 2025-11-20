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
                    <li class="breadcrumb-item active">Customer Statement</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- Customer Selection -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user"></i> Select Customer</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.customer-statement') }}">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Customer</label>
                                <select name="customer_id" class="form-control" required>
                                    <option value="">Choose Customer...</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->full_name }} - {{ $customer->contact }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-file-alt"></i> Generate Statement
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($statement)
            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <button onclick="window.print()" class="btn btn-secondary">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <a href="{{ route('reports.customer-statement.pdf', $statement['customer']->id) }}" target="_blank" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Export to PDF
                    </a>
                </div>
            </div>

            <!-- Statement Content -->
            <div class="card" id="statement-content">
                <div class="card-body">
                    <!-- Header -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h3>EquiServe</h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <h4>CUSTOMER STATEMENT</h4>
                            <p class="mb-0">Statement Date: {{ now()->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <hr>

                    <!-- Customer Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Customer Information</h5>
                            <p class="mb-1"><strong>Name:</strong> {{ $statement['customer']->full_name }}</p>
                            <p class="mb-1"><strong>Contact:</strong> {{ $statement['customer']->contact }}</p>
                            <p class="mb-1"><strong>Address:</strong> {{ $statement['customer']->address ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Account Summary</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Total Purchases:</strong></td>
                                    <td class="text-right">₱{{ number_format($statement['total_purchases'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Paid:</strong></td>
                                    <td class="text-right">₱{{ number_format($statement['total_paid'], 2) }}</td>
                                </tr>
                                <tr class="table-warning">
                                    <td><strong>Outstanding Balance:</strong></td>
                                    <td class="text-right"><strong>₱{{ number_format($statement['outstanding'], 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Transaction History -->
                    <h5 class="mb-3">Transaction History</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th class="text-right">Debit</th>
                                    <th class="text-right">Credit</th>
                                    <th class="text-right">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $runningBalance = 0;
                                    $transactions = collect();
                                    
                                    // Add sales
                                    foreach($statement['sales'] as $sale) {
                                        $transactions->push([
                                            'date' => $sale->created_at,
                                            'description' => 'Sale ' . $sale->sale_number . ' (' . ucfirst($sale->sale_type) . ')',
                                            'debit' => $sale->total_amount,
                                            'credit' => 0,
                                            'type' => 'sale'
                                        ]);
                                        
                                        // Add down payment if loan
                                        if($sale->sale_type === 'loan' && $sale->loan) {
                                            $transactions->push([
                                                'date' => $sale->created_at,
                                                'description' => 'Down Payment - ' . $sale->sale_number,
                                                'debit' => 0,
                                                'credit' => $sale->loan->down_payment,
                                                'type' => 'payment'
                                            ]);
                                        } elseif($sale->sale_type === 'cash') {
                                            $transactions->push([
                                                'date' => $sale->created_at,
                                                'description' => 'Payment - ' . $sale->sale_number,
                                                'debit' => 0,
                                                'credit' => $sale->total_amount,
                                                'type' => 'payment'
                                            ]);
                                        }
                                    }
                                    
                                    // Add loan payments
                                    foreach($statement['payments'] as $payment) {
                                        $transactions->push([
                                            'date' => $payment->payment_date,
                                            'description' => 'Loan Payment - ' . ($payment->loan->sale->sale_number ?? 'N/A'),
                                            'debit' => 0,
                                            'credit' => $payment->amount_paid,
                                            'type' => 'payment'
                                        ]);
                                    }
                                    
                                    $transactions = $transactions->sortBy('date');
                                @endphp
                                
                                @foreach($transactions as $transaction)
                                    @php
                                        $runningBalance += $transaction['debit'] - $transaction['credit'];
                                    @endphp
                                    <tr>
                                        <td>{{ $transaction['date']->format('M d, Y') }}</td>
                                        <td>{{ $transaction['description'] }}</td>
                                        <td class="text-right">
                                            @if($transaction['debit'] > 0)
                                                ₱{{ number_format($transaction['debit'], 2) }}
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if($transaction['credit'] > 0)
                                                ₱{{ number_format($transaction['credit'], 2) }}
                                            @endif
                                        </td>
                                        <td class="text-right">₱{{ number_format($runningBalance, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Active Loans -->
                    @if($statement['active_loans']->count() > 0)
                        <h5 class="mb-3 mt-4">Active Loans</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Loan Number</th>
                                        <th>Loan Amount</th>
                                        <th>Balance</th>
                                        <th>Monthly Amount</th>
                                        <th>Next Due Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statement['active_loans'] as $loan)
                                        <tr>
                                            <td>{{ $loan->sale->sale_number ?? 'N/A' }}</td>
                                            <td>₱{{ number_format($loan->loan_amount, 2) }}</td>
                                            <td>₱{{ number_format($loan->balance, 2) }}</td>
                                            <td>₱{{ number_format($loan->monthly_amount, 2) }}</td>
                                            <td>{{ $loan->next_due_date ? $loan->next_due_date->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $loan->status === 'active' ? 'primary' : 'danger' }}">
                                                    {{ ucfirst($loan->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <!-- Footer -->
                    <div class="row mt-5">
                        <div class="col-md-12 text-center">
                            <p class="text-muted small">
                                This is a computer-generated statement and does not require a signature.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
@media print {
    .content-header, .breadcrumb, .card:first-child, .card:nth-child(2), nav, aside, footer {
        display: none !important;
    }
    #statement-content {
        box-shadow: none !important;
        border: none !important;
    }
}
</style>
@endsection
