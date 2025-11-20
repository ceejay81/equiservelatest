<!DOCTYPE html>
<html>
<head>
    <title>Amortization Schedule - Loan #{{ $loan->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        h1 { font-size: 18px; text-align: center; margin-bottom: 5px; }
        h2 { font-size: 14px; text-align: center; margin-top: 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .info-section { margin: 20px 0; }
        .info-table { border: none; }
        .info-table td { border: none; padding: 5px; }
        .highlight { background-color: #fff3cd; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-warning { color: #ffc107; }
        @media print { body { margin: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
            Print Schedule
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
            Close
        </button>
    </div>

    <h1>LOAN AMORTIZATION SCHEDULE</h1>
    <h2>Loan #LN-{{ str_pad($loan->id, 6, '0', STR_PAD_LEFT) }}</h2>

    <!-- Loan Information -->
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td width="25%"><strong>Customer:</strong></td>
                <td width="25%">{{ $customer->full_name ?? 'N/A' }}</td>
                <td width="25%"><strong>Loan Date:</strong></td>
                <td width="25%">{{ $loan->start_date->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td><strong>Total Amount:</strong></td>
                <td>₱{{ number_format($loan->loan_amount, 2) }}</td>
                <td><strong>Maturity Date:</strong></td>
                <td>{{ $loan->maturity_date ? $loan->maturity_date->format('F d, Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Down Payment:</strong></td>
                <td>₱{{ number_format($loan->down_payment, 2) }}</td>
                <td><strong>Monthly Payment:</strong></td>
                <td>₱{{ number_format($loan->monthly_amount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Principal Amount:</strong></td>
                <td>₱{{ number_format($summary['principal_amount'], 2) }}</td>
                <td><strong>Term:</strong></td>
                <td>{{ $loan->term_months }} months</td>
            </tr>
        </table>
    </div>

    <!-- Penalty Terms -->
    <div class="info-section">
        <h3 style="margin-bottom: 10px;">Penalty Terms</h3>
        <table class="info-table">
            <tr>
                <td width="25%"><strong>Grace Period:</strong></td>
                <td width="25%">{{ $loan->grace_period_days }} days</td>
                <td width="25%"><strong>Late Penalty:</strong></td>
                <td width="25%">{{ number_format($loan->late_penalty_rate, 2) }}%</td>
            </tr>
            <tr>
                <td><strong>Maturity Penalty:</strong></td>
                <td>{{ number_format($loan->maturity_penalty_rate, 2) }}%</td>
                <td><strong>Available Rebates:</strong></td>
                <td class="text-success">₱{{ number_format($total_rebate_balance, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Payment Schedule -->
    <table>
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th>Due Date</th>
                <th>Grace Until</th>
                <th class="text-right">Payment</th>
                <th class="text-right">Rebate</th>
                <th class="text-right">After Rebate</th>
                <th class="text-right">If Late</th>
                <th class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schedule as $payment)
            <tr class="{{ $payment['is_maturity'] ? 'highlight' : '' }}">
                <td class="text-center"><strong>{{ $payment['month'] }}</strong></td>
                <td>{{ $payment['due_date']->format('M d, Y') }}</td>
                <td>{{ $payment['grace_period_end']->format('M d, Y') }}</td>
                <td class="text-right">₱{{ number_format($payment['payment'], 2) }}</td>
                <td class="text-right text-success">
                    @if($payment['available_rebate'] > 0)
                        -₱{{ number_format($payment['available_rebate'], 2) }}
                    @else
                        ₱0.00
                    @endif
                </td>
                <td class="text-right">₱{{ number_format($payment['amount_after_rebate'], 2) }}</td>
                <td class="text-right text-danger">₱{{ number_format($payment['amount_if_late'], 2) }}</td>
                <td class="text-right">₱{{ number_format($payment['balance'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f2f2f2;">
                <th colspan="3" class="text-right">TOTALS:</th>
                <th class="text-right">₱{{ number_format($summary['total_payments'], 2) }}</th>
                <th class="text-right text-success">-₱{{ number_format($total_rebate_balance, 2) }}</th>
                <th class="text-right text-success">₱{{ number_format($summary['best_case_total'], 2) }}</th>
                <th class="text-right text-danger">₱{{ number_format($summary['worst_case_total'], 2) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    <!-- Summary -->
    <div class="info-section">
        <h3>Payment Scenarios</h3>
        <table class="info-table">
            <tr>
                <td width="33%"><strong>Best Case:</strong></td>
                <td width="33%" class="text-success">₱{{ number_format($summary['best_case_total'], 2) }}</td>
                <td width="34%">On-time + Rebates</td>
            </tr>
            <tr>
                <td><strong>Regular:</strong></td>
                <td class="text-warning">₱{{ number_format($summary['total_payments'], 2) }}</td>
                <td>On-time, No Rebates</td>
            </tr>
            <tr>
                <td><strong>Worst Case:</strong></td>
                <td class="text-danger">₱{{ number_format($summary['worst_case_total'], 2) }}</td>
                <td>All Late + Maturity</td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #666;">
        <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html>
