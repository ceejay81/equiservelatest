<div class="form-section-title">
  <i class="fas fa-receipt mr-2"></i>Tax Settings
</div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      <div class="custom-control custom-switch">
        <input type="hidden" name="enabled" value="0">
        <input type="checkbox" 
               class="custom-control-input" 
               id="taxEnabled" 
               name="enabled" 
               value="1"
               {{ old('enabled', $settings['enabled'] ?? false) ? 'checked' : '' }}>
        <label class="custom-control-label" for="taxEnabled">
          <strong>Enable Tax Calculation</strong>
        </label>
      </div>
      <small class="form-text text-muted">When enabled, tax will be calculated on all sales transactions</small>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="form-group">
      <label class="form-label">Default Tax Rate (%) <span class="text-danger">*</span></label>
      <div class="input-group">
        <input type="number" 
               name="default_rate" 
               id="taxDefaultRate"
               class="form-control" 
               value="{{ old('default_rate', $settings['default_rate'] ?? 12.0) }}" 
               step="0.01"
               min="0"
               max="100"
               placeholder="12.00"
               required>
        <div class="input-group-append">
          <span class="input-group-text">%</span>
        </div>
      </div>
      <small class="form-text text-muted">Tax percentage applied to sales (0-100)</small>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="form-group mb-0">
      <label class="form-label">Tax Label <span class="text-danger">*</span></label>
      <input type="text" 
             name="label" 
             id="taxLabel"
             class="form-control" 
             value="{{ old('label', $settings['label'] ?? 'VAT') }}" 
             placeholder="e.g., VAT, GST, Sales Tax"
             maxlength="50"
             required>
      <small class="form-text text-muted">How tax will be labeled on documents (e.g., VAT, GST)</small>
    </div>
  </div>
</div>

<div class="alert alert-info mt-3 mb-0">
  <i class="fas fa-info-circle mr-2"></i>
  <strong>Note:</strong> Tax settings apply to new transactions only. Existing transactions will not be affected by changes to these settings.
</div>

@push('scripts')
<script>
  $(document).ready(function() {
    // Validate tax rate is within range
    $('#taxDefaultRate').on('input change', function() {
      const value = parseFloat($(this).val());
      
      if (isNaN(value) || value < 0 || value > 100) {
        $(this).addClass('is-invalid');
        if ($(this).parent().next('.invalid-feedback').length === 0) {
          $(this).parent().after('<div class="invalid-feedback">Tax rate must be between 0 and 100</div>');
        }
      } else {
        $(this).removeClass('is-invalid');
        $(this).parent().next('.invalid-feedback').remove();
      }
    });
  });
</script>
@endpush
