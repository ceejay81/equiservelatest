@extends('layouts.app')

@php($pageTitle = 'Settings - ' . $groupInfo['title'])

@push('styles')
<style>
  /* Form Card Styling - Matches Products Module */
  .settings-form-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #E5E7EB;
  }
  
  /* Form Section Title */
  .form-section-title {
    font-size: 1rem;
    font-weight: 600;
    color: #0F172A;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 2px solid #E5E7EB;
  }
  
  /* Form Labels */
  .form-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px;
  }
  
  /* Form Controls - Enhanced Focus State */
  .form-control:focus,
  .custom-select:focus {
    border-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }
  
  /* Custom Switch Styling */
  .custom-control-input:checked ~ .custom-control-label::before {
    background-color: #3B82F6;
    border-color: #3B82F6;
  }
  .custom-control-input:focus ~ .custom-control-label::before {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }
  
  /* Breadcrumb Navigation */
  .breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 1rem;
  }
  .breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #9CA3AF;
  }
  .breadcrumb-item a {
    color: #3B82F6;
    text-decoration: none;
  }
  .breadcrumb-item a:hover {
    color: #2563EB;
    text-decoration: underline;
  }
  .breadcrumb-item.active {
    color: #6B7280;
  }
  
  /* Last Modified Info */
  .last-modified {
    font-size: 0.875rem;
    color: #64748B;
    padding: 12px;
    background: #F8FAFC;
    border-radius: 8px;
    margin-top: 16px;
  }
  
  /* Button Styling */
  .btn-primary {
    background-color: #3B82F6;
    border-color: #3B82F6;
  }
  .btn-primary:hover {
    background-color: #2563EB;
    border-color: #2563EB;
  }
  .btn-outline-secondary:hover {
    background-color: #F8FAFC;
  }
  .btn-outline-danger:hover {
    background-color: #FEE2E2;
    color: #DC2626;
  }
  
  /* Responsive Design for Mobile */
  @media (max-width: 767px) {
    .settings-form-card {
      padding: 16px;
    }
    .d-flex[style*="gap"] {
      flex-direction: column;
      gap: 0.5rem !important;
    }
    .d-flex[style*="gap"] .btn {
      width: 100%;
    }
    h1 {
      font-size: 1.5rem !important;
    }
    .d-flex.justify-content-between {
      flex-direction: column;
      align-items: flex-start !important;
      gap: 1rem;
    }
  }
  
  /* Input Group Styling */
  .input-group-text {
    background-color: #F8FAFC;
    border-color: #E5E7EB;
    color: #64748B;
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
  
  /* Form Text Styling */
  .form-text {
    font-size: 0.8125rem;
    color: #64748B;
  }
  
  /* Invalid Feedback */
  .invalid-feedback {
    font-size: 0.8125rem;
    color: #DC2626;
  }
  .is-invalid {
    border-color: #DC2626 !important;
  }
  .is-invalid:focus {
    border-color: #DC2626 !important;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
  }
</style>
@endpush

@section('content')

<!-- Breadcrumb Navigation -->
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('settings.index') }}"><i class="fas fa-cog mr-1"></i>Settings</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $groupInfo['title'] }}</li>
  </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="mb-1" style="font-size: 1.875rem; font-weight: 700; color: #0F172A;">
      <i class="fas fa-{{ $groupInfo['icon'] }} mr-2" style="color: #3B82F6;"></i>
      {{ $groupInfo['title'] }}
    </h1>
    <p class="text-muted mb-0">{{ $groupInfo['description'] }}</p>
  </div>
  <div class="d-flex" style="gap: 0.5rem;">
    <a href="{{ route('settings.export') }}?group={{ $group }}" class="btn btn-outline-secondary">
      <i class="fas fa-file-export mr-1"></i> Export
    </a>
    <button class="btn btn-outline-danger" data-toggle="modal" data-target="#resetModal">
      <i class="fas fa-undo mr-1"></i> Reset to Defaults
    </button>
  </div>
</div>

<!-- Success/Error Messages -->
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

@if($errors->any())
  <div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle mr-2"></i>
    <strong>Please fix the following errors:</strong>
    <ul class="mb-0 mt-2">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
@endif

<!-- Settings Form -->
<div class="settings-form-card">
  <form method="POST" action="{{ route('settings.update', $group) }}" id="settingsForm">
    @csrf
    @method('PUT')
    
    <!-- Include group-specific partial -->
    @include('settings.partials.' . $group)
    
    <!-- Form Actions -->
    <div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top: 1px solid #E5E7EB;">
      <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Back to Settings
      </a>
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-save mr-1"></i> Save Changes
      </button>
    </div>
  </form>
  
  <!-- Last Modified Info -->
  @if(isset($metadata['last_modified']) && isset($metadata['modified_by']))
    <div class="last-modified">
      <i class="fas fa-clock mr-2"></i>
      <strong>Last modified:</strong> {{ $metadata['last_modified'] }} by {{ $metadata['modified_by'] }}
    </div>
  @endif
</div>

<!-- Reset Confirmation Modal -->
<div class="modal fade" id="resetModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius: 12px; border: none;">
      <div class="modal-header" style="border-bottom: 1px solid #E5E7EB;">
        <h5 class="modal-title" style="font-weight: 600; color: #0F172A;">
          <i class="fas fa-exclamation-triangle mr-2" style="color: #EF4444;"></i>
          Reset to Defaults
        </h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle mr-2"></i>
          <strong>Warning:</strong> This action will reset all settings in this group to their default values.
        </div>
        <p class="mb-0">Are you sure you want to reset <strong>{{ $groupInfo['title'] }}</strong> to default values? This action cannot be undone.</p>
      </div>
      <div class="modal-footer" style="border-top: 1px solid #E5E7EB;">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
        <form method="POST" action="{{ route('settings.reset', $group) }}" style="display: inline;">
          @csrf
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-undo mr-1"></i>Reset to Defaults
          </button>
        </form>
      </div>
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
    
    // Form validation and submission handling
    $('#settingsForm').on('submit', function(e) {
      const form = $(this);
      const submitBtn = form.find('button[type="submit"]');
      
      // Check HTML5 validation first
      if (!this.checkValidity()) {
        e.preventDefault();
        this.reportValidity();
        showToast('error', 'Please fill in all required fields correctly.');
        return false;
      }
      
      // Disable submit button and show loading state
      submitBtn.prop('disabled', true);
      submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
      
      // Disable all form inputs to prevent changes during save
      form.find('input, select, textarea, button').not(submitBtn).prop('disabled', true);
      
      // Show loading overlay
      showLoadingOverlay();
      
      // Re-enable after 10 seconds as fallback (in case of error)
      setTimeout(function() {
        submitBtn.prop('disabled', false);
        submitBtn.html('<i class="fas fa-save mr-1"></i> Save Changes');
        form.find('input, select, textarea, button').prop('disabled', false);
        hideLoadingOverlay();
      }, 10000);
    });
    
    // Reset form confirmation with enhanced modal
    $('#resetModal').on('show.bs.modal', function() {
      // Focus on cancel button by default for safety
      setTimeout(function() {
        $('#resetModal .btn-outline-secondary').focus();
      }, 500);
    });
    
    // Reset form submission with loading state
    $('#resetModal form').on('submit', function(e) {
      const submitBtn = $(this).find('button[type="submit"]');
      
      // Disable submit button and show loading state
      submitBtn.prop('disabled', true);
      submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Resetting...');
      
      // Show loading overlay
      showLoadingOverlay();
      
      // Close modal
      $('#resetModal').modal('hide');
    });
    
    // Add validation feedback for required fields
    $('input[required], select[required], textarea[required]').on('blur', function() {
      if (!this.validity.valid) {
        $(this).addClass('is-invalid');
      } else {
        $(this).removeClass('is-invalid');
      }
    });
    
    // Remove invalid class on input
    $('input, select, textarea').on('input change', function() {
      if (this.validity.valid) {
        $(this).removeClass('is-invalid');
      }
    });
    
    // Export button loading state
    $('a[href*="export"]').on('click', function(e) {
      const btn = $(this);
      const originalHtml = btn.html();
      
      btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Exporting...');
      btn.addClass('disabled');
      
      // Reset after 3 seconds
      setTimeout(function() {
        btn.html(originalHtml);
        btn.removeClass('disabled');
        showToast('success', 'Settings exported successfully');
      }, 3000);
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
  function showLoadingOverlay() {
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
            <div style="font-size: 1.125rem; font-weight: 600; color: #0F172A;">Processing...</div>
            <div style="font-size: 0.875rem; color: #64748B; margin-top: 8px;">Please wait while we save your changes</div>
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
