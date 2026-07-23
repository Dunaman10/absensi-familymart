<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Absensiku Pro - Pengaturan Sistem">
  <title>Pengaturan Sistem | Absensiku Pro</title>

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

      <!-- Dashboard Content - Settings -->
      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">
          <!-- Page Heading -->
          <div class="page-heading">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="bi bi-gear" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1">WORKSPACE</p>
                <h1 class="h3 mb-1">Pengaturan Sistem</h1>
                <p class="text-muted mb-0">Konfigurasi parameter aplikasi, toleransi geofencing, otentikasi biometrik, dan preferensi notifikasi.</p>
              </div>
            </div>
          </div>

          <section class="row g-3">
            <!-- Form Pengaturan Umum Workspace -->
            <div class="col-12 col-xl-6">
              <form class="panel needs-validation" novalidate>
                <div class="panel-header">
                  <div>
                    <h2 class="h5 mb-1 section-title"><i class="bi bi-sliders" aria-hidden="true"></i><span>Pengaturan Umum HRIS</span></h2>
                    <p class="text-muted mb-0">Atur identitas sistem dan zona waktu operasional presensi.</p>
                  </div>
                </div>

                <div class="p-3">
                  <div class="mb-3">
                    <label class="form-label" for="appName">Nama Aplikasi HRIS</label>
                    <input class="form-control" id="appName" type="text" value="Absensiku Pro V2" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="timeZone">Zona Waktu Operasional</label>
                    <select class="form-select" id="timeZone">
                      <option selected>Asia/Jakarta (WIB - UTC+7)</option>
                      <option>Asia/Makassar (WITA - UTC+8)</option>
                      <option>Asia/Jayapura (WIT - UTC+9)</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="officeHours">Jam Masuk Default</label>
                    <input class="form-control" id="officeHours" type="time" value="08:00">
                  </div>
                  <button class="btn btn-primary" type="submit"><i class="bi bi-check2-circle me-1" aria-hidden="true"></i> Simpan Pengaturan</button>
                </div>
              </form>
            </div>

            <!-- Panel Preferensi Keamanan & Biometrik -->
            <div class="col-12 col-xl-6">
              <div class="panel h-100">
                <div class="panel-header">
                  <div>
                    <h2 class="h5 mb-1 section-title"><i class="bi bi-shield-check" aria-hidden="true"></i><span>Opsi Keamanan & Biometrik</span></h2>
                    <p class="text-muted mb-0">Kontrol validasi Face Detection dan geofencing radius.</p>
                  </div>
                </div>
                <div class="settings-list p-3">
                  <label class="settings-row d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span>
                      <strong class="d-block">Otentikasi Wajah Biometrik (Face-API)</strong>
                      <small class="text-muted">Wajibkan scan wajah sebelum tombol presensi aktif.</small>
                    </span>
                    <input class="form-check-input ms-3" type="checkbox" checked>
                  </label>
                  <label class="settings-row d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span>
                      <strong class="d-block">Strict Geofencing Anti Fake GPS</strong>
                      <small class="text-muted">Tolak presensi otomatis jika koordinat melenceng dari radius kantor.</small>
                    </span>
                    <input class="form-check-input ms-3" type="checkbox" checked>
                  </label>
                  <label class="settings-row d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span>
                      <strong class="d-block">Notifikasi Email Pelanggaran</strong>
                      <small class="text-muted">Kirim laporan otomatis jika karyawan presensi di luar radius.</small>
                    </span>
                    <input class="form-check-input ms-3" type="checkbox" checked>
                  </label>
                  <label class="settings-row d-flex justify-content-between align-items-center py-2">
                    <span>
                      <strong class="d-block">Otentikasi Dua Faktor (2FA) Admin</strong>
                      <small class="text-muted">Wajibkan kode tambahan saat login ke dashboard HRIS.</small>
                    </span>
                    <input class="form-check-input ms-3" type="checkbox">
                  </label>
                </div>
              </div>
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
