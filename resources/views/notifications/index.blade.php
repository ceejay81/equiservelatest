@extends('layouts.app')

@section('content')
<div class="row mb-3">
  <div class="col-sm-6"><h1 class="mb-0">Notifications & Reminders</h1></div>
  <div class="col-sm-6 text-right">
    <form method="POST" action="{{ route('notifications.markAllRead') }}" class="d-inline">
      @csrf
      <button type="submit" class="btn btn-outline-secondary">
        <i class="fas fa-check-double mr-1"></i> Mark All as Read
      </button>
    </form>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
@endif

<!-- Stats -->
<div class="row mb-3">
  <div class="col-md-4">
    <div class="small-box bg-danger">
      <div class="inner">
        <h3>{{ \App\Models\Notification::where('priority', 'critical')->unactioned()->count() }}</h3>
        <p>Critical (Overdue)</p>
      </div>
      <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>{{ \App\Models\Notification::where('priority', 'high')->unactioned()->count() }}</h3>
        <p>High Priority (Due Soon)</p>
      </div>
      <div class="icon"><i class="fas fa-clock"></i></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="small-box bg-info">
      <div class="inner">
        <h3>{{ \App\Models\Notification::unactioned()->count() }}</h3>
        <p>Total Pending</p>
      </div>
      <div class="icon"><i class="fas fa-bell"></i></div>
    </div>
  </div>
</div>

<!-- Notifications List -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">All Notifications</h3>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th style="width: 50px;"></th>
            <th>Customer</th>
            <th>Message</th>
            <th>Contact</th>
            <th>Priority</th>
            <th>Created</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($notifications as $notification)
            <tr class="{{ !$notification->is_read ? 'table-active' : '' }} {{ $notification->priority === 'critical' ? 'table-danger' : ($notification->priority === 'high' ? 'table-warning' : '') }}">
              <td class="text-center">
                @if($notification->priority === 'critical')
                  <i class="fas fa-exclamation-circle text-danger fa-lg"></i>
                @elseif($notification->priority === 'high')
                  <i class="fas fa-exclamation-triangle text-warning fa-lg"></i>
                @else
                  <i class="fas fa-info-circle text-info fa-lg"></i>
                @endif
              </td>
              <td>
                <strong>{{ $notification->data['customer_name'] ?? 'N/A' }}</strong>
                @if(isset($notification->data['sale_number']))
                  <br><small class="text-muted">{{ $notification->data['sale_number'] }}</small>
                @endif
              </td>
              <td>
                <div>{{ $notification->title }}</div>
                <small class="text-muted">{{ $notification->message }}</small>
              </td>
              <td>
                @if(isset($notification->data['customer_phone']))
                  <a href="tel:{{ $notification->data['customer_phone'] }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-phone mr-1"></i> {{ $notification->data['customer_phone'] }}
                  </a>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
              <td>
                @if($notification->priority === 'critical')
                  <span class="badge badge-danger">Critical</span>
                @elseif($notification->priority === 'high')
                  <span class="badge badge-warning">High</span>
                @elseif($notification->priority === 'medium')
                  <span class="badge badge-info">Medium</span>
                @else
                  <span class="badge badge-secondary">Low</span>
                @endif
              </td>
              <td>
                <div>{{ $notification->created_at->format('M d, Y') }}</div>
                <small class="text-muted">{{ $notification->created_at->format('g:i A') }}</small>
              </td>
              <td>
                @if($notification->is_actioned)
                  <span class="badge badge-success">
                    <i class="fas fa-check mr-1"></i> Contacted
                  </span>
                  @if($notification->actionedBy)
                    <br><small class="text-muted">by {{ $notification->actionedBy->name }}</small>
                  @endif
                @else
                  <span class="badge badge-secondary">Pending</span>
                @endif
              </td>
              <td>
                @if(!$notification->is_actioned)
                  <form method="POST" action="{{ route('notifications.action', $notification->id) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success" title="Mark as contacted">
                      <i class="fas fa-check"></i>
                    </button>
                  </form>
                @endif
                @if($notification->related_type === 'App\Models\Loan' && $notification->related_id)
                  <a href="/loans/{{ $notification->related_id }}" class="btn btn-sm btn-outline-primary" title="View loan">
                    <i class="fas fa-eye"></i>
                  </a>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-4">
                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No notifications found</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer">
    {{ $notifications->links() }}
  </div>
</div>

@endsection
