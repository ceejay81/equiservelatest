@forelse($loans as $loan)
  <div class="loan-card">
    <div class="loan-header">
      <div class="loan-number">
        <i class="fas fa-file-invoice mr-2" style="color: #3B82F6;"></i>
        Loan #{{ $loan->id }}
      </div>
      <span class="badge {{ $loan->status === 'active' ? 'badge-success' : ($loan->status === 'completed' ? 'badge-primary' : ($loan->status === 'overdue' ? 'badge-danger' : 'badge-secondary')) }}">
        {{ ucfirst($loan->status) }}
      </span>
    </div>
    <div class="loan-details">
      <div class="loan-detail-item">
        <span class="loan-detail-label">Loan Amount</span>
        <span class="loan-detail-value">₱{{ number_format($loan->loan_amount, 2) }}</span>
      </div>
      <div class="loan-detail-item">
        <span class="loan-detail-label">Balance</span>
        <span class="loan-detail-value" style="color: #EF4444;">₱{{ number_format($loan->balance, 2) }}</span>
      </div>
      <div class="loan-detail-item">
        <span class="loan-detail-label">Monthly Payment</span>
        <span class="loan-detail-value">₱{{ number_format($loan->monthly_amount, 2) }}</span>
      </div>
      <div class="loan-detail-item">
        <span class="loan-detail-label">Term</span>
        <span class="loan-detail-value">{{ $loan->term_months }} months</span>
      </div>
    </div>
    <div class="mt-3">
      <a href="/loans/{{ $loan->id }}" class="btn btn-sm btn-outline-primary">
        <i class="fas fa-eye mr-1"></i> View Details
      </a>
    </div>
  </div>
@empty
  <div class="empty-state">
    <i class="fas fa-file-invoice-dollar"></i>
    <p>No loans found for this customer</p>
  </div>
@endforelse
