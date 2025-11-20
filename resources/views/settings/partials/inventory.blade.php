<div class="form-section-title">
  <i class="fas fa-warehouse mr-2"></i>Inventory Thresholds
</div>

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label class="form-label">Low Stock Threshold <span class="text-danger">*</span></label>
      <div class="input-group">
        <input type="number" 
               name="low_stock_threshold" 
               id="lowStockThreshold"
               class="form-control" 
               value="{{ old('low_stock_threshold', $settings['low_stock_threshold'] ?? 10) }}" 
               min="0"
               step="1"
               placeholder="10"
               required>
        <div class="input-group-append">
          <span class="input-group-text">units</span>
        </div>
      </div>
      <small class="form-text text-muted">Products at or below this quantity will show a low stock warning</small>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="form-group mb-0">
      <label class="form-label">Critical Stock Threshold <span class="text-danger">*</span></label>
      <div class="input-group">
        <input type="number" 
               name="critical_stock_threshold" 
               id="criticalStockThreshold"
               class="form-control" 
               value="{{ old('critical_stock_threshold', $settings['critical_stock_threshold'] ?? 5) }}" 
               min="0"
               step="1"
               placeholder="5"
               required>
        <div class="input-group-append">
          <span class="input-group-text">units</span>
        </div>
      </div>
      <small class="form-text text-muted">Products at or below this quantity will show a critical stock alert</small>
    </div>
  </div>
</div>

<div class="alert alert-warning mt-3 mb-0">
  <i class="fas fa-exclamation-triangle mr-2"></i>
  <strong>Important:</strong> Critical stock threshold must be less than or equal to low stock threshold. The system will validate this when you save.
</div>

@push('scripts')
<script>
  // Client-side validation for inventory thresholds
  $(document).ready(function() {
    function validateThresholds() {
      const lowStock = parseInt($('#lowStockThreshold').val()) || 0;
      const criticalStock = parseInt($('#criticalStockThreshold').val()) || 0;
      
      if (criticalStock > lowStock) {
        $('#criticalStockThreshold').addClass('is-invalid');
        if ($('#criticalStockThreshold').next('.invalid-feedback').length === 0) {
          $('#criticalStockThreshold').after('<div class="invalid-feedback">Critical threshold must be less than or equal to low stock threshold</div>');
        }
        return false;
      } else {
        $('#criticalStockThreshold').removeClass('is-invalid');
        $('#criticalStockThreshold').next('.invalid-feedback').remove();
        return true;
      }
    }
    
    $('#lowStockThreshold, #criticalStockThreshold').on('input change', validateThresholds);
    
    $('#settingsForm').on('submit', function(e) {
      if (!validateThresholds()) {
        e.preventDefault();
        alert('Please fix the validation errors before saving.');
        return false;
      }
    });
  });
</script>
@endpush
