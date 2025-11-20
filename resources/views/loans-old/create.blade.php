@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Create New Loan</h5>
            <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back to Loans
            </a>
        </div>
        <div class="card-body">
            <form id="createLoanForm" method="POST" action="{{ route('loans.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Sale Information -->
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Sale Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="sale_id" class="form-label required">Select Sale</label>
                                    <select class="form-select @error('sale_id') is-invalid @enderror" 
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
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div id="saleDetails" class="border rounded p-3 bg-light" style="display: none;">
                                    <h6>Sale Details</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <p class="mb-1"><small>Customer:</small></p>
                                            <p class="fw-bold" id="customerName">-</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="mb-1"><small>Total Amount:</small></p>
                                            <p class="fw-bold" id="saleAmount">₱0.00</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loan Terms -->
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Loan Terms</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="loan_amount" class="form-label required">Loan Amount</label>
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
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
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
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="term_months" class="form-label required">Term (Months)</label>
                                    <input type="number" 
                                           class="form-control @error('term_months') is-invalid @enderror" 
                                           id="term_months" 
                                           name="term_months" 
                                           min="1"
                                           max="60"
                                           value="{{ old('term_months') }}"
                                           required>
                                    @error('term_months')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Payment Summary -->
                                <div class="card bg-light mt-4">
                                    <div class="card-body">
                                        <h6>Payment Summary</h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <p class="mb-1"><small>Monthly Payment (Est.):</small></p>
                                                <p class="fw-bold" id="monthlyPayment">₱0.00</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-1"><small>Total Payable:</small></p>
                                                <p class="fw-bold" id="totalPayable">₱0.00</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ID Verification Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-id-card mr-2"></i>
                                    ID Verification (Required)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Required:</strong> Please provide a valid government-issued ID for loan verification. Accepted IDs include Driver's License, Passport, National ID, SSS ID, PhilHealth ID, or Voter's ID.
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="id_type" class="form-label required">ID Type</label>
                                        <select class="form-select @error('id_type') is-invalid @enderror" 
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
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="id_number" class="form-label required">ID Number</label>
                                        <input type="text" 
                                               class="form-control @error('id_number') is-invalid @enderror" 
                                               id="id_number" 
                                               name="id_number" 
                                               placeholder="Enter ID number"
                                               value="{{ old('id_number') }}"
                                               required>
                                        <small class="form-text text-muted">Enter the ID number exactly as shown on the document</small>
                                        @error('id_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="id_image" class="form-label required">Upload ID Image</label>
                                        <input type="file" 
                                               class="form-control @error('id_image') is-invalid @enderror" 
                                               id="id_image" 
                                               name="id_image" 
                                               accept="image/jpeg,image/png,image/jpg,application/pdf"
                                               required>
                                        <small class="form-text text-muted">JPG, PNG, or PDF (Max 5MB)</small>
                                        @error('id_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Image Preview -->
                                <div id="imagePreview" class="mt-3" style="display: none;">
                                    <label class="form-label">Preview:</label>
                                    <div class="border rounded p-2 bg-light">
                                        <img id="previewImg" src="" alt="ID Preview" style="max-width: 300px; max-height: 200px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-3">
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

    // Update sale details when sale is selected
    saleSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const saleAmount = parseFloat(selectedOption.dataset.amount);
            saleDetails.style.display = 'block';
            saleAmountEl.textContent = '₱' + saleAmount.toLocaleString(undefined, {minimumFractionDigits: 2});
            loanAmount.value = saleAmount;
            updateCalculations();
        } else {
            saleDetails.style.display = 'none';
        }
    });

    // Update calculations when loan terms change
    [loanAmount, downPayment, termMonths].forEach(el => {
        el.addEventListener('input', updateCalculations);
    });

    function updateCalculations() {
        const principal = parseFloat(loanAmount.value || 0) - parseFloat(downPayment.value || 0);
        const months = parseInt(termMonths.value || 0);
        
        if (principal > 0 && months > 0) {
            // Simple calculation (no interest for now)
            const monthly = principal / months;
            const total = principal;
            
            monthlyPaymentEl.textContent = '₱' + monthly.toLocaleString(undefined, {minimumFractionDigits: 2});
            totalPayableEl.textContent = '₱' + total.toLocaleString(undefined, {minimumFractionDigits: 2});
        } else {
            monthlyPaymentEl.textContent = '₱0.00';
            totalPayableEl.textContent = '₱0.00';
        }
    }

    // Form validation
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

    // Image preview functionality
    const idImageInput = document.getElementById('id_image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    idImageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Check file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                this.value = '';
                imagePreview.style.display = 'none';
                return;
            }

            // Show preview for images only
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else if (file.type === 'application/pdf') {
                // For PDF, just show filename
                imagePreview.style.display = 'block';
                previewImg.style.display = 'none';
                imagePreview.querySelector('.border').innerHTML = `
                    <div class="text-center p-3">
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

<style>
.required:after {
    content: ' *';
    color: red;
}
</style>
@endsection