<div class="alert alert-info mb-4">
  <div style="display: flex; align-items: center; gap: 12px;">
    <i class="fas fa-money-bill-wave" style="font-size: 1.5rem;"></i>
    <div>
      <strong>Payment Timeline</strong>
      <p class="mb-0 mt-1" style="font-size: 0.9rem;">Complete history of all payments made by this customer including loan payments and down payments.</p>
    </div>
  </div>
</div>

@if($allPayments->count() > 0)
  <div class="table-responsive">
    <table class="table rebate-table mb-0">
      <thead>
        <tr>
          <th>Date</th>
          <th>Type</th>
          <th>Related To</th>
          <th>Payment Method</th>
          <th>Reference</th>
          <th class="text-right">Amount</th>
        </tr>
      </thead>
      <tbody>
        @foreach($allPayments as $payment)
          <tr>
            <td>
              <div style="font-weight: 500;">{{ $payment->date->format('M d, Y') }}</div>
              <div style="font-size: 0.8rem; color: #64748B;">{{ $payment->date->format('g:i A') }}</div>
            </td>
            <td>
              @if($payment->type === 'down_payment')
                <span class="badge badge-info">Down Payment</span>
              @else
                <span class="badge badge-success">Loan Payment</span>
              @endif
            </td>
            <td>
              @if($payment->loan && $payment->loan->sale)
                <a href="/loans/{{ $payment->loan->id }}" style="color: #3B82F6;">
                  {{ $payment->loan->sale->sale_number }}
                </a>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td>
              <span class="badge badge-secondary">{{ $payment->mode }}</span>
              @if(isset($payment->bank) && $payment->bank)
                <br><small class="text-muted">{{ $payment->bank }}</small>
              @endif
            </td>
            <td>
              @if(isset($payment->reference) && $payment->reference)
                <code class="bg-light p-1 rounded" style="font-size: 0.85rem;">{{ $payment->reference }}</code>
                @if(isset($payment->proof) && $payment->proof)
                  <br>
                  <button type="button" class="btn btn-xs btn-outline-primary mt-1" onclick="showProofImage('{{ \Storage::url($payment->proof) }}', '{{ $payment->reference }}')">
                    <i class="fas fa-image"></i> View Proof
                  </button>
                @endif
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td class="text-right">
              <div style="font-weight: 700; color: #10B981; font-size: 1.1rem;">
                ₱{{ number_format($payment->amount, 2) }}
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
      <tfoot style="background: #F8FAFC;">
        <tr>
          <th colspan="5">Total Payments ({{ $allPayments->count() }})</th>
          <th class="text-right">
            <span style="color: #10B981; font-size: 1.1rem;">₱{{ number_format($allPayments->sum('amount'), 2) }}</span>
          </th>
        </tr>
      </tfoot>
    </table>
  </div>
  
  <div class="mt-4">
    <div class="summary-box">
      <div class="summary-label">Total Paid to Date</div>
      <div class="summary-value">₱{{ number_format($allPayments->sum('amount'), 2) }}</div>
      <div style="font-size: 0.875rem; opacity: 0.9; margin-top: 8px;">
        Across all loans and purchases
      </div>
    </div>
  </div>
@else
  <div class="empty-state">
    <i class="fas fa-money-bill-wave"></i>
    <p>No payments recorded yet</p>
    <small style="color: #94A3B8;">Payment history will appear here once customer makes payments</small>
  </div>
@endif
