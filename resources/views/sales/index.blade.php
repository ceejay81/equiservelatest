@extends('layouts.app')

@php($pageTitle = 'Sales')

@push('styles')
<style>
  .stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }
  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 12px;
  }
  .stat-icon.blue { background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); color: white; }
  .stat-icon.green { background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; }
  .stat-icon.orange { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); color: white; }
  .stat-icon.purple { background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); color: white; }
  .stat-label {
    font-size: 0.875rem;
    color: #64748B;
    margin-bottom: 4px;
  }
  .stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #0F172A;
  }
  .table tbody tr:hover {
    background-color: #F8FAFC !important;
  }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="mb-1" style="font-size: 1.875rem; font-weight: 700; color: #0F172A;">Sales</h1>
    <p class="text-muted mb-0">Track and manage all sales transactions</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('sales.create') }}" class="btn btn-primary">
      <i class="fas fa-plus mr-1"></i> Create Sale
    </a>
    <a href="{{ route('sales.reconciliation') }}" class="btn btn-info">
      <i class="fas fa-calculator mr-1"></i> Reconciliation
    </a>
  </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon blue">
        <i class="fas fa-calendar-day"></i>
      </div>
      <div class="stat-label">Today</div>
      <div class="stat-value">₱{{ number_format($todaySales / 1000, 1) }}K</div>
      <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">
        <i class="fas fa-hand-holding-usd mr-1"></i>Collected: ₱{{ number_format($todayCollections / 1000, 1) }}K
      </div>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon green">
        <i class="fas fa-calendar-week"></i>
      </div>
      <div class="stat-label">This Week</div>
      <div class="stat-value">₱{{ number_format($weekSales / 1000, 1) }}K</div>
      <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">
        <i class="fas fa-hand-holding-usd mr-1"></i>Collected: ₱{{ number_format($weekCollections / 1000, 1) }}K
      </div>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon orange">
        <i class="fas fa-calendar-alt"></i>
      </div>
      <div class="stat-label">This Month</div>
      <div class="stat-value">₱{{ number_format($monthSales / 1000, 1) }}K</div>
      <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">
        <i class="fas fa-hand-holding-usd mr-1"></i>Collected: ₱{{ number_format($monthCollections / 1000, 1) }}K
      </div>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon purple">
        <i class="fas fa-hourglass-half"></i>
      </div>
      <div class="stat-label">Outstanding</div>
      <div class="stat-value">₱{{ number_format($totalOutstanding / 1000, 1) }}K</div>
      <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">
        <i class="fas fa-clock mr-1"></i>Money still owed
      </div>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET">
      <div class="row align-items-end">
        <div class="col-md-2 mb-2">
          <label class="small text-muted mb-1">Date From</label>
          <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2 mb-2">
          <label class="small text-muted mb-1">Date To</label>
          <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-2 mb-2">
          <label class="small text-muted mb-1">Sale Type</label>
          <select name="sale_type" class="form-control">
            <option value="">All Types</option>
            <option value="cash" {{ request('sale_type')=='cash'?'selected':'' }}>Cash</option>
            <option value="loan" {{ request('sale_type')=='loan'?'selected':'' }}>Loan</option>
          </select>
        </div>
        <div class="col-md-4 mb-2">
          <label class="small text-muted mb-1">Search</label>
          <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Sale #, customer, user">
        </div>
        <div class="col-md-2 mb-2">
          <button class="btn btn-primary btn-block" type="submit">
            <i class="fas fa-filter mr-1"></i> Filter
          </button>
        </div>
      </div>
      @if(request()->hasAny(['date_from', 'date_to', 'sale_type', 'search']))
        <div class="mt-2">
          <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-times mr-1"></i> Clear Filters
          </a>
        </div>
      @endif
    </form>
  </div>
</div>

<!-- Sales Table -->
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead style="background: #F8FAFC;">
          <tr>
            <th>Sale #</th>
            <th>Date & Time</th>
            <th>Customer</th>
            <th>Type</th>
            <th>Payment</th>
            <th class="text-right">Total Amount</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sales as $sale)
            <tr>
              <td>
                <a href="{{ route('sales.show', $sale) }}" style="color: #3B82F6; font-weight: 600; text-decoration: none;">
                  {{ $sale->sale_number }}
                </a>
              </td>
              <td>
                <div style="font-weight: 500;">{{ $sale->created_at->format('M d, Y') }}</div>
                <div style="font-size: 0.8rem; color: #64748B;">{{ $sale->created_at->format('g:i A') }}</div>
              </td>
              <td>
                @if($sale->customer)
                  <div style="font-weight: 500;">{{ $sale->customer->full_name }}</div>
                  <div style="font-size: 0.8rem; color: #64748B;">{{ $sale->customer->account_number }}</div>
                @else
                  <span style="color: #94A3B8;">—</span>
                @endif
              </td>
              <td>
                @if($sale->sale_type === 'cash')
                  <span class="badge badge-success">
                    <i class="fas fa-money-bill-wave mr-1"></i>Cash
                  </span>
                @else
                  <span class="badge badge-primary">
                    <i class="fas fa-file-invoice-dollar mr-1"></i>Loan
                  </span>
                @endif
              </td>
              <td>
                @if($sale->payment_mode === 'cash')
                  <span class="badge badge-secondary">Cash</span>
                @elseif($sale->payment_mode === 'online')
                  <span class="badge badge-info">Online</span>
                @else
                  <span style="color: #94A3B8;">—</span>
                @endif
              </td>
              <td class="text-right">
                <div style="font-weight: 700; color: #10B981; font-size: 1.1rem;">
                  ₱{{ number_format($sale->total_amount, 2) }}
                </div>
              </td>
              <td class="text-right">
                <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-eye mr-1"></i>View
                </a>
                <a href="{{ route('sales.print', $sale) }}" class="btn btn-sm btn-outline-info">
                  <i class="fas fa-file-pdf mr-1"></i>PDF
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-5">
                <div style="color: #94A3B8;">
                  <i class="fas fa-shopping-cart fa-3x mb-3" style="opacity: 0.3;"></i>
                  <p class="mb-2" style="font-size: 1.1rem; font-weight: 500;">No sales found</p>
                  <p class="mb-0" style="font-size: 0.875rem;">
                    @if(request()->hasAny(['date_from', 'date_to', 'sale_type', 'search']))
                      Try adjusting your filters or <a href="{{ route('sales.index') }}">clear filters</a>
                    @else
                      Get started by creating your first sale
                    @endif
                  </p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  
  @if($sales->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center">
      <div class="text-muted small">
        Showing {{ $sales->firstItem() }} to {{ $sales->lastItem() }} of {{ $sales->total() }} sales
      </div>
      <nav>
        {{ $sales->links() }}
      </nav>
    </div>
  @endif
</div>

@endsection
