<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Absensiku Pro - Dashboard Portal Presensi Karyawan">
  <title>Dashboard Karyawan | Absensiku Pro</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

  <style>
    .status-pill {
      font-size: 0.85rem;
      font-weight: 600;
      padding: 0.4rem 0.85rem;
      border-radius: 50rem;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
    }

    .badge-geofence-in {
      background-color: rgba(16, 185, 129, 0.15);
      color: #10b981;
      border: 1px solid rgba(16, 185, 129, 0.3);
    }
  </style>
</head>

<body>
  <div class="admin-shell">
    <div class="sidebar-backdrop" data-sidebar-close></div>

    <!-- Sidebar Navigation -->
    @include('employee.layouts.sidebar')

    <div class="admin-main">
      <!-- Navbar Top -->
      @include('employee.layouts.navbar')

      <!-- Dashboard Content -->
      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">

          <!-- F-EMP-01: Header & Status Harian -->
          <div class="page-heading mb-4">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1">PORTAL KARYAWAN</p>
                <h1 class="h3 mb-1">Halo, {{ $user->name }} 👋</h1>
                <p class="text-muted mb-0"><i class="bi bi-calendar3 me-1"></i> {{ $todayStatus['date_formatted'] }} &bull; <span id="liveClock" class="fw-semibold text-primary">00:00:00 WIB</span></p>
              </div>
            </div>
            <div class="heading-actions align-items-center">
              <span class="status-pill {{ $todayStatus['status'] == 'Sudah Absen Masuk' || $todayStatus['status'] == 'Sudah Absen Pulang' ? 'badge-geofence-in' : 'bg-warning-subtle text-warning border border-warning-subtle' }}">
                <span class="status-dot"></span> {{ $todayStatus['status'] }}
              </span>
            </div>
          </div>

          <!-- Quick Banner CTA ke Kamera Presensi -->
          <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, rgba(37, 99, 235, 0.15), rgba(16, 185, 129, 0.1)); border: 1px solid rgba(37, 99, 235, 0.3) !important;">
            <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
              <div>
                <span class="badge text-bg-primary mb-2"><i class="bi bi-camera-video me-1"></i> Quick Action</span>
                <h5 class="fw-bold mb-1">Siap Lakukan Presensi Hari Ini?</h5>
                <p class="text-muted mb-0 small">Buka menu <strong>Kamera Presensi</strong> untuk melakukan verifikasi biometrik wajah & koordinat geofencing kantor.</p>
              </div>
              <a href="{{ route('employee.attendance.scan') }}" class="btn btn-primary rounded-3 px-4 py-2.5 fw-semibold shadow-sm flex-shrink-0">
                <i class="bi bi-camera me-2"></i> Buka Kamera Presensi <i class="bi bi-arrow-right ms-1"></i>
              </a>
            </div>
          </div>

          <!-- Quick Stats Karyawan -->
          <section class="row g-3 mb-4" aria-label="Employee summary metrics">
            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-primary">
                <div class="metric-top">
                  <span class="metric-label">HADIR BULAN INI</span>
                  <span class="metric-icon"><i class="bi bi-check-circle-fill" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $employeeStats['present_count'] }} Hari</div>
                <div class="metric-meta">
                  <span class="text-success">Absen Masuk</span>
                  <span>bulan ini</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-warning">
                <div class="metric-top">
                  <span class="metric-label">TERLAMBAT</span>
                  <span class="metric-icon"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $employeeStats['late_count'] }} Kali</div>
                <div class="metric-meta">
                  <span class="text-warning">Terlambat masuk</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-danger">
                <div class="metric-top">
                  <span class="metric-label">LUAR RADIUS</span>
                  <span class="metric-icon"><i class="bi bi-geo-alt-fill" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $employeeStats['out_of_radius_count'] }} Alert</div>
                <div class="metric-meta">
                  <span class="text-danger">Presensi luar geofence</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-success">
                <div class="metric-top">
                  <span class="metric-label">TOTAL LOG PRESENSI</span>
                  <span class="metric-icon"><i class="bi bi-card-checklist" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $employeeStats['total_logs_count'] }} Log</div>
                <div class="metric-meta">
                  <span class="text-success">Riwayat audit</span>
                </div>
              </article>
            </div>
          </section>

          <!-- Office Info & Recent Logs Summary -->
          <div class="row g-4 mb-4">
            <div class="col-12 col-lg-4">
              <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                  <h5 class="card-title h6 mb-1 fw-bold"><i class="bi bi-building me-2 text-primary"></i>Lokasi Kantor Acuan</h5>
                  <p class="card-subtitle text-muted small">Aturan geofence aktif akun Anda.</p>
                </div>
                <div class="card-body p-4">
                  <div class="p-3 bg-body-tertiary rounded-3 mb-3 border">
                    <strong class="d-block text-body mb-1">{{ $geofence['office_name'] }}</strong>
                    <span class="small text-muted d-block mb-1"><i class="bi bi-pin-map me-1"></i> Max Radius: {{ $geofence['radius_meters'] }} Meter</span>
                    <span class="small text-muted d-block mb-1"><i class="bi bi-crosshair me-1"></i> Lat/Lng: {{ $geofence['office_lat'] }}, {{ $geofence['office_lng'] }}</span>
                    <span class="small text-muted"><i class="bi bi-clock me-1"></i> Jam Shift: {{ $geofence['shift_start'] }} - {{ $geofence['shift_end'] }} WIB</span>
                  </div>

                  <div class="p-3 bg-body-tertiary rounded-3 border">
                    <strong class="d-block small text-muted mb-1">STATUS REGISTRASI BIOMETRIK</strong>
                    @if($user->account_status === 'Pending Biometrik' || empty($user->registered_face_photo))
                      <span class="badge text-bg-warning"><i class="bi bi-shield-lock me-1"></i>Pending Biometrik (Perdana)</span>
                    @else
                      <span class="badge text-bg-success"><i class="bi bi-shield-check me-1"></i>Aktif & Terverifikasi</span>
                    @endif
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12 col-lg-8">
              <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                  <div>
                    <h5 class="card-title h6 mb-1 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Presensi Terbaru Saya</h5>
                    <p class="card-subtitle text-muted small">Ringkasan aktivitas presensi terbaru.</p>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('employee.history') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">Lihat Semua</a>
                  </div>
                </div>

                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                      <thead class="table-light">
                        <tr>
                          <th class="ps-4">Tanggal</th>
                          <th>Tipe & Waktu</th>
                          <th>Status</th>
                          <th>Jarak</th>
                          <th>Biometric Match</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($recentLogs->take(5) as $log)
                        <tr>
                          <td class="ps-4">
                            <strong class="d-block text-body">{{ \Carbon\Carbon::parse($log->date)->translatedFormat('d M Y') }}</strong>
                          </td>
                          <td>
                            <span class="badge text-bg-dark me-1">{{ $log->type }}</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">{{ $log->time }}</span>
                          </td>
                          <td>
                            @if($log->status == 'TEPAT_WAKTU')
                              <span class="badge bg-success text-white px-2 py-1 rounded-pill">Tepat Waktu</span>
                            @elseif($log->status == 'TERLAMBAT')
                              <span class="badge bg-warning text-dark px-2 py-1 rounded-pill">Terlambat</span>
                            @elseif($log->status == 'PULANG_CEPAT')
                              <span class="badge bg-info text-white px-2 py-1 rounded-pill">Pulang Cepat</span>
                            @else
                              <span class="badge bg-danger text-white px-2 py-1 rounded-pill">Luar Radius</span>
                            @endif
                          </td>
                          <td>
                            <span class="small fw-semibold {{ $log->status == 'LUAR_RADIUS' ? 'text-danger' : 'text-success' }}">{{ $log->distance_from_office }}m</span>
                          </td>
                          <td>
                            @if($log->face_confidence_score)
                              <span class="small text-success fw-semibold"><i class="bi bi-shield-check me-1"></i>{{ $log->face_confidence_score }}%</span>
                            @else
                              <span class="text-muted small">N/A</span>
                            @endif
                          </td>
                        </tr>
                        @empty
                        <tr>
                          <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat presensi recorded.</td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
                @if($recentLogs->total() > 0)
                <div class="card-footer bg-transparent border-top px-4 py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                  <form action="{{ route('employee.dashboard') }}" method="GET" class="mb-0">
                    <div class="d-flex align-items-center gap-2">
                      <span class="text-muted small">Tampilkan:</span>
                      <select name="per_page" class="form-select form-select-sm rounded-pill w-auto" onchange="this.form.submit()">
                        <option value="5" {{ request('per_page') == 5 || !request('per_page') ? 'selected' : '' }}>5 baris</option>
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 baris</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 baris</option>
                      </select>
                    </div>
                  </form>
                  <div>
                    {{ $recentLogs->links() }}
                  </div>
                </div>
                @endif
              </div>
            </div>
          </div>

        </div>
      </main>
    </div>
  </div>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script>
    function updateClock() {
      const now = new Date();
      const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
      document.getElementById('liveClock').textContent = timeString;
    }
    setInterval(updateClock, 1000);
    updateClock();
  </script>
</body>
</html>
