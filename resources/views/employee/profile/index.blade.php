<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="description" content="Absensiku Pro - Profil Karyawan">
  <title>Profil Saya | Absensiku Pro</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <div class="admin-shell">
    <div class="sidebar-backdrop" data-sidebar-close></div>

    <!-- Sidebar Navigation -->
    @include('employee.layouts.sidebar')

    <div class="admin-main">
      <!-- Navbar Top -->
      @include('employee.layouts.navbar')

      <!-- Dashboard Content - Employee Profile -->
      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">
          <!-- Page Heading -->
          <div class="page-heading mb-4">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="bi bi-person-badge" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1">ACCOUNT</p>
                <h1 class="h3 mb-1">Profil Saya</h1>
                <p class="text-muted mb-0">Kelola detail informasi diri, foto biometrik, dan password akun karyawan Anda.</p>
              </div>
            </div>
          </div>

          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
              <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
              <div class="d-flex align-items-center mb-1">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <strong class="h6 mb-0">Terdapat kesalahan pada input Anda:</strong>
              </div>
              <ul class="mb-0 ps-4 small">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          <section class="row g-3">
            <!-- Profile Card Sidebar Left -->
            <div class="col-12 col-xl-4">
              <div class="panel h-100 text-center profile-card p-3">
                <div class="profile-cover rounded mb-2 overflow-hidden" style="height: 90px; background: linear-gradient(135deg, #2563eb, #0f766e);"></div>
                
                <div id="avatarContainerLeft" class="position-relative d-inline-block" style="margin-top: -45px;">
                  @if($user->registered_face_photo)
                    <img id="avatarPreviewLeft" class="avatar-img avatar-xl profile-photo rounded-circle shadow border border-3 border-body" src="{{ asset($user->registered_face_photo) }}" alt="{{ $user->name }}" style="width: 84px; height: 84px; object-fit: cover;">
                  @else
                    <div id="avatarInitLeft" class="avatar-xl rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow border border-3 border-body mx-auto" style="width: 84px; height: 84px; font-size: 2rem;">
                      {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                  @endif
                </div>

                <h2 class="h5 mt-3 mb-1 fw-bold">{{ $user->name }}</h2>
                <p class="text-muted mb-3">{{ $user->position ?? 'Staff Karyawan' }}</p>
                <div class="d-flex justify-content-center gap-2">
                  <span class="badge text-bg-secondary">Karyawan</span>
                  @if($user->account_status == 'Aktif')
                    <span class="badge text-bg-success"><i class="bi bi-patch-check-fill me-1"></i>Biometrik Aktif</span>
                  @else
                    <span class="badge text-bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>Pending Biometrik</span>
                  @endif
                </div>
                <div class="info-list mt-4 text-start p-3 bg-body-tertiary rounded-3">
                  <div class="mb-2.5 d-flex justify-content-between">
                    <span class="text-muted">NIP Username:</span>
                    <strong class="fw-mono">{{ $user->nip }}</strong>
                  </div>
                  <div class="mb-2.5 d-flex justify-content-between">
                    <span class="text-muted">Jabatan / Tim:</span>
                    <strong>{{ $user->position ?? 'Staff Karyawan' }}</strong>
                  </div>
                  <div class="mb-0 d-flex justify-content-between">
                    <span class="text-muted">Status Biometrik:</span>
                    <span class="badge {{ $user->account_status == 'Aktif' ? 'text-bg-success' : 'text-bg-warning text-dark' }}">{{ $user->account_status }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Profile Settings Form Right -->
            <div class="col-12 col-xl-8">
              <form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data" class="panel h-100 d-flex flex-column">
                @csrf
                @method('PUT')
                <div class="panel-header">
                  <div>
                    <h2 class="h5 mb-1 section-title"><i class="bi bi-person-gear" aria-hidden="true"></i><span>Edit Detail Profil Saya</span></h2>
                    <p class="text-muted mb-0">Perbarui informasi nama lengkap, jabatan, dan foto profil biometrik Anda.</p>
                  </div>
                </div>

                <div class="p-4 flex-grow-1">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label fw-semibold" for="profileName">Nama Lengkap</label>
                      <input class="form-control" id="profileName" name="name" type="text" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-semibold" for="profileNip">NIP (Username Login)</label>
                      <input class="form-control fw-mono bg-body-tertiary" id="profileNip" type="text" value="{{ $user->nip }}" readonly disabled title="NIP dibuat otomatis oleh sistem">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-semibold" for="profileEmail">Alamat Email</label>
                      <input class="form-control" id="profileEmail" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-semibold" for="profilePosition">Jabatan / Tim</label>
                      <input class="form-control" id="profilePosition" name="position" type="text" value="{{ old('position', $user->position ?? 'Staff Karyawan') }}">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-semibold" for="profileRole">Role Akses</label>
                      <input class="form-control bg-body-tertiary" id="profileRole" type="text" value="Karyawan (User)" readonly disabled>
                    </div>

                    <!-- Upload Foto Profile Section -->
                    <div class="col-12 mt-4">
                      <label class="form-label fw-semibold" for="profilePhotoInput">Upload Foto Profil / Biometrik Baru</label>
                      <div class="input-group">
                        <input type="file" class="form-control" id="profilePhotoInput" name="profile_photo" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewUploadImage(this)">
                        <label class="input-group-text" for="profilePhotoInput"><i class="bi bi-upload"></i></label>
                      </div>
                      <small class="text-muted mt-1 d-block" style="font-size: 0.78rem;">Format didukung: JPG, PNG, WEBP (Maksimal 1MB).</small>
                    </div>
                  </div>

                  <hr class="my-4 opacity-25">

                  <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                      <i class="bi bi-key me-1"></i> Ganti Password
                    </button>
                    <button class="btn btn-primary px-4" type="submit">
                      <i class="bi bi-check2-circle me-1" aria-hidden="true"></i> Simpan Perubahan Profil
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </section>
        </div>
      </main>
    </div>
  </div>

  <!-- Modal Ganti Password Karyawan -->
  <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="{{ route('employee.profile.password') }}" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title fw-bold" id="changePasswordModalLabel"><i class="bi bi-key me-2 text-warning"></i>Ganti Password Akun Karyawan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="currentPassword" class="form-label fw-semibold">Password Saat Ini</label>
              <div class="input-group">
                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="currentPassword" name="current_password" placeholder="••••••••" required>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('currentPassword', 'eyeIconCurrent')">
                  <i class="bi bi-eye" id="eyeIconCurrent"></i>
                </button>
              </div>
              @error('current_password')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="newPassword" class="form-label fw-semibold">Password Baru</label>
              <div class="input-group">
                <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="newPassword" name="new_password" placeholder="••••••••" required minlength="6">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('newPassword', 'eyeIconNew')">
                  <i class="bi bi-eye" id="eyeIconNew"></i>
                </button>
              </div>
              <small class="text-muted" style="font-size: 0.75rem;">Password minimal 6 karakter.</small>
              @error('new_password')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="newPasswordConfirmation" class="form-label fw-semibold">Konfirmasi Password Baru</label>
              <div class="input-group">
                <input type="password" class="form-control" id="newPasswordConfirmation" name="new_password_confirmation" placeholder="••••••••" required minlength="6">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('newPasswordConfirmation', 'eyeIconConfirm')">
                  <i class="bi bi-eye" id="eyeIconConfirm"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-warning btn-sm fw-semibold text-dark"><i class="bi bi-shield-check me-1"></i> Simpan Password Baru</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script>
    function previewUploadImage(input) {
      if (input.files && input.files[0]) {
        const file = input.files[0];
        const maxSize = 1024 * 1024; // 1MB

        if (file.size > maxSize) {
          const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
          Swal.fire({
            title: 'Ukuran File Terlalu Besar',
            html: `File foto yang Anda pilih berukuran <strong>${fileSizeMB} MB</strong>. Batas maksimal yang diizinkan adalah <strong>1 MB</strong>. Harap pilih foto lain yang lebih kecil.`,
            icon: 'warning',
            confirmButtonText: 'Saya Mengerti',
            background: '#1e293b',
            color: '#f8fafc',
            customClass: {
              popup: 'border border-secondary border-opacity-25 rounded-4 shadow-lg',
              confirmButton: 'btn btn-primary btn-sm rounded-pill px-4 shadow-sm'
            },
            buttonsStyling: false
          });
          input.value = '';
          return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
          const avatarContainer = document.getElementById('avatarContainerLeft');
          if (avatarContainer) {
            avatarContainer.innerHTML = `<img id="avatarPreviewLeft" class="avatar-img avatar-xl profile-photo rounded-circle shadow border border-3 border-body" src="${e.target.result}" alt="Preview" style="width: 84px; height: 84px; object-fit: cover;">`;
          }
        }
        reader.readAsDataURL(file);
      }
    }

    function togglePasswordVisibility(inputId, iconId) {
      const input = document.getElementById(inputId);
      const icon = document.getElementById(iconId);
      if (input && icon) {
        if (input.type === 'password') {
          input.type = 'text';
          icon.classList.remove('bi-eye');
          icon.classList.add('bi-eye-slash');
        } else {
          input.type = 'password';
          icon.classList.remove('bi-eye-slash');
          icon.classList.add('bi-eye');
        }
      }
    }

    @if(session('error_modal'))
      document.addEventListener("DOMContentLoaded", function() {
        var myModal = new bootstrap.Modal(document.getElementById("{{ session('error_modal') }}"));
        myModal.show();
      });
    @endif
  </script>
</body>
</html>
