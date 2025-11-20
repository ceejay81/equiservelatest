@extends('layouts.app')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/loan-components.css') }}">
  <link rel="stylesheet" href="{{ asset('css/loans.css') }}">
@endpush

@section('content')
<div class="container">
    <div class="card">
        <!-- Updated header styling to match design system -->
        <div class="card-header d-flex justify-content-between align-items-center" style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0;">
            <h5 class="mb-0" style="color: #0F172A; font-weight: 600;">Create New Loan</h5>
            <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Loans
            </a>
        </div>
        <div class="card-body">
            <form id="createLoanForm" method="POST" action="{{ route('loans.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Using reusable form-section component -->
                    <div class="col-md-6 mb-3">
                        <x-loans.form-section title="Sale Information" icon="shopping-cart">
                            <div class="form-group">
                                <label for="sale_id" class="form-label">Select Sale <span class="text-danger">*</span></label>
                                <select class="form-control @error('sale_id') is-invalid @enderror" 
                                        id="sale_id" 
                                        name="sale_id" 
                                        required>
                                    <option value="">Choose a sale...</option>
                                    @foreach($sales ?? [] as $sale)
                                        <option value="{{ $sale->id }}" 
                                                data-amount="{{ $sale->total_amount }}"
                                                {{ old('sale_id') == $sale->id ? 'selected' : '' }}>
                                            Sale #{{ $sale->id }} - ₱{{ number_format($sale->total_amount, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('sale_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Simplified sale details display -->
                            <div id="saleDetails" class="alert alert-light border" style="display: none; margin-top: 16px;">
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Customer:</small>
                                        <p class="mb-0 fw-bold" id="customerName">-</p>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Total Amount:</small>
                                        <p class="mb-0 fw-bold" id="saleAmount">₱0.00</p>
                                    </div>
                                </div>
                            </div>
                        </x-loans.form-section>
                    </div>

                    <div class="col-md-6 mb-3">
                        <x-loans.form-section title="Loan Terms" icon="percent">
                            <div class="form-group">
                                <label for="loan_amount" class="form-label">Loan Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" 
                                           class="form-control @error('loan_amount') is-invalid @enderror" 
                                           id="loan_amount" 
                                           name="loan_amount" 
                                           step="0.01" 
                                           min="0.01"
                                           value="{{ old('loan_amount') }}"
                                           required>
                                </div>
                                @error('loan_amount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="down_payment" class="form-label">Down Payment</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" 
                                           class="form-control @error('down_payment') is-invalid @enderror" 
                                           id="down_payment" 
                                           name="down_payment" 
                                           step="0.01" 
                                           min="0"
                                           value="{{ old('down_payment', 0) }}">
                                </div>
                                @error('down_payment')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="term_months" class="form-label">Term (Months) <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('term_months') is-invalid @enderror" 
                                       id="term_months" 
                                       name="term_months" 
                                       min="1"
                                       max="60"
                                       value="{{ old('term_months') }}"
                                       required>
                                @error('term_months')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Improved payment summary styling -->
                            <div class="alert alert-info mt-4">
                                <div class="row mb-0">
                                    <div class="col-6">
                                        <small>Monthly Payment (Est.):</small>
                                        <p class="mb-0 fw-bold" style="color: #3B82F6; font-size: 1.1rem;" id="monthlyPayment">₱0.00</p>
                                    </div>
                                    <div class="col-6">
                                        <small>Total Payable:</small>
                                        <p class="mb-0 fw-bold" style="color: #3B82F6; font-size: 1.1rem;" id="totalPayable">₱0.00</p>
                                    </div>
                                </div>
                            </div>
                        </x-loans.form-section>
                    </div>
                </div>

                <!-- ID Verification Section -->
                <div class="row mt-3">
                    <div class="col-12">
                        <x-loans.form-section title="ID Verification (Required)" icon="id-card">
                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Required:</strong> Please provide a valid government-issued ID for loan verification. Accepted IDs include Driver's License, Passport, National ID, SSS ID, PhilHealth ID, or Voter's ID.
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <label for="id_type" class="form-label">ID Type <span class="text-danger">*</span></label>
                                        <select class="form-control @error('id_type') is-invalid @enderror" 
                                                id="id_type" 
                                                name="id_type" 
                                                required>
                                            <option value="">Select ID Type...</option>
                                            <option value="Driver's License" {{ old('id_type') == "Driver's License" ? 'selected' : '' }}>Driver's License</option>
                                            <option value="Passport" {{ old('id_type') == 'Passport' ? 'selected' : '' }}>Passport</option>
                                            <option value="National ID" {{ old('id_type') == 'National ID' ? 'selected' : '' }}>National ID (PhilSys)</option>
                                            <option value="SSS ID" {{ old('id_type') == 'SSS ID' ? 'selected' : '' }}>SSS ID</option>
                                            <option value="PhilHealth ID" {{ old('id_type') == 'PhilHealth ID' ? 'selected' : '' }}>PhilHealth ID</option>
                                            <option value="Voter's ID" {{ old('id_type') == "Voter's ID" ? 'selected' : '' }}>Voter's ID</option>
                                            <option value="UMID" {{ old('id_type') == 'UMID' ? 'selected' : '' }}>UMID</option>
                                            <option value="Postal ID" {{ old('id_type') == 'Postal ID' ? 'selected' : '' }}>Postal ID</option>
                                            <option value="Other" {{ old('id_type') == 'Other' ? 'selected' : '' }}>Other Government ID</option>
                                        </select>
                                        @error('id_type')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <label for="id_number" class="form-label">ID Number <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('id_number') is-invalid @enderror" 
                                               id="id_number" 
                                               name="id_number" 
                                               placeholder="Enter ID number"
                                               value="{{ old('id_number') }}"
                                               required>
                                        <small class="text-muted">Enter the ID number exactly as shown on the document</small>
                                        @error('id_number')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="form-group">
                                        <label for="id_image" class="form-label">Upload ID Image <span class="text-danger">*</span></label>
                                        <input type="file" 
                                               class="form-control @error('id_image') is-invalid @enderror" 
                                               id="id_image" 
                                               name="id_image" 
                                               accept="image/jpeg,image/png,image/jpg,application/pdf"
                                               required>
                                        <small class="text-muted">JPG, PNG, or PDF (Max 5MB)</small>
                                        @error('id_image')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Simplified image preview -->
                            <div id="imagePreview" style="display: none; margin-top: 16px;">
                                <label class="form-label mb-2">Preview:</label>
                                <div class="border rounded p-3 bg-light text-center">
                                    <img id="previewImg" src="/placeholder.svg" alt="ID Preview" style="max-width: 100%; max-height: 300px; border-radius: 8px;">
                                </div>
                            </div>
                        </x-loans.form-section>
                    </div>
                </div>

                <div class="text-end mt-4 mb-3">
                    <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check mr-1"></i>Create Loan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const saleSelect = document.getElementById('sale_id');
    const loanAmount = document.getElementById('loan_amount');
    const downPayment = document.getElementById('down_payment');
    const termMonths = document.getElementById('term_months');
    const saleDetails = document.getElementById('saleDetails');
    const saleAmountEl = document.getElementById('saleAmount');
    const monthlyPaymentEl = document.getElementById('monthlyPayment');
    const totalPayableEl = document.getElementById('totalPayable');

    function updateSaleDetails() {
        const selectedOption = saleSelect.options[saleSelect.selectedIndex];
        if (selectedOption.value) {
            const saleAmount = parseFloat(selectedOption.dataset.amount);
            saleDetails.style.display = 'block';
            saleAmountEl.textContent = '₱' + saleAmount.toLocaleString(undefined, {minimumFractionDigits: 2});
            loanAmount.value = saleAmount;
            updateCalculations();
        } else {
            saleDetails.style.display = 'none';
        }
    }

    saleSelect.addEventListener('change', updateSaleDetails);
    [loanAmount, downPayment, termMonths].forEach(el => {
        el.addEventListener('input', updateCalculations);
    });

    function updateCalculations() {
        const principal = parseFloat(loanAmount.value || 0) - parseFloat(downPayment.value || 0);
        const months = parseInt(termMonths.value || 0);
        
        if (principal > 0 && months > 0) {
            const monthly = principal / months;
            monthlyPaymentEl.textContent = '₱' + monthly.toLocaleString(undefined, {minimumFractionDigits: 2});
            totalPayableEl.textContent = '₱' + principal.toLocaleString(undefined, {minimumFractionDigits: 2});
        } else {
            monthlyPaymentEl.textContent = '₱0.00';
            totalPayableEl.textContent = '₱0.00';
        }
    }

    const form = document.getElementById('createLoanForm');
    form.addEventListener('submit', function(e) {
        const principal = parseFloat(loanAmount.value || 0) - parseFloat(downPayment.value || 0);
        if (principal <= 0) {
            e.preventDefault();
            alert('The loan amount must be greater than the down payment.');
            return;
        }
        if (parseInt(termMonths.value || 0) <= 0) {
            e.preventDefault();
            alert('Please enter a valid loan term.');
            return;
        }
    });

    const idImageInput = document.getElementById('id_image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    idImageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                this.value = '';
                imagePreview.style.display = 'none';
                return;
            }

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else if (file.type === 'application/pdf') {
                imagePreview.style.display = 'block';
                imagePreview.querySelector('.text-center').innerHTML = `
                    <div>
                        <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                        <p class="mb-0">${file.name}</p>
                    </div>
                `;
            }
        } else {
            imagePreview.style.display = 'none';
        }
    });
});
</script>
@endpush
