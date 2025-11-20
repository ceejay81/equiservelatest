@extends('layouts.app')

@php($pageTitle = 'Customer Profile')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/customer-profile.css') }}">
@endpush

@section('content')

<a href="/customers" class="btn btn-link px-0 mb-3" style="color: #3B82F6; font-weight: 500;">
  <i class="fas fa-arrow-left mr-2"></i> Back to Customers
</a>

@if(session('status'))
  <div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle mr-2"></i>{{ session('status') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
@endif

@if($errors->any())
  <div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle mr-2"></i>
    <strong>Error:</strong>
    <ul class="mb-0 mt-2">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
@endif

<!-- Customer Header -->
<div class="customer-header">
  <div class="d-flex justify-content-between align-items-start flex-wrap">
    <div>
      <h1 class="customer-name">
        {{ $customer->full_name }}
        @if($customerStatus === 'overdue')
          <span class="badge badge-danger" style="font-size: 0.875rem; padding: 6px 12px;">
            <i class="fas fa-exclamation-triangle mr-1"></i> Overdue
          </span>
        @elseif($customerStatus === 'active')
          <span class="badge badge-success" style="font-size: 0.875rem; padding: 6px 12px;">
            <i class="fas fa-check-circle mr-1"></i> Active
          </span>
        @else
          <span class="badge badge-light" style="font-size: 0.875rem; padding: 6px 12px;">
            <i class="fas fa-check-circle mr-1"></i> Good Standing
          </span>
        @endif
      </h1>
      <div class="customer-meta">
        <div class="customer-meta-item">
          <i class="fas fa-id-card"></i>
          <span>{{ $customer->account_number }}</span>
        </div>
        <div class="customer-meta-item">
          <i class="fas fa-phone"></i>
          <span>{{ $customer->contact ?? 'No contact' }}</span>
        </div>
        <div class="customer-meta-item">
          <i class="fas fa-map-marker-alt"></i>
          <span>{{ $customer->address ?? 'No address' }}</span>
        </div>
      </div>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
      @can('manage-customers')
      <a href="/customers/{{ $customer->id }}/edit" class="btn btn-light">
        <i class="fas fa-edit mr-1"></i> Edit
      </a>
      <button class="btn btn-outline-light" data-toggle="modal" data-target="#confirmDeleteModal">
        <i class="fas fa-trash mr-1"></i> Delete
      </button>
      <form id="deleteCustomerForm" action="/customers/{{ $customer->id }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
      </form>
      @endcan
    </div>
  </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon blue">
        <i class="fas fa-shopping-cart"></i>
      </div>
      <div class="stat-label">Total Purchases</div>
      <div class="stat-value">{{ $totalPurchases }}</div>
      <small class="text-muted">All sales</small>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon green">
        <i class="fas fa-dollar-sign"></i>
      </div>
      <div class="stat-label">Total Spent</div>
      <div class="stat-value">₱{{ number_format($totalSpent / 1000, 1) }}K</div>
      <small class="text-muted">Lifetime value</small>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon orange">
        <i class="fas fa-calendar-check"></i>
      </div>
      <div class="stat-label">Last Purchase</div>
      <div class="stat-value" style="font-size: 1.3rem;">{{ $lastPurchase ? $lastPurchase->format('M d') : 'Never' }}</div>
      <small class="text-muted">{{ $lastPurchase ? $lastPurchase->diffForHumans() : 'No purchases' }}</small>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon blue">
        <i class="fas fa-chart-line"></i>
      </div>
      <div class="stat-label">Avg Purchase</div>
      <div class="stat-value">₱{{ number_format($avgPurchase / 1000, 1) }}K</div>
      <small class="text-muted">Per transaction</small>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon blue">
        <i class="fas fa-file-invoice-dollar"></i>
      </div>
      <div class="stat-label">Total Loans</div>
      <div class="stat-value">{{ $totalLoans }}</div>
      <small class="text-muted">Loan accounts</small>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon green">
        <i class="fas fa-check-circle"></i>
      </div>
      <div class="stat-label">Active Loans</div>
      <div class="stat-value">{{ $activeLoans }}</div>
      <small class="text-muted">Currently active</small>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon orange">
        <i class="fas fa-gift"></i>
      </div>
      <div class="stat-label">Rewards Balance</div>
      <div class="stat-value">₱{{ number_format($totalRebatesAmount, 2) }}</div>
      <small class="text-muted">Available rewards</small>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="stat-card">
      <div class="stat-icon red">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <div class="stat-label">Overdue Loans</div>
      <div class="stat-value">{{ $overdueLoans }}</div>
      <small class="text-muted">Need attention</small>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-8 mb-3">
    <div class="modern-tabs">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" data-toggle="tab" href="#tab-purchases" role="tab">
            <i class="fas fa-shopping-bag mr-1"></i> Purchase History
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#tab-loans" role="tab">
            <i class="fas fa-file-invoice-dollar mr-1"></i> Loans
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#tab-payments" role="tab">
            <i class="fas fa-money-bill-wave mr-1"></i> Payments
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#tab-rebates" role="tab">
            <i class="fas fa-gift mr-1"></i> Rewards
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#tab-activity" role="tab">
            <i class="fas fa-history mr-1"></i> Activity
          </a>
        </li>
      </ul>
      
      <div class="tab-content">
        <div class="tab-pane fade show active" id="tab-purchases" role="tabpanel">
          @include('customers.partials._tab_purchases')
        </div>

        <div class="tab-pane fade" id="tab-loans" role="tabpanel">
          @include('customers.partials._tab_loans')
        </div>

        <div class="tab-pane fade" id="tab-payments" role="tabpanel">
          @include('customers.partials._tab_payments')
        </div>

        <div class="tab-pane fade" id="tab-rebates" role="tabpanel">
          @include('customers.partials._tab_rebates')
        </div>

        <div class="tab-pane fade" id="tab-activity" role="tabpanel">
          @include('customers.partials._tab_activity')
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 mb-3">
    <div class="action-card">
      <h6><i class="fas fa-bolt mr-2" style="color: #F59E0B;"></i>Quick Actions</h6>
      <a href="/sales/create?customer={{ $customer->id }}" class="btn btn-primary action-btn">
        <i class="fas fa-shopping-cart mr-2"></i> New Sale
      </a>
      @if($activeLoans > 0)
        <a href="/loans?search={{ $customer->account_number }}" class="btn btn-outline-primary action-btn">
          <i class="fas fa-money-bill-wave mr-2"></i> View Loans & Payments
        </a>
      @endif
      <button class="btn btn-outline-success action-btn" data-toggle="modal" data-target="#awardRebateModal">
        <i class="fas fa-gift mr-2"></i> Award Reward
      </button>
      @can('manage-customers')
      <a href="/customers/{{ $customer->id }}/edit" class="btn btn-outline-secondary action-btn">
        <i class="fas fa-edit mr-2"></i> Edit Customer
      </a>
      @endcan
    </div>
  </div>
</div>

@endsection
 
@push('scripts')
<!-- Confirm Delete Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Confirm Delete</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <p class="mb-2">This action cannot be undone. To confirm, type the account number below:</p>
        <div class="form-group mb-0">
          <label class="small text-muted">Account #</label>
          <input type="text" class="form-control" id="deleteConfirmInput" placeholder="Type: {{ $customer->account_number }}">
          <small class="form-text text-muted">Must match exactly: <strong>{{ $customer->account_number }}</strong></small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn" disabled>Delete</button>
      </div>
    </div>
  </div>
</div>

<script>
  (function(){
  // Delete confirmation
  const requiredValue = {!! json_encode($customer->account_number, JSON_HEX_APOS|JSON_HEX_QUOT) !!};
    const input = document.getElementById('deleteConfirmInput');
    const btn = document.getElementById('confirmDeleteBtn');
    const form = document.getElementById('deleteCustomerForm');
    if (input && btn && form) {
      input.addEventListener('input', function(){
        btn.disabled = this.value.trim() !== requiredValue;
      });
      btn.addEventListener('click', function(){
        if (!btn.disabled) {
          btn.disabled = true;
          btn.textContent = 'Deleting...';
          form.submit();
        }
      });
    }
    // Existing placeholders and modals scripts below
  })();
</script>

<!-- Award Rebate Modal -->
<div class="modal fade" id="awardRebateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-gift mr-2" style="color: #10B981;"></i>
          Award Customer Reward
        </h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form method="POST" action="/customers/{{ $customer->id }}/rebates">
        @csrf
        <div class="modal-body">
          <div class="alert alert-success">
            <strong>Rewarding: {{ $customer->full_name }}</strong><br>
            <small>Account: {{ $customer->account_number }}</small>
          </div>
          
          <div class="form-group">
            <label>Reward Amount <span class="text-danger">*</span></label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">₱</span>
              </div>
              <input type="number" step="0.01" name="rebate_amount" class="form-control" placeholder="0.00" min="0" required>
            </div>
            <small class="text-muted">Amount to award as customer reward</small>
          </div>
          
          <div class="form-group">
            <label>Related Sale (Optional)</label>
            <select name="sale_id" class="form-control">
              <option value="">General Reward</option>
              @foreach($customer->sales as $sale)
                <option value="{{ $sale->id }}">{{ $sale->sale_number }} - ₱{{ number_format($sale->total_amount, 2) }}</option>
              @endforeach
            </select>
            <small class="text-muted">Link this reward to a specific sale (optional)</small>
          </div>
          
          <div class="form-group">
            <label>Product (Optional)</label>
            <input type="text" name="product_note" class="form-control" placeholder="e.g., Honda XRM 125, Helmet">
            <small class="text-muted">Note what product this reward is for (optional)</small>
          </div>
          
          <div class="form-group mb-0">
            <label>Reason for Reward</label>
            <select class="form-control">
              <option>On-time payment bonus</option>
              <option>Loan completion reward</option>
              <option>Loyalty reward</option>
              <option>Referral bonus</option>
              <option>Special promotion</option>
            </select>
            <small class="text-muted">This is for reference only</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-gift mr-1"></i> Award Reward
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

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

<script>
function showProofImage(imageUrl, reference) {
  document.getElementById('proofImage').src = imageUrl;
  document.getElementById('proofReference').textContent = reference;
  document.getElementById('proofDownload').href = imageUrl;
  $('#proofImageModal').modal('show');
}
</script>
@endpush
