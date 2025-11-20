@extends('layouts.app')

@php($pageTitle = 'Create Sale')

@section('content')

<link rel="stylesheet" href="{{ asset('css/sales-create.css') }}">

<a href="{{ route('sales.index') }}" class="btn btn-link px-0 mb-3" style="color: #3B82F6; font-weight: 500;">
  <i class="fas fa-arrow-left mr-2"></i> Back to Sales
</a>

@if ($errors->any())
  <div class="alert-custom danger">
    <div style="display: flex; align-items: start; gap: 12px;">
      <i class="fas fa-exclamation-circle" style="font-size: 1.25rem; margin-top: 2px;"></i>
      <div style="flex: 1;">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2" style="padding-left: 20px;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
@endif

@if (session('warning'))
  <div class="alert-custom warning">
    <div style="display: flex; align-items: center; gap: 12px;">
      <i class="fas fa-exclamation-triangle" style="font-size: 1.25rem;"></i>
      <div>{{ session('warning') }}</div>
    </div>
  </div>
@endif

<div class="card" style="border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.07);">
  <div class="create-sale-header">
    <h1><i class="fas fa-plus-circle mr-2"></i> Create New Sale</h1>
    <p>Fill in the details below to create a new sale transaction</p>
  </div>

  <form method="POST" action="{{ route('sales.store') }}" enctype="multipart/form-data" style="padding: 24px;">
    @csrf
    <!-- Section 1: Customer & Sale Type -->
    <div class="section-card">
      <div class="section-header">
        <div class="section-icon blue">
          <i class="fas fa-user"></i>
        </div>
        <div>
          <h2 class="section-title">Customer & Sale Information</h2>
          <p class="section-subtitle">Select customer and sale type</p>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">
            Customer <span style="color: #EF4444;">*</span>
          </label>
          
          <select name="customer_id" id="customer_id" class="form-control" required>
            <option value="">Select customer...</option>
            @foreach ($customers as $customer)
              <option value="{{ $customer->id }}" {{ old('customer_id')==$customer->id?'selected':'' }}>
                {{ $customer->full_name }} ({{ $customer->account_number }})
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Sale Type <span style="color: #EF4444;">*</span></label>
          <select name="sale_type" id="sale_type" class="form-control" required>
            <option value="cash" {{ old('sale_type')=='cash'?'selected':'' }}>Cash Sale</option>
            <option value="loan" {{ old('sale_type')=='loan'?'selected':'' }}>Loan / Installment</option>
          </select>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Payment Mode <span style="color: #EF4444;">*</span></label>
          <select name="payment_mode" id="payment_mode" class="form-control" required>
            @if(setting('payment.cash_enabled', true))
              <option value="cash" {{ old('payment_mode')=='cash'?'selected':'' }}>Cash</option>
            @endif
            @if(setting('payment.online_enabled', false))
              <option value="online" {{ old('payment_mode')=='online'?'selected':'' }}>Online Banking</option>
            @endif
          </select>
        </div>
      </div>

      <!-- Online Payment Fields -->
      <div id="online_fields" style="display:none;">
        <div class="conditional-section">
          <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <i class="fas fa-globe" style="color: #F59E0B;"></i>
            <strong style="color: #92400E;">Online Banking Details</strong>
          </div>
          <div class="alert alert-warning" style="font-size: 0.9rem; padding: 10px;">
            <i class="fas fa-info-circle mr-1"></i>
            <strong>Required:</strong> Reference number and proof of payment are mandatory for online payments.
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Bank/Platform</label>
              <input type="text" name="payment_bank" class="form-control" value="{{ old('payment_bank') }}" placeholder="e.g., BPI, BDO, GCash">
              <small class="text-muted">Optional: Which bank or platform was used</small>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Payment Timestamp</label>
              <input type="datetime-local" name="payment_timestamp" class="form-control" value="{{ old('payment_timestamp') }}">
              <small class="text-muted">Optional: When customer actually paid</small>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Reference Number <span style="color: #EF4444;">*</span></label>
              <input type="text" name="reference_number" id="referenceNumber" class="form-control" value="{{ old('reference_number') }}" placeholder="e.g., BPI-123456789">
              <small class="text-muted">Format: 6-20 characters (e.g., BPI-123456789)</small>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Proof of Payment <span style="color: #EF4444;">*</span></label>
              <input type="file" name="proof_image" id="proofImage" accept="image/*" class="form-control-file">
              <small class="text-muted">Required: Upload screenshot (JPG, PNG, GIF, max 2MB)</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Loan Fields -->
      <div id="loan_fields" style="display:none;">
        <div class="conditional-section loan">
          <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <i class="fas fa-file-invoice-dollar" style="color: #3B82F6;"></i>
            <strong style="color: #1E40AF;">Loan Terms</strong>
          </div>
          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Down Payment <small class="text-muted">(Min: {{ setting('loan.min_down_payment_percent', 20) }}%)</small></label>
              <input type="number" step="0.01" name="down_payment" class="form-control" value="{{ old('down_payment', 0) }}" placeholder="0.00">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Term (months) <small class="text-muted">(Max: {{ setting('loan.max_term_months', 36) }})</small></label>
              <input type="number" name="term_months" class="form-control" value="{{ old('term_months', 12) }}" placeholder="12" max="{{ setting('loan.max_term_months', 36) }}">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Interest Rate (%) <small class="text-muted">(Default: {{ setting('loan.default_interest_rate', 2) }}%)</small></label>
              <input type="number" step="0.01" name="interest_rate" class="form-control" value="{{ old('interest_rate', setting('loan.default_interest_rate', 2)) }}" placeholder="{{ setting('loan.default_interest_rate', 2) }}">
            </div>
          </div>

          <!-- ID Verification for Loans -->
          <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #E5E7EB;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
              <i class="fas fa-id-card" style="color: #8B5CF6;"></i>
              <strong style="color: #6D28D9;">ID Verification (Required)</strong>
            </div>
            <div class="alert alert-info" style="font-size: 0.9rem; padding: 10px;">
              <i class="fas fa-info-circle mr-1"></i>
              <strong>Required:</strong> Please provide a valid government-issued ID for loan verification. Accepted IDs include Driver's License, Passport, National ID, SSS ID, PhilHealth ID, or Voter's ID.
            </div>
            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label">ID Type <span style="color: #EF4444;">*</span></label>
                <select name="id_type" id="id_type" class="form-control">
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
                <small class="text-muted">Choose the type of ID being presented</small>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">ID Number <span style="color: #EF4444;">*</span></label>
                <input type="text" name="id_number" id="id_number" class="form-control" value="{{ old('id_number') }}" placeholder="Enter ID number">
                <small class="text-muted">Enter exactly as shown on the ID</small>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Upload ID Image <span style="color: #EF4444;">*</span></label>
                <input type="file" name="id_image" id="id_image" accept="image/jpeg,image/png,image/jpg,application/pdf" class="form-control-file">
                <small class="text-muted">JPG, PNG, or PDF (Max 5MB)</small>
              </div>
            </div>
            <!-- Image Preview -->
            <div id="id_image_preview" style="display: none; margin-top: 12px;">
              <label class="form-label">Preview:</label>
              <div style="border: 2px dashed #CBD5E1; border-radius: 8px; padding: 16px; background: #F8FAFC; text-align: center;">
                <img id="id_preview_img" src="" alt="ID Preview" style="max-width: 300px; max-height: 200px; border-radius: 4px;">
                <div id="id_preview_pdf" style="display: none;">
                  <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                  <p class="mb-0" id="id_pdf_name"></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Section 2: Items -->
    <div class="section-card">
      <div class="section-header">
        <div class="section-icon green">
          <i class="fas fa-shopping-cart"></i>
        </div>
        <div>
          <h2 class="section-title">Sale Items</h2>
          <p class="section-subtitle">Add products to this sale</p>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table items-table" id="items_table">
          <thead>
            <tr>
              <th style="width:45%">Product</th>
              <th style="width:12%" class="text-center">Quantity</th>
              <th style="width:18%" class="text-right">Unit Price</th>
              <th style="width:18%" class="text-right">Subtotal</th>
              <th style="width:7%" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody></tbody>
          <tfoot>
            <tr>
              <td colspan="5" style="text-align: center;">
                <button type="button" class="btn-add-item" onclick="addRow()">
                  <i class="fas fa-plus mr-2"></i> Add Item
                </button>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <!-- Section 3: Summary & Discount -->
    <div class="row">
      <div class="col-md-7 mb-3">
        <div class="section-card">
          <div class="section-header">
            <div class="section-icon orange">
              <i class="fas fa-tag"></i>
            </div>
            <div>
              <h2 class="section-title">Discount (Optional)</h2>
              <p class="section-subtitle">Apply promotional discounts</p>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Discount Amount</label>
              <input type="number" step="0.01" name="discount_total" id="discount_total" class="form-control" value="{{ old('discount_total', 0) }}" placeholder="0.00">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Discount Reason</label>
              <input type="text" name="discount_reason" class="form-control" value="{{ old('discount_reason') }}" placeholder="e.g., Anniversary Sale">
            </div>
          </div>
          <small class="text-muted">
            <i class="fas fa-info-circle mr-1"></i>
            For promotions like anniversary sales, bulk purchases, or loyalty rewards
          </small>
        </div>
      </div>

      <div class="col-md-5 mb-3">
        <div class="summary-box">
          <div class="summary-row">
            <span class="summary-label">Subtotal</span>
            <span class="summary-value">₱<span id="subtotal_display">0.00</span></span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Discount</span>
            <span class="summary-value" style="color: #EF4444;">-₱<span id="discount_display">0.00</span></span>
          </div>
          <div class="summary-total">
            <div class="summary-row">
              <span class="summary-label">Total Amount</span>
              <span class="summary-value">₱<span id="grand_total">0.00</span></span>
            </div>
          </div>
        </div>

        <!-- Cash Payment Fields - Moved here for better UX -->
        <div id="cash_fields" style="display:none; margin-top: 16px;">
          <div style="background: #FFFBEB; border: 2px solid #F59E0B; border-radius: 12px; padding: 20px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
              <i class="fas fa-money-bill-wave" style="color: #F59E0B; font-size: 1.25rem;"></i>
              <strong style="color: #92400E; font-size: 1.1rem;">Cash Payment</strong>
            </div>
            
            <div class="mb-3">
              <label class="form-label" style="font-weight: 600;">Amount Tendered <span style="color: #EF4444;">*</span></label>
              <input type="number" step="0.01" name="amount_tendered" id="amount_tendered" class="form-control" value="{{ old('amount_tendered') }}" placeholder="0.00" min="0" style="font-size: 1.1rem; padding: 12px;">
              <small class="text-muted">Enter amount received from customer</small>
            </div>
            
            <div class="mb-3">
              <label class="form-label" style="font-weight: 600;">Change</label>
              <div style="padding: 16px; background: #ECFDF5; border: 2px solid #059669; border-radius: 8px; font-size: 1.5rem; font-weight: 700; color: #059669; text-align: center;">
                ₱<span id="change_display">0.00</span>
              </div>
            </div>
            
            <div id="cash_warning" style="display:none; padding: 12px; background: #FEF2F2; border-left: 4px solid #EF4444; border-radius: 4px;">
              <div style="display: flex; align-items: center; gap: 8px; color: #991B1B;">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Insufficient! Need ₱<span id="required_amount">0.00</span></strong>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="action-buttons">
      <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-lg">
        <i class="fas fa-times mr-2"></i> Cancel
      </a>
      <button type="submit" class="btn btn-primary btn-lg">
        <i class="fas fa-save mr-2"></i> Create Sale
      </button>
    </div>
  </form>
</div>

<script>
  const products = {!! json_encode($products, JSON_HEX_APOS|JSON_HEX_QUOT) !!};
  function addRow() {
    const tbody = document.querySelector('#items_table tbody');
    const idx = tbody.children.length;
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>
        <select name="items[${idx}][product_id]" class="form-control" onchange="onProductChange(this, ${idx})">
          <option value="">Select product</option>
          ${products.map(p => `<option data-price="${p.selling_price}" value="${p.id}">${p.sku ?? ''} ${p.name}</option>`).join('')}
        </select>
      </td>
      <td><input type="number" name="items[${idx}][quantity]" class="form-control" min="1" value="1" oninput="recalcRow(${idx})"></td>
      <td><input type="number" step="0.01" name="items[${idx}][unit_price]" class="form-control" value="0" oninput="recalcRow(${idx})"></td>
      <td class="text-right"><span id="subtotal_${idx}">0.00</span></td>
      <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove(); renumber(); recalcTotals();">&times;</button></td>
    `;
    tbody.appendChild(tr);
  }
  function onProductChange(sel, idx) {
    const price = sel.options[sel.selectedIndex]?.getAttribute('data-price') || 0;
    document.querySelector(`[name="items[${idx}][unit_price]"]`).value = parseFloat(price).toFixed(2);
    recalcRow(idx);
  }
  function recalcRow(idx) {
    const qty = parseFloat(document.querySelector(`[name="items[${idx}][quantity]"]`)?.value || 0);
    const price = parseFloat(document.querySelector(`[name="items[${idx}][unit_price]"]`)?.value || 0);
    const sub = qty * price;
    document.getElementById(`subtotal_${idx}`).innerText = sub.toFixed(2);
    recalcTotals();
  }
  function recalcTotals() {
    const tbody = document.querySelector('#items_table tbody');
    let gross = 0;
    [...tbody.querySelectorAll('tr')].forEach((tr, i) => {
      const qty = parseFloat(tr.querySelector(`[name="items[${i}][quantity]"]`)?.value || 0);
      const price = parseFloat(tr.querySelector(`[name="items[${i}][unit_price]"]`)?.value || 0);
      gross += qty * price;
    });
    document.getElementById('subtotal_display').innerText = gross.toFixed(2);
    const discount = parseFloat(document.getElementById('discount_total').value || 0);
    document.getElementById('discount_display').innerText = discount.toFixed(2);
    const total = Math.max(0, gross - discount);
    document.getElementById('grand_total').innerText = total.toFixed(2);
    updateChange();
  }
  function renumber() {
    const rows = document.querySelectorAll('#items_table tbody tr');
    rows.forEach((tr, i) => {
      tr.querySelectorAll('select, input').forEach(el => {
        el.name = el.name.replace(/items\[[0-9]+\]/, `items[${i}]`);
      });
      tr.querySelector('[id^="subtotal_"]').id = `subtotal_${i}`;
    });
  }
  document.addEventListener('input', function(e){ 
    if(e.target && e.target.id==='discount_total'){ 
      recalcTotals(); 
    }
    if(e.target && e.target.id==='amount_tendered'){ 
      updateChange(); 
    }
  });

  function updateChange() {
    const tendered = parseFloat(document.getElementById('amount_tendered')?.value || 0);
    const total = parseFloat(document.getElementById('grand_total')?.innerText || 0);
    const change = Math.max(0, tendered - total);
    
    document.getElementById('change_display').innerText = change.toFixed(2);
    document.getElementById('required_amount').innerText = total.toFixed(2);
    
    const warning = document.getElementById('cash_warning');
    if (tendered > 0 && tendered < total) {
      warning.style.display = '';
    } else {
      warning.style.display = 'none';
    }
  }

  function validateCashPayment() {
    const saleType = document.getElementById('sale_type').value;
    const paymentMode = document.getElementById('payment_mode').value;
    
    if (saleType === 'cash' && paymentMode === 'cash') {
      const tendered = parseFloat(document.getElementById('amount_tendered')?.value || 0);
      const total = parseFloat(document.getElementById('grand_total')?.innerText || 0);
      
      if (tendered < total) {
        alert(`Insufficient payment!\n\nTotal Amount: ₱${total.toFixed(2)}\nAmount Tendered: ₱${tendered.toFixed(2)}\n\nPlease enter at least ₱${total.toFixed(2)}`);
        return false;
      }
    }
    return true;
  }
  
  document.addEventListener('DOMContentLoaded', function(){
    addRow();
    const pm = document.getElementById('payment_mode');
    const st = document.getElementById('sale_type');
    const form = document.querySelector('form');
    
    function togglePayment() {
      const online = pm.value === 'online';
      document.getElementById('online_fields').style.display = online ? '' : 'none';
      document.getElementById('cash_fields').style.display = online ? 'none' : '';
      updateChange();
    }
    function toggleLoan() {
      const isLoan = st.value === 'loan';
      document.getElementById('loan_fields').style.display = isLoan ? '' : 'none';
      
      // Toggle ID verification required status
      const idType = document.getElementById('id_type');
      const idNumber = document.getElementById('id_number');
      const idImage = document.getElementById('id_image');
      
      if (isLoan) {
        idType.setAttribute('required', 'required');
        idNumber.setAttribute('required', 'required');
        idImage.setAttribute('required', 'required');
      } else {
        idType.removeAttribute('required');
        idNumber.removeAttribute('required');
        idImage.removeAttribute('required');
      }
    }
    
    // ID Image Preview
    const idImageInput = document.getElementById('id_image');
    const idImagePreview = document.getElementById('id_image_preview');
    const idPreviewImg = document.getElementById('id_preview_img');
    const idPreviewPdf = document.getElementById('id_preview_pdf');
    const idPdfName = document.getElementById('id_pdf_name');
    
    idImageInput.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        // Check file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
          alert('File size must be less than 5MB');
          this.value = '';
          idImagePreview.style.display = 'none';
          return;
        }
        
        // Show preview for images
        if (file.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = function(e) {
            idPreviewImg.src = e.target.result;
            idPreviewImg.style.display = 'block';
            idPreviewPdf.style.display = 'none';
            idImagePreview.style.display = 'block';
          };
          reader.readAsDataURL(file);
        } else if (file.type === 'application/pdf') {
          // For PDF, show filename
          idPreviewImg.style.display = 'none';
          idPreviewPdf.style.display = 'block';
          idPdfName.textContent = file.name;
          idImagePreview.style.display = 'block';
        }
      } else {
        idImagePreview.style.display = 'none';
      }
    });
    
    pm.addEventListener('change', togglePayment);
    st.addEventListener('change', toggleLoan);
    form.addEventListener('submit', function(e) {
      if (!validateCashPayment()) {
        e.preventDefault();
        return false;
      }
      
      // Validate ID fields for loan sales
      if (st.value === 'loan') {
        const idType = document.getElementById('id_type').value;
        const idNumber = document.getElementById('id_number').value;
        const idImage = document.getElementById('id_image').files.length;
        
        if (!idType || !idNumber || !idImage) {
          e.preventDefault();
          alert('Please complete all ID verification fields for loan sales.');
          return false;
        }
      }
    });
    
    togglePayment();
    toggleLoan();
  });
</script>

@endsection
