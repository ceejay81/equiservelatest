<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EquiServe') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap');
      
      * { margin: 0; padding: 0; box-sizing: border-box; }
      
      body { 
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: #0a0a0a;
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
      }
      
      /* Racing stripes background */
      body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
          linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 50%, #1a1a1a 100%),
          repeating-linear-gradient(
            45deg,
            transparent,
            transparent 10px,
            rgba(220, 38, 38, 0.03) 10px,
            rgba(220, 38, 38, 0.03) 20px
          );
        z-index: 0;
      }
      
      /* Animated red glow effect */
      body::after {
        content: '';
        position: absolute;
        width: 800px;
        height: 800px;
        background: radial-gradient(circle, rgba(220, 38, 38, 0.15) 0%, transparent 70%);
        top: -400px;
        right: -400px;
        animation: pulse 8s infinite ease-in-out;
        z-index: 0;
      }
      
      @keyframes pulse {
        0%, 100% { 
          transform: scale(1);
          opacity: 0.5;
        }
        50% { 
          transform: scale(1.2);
          opacity: 0.8;
        }
      }
      
      /* Tire track pattern */
      .tire-tracks {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 150px;
        background: 
          repeating-linear-gradient(
            90deg,
            transparent,
            transparent 20px,
            rgba(220, 38, 38, 0.05) 20px,
            rgba(220, 38, 38, 0.05) 22px
          );
        opacity: 0.3;
        z-index: 0;
      }
      
      .auth-wrapper { 
        min-height: 100vh; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        padding: 24px;
        position: relative;
        z-index: 1;
      }
      
      .auth-card { 
        width: 100%; 
        max-width: 480px; 
        border-radius: 24px; 
        overflow: hidden; 
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        background: white;
        border: none;
        animation: slideUp 0.6s ease-out;
      }
      
      @keyframes slideUp {
        from { 
          opacity: 0; 
          transform: translateY(30px); 
        }
        to { 
          opacity: 1; 
          transform: translateY(0); 
        }
      }
      
      .auth-header {
        background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
        border-bottom: 3px solid #dc2626;
        padding: 40px 40px 30px;
        text-align: center;
        position: relative;
        overflow: hidden;
      }
      
      /* Racing flag pattern */
      .auth-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(
          90deg,
          #dc2626 0%,
          #991b1b 25%,
          #dc2626 50%,
          #991b1b 75%,
          #dc2626 100%
        );
        animation: slideRacing 2s linear infinite;
      }
      
      @keyframes slideRacing {
        0% { background-position: 0 0; }
        100% { background-position: 40px 0; }
      }
      
      /* Speed lines effect */
      .auth-header::after {
        content: '';
        position: absolute;
        top: 50%;
        left: -100%;
        width: 200%;
        height: 2px;
        background: linear-gradient(
          90deg,
          transparent 0%,
          rgba(220, 38, 38, 0.3) 50%,
          transparent 100%
        );
        animation: speedLine 3s ease-in-out infinite;
      }
      
      @keyframes speedLine {
        0%, 100% { 
          left: -100%;
          opacity: 0;
        }
        50% { 
          left: 100%;
          opacity: 1;
        }
      }
      
      .brand { 
        position: relative;
        z-index: 1;
      }
      
      .brand .logo { 
        width: 80px; 
        height: 80px; 
        border-radius: 50%; 
        background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        border: 4px solid #1a1a1a;
        box-shadow: 
          0 0 0 2px #dc2626,
          0 0 30px rgba(220, 38, 38, 0.5),
          inset 0 0 20px rgba(0, 0, 0, 0.3);
        color: white; 
        display: flex; 
        align-items: center; 
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 36px;
        position: relative;
        z-index: 1;
        animation: revEngine 2s ease-in-out infinite;
      }
      
      @keyframes revEngine {
        0%, 100% { 
          transform: scale(1);
          box-shadow: 
            0 0 0 2px #dc2626,
            0 0 30px rgba(220, 38, 38, 0.5),
            inset 0 0 20px rgba(0, 0, 0, 0.3);
        }
        50% { 
          transform: scale(1.05);
          box-shadow: 
            0 0 0 2px #dc2626,
            0 0 40px rgba(220, 38, 38, 0.8),
            inset 0 0 20px rgba(0, 0, 0, 0.3);
        }
      }
      
      .brand-name {
        font-family: 'Rajdhani', sans-serif;
        font-size: 32px;
        font-weight: 700;
        color: white;
        margin-bottom: 8px;
        letter-spacing: 2px;
        text-transform: uppercase;
        text-shadow: 
          0 0 10px rgba(220, 38, 38, 0.5),
          0 2px 4px rgba(0, 0, 0, 0.8);
      }
      
      .brand-tagline {
        font-size: 13px;
        color: #9ca3af;
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
      }
      
      .auth-body {
        padding: 40px;
        position: relative;
      }
      
      /* Subtle motorcycle watermark */
      .auth-body::before {
        content: '\f21c';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        bottom: 20px;
        right: 20px;
        font-size: 120px;
        color: #f3f4f6;
        opacity: 0.3;
        z-index: 0;
        transform: rotate(-15deg);
      }
      
      .welcome-text {
        font-size: 24px;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 8px;
        position: relative;
        z-index: 1;
      }
      
      .welcome-subtext {
        font-size: 14px;
        color: #6B7280;
        margin-bottom: 32px;
        position: relative;
        z-index: 1;
      }
      
      .form-group {
        margin-bottom: 24px;
        position: relative;
        z-index: 1;
      }
      
      .form-group label {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        display: block;
      }
      
      .form-control {
        height: 48px;
        border-radius: 12px;
        border: 2px solid #E5E7EB;
        padding: 12px 16px;
        font-size: 15px;
        transition: all 0.2s;
        background: #F9FAFB;
      }
      
      .form-control:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
        background: white;
      }
      
      .form-control.is-invalid {
        border-color: #EF4444;
      }
      
      .input-icon {
        position: relative;
      }
      
      .input-icon i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #9CA3AF;
        font-size: 16px;
      }
      
      .input-icon .form-control {
        padding-left: 48px;
      }
      
      .password-toggle {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #9CA3AF;
        cursor: pointer;
        font-size: 16px;
        transition: color 0.2s;
      }
      
      .password-toggle:hover {
        color: #dc2626;
      }
      
      .form-check {
        padding-left: 0;
      }
      
      .form-check-input {
        width: 18px;
        height: 18px;
        margin-top: 2px;
        cursor: pointer;
      }
      
      .form-check-label {
        font-size: 14px;
        color: #6B7280;
        margin-left: 8px;
        cursor: pointer;
      }
      
      .btn-primary {
        height: 52px;
        border-radius: 12px;
        background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        border: 2px solid #dc2626;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        transition: all 0.3s;
        box-shadow: 
          0 4px 12px rgba(220, 38, 38, 0.4),
          inset 0 -2px 8px rgba(0, 0, 0, 0.2);
        position: relative;
        overflow: hidden;
      }
      
      .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(
          90deg,
          transparent,
          rgba(255, 255, 255, 0.2),
          transparent
        );
        transition: left 0.5s;
      }
      
      .btn-primary:hover::before {
        left: 100%;
      }
      
      .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 
          0 6px 20px rgba(220, 38, 38, 0.6),
          inset 0 -2px 8px rgba(0, 0, 0, 0.2);
        background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
        border-color: #ef4444;
      }
      
      .btn-primary:active {
        transform: translateY(0);
        box-shadow: 
          0 2px 8px rgba(220, 38, 38, 0.4),
          inset 0 2px 8px rgba(0, 0, 0, 0.3);
      }
      
      .alert {
        border-radius: 12px;
        border: none;
        padding: 16px;
        margin-bottom: 24px;
        font-size: 14px;
      }
      
      .alert-secondary {
        background: #F3F4F6;
        color: #4B5563;
      }
      
      .alert-info {
        background: #EFF6FF;
        color: #1E40AF;
        border-left: 4px solid #3B82F6;
      }
      
      .alert code {
        background: white;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 13px;
        color: #dc2626;
        font-weight: 500;
      }
      
      .invalid-feedback {
        font-size: 13px;
        margin-top: 6px;
      }
      
      .divider {
        text-align: center;
        margin: 24px 0;
        position: relative;
      }
      
      .divider::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        width: 100%;
        height: 1px;
        background: #E5E7EB;
      }
      
      .divider span {
        background: white;
        padding: 0 16px;
        color: #9CA3AF;
        font-size: 13px;
        position: relative;
        z-index: 1;
      }
      
      @media (max-width: 576px) {
        .auth-header {
          padding: 32px 24px 24px;
        }
        
        .auth-body {
          padding: 32px 24px;
        }
        
        .brand-name {
          font-size: 24px;
        }
        
        .welcome-text {
          font-size: 20px;
        }
      }
    </style>
</head>
<body>
  <div class="auth-wrapper">
    <div class="card auth-card">
      <div class="tire-tracks"></div>
      <div class="auth-header">
        <div class="brand">
          <div class="logo">
            <i class="fas fa-motorcycle"></i>
          </div>
          <div class="brand-name">EquiServe</div>
          <div class="brand-tagline">Motorcycle Sales & Loan Management</div>
        </div>
      </div>
      <div class="auth-body">
        {{ $slot }}
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
