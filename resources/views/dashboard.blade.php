@extends('layouts.app')

@section('content')

<style>
  /* Enhanced Dashboard Styles */
  .dashboard-header {
    margin-bottom: 24px;
  }
  .dashboard-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 4px 0;
  }
  .dashboard-header p {
    color: var(--text-secondary);
    margin: 0;
    font-size: 14px;
  }
  
  /* Stat Cards */
  .stat-card {
    background: var(--surface);
    border: 1px solid var(--outline);
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s ease;
    height: 100%;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
  }
  .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
  }
  .stat-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
  }
  .stat-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }
  .stat-card-icon.blue { background: rgba(59,130,246,.1); color: #3B82F6; }
  .stat-card-icon.green { background: rgba(16,185,129,.1); color: #10B981; }
  .stat-card-icon.amber { background: rgba(245,158,11,.1); color: #F59E0B; }
  .stat-card-icon.red { background: rgba(239,68,68,.1); color: #EF4444; }
  .stat-card-icon.purple { background: rgba(139,92,246,.1); color: #8B5CF6; }
  
  .stat-card-label {
    font-size: 13px;
    color: var(--text-secondary);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .stat-card-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 8px 0;
    line-height: 1;
  }
  .stat-card-footer {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--text-secondary);
  }
  .stat-trend {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 12px;
  }
  .stat-trend.up { background: rgba(16,185,129,.1); color: #10B981; }
  .stat-trend.down { background: rgba(239,68,68,.1); color: #EF4444; }
  
  /* Alert Banner */
  .alert-banner {
    background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
    color: white;
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 4px 12px rgba(245,158,11,.3);
  }
  .alert-banner-icon {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,.2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }
  .alert-banner-content {
    flex: 1;
  }
  .alert-banner-content h5 {
    margin: 0 0 4px 0;
    font-size: 16px;
    font-weight: 700;
  }
  .alert-banner-content p {
    margin: 0;
    font-size: 14px;
    opacity: 0.95;
  }
  .alert-banner-action {
    background: white;
    color: #D97706;
    padding: 8px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.2s;
  }
  .alert-banner-action:hover {
    background: rgba(255,255,255,.9);
    transform: translateY(-1px);
    color: #D97706;
  }
  
  /* Quick Actions */
  .quick-action-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 10px;
    border: 2px solid var(--outline);
    background: var(--surface);
    color: var(--text-primary);
    font-weight: 600;
    font-size: 14px;
    transition: all 0.2s;
    text-decoration: none;
    margin-bottom: 12px;
  }
  .quick-action-btn:hover {
    border-color: var(--primary-variant);
    background: rgba(59,130,246,.05);
    color: var(--primary-variant);
    transform: translateX(4px);
  }
  .quick-action-btn.primary {
    background: var(--primary-variant);
    border-color: var(--primary-variant);
    color: white;
  }
  .quick-action-btn.primary:hover {
    background: #2563EB;
    color: white;
    transform: translateY(-2px);
  }
  .quick-action-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,.15);
  }
  .quick-action-btn:not(.primary) .quick-action-icon {
    background: var(--background);
  }
  
  /* Recent Activity */
  .activity-item {
    display: flex;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--outline);
  }
  .activity-item:last-child {
    border-bottom: none;
  }
  .activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(59,130,246,.1);
    color: #3B82F6;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .activity-content {
    flex: 1;
    min-width: 0;
  }
  .activity-title {
    font-weight: 600;
    font-size: 14px;
    color: var(--text-primary);
    margin: 0 0 2px 0;
  }
  .activity-subtitle {
    font-size: 13px;
    color: var(--text-secondary);
    margin: 0;
  }
  .activity-meta {
    text-align: right;
    flex-shrink: 0;
  }
  .activity-amount {
    font-weight: 700;
    font-size: 14px;
    color: var(--text-primary);
  }
  .activity-time {
    font-size: 12px;
    color: var(--text-secondary);
  }
  
  /* Top Products */
  .product-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
  }
  .product-rank {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: var(--background);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 13px;
    color: var(--text-secondary);
    flex-shrink: 0;
  }
  .product-rank.top {
    background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
    color: white;
  }
  .product-name {
    flex: 1;
    font-weight: 600;
    font-size: 14px;
    color: var(--text-primary);
  }
  .product-sold {
    font-weight: 700;
    font-size: 14px;
    color: var(--text-secondary);
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .stat-card-value { font-size: 24px; }
    .dashboard-header h1 { font-size: 24px; }
  }
</style>

<!-- Dashboard Header -->
<div class="dashboard-header">
  <h1>Welcome back, {{ Auth::user()->name }} 👋</h1>
  <p>Here's what's happening with your business today</p>
</div>

<!-- Urgent Alert Banner -->
@if($urgentCount > 0)
<div class="alert-banner">
  <div class="alert-banner-icon">
    <i class="fas fa-exclamation-triangle"></i>
  </div>
  <div class="alert-banner-content">
    <h5>{{ $urgentCount }} Urgent Notification{{ $urgentCount > 1 ? 's' : '' }}</h5>
    <p>You have customers with overdue or upcoming payments that need attention</p>
  </div>
  <a href="/notifications" class="alert-banner-action">View Now</a>
</div>
@endif

<!-- Key Metrics -->
<div class="row mb-4">
  <div class="col-lg-3 col-md-6 mb-3">
    <a href="/sales" style="text-decoration: none;">
      <div class="stat-card">
        <div class="stat-card-header">
          <div>
            <div class="stat-card-label">Sales Today</div>
            <div class="stat-card-value">₱{{ number_format($salesTodaySum, 0) }}</div>
          </div>
          <div class="stat-card-icon green">
            <i class="fas fa-shopping-cart"></i>
          </div>
        </div>
        <div class="stat-card-footer">
          <span>This week: ₱{{ number_format($salesThisWeek, 0) }}</span>
        </div>
      </div>
    </a>
  </div>

  <div class="col-lg-3 col-md-6 mb-3">
    <a href="/sales" style="text-decoration: none;">
      <div class="stat-card">
        <div class="stat-card-header">
          <div>
            <div class="stat-card-label">Monthly Sales</div>
            <div class="stat-card-value">₱{{ number_format($salesThisMonth, 0) }}</div>
          </div>
          <div class="stat-card-icon blue">
            <i class="fas fa-chart-line"></i>
          </div>
        </div>
        <div class="stat-card-footer">
          @if($salesGrowth >= 0)
            <span class="stat-trend up">
              <i class="fas fa-arrow-up"></i> {{ number_format(abs($salesGrowth), 1) }}%
            </span>
            <span>vs last month</span>
          @else
            <span class="stat-trend down">
              <i class="fas fa-arrow-down"></i> {{ number_format(abs($salesGrowth), 1) }}%
            </span>
            <span>vs last month</span>
          @endif
        </div>
      </div>
    </a>
  </div>

  <div class="col-lg-3 col-md-6 mb-3">
    <a href="/loans" style="text-decoration: none;">
      <div class="stat-card">
        <div class="stat-card-header">
          <div>
            <div class="stat-card-label">Receivables</div>
            <div class="stat-card-value">₱{{ number_format($totalReceivables, 0) }}</div>
          </div>
          <div class="stat-card-icon purple">
            <i class="fas fa-file-invoice-dollar"></i>
          </div>
        </div>
        <div class="stat-card-footer">
          @if($overdueLoans > 0)
            <span class="stat-trend down">{{ $overdueLoans }} overdue</span>
          @else
            <span style="color: var(--success);">All up to date</span>
          @endif
        </div>
      </div>
    </a>
  </div>

  <div class="col-lg-3 col-md-6 mb-3">
    <a href="/inventory" style="text-decoration: none;">
      <div class="stat-card">
        <div class="stat-card-header">
          <div>
            <div class="stat-card-label">Low Stock Items</div>
            <div class="stat-card-value">{{ $inventoryAlertsCount }}</div>
          </div>
          <div class="stat-card-icon {{ $inventoryAlertsCount > 0 ? 'red' : 'amber' }}">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
        </div>
        <div class="stat-card-footer">
          <span>{{ $productsCount }} total products</span>
        </div>
      </div>
    </a>
  </div>
</div>

<!-- Main Content Grid -->
<div class="row">
  <!-- Sales Chart -->
  <div class="col-lg-8 mb-4">
    <div class="card">
      <div class="card-header border-0 pb-0">
        <h3 class="card-title mb-0" style="font-weight: 700; font-size: 18px;">
          <i class="fas fa-chart-line mr-2" style="color: var(--primary-variant);"></i>Sales Overview
        </h3>
        <div class="card-tools">
          <span class="badge badge-light" style="font-size: 12px;">Last 7 Days</span>
        </div>
      </div>
      <div class="card-body pt-3">
        <canvas id="salesChart" style="height: 280px;"></canvas>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="card mt-4">
      <div class="card-header border-0 pb-0">
        <h3 class="card-title mb-0" style="font-weight: 700; font-size: 18px;">
          <i class="fas fa-history mr-2" style="color: var(--primary-variant);"></i>Recent Sales
        </h3>
        <div class="card-tools">
          <a href="/sales" style="font-size: 13px; color: var(--primary-variant); font-weight: 600;">View All →</a>
        </div>
      </div>
      <div class="card-body pt-3">
        @forelse($recentSales as $sale)
          <div class="activity-item">
            <div class="activity-icon">
              <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="activity-content">
              <p class="activity-title">Sale #{{ $sale->id }}</p>
              <p class="activity-subtitle">{{ $sale->customer->name ?? 'Walk-in Customer' }}</p>
            </div>
            <div class="activity-meta">
              <div class="activity-amount">₱{{ number_format($sale->total_amount, 2) }}</div>
              <div class="activity-time">{{ $sale->created_at->diffForHumans() }}</div>
            </div>
          </div>
        @empty
          <p class="text-center text-muted py-4">No recent sales</p>
        @endforelse
      </div>
    </div>
  </div>

  <!-- Sidebar -->
  <div class="col-lg-4 mb-4">
    <!-- Quick Actions -->
    <div class="card mb-4">
      <div class="card-header border-0 pb-0">
        <h3 class="card-title mb-0" style="font-weight: 700; font-size: 18px;">
          <i class="fas fa-bolt mr-2" style="color: var(--primary-variant);"></i>Quick Actions
        </h3>
      </div>
      <div class="card-body pt-3">
        <a href="/sales/create" class="quick-action-btn primary">
          <div class="quick-action-icon">
            <i class="fas fa-plus"></i>
          </div>
          <span>Create New Sale</span>
        </a>
        <a href="/customers/create" class="quick-action-btn">
          <div class="quick-action-icon">
            <i class="fas fa-user-plus"></i>
          </div>
          <span>Add Customer</span>
        </a>
        <a href="/loans" class="quick-action-btn">
          <div class="quick-action-icon">
            <i class="fas fa-file-invoice-dollar"></i>
          </div>
          <span>Accounts Receivable</span>
        </a>
        <a href="/inventory" class="quick-action-btn">
          <div class="quick-action-icon">
            <i class="fas fa-boxes"></i>
          </div>
          <span>Manage Inventory</span>
        </a>
        <a href="/notifications" class="quick-action-btn">
          <div class="quick-action-icon">
            <i class="fas fa-bell"></i>
          </div>
          <span>Notifications</span>
          @if($urgentCount > 0)
            <span class="badge badge-danger ml-auto">{{ $urgentCount }}</span>
          @endif
        </a>
      </div>
    </div>

    <!-- Top Products -->
    <div class="card mb-4">
      <div class="card-header border-0 pb-0">
        <h3 class="card-title mb-0" style="font-weight: 700; font-size: 18px;">
          <i class="fas fa-fire mr-2" style="color: var(--warning);"></i>Top Products
        </h3>
        <div class="card-tools">
          <span class="badge badge-light" style="font-size: 12px;">This Month</span>
        </div>
      </div>
      <div class="card-body pt-3">
        @forelse($topProducts as $index => $product)
          <div class="product-item">
            <div class="product-rank {{ $index === 0 ? 'top' : '' }}">{{ $index + 1 }}</div>
            <div class="product-name">{{ $product->name }}</div>
            <div class="product-sold">{{ number_format($product->total_sold) }}</div>
          </div>
        @empty
          <p class="text-center text-muted py-3">No sales data yet</p>
        @endforelse
      </div>
    </div>

    <!-- Business Insights -->
    <div class="card">
      <div class="card-header border-0 pb-0">
        <h3 class="card-title mb-0" style="font-weight: 700; font-size: 18px;">
          <i class="fas fa-lightbulb mr-2" style="color: var(--warning);"></i>Business Insights
        </h3>
      </div>
      <div class="card-body pt-3">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom: 1px solid var(--outline);">
          <div>
            <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 4px;">Total Customers</div>
            <div style="font-size: 20px; font-weight: 700; color: var(--text-primary);">{{ number_format($customersCount) }}</div>
          </div>
          <div class="stat-card-icon blue">
            <i class="fas fa-users"></i>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom: 1px solid var(--outline);">
          <div>
            <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 4px;">Active Products</div>
            <div style="font-size: 20px; font-weight: 700; color: var(--text-primary);">{{ number_format($productsCount) }}</div>
          </div>
          <div class="stat-card-icon amber">
            <i class="fas fa-box"></i>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 4px;">System Status</div>
            <div style="font-size: 14px; font-weight: 600; color: var(--success);">
              <i class="fas fa-check-circle mr-1"></i>All Systems Operational
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function(){
  if(!window.Chart) return;
  var ctx = document.getElementById('salesChart');
  if(!ctx) return;
  
  // Chart data from backend
  var labels = {!! json_encode($chartLabels, JSON_HEX_APOS|JSON_HEX_QUOT) !!};
  var data = {!! json_encode($chartValues, JSON_HEX_APOS|JSON_HEX_QUOT) !!};
  
  // Create gradient
  var gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
  gradient.addColorStop(0, 'rgba(59,130,246,0.2)');
  gradient.addColorStop(1, 'rgba(59,130,246,0.01)');
  
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Daily Sales',
        data: data,
        borderColor: '#3B82F6',
        backgroundColor: gradient,
        tension: 0.4,
        fill: true,
        borderWidth: 3,
        pointRadius: 5,
        pointHoverRadius: 7,
        pointBackgroundColor: '#3B82F6',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointHoverBackgroundColor: '#3B82F6',
        pointHoverBorderColor: '#fff',
        pointHoverBorderWidth: 3
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: {
        intersect: false,
        mode: 'index'
      },
      plugins: { 
        legend: { 
          display: false 
        },
        tooltip: {
          backgroundColor: 'rgba(15,23,42,0.95)',
          titleColor: '#fff',
          bodyColor: '#fff',
          padding: 12,
          borderColor: 'rgba(59,130,246,0.3)',
          borderWidth: 1,
          cornerRadius: 8,
          displayColors: false,
          callbacks: {
            title: function(context) {
              return context[0].label;
            },
            label: function(context) {
              return 'Sales: ₱' + context.parsed.y.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              });
            }
          }
        }
      },
      scales: { 
        x: {
          grid: {
            display: false
          },
          ticks: {
            font: {
              size: 12,
              weight: '600'
            },
            color: '#64748B'
          }
        },
        y: { 
          beginAtZero: true,
          grid: {
            color: 'rgba(226,232,240,0.5)',
            drawBorder: false
          },
          ticks: {
            font: {
              size: 12
            },
            color: '#64748B',
            callback: function(value) {
              if (value >= 1000) {
                return '₱' + (value/1000) + 'k';
              }
              return '₱' + value;
            }
          }
        }
      }
    }
  });
})();
</script>
@endpush

@endsection
