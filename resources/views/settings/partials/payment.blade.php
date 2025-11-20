<div class="form-section-title">
  <i class="fas fa-credit-card mr-2"></i>Payment Methods
</div>

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <div class="custom-control custom-switch">
        <input type="hidden" name="cash_enabled" value="0">
        <input type="checkbox" 
               class="custom-control-input payment-method" 
               id="cashEnabled" 
               name="cash_enabled" 
               value="1"
               {{ old('cash_enabled', $settings['cash_enabled'] ?? true) ? 'checked' : '' }}>
        <label class="custom-control-label" for="cashEnabled">
          <i class="fas fa-money-bill-wave text-success mr-1"></i>
          <strong>Cash</strong>
        </label>
      </div>
      <small class="form-text text-muted">Accept cash payments</small>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="form-group">
      <div class="custom-control custom-switch">
        <input type="hidden" name="bank_transfer_enabled" value="0">
        <input type="checkbox" 
               class="custom-control-input payment-method" 
               id="bankTransferEnabled" 
               name="bank_transfer_enabled" 
               value="1"
               {{ old('bank_transfer_enabled', $settings['bank_transfer_enabled'] ?? true) ? 'checked' : '' }}>
        <label class="custom-control-label" for="bankTransferEnabled">
          <i class="fas fa-university text-primary mr-1"></i>
          <strong>Bank Transfer</strong>
        </label>
      </div>
      <small class="form-text text-muted">Accept bank transfer payments</small>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="form-group">
      <div class="custom-control custom-switch">
        <input type="hidden" name="check_enabled" value="0">
        <input type="checkbox" 
               class="custom-control-input payment-method" 
               id="checkEnabled" 
               name="check_enabled" 
               value="1"
               {{ old('check_enabled', $settings['check_enabled'] ?? true) ? 'checked' : '' }}>
        <label class="custom-control-label" for="checkEnabled">
          <i class="fas fa-money-check text-info mr-1"></i>
          <strong>Check</strong>
        </label>
      </div>
      <small class="form-text text-muted">Accept check payments</small>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="form-group mb-0">
      <div class="custom-control custom-switch">
        <input type="hidden" name="online_enabled" value="0">
        <input type="checkbox" 
               class="custom-control-input payment-method" 
               id="onlineEnabled" 
               name="online_enabled" 
               value="1"
               {{ old('online_enabled', $settings['online_enabled'] ?? false) ? 'checked' : '' }}>
        <label class="custom-control-label" for="onlineEnabled">
          <i class="fas fa-globe text-warning mr-1"></i>
          <strong>Online Payment</strong>
        </label>
      </div>
      <small class="form-text text-muted">Accept online/digital payments</small>
    </div>
  </div>
</div>

<div class="alert alert-warning mt-3 mb-0">
  <i class="fas fa-exclamation-triangle mr-2"></i>
  <strong>Important:</strong> At least one payment method must remain enabled. The system will prevent you from disabling all payment methods.
</div>

@push('scripts')
<script>
  // Client-side validation for payment methods
  $(document).ready(function() {
    function validatePaymentMethods() {
      const enabledCount = $('.payment-method:checked').length;
      
      if (enabledCount === 0) {
        alert('At least one payment method must be enabled.');
        return false;
      }
      
      // If only one is checked, disable its checkbox to prevent unchecking
      if (enabledCount === 1) {
        $('.payment-method:checked').prop('disabled', true);
      } else {
        $('.payment-method').prop('disabled', false);
      }
      
      return true;
    }
    
    $('.payment-method').on('change', function() {
      const enabledCount = $('.payment-method:checked').length;
      
      if (enabledCount === 0) {
        // Prevent unchecking the last one
        $(this).prop('checked', true);
        alert('At least one payment method must remain enabled.');
        return false;
      }
      
      validatePaymentMethods();
    });
    
    // Re-enable all checkboxes before form submission
    $('#settingsForm').on('submit', function(e) {
      $('.payment-method').prop('disabled', false);
      
      if (!validatePaymentMethods()) {
        e.preventDefault();
        return false;
      }
    });
    
    // Initial validation
    validatePaymentMethods();
  });
</script>
@endpush
