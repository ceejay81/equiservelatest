@extends('layouts.app')

@php($pageTitle = 'Customers')

@push('styles')
<style>
  .table tbody tr:hover {
    background-color: #F8FAFC !important;
    cursor: pointer;
  }
  .table tbody tr:hover td:first-child {
    background-color: #F8FAFC !important;
  }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h1 class="mb-1" style="font-size: 1.875rem; font-weight: 700; color: #0F172A;">Customers</h1>
    <p class="text-muted mb-0">Manage customer records, loans, and contact details</p>
  </div>
  <div>
    @can('manage-customers')
    <button class="btn btn-primary" id="btnAddCustomer" data-toggle="modal" data-target="#customerModal">
      <i class="fas fa-user-plus mr-1"></i> Add Customer
    </button>
    @endcan
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <form method="GET" action="/customers">
      <div class="row align-items-center">
        <div class="col-md-6 mb-2 mb-md-0">
          <div class="input-group">
            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-search"></i></span></div>
            <input name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by name, account #, or contact">
            <div class="input-group-append"><button class="btn btn-outline-secondary" type="submit">Search</button></div>
          </div>
        </div>
        <div class="col-md-3 mb-2 mb-md-0"></div>
        <div class="col-md-3 text-md-right">
          <select name="sort" class="form-control" onchange="this.form.submit()">
            <option value="">Sort: Name (A–Z)</option>
            <option value="recent" {{ request('sort')==='recent'?'selected':'' }}>Recently Added</option>
          </select>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body p-0">
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
      <div class="d-flex align-items-center gap-2">
        <div class="dropdown">
          <button id="bulkBtn" class="btn btn-outline-secondary btn-sm dropdown-toggle disabled" type="button" data-toggle="dropdown" aria-disabled="true">Bulk Actions</button>
          <div class="dropdown-menu">
            <a class="dropdown-item text-danger" href="#">Delete Selected</a>
          </div>
        </div>
        <span class="text-muted small">Select rows to enable actions</span>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead style="background: #F8FAFC;">
          <tr>
            <th style="width:36px"><input type="checkbox" onclick="document.querySelectorAll('.row-check').forEach(cb=>cb.checked=this.checked)"></th>
            <th>Account #</th>
            <th>Full Name</th>
            <th>Contact</th>
            <th class="text-center">Active Loans</th>
            <th>Last Purchase</th>
            <th>Status</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($customers as $c)
          <tr style="transition: background-color 0.15s ease;">
            <td><input type="checkbox" class="row-check" value="{{ $c->account_number }}"></td>
            <td>
              <a href="/customers/{{ $c->id }}" style="color: #3B82F6; font-weight: 600; text-decoration: none;">
                {{ $c->account_number }}
              </a>
            </td>
            <td style="font-weight: 500;">{{ $c->full_name }}</td>
            <td style="color: #64748B;">{{ $c->contact ?: '—' }}</td>
            <td class="text-center">
              @if(($c->active_loans_count ?? 0) > 0)
                <span class="badge badge-primary" style="font-size: 0.875rem; padding: 4px 10px;">
                  <i class="fas fa-file-invoice-dollar mr-1"></i>{{ $c->active_loans_count }}
                </span>
              @else
                <span style="color: #94A3B8;">—</span>
              @endif
            </td>
            <td>
              @if($c->last_purchase_date)
                <div style="font-size: 0.875rem;">{{ \Carbon\Carbon::parse($c->last_purchase_date)->format('M d, Y') }}</div>
                <div style="font-size: 0.75rem; color: #64748B;">{{ \Carbon\Carbon::parse($c->last_purchase_date)->diffForHumans() }}</div>
              @else
                <span style="color: #94A3B8;">No purchases</span>
              @endif
            </td>
            <td>
              @if(($c->overdue_loans_count ?? 0) > 0)
                <span class="badge badge-danger">
                  <i class="fas fa-exclamation-triangle mr-1"></i>Overdue
                </span>
              @elseif(($c->active_loans_count ?? 0) > 0)
                <span class="badge badge-success">
                  <i class="fas fa-check-circle mr-1"></i>Active
                </span>
              @endif
            </td>
            <td class="text-right">
              <a class="btn btn-sm btn-outline-primary" href="/customers/{{ $c->id }}">
                <i class="fas fa-eye mr-1"></i>View
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-5">
              <div style="color: #94A3B8;">
                <i class="fas fa-users fa-3x mb-3" style="opacity: 0.3;"></i>
                <p class="mb-2" style="font-size: 1.1rem; font-weight: 500;">No customers found</p>
                <p class="mb-0" style="font-size: 0.875rem;">
                  @if(request('q'))
                    Try adjusting your search terms or <a href="/customers">clear filters</a>
                  @else
                    Get started by adding your first customer
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
  <div class="card-footer d-flex justify-content-between align-items-center">
    @if(isset($customers))
      <div class="text-muted small">Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() ?? 0 }}</div>
      <nav>
        <ul class="pagination mb-0">
          <li class="page-item {{ $customers->onFirstPage()?'disabled':'' }}"><a class="page-link" href="{{ $customers->previousPageUrl() ?: '#' }}">Previous</a></li>
          <li class="page-item {{ $customers->hasMorePages()?'':'disabled' }}"><a class="page-link" href="{{ $customers->nextPageUrl() ?: '#' }}">Next</a></li>
        </ul>
      </nav>
    @endif
  </div>
</div>

<!-- Customer Create/Edit Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <div class="d-flex align-items-center">
          <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mr-2" style="width:36px;height:36px;">
            <i class="fas fa-user text-white"></i>
          </div>
          <h5 class="modal-title" id="customerModalTitle">Add Customer</h5>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body pt-2">
        @if ($errors->any())
          <div class="alert alert-danger"><strong>There were problems with your input.</strong><ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <form id="customerForm" method="POST" action="/customers">
          @csrf

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="account_number">Account #</label>
              <input id="account_number" name="account_number" value="{{ old('account_number') }}" class="form-control" placeholder="e.g., ACCT-000123" required autofocus>
            </div>
            <div class="form-group col-md-6">
              <label for="full_name">Full Name</label>
              <input id="full_name" name="full_name" value="{{ old('full_name') }}" class="form-control" placeholder="e.g., Juan Dela Cruz" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="contact">Contact</label>
              <input id="contact" name="contact" value="{{ old('contact') }}" class="form-control" placeholder="e.g., 0917 123 4567">
            </div>
            <div class="form-group col-md-6 mb-0">
              <label for="address">Address</label>
              <input id="address" name="address" value="{{ old('address') }}" class="form-control" placeholder="e.g., Brgy. 123, Quezon City">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer border-0 pt-0 d-flex justify-content-between">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" form="customerForm" class="btn btn-primary" id="customerSubmitBtn">Save</button>
      </div>
    </div>
  </div>
  </div>
<!-- /Customer Modal -->

{{-- Delete handled inline via form per row --}}

@endsection

@push('scripts')
<script>
  (function(){
    // Bulk selection
    const KEY = 'custSelected';
    const bulkBtn = document.getElementById('bulkBtn');
    const checks = Array.from(document.querySelectorAll('.row-check'));
    if (bulkBtn && checks.length) {
      const sync = () => {
        const ids = checks.filter(c => c.checked).map(c => c.value);
        localStorage.setItem(KEY, JSON.stringify(ids));
        bulkBtn.disabled = ids.length === 0;
      };
      checks.forEach(c => c.addEventListener('change', sync));
      sync();
    }

    // Auto-open modal if validation failed previously (create only)
    const modal = $('#customerModal');
    @if ($errors->any())
      modal.modal('show');
    @endif
  })();
</script>
@endpush
