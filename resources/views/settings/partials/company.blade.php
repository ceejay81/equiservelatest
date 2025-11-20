<div class="form-section-title">
  <i class="fas fa-building mr-2"></i>Company Information
</div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      <label class="form-label">Company Name <span class="text-danger">*</span></label>
      <input type="text" 
             name="name" 
             id="companyName"
             class="form-control" 
             value="{{ old('name', $settings['name'] ?? '') }}" 
             placeholder="Enter company name"
             maxlength="255"
             required>
      <small class="form-text text-muted">This will appear on invoices, receipts, and reports</small>
    </div>
  </div>
  
  <div class="col-md-12">
    <div class="form-group">
      <label class="form-label">Address</label>
      <textarea name="address" 
                id="companyAddress"
                class="form-control" 
                rows="2" 
                maxlength="500"
                placeholder="Enter company address">{{ old('address', $settings['address'] ?? '') }}</textarea>
      <small class="form-text text-muted">Full business address including street, city, and postal code</small>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="form-group">
      <label class="form-label">Phone Number</label>
      <input type="text" 
             name="phone" 
             id="companyPhone"
             class="form-control" 
             value="{{ old('phone', $settings['phone'] ?? '') }}" 
             maxlength="50"
             placeholder="e.g., +63 2 1234 5678">
      <small class="form-text text-muted">Contact phone number for customer inquiries</small>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="form-group">
      <label class="form-label">Email Address <span class="text-danger">*</span></label>
      <input type="email" 
             name="email" 
             id="companyEmail"
             class="form-control" 
             value="{{ old('email', $settings['email'] ?? '') }}" 
             placeholder="e.g., info@company.com"
             maxlength="255"
             required>
      <small class="form-text text-muted">Official business email address</small>
    </div>
  </div>
  
  <div class="col-md-12">
    <div class="form-group mb-0">
      <label class="form-label">Tax Identification Number</label>
      <input type="text" 
             name="tax_id" 
             id="companyTaxId"
             class="form-control" 
             value="{{ old('tax_id', $settings['tax_id'] ?? '') }}" 
             maxlength="100"
             placeholder="e.g., 123-456-789-000">
      <small class="form-text text-muted">TIN or business registration number for tax purposes</small>
    </div>
  </div>
</div>

@push('scripts')
<script>
  $(document).ready(function() {
    // Email validation
    $('#companyEmail').on('blur', function() {
      const email = $(this).val();
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      
      if (email && !emailRegex.test(email)) {
        $(this).addClass('is-invalid');
        if ($(this).next('.invalid-feedback').length === 0) {
          $(this).after('<div class="invalid-feedback">Please enter a valid email address</div>');
        }
      } else {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
      }
    });
    
    // Clear validation on input
    $('#companyEmail').on('input', function() {
      $(this).removeClass('is-invalid');
      $(this).next('.invalid-feedback').remove();
    });
  });
</script>
@endpush
