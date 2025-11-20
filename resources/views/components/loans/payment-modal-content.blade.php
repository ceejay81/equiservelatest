@props(['loan'])

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

<!-- Online Payment Fields -->
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

<script>
document.addEventListener('DOMContentLoaded', function() {
  const paymentMode = document.getElementById('paymentMode');
  const onlineFields = document.getElementById('onlinePaymentFields');
  const referenceNumber = document.getElementById('referenceNumber');
  const proofImage = document.getElementById('proofImage');
  
  paymentMode.addEventListener('change', function() {
    const isOnline = ['Bank Transfer', 'GCash', 'PayMaya'].includes(this.value);
    onlineFields.style.display = isOnline ? 'block' : 'none';
    
    if (isOnline) {
      referenceNumber.setAttribute('required', 'required');
      proofImage.setAttribute('required', 'required');
    } else {
      referenceNumber.removeAttribute('required');
      proofImage.removeAttribute('required');
    }
  });
});
</script>
