@extends('layouts.app')

@php($pageTitle = 'Accounts Receivable')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/loan-components.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('css/loans.css') }}?v={{ time() }}">
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
    <x-loans.stat-card 
      icon="file-invoice-dollar"
      color="blue"
      label="Total Receivable"
      value="₱{{ number_format($stats['total_receivable'], 2) }}"
      :meta="$stats['active_loans'] . ' active loans'"
    />
  </div>
  <div class="col-md-3 mb-3">
    <x-loans.stat-card 
      icon="check-circle"
      color="green"
      label="Current (Not Due)"
      value="₱{{ number_format($stats['current_not_due'], 2) }}"
      :meta="($stats['total_receivable'] > 0 ? number_format(($stats['current_not_due'] / $stats['total_receivable']) * 100, 1) : 0) . '% of total'"
    />
  </div>
  <div class="col-md-3 mb-3">
    <x-loans.stat-card 
      icon="exclamation-triangle"
      color="red"
      label="Total Overdue"
      value="₱{{ number_format($stats['total_overdue'], 2) }}"
      :meta="($stats['total_receivable'] > 0 ? number_format(($stats['total_overdue'] / $stats['total_receivable']) * 100, 1) : 0) . '% of total'"
    />
  </div>
  <div class="col-md-3 mb-3">
    <x-loans.stat-card 
      icon="users"
      color="orange"
      label="Overdue Accounts"
      value="{{ $stats['overdue_count'] }}"
      meta="Require attention"
    />
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
        <x-loans.aging-card 
          status="current"
          label="Current (Not Due)"
          amount="{{ $aging['current'] }}"
          :percentage="$stats['total_receivable'] > 0 ? ($aging['current'] / $stats['total_receivable']) * 100 : 0"
        />
      </div>
      <div class="col-md-3 mb-3">
        <x-loans.aging-card 
          status="warning"
          label="1-30 Days Overdue"
          amount="{{ $aging['overdue_1_30'] }}"
          :percentage="$stats['total_receivable'] > 0 ? ($aging['overdue_1_30'] / $stats['total_receivable']) * 100 : 0"
        />
      </div>
      <div class="col-md-3 mb-3">
        <x-loans.aging-card 
          status="danger"
          label="31-60 Days Overdue"
          amount="{{ $aging['overdue_31_60'] }}"
          :percentage="$stats['total_receivable'] > 0 ? ($aging['overdue_31_60'] / $stats['total_receivable']) * 100 : 0"
        />
      </div>
      <div class="col-md-3 mb-3">
        <x-loans.aging-card 
          status="critical"
          label="60+ Days Overdue"
          amount="{{ $aging['overdue_60_plus'] }}"
          :percentage="$stats['total_receivable'] > 0 ? ($aging['overdue_60_plus'] / $stats['total_receivable']) * 100 : 0"
        />
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
          <label class="form-label-small">Date From</label>
          <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2 mb-2">
          <label class="form-label-small">Date To</label>
          <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-2 mb-2">
          <label class="form-label-small">Status</label>
          <select name="status" class="form-control">
            <option value="">All Status</option>
            <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
            <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
          </select>
        </div>
        <div class="col-md-4 mb-2">
          <label class="form-label-small">Search</label>
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
      <table class="table table-hover mb-0 align-middle payment-table">
        <thead>
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
                <x-loans.status-badge :status="$loan->status" :isOverdue="$loan->is_overdue" />
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
              <td colspan="10">
                <div class="empty-state">
                  <div class="empty-state-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                  </div>
                  <p class="empty-state-title">No loans found</p>
                  <p class="empty-state-text">
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
