<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Absensiku Pro - Profil Pengguna">
  <title>Profil Saya | Absensiku Pro</title>

  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body>
  <div class="admin-shell">
    <div class="sidebar-backdrop" data-sidebar-close></div>

    <!-- Sidebar Admin -->
    @include('admin.layouts.sidebar')

    <div class="admin-main">
      <!-- Navbar Top -->
      @include('admin.layouts.navbar')

      <!-- Dashboard Content - Profile -->
      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">
          <!-- Page Heading -->
          <div class="page-heading">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="bi bi-person-badge" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1">ACCOUNT</p>
                <h1 class="h3 mb-1">Profil Saya</h1>
                <p class="text-muted mb-0">Kelola detail informasi pribadi, bio, dan kredensial akun administrator Anda.</p>
              </div>
            </div>
          </div>

          <section class="row g-3">
            <!-- Profile Card Sidebar Left -->
            <div class="col-12 col-xl-4">
              <div class="panel h-100 text-center profile-card p-3">
                <div class="profile-cover rounded mb-2 overflow-hidden" style="height: 90px; background: linear-gradient(135deg, #2563eb, #0f766e);"></div>
                <img class="avatar-img avatar-xl profile-photo rounded-circle shadow border border-3 border-body" src="{{ asset('assets/images/avatar/avatar.jpg') }}" alt="Admin Hasan" style="margin-top: -45px; width: 80px; height: 80px;">
                <h2 class="h5 mt-3 mb-1">Admin HR Hasan</h2>
                <p class="text-muted mb-3">Head of HR & System Administrator</p>
                <div class="d-flex justify-content-center gap-2">
                  <span class="badge text-bg-primary">Super Admin</span>
                  <span class="badge text-bg-success"><i class="bi bi-patch-check-fill me-1"></i>Terverifikasi</span>
                </div>
                <div class="info-list mt-4 text-start p-3 bg-body-tertiary rounded">
                  <div class="mb-2"><span>Email: </span><strong>admin.hr@absensiku.com</strong></div>
                  <div class="mb-2"><span>Departemen: </span><strong>Human Resources</strong></div>
                  <div class="mb-0"><span>Zona Waktu: </span><strong>Asia/Jakarta (WIB)</strong></div>
                </div>
              </div>
            </div>

            <!-- Profile Settings Form Right -->
            <div class="col-12 col-xl-8">
              <form class="panel needs-validation" novalidate>
                <div class="panel-header">
                  <div>
                    <h2 class="h5 mb-1 section-title"><i class="bi bi-person-gear" aria-hidden="true"></i><span>Edit Detail Profil</span></h2>
                    <p class="text-muted mb-0">Perbarui kontak dan informasi akun administrator Anda.</p>
                  </div>
                </div>

                <div class="p-3">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label" for="profileName">Nama Lengkap</label>
                      <input class="form-control" id="profileName" type="text" value="Admin HR Hasan" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label" for="profileEmail">Alamat Email</label>
                      <input class="form-control" id="profileEmail" type="email" value="admin.hr@absensiku.com" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label" for="profilePhone">Nomor Telepon</label>
                      <input class="form-control" id="profilePhone" type="text" value="+62 812-3456-7890">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label" for="profileRole">Role Akses</label>
                      <input class="form-control" id="profileRole" type="text" value="Administrator HR" readonly disabled>
                    </div>
                    <div class="col-12">
                      <label class="form-label" for="profileBio">Bio Ringkas</label>
                      <textarea class="form-control" id="profileBio" rows="4">Mengelola sistem presensi karyawan berbasis Geofencing dan validasi Biometrik Wajah di lingkungan kerja Absensiku Pro.</textarea>
                    </div>
                  </div>
                  <div class="d-flex justify-content-end mt-4">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-check2-circle me-1" aria-hidden="true"></i> Simpan Perubahan Profil</button>
                  </div>
                </div>
              </form>
            </div>
          </section>
        </div>
      </main>
    </div>
  </div>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
