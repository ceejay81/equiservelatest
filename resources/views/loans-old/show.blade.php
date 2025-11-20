@extends('layouts.app')

@php($pageTitle = 'Account Details')

@section('content')

<style>
  .loan-header {
    background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
    color: white;
    padding: 24px;
    border-radius: 12px 12px 0 0;
    margin: -16px -16px 0 -16px;
  }
  .loan-number {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0;
  }
  .loan-meta {
    opacity: 0.95;
    font-size: 0.9rem;
    margin-top: 4px;
  }
  .info-card {
    background: #f8f9fa;
    border-left: 4px solid #3B82F6;
    padding: 20px;
    border-radius: 8px;
    height: 100%;
  }
  .info-card.customer {
    border-left-color: #10B981;
  }
  .info-card.payment {
    border-left-color: #F59E0B;
  }
  .info-card h6 {
    color: #475569;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 12px;
    font-weight: 600;
  }
  .detail-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #E2E8F0;
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
  .progress-card {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%);
    color: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
  }
  .progress-card h6 {
    font-size: 0.875rem;
    opacity: 0.9;
    margin-bottom: 8px;
  }
  .progress-card .amount {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 12px;
  }
  .payment-table thead th {
    background: #F1F5F9;
    color: #334155;
    font-weight: 600;
    font-size: 0.875rem;
    border: none;
    padding: 12px 16px;
  }
  .payment-table tbody td {
    padding: 12px 16px;
    vertical-align: middle;
    border-bottom: 1px solid #E2E8F0;
  }
  .amortization-table {
    font-size: 0.875rem;
  }
  .amortization-table thead th {
    background: #F1F5F9;
    color: #334155;
    font-weight: 600;
    font-size: 0.8rem;
    border: none;
    padding: 10px 12px;
  }
  .amortization-table tbody td {
    padding: 8px 12px;
    border-bottom: 1px solid #F1F5F9;
  }
</style>

@if (session('success'))
  <div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
@endif

<div class="card" style="border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.07);">
  <div class="loan-header">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <h1 class="loan-number">Account Receivable - {{ $loan->sale->sale_number ?? 'N/A' }}</h1>
        <div class="loan-meta">
          <i class="far fa-calendar mr-1"></i> Started {{ $loan->start_date->format('F d, Y') }}
          <span class="mx-2">•</span>
          <i class="far fa-user mr-1"></i> {{ $loan->sale->customer->full_name ?? 'Unknown' }}
        </div>
      </div>
      <div style="display: flex; gap: 8px;">
        <button class="btn btn-success" data-toggle="modal" data-target="#recordPaymentModal">
          <i class="fas fa-money-bill-wave mr-1"></i> Record Payment
        </button>
        <a href="{{ route('loans.index') }}" class="btn btn-outline-light">
          <i class="fas fa-arrow-left mr-1"></i> Back to A/R
        </a>
      </div>
    </div>
  </div>

  <div class="card-body" style="padding: 24px;">
    <!-- Status Badge -->
    <div class="mb-4">
      @php($status = $loan->status)
      <span class="badge badge-xl {{ $status==='active' ? 'badge-primary' : ($status==='completed' ? 'badge-success' : 'badge-danger') }}" style="padding: 8px 16px; font-size: 1rem;">
        <i class="fas {{ $status==='active' ? 'fa-clock' : ($status==='completed' ? 'fa-check-circle' : 'fa-exclamation-triangle') }} mr-1"></i>
        {{ ucfirst($status) }}
      </span>
      @if($loan->is_overdue)
        <span class="badge badge-xl badge-danger ml-2" style="padding: 8px 16px; font-size: 1rem;">
          <i class="fas fa-exclamation-triangle mr-1"></i> Overdue
        </span>
      @endif
    </div>

    <!-- Progress Card -->
    <div class="progress-card">
      <h6>REMAINING BALANCE</h6>
      <div class="amount">₱{{ number_format($loan->balance, 2) }}</div>
      <div class="progress" style="height: 8px; background: rgba(255,255,255,0.3);">
        <div class="progress-bar" style="width: {{ $loan->payment_progress }}%; background: white;"></div>
      </div>
      <div class="mt-2" style="font-size: 0.875rem; opacity: 0.9;">
        {{ number_format($loan->payment_progress, 1) }}% paid • ₱{{ number_format($loan->total_paid, 2) }} of ₱{{ number_format($loan->loan_amount, 2) }}
      </div>
    </div>

    <!-- Info Cards -->
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="info-card customer">
          <h6><i class="fas fa-user mr-1"></i> Customer</h6>
          <div class="detail-row">
            <span class="detail-label">Name</span>
            <span class="detail-value">{{ $loan->sale->customer->full_name ?? '-' }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Account #</span>
            <span class="detail-value">{{ $loan->sale->customer->account_number ?? '-' }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Contact</span>
            <span class="detail-value">{{ $loan->sale->customer->contact ?? '-' }}</span>
          </div>
          <div class="mt-3">
            <a href="/customers/{{ $loan->sale->customer->id ?? '#' }}" class="btn btn-sm btn-outline-primary btn-block">
              <i class="fas fa-eye mr-1"></i> View Customer
            </a>
          </div>
        </div>
      </div>

      <!-- ID Verification Card -->
      <div class="col-md-4">
        <div class="info-card" style="border-left-color: #8B5CF6;">
          <h6><i class="fas fa-id-card mr-1"></i> ID Verification</h6>
          @if($loan->id_type && $loan->id_number)
            <div class="detail-row">
              <span class="detail-label">ID Type</span>
              <span class="detail-value">{{ $loan->id_type }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">ID Number</span>
              <span class="detail-value">{{ $loan->id_number }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Verified</span>
              <span class="detail-value">
                <span class="badge badge-success">
                  <i class="fas fa-check-circle"></i> Verified
                </span>
              </span>
            </div>
            @if($loan->id_verified_at)
            <div class="detail-row">
              <span class="detail-label">Verified On</span>
              <span class="detail-value">{{ $loan->id_verified_at->format('M d, Y') }}</span>
            </div>
            @endif
            @if($loan->id_image_path)
            <div class="mt-3">
              <button type="button" class="btn btn-sm btn-outline-primary btn-block" onclick="showIDImage()">
                <i class="fas fa-image mr-1"></i> View ID Image
              </button>
            </div>
            @endif
          @else
            <div class="text-center py-3">
              <i class="fas fa-exclamation-triangle text-warning fa-2x mb-2"></i>
              <p class="text-muted mb-0">No ID verification on file</p>
            </div>
          @endif
        </div>
      </div>

      <div class="col-md-4">
        <div class="info-card">
          <h6><i class="fas fa-file-invoice-dollar mr-1"></i> Loan Terms</h6>
          <div class="detail-row">
            <span class="detail-label">Loan Amount</span>
            <span class="detail-value">₱{{ number_format($loan->loan_amount, 2) }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Down Payment</span>
            <span class="detail-value">₱{{ number_format($loan->down_payment, 2) }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Principal</span>
            <span class="detail-value">₱{{ number_format($loan->loan_amount - $loan->down_payment, 2) }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Interest Rate</span>
            <span class="detail-value">{{ number_format($loan->interest_rate, 2) }}% p.a.</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Term</span>
            <span class="detail-value">{{ $loan->term_months }} months</span>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="info-card payment">
          <h6><i class="fas fa-calendar-check mr-1"></i> Payment Schedule</h6>
          <div class="detail-row">
            <span class="detail-label">Monthly Amount</span>
            <span class="detail-value" style="color: #3B82F6; font-size: 1.1rem;">₱{{ number_format($loan->monthly_amount, 2) }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Start Date</span>
            <span class="detail-value">{{ $loan->start_date->format('M d, Y') }}</span>
          </div>
          @if($loan->next_due_date)
          <div class="detail-row">
            <span class="detail-label">Next Due</span>
            <span class="detail-value {{ $loan->is_overdue ? 'text-danger' : '' }}">
              {{ $loan->next_due_date->format('M d, Y') }}
              @if($loan->is_overdue)
                <br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Overdue</small>
              @endif
            </span>
          </div>
          @endif
          <div class="detail-row">
            <span class="detail-label">End Date</span>
            <span class="detail-value">{{ $loan->end_date ? $loan->end_date->format('M d, Y') : '-' }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Remaining Payments</span>
            <span class="detail-value">{{ $loan->remaining_payments }}</span>
          </div>
        </div>
      </div>
    </div>

    @if($loan->remarks)
    <div class="alert alert-info">
      <strong><i class="fas fa-info-circle mr-1"></i> Remarks:</strong> {{ $loan->remarks }}
    </div>
    @endif

    <!-- Payment History -->
    <div class="mb-4">
      <h5 style="font-weight: 600; color: #0F172A; margin-bottom: 16px;">
        <i class="fas fa-history mr-2" style="color: #3B82F6;"></i>Payment History
      </h5>
      <div class="table-responsive">
        <table class="table payment-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Amount Paid</th>
              <th>Mode</th>
              <th>Reference / Proof</th>
              <th>Balance After</th>
            </tr>
          </thead>
          <tbody>
            @php($runningBalance = $loan->loan_amount - $loan->down_payment)
            @if($loan->down_payment > 0)
            <tr>
              <td>
                <div>{{ $loan->start_date->format('M d, Y') }}</div>
                @if($loan->sale && $loan->sale->payment_timestamp)
                  <small class="text-muted">{{ \Carbon\Carbon::parse($loan->sale->payment_timestamp)->format('h:i A') }}</small>
                @endif
              </td>
              <td><strong style="color: #10B981;">₱{{ number_format($loan->down_payment, 2) }}</strong></td>
              <td>
                <span class="badge badge-info">Down Payment</span>
                @if($loan->sale && $loan->sale->payment_mode)
                  <br><small class="text-muted">{{ ucfirst($loan->sale->payment_mode) }}</small>
                @endif
                @if($loan->sale && $loan->sale->payment_bank)
                  <br><small class="text-muted">{{ $loan->sale->payment_bank }}</small>
                @endif
              </td>
              <td>
                @if($loan->sale && $loan->sale->reference_number)
                  <div>
                    <code class="bg-light p-1 rounded" style="font-size: 0.85rem;">{{ $loan->sale->reference_number }}</code>
                  </div>
                @endif
                @if($loan->sale && $loan->sale->proof_image_path)
                  <button type="button" class="btn btn-sm btn-outline-primary mt-1" 
                          onclick="showProofImage('{{ \Storage::url($loan->sale->proof_image_path) }}', '{{ $loan->sale->reference_number ?? 'N/A' }}')">
                    <i class="fas fa-image"></i> View Proof
                  </button>
                @endif
                @if(!$loan->sale || (!$loan->sale->reference_number && !$loan->sale->proof_image_path))
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td>₱{{ number_format($runningBalance, 2) }}</td>
            </tr>
            @endif
            @forelse($loan->payments as $payment)
              @php($runningBalance -= $payment->amount_paid)
              <tr>
                <td>
                  <div>{{ $payment->payment_date->format('M d, Y') }}</div>
                  @if($payment->payment_timestamp)
                    <small class="text-muted">{{ \Carbon\Carbon::parse($payment->payment_timestamp)->format('h:i A') }}</small>
                  @endif
                </td>
                <td><strong style="color: #10B981;">₱{{ number_format($payment->amount_paid, 2) }}</strong></td>
                <td>
                  <span class="badge badge-success">{{ $payment->mode_of_payment }}</span>
                  @if($payment->payment_bank)
                    <br><small class="text-muted">{{ $payment->payment_bank }}</small>
                  @endif
                </td>
                <td>
                  @if($payment->reference_number)
                    <div>
                      <code class="bg-light p-1 rounded" style="font-size: 0.85rem;">{{ $payment->reference_number }}</code>
                    </div>
                  @endif
                  @if($payment->proof_image_path)
                    <button type="button" class="btn btn-sm btn-outline-primary mt-1" 
                            onclick="showProofImage('{{ \Storage::url($payment->proof_image_path) }}', '{{ $payment->reference_number ?? 'N/A' }}')">
                      <i class="fas fa-image"></i> View Proof
                    </button>
                  @endif
                  @if(!$payment->reference_number && !$payment->proof_image_path)
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>₱{{ number_format(max(0, $runningBalance), 2) }}</td>
              </tr>
            @empty
              @if($loan->down_payment == 0)
              <tr><td colspan="5" class="text-center text-muted">No payments recorded yet</td></tr>
              @endif
            @endforelse
          </tbody>
          <tfoot style="background: #F8FAFC;">
            <tr>
              <th>Total Paid</th>
              <th colspan="4"><span style="color: #10B981; font-size: 1.1rem;">₱{{ number_format($loan->total_paid, 2) }}</span></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <!-- Amortization Schedule -->
    <div>
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 style="font-weight: 600; color: #0F172A; margin: 0;">
          <i class="fas fa-calculator mr-2" style="color: #3B82F6;"></i>Amortization Schedule
        </h5>
        <a href="{{ route('loans.amortization', $loan) }}" class="btn btn-primary">
          <i class="fas fa-chart-line mr-1"></i> View Enhanced Schedule
        </a>
      </div>
      <div class="alert alert-info">
        <i class="fas fa-info-circle mr-2"></i>
        <strong>New!</strong> View the enhanced amortization schedule with penalty calculations, rebate tracking, and payment scenarios.
        <a href="{{ route('loans.amortization', $loan) }}" class="alert-link">Click here to view →</a>
      </div>
      <div class="table-responsive">
        <table class="table amortization-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Due Date</th>
              <th class="text-right">Payment</th>
              <th class="text-right">Principal</th>
              <th class="text-right">Interest</th>
              <th class="text-right">Balance</th>
            </tr>
          </thead>
          <tbody>
            @foreach($loan->getAmortizationSchedule() as $schedule)
            <tr>
              <td>{{ $schedule['month'] }}</td>
              <td>{{ $schedule['due_date']->format('M d, Y') }}</td>
              <td class="text-right">₱{{ number_format($schedule['payment'], 2) }}</td>
              <td class="text-right">₱{{ number_format($schedule['principal'], 2) }}</td>
              <td class="text-right">₱{{ number_format($schedule['interest'], 2) }}</td>
              <td class="text-right">₱{{ number_format($schedule['balance'], 2) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Record Payment Modal -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form method="POST" action="{{ route('loans.payments.store', $loan) }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-money-bill-wave mr-2"></i>Record Payment</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">
            <strong>Remaining Balance:</strong> ₱{{ number_format($loan->balance, 2) }}<br>
            <strong>Suggested Payment:</strong> ₱{{ number_format($loan->next_payment_amount, 2) }}
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Payment Date <span class="text-danger">*</span></label>
                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label>Amount Paid <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">₱</span>
                  </div>
                  <input type="number" step="0.01" name="amount_paid" class="form-control" value="{{ $loan->next_payment_amount }}" min="0" max="{{ $loan->balance }}" required>
                </div>
                <small class="text-muted">Maximum: ₱{{ number_format($loan->balance, 2) }}</small>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label>Mode of Payment <span class="text-danger">*</span></label>
            <select name="mode_of_payment" id="paymentMode" class="form-control" required>
              <option value="Cash">Cash</option>
              <option value="Bank Transfer">Bank Transfer</option>
              <option value="GCash">GCash</option>
              <option value="PayMaya">PayMaya</option>
              <option value="Check">Check</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <!-- Online Payment Fields (shown when Bank Transfer, GCash, or PayMaya selected) -->
          <div id="onlinePaymentFields" style="display: none;">
            <div class="alert alert-warning">
              <i class="fas fa-info-circle mr-1"></i>
              <strong>Online Payment:</strong> Reference number and proof of payment are required.
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Bank/Platform</label>
                  <input type="text" name="payment_bank" class="form-control" placeholder="e.g., BPI, BDO, GCash">
                  <small class="text-muted">Optional: Specify which bank or platform</small>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label>Payment Timestamp</label>
                  <input type="datetime-local" name="payment_timestamp" class="form-control">
                  <small class="text-muted">Optional: When customer actually paid</small>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label>Reference Number <span class="text-danger" id="refRequired">*</span></label>
              <input type="text" name="reference_number" id="referenceNumber" class="form-control" placeholder="e.g., BPI-123456789">
              <small class="text-muted">Format: 6-20 characters (letters, numbers, dashes)</small>
            </div>
            
            <div class="form-group">
              <label>Proof of Payment <span class="text-danger" id="proofRequired">*</span></label>
              <input type="file" name="proof_image" id="proofImage" class="form-control-file" accept="image/*">
              <small class="text-muted">Upload screenshot or photo (JPG, PNG, GIF, max 2MB)</small>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-save mr-1"></i> Save Payment
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const paymentMode = document.getElementById('paymentMode');
  const onlineFields = document.getElementById('onlinePaymentFields');
  const referenceNumber = document.getElementById('referenceNumber');
  const proofImage = document.getElementById('proofImage');
  
  paymentMode.addEventListener('change', function() {
    const isOnline = ['Bank Transfer', 'GCash', 'PayMaya'].includes(this.value);
    onlineFields.style.display = isOnline ? 'block' : 'none';
    
    // Toggle required attribute
    if (isOnline) {
      referenceNumber.setAttribute('required', 'required');
      proofImage.setAttribute('required', 'required');
    } else {
      referenceNumber.removeAttribute('required');
      proofImage.removeAttribute('required');
    }
  });
});

// Show proof image modal
function showProofImage(imageUrl, reference) {
  console.log('Loading image:', imageUrl);
  const imgElement = document.getElementById('proofImage');
  imgElement.onerror = function() {
    console.error('Failed to load image:', imageUrl);
    imgElement.alt = 'Failed to load image. URL: ' + imageUrl;
  };
  imgElement.onload = function() {
    console.log('Image loaded successfully');
  };
  imgElement.src = imageUrl;
  document.getElementById('proofReference').textContent = reference;
  document.getElementById('proofDownload').href = imageUrl;
  $('#proofImageModal').modal('show');
}

// Show ID image modal
function showIDImage() {
  const imageUrl = '{{ $loan->id_image_path ? Storage::url($loan->id_image_path) : "" }}';
  const idType = '{{ $loan->id_type ?? "" }}';
  const idNumber = '{{ $loan->id_number ?? "" }}';
  
  const imgElement = document.getElementById('idImage');
  imgElement.src = imageUrl;
  document.getElementById('idTypeDisplay').textContent = idType;
  document.getElementById('idNumberDisplay').textContent = idNumber;
  document.getElementById('idDownload').href = imageUrl;
  $('#idImageModal').modal('show');
}
</script>

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

<!-- ID Image Modal -->
<div class="modal fade" id="idImageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
          <i class="fas fa-id-card mr-2"></i>
          ID Verification Document
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body" style="background: #F8FAFC;">
        <div class="row mb-3">
          <div class="col-6">
            <strong>ID Type:</strong> <span id="idTypeDisplay" class="text-muted"></span>
          </div>
          <div class="col-6">
            <strong>ID Number:</strong> <span id="idNumberDisplay" class="text-muted"></span>
          </div>
        </div>
        <div class="text-center">
          <img id="idImage" src="" alt="ID Document" style="max-width: 100%; max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
        </div>
      </div>
      <div class="modal-footer">
        <a id="idDownload" href="" download class="btn btn-outline-primary">
          <i class="fas fa-download mr-1"></i>Download
        </a>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@endsection
