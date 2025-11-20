<div class="form-section-title">
  <i class="fas fa-hand-holding-usd mr-2"></i>Loan Policies
</div>

<div class="row">
  <div class="col-md-4">
    <div class="form-group">
      <label class="form-label">Minimum Down Payment (%) <span class="text-danger">*</span></label>
      <div class="input-group">
        <input type="number" 
               name="min_down_payment_percent" 
               id="loanMinDownPayment"
               class="form-control" 
               value="{{ old('min_down_payment_percent', $settings['min_down_payment_percent'] ?? 20.0) }}" 
               step="0.01"
               min="0"
               max="100"
               placeholder="20.00"
               required>
        <div class="input-group-append">
          <span class="input-group-text">%</span>
        </div>
      </div>
      <small class="form-text text-muted">Minimum percentage of total price required as down payment</small>
    </div>
  </div>
  
  <div class="col-md-4">
    <div class="form-group">
      <label class="form-label">Maximum Loan Term (Months) <span class="text-danger">*</span></label>
      <div class="input-group">
        <input type="number" 
               name="max_term_months" 
               id="loanMaxTerm"
               class="form-control" 
               value="{{ old('max_term_months', $settings['max_term_months'] ?? 36) }}" 
               min="1"
               max="120"
               step="1"
               placeholder="36"
               required>
        <div class="input-group-append">
          <span class="input-group-text">months</span>
        </div>
      </div>
      <small class="form-text text-muted">Maximum number of months allowed for loan repayment</small>
    </div>
  </div>
  
  <div class="col-md-4">
    <div class="form-group mb-0">
      <label class="form-label">Default Interest Rate (%) <span class="text-danger">*</span></label>
      <div class="input-group">
        <input type="number" 
               name="default_interest_rate" 
               id="loanDefaultInterest"
               class="form-control" 
               value="{{ old('default_interest_rate', $settings['default_interest_rate'] ?? 2.0) }}" 
               step="0.01"
               min="0"
               max="100"
               placeholder="2.00"
               required>
        <div class="input-group-append">
          <span class="input-group-text">%</span>
        </div>
      </div>
      <small class="form-text text-muted">Default monthly interest rate for new loans</small>
    </div>
  </div>
</div>

<div class="alert alert-info mt-3 mb-0">
  <i class="fas fa-info-circle mr-2"></i>
  <strong>Note:</strong> These policies will be enforced when creating new loans. Existing loans will not be affected by changes to these settings.
</div>

@push('scripts')
<script>
  $(document).ready(function() {
    // Validate down payment percentage
    $('#loanMinDownPayment').on('input change', function() {
      const value = parseFloat($(this).val());
      
      if (isNaN(value) || value < 0 || value > 100) {
        $(this).addClass('is-invalid');
        if ($(this).parent().next('.invalid-feedback').length === 0) {
          $(this).parent().after('<div class="invalid-feedback">Down payment must be between 0 and 100%</div>');
        }
      } else {
        $(this).removeClass('is-invalid');
        $(this).parent().next('.invalid-feedback').remove();
      }
    });
    
    // Validate loan term
    $('#loanMaxTerm').on('input change', function() {
      const value = parseInt($(this).val());
      
      if (isNaN(value) || value < 1 || value > 120) {
        $(this).addClass('is-invalid');
        if ($(this).parent().next('.invalid-feedback').length === 0) {
          $(this).parent().after('<div class="invalid-feedback">Loan term must be between 1 and 120 months</div>');
        }
      } else {
        $(this).removeClass('is-invalid');
        $(this).parent().next('.invalid-feedback').remove();
      }
    });
    
    // Validate interest rate
    $('#loanDefaultInterest').on('input change', function() {
      const value = parseFloat($(this).val());
      
      if (isNaN(value) || value < 0 || value > 100) {
        $(this).addClass('is-invalid');
        if ($(this).parent().next('.invalid-feedback').length === 0) {
          $(this).parent().after('<div class="invalid-feedback">Interest rate must be between 0 and 100%</div>');
        }
      } else {
        $(this).removeClass('is-invalid');
        $(this).parent().next('.invalid-feedback').remove();
      }
    });
  });
</script>
@endpush
