<div class="alert alert-info mb-4">
  <div style="display: flex; align-items: center; gap: 12px;">
    <i class="fas fa-info-circle" style="font-size: 1.5rem;"></i>
    <div>
      <strong>Customer Rewards Program</strong>
      <p class="mb-0 mt-1" style="font-size: 0.9rem;">Customers earn rebates as rewards for on-time payments and good standing. Rebates can be used as discounts on future purchases.</p>
    </div>
  </div>
</div>

@if($rebates->count() > 0)
  <div class="table-responsive">
    <table class="table rebate-table mb-0">
      <thead>
        <tr>
          <th>Date Awarded</th>
          <th>Reason</th>
          <th>Related Sale</th>
          <th>Status</th>
          <th class="text-right">Reward Amount</th>
          <th class="text-center">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rebates as $rebate)
          <tr>
            <td>
              <div style="font-weight: 500;">{{ $rebate->created_at->format('M d, Y') }}</div>
              <div style="font-size: 0.8rem; color: #64748B;">{{ $rebate->created_at->format('g:i A') }}</div>
            </td>
            <td>
              <div style="font-weight: 500;">{{ $rebate->product->name ?? 'Loyalty Reward' }}</div>
              <div style="font-size: 0.8rem; color: #64748B;">
                @if($rebate->product && $rebate->product->sku)
                  Product: {{ $rebate->product->sku }}
                @else
                  Good payment history
                @endif
              </div>
            </td>
            <td>
              @if($rebate->sale)
                <a href="/sales/{{ $rebate->sale->id }}" style="color: #3B82F6;">
                  {{ $rebate->sale->sale_number }}
                </a>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td>
              @if($rebate->status === 'available')
                <span class="badge badge-success">Available</span>
              @else
                <span class="badge badge-secondary">Used</span>
                @if($rebate->used_for === 'loan_payment')
                  <br><small class="text-muted">Applied to loan</small>
                @elseif($rebate->used_for === 'purchase')
                  <br><small class="text-muted">Used on purchase</small>
                @endif
              @endif
            </td>
            <td class="text-right">
              <span style="font-weight: 700; color: {{ $rebate->status === 'available' ? '#10B981' : '#94A3B8' }}; font-size: 1.1rem;">
                {{ $rebate->status === 'available' ? '+' : '' }}₱{{ number_format($rebate->rebate_amount, 2) }}
              </span>
            </td>
            <td class="text-center">
              @if($rebate->status === 'available' && $activeLoans > 0)
                <button type="button" class="btn btn-sm btn-primary" 
                        onclick="showApplyRebateModal({{ $rebate->id }}, {{ $rebate->rebate_amount }})">
                  <i class="fas fa-hand-holding-usd"></i> Apply to Loan
                </button>
              @elseif($rebate->status === 'used')
                <span class="text-muted" style="font-size: 0.85rem;">
                  <i class="fas fa-check-circle"></i> Used {{ $rebate->used_at->format('M d, Y') }}
                </span>
              @else
                <span class="text-muted" style="font-size: 0.85rem;">No active loans</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  
  <div class="mt-4">
    <div class="row">
      <div class="col-md-6">
        <div class="summary-box">
          <div class="summary-label">Available Rewards</div>
          <div class="summary-value">₱{{ number_format($rebates->where('status', 'available')->sum('rebate_amount'), 2) }}</div>
          <div style="font-size: 0.875rem; opacity: 0.9; margin-top: 8px;">
            Can be used on purchases or applied to loans
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="summary-box" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
          <div class="summary-label">Total Earned</div>
          <div class="summary-value" style="color: #64748B;">₱{{ number_format($totalRebatesAmount, 2) }}</div>
          <div style="font-size: 0.875rem; opacity: 0.9; margin-top: 8px;">
            Lifetime rewards earned
          </div>
        </div>
      </div>
    </div>
  </div>
@else
  <div class="empty-state">
    <i class="fas fa-gift"></i>
    <p>No rewards earned yet</p>
    <small style="color: #94A3B8;">Customer will earn rewards for on-time payments and good standing</small>
  </div>
@endif

<!-- Apply Rebate to Loan Modal -->
<div class="modal fade" id="applyRebateModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="/customers/{{ $customer->id }}/rebates/apply-to-loan">
        @csrf
        <input type="hidden" name="rebate_id" id="rebate_id">
        
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-hand-holding-usd mr-2" style="color: #10B981;"></i>
            Apply Rebate to Loan
          </h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="fas fa-info-circle mr-1"></i>
            <strong>Rebate Amount:</strong> ₱<span id="rebate_amount_display">0.00</span>
            <br>
            This amount will be deducted from the selected loan's balance.
          </div>
          
          <div class="form-group">
            <label>Select Loan to Apply Rebate <span class="text-danger">*</span></label>
            <select name="loan_id" class="form-control" required>
              <option value="">Choose a loan...</option>
              @foreach($loans->whereIn('status', ['active', 'overdue']) as $loan)
                <option value="{{ $loan->id }}">
                  {{ $loan->sale->sale_number ?? 'Loan #' . $loan->id }} - 
                  Balance: ₱{{ number_format($loan->balance, 2) }}
                  @if($loan->status === 'overdue')
                    (OVERDUE)
                  @endif
                </option>
              @endforeach
            </select>
            <small class="text-muted">Only active loans are shown</small>
          </div>
          
          @if($loans->whereIn('status', ['active', 'overdue'])->isEmpty())
            <div class="alert alert-warning">
              <i class="fas fa-exclamation-triangle mr-1"></i>
              No active loans available. Customer must have an active loan to apply rebates.
            </div>
          @endif
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success" {{ $loans->whereIn('status', ['active', 'overdue'])->isEmpty() ? 'disabled' : '' }}>
            <i class="fas fa-check mr-1"></i> Apply Rebate
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function showApplyRebateModal(rebateId, rebateAmount) {
  document.getElementById('rebate_id').value = rebateId;
  document.getElementById('rebate_amount_display').textContent = rebateAmount.toFixed(2);
  $('#applyRebateModal').modal('show');
}
</script>
