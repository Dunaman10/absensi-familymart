<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Absensiku Pro - Log Presensi Karyawan">
  <title>Log Presensi | Absensiku Pro</title>

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
      <nav class="navbar admin-navbar navbar-expand bg-white">
        <div class="container-fluid px-3 px-lg-4">
          <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-controls="adminSidebar" aria-expanded="true" aria-label="Toggle sidebar">
            <span></span>
            <span></span>
            <span></span>
          </button>

          <form class="d-none d-md-flex ms-3 flex-grow-1" role="search">
            <input class="form-control search-input" type="search" placeholder="Cari log presensi, NIP, atau tanggal..." aria-label="Search">
          </form>

          <div class="navbar-actions ms-auto">
            <button class="icon-button theme-toggle" type="button" data-theme-toggle aria-label="Switch color theme" title="Switch color theme">
              <i class="bi bi-moon-stars" data-theme-icon aria-hidden="true"></i>
            </button>
            <div class="dropdown">
              <button class="icon-button" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                <span class="notification-dot"></span>
                <i class="bi bi-bell" aria-hidden="true"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end notification-menu">
                <div class="dropdown-header fw-bold text-body">Notifications</div>
                <a class="dropdown-item" href="#">
                  <span class="notification-title">3 Karyawan Presensi di Luar Radius</span>
                  <span class="notification-time">4 menit lalu</span>
                </a>
              </div>
            </div>

            <div class="dropdown">
              <button class="profile-button dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img class="avatar-img avatar-sm" src="{{ asset('assets/images/avatar/avatar.jpg') }}" alt="Admin Hasan">
                <span class="profile-name d-none d-sm-inline">Admin Hasan</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li><a class="dropdown-item" href="#">Account settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <a class="dropdown-item text-danger" href="{{ route('login') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right me-1"></i> Sign out
                  </a>
                  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                  </form>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </nav>

      <!-- Dashboard Content - Log Presensi -->
      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">
          <!-- Page Heading -->
          <div class="page-heading">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="bi bi-calendar-check" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1">MONITORING</p>
                <h1 class="h3 mb-1">Log Presensi Karyawan</h1>
                <p class="text-muted mb-0">Riwayat audit presensi real-time, verifikasi biometrik wajah, dan jarak koordinat kantor.</p>
              </div>
            </div>
            <div class="heading-actions">
              <button class="btn btn-outline-secondary btn-sm" type="button"><i class="bi bi-funnel" aria-hidden="true"></i> Filter Tanggal</button>
              <button class="btn btn-primary btn-sm" type="button"><i class="bi bi-download" aria-hidden="true"></i> Export Laporan</button>
            </div>
          </div>

          <!-- Metrik Ringkasan Presensi -->
          <section class="row g-3 mt-1" aria-label="Attendance summary metrics">
            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-primary">
                <div class="metric-top">
                  <span class="metric-label">TOTAL PRESENSI</span>
                  <span class="metric-icon"><i class="bi bi-card-checklist" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">123</div>
                <div class="metric-meta">
                  <span class="text-success">+12 hari ini</span>
                  <span>log tercatat</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-success">
                <div class="metric-top">
                  <span class="metric-label">PRESENSI MASUK</span>
                  <span class="metric-icon"><i class="bi bi-box-arrow-in-right" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">112</div>
                <div class="metric-meta">
                  <span class="text-success">+91%</span>
                  <span>tepat waktu</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-warning">
                <div class="metric-top">
                  <span class="metric-label">PRESENSI PULANG</span>
                  <span class="metric-icon"><i class="bi bi-box-arrow-left" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">11</div>
                <div class="metric-meta">
                  <span class="text-warning">9% sesi selesai</span>
                  <span>sudah absen pulang</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-danger">
                <div class="metric-top">
                  <span class="metric-label">BEDA RADIUS</span>
                  <span class="metric-icon"><i class="bi bi-geo-alt" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">5</div>
                <div class="metric-meta">
                  <span class="text-danger">&gt; 50 meter</span>
                  <span>di luar geofence</span>
                </div>
              </article>
            </div>
          </section>

          <!-- Table Log Presensi Karyawan -->
          <section class="panel mt-3">
            <div class="panel-header">
              <div>
                <h2 class="h5 mb-1 section-title"><i class="bi bi-table" aria-hidden="true"></i><span>Riwayat Audit Presensi</span></h2>
                <p class="text-muted mb-0">Tabel log presensi read-only beserta tangkapan foto biometrik wajah dan koordinat.</p>
              </div>
              <div class="d-flex flex-wrap gap-2">
                <input class="form-control form-control-sm table-search" type="search" placeholder="Cari log presensi..." aria-label="Search logs">
              </div>
            </div>
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th scope="col">Karyawan</th>
                    <th scope="col">Tanggal & Waktu</th>
                    <th scope="col">Tipe Presensi</th>
                    <th scope="col">Skor Biometrik</th>
                    <th scope="col">Koordinat & Jarak</th>
                    <th scope="col">Foto Presensi</th>
                    <th scope="col" class="text-end">Aksi Audit</th>
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
                    <td>
                      <span class="fw-semibold d-block">23 Juli 2026</span>
                      <small class="text-muted">07:55:12 WIB</small>
                    </td>
                    <td><span class="badge text-bg-primary">MASUK</span></td>
                    <td><span class="badge text-bg-success"><i class="bi bi-shield-check me-1"></i>98% Match</span></td>
                    <td>
                      <span class="d-block text-success fw-semibold">12 meter (Dalam Radius)</span>
                      <small class="text-muted">-6.2088, 106.8456</small>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#photoModal"><i class="bi bi-camera me-1"></i> Lihat Foto</button>
                    </td>
                    <td class="text-end">
                      <button class="btn btn-light btn-sm"><i class="bi bi-info-circle"></i> Detail</button>
                    </td>
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
                    <td>
                      <span class="fw-semibold d-block">23 Juli 2026</span>
                      <small class="text-muted">08:02:45 WIB</small>
                    </td>
                    <td><span class="badge text-bg-primary">MASUK</span></td>
                    <td><span class="badge text-bg-success"><i class="bi bi-shield-check me-1"></i>95% Match</span></td>
                    <td>
                      <span class="d-block text-success fw-semibold">28 meter (Dalam Radius)</span>
                      <small class="text-muted">-6.2089, 106.8458</small>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#photoModal"><i class="bi bi-camera me-1"></i> Lihat Foto</button>
                    </td>
                    <td class="text-end">
                      <button class="btn btn-light btn-sm"><i class="bi bi-info-circle"></i> Detail</button>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <img class="avatar-img avatar-sm" src="{{ asset('assets/images/avatar/avatar-3.jpg') }}" alt="Ahmad Hidayat">
                        <div>
                          <p class="fw-semibold mb-0">Ahmad Hidayat</p>
                          <p class="text-muted small mb-0">NIP. 19900823</small>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="fw-semibold d-block">23 Juli 2026</span>
                      <small class="text-muted">08:16:30 WIB</small>
                    </td>
                    <td><span class="badge text-bg-primary">MASUK</span></td>
                    <td><span class="badge text-bg-danger"><i class="bi bi-exclamation-diamond me-1"></i>82% Warning</span></td>
                    <td>
                      <span class="d-block text-danger fw-semibold">140 meter (Luar Radius)</span>
                      <small class="text-muted">-6.2105, 106.8480</small>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#photoModal"><i class="bi bi-camera me-1"></i> Lihat Foto</button>
                    </td>
                    <td class="text-end">
                      <button class="btn btn-light btn-sm"><i class="bi bi-info-circle"></i> Detail</button>
                    </td>
                  </tr>
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
                    <td>
                      <span class="fw-semibold d-block">23 Juli 2026</span>
                      <small class="text-muted">17:01:05 WIB</small>
                    </td>
                    <td><span class="badge text-bg-warning">PULANG</span></td>
                    <td><span class="badge text-bg-success"><i class="bi bi-shield-check me-1"></i>99% Match</span></td>
                    <td>
                      <span class="d-block text-success fw-semibold">15 meter (Dalam Radius)</span>
                      <small class="text-muted">-6.2088, 106.8457</small>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#photoModal"><i class="bi bi-camera me-1"></i> Lihat Foto</button>
                    </td>
                    <td class="text-end">
                      <button class="btn btn-light btn-sm"><i class="bi bi-info-circle"></i> Detail</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>
        </div>
      </main>
    </div>
  </div>

  <!-- Modal Preview Foto Presensi Biometrik -->
  <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="photoModalLabel"><i class="bi bi-camera me-2 text-primary"></i>Foto Tangkapan Biometrik Wajah</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center p-4">
          <img src="{{ asset('assets/images/avatar/avatar.jpg') }}" class="img-fluid rounded shadow mb-3" style="max-height: 280px;" alt="Tangkapan Wajah Presensi">
          <div class="bg-body-tertiary p-3 rounded text-start">
            <p class="mb-1"><strong>Status Matching:</strong> <span class="badge text-bg-success">98% Match Confidence</span></p>
            <p class="mb-1"><strong>Waktu Capture:</strong> 23 Juli 2026 - 07:55:12 WIB</p>
            <p class="mb-0"><strong>Metode:</strong> WebRTC Face-API Biometric Detector</p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
