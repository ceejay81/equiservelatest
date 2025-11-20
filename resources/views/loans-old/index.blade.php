@extends('layouts.app')

@php($pageTitle = 'Accounts Receivable')

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
  .stat-icon.red { background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%); color: white; }
  .stat-icon.orange { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); color: white; }
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
  .aging-card {
    padding: 20px;
    border-radius: 12px;
    border-left: 4px solid;
    transition: transform 0.2s;
  }
  .aging-card:hover {
    transform: translateX(4px);
  }
  .aging-card.current {
    background: #E8F5E9;
    border-color: #4CAF50;
  }
  .aging-card.warning {
    background: #FFF3E0;
    border-color: #FF9800;
  }
  .aging-card.danger {
    background: #FFEBEE;
    border-color: #F44336;
  }
  .aging-card.critical {
    background: #FCE4EC;
    border-color: #880E4F;
  }
  .table tbody tr:hover {
    background-color: #F8FAFC !important;
  }
  .filter-btn {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
  }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="mb-1" style="font-size: 1.875rem; font-weight: 700; color: #0F172A;">Accounts Receivable</h1>
    <p class="text-muted mb-0">Monitor and manage loan accounts and payments</p>
  </div>
  <a href="{{ route('sales.create') }}" class="btn btn-primary">
    <i class="fas fa-plus mr-1"></i> Create Loan Sale
  </a>
</div>

<!-- Financial Summary -->
<div class="row mb-4">
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon blue">
        <i class="fas fa-file-invoice-dollar"></i>
      </div>
      <div class="stat-label">Total Receivable</div>
      <div class="stat-value">₱{{ number_format($stats['total_receivable'], 2) }}</div>
      <small class="text-muted">{{ $stats['active_loans'] }} active loans</small>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon green">
        <i class="fas fa-check-circle"></i>
      </div>
      <div class="stat-label">Current (Not Due)</div>
      <div class="stat-value">₱{{ number_format($stats['current_not_due'], 2) }}</div>
      <small class="text-muted">
        {{ $stats['total_receivable'] > 0 ? number_format(($stats['current_not_due'] / $stats['total_receivable']) * 100, 1) : 0 }}% of total
      </small>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon red">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <div class="stat-label">Total Overdue</div>
      <div class="stat-value">₱{{ number_format($stats['total_overdue'], 2) }}</div>
      <small class="text-muted">
        {{ $stats['total_receivable'] > 0 ? number_format(($stats['total_overdue'] / $stats['total_receivable']) * 100, 1) : 0 }}% of total
      </small>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon orange">
        <i class="fas fa-users"></i>
      </div>
      <div class="stat-label">Overdue Accounts</div>
      <div class="stat-value">{{ $stats['overdue_count'] }}</div>
      <small class="text-muted">Require attention</small>
    </div>
  </div>
</div>

<!-- Aging Analysis -->
<div class="card mb-4">
  <div class="card-body">
    <h6 class="mb-3" style="font-weight: 600; color: #0F172A;">
      <i class="fas fa-chart-bar mr-2" style="color: #3B82F6;"></i>
      Aging Analysis
    </h6>
    <div class="row">
      <div class="col-md-3 mb-3">
        <div class="aging-card current">
          <div style="font-size: 0.875rem; color: #2E7D32; margin-bottom: 4px; font-weight: 600;">Current (Not Due)</div>
          <div style="font-size: 1.5rem; font-weight: 700; color: #1B5E20;">₱{{ number_format($aging['current'], 2) }}</div>
          <div style="font-size: 0.75rem; color: #558B2F; margin-top: 4px;">
            {{ $stats['total_receivable'] > 0 ? number_format(($aging['current'] / $stats['total_receivable']) * 100, 1) : 0 }}% of total
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="aging-card warning">
          <div style="font-size: 0.875rem; color: #E65100; margin-bottom: 4px; font-weight: 600;">1-30 Days Overdue</div>
          <div style="font-size: 1.5rem; font-weight: 700; color: #E65100;">₱{{ number_format($aging['overdue_1_30'], 2) }}</div>
          <div style="font-size: 0.75rem; color: #EF6C00; margin-top: 4px;">
            {{ $stats['total_receivable'] > 0 ? number_format(($aging['overdue_1_30'] / $stats['total_receivable']) * 100, 1) : 0 }}% of total
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="aging-card danger">
          <div style="font-size: 0.875rem; color: #C62828; margin-bottom: 4px; font-weight: 600;">31-60 Days Overdue</div>
          <div style="font-size: 1.5rem; font-weight: 700; color: #B71C1C;">₱{{ number_format($aging['overdue_31_60'], 2) }}</div>
          <div style="font-size: 0.75rem; color: #D32F2F; margin-top: 4px;">
            {{ $stats['total_receivable'] > 0 ? number_format(($aging['overdue_31_60'] / $stats['total_receivable']) * 100, 1) : 0 }}% of total
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="aging-card critical">
          <div style="font-size: 0.875rem; color: #880E4F; margin-bottom: 4px; font-weight: 600;">60+ Days Overdue</div>
          <div style="font-size: 1.5rem; font-weight: 700; color: #4A148C;">₱{{ number_format($aging['overdue_60_plus'], 2) }}</div>
          <div style="font-size: 0.75rem; color: #6A1B9A; margin-top: 4px;">
            {{ $stats['total_receivable'] > 0 ? number_format(($aging['overdue_60_plus'] / $stats['total_receivable']) * 100, 1) : 0 }}% of total
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Quick Filters -->
<div class="mb-3">
  <div class="btn-group" role="group">
    <a href="{{ route('loans.index') }}" class="filter-btn btn {{ !request('aging') && !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">
      <i class="fas fa-list mr-1"></i>All Accounts
    </a>
    <a href="{{ route('loans.index', ['aging' => 'current']) }}" class="filter-btn btn {{ request('aging')=='current' ? 'btn-success' : 'btn-outline-success' }}">
      <i class="fas fa-check mr-1"></i>Current
    </a>
    <a href="{{ route('loans.index', ['aging' => '1-30']) }}" class="filter-btn btn {{ request('aging')=='1-30' ? 'btn-warning' : 'btn-outline-warning' }}">
      <i class="fas fa-clock mr-1"></i>1-30 Days
    </a>
    <a href="{{ route('loans.index', ['aging' => '31-60']) }}" class="filter-btn btn {{ request('aging')=='31-60' ? 'btn-danger' : 'btn-outline-danger' }}">
      <i class="fas fa-exclamation-circle mr-1"></i>31-60 Days
    </a>
    <a href="{{ route('loans.index', ['aging' => '60+']) }}" class="filter-btn btn {{ request('aging')=='60+' ? 'btn-dark' : 'btn-outline-dark' }}">
      <i class="fas fa-exclamation-triangle mr-1"></i>60+ Days
    </a>
  </div>
</div>

<!-- Search & Filters -->
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
          <label class="small text-muted mb-1">Status</label>
          <select name="status" class="form-control">
            <option value="">All Status</option>
            <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
            <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
          </select>
        </div>
        <div class="col-md-4 mb-2">
          <label class="small text-muted mb-1">Search</label>
          <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Customer, sale #">
        </div>
        <div class="col-md-2 mb-2">
          <button class="btn btn-primary btn-block" type="submit">
            <i class="fas fa-filter mr-1"></i> Filter
          </button>
        </div>
      </div>
      @if(request()->hasAny(['date_from', 'date_to', 'status', 'search', 'aging']))
        <div class="mt-2">
          <a href="{{ route('loans.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-times mr-1"></i> Clear Filters
          </a>
        </div>
      @endif
    </form>
  </div>
</div>

<!-- Loans Table -->
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead style="background: #F8FAFC;">
          <tr>
            <th>Sale #</th>
            <th>Customer</th>
            <th class="text-right">Loan Amount</th>
            <th class="text-right">Balance Due</th>
            <th class="text-right">Monthly</th>
            <th class="text-center">Term</th>
            <th>Next Due Date</th>
            <th>Status</th>
            <th style="min-width: 120px;">Progress</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($loans as $loan)
            <tr style="{{ $loan->is_overdue ? 'background-color: #FEE2E2;' : '' }}">
              <td>
                <a href="{{ route('loans.show', $loan) }}" style="color: #3B82F6; font-weight: 600; text-decoration: none;">
                  {{ $loan->sale->sale_number ?? 'N/A' }}
                </a>
                <div style="font-size: 0.75rem; color: #64748B;">{{ $loan->created_at->format('M d, Y') }}</div>
              </td>
              <td>
                <div style="font-weight: 500;">{{ $loan->sale->customer->full_name ?? 'N/A' }}</div>
                <div style="font-size: 0.8rem; color: #64748B;">{{ $loan->sale->customer->contact ?? '' }}</div>
              </td>
              <td class="text-right">
                <div style="font-weight: 600;">₱{{ number_format($loan->loan_amount, 2) }}</div>
                @if($loan->down_payment > 0)
                  <div style="font-size: 0.75rem; color: #64748B;">Down: ₱{{ number_format($loan->down_payment, 2) }}</div>
                @endif
              </td>
              <td class="text-right">
                <div style="font-weight: 700; font-size: 1.1rem; color: {{ $loan->balance > 0 ? '#EF4444' : '#10B981' }};">
                  ₱{{ number_format($loan->balance, 2) }}
                </div>
              </td>
              <td class="text-right">
                <div style="font-weight: 500;">₱{{ number_format($loan->monthly_amount, 2) }}</div>
              </td>
              <td class="text-center">
                <div style="font-weight: 500;">{{ $loan->term_months }} mo</div>
                @if($loan->interest_rate > 0)
                  <div style="font-size: 0.75rem; color: #64748B;">{{ number_format($loan->interest_rate, 2) }}%</div>
                @endif
              </td>
              <td>
                @if($loan->next_due_date)
                  <div style="font-weight: 500;">{{ $loan->next_due_date->format('M d, Y') }}</div>
                  @if($loan->is_overdue)
                    <div style="font-size: 0.75rem; color: #DC2626;">
                      <i class="fas fa-exclamation-triangle"></i> Overdue
                    </div>
                  @else
                    <div style="font-size: 0.75rem; color: #64748B;">{{ $loan->next_due_date->diffForHumans() }}</div>
                  @endif
                @else
                  <span style="color: #94A3B8;">—</span>
                @endif
              </td>
              <td>
                @if($loan->status === 'active')
                  <span class="badge badge-primary">
                    <i class="fas fa-clock mr-1"></i>Active
                  </span>
                @elseif($loan->status === 'completed')
                  <span class="badge badge-success">
                    <i class="fas fa-check-circle mr-1"></i>Completed
                  </span>
                @elseif($loan->status === 'overdue')
                  <span class="badge badge-danger">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Overdue
                  </span>
                @else
                  <span class="badge badge-secondary">{{ ucfirst($loan->status) }}</span>
                @endif
              </td>
              <td>
                <div class="progress" style="height: 24px; border-radius: 8px;">
                  <div class="progress-bar {{ $loan->payment_progress >= 100 ? 'bg-success' : 'bg-info' }}" 
                       role="progressbar" 
                       style="width: {{ min(100, $loan->payment_progress) }}%; font-size: 0.75rem; font-weight: 600;"
                       aria-valuenow="{{ $loan->payment_progress }}" 
                       aria-valuemin="0" 
                       aria-valuemax="100">
                    {{ number_format($loan->payment_progress, 0) }}%
                  </div>
                </div>
              </td>
              <td class="text-right">
                <a href="{{ route('loans.show', $loan) }}" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-eye mr-1"></i>View
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" class="text-center py-5">
                <div style="color: #94A3B8;">
                  <i class="fas fa-file-invoice-dollar fa-3x mb-3" style="opacity: 0.3;"></i>
                  <p class="mb-2" style="font-size: 1.1rem; font-weight: 500;">No loans found</p>
                  <p class="mb-0" style="font-size: 0.875rem;">
                    @if(request()->hasAny(['date_from', 'date_to', 'status', 'search', 'aging']))
                      Try adjusting your filters or <a href="{{ route('loans.index') }}">clear filters</a>
                    @else
                      Loan accounts will appear here once created
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
  
  @if($loans->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center">
      <div class="text-muted small">
        Showing {{ $loans->firstItem() }} to {{ $loans->lastItem() }} of {{ $loans->total() }} loans
      </div>
      <nav>
        {{ $loans->links() }}
      </nav>
    </div>
  @endif
</div>

@endsection
