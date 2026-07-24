<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Absensiku Pro - Dashboard HRIS Panel Admin">
  <title>Dashboard Admin | Absensiku Pro</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

  <!-- Leaflet.js CSS for Live Geofencing Map -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>

<body>
  <div class="admin-shell">
    <div class="sidebar-backdrop" data-sidebar-close></div>

    <!-- Sidebar Admin -->
    @include('admin.layouts.sidebar')

    <div class="admin-main">
      <!-- Navbar Top -->
      @include('admin.layouts.navbar')

      <!-- Dashboard Content -->
      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">
          <!-- Page Heading -->
          <div class="page-heading">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="bi bi-grid-1x2-fill" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1">WORKSPACE OVERVIEW</p>
                <h1 class="h3 mb-1">Dashboard Monitoring HRIS</h1>
                <p class="text-muted mb-0">Overview presensi real-time, verifikasi biometrik wajah, dan pemantauan geofencing kantor.</p>
              </div>
            </div>
          </div>

          <!-- Metrik Ringkasan Utama Dashboard -->
          <section class="row g-3 mt-1" aria-label="Key HRIS metrics">
            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-primary">
                <div class="metric-top">
                  <span class="metric-label">TOTAL KARYAWAN</span>
                  <span class="metric-icon"><i class="bi bi-people-fill" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $totalEmployees }}</div>
                <div class="metric-meta">
                  <span class="text-success">Aktif</span>
                  <span>terdaftar di sistem</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-success">
                <div class="metric-top">
                  <span class="metric-label">HADIR HARI INI</span>
                  <span class="metric-icon"><i class="bi bi-person-check-fill" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $presentToday }}</div>
                <div class="metric-meta">
                  <span class="text-success">Terverifikasi</span>
                  <span>presensi masuk</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-warning">
                <div class="metric-top">
                  <span class="metric-label">TERLAMBAT</span>
                  <span class="metric-icon"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $lateToday }}</div>
                <div class="metric-meta">
                  <span class="text-warning">Terlambat</span>
                  <span>masuk kerja</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-danger">
                <div class="metric-top">
                  <span class="metric-label">DI LUAR RADIUS</span>
                  <span class="metric-icon"><i class="bi bi-geo-alt-fill" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $outOfRadiusToday }}</div>
                <div class="metric-meta">
                  <span class="text-danger">Alert</span>
                  <span>perlu verifikasi</span>
                </div>
              </article>
            </div>
          </section>

          <!-- Panels Row 1: Interactive Leaflet Live Map & Aktivitas Presensi -->
          <section class="row g-3 mt-1">
            <div class="col-12 col-xl-8">
              <div class="panel h-100">
                <div class="panel-header">
                  <div>
                    <h2 class="h5 mb-1 section-title"><i class="bi bi-map" aria-hidden="true"></i><span>Peta Persebaran Presensi Live</span></h2>
                    <p class="text-muted mb-0">Pantauan koordinat real-time geofencing & sebaran presensi karyawan di lokasi kantor.</p>
                  </div>
                  <a class="btn btn-light btn-sm" href="{{ route('admin.geofences.index') }}">Lihat Detail Geofence</a>
                </div>

                <div class="p-3">
                  <div id="dashboardMap" style="height: 320px; width: 100%; border-radius: 8px;" class="shadow-sm border"></div>
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
                      <p class="text-muted small mb-0">Sistem otomatis membuka absensi pukul {{ $primaryGeofence->shift_start ?? '08:00' }} WIB.</p>
                    </div>
                  </div>
                  <div class="activity-item">
                    <span class="activity-dot bg-success"></span>
                    <div>
                      <p class="mb-1 fw-semibold">Verifikasi Wajah Berhasil</p>
                      <p class="text-muted small mb-0">{{ $presentToday }} karyawan terverifikasi biometrik.</p>
                    </div>
                  </div>
                  <div class="activity-item">
                    <span class="activity-dot bg-warning"></span>
                    <div>
                      <p class="mb-1 fw-semibold">Peringatan Geofencing</p>
                      <p class="text-muted small mb-0">{{ $outOfRadiusToday }} presensi terdeteksi di luar radius.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Panels Row 2: Tabel Log Karyawan Terbaru -->
          <section class="panel mt-3">
            <div class="panel-header">
              <div>
                <h2 class="h5 mb-1 section-title"><i class="bi bi-people" aria-hidden="true"></i><span>Presensi Karyawan Terbaru</span></h2>
                <p class="text-muted mb-0">Aktivitas presensi dan skor biometrik karyawan ter-update dari database.</p>
              </div>
              <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.attendance_logs.index') }}">Kelola Semua Log</a>
            </div>
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th scope="col">Karyawan</th>
                    <th scope="col">Jabatan</th>
                    <th scope="col">Tipe & Waktu</th>
                    <th scope="col">Status Presensi</th>
                    <th scope="col">Score Biometrik</th>
                    <th scope="col">Jarak Geofence</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($recentLogs as $log)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <img class="avatar-img avatar-sm" src="{{ $log->user->registered_face_photo ? asset($log->user->registered_face_photo) : asset('assets/images/avatar/avatar.jpg') }}" alt="{{ $log->user->name }}">
                        <div>
                          <p class="fw-semibold mb-0">{{ $log->user->name }}</p>
                          <small class="text-muted">NIP. {{ $log->user->nip }}</small>
                        </div>
                      </div>
                    </td>
                    <td>{{ $log->user->position ?? '-' }}</td>
                    <td>
                      <span class="badge text-bg-dark me-1">{{ $log->type }}</span>
                      <span class="badge text-bg-secondary">{{ $log->time }}</span>
                    </td>
                    <td>
                      @if($log->status == 'TEPAT_WAKTU')
                        <span class="badge text-bg-success"><i class="bi bi-check-circle me-1"></i>Tepat Waktu</span>
                      @elseif($log->status == 'TERLAMBAT')
                        <span class="badge text-bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Terlambat</span>
                      @elseif($log->status == 'PULANG_CEPAT')
                        <span class="badge text-bg-info"><i class="bi bi-box-arrow-right me-1"></i>Pulang Cepat</span>
                      @else
                        <span class="badge text-bg-danger"><i class="bi bi-geo-alt me-1"></i>Luar Radius</span>
                      @endif
                    </td>
                    <td>
                      @if($log->face_confidence_score)
                        <span class="badge text-bg-success"><i class="bi bi-shield-check me-1"></i>{{ $log->face_confidence_score }}% Match</span>
                      @else
                        <span class="text-muted small">N/A</span>
                      @endif
                    </td>
                    <td>
                      <span class="fw-semibold {{ $log->status == 'LUAR_RADIUS' ? 'text-danger' : 'text-success' }}">{{ $log->distance_from_office }} meter</span>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada data presensi di dalam database.</td>
                  </tr>
                  @endforelse
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

  <!-- Leaflet.js Interactive Map Script -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      var centerLat = {{ $primaryGeofence->lat ?? -6.2087634 }};
      var centerLng = {{ $primaryGeofence->lng ?? 106.8455992 }};
      var radius = {{ $primaryGeofence->radius_in_meters ?? 50 }};
      var officeName = "{{ $primaryGeofence->office_name ?? 'Kantor Utama' }}";

      var map = L.map('dashboardMap').setView([centerLat, centerLng], 15);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
      }).addTo(map);

      // Circle & Marker Kantor Utama
      L.circle([centerLat, centerLng], {
        color: '#2563eb',
        fillColor: '#3b82f6',
        fillOpacity: 0.2,
        radius: radius
      }).addTo(map);

      L.marker([centerLat, centerLng]).addTo(map)
        .bindPopup("<b>" + officeName + "</b><br>Titik Pusat Geofence (" + radius + "m)").openPopup();

      // Render marker lokasi presensi real karyawan dari database
      @foreach($mapLogs as $log)
        (function() {
          var lat = {{ $log->lat }};
          var lng = {{ $log->lng }};
          var isOutside = "{{ $log->status }}" === "LUAR_RADIUS";
          
          var circleMarker = L.circleMarker([lat, lng], {
            radius: 7,
            color: isOutside ? '#ef4444' : '#10b981',
            fillColor: isOutside ? '#f87171' : '#34d399',
            fillOpacity: 0.85
          }).addTo(map);

          circleMarker.bindPopup(
            "<b>{{ $log->user->name ?? 'Karyawan' }}</b><br>" +
            "Tipe: {{ $log->type }} ({{ $log->time }} WIB)<br>" +
            "Status: <strong style='color:" + (isOutside ? "#ef4444" : "#10b981") + "'>{{ $log->status }}</strong><br>" +
            "Jarak: {{ $log->distance_from_office }}m dari kantor"
          );
        })();
      @endforeach
    });
  </script>
</body>
</html>
