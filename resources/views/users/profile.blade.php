@extends('layouts.app')

@php($pageTitle = $user->name . ' - Profile')

@section('content')

<style>
  /* Modern Profile Styles */
  :root {
    --profile-primary: #3B82F6;
    --profile-secondary: #8B5CF6;
    --profile-success: #10B981;
    --profile-warning: #F59E0B;
    --profile-danger: #EF4444;
  }

  .profile-wrapper {
    max-width: 1200px;
    margin: 0 auto;
  }

  /* Cover Section */
  .profile-cover {
    position: relative;
    height: 280px;
    background: linear-gradient(135deg, var(--profile-primary) 0%, var(--profile-secondary) 100%);
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 32px;
  }

  .profile-cover::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
  }

  .profile-header-content {
    position: absolute;
    bottom: 20px;
    left: 40px;
    right: 40px;
    display: flex;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
  }

  .profile-avatar-wrapper {
    position: relative;
  }

  .profile-avatar-main {
    width: 120px;
    height: 120px;
    border-radius: 20px;
    border: 4px solid var(--surface, #fff);
    object-fit: cover;
    box-shadow: 0 8px 24px rgba(0,0,0,.15);
    background: white;
  }

  .profile-avatar-badge {
    position: absolute;
    bottom: 8px;
    right: 8px;
    width: 40px;
    height: 40px;
    background: var(--profile-success);
    border: 4px solid var(--surface, #fff);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
  }

  .profile-header-info {
    flex: 1;
    min-width: 250px;
  }

  .profile-name {
    font-size: 32px;
    font-weight: 700;
    color: white;
    margin: 0 0 8px 0;
    text-shadow: 0 2px 8px rgba(0,0,0,.2);
    line-height: 1.2;
  }

  .profile-title {
    font-size: 16px;
    color: rgba(255,255,255,.9);
    margin: 0;
    text-shadow: 0 1px 4px rgba(0,0,0,.2);
    line-height: 1.5;
  }

  .profile-header-actions {
    display: flex;
    gap: 12px;
  }

  .profile-header-actions .btn {
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: 600;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,.15);
  }

  .profile-header-actions .btn-light {
    background: white;
    color: var(--profile-primary);
  }

  .profile-header-actions .btn-outline-light {
    background: rgba(255,255,255,.2);
    color: white;
    border: 2px solid rgba(255,255,255,.3);
  }

  /* Stats Cards */
  .profile-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
  }

  .stat-card {
    background: var(--surface, #fff);
    border: 1px solid var(--outline, #e5e7eb);
    border-radius: 16px;
    padding: 24px;
    text-align: center;
    transition: all 0.3s;
  }

  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,.1);
  }

  .stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    font-size: 24px;
  }

  .stat-icon.blue { background: rgba(59,130,246,.1); color: var(--profile-primary); }
  .stat-icon.purple { background: rgba(139,92,246,.1); color: var(--profile-secondary); }
  .stat-icon.green { background: rgba(16,185,129,.1); color: var(--profile-success); }
  .stat-icon.orange { background: rgba(245,158,11,.1); color: var(--profile-warning); }

  .stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary, #111827);
    margin: 0 0 4px 0;
  }

  .stat-label {
    font-size: 14px;
    color: var(--text-secondary, #6b7280);
    margin: 0;
  }

  /* Content Grid */
  .profile-content {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 24px;
  }

  /* Tabs */
  .profile-tabs {
    background: var(--surface, #fff);
    border: 1px solid var(--outline, #e5e7eb);
    border-radius: 16px;
    padding: 8px;
    display: flex;
    gap: 8px;
    margin-bottom: 24px;
  }

  .profile-tab {
    flex: 1;
    padding: 12px 20px;
    border: none;
    background: transparent;
    border-radius: 10px;
    font-weight: 600;
    color: var(--text-secondary, #6b7280);
    cursor: pointer;
    transition: all 0.2s;
  }

  .profile-tab:hover {
    background: rgba(59,130,246,.05);
    color: var(--profile-primary);
  }

  .profile-tab.active {
    background: var(--profile-primary);
    color: white;
    box-shadow: 0 4px 12px rgba(59,130,246,.3);
  }

  /* Content Cards */
  .content-card {
    background: var(--surface, #fff);
    border: 1px solid var(--outline, #e5e7eb);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
  }

  .content-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }

  .content-card-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary, #111827);
    margin: 0;
  }

  /* Activity Timeline */
  .activity-timeline {
    position: relative;
  }

  .activity-item {
    position: relative;
    padding-left: 48px;
    padding-bottom: 24px;
  }

  .activity-item:last-child {
    padding-bottom: 0;
  }

  .activity-item::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 32px;
    bottom: 0;
    width: 2px;
    background: var(--outline, #e5e7eb);
  }

  .activity-item:last-child::before {
    display: none;
  }

  .activity-icon {
    position: absolute;
    left: 0;
    top: 0;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: white;
  }

  .activity-content {
    background: var(--background, #f9fafb);
    border-radius: 12px;
    padding: 16px;
  }

  .activity-title {
    font-weight: 600;
    color: var(--text-primary, #111827);
    margin: 0 0 4px 0;
  }

  .activity-description {
    font-size: 14px;
    color: var(--text-secondary, #6b7280);
    margin: 0 0 8px 0;
  }

  .activity-time {
    font-size: 12px;
    color: var(--text-secondary, #6b7280);
  }

  /* Info List */
  .info-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .info-item {
    display: flex;
    align-items: center;
    padding: 16px 0;
    border-bottom: 1px solid var(--outline, #e5e7eb);
  }

  .info-item:last-child {
    border-bottom: none;
  }

  .info-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(59,130,246,.1);
    color: var(--profile-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 16px;
    flex-shrink: 0;
  }

  .info-content {
    flex: 1;
    min-width: 0;
  }

  .info-label {
    font-size: 12px;
    color: var(--text-secondary, #6b7280);
    margin: 0 0 2px 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
  }

  .info-value {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary, #111827);
    margin: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  /* Skills */
  .skills-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .skill-badge {
    padding: 8px 16px;
    background: rgba(59,130,246,.1);
    color: var(--profile-primary);
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
  }

  /* Responsive */
  @media (max-width: 991px) {
    .profile-content {
      grid-template-columns: 1fr;
    }

    .profile-header-content {
      flex-direction: column;
      align-items: center;
      text-align: center;
      left: 20px;
      right: 20px;
      bottom: -80px;
    }

    .profile-header-info {
      width: 100%;
    }

    .profile-header-actions {
      width: 100%;
      justify-content: center;
    }

    .profile-cover {
      margin-bottom: 120px;
    }
  }

  @media (max-width: 767px) {
    .profile-cover {
      height: 200px;
      margin-bottom: 100px;
    }

    .profile-avatar-main {
      width: 120px;
      height: 120px;
    }

    .profile-name {
      font-size: 24px;
    }

    .profile-stats {
      grid-template-columns: repeat(2, 1fr);
    }
  }
</style>

<div class="profile-wrapper">
  <!-- Cover & Header -->
  <div class="profile-cover">
    <div class="profile-header-content">
      <div class="profile-avatar-wrapper">
        <img src="{{ asset('images/avatar.png') }}" 
             class="profile-avatar-main" 
             alt="{{ $user->name }}"
             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=160&background=3B82F6&color=fff'">
        <div class="profile-avatar-badge">
          <i class="fas fa-check"></i>
        </div>
      </div>
      
      <div class="profile-header-info">
        <h1 class="profile-name">{{ $user->name }}</h1>
        <p class="profile-title">{{ ucfirst($user->role ?? 'Staff Member') }} • {{ $user->email }}</p>
      </div>
      
      <div class="profile-header-actions">
        <a href="{{ route('profile.edit') }}" class="btn btn-light">
          <i class="fas fa-edit mr-2"></i>Edit Profile
        </a>
        <a href="{{ route('profile.edit') }}" class="btn btn-outline-light">
          <i class="fas fa-cog"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- Stats -->
  <div class="profile-stats">
    <div class="stat-card">
      <div class="stat-icon blue">
        <i class="fas fa-shopping-cart"></i>
      </div>
      <p class="stat-value">{{ $stats['tasks_completed'] }}</p>
      <p class="stat-label">Sales Processed</p>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon purple">
        <i class="fas fa-file-invoice-dollar"></i>
      </div>
      <p class="stat-value">{{ $stats['active_projects'] }}</p>
      <p class="stat-label">Active Loans</p>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon green">
        <i class="fas fa-clock"></i>
      </div>
      <p class="stat-value">{{ $stats['hours_logged'] }}h</p>
      <p class="stat-label">Estimated Hours</p>
    </div>
    
    <div class="stat-card">
      <div class="stat-icon orange">
        <i class="fas fa-star"></i>
      </div>
      <p class="stat-value">{{ number_format($stats['performance_score'], 1) }}</p>
      <p class="stat-label">Performance Score</p>
    </div>
  </div>

  <!-- Main Content -->
  <div class="profile-content">
    <!-- Left Column -->
    <div>
      <!-- Profile Content -->
      <div>
        <div class="content-card">
          <div class="content-card-header">
            <h3 class="content-card-title">Performance Summary</h3>
          </div>
          <div style="color: var(--text-secondary, #6b7280); line-height: 1.8;">
            <p class="mb-3">
              <strong>{{ $user->name }}</strong> is a {{ strtolower($user->role ?? 'staff member') }} who has processed 
              <strong>{{ $stats['tasks_completed'] }} sales</strong> generating 
              <strong>₱{{ number_format($stats['total_revenue'], 2) }}</strong> in total revenue.
            </p>
            @if($stats['active_projects'] > 0)
            <p class="mb-3">
              Currently managing <strong>{{ $stats['active_projects'] }} active loan accounts</strong> with an estimated 
              <strong>{{ $stats['hours_logged'] }} hours</strong> of work logged.
            </p>
            @endif
            <p class="mb-0">
              Performance score: <strong class="text-primary">{{ number_format($stats['performance_score'], 1) }}/10.0</strong>
              @if($stats['performance_score'] >= 8.0)
                <span class="badge badge-success ml-2">Excellent</span>
              @elseif($stats['performance_score'] >= 6.5)
                <span class="badge badge-info ml-2">Good</span>
              @else
                <span class="badge badge-warning ml-2">Developing</span>
              @endif
            </p>
          </div>
        </div>

        <div class="content-card">
          <div class="content-card-header">
            <h3 class="content-card-title">Recent Activity</h3>
          </div>
          <div class="activity-timeline">
            @forelse($activities as $activity)
            <div class="activity-item">
              <div class="activity-icon" style="background: var(--profile-{{ $activity['color'] }});">
                <i class="fas {{ $activity['icon'] }}"></i>
              </div>
              <div class="activity-content">
                <p class="activity-title">{{ $activity['title'] }}</p>
                <p class="activity-description">{{ $activity['description'] }}</p>
                <span class="activity-time"><i class="far fa-clock mr-1"></i>{{ $activity['time']->diffForHumans() }}</span>
              </div>
            </div>
            @empty
            <div class="text-center py-4">
              <i class="fas fa-inbox fa-3x text-muted mb-2"></i>
              <p class="text-muted mb-0">No recent activity</p>
            </div>
            @endforelse
          </div>
        </div>
      </div>

    </div>

    <!-- Right Sidebar -->
    <div>
      <div class="content-card">
        <div class="content-card-header">
          <h3 class="content-card-title">Contact Information</h3>
        </div>
        <ul class="info-list">
          <li class="info-item">
            <div class="info-icon">
              <i class="fas fa-envelope"></i>
            </div>
            <div class="info-content">
              <p class="info-label">Email</p>
              <p class="info-value">{{ $user->email }}</p>
            </div>
          </li>
          <li class="info-item">
            <div class="info-icon">
              <i class="fas fa-user"></i>
            </div>
            <div class="info-content">
              <p class="info-label">Username</p>
              <p class="info-value">{{ $user->username ?? 'N/A' }}</p>
            </div>
          </li>
          <li class="info-item">
            <div class="info-icon">
              <i class="fas fa-id-badge"></i>
            </div>
            <div class="info-content">
              <p class="info-label">User ID</p>
              <p class="info-value">#{{ $user->id }}</p>
            </div>
          </li>
          <li class="info-item">
            <div class="info-icon">
              <i class="fas fa-shield-alt"></i>
            </div>
            <div class="info-content">
              <p class="info-label">Role</p>
              <p class="info-value">{{ ucfirst($user->role ?? 'Staff') }}</p>
            </div>
          </li>
        </ul>
      </div>

      <div class="content-card">
        <div class="content-card-header">
          <h3 class="content-card-title">Account Details</h3>
        </div>
        <ul class="info-list">
          <li class="info-item">
            <div class="info-icon">
              <i class="fas fa-calendar-plus"></i>
            </div>
            <div class="info-content">
              <p class="info-label">Member Since</p>
              <p class="info-value">{{ optional($user->created_at)->format('M j, Y') ?? 'N/A' }}</p>
            </div>
          </li>
          <li class="info-item">
            <div class="info-icon">
              <i class="fas fa-clock"></i>
            </div>
            <div class="info-content">
              <p class="info-label">Last Login</p>
              <p class="info-value">{{ optional($user->last_login_at)->diffForHumans() ?? 'Never' }}</p>
            </div>
          </li>
          <li class="info-item">
            <div class="info-icon">
              <i class="fas fa-toggle-on"></i>
            </div>
            <div class="info-content">
              <p class="info-label">Status</p>
              <p class="info-value" style="color: var(--profile-success);">Active</p>
            </div>
          </li>
        </ul>
      </div>

    </div>
  </div>
</div>



@endsection
