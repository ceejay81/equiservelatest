@extends('layouts.app')

@php($pageTitle = 'Sale ' . $sale->sale_number)

@section('content')

<style>
  .sale-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 24px;
    border-radius: 12px 12px 0 0;
    margin: -16px -16px 0 -16px;
  }
  .sale-number {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0;
  }
  .sale-meta {
    opacity: 0.95;
    font-size: 0.9rem;
    margin-top: 4px;
  }
  .info-card {
    background: #f8f9fa;
    border-left: 4px solid #3B82F6;
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 16px;
  }
  .info-card.customer {
    border-left-color: #10B981;
  }
  .info-card h6 {
    color: #475569;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 8px;
    font-weight: 600;
  }
  .info-card .value {
    color: #0F172A;
    font-size: 1rem;
    font-weight: 500;
    margin-bottom: 4px;
  }
  .info-card .label {
    color: #64748B;
    font-size: 0.875rem;
  }
  .items-table {
    margin-top: 24px;
  }
  .items-table thead th {
    background: #F1F5F9;
    color: #334155;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border: none;
    padding: 12px 16px;
  }
  .items-table tbody td {
    padding: 14px 16px;
    vertical-align: middle;
    border-bottom: 1px solid #E2E8F0;
  }
  .items-table tbody tr:hover {
    background: #F8FAFC;
  }
  .items-table tfoot {
    background: #F8FAFC;
  }
  .items-table tfoot th {
    padding: 12px 16px;
    font-weight: 600;
    border-top: 2px solid #CBD5E1;
  }
  .total-row th {
    font-size: 1.125rem;
    color: #0F172A;
  }
  .payment-card, .loan-card {
    background: white;
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    padding: 20px;
    height: 100%;
  }
  .payment-card h5, .loan-card h5 {
    font-size: 1rem;
    font-weight: 600;
    color: #0F172A;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .payment-card h5 i, .loan-card h5 i {
    color: #3B82F6;
  }
  .detail-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #F1F5F9;
  }
  .detail-row:last-child {
    border-bottom: none;
  }
  .detail-label {
    color: #64748B;
    font-size: 0.875rem;
  }
  .detail-value {
    color: #0F172A;
    font-weight: 500;
  }
  .action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }
  .badge-xl {
    padding: 6px 12px;
    font-size: 0.875rem;
    font-weight: 600;
  }
  .product-name {
    font-weight: 500;
    color: #0F172A;
  }
  .product-sku {
    font-size: 0.8rem;
    color: #64748B;
  }
</style>

@if (session('success'))
  <div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
@endif
@if (session('warning'))
  <div class="alert alert-warning alert-dismissible fade show">
    <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('warning') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
@endif

<div class="card" style="border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.07);">
  <div class="sale-header">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <h1 class="sale-number">{{ $sale->sale_number }}</h1>
        <div class="sale-meta">
          <i class="far fa-calendar mr-1"></i> {{ $sale->created_at->format('F d, Y') }} at {{ $sale->created_at->format('g:i A') }}
          <span class="mx-2">•</span>
          <i class="far fa-user mr-1"></i> {{ $sale->user->name ?? 'Unknown' }}
        </div>
      </div>
      <div class="action-buttons">
        <a href="{{ route('sales.print', $sale) }}" class="btn btn-light">
          <i class="fas fa-file-pdf mr-1"></i> Download PDF
        </a>
        <a href="{{ route('sales.index') }}" class="btn btn-outline-light">
          <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
      </div>
    </div>
  </div>

  <div class="card-body" style="padding: 24px;">
    <!-- Sale Type & Status -->
    <div class="mb-4">
      <span class="badge badge-xl {{ $sale->sale_type === 'cash' ? 'badge-success' : 'badge-info' }}">
        <i class="fas {{ $sale->sale_type === 'cash' ? 'fa-money-bill-wave' : 'fa-credit-card' }} mr-1"></i>
        {{ ucfirst($sale->sale_type) }} Sale
      </span>
      @if($sale->discount_total && $sale->discount_reason)
        <span class="badge badge-xl badge-warning ml-2">
          <i class="fas fa-tag mr-1"></i> {{ $sale->discount_reason }}
        </span>
      @endif
    </div>

    <!-- Customer & Sale Info -->
    <div class="row mb-4">
      <div class="col-md-6">
        <div class="info-card">
          <h6><i class="fas fa-receipt mr-1"></i> Sale Information</h6>
          <div class="value">{{ $sale->sale_number }}</div>
          <div class="label">Sale Number</div>
          <div class="value mt-2">{{ $sale->created_at->format('M d, Y H:i') }}</div>
          <div class="label">Transaction Date</div>
          <div class="value mt-2">{{ $sale->user->name ?? '-' }}</div>
          <div class="label">Processed By</div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="info-card customer">
          <h6><i class="fas fa-user mr-1"></i> Customer Details</h6>
          <div class="value">{{ $sale->customer->full_name ?? '-' }}</div>
          <div class="label">Full Name</div>
          <div class="value mt-2">{{ $sale->customer->account_number ?? '-' }}</div>
          <div class="label">Account Number</div>
          <div class="value mt-2">{{ $sale->customer->contact ?? '-' }}</div>
          <div class="label">Contact Number</div>
        </div>
      </div>
    </div>

    <!-- Items Table -->
    <div class="items-table">
      <h5 class="mb-3" style="font-weight: 600; color: #0F172A;">
        <i class="fas fa-shopping-cart mr-2" style="color: #3B82F6;"></i>Items Purchased
      </h5>
      <div class="table-responsive">
        <table class="table items-table mb-0">
          <thead>
            <tr>
              <th style="width: 50%;">Product</th>
              <th class="text-center" style="width: 15%;">Quantity</th>
              <th class="text-right" style="width: 17.5%;">Unit Price</th>
              <th class="text-right" style="width: 17.5%;">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($sale->items as $item)
              <tr>
                <td>
                  <div class="product-name">{{ $item->product->name ?? 'Product #'.$item->product_id }}</div>
                  @if($item->product && $item->product->sku)
                    <div class="product-sku">SKU: {{ $item->product->sku }}</div>
                  @endif
                </td>
                <td class="text-center">
                  <span class="badge badge-secondary">{{ $item->quantity }}</span>
                </td>
                <td class="text-right">₱ {{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right" style="font-weight: 500;">₱ {{ number_format($item->subtotal, 2) }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            @if ($sale->discount_total)
            <tr>
              <th colspan="3" class="text-right" style="color: #EF4444;">Discount</th>
              <th class="text-right" style="color: #EF4444;">- ₱ {{ number_format($sale->discount_total, 2) }}</th>
            </tr>
            @endif
            <tr class="total-row">
              <th colspan="3" class="text-right">Total Amount</th>
              <th class="text-right" style="color: #10B981;">₱ {{ number_format($sale->total_amount, 2) }}</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <!-- Payment & Loan Info -->
    <div class="row mt-4">
      <div class="col-md-6">
        <div class="payment-card">
          <h5><i class="fas fa-credit-card"></i> Payment Details</h5>
          
          <div class="detail-row">
            <span class="detail-label">Payment Mode</span>
            <span class="detail-value">
              @if ($sale->payment_mode)
                @php($pm = $sale->payment_mode)
                <span class="badge {{ $pm==='cash' ? 'badge-success' : 'badge-warning' }}">
                  <i class="fas {{ $pm==='cash' ? 'fa-money-bill-wave' : 'fa-globe' }} mr-1"></i>
                  {{ ucfirst($pm) }}
                </span>
              @else
                <span class="text-muted">-</span>
              @endif
            </span>
          </div>

          @if ($sale->payment_mode==='cash')
            <div class="detail-row">
              <span class="detail-label">Amount Tendered</span>
              <span class="detail-value">₱ {{ $sale->amount_tendered ? number_format($sale->amount_tendered, 2) : '-' }}</span>
            </div>
            @if ($sale->amount_tendered)
              <div class="detail-row">
                <span class="detail-label">Change</span>
                <span class="detail-value" style="color: #10B981;">₱ {{ number_format(max(0, $sale->amount_tendered - $sale->total_amount), 2) }}</span>
              </div>
            @endif
          @elseif ($sale->payment_mode==='online')
            @if($sale->payment_bank)
            <div class="detail-row">
              <span class="detail-label">Bank/Platform</span>
              <span class="detail-value"><span class="badge badge-info">{{ $sale->payment_bank }}</span></span>
            </div>
            @endif
            @if($sale->payment_timestamp)
            <div class="detail-row">
              <span class="detail-label">Payment Time</span>
              <span class="detail-value">{{ \Carbon\Carbon::parse($sale->payment_timestamp)->format('M d, Y h:i A') }}</span>
            </div>
            @endif
            <div class="detail-row">
              <span class="detail-label">Reference Number</span>
              <span class="detail-value">
                <code class="bg-light p-1 rounded">{{ $sale->reference_number ?? '-' }}</code>
              </span>
            </div>
            @if ($sale->proof_image_path)
              <div class="detail-row">
                <span class="detail-label">Proof of Payment</span>
                <span class="detail-value">
                  <button type="button" class="btn btn-sm btn-outline-primary" 
                          onclick="showProofImage('{{ Storage::url($sale->proof_image_path) }}', '{{ $sale->reference_number ?? 'N/A' }}')">
                    <i class="fas fa-image mr-1"></i> View Proof
                  </button>
                </span>
              </div>
            @endif
          @endif
        </div>
      </div>

      @if ($sale->sale_type==='loan' && $sale->loan)
      <div class="col-md-6">
        <div class="loan-card">
          <h5><i class="fas fa-file-invoice-dollar"></i> Loan Information</h5>
          
          <div class="detail-row">
            <span class="detail-label">Status</span>
            <span class="detail-value">
              @php($ls = $sale->loan->status)
              <span class="badge {{ $ls==='active' ? 'badge-success' : ($ls==='completed' ? 'badge-primary' : ($ls==='overdue' ? 'badge-danger' : 'badge-secondary')) }}">
                {{ ucfirst($ls) }}
              </span>
            </span>
          </div>

          <div class="detail-row">
            <span class="detail-label">Loan Amount</span>
            <span class="detail-value">₱ {{ number_format($sale->loan->loan_amount, 2) }}</span>
          </div>

          <div class="detail-row">
            <span class="detail-label">Down Payment</span>
            <span class="detail-value">₱ {{ number_format($sale->loan->down_payment, 2) }}</span>
          </div>

          <div class="detail-row">
            <span class="detail-label">Monthly Payment</span>
            <span class="detail-value" style="color: #3B82F6;">₱ {{ number_format($sale->loan->monthly_amount, 2) }}</span>
          </div>

          <div class="detail-row">
            <span class="detail-label">Remaining Balance</span>
            <span class="detail-value" style="color: #EF4444; font-weight: 600;">₱ {{ number_format($sale->loan->balance, 2) }}</span>
          </div>

          @if($sale->loan->next_due_date)
          <div class="detail-row">
            <span class="detail-label">Next Due Date</span>
            <span class="detail-value">{{ $sale->loan->next_due_date->format('M d, Y') }}</span>
          </div>
          @endif

          <div class="mt-3">
            <a href="/loans/{{ $sale->loan->id }}" class="btn btn-sm btn-primary btn-block">
              <i class="fas fa-eye mr-1"></i> View Loan Details
            </a>
          </div>
        </div>
      </div>
      @endif
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

@endsection
