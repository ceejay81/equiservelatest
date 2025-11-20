<div class="form-section-title">
  <i class="fas fa-cogs mr-2"></i>Module Configuration
</div>

<!-- Products Module Settings -->
<div class="card mb-3">
  <div class="card-header" style="background: #F8FAFC;">
    <h6 class="mb-0" style="font-weight: 600; color: #0F172A;">
      <i class="fas fa-box mr-2" style="color: #3B82F6;"></i>
      Products Module
    </h6>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group mb-2">
          <div class="custom-control custom-switch">
            <input type="hidden" name="products_require_image" value="0">
            <input type="checkbox" 
                   class="custom-control-input" 
                   id="productsRequireImage" 
                   name="products_require_image" 
                   value="1"
                   {{ old('products_require_image', $settings['products_require_image'] ?? false) ? 'checked' : '' }}>
            <label class="custom-control-label" for="productsRequireImage">
              <strong>Require Product Image</strong>
            </label>
          </div>
          <small class="form-text text-muted">Make product image mandatory when adding products</small>
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="form-group mb-2">
          <div class="custom-control custom-switch">
            <input type="hidden" name="products_require_category" value="0">
            <input type="checkbox" 
                   class="custom-control-input" 
                   id="productsRequireCategory" 
                   name="products_require_category" 
                   value="1"
                   {{ old('products_require_category', $settings['products_require_category'] ?? false) ? 'checked' : '' }}>
            <label class="custom-control-label" for="productsRequireCategory">
              <strong>Require Product Category</strong>
            </label>
          </div>
          <small class="form-text text-muted">Make category selection mandatory when adding products</small>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Sales Module Settings -->
<div class="card mb-3">
  <div class="card-header" style="background: #F8FAFC;">
    <h6 class="mb-0" style="font-weight: 600; color: #0F172A;">
      <i class="fas fa-shopping-cart mr-2" style="color: #10B981;"></i>
      Sales Module
    </h6>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group mb-0">
          <div class="custom-control custom-switch">
            <input type="hidden" name="sales_allow_negative_stock" value="0">
            <input type="checkbox" 
                   class="custom-control-input" 
                   id="salesAllowNegativeStock" 
                   name="sales_allow_negative_stock" 
                   value="1"
                   {{ old('sales_allow_negative_stock', $settings['sales_allow_negative_stock'] ?? false) ? 'checked' : '' }}>
            <label class="custom-control-label" for="salesAllowNegativeStock">
              <strong>Allow Negative Stock</strong>
            </label>
          </div>
          <small class="form-text text-muted">Allow sales even when product stock is zero or negative (backorder)</small>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Inventory Module Settings -->
<div class="card mb-3">
  <div class="card-header" style="background: #F8FAFC;">
    <h6 class="mb-0" style="font-weight: 600; color: #0F172A;">
      <i class="fas fa-warehouse mr-2" style="color: #F59E0B;"></i>
      Inventory Module
    </h6>
  </div>
  <div class="card-body">
    <div class="alert alert-info mb-0">
      <i class="fas fa-info-circle mr-2"></i>
      <small>Inventory module settings are configured in the Inventory Thresholds section.</small>
    </div>
  </div>
</div>

<!-- Loans Module Settings -->
<div class="card mb-0">
  <div class="card-header" style="background: #F8FAFC;">
    <h6 class="mb-0" style="font-weight: 600; color: #0F172A;">
      <i class="fas fa-hand-holding-usd mr-2" style="color: #8B5CF6;"></i>
      Loans Module
    </h6>
  </div>
  <div class="card-body">
    <div class="alert alert-info mb-0">
      <i class="fas fa-info-circle mr-2"></i>
      <small>Loan module settings are configured in the Loan Policies section.</small>
    </div>
  </div>
</div>

<div class="alert alert-info mt-3 mb-0">
  <i class="fas fa-info-circle mr-2"></i>
  <strong>Note:</strong> Module configuration changes take effect immediately and apply to all future operations in the respective modules.
</div>
