<div class="form-section-title">
  <i class="fas fa-bell mr-2"></i>Notification Preferences
</div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      <div class="custom-control custom-switch">
        <input type="hidden" name="enabled" value="0">
        <input type="checkbox" 
               class="custom-control-input" 
               id="notificationsEnabled" 
               name="enabled" 
               value="1"
               {{ old('enabled', $settings['enabled'] ?? true) ? 'checked' : '' }}>
        <label class="custom-control-label" for="notificationsEnabled">
          <strong>Enable In-App Notifications</strong>
        </label>
      </div>
      <small class="form-text text-muted">Master switch for all in-app notifications</small>
    </div>
  </div>
</div>

<div class="form-section-title mt-3" style="font-size: 0.9rem; border-bottom: 1px solid #E5E7EB;">
  Notification Types
</div>

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <div class="custom-control custom-checkbox">
        <input type="hidden" name="low_stock_alert" value="0">
        <input type="checkbox" 
               class="custom-control-input" 
               id="lowStockAlert" 
               name="low_stock_alert" 
               value="1"
               {{ old('low_stock_alert', $settings['low_stock_alert'] ?? true) ? 'checked' : '' }}>
        <label class="custom-control-label" for="lowStockAlert">
          <i class="fas fa-exclamation-circle text-warning mr-1"></i>
          <strong>Low Stock Alerts</strong>
        </label>
      </div>
      <small class="form-text text-muted ml-4">Notify when product stock reaches low threshold</small>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="form-group">
      <div class="custom-control custom-checkbox">
        <input type="hidden" name="critical_stock_alert" value="0">
        <input type="checkbox" 
               class="custom-control-input" 
               id="criticalStockAlert" 
               name="critical_stock_alert" 
               value="1"
               {{ old('critical_stock_alert', $settings['critical_stock_alert'] ?? true) ? 'checked' : '' }}>
        <label class="custom-control-label" for="criticalStockAlert">
          <i class="fas fa-exclamation-triangle text-danger mr-1"></i>
          <strong>Critical Stock Alerts</strong>
        </label>
      </div>
      <small class="form-text text-muted ml-4">Notify when product stock reaches critical threshold</small>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="form-group mb-0">
      <div class="custom-control custom-checkbox">
        <input type="hidden" name="loan_payment_due" value="0">
        <input type="checkbox" 
               class="custom-control-input" 
               id="loanPaymentDue" 
               name="loan_payment_due" 
               value="1"
               {{ old('loan_payment_due', $settings['loan_payment_due'] ?? true) ? 'checked' : '' }}>
        <label class="custom-control-label" for="loanPaymentDue">
          <i class="fas fa-calendar-check text-info mr-1"></i>
          <strong>Loan Payment Due</strong>
        </label>
      </div>
      <small class="form-text text-muted ml-4">Notify when loan payments are due or overdue</small>
    </div>
  </div>
</div>

<div class="alert alert-info mt-3 mb-0">
  <i class="fas fa-info-circle mr-2"></i>
  <strong>Note:</strong> Individual notification types can only be enabled if the master notification switch is enabled. Notifications appear in the system notification panel.
</div>
