<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="description" content="Absensiku Pro Authentication Page">
  <title>Login | Absensiku Pro</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body class="auth-body">
  <button class="icon-button theme-toggle auth-theme-toggle" type="button" data-theme-toggle aria-label="Switch color theme" title="Switch color theme">
    <i class="bi bi-moon-stars" data-theme-icon aria-hidden="true"></i>
  </button>
  <main class="auth-page">
    <section class="auth-card">
      <a class="auth-brand" href="{{ url('/') }}">
        <span class="brand-icon"><i class="bi bi-person-check-fill" aria-hidden="true"></i></span>
        <span><strong>Absensiku Pro</strong><small>Sistem Presensi Geofencing & Biometrik</small></span>
      </a>

      
      @if(session('status'))
        <div class="alert alert-success mb-3">
          {{ session('status') }}
        </div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger mb-3">
          <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
        @csrf
        <div class="mb-4">
          <p class="eyebrow mb-1">Akses Terjaga & Aman</p>
          <h1 class="h3 mb-1">Masuk</h1>
          <p class="text-muted mb-0">Silakan masuk ke akun Absensiku Pro Anda.</p>
        </div>
        <div class="mb-3">
          <label class="form-label" for="loginNip">Nomor Induk Pegawai (NIP)</label>
          <input class="form-control @error('nip') is-invalid @enderror" id="loginNip" name="nip" type="text" value="{{ old('nip') }}" required autofocus placeholder="Contoh: ADMIN001 atau EMP2026001">
          <div class="invalid-feedback">Masukkan NIP akun Anda.</div>
        </div>
        <div class="mb-3">
          <div class="d-flex justify-content-between">
            <label class="form-label" for="loginPassword">Kata Sandi</label>
            <a class="small fw-semibold" href="#">Lupa kata sandi?</a>
          </div>
          <div class="input-group">
            <input class="form-control @error('password') is-invalid @enderror" id="loginPassword" name="password" type="password" minlength="6" required placeholder="••••••••">
            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('loginPassword', 'eyeIconLogin')" title="Tampilkan/Sembunyikan Kata Sandi">
              <i class="bi bi-eye" id="eyeIconLogin" aria-hidden="true"></i>
            </button>
          </div>
          <div class="invalid-feedback">Kata sandi minimal 6 karakter.</div>
        </div>
        <div class="form-check mb-4">
          <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" {{ old('remember') ? 'checked' : '' }}>
          <label class="form-check-label" for="rememberMe">Ingat saya</label>
        </div>
        <button class="btn btn-primary w-100" type="submit">
          <i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i> Masuk
        </button>
      </form>
      
      <div class="auth-footer">Belum memiliki akun? Hubungi Admin HR.</div>
    </section>
  </main>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
