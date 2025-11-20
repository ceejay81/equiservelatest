@extends('layouts.app')

@php($pageTitle = 'Daily Reconciliation')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/modern-inventory.css') }}">
@endpush

@section('content')

<!-- Header -->
<div class="page-header-gradient">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h2 class="mb-2" style="font-weight: 700;">Daily Reconciliation</h2>
            <p class="mb-0" style="opacity: 0.9;">Review and verify all online payments for {{ $date->format('F d, Y') }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
            <form method="GET" class="d-flex align-items-center">
                <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="form-control mr-2">
                <button type="submit" class="btn-modern btn-modern-primary">
                    <i class="fas fa-calendar"></i> Change Date
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="stats-grid">
    <div class="stat-card-modern success">
        <div class="stat-content">
            <div class="stat-value">₱{{ number_format($totalCashReceived / 1000, 1) }}K</div>
            <div class="stat-label">Cash Received</div>
        </div>
        <i class="fas fa-money-bill-wave stat-icon"></i>
    </div>
    <div class="stat-card-modern info">
        <div class="stat-content">
            <div class="stat-value">₱{{ number_format($totalOnlineReceived / 1000, 1) }}K</div>
            <div class="stat-label">Online Received</div>
        </div>
        <i class="fas fa-credit-card stat-icon"></i>
    </div>
    <div class="stat-card-modern warning">
        <div class="stat-content">
            <div class="stat-value">₱{{ number_format($totalSalesValue / 1000, 1) }}K</div>
            <div class="stat-label">Total Sales Value</div>
        </div>
        <i class="fas fa-chart-line stat-icon"></i>
    </div>
    <div class="stat-card-modern danger">
        <div class="stat-content">
            <div class="stat-value">₱{{ number_format(($totalCashReceived + $totalOnlineReceived) / 1000, 1) }}K</div>
            <div class="stat-label">Total Collected</div>
        </div>
        <i class="fas fa-calculator stat-icon"></i>
    </div>
</div>

<!-- Breakdown Summary -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="mb-3" style="font-weight: 600;">Daily Breakdown</h5>
        <div class="row">
            <div class="col-md-6">
                <h6 style="color: #10B981; font-weight: 600;">Cash Breakdown</h6>
                <table class="table table-sm">
                    <tr>
                        <td>Cash Sales</td>
                        <td class="text-right font-weight-bold">₱{{ number_format($totalCashSales, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Down Payments (Cash)</td>
                        <td class="text-right font-weight-bold">₱{{ number_format($loanSales->where('payment_mode', 'cash')->sum(function($sale) { return $sale->loan ? $sale->loan->down_payment : 0; }), 2) }}</td>
                    </tr>
                    <tr>
                        <td>Loan Payments (Cash)</td>
                        <td class="text-right font-weight-bold">₱{{ number_format($loanPayments->where('mode_of_payment', 'Cash')->sum('amount_paid'), 2) }}</td>
                    </tr>
                    <tr style="border-top: 2px solid #10B981;">
                        <td><strong>Total Cash</strong></td>
                        <td class="text-right" style="font-size: 1.1rem; font-weight: 700; color: #10B981;">₱{{ number_format($totalCashReceived, 2) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 style="color: #3B82F6; font-weight: 600;">Online Breakdown</h6>
                <table class="table table-sm">
                    <tr>
                        <td>Online Sales</td>
                        <td class="text-right font-weight-bold">₱{{ number_format($totalOnlineSales, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Down Payments (Online)</td>
                        <td class="text-right font-weight-bold">₱{{ number_format($loanSales->where('payment_mode', 'online')->sum(function($sale) { return $sale->loan ? $sale->loan->down_payment : 0; }), 2) }}</td>
                    </tr>
                    <tr>
                        <td>Loan Payments (Online)</td>
                        <td class="text-right font-weight-bold">₱{{ number_format($loanPayments->where('mode_of_payment', '!=', 'Cash')->sum('amount_paid'), 2) }}</td>
                    </tr>
                    <tr style="border-top: 2px solid #3B82F6;">
                        <td><strong>Total Online</strong></td>
                        <td class="text-right" style="font-size: 1.1rem; font-weight: 700; color: #3B82F6;">₱{{ number_format($totalOnlineReceived, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- All Transactions Table -->
<div class="table-modern">
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
        <h5 class="mb-0">All Transactions for {{ $date->format('F d, Y') }}</h5>
        <button onclick="window.print()" class="btn-modern btn-modern-outline">
            <i class="fas fa-print"></i> Print Report
        </button>
    </div>
    
    @if($cashSales->count() > 0 || $onlineSales->count() > 0 || $loanSales->count() > 0 || $loanPayments->count() > 0)
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>TIME</th>
                    <th>TYPE</th>
                    <th>SALE #</th>
                    <th>CUSTOMER</th>
                    <th>PAYMENT MODE</th>
                    <th>REFERENCE</th>
                    <th class="text-right">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                {{-- Cash Sales --}}
                @foreach($cashSales as $sale)
                <tr>
                    <td>{{ $sale->created_at->format('h:i A') }}</td>
                    <td><span class="badge badge-success">Cash Sale</span></td>
                    <td><a href="{{ url('/sales/' . $sale->id) }}">{{ $sale->sale_number }}</a></td>
                    <td>{{ $sale->customer->full_name }}</td>
                    <td><span class="badge badge-secondary">Cash</span></td>
                    <td class="text-muted">—</td>
                    <td class="text-right font-weight-bold">₱{{ number_format($sale->total_amount, 2) }}</td>
                </tr>
                @endforeach
                
                {{-- Online Sales --}}
                @foreach($onlineSales as $sale)
                <tr>
                    <td>{{ $sale->created_at->format('h:i A') }}</td>
                    <td><span class="badge badge-info">Online Sale</span></td>
                    <td><a href="{{ url('/sales/' . $sale->id) }}">{{ $sale->sale_number }}</a></td>
                    <td>{{ $sale->customer->full_name }}</td>
                    <td><span class="badge badge-info">{{ $sale->payment_bank ?? 'Online' }}</span></td>
                    <td><code class="bg-light p-1 rounded">{{ $sale->reference_number }}</code></td>
                    <td class="text-right font-weight-bold">₱{{ number_format($sale->total_amount, 2) }}</td>
                </tr>
                @endforeach
                
                {{-- Loan Down Payments --}}
                @foreach($loanSales as $sale)
                <tr>
                    <td>{{ $sale->created_at->format('h:i A') }}</td>
                    <td><span class="badge badge-warning">Down Payment</span></td>
                    <td><a href="{{ url('/sales/' . $sale->id) }}">{{ $sale->sale_number }}</a></td>
                    <td>{{ $sale->customer->full_name }}</td>
                    <td><span class="badge badge-{{ $sale->payment_mode == 'cash' ? 'secondary' : 'info' }}">{{ ucfirst($sale->payment_mode) }}</span></td>
                    <td>{{ $sale->reference_number ? $sale->reference_number : '—' }}</td>
                    <td class="text-right font-weight-bold">₱{{ number_format($sale->loan ? $sale->loan->down_payment : 0, 2) }}</td>
                </tr>
                @endforeach
                
                {{-- Loan Payments --}}
                @foreach($loanPayments as $payment)
                <tr>
                    <td>{{ $payment->payment_date->format('h:i A') }}</td>
                    <td><span class="badge badge-primary">Loan Payment</span></td>
                    <td><a href="{{ url('/loans/' . $payment->loan_id) }}">{{ $payment->loan->sale->sale_number ?? 'N/A' }}</a></td>
                    <td>{{ $payment->loan->sale->customer->full_name ?? 'N/A' }}</td>
                    <td><span class="badge badge-{{ $payment->mode_of_payment == 'Cash' ? 'secondary' : 'info' }}">{{ $payment->mode_of_payment }}</span></td>
                    <td>{{ $payment->reference_number ?? '—' }}</td>
                    <td class="text-right font-weight-bold">₱{{ number_format($payment->amount_paid, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="background: #F8FAFC;">
                <tr>
                    <td colspan="6" class="font-weight-bold">TOTAL COLLECTED</td>
                    <td class="text-right font-weight-bold text-success" style="font-size: 1.1rem;">
                        ₱{{ number_format($totalCashReceived + $totalOnlineReceived, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    @else
    <div class="text-center py-5">
        <i class="fas fa-university" style="font-size: 3rem; opacity: 0.3; color: #64748b;"></i>
        <p class="text-muted mt-3 mb-0">No online payments found for this date</p>
    </div>
    @endif
</div>

<!-- Bank Verification Section -->
<div class="filter-card-modern mt-4">
    <h5 class="mb-3">Bank Account Verification</h5>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="filter-label">BANK STATEMENT TOTAL</label>
                <input type="number" step="0.01" class="form-control modern-input" 
                       placeholder="Enter total from bank statement" id="bankTotal">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="filter-label">SYSTEM TOTAL</label>
                <input type="text" class="form-control modern-input" 
                       value="₱{{ number_format($totalOnlineReceived, 2) }}" readonly>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button onclick="verifyTotals()" class="btn-modern btn-modern-success">
                <i class="fas fa-check"></i> Verify Totals
            </button>
            <div id="verificationResult" class="mt-3"></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function verifyTotals() {
    const bankTotal = parseFloat(document.getElementById('bankTotal').value) || 0;
    const systemTotal = {{ $totalOnlineReceived }};
    const difference = Math.abs(bankTotal - systemTotal);
    const resultDiv = document.getElementById('verificationResult');
    
    if (difference < 0.01) {
        resultDiv.innerHTML = `
            <div class="alert alert-success">
                <i class="fas fa-check-circle mr-2"></i>
                <strong>Perfect Match!</strong> Bank statement and system totals match.
            </div>
        `;
    } else {
        resultDiv.innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Discrepancy Found!</strong> 
                Difference: ₱${difference.toFixed(2)}<br>
                Bank: ₱${bankTotal.toFixed(2)} | System: ₱${systemTotal.toFixed(2)}
            </div>
        `;
    }
}

function showProofImage(imageUrl, reference) {
  document.getElementById('proofImage').src = imageUrl;
  document.getElementById('proofReference').textContent = reference;
  document.getElementById('proofDownload').href = imageUrl;
  $('#proofImageModal').modal('show');
}
</script>

<!-- Payment Proof Image Modal -->
<div class="modal fade" id="proofImageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-image mr-2" style="color: #3B82F6;"></i>
          Payment Proof
        </h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body text-center" style="background: #F8FAFC;">
        <div class="mb-3">
          <strong>Reference Number:</strong> <span id="proofReference" class="text-muted"></span>
        </div>
        <img id="proofImage" src="" alt="Payment Proof" style="max-width: 100%; max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
      </div>
      <div class="modal-footer">
        <a id="proofDownload" href="" download class="btn btn-outline-primary">
          <i class="fas fa-download mr-1"></i>Download
        </a>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endpush
