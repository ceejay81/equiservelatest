<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Statement - {{ $statement['customer']->full_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 20px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section h3 {
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .summary-box {
            background-color: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>EquiServe</h2>
        <h3>CUSTOMER STATEMENT</h3>
        <p>Statement Date: {{ now()->format('M d, Y') }}</p>
    </div>

    <div class="info-section">
        <h3>Customer Information</h3>
        <p><strong>Name:</strong> {{ $statement['customer']->full_name }}</p>
        <p><strong>Contact:</strong> {{ $statement['customer']->contact }}</p>
        <p><strong>Address:</strong> {{ $statement['customer']->address ?? 'N/A' }}</p>
    </div>

    <div class="summary-box">
        <h3>Account Summary</h3>
        <table>
            <tr>
                <td><strong>Total Purchases:</strong></td>
                <td class="text-right">₱{{ number_format($statement['total_purchases'], 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total Paid:</strong></td>
                <td class="text-right">₱{{ number_format($statement['total_paid'], 2) }}</td>
            </tr>
            <tr style="background-color: #fff3cd;">
                <td><strong>Outstanding Balance:</strong></td>
                <td class="text-right"><strong>₱{{ number_format($statement['outstanding'], 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="info-section">
        <h3>Transaction History</h3>
        <!-- Replaced inline transaction building with helper function -->
        <table>
            <thead>
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
                    $transactions = \App\Helpers\ReportHelper::buildTransactionHistory(
                        $statement['sales'],
                        $statement['payments']
                    );
                    $runningBalance = 0;
                @endphp

                @foreach($transactions as $transaction)
                    @php $runningBalance += $transaction['debit'] - $transaction['credit']; @endphp
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

    @if($statement['active_loans']->count() > 0)
        <div class="info-section">
            <h3>Active Loans</h3>
            <table>
                <thead>
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
                            <td>{{ ucfirst($loan->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>This is a computer-generated statement and does not require a signature.</p>
        <p>Generated on {{ now()->format('M d, Y h:i A') }}</p>
    </div>
</body>
</html>
