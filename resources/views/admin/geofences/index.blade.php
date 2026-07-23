<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Absensiku Pro - Konfigurasi Geofencing & Lokasi Kantor">
  <title>Geofencing & Lokasi | Absensiku Pro</title>

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
            <input class="form-control search-input" type="search" placeholder="Cari titik lokasi kantor..." aria-label="Search">
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
                  <span class="notification-title">Radius Lokasi Utama Diperbarui</span>
                  <span class="notification-time">2 jam lalu</span>
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

      <!-- Dashboard Content - Geofencing & Lokasi -->
      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">
          <!-- Page Heading -->
          <div class="page-heading">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="bi bi-geo-alt" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1">CONFIGURATION</p>
                <h1 class="h3 mb-1">Geofencing & Lokasi Kantor</h1>
                <p class="text-muted mb-0">Atur titik koordinat lokasi kantor (Latitude/Longitude) dan batas radius aman (meter) untuk presensi karyawan.</p>
              </div>
            </div>
            <div class="heading-actions">
              <button class="btn btn-outline-secondary btn-sm" type="button"><i class="bi bi-arrow-clockwise" aria-hidden="true"></i> Reset Peta</button>
              <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#locationModal"><i class="bi bi-geo-fill" aria-hidden="true"></i> Tambah Lokasi Kantor</button>
            </div>
          </div>

          <!-- Metrik Ringkasan Geofencing (PRD F-ADM-03) -->
          <section class="row g-3 mt-1" aria-label="Geofencing metrics">
            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-primary">
                <div class="metric-top">
                  <span class="metric-label">LOKASI TERDAFTAR</span>
                  <span class="metric-icon"><i class="bi bi-building" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">3</div>
                <div class="metric-meta">
                  <span class="text-success">3 titik</span>
                  <span>area presensi aktif</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-success">
                <div class="metric-top">
                  <span class="metric-label">RADIUS AMAN UTAMA</span>
                  <span class="metric-icon"><i class="bi bi-bounding-box-circles" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">50m</div>
                <div class="metric-meta">
                  <span class="text-success">Toleransi Haversine</span>
                  <span>Kantor Pusat</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-warning">
                <div class="metric-top">
                  <span class="metric-label">KOORDINAT KANTOR</span>
                  <span class="metric-icon"><i class="bi bi-pin-map" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value" style="font-size: 1.15rem;">-6.2088, 106.8456</div>
                <div class="metric-meta">
                  <span class="text-warning">GPS High-Accuracy</span>
                  <span>Lat / Lng</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-danger">
                <div class="metric-top">
                  <span class="metric-label">PELANGGARAN RADIUS</span>
                  <span class="metric-icon"><i class="bi bi-shield-x" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">5</div>
                <div class="metric-meta">
                  <span class="text-danger">5 deteksi hari ini</span>
                  <span>di luar geofence</span>
                </div>
              </article>
            </div>
          </section>

          <!-- Main Layout: Map Interactive Editor & Form Konfigurasi -->
          <section class="row g-4 mt-1">
            <!-- Interaktif Leaflet Map Editor -->
            <div class="col-12 col-xl-7">
              <div class="panel h-100">
                <div class="panel-header">
                  <div>
                    <h2 class="h5 mb-1 section-title"><i class="bi bi-map" aria-hidden="true"></i><span>Editor Peta Geofencing Interaktif</span></h2>
                    <p class="text-muted mb-0">Geser marker untuk memperbarui koordinat Latitude & Longitude secara otomatis.</p>
                  </div>
                  <span class="badge text-bg-success">Leaflet.js + OSM Engine</span>
                </div>

                <div class="chart-bars d-flex align-items-center justify-content-center text-center p-4" style="min-height: 360px;">
                  <div>
                    <i class="bi bi-pin-map-fill display-3 text-primary mb-3"></i>
                    <h5>Integrasi Leaflet.js Interactive Map Picker</h5>
                    <p class="text-muted small mb-3">Tampilan peta interaktif dengan lingkaran radius (Geofence Circle Overlay) akan dirender di sini pada fase Backend.</p>
                    <button class="btn btn-outline-primary btn-sm"><i class="bi bi-crosshair me-1"></i> Deteksi Lokasi Saya Saat Ini</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Konfigurasi Geofencing (PRD F-ADM-03) -->
            <div class="col-12 col-xl-5">
              <div class="panel h-100">
                <div class="panel-header">
                  <div>
                    <h2 class="h5 mb-1 section-title"><i class="bi bi-sliders" aria-hidden="true"></i><span>Form Titik Presensi</span></h2>
                    <p class="text-muted mb-0">Pengaturan parameter geofencing aktif.</p>
                  </div>
                </div>

                <div class="p-3">
                  <form id="geofenceForm">
                    <div class="mb-3">
                      <label for="officeName" class="form-label">Nama Lokasi / Cabang Kantor</label>
                      <input type="text" class="form-control" id="officeName" value="Kantor Pusat Absensiku Pro" required>
                    </div>

                    <div class="row g-2 mb-3">
                      <div class="col-md-6">
                        <label for="latitude" class="form-label">Latitude (Garis Lintang)</label>
                        <input type="text" class="form-control fw-mono" id="latitude" value="-6.2087634" required>
                      </div>
                      <div class="col-md-6">
                        <label for="longitude" class="form-label">Longitude (Garis Bujur)</label>
                        <input type="text" class="form-control fw-mono" id="longitude" value="106.8455992" required>
                      </div>
                    </div>

                    <div class="mb-3">
                      <label for="radiusMeters" class="form-label">Batas Radius Presensi (Meter)</label>
                      <div class="input-group">
                        <input type="number" class="form-control" id="radiusMeters" value="50" min="5" max="5000" required>
                        <span class="input-group-text">Meter</span>
                      </div>
                      <small class="text-muted d-block mt-1">Sistem Haversine akan menolak presensi jika jarak user > radius di atas.</small>
                    </div>

                    <div class="mb-4">
                      <label for="geofenceStatus" class="form-label">Status Geofencing</label>
                      <select class="form-select" id="geofenceStatus">
                        <option value="1" selected>Aktif (Wajibkan Geofencing)</option>
                        <option value="0">Bebas Lokasi (Bypass Radius)</option>
                      </select>
                    </div>

                    <button type="button" class="btn btn-primary w-100">
                      <i class="bi bi-save me-1"></i> Simpan Konfigurasi Geofencing
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </section>

          <!-- Table Daftar Lokasi Kantor Terdaftar -->
          <section class="panel mt-3">
            <div class="panel-header">
              <div>
                <h2 class="h5 mb-1 section-title"><i class="bi bi-building-gear" aria-hidden="true"></i><span>Daftar Titik Lokasi Kantor</span></h2>
                <p class="text-muted mb-0">Kelola cabang kantor yang memiliki aturan geofencing presensi tersendiri.</p>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th scope="col">Nama Lokasi</th>
                    <th scope="col">Koordinat (Lat, Lng)</th>
                    <th scope="col">Radius Aman</th>
                    <th scope="col">Status Radius</th>
                    <th scope="col" class="text-end">Aksi Editor</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <strong class="d-block">Kantor Pusat Jakarta</strong>
                      <small class="text-muted">Jl. Sudirman No. 45, Jakarta Selatan</small>
                    </td>
                    <td><span class="fw-mono text-muted">-6.20876, 106.84559</span></td>
                    <td><span class="badge text-bg-primary">50 Meter</span></td>
                    <td><span class="badge text-bg-success">Aktif</span></td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-light btn-sm text-primary" title="Edit Lokasi"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-light btn-sm text-danger" title="Hapus Lokasi"><i class="bi bi-trash"></i></button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <strong class="d-block">Cabang Bandung</strong>
                      <small class="text-muted">Jl. Asia Afrika No. 12, Bandung</small>
                    </td>
                    <td><span class="fw-mono text-muted">-6.91746, 107.61912</span></td>
                    <td><span class="badge text-bg-primary">100 Meter</span></td>
                    <td><span class="badge text-bg-success">Aktif</span></td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-light btn-sm text-primary" title="Edit Lokasi"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-light btn-sm text-danger" title="Hapus Lokasi"><i class="bi bi-trash"></i></button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <strong class="d-block">Cabang Surabaya</strong>
                      <small class="text-muted">Jl. Tunjungan No. 88, Surabaya</small>
                    </td>
                    <td><span class="fw-mono text-muted">-7.25747, 112.75209</span></td>
                    <td><span class="badge text-bg-primary">75 Meter</span></td>
                    <td><span class="badge text-bg-secondary">Non-Aktif</span></td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-light btn-sm text-primary" title="Edit Lokasi"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-light btn-sm text-danger" title="Hapus Lokasi"><i class="bi bi-trash"></i></button>
                      </div>
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

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
