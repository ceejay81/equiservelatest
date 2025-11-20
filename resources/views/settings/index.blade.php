@extends('layouts.app')

@php($pageTitle = 'Settings')

@push('styles')
<style>
  /* Settings Card Styling - Matches Products Module */
  .settings-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    height: 100%;
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: inherit;
    border: 1px solid #E5E7EB;
  }
  .settings-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    text-decoration: none;
    color: inherit;
  }
  
  /* Icon Styling with Gradient Backgrounds */
  .settings-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    margin-bottom: 16px;
  }
  .settings-icon.blue { background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); color: white; }
  .settings-icon.green { background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; }
  .settings-icon.orange { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); color: white; }
  .settings-icon.purple { background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); color: white; }
  .settings-icon.indigo { background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%); color: white; }
  .settings-icon.pink { background: linear-gradient(135deg, #EC4899 0%, #DB2777 100%); color: white; }
  .settings-icon.teal { background: linear-gradient(135deg, #14B8A6 0%, #0D9488 100%); color: white; }
  .settings-icon.cyan { background: linear-gradient(135deg, #06B6D4 0%, #0891B2 100%); color: white; }
  
  /* Typography */
  .settings-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #0F172A;
    margin-bottom: 8px;
  }
  .settings-description {
    font-size: 0.875rem;
    color: #64748B;
    line-height: 1.5;
    flex-grow: 1;
  }
  .settings-arrow {
    margin-top: 12px;
    color: #3B82F6;
    font-size: 0.875rem;
    font-weight: 600;
  }
  
  /* Responsive Design for Mobile */
  @media (max-width: 767px) {
    .settings-card {
      padding: 20px;
    }
    .settings-icon {
      width: 48px;
      height: 48px;
      font-size: 1.5rem;
    }
    .settings-title {
      font-size: 1rem;
    }
    h1 {
      font-size: 1.5rem !important;
    }
    .d-flex.justify-content-between {
      flex-direction: column;
      align-items: flex-start !important;
      gap: 1rem;
    }
    .row.mb-4 .col-md-4 {
      margin-bottom: 1rem;
    }
  }
  
  /* Tablet Responsive */
  @media (min-width: 768px) and (max-width: 991px) {
    .settings-card {
      padding: 20px;
    }
  }
  
  /* Alert Styling */
  .alert {
    border-radius: 8px;
    border: none;
  }
  .alert-info {
    background-color: #EFF6FF;
    color: #1E40AF;
  }
  .alert-warning {
    background-color: #FEF3C7;
    color: #92400E;
  }
  .alert-success {
    background-color: #D1FAE5;
    color: #065F46;
  }
  .alert-danger {
    background-color: #FEE2E2;
    color: #991B1B;
  }
  
  /* Card Styling */
  .card {
    border-radius: 12px;
    border: 1px solid #E5E7EB;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  }
  .card-header {
    background: #F8FAFC;
    border-bottom: 1px solid #E5E7EB;
    padding: 1rem 1.25rem;
  }
  .card-body {
    padding: 1.25rem;
  }
  
  /* Modal Styling */
  .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  }
  .modal-header {
    border-bottom: 1px solid #E5E7EB;
    padding: 1.25rem 1.5rem;
  }
  .modal-body {
    padding: 1.5rem;
  }
  .modal-footer {
    border-top: 1px solid #E5E7EB;
    padding: 1rem 1.5rem;
  }
  
  /* Button Styling */
  .btn {
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s;
  }
  .btn-primary {
    background-color: #3B82F6;
    border-color: #3B82F6;
  }
  .btn-primary:hover {
    background-color: #2563EB;
    border-color: #2563EB;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
  }
  .btn-outline-secondary:hover {
    background-color: #F8FAFC;
  }
  .btn-outline-primary:hover {
    background-color: #EFF6FF;
    color: #3B82F6;
  }
  .btn-outline-info:hover {
    background-color: #ECFEFF;
    color: #0891B2;
  }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="mb-1" style="font-size: 1.875rem; font-weight: 700; color: #0F172A;">System Settings</h1>
    <p class="text-muted mb-0">Configure system-wide settings and business rules</p>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
@endif

@if(session('warning'))
  <div class="alert alert-warning alert-dismissible fade show">
    <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('warning') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
@endif

<!-- Info Alert -->
<div class="alert alert-info mb-4">
  <i class="fas fa-info-circle mr-2"></i>
  <strong>Settings Module:</strong> Configure business rules and module-specific settings. 
  Changes take effect immediately and are logged in the audit trail.
</div>

<!-- Settings Groups Grid -->
<div class="row">
  <!-- Loan & Penalty Settings Card -->
  <div class="col-md-6 col-lg-4 mb-4">
    <a href="{{ route('settings.loan-penalty') }}" class="settings-card">
      <div class="settings-icon purple">
        <i class="fas fa-percent"></i>
      </div>
      <div class="settings-title">Loan & Penalty</div>
      <div class="settings-description">Configure penalty rates, grace periods, and rebate rules for loan payments</div>
      <div class="settings-arrow">
        Configure <i class="fas fa-arrow-right ml-1"></i>
      </div>
    </a>
  </div>

  @forelse($groups ?? [] as $key => $group)
    <div class="col-md-6 col-lg-4 mb-4">
      <a href="{{ route('settings.show', $key) }}" class="settings-card">
        <div class="settings-icon {{ $group['color'] ?? 'blue' }}">
          <i class="fas fa-{{ $group['icon'] }}"></i>
        </div>
        <div class="settings-title">{{ $group['title'] }}</div>
        <div class="settings-description">{{ $group['description'] }}</div>
        <div class="settings-arrow">
          Configure <i class="fas fa-arrow-right ml-1"></i>
        </div>
      </a>
    </div>
  @empty
    <div class="col-12">
      <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <strong>No settings groups found.</strong> Please run <code>php artisan config:cache</code> to refresh configuration.
      </div>
    </div>
  @endforelse
</div>

<!-- Quick Actions Card -->
<div class="card mt-4">
  <div class="card-header" style="background: #F8FAFC;">
    <h5 class="mb-0" style="font-weight: 600; color: #0F172A;">
      <i class="fas fa-bolt mr-2" style="color: #F59E0B;"></i>
      Quick Actions
    </h5>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-4 mb-3 mb-md-0">
        <a href="{{ route('settings.export') }}" class="btn btn-outline-primary btn-block">
          <i class="fas fa-file-export mr-2"></i>Export All Settings
        </a>
        <small class="text-muted d-block mt-2">Download settings as JSON backup</small>
      </div>
      <div class="col-md-4 mb-3 mb-md-0">
        <button class="btn btn-outline-secondary btn-block" data-toggle="modal" data-target="#importModal">
          <i class="fas fa-file-import mr-2"></i>Import Settings
        </button>
        <small class="text-muted d-block mt-2">Restore settings from backup file</small>
      </div>
      <div class="col-md-4">
        <a href="{{ route('settings.audit') }}" class="btn btn-outline-info btn-block">
          <i class="fas fa-history mr-2"></i>View Audit Log
        </a>
        <small class="text-muted d-block mt-2">Track all settings changes</small>
      </div>
    </div>
  </div>
</div>

<!-- System Information Card -->
<div class="card mt-4">
  <div class="card-header" style="background: #F8FAFC;">
    <h5 class="mb-0" style="font-weight: 600; color: #0F172A;">
      <i class="fas fa-server mr-2" style="color: #3B82F6;"></i>
      System Information
    </h5>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-3">
        <small class="text-muted d-block">Application Version</small>
        <strong>1.0.0</strong>
      </div>
      <div class="col-md-3">
        <small class="text-muted d-block">Laravel Version</small>
        <strong>{{ app()->version() }}</strong>
      </div>
      <div class="col-md-3">
        <small class="text-muted d-block">PHP Version</small>
        <strong>{{ PHP_VERSION }}</strong>
      </div>
      <div class="col-md-3">
        <small class="text-muted d-block">Environment</small>
        <strong class="text-uppercase">{{ config('app.env') }}</strong>
      </div>
    </div>
  </div>
</div>

<!-- Import Settings Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius: 12px; border: none;">
      <div class="modal-header" style="border-bottom: 1px solid #E5E7EB;">
        <h5 class="modal-title" style="font-weight: 600; color: #0F172A;">
          <i class="fas fa-file-import mr-2" style="color: #3B82F6;"></i>
          Import Settings
        </h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form method="POST" action="{{ route('settings.import') }}" enctype="multipart/form-data" id="importForm">
        @csrf
        <div class="modal-body">
          <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Warning:</strong> Importing settings will overwrite current values. Make sure to export your current settings first as a backup.
          </div>
          
          <div class="form-group">
            <label>Settings File <span class="text-danger">*</span></label>
            <input type="file" name="file" class="form-control-file" accept=".json" required>
            <small class="form-text text-muted">Upload a JSON file exported from this system</small>
          </div>
          
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="confirmImport" required>
            <label class="custom-control-label" for="confirmImport">
              I understand that this will show a preview before applying changes
            </label>
          </div>
        </div>
        <div class="modal-footer" style="border-top: 1px solid #E5E7EB;">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-search mr-1"></i>Preview Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  $(document).ready(function() {
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
      $('.alert').fadeOut('slow');
    }, 5000);
    
    // Show success toast if session has success message
    @if(session('success'))
      showToast('success', '{{ session('success') }}');
    @endif
    
    // Show error toast if session has error message
    @if(session('error'))
      showToast('error', '{{ session('error') }}');
    @endif
    
    // Show warning toast if session has warning message
    @if(session('warning'))
      showToast('warning', '{{ session('warning') }}');
    @endif
    
    // Import form submission with loading state
    $('#importForm').on('submit', function(e) {
      const form = $(this);
      const submitBtn = form.find('button[type="submit"]');
      const fileInput = form.find('input[type="file"]');
      
      // Validate file is selected
      if (!fileInput[0].files.length) {
        e.preventDefault();
        showToast('error', 'Please select a file to import.');
        return false;
      }
      
      // Validate file extension
      const fileName = fileInput[0].files[0].name;
      if (!fileName.endsWith('.json')) {
        e.preventDefault();
        showToast('error', 'Please select a valid JSON file.');
        return false;
      }
      
      // Disable submit button and show loading state
      submitBtn.prop('disabled', true);
      submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Processing...');
      
      // Show loading overlay
      showLoadingOverlay('Validating import file...');
      
      // Close modal
      $('#importModal').modal('hide');
    });
    
    // Export button loading state
    $('a[href*="export"]').on('click', function(e) {
      const btn = $(this);
      const originalHtml = btn.html();
      
      btn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Exporting...');
      btn.addClass('disabled');
      
      // Reset after 2 seconds
      setTimeout(function() {
        btn.html(originalHtml);
        btn.removeClass('disabled');
        showToast('success', 'Settings exported successfully');
      }, 2000);
    });
    
    // Settings card hover effect with loading state
    $('.settings-card').on('click', function(e) {
      if (!$(e.target).is('a')) return;
      
      const card = $(this);
      const arrow = card.find('.settings-arrow');
      const originalHtml = arrow.html();
      
      arrow.html('<i class="fas fa-spinner fa-spin ml-1"></i>');
      
      // Show brief loading state
      setTimeout(function() {
        arrow.html(originalHtml);
      }, 500);
    });
    
    // File input styling
    $('input[type="file"]').on('change', function() {
      const fileName = $(this).val().split('\\').pop();
      if (fileName) {
        $(this).next('.custom-file-label').html(fileName);
      }
    });
  });
  
  // Toast notification function
  function showToast(type, message) {
    const iconMap = {
      'success': 'fa-check-circle',
      'error': 'fa-exclamation-circle',
      'warning': 'fa-exclamation-triangle',
      'info': 'fa-info-circle'
    };
    
    const colorMap = {
      'success': '#10B981',
      'error': '#EF4444',
      'warning': '#F59E0B',
      'info': '#3B82F6'
    };
    
    const icon = iconMap[type] || 'fa-info-circle';
    const color = colorMap[type] || '#3B82F6';
    
    const toast = $(`
      <div class="toast-notification" style="
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 9999;
        min-width: 300px;
        max-width: 500px;
        animation: slideInRight 0.3s ease-out;
        border-left: 4px solid ${color};
      ">
        <i class="fas ${icon}" style="color: ${color}; font-size: 1.25rem;"></i>
        <span style="flex: 1; color: #0F172A; font-weight: 500;">${message}</span>
        <button onclick="$(this).parent().remove()" style="
          background: none;
          border: none;
          color: #64748B;
          cursor: pointer;
          font-size: 1.25rem;
          padding: 0;
          line-height: 1;
        ">&times;</button>
      </div>
    `);
    
    $('body').append(toast);
    
    // Auto-remove after 5 seconds
    setTimeout(function() {
      toast.fadeOut(300, function() {
        $(this).remove();
      });
    }, 5000);
  }
  
  // Loading overlay functions
  function showLoadingOverlay(message = 'Processing...') {
    if ($('#loadingOverlay').length === 0) {
      const overlay = $(`
        <div id="loadingOverlay" style="
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0, 0, 0, 0.5);
          display: flex;
          align-items: center;
          justify-content: center;
          z-index: 9998;
        ">
          <div style="
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
          ">
            <i class="fas fa-spinner fa-spin" style="font-size: 3rem; color: #3B82F6; margin-bottom: 16px;"></i>
            <div style="font-size: 1.125rem; font-weight: 600; color: #0F172A;">${message}</div>
            <div style="font-size: 0.875rem; color: #64748B; margin-top: 8px;">Please wait...</div>
          </div>
        </div>
      `);
      $('body').append(overlay);
    }
  }
  
  function hideLoadingOverlay() {
    $('#loadingOverlay').fadeOut(300, function() {
      $(this).remove();
    });
  }
  
  // Add CSS animation for toast
  if (!$('#toastAnimations').length) {
    $('head').append(`
      <style id="toastAnimations">
        @keyframes slideInRight {
          from {
            transform: translateX(100%);
            opacity: 0;
          }
          to {
            transform: translateX(0);
            opacity: 1;
          }
        }
      </style>
    `);
  }
</script>
@endpush
