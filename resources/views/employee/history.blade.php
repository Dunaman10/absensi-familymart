<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="description" content="Absensiku Pro - Riwayat Presensi Karyawan">
  <title>Riwayat Presensi Saya | Absensiku Pro</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body>
  <div class="admin-shell">
    <div class="sidebar-backdrop" data-sidebar-close></div>

    <!-- Sidebar Navigation -->
    @include('employee.layouts.sidebar')

    <div class="admin-main">
      <!-- Navbar Top -->
      @include('employee.layouts.navbar')

      <!-- Content Riwayat Presensi -->
      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">

          <!-- Page Heading -->
          <div class="page-heading mb-4">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1">LOG PRESENSI</p>
                <h1 class="h3 mb-1">Riwayat Presensi Saya</h1>
                <p class="text-muted mb-0">Catatan lengkap riwayat kehadiran, waktu masuk/pulang, dan jarak lokasi kantor.</p>
              </div>
            </div>
            <div class="heading-actions">
              <a href="{{ route('employee.dashboard') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                <i class="bi bi-camera-video me-1"></i> Ke Kamera Presensi
              </a>
            </div>
          </div>

          <!-- Table Container -->
          <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
              <h5 class="card-title h6 mb-0 fw-bold"><i class="bi bi-list-check me-2 text-primary"></i>Catatan Presensi (30 Hari Terakhir)</h5>
              <span class="badge bg-body-tertiary text-body border px-3 py-2">Total {{ count($logs) }} Log</span>
            </div>

            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th class="ps-4">Tanggal & Hari</th>
                      <th>Jam Masuk</th>
                      <th>Jam Pulang</th>
                      <th>Status Kehadiran</th>
                      <th>Jarak Geofence</th>
                      <th>Confidence Score</th>
                      <th>Catatan</th>
                      <th class="pe-4 text-center">Pratinjau Foto</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($logs as $log)
                    <tr>
                      <td class="ps-4">
                        <strong class="d-block text-body">{{ \Carbon\Carbon::parse($log->date)->translatedFormat('d M Y') }}</strong>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($log->date)->translatedFormat('l') }}</small>
                      </td>
                      <td>
                        <span class="badge text-bg-dark me-1">{{ $log->type }}</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-clock me-1"></i>{{ $log->time }}</span>
                      </td>
                      <td>
                        @if($log->status == 'TEPAT_WAKTU')
                          <span class="badge bg-success text-white px-2 py-1 rounded-pill"><i class="bi bi-check-circle me-1"></i>Tepat Waktu</span>
                        @elseif($log->status == 'TERLAMBAT')
                          <span class="badge bg-warning text-dark px-2 py-1 rounded-pill"><i class="bi bi-clock-history me-1"></i>Terlambat</span>
                        @elseif($log->status == 'PULANG_CEPAT')
                          <span class="badge bg-info text-white px-2 py-1 rounded-pill"><i class="bi bi-box-arrow-right me-1"></i>Pulang Cepat</span>
                        @else
                          <span class="badge bg-danger text-white px-2 py-1 rounded-pill"><i class="bi bi-geo-alt me-1"></i>Luar Radius</span>
                        @endif
                      </td>
                      <td>
                        <span class="small {{ $log->status == 'LUAR_RADIUS' ? 'text-danger' : 'text-success' }} fw-semibold"><i class="bi bi-geo-alt me-1"></i>{{ $log->distance_from_office }}m</span>
                      </td>
                      <td>
                        @if($log->face_confidence_score)
                          <span class="small text-success fw-semibold"><i class="bi bi-shield-check me-1"></i>{{ $log->face_confidence_score }}% Match</span>
                        @else
                          <span class="text-muted small">N/A</span>
                        @endif
                      </td>
                      <td>
                        <span class="small text-muted">{{ $log->geofence->office_name ?? 'Kantor Utama' }}</span>
                      </td>
                      <td class="pe-4 text-center">
                        @if($log->photo_path)
                          <img src="{{ asset($log->photo_path) }}" alt="Foto Presensi" class="rounded-circle border" style="width: 36px; height: 36px; object-fit: cover; cursor: pointer;" onclick="showPhotoModal('{{ asset($log->photo_path) }}', '{{ $log->date }}')">
                        @else
                          <span class="text-muted small">-</span>
                        @endif
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="7" class="text-center text-muted py-4">Belum ada riwayat presensi recorded.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
      </main>
    </div>
  </div>

  <!-- Modal Lightbox Preview Foto -->
  <div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-0 pb-0">
          <h6 class="modal-title fw-bold">Pratinjau Foto Presensi</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center p-4">
          <img id="modalImg" src="" alt="Bukti Presensi" class="img-fluid rounded-3 mb-3 border shadow-sm" style="max-height: 280px;">
          <p id="modalDate" class="small text-muted mb-0"></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script>
    function showPhotoModal(src, date) {
      document.getElementById('modalImg').src = src;
      document.getElementById('modalDate').textContent = 'Tanggal Presensi: ' + date;
      const modal = new bootstrap.Modal(document.getElementById('photoModal'));
      modal.show();
    }
  </script>
</body>
</html>
