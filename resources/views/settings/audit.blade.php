@extends('layouts.app')

@php($pageTitle = 'Settings Audit Log')

@push('styles')
<style>
  /* Audit Table Styling - Matches Products Module */
  .audit-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #E5E7EB;
  }
  .audit-table thead {
    background: #F8FAFC;
  }
  .audit-table th {
    font-weight: 600;
    color: #0F172A;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    padding: 16px;
    border-bottom: 2px solid #E5E7EB;
  }
  .audit-table td {
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid #F3F4F6;
  }
  .audit-table tbody tr:hover {
    background: #F8FAFC;
    transition: background-color 0.2s;
  }
  
  /* Value Badge Styling */
  .value-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 0.875rem;
    font-family: 'Courier New', monospace;
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .value-old {
    background: #FEE2E2;
    color: #991B1B;
  }
  .value-new {
    background: #D1FAE5;
    color: #065F46;
  }
  
  /* Filter Card */
  .filter-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #E5E7EB;
    margin-bottom: 24px;
  }
  
  /* Breadcrumb Navigation */
  .breadcrumb-custom {
    background: transparent;
    padding: 0;
    margin-bottom: 24px;
  }
  .breadcrumb-custom .breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #9CA3AF;
  }
  .breadcrumb-custom a {
    color: #3B82F6;
    text-decoration: none;
  }
  .breadcrumb-custom a:hover {
    color: #2563EB;
    text-decoration: underline;
  }
  
  /* Empty State */
  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #64748B;
  }
  .empty-state i {
    font-size: 4rem;
    color: #CBD5E1;
    margin-bottom: 16px;
  }
  
  /* Form Controls */
  .form-control:focus {
    border-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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
  
  /* Pagination Styling */
  .pagination {
    margin-bottom: 0;
  }
  .page-link {
    color: #3B82F6;
    border: 1px solid #E5E7EB;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
  }
  .page-link:hover {
    background-color: #F8FAFC;
    color: #2563EB;
  }
  .page-item.active .page-link {
    background-color: #3B82F6;
    border-color: #3B82F6;
    color: white;
  }
  .page-item.disabled .page-link {
    color: #9CA3AF;
    background-color: #F9FAFB;
  }
  
  /* Responsive Design for Mobile */
  @media (max-width: 767px) {
    .filter-card {
      padding: 16px;
    }
    .audit-table th,
    .audit-table td {
      padding: 12px 8px;
      font-size: 0.8rem;
    }
    .value-badge {
      max-width: 100px;
      font-size: 0.75rem;
    }
    h1 {
      font-size: 1.5rem !important;
    }
    .d-flex.justify-content-between {
      flex-direction: column;
      gap: 1rem;
    }
    .d-flex.justify-content-between .btn {
      width: 100%;
    }
  }
  
  /* Code Styling */
  code {
    font-size: 0.875rem;
    color: #6366F1;
    background: #EEF2FF;
    padding: 4px 8px;
    border-radius: 4px;
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
  
  /* Badge Styling */
  .badge {
    border-radius: 6px;
    padding: 4px 10px;
    font-weight: 600;
  }
  .badge-info {
    background-color: #3B82F6;
    color: white;
  }
</style>
@endpush

@section('content')

<!-- Breadcrumb -->
<nav aria-label="breadcrumb">
  <ol class="breadcrumb breadcrumb-custom">
    <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Settings</a></li>
    <li class="breadcrumb-item active">Audit Log</li>
  </ol>
</nav>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="mb-1" style="font-size: 1.875rem; font-weight: 700; color: #0F172A;">
      <i class="fas fa-history mr-2" style="color: #3B82F6;"></i>
      Settings Audit Log
    </h1>
    <p class="text-muted mb-0">Track all changes made to system settings</p>
  </div>
  <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary">
    <i class="fas fa-arrow-left mr-2"></i>Back to Settings
  </a>
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

<!-- Filters Card -->
<div class="filter-card">
  <h5 class="mb-3" style="font-weight: 600; color: #0F172A;">
    <i class="fas fa-filter mr-2" style="color: #6366F1;"></i>
    Filter Audit Log
  </h5>
  
  <form method="GET" action="{{ route('settings.audit') }}" id="filterForm">
    <div class="row">
      <div class="col-md-3">
        <div class="form-group">
          <label for="date_from" style="font-size: 0.875rem; font-weight: 600; color: #374151;">From Date</label>
          <input 
            type="date" 
            class="form-control" 
            id="date_from" 
            name="date_from" 
            value="{{ $filters['date_from'] ?? '' }}"
          >
        </div>
      </div>
      
      <div class="col-md-3">
        <div class="form-group">
          <label for="date_to" style="font-size: 0.875rem; font-weight: 600; color: #374151;">To Date</label>
          <input 
            type="date" 
            class="form-control" 
            id="date_to" 
            name="date_to" 
            value="{{ $filters['date_to'] ?? '' }}"
          >
        </div>
      </div>
      
      <div class="col-md-3">
        <div class="form-group">
          <label for="setting_key" style="font-size: 0.875rem; font-weight: 600; color: #374151;">Setting Key</label>
          <input 
            type="text" 
            class="form-control" 
            id="setting_key" 
            name="setting_key" 
            placeholder="e.g., loan.max_term_months"
            value="{{ $filters['setting_key'] ?? '' }}"
          >
        </div>
      </div>
      
      <div class="col-md-3">
        <div class="form-group">
          <label for="user" style="font-size: 0.875rem; font-weight: 600; color: #374151;">User</label>
          <input 
            type="text" 
            class="form-control" 
            id="user" 
            name="user" 
            placeholder="Username"
            value="{{ $filters['user'] ?? '' }}"
          >
        </div>
      </div>
    </div>
    
    <div class="d-flex justify-content-end">
      <a href="{{ route('settings.audit') }}" class="btn btn-outline-secondary mr-2">
        <i class="fas fa-times mr-1"></i>Clear Filters
      </a>
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-search mr-1"></i>Apply Filters
      </button>
    </div>
  </form>
</div>

<!-- Results Summary -->
@if($entries->total() > 0)
  <div class="mb-3">
    <p class="text-muted mb-0">
      Showing {{ $entries->firstItem() }} to {{ $entries->lastItem() }} of {{ $entries->total() }} entries
      @if($filters['date_from'] || $filters['date_to'] || $filters['setting_key'] || $filters['user'])
        <span class="badge badge-info ml-2">Filtered</span>
      @endif
    </p>
  </div>
@endif

<!-- Audit Log Table -->
<div class="audit-table">
  @if($entries->count() > 0)
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th style="width: 180px;">Timestamp</th>
          <th style="width: 120px;">User</th>
          <th style="width: 200px;">Setting Key</th>
          <th>Old Value</th>
          <th>New Value</th>
          <th style="width: 120px;">IP Address</th>
        </tr>
      </thead>
      <tbody>
        @foreach($entries as $entry)
          <tr>
            <td>
              <div style="font-size: 0.875rem; color: #0F172A; font-weight: 500;">
                {{ \Carbon\Carbon::parse($entry['timestamp'])->format('M d, Y') }}
              </div>
              <div style="font-size: 0.75rem; color: #64748B;">
                {{ \Carbon\Carbon::parse($entry['timestamp'])->format('h:i A') }}
              </div>
            </td>
            <td>
              <div style="font-size: 0.875rem; color: #0F172A; font-weight: 500;">
                {{ $entry['user'] }}
              </div>
              @if(isset($entry['user_id']))
                <div style="font-size: 0.75rem; color: #64748B;">
                  ID: {{ $entry['user_id'] }}
                </div>
              @endif
            </td>
            <td>
              <code style="font-size: 0.875rem; color: #6366F1; background: #EEF2FF; padding: 4px 8px; border-radius: 4px;">
                {{ $entry['setting_key'] }}
              </code>
            </td>
            <td>
              @if($entry['old_value'] === null)
                <span class="text-muted" style="font-style: italic; font-size: 0.875rem;">null</span>
              @elseif(is_bool($entry['old_value']))
                <span class="value-badge value-old">
                  {{ $entry['old_value'] ? 'true' : 'false' }}
                </span>
              @else
                <span class="value-badge value-old" title="{{ $entry['old_value'] }}">
                  {{ $entry['old_value'] }}
                </span>
              @endif
            </td>
            <td>
              @if($entry['new_value'] === null)
                <span class="text-muted" style="font-style: italic; font-size: 0.875rem;">null</span>
              @elseif(is_bool($entry['new_value']))
                <span class="value-badge value-new">
                  {{ $entry['new_value'] ? 'true' : 'false' }}
                </span>
              @else
                <span class="value-badge value-new" title="{{ $entry['new_value'] }}">
                  {{ $entry['new_value'] }}
                </span>
              @endif
            </td>
            <td>
              <span style="font-size: 0.875rem; color: #64748B; font-family: 'Courier New', monospace;">
                {{ $entry['ip_address'] ?? 'N/A' }}
              </span>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <div class="empty-state">
      <i class="fas fa-inbox"></i>
      <h5 style="color: #374151; font-weight: 600; margin-bottom: 8px;">No Audit Entries Found</h5>
      <p class="mb-0">
        @if($filters['date_from'] || $filters['date_to'] || $filters['setting_key'] || $filters['user'])
          No entries match your filter criteria. Try adjusting your filters.
        @else
          No settings changes have been recorded yet.
        @endif
      </p>
    </div>
  @endif
</div>

<!-- Pagination -->
@if($entries->hasPages())
  <div class="d-flex justify-content-center mt-4">
    {{ $entries->links() }}
  </div>
@endif

<!-- Info Card -->
<div class="card mt-4" style="border-left: 4px solid #3B82F6;">
  <div class="card-body">
    <h6 style="font-weight: 600; color: #0F172A; margin-bottom: 12px;">
      <i class="fas fa-info-circle mr-2" style="color: #3B82F6;"></i>
      About Audit Log
    </h6>
    <ul class="mb-0" style="font-size: 0.875rem; color: #64748B;">
      <li>All changes to system settings are automatically logged</li>
      <li>Audit entries include timestamp, user, setting key, old value, and new value</li>
      <li>The system retains the last 1,000 audit entries</li>
      <li>Use filters to narrow down specific changes or time periods</li>
      <li>Audit logs cannot be modified or deleted to maintain integrity</li>
    </ul>
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
    
    // Set max date for date inputs to today
    const today = new Date().toISOString().split('T')[0];
    $('#date_from, #date_to').attr('max', today);
    
    // Validate date range and show loading state
    $('#filterForm').on('submit', function(e) {
      const dateFrom = $('#date_from').val();
      const dateTo = $('#date_to').val();
      const submitBtn = $(this).find('button[type="submit"]');
      
      if (dateFrom && dateTo && dateFrom > dateTo) {
        e.preventDefault();
        showToast('error', 'From Date cannot be later than To Date');
        return false;
      }
      
      // Disable submit button and show loading state
      submitBtn.prop('disabled', true);
      submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Filtering...');
      
      // Disable all form inputs
      $(this).find('input, button').not(submitBtn).prop('disabled', true);
      
      // Show loading overlay
      showLoadingOverlay('Filtering audit log...', 'Searching through entries...');
    });
    
    // Clear filters button with confirmation
    $('a[href*="settings.audit"]:contains("Clear Filters")').on('click', function(e) {
      const hasFilters = $('#date_from').val() || $('#date_to').val() || $('#setting_key').val() || $('#user').val();
      
      if (hasFilters) {
        if (!confirm('Are you sure you want to clear all filters?')) {
          e.preventDefault();
          return false;
        }
        
        // Show loading state
        const btn = $(this);
        const originalHtml = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Clearing...');
        
        showLoadingOverlay('Clearing filters...', 'Loading all entries...');
      }
    });
    
    // Pagination links with loading state
    $('.pagination a').on('click', function(e) {
      const btn = $(this);
      if (!btn.parent().hasClass('disabled') && !btn.parent().hasClass('active')) {
        btn.html('<i class="fas fa-spinner fa-spin"></i>');
        showLoadingOverlay('Loading page...', 'Please wait...');
      }
    });
    
    // Add tooltip to truncated values
    $('.value-badge').each(function() {
      const $this = $(this);
      if (this.offsetWidth < this.scrollWidth) {
        $this.attr('data-toggle', 'tooltip');
        $this.attr('data-placement', 'top');
      }
    });
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Highlight row on hover
    $('.audit-table tbody tr').on('mouseenter', function() {
      $(this).css('background-color', '#F8FAFC');
    }).on('mouseleave', function() {
      $(this).css('background-color', '');
    });
    
    // Real-time filter validation
    $('#date_from').on('change', function() {
      const dateTo = $('#date_to').val();
      if (dateTo && $(this).val() > dateTo) {
        showToast('warning', 'From Date should not be later than To Date');
        $(this).addClass('is-invalid');
      } else {
        $(this).removeClass('is-invalid');
      }
    });
    
    $('#date_to').on('change', function() {
      const dateFrom = $('#date_from').val();
      if (dateFrom && $(this).val() < dateFrom) {
        showToast('warning', 'To Date should not be earlier than From Date');
        $(this).addClass('is-invalid');
      } else {
        $(this).removeClass('is-invalid');
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
        .is-invalid {
          border-color: #DC2626 !important;
        }
      </style>
    `);
  }
</script>
@endpush
