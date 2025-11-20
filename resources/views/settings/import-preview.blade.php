@extends('layouts.app')

@php($pageTitle = 'Import Settings - Preview Changes')

@push('styles')
<style>
  /* Preview Card Styling - Matches Products Module */
  .preview-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #E5E7EB;
  }
  
  /* Change Group Styling */
  .change-group {
    margin-bottom: 24px;
    padding-bottom: 24px;
    border-bottom: 1px solid #E5E7EB;
  }
  .change-group:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
  }
  .group-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #0F172A;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
  }
  .change-badge {
    background: #3B82F6;
    color: white;
    font-size: 0.75rem;
    padding: 2px 8px;
    border-radius: 12px;
    margin-left: 8px;
    font-weight: 600;
  }
  
  /* Change Item Styling */
  .change-item {
    background: #F8FAFC;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 8px;
    border-left: 3px solid #3B82F6;
    transition: background-color 0.2s;
  }
  .change-item:hover {
    background: #F1F5F9;
  }
  .change-key {
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
    margin-bottom: 4px;
  }
  
  /* Value Comparison Styling */
  .change-values {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 0.875rem;
  }
  .value-box {
    flex: 1;
    padding: 8px 12px;
    border-radius: 6px;
    font-family: 'Courier New', monospace;
  }
  .current-value {
    background: #FEE2E2;
    color: #991B1B;
    border: 1px solid #FCA5A5;
  }
  .new-value {
    background: #D1FAE5;
    color: #065F46;
    border: 1px solid #6EE7B7;
  }
  .value-arrow {
    color: #6B7280;
    font-size: 1.25rem;
  }
  
  /* No Changes State */
  .no-changes-card {
    background: #F0FDF4;
    border: 1px solid #86EFAC;
    border-radius: 8px;
    padding: 24px;
    text-align: center;
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
  .btn-danger {
    background-color: #DC2626;
    border-color: #DC2626;
  }
  .btn-danger:hover {
    background-color: #B91C1C;
    border-color: #B91C1C;
  }
  
  /* Custom Checkbox Styling */
  .custom-control-input:checked ~ .custom-control-label::before {
    background-color: #3B82F6;
    border-color: #3B82F6;
  }
  .custom-control-input:focus ~ .custom-control-label::before {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }
  
  /* Responsive Design for Mobile */
  @media (max-width: 767px) {
    .preview-card {
      padding: 16px;
    }
    .change-values {
      flex-direction: column;
      gap: 8px;
    }
    .value-arrow {
      transform: rotate(90deg);
    }
    h1 {
      font-size: 1.5rem !important;
    }
    .d-flex.justify-content-between {
      flex-direction: column;
      gap: 0.5rem;
    }
    .d-flex.justify-content-between .btn {
      width: 100%;
    }
  }
</style>
@endpush

@section('content')

<!-- Breadcrumb Navigation -->
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('settings.index') }}"><i class="fas fa-cog mr-1"></i>Settings</a></li>
    <li class="breadcrumb-item active" aria-current="page">Import Preview</li>
  </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="mb-1" style="font-size: 1.875rem; font-weight: 700; color: #0F172A;">
      <i class="fas fa-file-import mr-2" style="color: #3B82F6;"></i>
      Import Settings Preview
    </h1>
    <p class="text-muted mb-0">Review changes before applying them to your system</p>
  </div>
</div>

<!-- Warning Alert -->
<div class="alert alert-warning">
  <i class="fas fa-exclamation-triangle mr-2"></i>
  <strong>Review Carefully:</strong> The following changes will be applied to your settings. 
  Make sure you have a backup before proceeding.
</div>

<!-- Preview Content -->
<div class="preview-card">
  @if(empty($changes))
    <!-- No Changes -->
    <div class="no-changes-card">
      <i class="fas fa-check-circle" style="font-size: 3rem; color: #10B981; margin-bottom: 16px;"></i>
      <h4 style="color: #065F46; margin-bottom: 8px;">No Changes Detected</h4>
      <p class="text-muted mb-0">The imported settings are identical to your current configuration.</p>
    </div>
  @else
    <!-- Summary -->
    <div class="alert alert-info mb-4">
      <i class="fas fa-info-circle mr-2"></i>
      <strong>Summary:</strong> {{ count($changes) }} setting group(s) will be updated with 
      {{ collect($changes)->sum(fn($group) => count($group['changes'])) }} total change(s).
    </div>

    <!-- Changes by Group -->
    @foreach($changes as $groupKey => $groupData)
      <div class="change-group">
        <div class="group-title">
          <i class="fas fa-folder-open mr-2" style="color: #3B82F6;"></i>
          {{ $groupData['title'] }}
          <span class="change-badge">{{ count($groupData['changes']) }} change(s)</span>
        </div>

        @foreach($groupData['changes'] as $change)
          <div class="change-item">
            <div class="change-key">
              <i class="fas fa-cog mr-1" style="color: #6B7280; font-size: 0.75rem;"></i>
              {{ $change['key'] }}
            </div>
            <div class="change-values">
              <div class="value-box current-value">
                <small class="d-block text-muted mb-1">Current:</small>
                <strong>{{ is_bool($change['current']) ? ($change['current'] ? 'true' : 'false') : ($change['current'] ?? 'null') }}</strong>
              </div>
              <div class="value-arrow">
                <i class="fas fa-arrow-right"></i>
              </div>
              <div class="value-box new-value">
                <small class="d-block text-muted mb-1">New:</small>
                <strong>{{ is_bool($change['new']) ? ($change['new'] ? 'true' : 'false') : ($change['new'] ?? 'null') }}</strong>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endforeach
  @endif

  <!-- Action Buttons -->
  <div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top: 1px solid #E5E7EB;">
    <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary">
      <i class="fas fa-times mr-1"></i> Cancel Import
    </a>
    
    @if(!empty($changes))
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmImportModal">
        <i class="fas fa-check mr-1"></i> Apply Changes
      </button>
    @else
      <a href="{{ route('settings.index') }}" class="btn btn-primary">
        <i class="fas fa-arrow-left mr-1"></i> Back to Settings
      </a>
    @endif
  </div>
</div>

<!-- Confirmation Modal -->
@if(!empty($changes))
<div class="modal fade" id="confirmImportModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius: 12px; border: none;">
      <div class="modal-header" style="border-bottom: 1px solid #E5E7EB;">
        <h5 class="modal-title" style="font-weight: 600; color: #0F172A;">
          <i class="fas fa-exclamation-triangle mr-2" style="color: #F59E0B;"></i>
          Confirm Import
        </h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form method="POST" action="{{ route('settings.import') }}" id="confirmImportForm">
        @csrf
        <input type="hidden" name="confirmed" value="true">
        <input type="hidden" name="import_data" value="{{ $importData }}">
        
        <div class="modal-body">
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <strong>Warning:</strong> This action will overwrite your current settings and cannot be undone.
          </div>
          
          <p class="mb-3">You are about to apply <strong>{{ collect($changes)->sum(fn($group) => count($group['changes'])) }} change(s)</strong> across <strong>{{ count($changes) }} setting group(s)</strong>.</p>
          
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="finalConfirm" required>
            <label class="custom-control-label" for="finalConfirm">
              I have reviewed the changes and want to proceed with the import
            </label>
          </div>
        </div>
        <div class="modal-footer" style="border-top: 1px solid #E5E7EB;">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger" id="confirmImportBtn">
            <i class="fas fa-upload mr-1"></i>Apply Import
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

@endsection

@push('scripts')
<script>
  $(document).ready(function() {
    // Disable submit button until checkbox is checked
    $('#finalConfirm').on('change', function() {
      $('#confirmImportBtn').prop('disabled', !this.checked);
    });
    
    // Initial state - disable button
    $('#confirmImportBtn').prop('disabled', true);
    
    // Show confirmation modal with focus on cancel for safety
    $('#confirmImportModal').on('show.bs.modal', function() {
      setTimeout(function() {
        $('#confirmImportModal .btn-outline-secondary').focus();
      }, 500);
    });
    
    // Show loading state on form submit
    $('#confirmImportForm').on('submit', function(e) {
      const btn = $('#confirmImportBtn');
      const checkbox = $('#finalConfirm');
      
      // Validate checkbox is checked
      if (!checkbox.is(':checked')) {
        e.preventDefault();
        showToast('error', 'Please confirm that you have reviewed the changes.');
        return false;
      }
      
      // Disable submit button and show loading state
      btn.prop('disabled', true);
      btn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Applying Changes...');
      
      // Disable all form inputs
      $(this).find('input, button').not(btn).prop('disabled', true);
      
      // Close modal
      $('#confirmImportModal').modal('hide');
      
      // Show loading overlay
      showLoadingOverlay('Importing settings...', 'This may take a few moments. Please do not close this window.');
    });
    
    // Cancel button confirmation
    $('a[href*="settings.index"]').on('click', function(e) {
      @if(!empty($changes))
        if (!confirm('Are you sure you want to cancel? The import will not be applied.')) {
          e.preventDefault();
          return false;
        }
      @endif
    });
    
    // Highlight changes on hover
    $('.change-item').on('mouseenter', function() {
      $(this).css('border-left-width', '4px');
    }).on('mouseleave', function() {
      $(this).css('border-left-width', '3px');
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
  function showLoadingOverlay(title = 'Processing...', message = 'Please wait...') {
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
            max-width: 400px;
          ">
            <i class="fas fa-spinner fa-spin" style="font-size: 3rem; color: #3B82F6; margin-bottom: 16px;"></i>
            <div style="font-size: 1.125rem; font-weight: 600; color: #0F172A; margin-bottom: 8px;">${title}</div>
            <div style="font-size: 0.875rem; color: #64748B;">${message}</div>
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
