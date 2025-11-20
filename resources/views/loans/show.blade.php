@extends('layouts.app')

@php($pageTitle = 'Account Details')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/loan-components.css') }}">
  <link rel="stylesheet" href="{{ asset('css/loans.css') }}">
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
    .empty-state {
      text-align: center;
      padding: 40px 20px;
    }
    .empty-state-icon {
      font-size: 3rem;
      color: #CBD5E1;
      margin-bottom: 16px;
    }
    .empty-state-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #0F172A;
      margin-bottom: 8px;
    }
    .empty-state-text {
      color: #64748B;
      margin: 0;
    }
  </style>
@endpush

@section('content')

@if (session('success'))
  <div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
@endif

<div class="card" style="border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.07);">
  <!-- Updated header styling with improved layout -->
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
    <!-- Status Badges -->
    <div class="mb-4">
      <x-loans.status-badge :status="$loan->status" :isOverdue="$loan->is_overdue" />
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

    <!-- Info Cards Row 1 -->
    <div class="row mb-4">
      <!-- Customer Card -->
      <div class="col-md-4">
        <x-loans.info-card icon="user" title="Customer" variant="customer">
          <x-loans.detail-row label="Name" :value="$loan->sale->customer->full_name ?? '-'" />
          <x-loans.detail-row label="Account #" :value="$loan->sale->customer->account_number ?? '-'" />
          <x-loans.detail-row label="Contact" :value="$loan->sale->customer->contact ?? '-'" />
          <div class="mt-3">
            <a href="/customers/{{ $loan->sale->customer->id ?? '#' }}" class="btn btn-sm btn-outline-primary btn-block">
              <i class="fas fa-eye mr-1"></i> View Customer
            </a>
          </div>
        </x-loans.info-card>
      </div>

      <!-- ID Verification Card -->
      <div class="col-md-4">
        <x-loans.info-card icon="id-card" title="ID Verification" variant="verification">
          @if($loan->id_type && $loan->id_number)
            <x-loans.detail-row label="ID Type" :value="$loan->id_type" />
            <x-loans.detail-row label="ID Number" :value="$loan->id_number" />
            <div class="detail-row">
              <span class="detail-label">Verified</span>
              <span class="detail-value">
                <span class="badge badge-success">
                  <i class="fas fa-check-circle"></i> Verified
                </span>
              </span>
            </div>
            @if($loan->id_verified_at)
              <x-loans.detail-row label="Verified On" :value="$loan->id_verified_at->format('M d, Y')" />
            @endif
            @if($loan->id_image_path)
              <div class="mt-3">
                <button type="button" class="btn btn-sm btn-outline-primary btn-block" onclick="showIDImage()">
                  <i class="fas fa-image mr-1"></i> View ID Image
                </button>
              </div>
            @endif
          @else
            <div class="text-center py-4">
              <i class="fas fa-exclamation-triangle text-warning fa-2x mb-2"></i>
              <p class="text-muted mb-0">No ID verification on file</p>
            </div>
          @endif
        </x-loans.info-card>
      </div>

      <!-- Loan Terms Card -->
      <div class="col-md-4">
        <x-loans.info-card icon="file-invoice-dollar" title="Loan Terms">
          <x-loans.detail-row label="Loan Amount" :value="'₱' . number_format($loan->loan_amount, 2)" />
          <x-loans.detail-row label="Down Payment" :value="'₱' . number_format($loan->down_payment, 2)" />
          <x-loans.detail-row label="Principal" :value="'₱' . number_format($loan->loan_amount - $loan->down_payment, 2)" />
          <x-loans.detail-row label="Interest Rate" :value="number_format($loan->interest_rate, 2) . '% p.a.'" />
          <x-loans.detail-row label="Term" :value="$loan->term_months . ' months'" />
        </x-loans.info-card>
      </div>
    </div>

    <!-- Info Cards Row 2 -->
    <div class="row mb-4">
      <div class="col-md-4">
        <x-loans.info-card icon="calendar-check" title="Payment Schedule" variant="payment">
          <x-loans.detail-row label="Monthly Amount" :value="'₱' . number_format($loan->monthly_amount, 2)" highlight="true" />
          <x-loans.detail-row label="Start Date" :value="$loan->start_date->format('M d, Y')" />
          @if($loan->next_due_date)
            <div class="detail-row">
              <span class="detail-label">Next Due</span>
              <span class="detail-value {{ $loan->is_overdue ? 'text-danger' : '' }}">
                {{ $loan->next_due_date->format('M d, Y') }}
              </span>
            </div>
          @endif
          <x-loans.detail-row label="End Date" :value="$loan->end_date ? $loan->end_date->format('M d, Y') : '-'" />
          <x-loans.detail-row label="Remaining Payments" :value="$loan->remaining_payments" />
        </x-loans.info-card>
      </div>
    </div>

    @if($loan->remarks)
      <div class="alert alert-info mb-4">
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
              <th class="text-right">Balance After</th>
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
              </td>
              <td>
                @if($loan->sale && $loan->sale->reference_number)
                  <code class="bg-light p-1 rounded" style="font-size: 0.85rem;">{{ $loan->sale->reference_number }}</code>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td class="text-right">₱{{ number_format($runningBalance, 2) }}</td>
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
                <td><span class="badge badge-success">{{ $payment->mode_of_payment }}</span></td>
                <td>
                  @if($payment->reference_number)
                    <code class="bg-light p-1 rounded" style="font-size: 0.85rem;">{{ $payment->reference_number }}</code>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td class="text-right">₱{{ number_format(max(0, $runningBalance), 2) }}</td>
              </tr>
            @empty
              @if($loan->down_payment == 0)
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">No payments recorded yet</td>
              </tr>
              @endif
            @endforelse
          </tbody>
          <tfoot style="background: #F8FAFC;">
            <tr>
              <th colspan="1">Total Paid</th>
              <th colspan="4" style="text-align: right;"><span style="color: #10B981; font-size: 1.1rem;">₱{{ number_format($loan->total_paid, 2) }}</span></th>
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
        <a href="{{ route('loans.amortization', $loan) }}" class="btn btn-primary btn-sm">
          <i class="fas fa-chart-line mr-1"></i> View Enhanced Schedule
        </a>
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
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <!-- Using reusable payment modal content component -->
          <x-loans.payment-modal-content :loan="$loan" />
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

<!-- Image Modals -->
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
        <img id="proofImage" src="/placeholder.svg" alt="Payment Proof" style="max-width: 100%; max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
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

<div class="modal fade" id="idImageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background: #3B82F6; color: white;">
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
          <img id="idImage" src="/placeholder.svg" alt="ID Document" style="max-width: 100%; max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
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

<script>
function showIDImage() {
  const imageUrl = '{{ $loan->id_image_path ? Storage::url($loan->id_image_path) : "" }}';
  document.getElementById('idImage').src = imageUrl;
  document.getElementById('idTypeDisplay').textContent = '{{ $loan->id_type ?? "" }}';
  document.getElementById('idNumberDisplay').textContent = '{{ $loan->id_number ?? "" }}';
  document.getElementById('idDownload').href = imageUrl;
  $('#idImageModal').modal('show');
}

function showProofImage(imageUrl, reference) {
  document.getElementById('proofImage').src = imageUrl;
  document.getElementById('proofReference').textContent = reference;
  document.getElementById('proofDownload').href = imageUrl;
  $('#proofImageModal').modal('show');
}
</script>

@endsection
