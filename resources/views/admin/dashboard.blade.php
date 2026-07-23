<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Absensiku Pro - Admin Dashboard HRIS">
  <title>Dashboard Admin | Absensiku Pro</title>

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

      <!-- Dashboard Content (Mengikuti persis struktur asli template index.html) -->
      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">
          <!-- Page Heading -->
          <div class="page-heading">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1">OVERVIEW</p>
                <h1 class="h3 mb-1">Dashboard</h1>
                <p class="text-muted mb-0">Pantau status kehadiran karyawan, verifikasi biometrik, dan geofencing dalam satu workspace.</p>
              </div>
            </div>
            <div class="heading-actions">
              <button class="btn btn-outline-secondary btn-sm" type="button"><i class="bi bi-download" aria-hidden="true"></i> Export</button>
              <button class="btn btn-primary btn-sm" type="button"><i class="bi bi-file-earmark-plus" aria-hidden="true"></i> Create Report</button>
            </div>
          </div>

          <!-- Dashboard Metrics Cards (Menggunakan struktur persis metric-card template) -->
          <section class="row g-3 mt-1" aria-label="Dashboard metrics">
            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-primary">
                <div class="metric-top">
                  <span class="metric-label">TOTAL KARYAWAN</span>
                  <span class="metric-icon"><i class="bi bi-people-fill" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">128</div>
                <div class="metric-meta">
                  <span class="text-success">+100%</span>
                  <span>terdaftar aktif</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-success">
                <div class="metric-top">
                  <span class="metric-label">HADIR HARI INI</span>
                  <span class="metric-icon"><i class="bi bi-person-check-fill" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">112</div>
                <div class="metric-meta">
                  <span class="text-success">+87.5%</span>
                  <span>kehadiran harian</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-warning">
                <div class="metric-top">
                  <span class="metric-label">TERLAMBAT</span>
                  <span class="metric-icon"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">11</div>
                <div class="metric-meta">
                  <span class="text-warning">8.5%</span>
                  <span>terlambat masuk</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-danger">
                <div class="metric-top">
                  <span class="metric-label">DI LUAR RADIUS</span>
                  <span class="metric-icon"><i class="bi bi-geo-alt-fill" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">5</div>
                <div class="metric-meta">
                  <span class="text-danger">5 akun</span>
                  <span>perlu verifikasi</span>
                </div>
              </article>
            </div>
          </section>

          <!-- Panels Row 1: Peta Live & Aktivitas Presensi -->
          <section class="row g-3 mt-1">
            <div class="col-12 col-xl-8">
              <div class="panel">
                <div class="panel-header">
                  <div>
                    <h2 class="h5 mb-1 section-title"><i class="bi bi-map" aria-hidden="true"></i><span>Peta Persebaran Presensi Live</span></h2>
                    <p class="text-muted mb-0">Pantauan koordinat real-time geofencing karyawan di lokasi kantor.</p>
                  </div>
                  <a class="btn btn-light btn-sm" href="#">Lihat Detail</a>
                </div>

                <div class="chart-bars d-flex align-items-center justify-content-center text-center p-4" style="min-height: 250px;">
                  <div>
                    <i class="bi bi-geo-fill display-4 text-primary mb-2"></i>
                    <h6>Integrasi Leaflet.js Geofencing Map</h6>
                    <p class="text-muted small mb-0">Radius Kantor Utama: 50 Meter</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12 col-xl-4">
              <div class="panel h-100">
                <div class="panel-header">
                  <div>
                    <h2 class="h5 mb-1 section-title"><i class="bi bi-activity" aria-hidden="true"></i><span>Aktivitas Presensi</span></h2>
                    <p class="text-muted mb-0">Update log sistem terkini.</p>
                  </div>
                </div>

                <div class="activity-list">
                  <div class="activity-item">
                    <span class="activity-dot bg-primary"></span>
                    <div>
                      <p class="mb-1 fw-semibold">Presensi Masuk Dibuka</p>
                      <p class="text-muted small mb-0">Sistem otomatis membuka absensi pukul 06:00 WIB.</p>
                    </div>
                  </div>
                  <div class="activity-item">
                    <span class="activity-dot bg-success"></span>
                    <div>
                      <p class="mb-1 fw-semibold">Verifikasi Wajah Berhasil</p>
                      <p class="text-muted small mb-0">112 karyawan terverifikasi biometrik.</p>
                    </div>
                  </div>
                  <div class="activity-item">
                    <span class="activity-dot bg-warning"></span>
                    <div>
                      <p class="mb-1 fw-semibold">Peringatan Geofencing</p>
                      <p class="text-muted small mb-0">5 presensi terdeteksi di luar radius kantor.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Panels Row 2: Tabel Log Karyawan Terbaru (Panel Table Asli Template) -->
          <section class="panel mt-3">
            <div class="panel-header">
              <div>
                <h2 class="h5 mb-1 section-title"><i class="bi bi-people" aria-hidden="true"></i><span>Presensi Karyawan Terbaru</span></h2>
                <p class="text-muted mb-0">Aktivitas presensi dan skor biometrik karyawan ter-update.</p>
              </div>
              <a class="btn btn-outline-secondary btn-sm" href="#">Kelola Log</a>
            </div>
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th scope="col">Karyawan</th>
                    <th scope="col">Status</th>
                    <th scope="col">Waktu Presensi</th>
                    <th scope="col">Score Biometrik</th>
                    <th scope="col">Jarak Geofencing</th>
                    <th scope="col" class="text-end">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <img class="avatar-img avatar-sm" src="{{ asset('assets/images/avatar/avatar-1.jpg') }}" alt="Budi Santoso">
                        <div>
                          <p class="fw-semibold mb-0">Budi Santoso</p>
                          <p class="text-muted small mb-0">NIP. 19920101</p>
                        </div>
                      </div>
                    </td>
                    <td>Operational</td>
                    <td><span class="badge text-bg-secondary">07:55:12</span></td>
                    <td><span class="badge text-bg-success"><i class="bi bi-shield-check me-1"></i>98% Match</span></td>
                    <td><span class="text-success fw-semibold">12 meter</span></td>
                    <td class="text-end"><a class="btn btn-light btn-sm" href="#">View</a></td>
                  </tr>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <img class="avatar-img avatar-sm" src="{{ asset('assets/images/avatar/avatar-2.jpg') }}" alt="Siti Rahma">
                        <div>
                          <p class="fw-semibold mb-0">Siti Rahma</p>
                          <p class="text-muted small mb-0">NIP. 19930412</p>
                        </div>
                      </div>
                    </td>
                    <td>HR & Finance</td>
                    <td><span class="badge text-bg-secondary">08:02:45</span></td>
                    <td><span class="badge text-bg-success"><i class="bi bi-shield-check me-1"></i>95% Match</span></td>
                    <td><span class="text-success fw-semibold">28 meter</span></td>
                    <td class="text-end"><a class="btn btn-light btn-sm" href="#">View</a></td>
                  </tr>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <img class="avatar-img avatar-sm" src="{{ asset('assets/images/avatar/avatar-3.jpg') }}" alt="Ahmad Hidayat">
                        <div>
                          <p class="fw-semibold mb-0">Ahmad Hidayat</p>
                          <p class="text-muted small mb-0">NIP. 19900823</p>
                        </div>
                      </div>
                    </td>
                    <td>IT Support</td>
                    <td><span class="badge text-bg-secondary">08:16:30</span></td>
                    <td><span class="badge text-bg-danger"><i class="bi bi-exclamation-diamond me-1"></i>82% Warning</span></td>
                    <td><span class="text-danger fw-semibold">140 meter</span></td>
                    <td class="text-end"><a class="btn btn-light btn-sm" href="#">View</a></td>
                  </tr>
                </tbody>
              </table>
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
