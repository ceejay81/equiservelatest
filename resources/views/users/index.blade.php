@extends('layouts.app')

@php($pageTitle = 'Users')

@section('content')

<style>
  /* Modern Users Index Styles */
  :root {
    --primary-blue: #3B82F6;
    --primary-dark: #2563EB;
    --purple: #8B5CF6;
    --green: #10B981;
    --orange: #F59E0B;
    --red: #EF4444;
    --gray-50: #F9FAFB;
    --gray-100: #F3F4F6;
    --gray-200: #E5E7EB;
    --gray-300: #D1D5DB;
    --gray-600: #4B5563;
    --gray-700: #374151;
    --gray-900: #111827;
  }

  .users-container {
    max-width: 1400px;
    margin: 0 auto;
  }

  /* Hero Header */
  .users-hero {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--purple) 100%);
    border-radius: 20px;
    padding: 40px;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(59,130,246,.25);
  }

  .users-hero::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 70%);
    border-radius: 50%;
    transform: translate(30%, -30%);
  }

  .users-hero-content {
    position: relative;
    z-index: 1;
  }

  .users-hero h1 {
    font-size: 36px;
    font-weight: 800;
    color: white;
    margin: 0 0 12px 0;
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .users-hero-icon {
    width: 56px;
    height: 56px;
    background: rgba(255,255,255,.2);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
  }

  .users-hero p {
    font-size: 16px;
    color: rgba(255,255,255,.95);
    margin: 0 0 24px 0;
    max-width: 600px;
  }

  .users-hero-stats {
    display: flex;
    gap: 32px;
    flex-wrap: wrap;
  }

  .hero-stat {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .hero-stat-icon {
    width: 48px;
    height: 48px;
    background: rgba(255,255,255,.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
  }

  .hero-stat-content h3 {
    font-size: 28px;
    font-weight: 700;
    color: white;
    margin: 0;
    line-height: 1;
  }

  .hero-stat-content p {
    font-size: 13px;
    color: rgba(255,255,255,.85);
    margin: 4px 0 0 0;
  }

  /* Toolbar */
  .users-toolbar {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
  }

  .toolbar-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
  }

  .toolbar-left {
    display: flex;
    gap: 12px;
    flex: 1;
    min-width: 300px;
  }

  .toolbar-right {
    display: flex;
    gap: 12px;
  }

  /* Search Box */
  .search-box {
    position: relative;
    flex: 1;
    max-width: 400px;
  }

  .search-box input {
    width: 100%;
    padding: 12px 16px 12px 44px;
    border: 2px solid var(--gray-200);
    border-radius: 12px;
    font-size: 14px;
    transition: all 0.2s;
  }

  .search-box input:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 4px rgba(59,130,246,.1);
  }

  .search-box i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-600);
  }

  /* Filter Select */
  .filter-select {
    padding: 12px 16px;
    border: 2px solid var(--gray-200);
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
    min-width: 150px;
  }

  .filter-select:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 4px rgba(59,130,246,.1);
  }

  /* View Toggle */
  .view-toggle {
    display: flex;
    background: var(--gray-100);
    border-radius: 10px;
    padding: 4px;
  }

  .view-toggle-btn {
    padding: 8px 16px;
    border: none;
    background: transparent;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    color: var(--gray-600);
    font-size: 16px;
  }

  .view-toggle-btn.active {
    background: white;
    color: var(--primary-blue);
    box-shadow: 0 2px 4px rgba(0,0,0,.1);
  }

  /* Action Buttons */
  .btn-modern {
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-primary-modern {
    background: var(--primary-blue);
    color: white;
    box-shadow: 0 4px 12px rgba(59,130,246,.3);
  }

  .btn-primary-modern:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(59,130,246,.4);
  }

  .btn-secondary-modern {
    background: white;
    color: var(--gray-700);
    border: 2px solid var(--gray-200);
  }

  .btn-secondary-modern:hover {
    border-color: var(--gray-300);
    background: var(--gray-50);
  }

  /* Grid View */
  .users-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
  }

  .user-grid-card {
    background: white;
    border: 2px solid var(--gray-200);
    border-radius: 16px;
    padding: 24px;
    transition: all 0.3s;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }

  .user-grid-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-blue), var(--purple));
    transform: scaleX(0);
    transition: transform 0.3s;
  }

  .user-grid-card:hover {
    border-color: var(--primary-blue);
    box-shadow: 0 8px 24px rgba(59,130,246,.15);
    transform: translateY(-4px);
  }

  .user-grid-card:hover::before {
    transform: scaleX(1);
  }

  .user-grid-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 16px;
  }

  .user-grid-avatar {
    width: 64px;
    height: 64px;
    border-radius: 14px;
    object-fit: cover;
    border: 3px solid var(--gray-100);
  }

  .user-grid-info h3 {
    font-size: 16px;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0 0 4px 0;
  }

  .user-grid-info p {
    font-size: 13px;
    color: var(--gray-600);
    margin: 0;
  }

  .user-grid-badges {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
  }

  .user-grid-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    padding-top: 16px;
    border-top: 1px solid var(--gray-200);
  }

  .user-grid-meta-item {
    text-align: center;
  }

  .user-grid-meta-label {
    font-size: 11px;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    margin-bottom: 4px;
  }

  .user-grid-meta-value {
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-900);
  }

  /* Table View */
  .users-table-wrapper {
    background: white;
    border: 2px solid var(--gray-200);
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 32px;
  }

  .users-table {
    width: 100%;
    border-collapse: collapse;
  }

  .users-table thead {
    background: var(--gray-50);
  }

  .users-table th {
    padding: 16px 20px;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    color: var(--gray-700);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid var(--gray-200);
  }

  .users-table td {
    padding: 16px 20px;
    border-bottom: 1px solid var(--gray-200);
  }

  .users-table tbody tr {
    transition: all 0.2s;
    cursor: pointer;
  }

  .users-table tbody tr:hover {
    background: var(--gray-50);
  }

  .table-user-cell {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .table-avatar {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    object-fit: cover;
    border: 2px solid var(--gray-100);
  }

  .table-user-info h4 {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0 0 2px 0;
  }

  .table-user-info p {
    font-size: 12px;
    color: var(--gray-600);
    margin: 0;
  }

  /* Badges */
  .badge-modern {
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-block;
  }

  .badge-admin {
    background: rgba(139,92,246,.15);
    color: var(--purple);
  }

  .badge-manager {
    background: rgba(59,130,246,.15);
    color: var(--primary-blue);
  }

  .badge-staff {
    background: rgba(100,116,139,.15);
    color: var(--gray-600);
  }

  .badge-active {
    background: rgba(16,185,129,.15);
    color: var(--green);
  }

  .badge-inactive {
    background: rgba(239,68,68,.15);
    color: var(--red);
  }

  /* Action Menu */
  .action-menu {
    position: relative;
  }

  .action-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: none;
    background: var(--gray-100);
    color: var(--gray-600);
    cursor: pointer;
    transition: all 0.2s;
  }

  .action-btn:hover {
    background: var(--gray-200);
    color: var(--gray-900);
  }

  /* Pagination */
  .pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    background: white;
    border: 2px solid var(--gray-200);
    border-radius: 16px;
  }

  .pagination-info {
    font-size: 14px;
    color: var(--gray-600);
    font-weight: 600;
  }

  .pagination-controls {
    display: flex;
    gap: 8px;
  }

  .pagination-btn {
    padding: 10px 20px;
    border-radius: 10px;
    border: 2px solid var(--gray-200);
    background: white;
    color: var(--gray-700);
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
  }

  .pagination-btn:hover:not(:disabled) {
    border-color: var(--primary-blue);
    color: var(--primary-blue);
    background: rgba(59,130,246,.05);
  }

  .pagination-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
  }

  /* Empty State */
  .empty-state-modern {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border: 2px dashed var(--gray-300);
    border-radius: 16px;
  }

  .empty-state-icon {
    width: 80px;
    height: 80px;
    background: var(--gray-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 32px;
    color: var(--gray-600);
  }

  .empty-state-modern h3 {
    font-size: 20px;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0 0 8px 0;
  }

  .empty-state-modern p {
    font-size: 14px;
    color: var(--gray-600);
    margin: 0 0 24px 0;
  }

  /* Modal Styles */
  .modal-modern .modal-content {
    border-radius: 20px;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,.3);
  }

  .modal-modern .modal-header {
    padding: 24px 32px;
    border-bottom: 2px solid var(--gray-200);
  }

  .modal-modern .modal-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--gray-900);
  }

  .modal-modern .modal-body {
    padding: 32px;
  }

  .modal-modern .modal-footer {
    padding: 20px 32px;
    border-top: 2px solid var(--gray-200);
  }

  .form-group-modern {
    margin-bottom: 20px;
  }

  .form-group-modern label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .form-control-modern {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--gray-200);
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.2s;
  }

  .form-control-modern:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 4px rgba(59,130,246,.1);
  }

  /* Responsive */
  @media (max-width: 991px) {
    .users-hero h1 {
      font-size: 28px;
    }

    .users-grid {
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }

    .toolbar-row {
      flex-direction: column;
      align-items: stretch;
    }

    .toolbar-left {
      flex-direction: column;
    }

    .search-box {
      max-width: 100%;
    }
  }

  @media (max-width: 767px) {
    .users-hero {
      padding: 24px;
    }

    .users-hero h1 {
      font-size: 24px;
    }

    .users-grid {
      grid-template-columns: 1fr;
    }

    .users-table-wrapper {
      overflow-x: auto;
    }
  }
</style>

<div class="users-container">
  <!-- Hero Header -->
  <div class="users-hero">
    <div class="users-hero-content">
      <h1>
        <div class="users-hero-icon">
          <i class="fas fa-users"></i>
        </div>
        User Management
      </h1>
      <p>Manage and monitor all user accounts, roles, and permissions in one centralized dashboard</p>
      
      <div class="users-hero-stats">
        <div class="hero-stat">
          <div class="hero-stat-icon">
            <i class="fas fa-users"></i>
          </div>
          <div class="hero-stat-content">
            <h3>{{ isset($users) ? $users->total() : 0 }}</h3>
            <p>Total Users</p>
          </div>
        </div>
        
        <div class="hero-stat">
          <div class="hero-stat-icon">
            <i class="fas fa-user-check"></i>
          </div>
          <div class="hero-stat-content">
            <h3>{{ isset($users) ? $users->total() : 0 }}</h3>
            <p>Active Users</p>
          </div>
        </div>
        
        <div class="hero-stat">
          <div class="hero-stat-icon">
            <i class="fas fa-user-plus"></i>
          </div>
          <div class="hero-stat-content">
            <h3>{{ rand(5, 15) }}</h3>
            <p>New This Month</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Toolbar -->
  <div class="users-toolbar">
    <form method="GET" action="/users" id="filterForm">
      <div class="toolbar-row">
        <div class="toolbar-left">
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search users by name, email, or username...">
          </div>
          
          <select name="role" class="filter-select" onchange="document.getElementById('filterForm').submit()">
            <option value="">All Roles</option>
            <option value="admin" {{ request('role')==='admin'?'selected':'' }}>Admin</option>
            <option value="manager" {{ request('role')==='manager'?'selected':'' }}>Manager</option>
            <option value="staff" {{ request('role')==='staff'?'selected':'' }}>Staff</option>
          </select>
        </div>
        
        <div class="toolbar-right">
          <div class="view-toggle">
            <button type="button" class="view-toggle-btn active" data-view="grid">
              <i class="fas fa-th"></i>
            </button>
            <button type="button" class="view-toggle-btn" data-view="table">
              <i class="fas fa-list"></i>
            </button>
          </div>
          
          <button type="button" class="btn-modern btn-secondary-modern" onclick="location.reload()">
            <i class="fas fa-sync-alt"></i>
          </button>
          
          @can('manage-users')
          <button type="button" class="btn-modern btn-primary-modern" data-toggle="modal" data-target="#addUserModal">
            <i class="fas fa-user-plus"></i>
            Add User
          </button>
          @endcan
        </div>
      </div>
    </form>
  </div>

  <!-- Grid View -->
  <div class="users-grid" id="gridView">
    @forelse($users as $u)
      @php($initials = collect(explode(' ', $u->name))->map(fn($p)=>mb_substr($p,0,1))->join(''))
      @php($roleClass = strtolower($u->role ?? 'staff'))
      
      <div class="user-grid-card">
        <div class="user-grid-header">
          <img src="{{ asset('images/avatar.png') }}" 
               class="user-grid-avatar" 
               alt="{{ $u->name }}"
               onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&size=64&background=3B82F6&color=fff'">
          <div class="user-grid-info">
            <h3>{{ $u->name }}</h3>
            <p>{{ $u->email }}</p>
          </div>
        </div>
        
        <div class="user-grid-badges">
          <span class="badge-modern badge-{{ $roleClass }}">{{ ucfirst($u->role ?? 'Staff') }}</span>
          <span class="badge-modern badge-active">Active</span>
        </div>
        
        <div class="user-grid-meta">
          <div class="user-grid-meta-item">
            <div class="user-grid-meta-label">Username</div>
            <div class="user-grid-meta-value">{{ $u->username ?? 'N/A' }}</div>
          </div>
          <div class="user-grid-meta-item">
            <div class="user-grid-meta-label">Joined</div>
            <div class="user-grid-meta-value">{{ optional($u->created_at)->format('M Y') ?? 'N/A' }}</div>
          </div>
        </div>
      </div>
    @empty
      <div class="empty-state-modern" style="grid-column: 1 / -1;">
        <div class="empty-state-icon">
          <i class="fas fa-users"></i>
        </div>
        <h3>No Users Found</h3>
        <p>Try adjusting your search or filter criteria to find what you're looking for.</p>
        @can('manage-users')
        <button class="btn-modern btn-primary-modern" data-toggle="modal" data-target="#addUserModal">
          <i class="fas fa-user-plus"></i>
          Add Your First User
        </button>
        @endcan
      </div>
    @endforelse
  </div>

  <!-- Table View (Hidden by default) -->
  <div class="users-table-wrapper" id="tableView" style="display: none;">
    <table class="users-table">
      <thead>
        <tr>
          <th>User</th>
          <th>Role</th>
          <th>Status</th>
          <th>Joined</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $u)
          @php($initials = collect(explode(' ', $u->name))->map(fn($p)=>mb_substr($p,0,1))->join(''))
          @php($roleClass = strtolower($u->role ?? 'staff'))
          
          <tr>
            <td>
              <div class="table-user-cell">
                <img src="{{ asset('images/avatar.png') }}" 
                     class="table-avatar" 
                     alt="{{ $u->name }}"
                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&size=44&background=3B82F6&color=fff'">
                <div class="table-user-info">
                  <h4>{{ $u->name }}</h4>
                  <p>{{ $u->email }}</p>
                </div>
              </div>
            </td>
            <td>
              <span class="badge-modern badge-{{ $roleClass }}">{{ ucfirst($u->role ?? 'Staff') }}</span>
            </td>
            <td>
              <span class="badge-modern badge-active">Active</span>
            </td>
            <td>
              <span style="color: var(--gray-600); font-size: 13px; font-weight: 600;">
                {{ optional($u->created_at)->format('M j, Y') ?? 'N/A' }}
              </span>
            </td>
            <td>
              <div class="action-menu">
                <button class="action-btn" onclick="event.stopPropagation();">
                  <i class="fas fa-ellipsis-v"></i>
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5">
              <div class="empty-state-modern">
                <div class="empty-state-icon">
                  <i class="fas fa-users"></i>
                </div>
                <h3>No Users Found</h3>
                <p>Try adjusting your search or filter criteria.</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  @if(isset($users) && $users->total() > 0)
    <div class="pagination-wrapper">
      <div class="pagination-info">
        Showing <strong>{{ $users->firstItem() }}</strong> to <strong>{{ $users->lastItem() }}</strong> of <strong>{{ $users->total() }}</strong> users
      </div>
      <div class="pagination-controls">
        <button class="pagination-btn" 
                onclick="window.location.href='{{ $users->previousPageUrl() ?: '#' }}'"
                {{ $users->onFirstPage() ? 'disabled' : '' }}>
          <i class="fas fa-chevron-left mr-2"></i> Previous
        </button>
        <button class="pagination-btn"
                onclick="window.location.href='{{ $users->nextPageUrl() ?: '#' }}'"
                {{ !$users->hasMorePages() ? 'disabled' : '' }}>
          Next <i class="fas fa-chevron-right ml-2"></i>
        </button>
      </div>
    </div>
  @endif
</div>

<!-- Add User Modal -->
<div class="modal fade modal-modern" id="addUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-user-plus mr-2" style="color: var(--primary-blue);"></i>
          Add New User
        </h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group-modern">
              <label>Full Name</label>
              <input type="text" class="form-control-modern" placeholder="John Doe">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group-modern">
              <label>Email Address</label>
              <input type="email" class="form-control-modern" placeholder="john@example.com">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group-modern">
              <label>Username</label>
              <input type="text" class="form-control-modern" placeholder="johndoe">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group-modern">
              <label>Role</label>
              <select class="form-control-modern">
                <option>Admin</option>
                <option>Manager</option>
                <option selected>Staff</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group-modern">
              <label>Status</label>
              <select class="form-control-modern">
                <option selected>Active</option>
                <option>Inactive</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group-modern">
              <label>Temporary Password</label>
              <input type="text" class="form-control-modern" placeholder="Auto-generated">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-modern btn-secondary-modern" data-dismiss="modal">
          Cancel
        </button>
        <button type="button" class="btn-modern btn-primary-modern">
          <i class="fas fa-check mr-2"></i>
          Create User
        </button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function(){
  // View Toggle
  const viewToggleBtns = document.querySelectorAll('.view-toggle-btn');
  const gridView = document.getElementById('gridView');
  const tableView = document.getElementById('tableView');

  viewToggleBtns.forEach(btn => {
    btn.addEventListener('click', function(){
      const view = this.getAttribute('data-view');
      
      // Update active state
      viewToggleBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      
      // Toggle views
      if(view === 'grid') {
        gridView.style.display = 'grid';
        tableView.style.display = 'none';
      } else {
        gridView.style.display = 'none';
        tableView.style.display = 'block';
      }
    });
  });

  // Auto-submit search on input (debounced)
  const searchInput = document.querySelector('.search-box input');
  let searchTimeout;
  
  if(searchInput) {
    searchInput.addEventListener('input', function(){
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        document.getElementById('filterForm').submit();
      }, 500);
    });
  }
})();
</script>
@endpush

@endsection
