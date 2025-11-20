<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EquiServe - Modern Inventory & Sales Management</title>
    
    <!-- PWA Meta Tags -->
    <meta name="description" content="Modern offline-capable inventory and sales management system">
    <meta name="theme-color" content="#3B82F6">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="EquiServe">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    
    <!-- Icons -->
    <link rel="icon" type="image/png" sizes="192x192" href="/images/icon-192.png">
    <link rel="apple-touch-icon" href="/images/icon-192.png">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    @vite(['resources/css/modern-components.css', 'resources/js/toast-notifications.js', 'resources/js/offline-manager.js'])
    @stack('styles')
    <style>
      :root{
        --primary:#0F172A; /* Dark Header Base */
        --primary-variant:#3B82F6; /* Accent Indigo */
        --on-primary:#FFFFFF;
        --sidebar-bg:#0B1220; /* Deep Navy for sidebar */
        --sidebar-muted:#94A3B8; /* Slate text */
        --sidebar-active:#1F2937; /* Active bg */
        --background:#F3F4F6; /* Page background */
        --surface:#FFFFFF; /* Cards */
        --outline:#E5E7EB; /* Borders */
        --text-primary:#0F172A; /* Charcoal */
        --text-secondary:#475569; /* Medium Gray */
        --success:#10B981; /* Emerald */
        --warning:#F59E0B; /* Amber */
        --error:#EF4444;  /* Red */
        --info:#38BDF8;   /* Sky */
      }

      /* Base */
      html{ margin:0; padding:0; background:var(--primary); }
      body{ margin:0; background:var(--background); color:var(--text-primary); }
      .content-wrapper{ background:var(--background); padding:16px; }
      .main-header.navbar{ background:var(--primary)!important; color:var(--on-primary)!important; box-shadow:0 2px 12px rgba(0,0,0,.25); margin-top:0; border-top:0; }
      .main-header .nav-link{ color:var(--on-primary)!important; }
      .navbar-search{
        background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12); border-radius:10px; color:#fff;
      }
      .navbar-search .form-control{ background:transparent; border:0; color:#fff; }
      .navbar-search .form-control::placeholder{ color:rgba(255,255,255,.7); }
      .card{ 
        background:var(--surface); 
        border:1px solid var(--outline); 
        border-radius:12px;
        box-shadow: 0 10px 24px rgba(2,6,23,.12), 0 2px 6px rgba(2,6,23,.08);
      }
      .card .card-title, .card .card-header{ color:var(--text-primary); }

      /* App bar (content header) */
      .content-header{ background:var(--primary); color:var(--on-primary); }
      .content-header a{ color:var(--on-primary); }
      .content-header .breadcrumb-item+.breadcrumb-item::before{ color:rgba(255,255,255,.7); }

      /* Sidebar dark scheme */
      .main-sidebar.sidebar-theme{ background:var(--sidebar-bg); color:#E5E7EB; }
      .sidebar-theme .brand-link{ background:transparent; border-bottom:1px solid rgba(255,255,255,.08); color:#E5E7EB; }
      .sidebar-theme .brand-link .brand-text{ color:#E5E7EB; }
      .sidebar-theme .nav-sidebar .nav-link{ color:var(--sidebar-muted); border-radius:10px; }
      .sidebar-theme .nav-sidebar .nav-link .nav-icon{ color:#64748B; }
      .sidebar-theme .nav-sidebar .nav-item>.nav-link.active{ background:rgba(255,255,255,.08); color:#FFFFFF; }
      .sidebar-theme .nav-sidebar .nav-item>.nav-link.active .nav-icon{ color:#FFFFFF; }
      .sidebar-theme .nav-sidebar .menu-open>.nav-link{ background:rgba(255,255,255,.05); color:#fff; }
      .sidebar-theme .nav-sidebar .nav-treeview .nav-link{ color:#CBD5E1; }
      .sidebar-theme .nav-sidebar .nav-link:hover{ background:rgba(255,255,255,.06); color:#fff; }
      .sidebar-theme .nav-header{ color:#94A3B8; font-size:.75rem; letter-spacing:.08em; padding:.75rem 1rem; }

      /* Buttons */
      .btn-primary{ background-color:var(--primary-variant); border-color:var(--primary-variant); }
      .btn-primary:hover{ background-color:#2563EB; border-color:#2563EB; }
      .btn-outline-primary{ color:var(--primary-variant); border-color:var(--primary-variant); }

      /* Small boxes accents */
      .small-box.bg-info{ background:var(--info)!important; color:#00314d; }
      .small-box.bg-success{ background:var(--success)!important; }
      .small-box.bg-danger{ background:var(--error)!important; }
      .small-box.bg-warning{ background:var(--warning)!important; }

      /* Tables */
      .table thead th{ background:#E5E7EB; color:#111827; border-color:var(--outline); }

      /* Badges */
      .badge-success, .text-bg-success{ background:var(--success)!important; }
      .badge-warning, .text-bg-warning{ background:var(--warning)!important; }
      .badge-danger, .text-bg-danger{ background:var(--error)!important; }
      .badge-info, .text-bg-info{ background:var(--info)!important; }

      /* Optional Dark Mode */
      .dark-mode body{ background:#0F172A; color:#E2E8F0; }
      .dark-mode .content-wrapper{ background:#0F172A; }
      .dark-mode .card{ background:#1E293B; border-color:#0f2233; }
      .dark-mode .main-sidebar.sidebar-theme{ background:#0B1220; color:#E2E8F0; }
      .dark-mode .sidebar-theme .nav-sidebar .nav-link{ color:#E2E8F0; }
      .dark-mode .sidebar-theme .nav-sidebar .nav-item>.nav-link.active{ background:var(--primary-variant); color:#fff; }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand" style="background:var(--primary);color:var(--on-primary);">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link text-white fw-bold">{{ $pageTitle ?? 'Dashboard' }} — Equiserve Gensan</span>
      </li>
    </ul>

    <!-- Center search -->
    <form class="form-inline mx-auto d-none d-md-flex" style="max-width:720px; flex:1 1 auto;">
      <div class="input-group navbar-search w-100">
        <div class="input-group-prepend"><span class="input-group-text bg-transparent border-0 text-white"><i class="fas fa-search"></i></span></div>
        <input class="form-control" type="search" placeholder="Search…" aria-label="Search">
      </div>
    </form>

    <ul class="navbar-nav ml-auto align-items-center">
      <!-- Online/Offline Status -->
      <li class="nav-item mr-2">
        <span id="online-status" class="badge badge-success">
          <i class="fas fa-wifi"></i> Online
        </span>
      </li>
      
      <li class="nav-item position-relative">
        <a class="nav-link text-white" href="/notifications" role="button" title="Notifications">
          <i class="fas fa-bell"></i>
          @php
            $urgentNotificationCount = \App\Models\Notification::urgent()->unactioned()->count();
          @endphp
          @if($urgentNotificationCount > 0)
            <span class="badge badge-danger" style="position:absolute;top:6px;right:6px;font-size:10px">{{ $urgentNotificationCount }}</span>
          @endif
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white" href="#" role="button" onclick="location.reload()" title="Refresh">
          <i class="fas fa-sync-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white" href="#" role="button" id="install-pwa" style="display:none;" title="Install App">
          <i class="fas fa-download"></i>
        </a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link text-white dropdown-toggle" data-toggle="dropdown" href="#" role="button">
          <i class="fas fa-user mr-1"></i>
          {{ Auth::user()->name }}
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="/profile"><i class="fas fa-user mr-2"></i> My Profile</a>
          @can('manage-settings')
          <a class="dropdown-item" href="/settings"><i class="fas fa-cog mr-2"></i> System Settings</a>
          @endcan
          <div class="dropdown-divider"></div>
          <form method="POST" action="{{ route('logout') }}" class="px-3">
            @csrf
            <button class="btn btn-link p-0"><i class="fas fa-sign-out-alt mr-2"></i> Logout</button>
          </form>
        </div>
      </li>
    </ul>
  </nav>

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-theme elevation-4">
    <!-- Brand Logo -->
    <a href="/dashboard" class="brand-link">
      <span class="brand-text font-weight-light">EquiServe</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-header">MAIN</li>
          <li class="nav-item"><a href="/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
          <li class="nav-item"><a href="/customers" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Customers</p></a></li>

          <li class="nav-item has-treeview menu-closed">
            <a href="#" class="nav-link"><i class="nav-icon fas fa-shopping-cart"></i><p>Sales<i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
              <li class="nav-item"><a href="/sales" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Sales</p></a></li>
              <li class="nav-item"><a href="/sales/create" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Create Sale</p></a></li>
            </ul>
          </li>

          <li class="nav-item"><a href="/loans" class="nav-link"><i class="nav-icon fas fa-file-invoice-dollar"></i><p>Accounts Receivable</p></a></li>

          <li class="nav-item has-treeview menu-closed">
            <a href="#" class="nav-link"><i class="nav-icon fas fa-warehouse"></i><p>Inventory<i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
              <li class="nav-item"><a href="/products" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Products</p></a></li>
              <li class="nav-item"><a href="/inventory" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Stock</p></a></li>
            </ul>
          </li>

          @can('view-reports')
          <li class="nav-item"><a href="/reports" class="nav-link"><i class="nav-icon fas fa-chart-bar"></i><p>Reports</p></a></li>
          @endcan

          <li class="nav-header">SYSTEM</li>
          @can('manage-settings')
          <li class="nav-item has-treeview menu-closed">
            <a href="#" class="nav-link"><i class="nav-icon fas fa-cog"></i><p>Settings<i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
              <li class="nav-item"><a href="/settings" class="nav-link"><i class="far fa-circle nav-icon"></i><p>System Settings</p></a></li>
              <li class="nav-item"><a href="/settings/audit" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Audit Log</p></a></li>
            </ul>
          </li>
          @endcan
          @can('manage-users')
          <li class="nav-item"><a href="/users" class="nav-link"><i class="nav-icon fas fa-users-cog"></i><p>User Management</p></a></li>
          @endcan
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <section class="content">
      <div class="container-fluid" style="padding-top:16px">
        @yield('content')
      </div>
    </section>
  </div>

  <footer class="main-footer text-sm">
    <div class="float-right d-none d-sm-inline">EquiServe</div>
    <strong> 2025 Equiserve Gensan</strong>
  </footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<!-- Service Worker Registration -->
<script>
  // Register Service Worker for offline functionality
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('/sw.js')
        .then(reg => console.log('Service Worker registered:', reg.scope))
        .catch(err => console.error('Service Worker registration failed:', err));
    });
  }

  // PWA Install Prompt
  let deferredPrompt;
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    document.getElementById('install-pwa').style.display = 'block';
  });

  document.getElementById('install-pwa')?.addEventListener('click', async (e) => {
    e.preventDefault();
    if (!deferredPrompt) return;
    
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    console.log(`User response: ${outcome}`);
    deferredPrompt = null;
    document.getElementById('install-pwa').style.display = 'none';
  });

  // Online/Offline Status
  function updateOnlineStatus() {
    const statusEl = document.getElementById('online-status');
    if (!statusEl) return;
    
    if (navigator.onLine) {
      statusEl.innerHTML = '<i class="fas fa-wifi"></i> Online';
      statusEl.className = 'badge badge-success';
    } else {
      statusEl.innerHTML = '<i class="fas fa-wifi-slash"></i> Offline';
      statusEl.className = 'badge badge-warning';
    }
  }

  window.addEventListener('online', updateOnlineStatus);
  window.addEventListener('offline', updateOnlineStatus);
  updateOnlineStatus();

  // Mark active link based on path
  (function(){
    var path = window.location.pathname;
    document.querySelectorAll('.nav-sidebar a.nav-link').forEach(function(a){
      if(a.getAttribute('href') === path){
        a.classList.add('active');
        var parent = a.closest('.has-treeview');
        if(parent){ parent.classList.add('menu-open'); parent.querySelector(':scope > .nav-link').classList.add('active'); }
      }
    });
  })();
</script>
@stack('scripts')
</body>
</html>
