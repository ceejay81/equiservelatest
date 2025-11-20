<x-guest-layout>
  <div class="welcome-text">Welcome Back</div>
  <div class="welcome-subtext">Sign in to your account to continue</div>

  @if (session('status'))
    <div class="alert alert-info" role="alert">
      <i class="fas fa-info-circle mr-2"></i>{{ session('status') }}
    </div>
  @endif

  <div class="alert alert-secondary" role="alert">
    <div class="d-flex align-items-start">
      <i class="fas fa-user-shield mr-2 mt-1" style="font-size: 18px;"></i>
      <div style="flex: 1;">
        <strong style="font-size: 13px;">Test Accounts Available:</strong>
        <div class="small mt-2" style="line-height: 1.8;">
          <div><code>admin@equiserve.test</code> / <code>password</code></div>
          <div><code>manager@equiserve.test</code> / <code>password</code></div>
          <div><code>staff@equiserve.test</code> / <code>password</code></div>
        </div>
      </div>
    </div>
  </div>

  <form method="POST" action="{{ route('login') }}" id="loginForm">
    @csrf
    
    <div class="form-group">
      <label for="email">
        <i class="fas fa-envelope mr-1" style="color: #dc2626;"></i>
        Email Address
      </label>
      <div class="input-icon">
        <i class="far fa-envelope"></i>
        <input id="email" 
               type="email" 
               name="email" 
               value="{{ old('email') }}" 
               class="form-control @error('email') is-invalid @enderror" 
               placeholder="Enter your email"
               required 
               autofocus 
               autocomplete="username">
      </div>
      @error('email')
        <span class="invalid-feedback d-block" role="alert">
          <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
        </span>
      @enderror
    </div>

    <div class="form-group">
      <label for="password">
        <i class="fas fa-lock mr-1" style="color: #dc2626;"></i>
        Password
      </label>
      <div class="input-icon">
        <i class="fas fa-lock"></i>
        <input id="password" 
               type="password" 
               name="password" 
               class="form-control @error('password') is-invalid @enderror" 
               placeholder="Enter your password"
               required 
               autocomplete="current-password">
        <span class="password-toggle" onclick="togglePassword()">
          <i class="far fa-eye" id="toggleIcon"></i>
        </span>
      </div>
      @error('password')
        <span class="invalid-feedback d-block" role="alert">
          <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
        </span>
      @enderror
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
        <label class="form-check-label" for="remember_me">
          Remember me
        </label>
      </div>
    </div>

    <button type="submit" class="btn btn-primary btn-block">
      <i class="fas fa-sign-in-alt mr-2"></i>
      Sign In
    </button>
  </form>

  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const toggleIcon = document.getElementById('toggleIcon');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
      }
    }

    // Add loading state on form submit
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      const btn = this.querySelector('button[type="submit"]');
      btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Signing in...';
      btn.disabled = true;
    });
  </script>
</x-guest-layout>
