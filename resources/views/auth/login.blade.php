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
            <a class="small fw-semibold text-primary" href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Lupa kata sandi?</a>
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Forgot Password Modal -->
  <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-bottom-0 pb-0">
          <h5 class="modal-title h5" id="forgotPasswordModalLabel"><i class="bi bi-shield-lock text-primary me-2"></i>Lupa Kata Sandi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted small mb-4">Masukkan NIP Anda. Kami akan mengirimkan kode OTP ke email yang terdaftar untuk mengatur ulang kata sandi Anda.</p>
          <form id="forgotPasswordForm">
            @csrf
            <div class="mb-3">
              <label for="resetNip" class="form-label">Nomor Induk Pegawai (NIP)</label>
              <input type="text" class="form-control" id="resetNip" name="nip" placeholder="Contoh: ADMIN001" required>
            </div>
            <button type="submit" class="btn btn-primary w-100" id="btnRequestOtp">
              Kirim Kode OTP <i class="bi bi-envelope ms-1"></i>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Verify OTP Modal -->
  <div class="modal fade" id="verifyOtpModal" tabindex="-1" aria-labelledby="verifyOtpModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-bottom-0 pb-0">
          <h5 class="modal-title h5" id="verifyOtpModalLabel"><i class="bi bi-envelope-open text-warning me-2"></i>Verifikasi OTP</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted small mb-4">Masukkan kode OTP 6 digit dari email Anda. <br><span class="text-warning fw-semibold"><i class="bi bi-clock-history me-1"></i>Kode hanya berlaku selama 15 menit.</span></p>
          <form id="verifyOtpForm">
            @csrf
            <div class="mb-4">
              <label for="otpCode" class="form-label">Kode OTP</label>
              <input type="text" class="form-control text-center fs-4 tracking-widest fw-bold" id="otpCode" name="otp" maxlength="6" placeholder="000000" required style="letter-spacing: 5px;">
            </div>
            <button type="submit" class="btn btn-warning w-100 fw-bold" id="btnVerifyOtp">
              Verifikasi Kode <i class="bi bi-arrow-right ms-1"></i>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Reset Password Modal -->
  <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-bottom-0 pb-0">
          <h5 class="modal-title h5" id="resetPasswordModalLabel"><i class="bi bi-check-circle text-success me-2"></i>Buat Sandi Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted small mb-4">Kode valid! Silakan masukkan kata sandi baru untuk akun Anda.</p>
          <form id="resetPasswordForm">
            @csrf
            <div class="mb-3">
              <label for="newPassword" class="form-label">Kata Sandi Baru</label>
              <div class="input-group">
                <input type="password" class="form-control" id="newPassword" name="password" minlength="6" placeholder="••••••••" required>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('newPassword', 'eyeIconNewPwd')">
                  <i class="bi bi-eye" id="eyeIconNewPwd"></i>
                </button>
              </div>
            </div>
            <div class="mb-4">
              <label for="confirmPassword" class="form-label">Konfirmasi Kata Sandi Baru</label>
              <div class="input-group">
                <input type="password" class="form-control" id="confirmPassword" name="password_confirmation" minlength="6" placeholder="••••••••" required>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('confirmPassword', 'eyeIconConfPwd')">
                  <i class="bi bi-eye" id="eyeIconConfPwd"></i>
                </button>
              </div>
            </div>
            <button type="submit" class="btn btn-success w-100" id="btnResetPassword">
              Simpan Kata Sandi Baru <i class="bi bi-check2-circle ms-1"></i>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('forgotPasswordForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const btn = document.getElementById('btnRequestOtp');
      const originalText = btn.innerHTML;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Mengirim...';
      btn.disabled = true;

      const formData = new FormData(this);
      
      try {
        const response = await fetch('{{ route('password.email') }}', {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          },
          body: formData
        });
        
        const data = await response.json();
        
        if (response.ok) {
          bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal')).hide();
          Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: data.message,
            timer: 2000,
            showConfirmButton: false,
            background: document.body.classList.contains('dark-theme') ? '#1e293b' : '#fff',
            color: document.body.classList.contains('dark-theme') ? '#f8fafc' : '#000'
          }).then(() => {
            new bootstrap.Modal(document.getElementById('verifyOtpModal')).show();
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: data.message || data.errors?.nip?.[0] || 'Terjadi kesalahan.',
            background: document.body.classList.contains('dark-theme') ? '#1e293b' : '#fff',
            color: document.body.classList.contains('dark-theme') ? '#f8fafc' : '#000'
          });
        }
      } catch (error) {
        Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
      } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
      }
    });

    document.getElementById('verifyOtpForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const btn = document.getElementById('btnVerifyOtp');
      const originalText = btn.innerHTML;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memverifikasi...';
      btn.disabled = true;

      const formData = new FormData(this);
      
      try {
        const response = await fetch('{{ route('password.verify-otp') }}', {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          },
          body: formData
        });
        
        const data = await response.json();
        
        if (response.ok) {
          bootstrap.Modal.getInstance(document.getElementById('verifyOtpModal')).hide();
          Swal.fire({
            icon: 'success',
            title: 'OTP Valid!',
            text: data.message,
            timer: 1500,
            showConfirmButton: false,
            background: document.body.classList.contains('dark-theme') ? '#1e293b' : '#fff',
            color: document.body.classList.contains('dark-theme') ? '#f8fafc' : '#000'
          }).then(() => {
            new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
          });
        } else {
          let errorText = data.message || 'Terjadi kesalahan.';
          if (data.errors && data.errors.otp) {
            errorText = data.errors.otp[0];
          }
          
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: errorText,
            background: document.body.classList.contains('dark-theme') ? '#1e293b' : '#fff',
            color: document.body.classList.contains('dark-theme') ? '#f8fafc' : '#000'
          });
        }
      } catch (error) {
        Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
      } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
      }
    });

    document.getElementById('resetPasswordForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const btn = document.getElementById('btnResetPassword');
      const originalText = btn.innerHTML;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';
      btn.disabled = true;

      const formData = new FormData(this);
      
      try {
        const response = await fetch('{{ route('password.update') }}', {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          },
          body: formData
        });
        
        const data = await response.json();
        
        if (response.ok) {
          bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal')).hide();
          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: data.message,
            background: document.body.classList.contains('dark-theme') ? '#1e293b' : '#fff',
            color: document.body.classList.contains('dark-theme') ? '#f8fafc' : '#000'
          }).then(() => {
            window.location.reload();
          });
        } else {
          let errorText = data.message || 'Terjadi kesalahan.';
          if (data.errors && data.errors.password) {
            errorText = data.errors.password[0];
          }
          
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: errorText,
            background: document.body.classList.contains('dark-theme') ? '#1e293b' : '#fff',
            color: document.body.classList.contains('dark-theme') ? '#f8fafc' : '#000'
          });
        }
      } catch (error) {
        Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
      } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
      }
    });
  </script>
</body>
</html>
