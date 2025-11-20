<div class="alert alert-info mb-4">
  <div style="display: flex; align-items: center; gap: 12px;">
    <i class="fas fa-shopping-bag" style="font-size: 1.5rem;"></i>
    <div>
      <strong>Complete Purchase History</strong>
      <p class="mb-0 mt-1" style="font-size: 0.9rem;">All sales transactions including cash purchases and loan-based sales.</p>
    </div>
  </div>
</div>

@if($allSales->count() > 0)
  <div class="table-responsive">
    <table class="table rebate-table mb-0">
      <thead>
        <tr>
          <th>Date</th>
          <th>Sale #</th>
          <th>Type</th>
          <th>Products</th>
          <th class="text-right">Amount</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($allSales as $sale)
          <tr>
            <td>
              <div style="font-weight: 500;">{{ $sale->created_at->format('M d, Y') }}</div>
              <div style="font-size: 0.8rem; color: #64748B;">{{ $sale->created_at->format('g:i A') }}</div>
            </td>
            <td>
              <a href="/sales/{{ $sale->id }}" style="color: #3B82F6; font-weight: 600;">
                {{ $sale->sale_number }}
              </a>
            </td>
            <td>
              @if($sale->sale_type === 'cash')
                <span class="badge badge-success">Cash</span>
              @else
                <span class="badge badge-primary">Loan</span>
              @endif
            </td>
            <td>
              <div style="max-width: 250px;">
                @foreach($sale->items->take(2) as $item)
                  <div style="font-size: 0.9rem;">
                    • {{ $item->product->name ?? 'Product' }}
                    @if($item->quantity > 1)
                      <span class="text-muted">(x{{ $item->quantity }})</span>
                    @endif
                  </div>
                @endforeach
                @if($sale->items->count() > 2)
                  <div style="font-size: 0.8rem; color: #64748B;">
                    +{{ $sale->items->count() - 2 }} more items
                  </div>
                @endif
              </div>
            </td>
            <td class="text-right">
              <div style="font-weight: 700; color: #10B981; font-size: 1.1rem;">
                ₱{{ number_format($sale->total_amount, 2) }}
              </div>
            </td>
            <td>
              @if($sale->sale_type === 'cash')
                <span class="badge badge-success">
                  <i class="fas fa-check-circle"></i> Paid
                </span>
              @elseif($sale->loan)
                @if($sale->loan->status === 'completed')
                  <span class="badge badge-success">
                    <i class="fas fa-check-circle"></i> Completed
                  </span>
                @elseif($sale->loan->status === 'active')
                  <span class="badge badge-primary">
                    <i class="fas fa-clock"></i> Active
                  </span>
                @elseif($sale->loan->status === 'overdue')
                  <span class="badge badge-danger">
                    <i class="fas fa-exclamation-triangle"></i> Overdue
                  </span>
                @else
                  <span class="badge badge-secondary">{{ ucfirst($sale->loan->status) }}</span>
                @endif
              @else
                <span class="badge badge-secondary">N/A</span>
              @endif
            </td>
            <td>
              <a href="/sales/{{ $sale->id }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-eye"></i>
              </a>
            </td>
          </tr>
        @endforeach
      </tbody>
      <tfoot style="background: #F8FAFC;">
        <tr>
          <th colspan="4">Total ({{ $totalPurchases }} purchases)</th>
          <th class="text-right">
            <span style="color: #10B981; font-size: 1.1rem;">₱{{ number_format($totalSpent, 2) }}</span>
          </th>
          <th colspan="2"></th>
        </tr>
      </tfoot>
    </table>
  </div>
  
  <div class="mt-4">
    <div class="row">
      <div class="col-md-6">
        <div style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; padding: 20px; border-radius: 12px; text-align: center;">
          <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 8px;">Cash Sales</div>
          <div style="font-size: 2rem; font-weight: 700;">{{ $totalCashSales }}</div>
          <div style="font-size: 1.2rem; margin-top: 8px;">₱{{ number_format($totalCashAmount, 2) }}</div>
        </div>
      </div>
      <div class="col-md-6">
        <div style="background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); color: white; padding: 20px; border-radius: 12px; text-align: center;">
          <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 8px;">Loan Sales</div>
          <div style="font-size: 2rem; font-weight: 700;">{{ $totalLoans }}</div>
          <div style="font-size: 1.2rem; margin-top: 8px;">₱{{ number_format($totalSpent - $totalCashAmount, 2) }}</div>
        </div>
      </div>
    </div>
  </div>
@else
  <div class="empty-state">
    <i class="fas fa-shopping-bag"></i>
    <p>No purchases found for this customer</p>
  </div>
@endif
