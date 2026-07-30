<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="description" content="Absensiku Pro - Riwayat Presensi Karyawan">
  <title>Riwayat Presensi Saya | Absensiku Pro</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

  <!-- PWA Meta Tags -->
  <link rel="manifest" href="{{ asset('manifest.json') }}">
  <meta name="theme-color" content="#0a0c10">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Absensiku Pro">
  <link rel="apple-touch-icon" href="{{ asset('assets/images/favicon/apple-icon-180x180.png') }}">

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
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
              <h5 class="card-title h6 mb-0 fw-bold"><i class="bi bi-list-check me-2 text-primary"></i>Catatan Presensi Anda</h5>
              <span class="badge bg-body-tertiary text-body border px-3 py-2">Total {{ $logs->total() }} Log</span>
            </div>

            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th class="ps-4">Tanggal & Hari</th>
                      <th>Tipe & Waktu</th>
                      <th>Status Kehadiran</th>
                      <th>Validasi Alasan HR</th>
                      <th class="pe-4 text-center">Aksi</th>
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
                          <span class="badge bg-secondary text-white px-2 py-1 rounded-pill">{{ $log->status }}</span>
                        @endif
                      </td>
                      <td>
                        @if($log->approval_status == 'APPROVED')
                          <span class="badge bg-success text-white px-2 py-1 rounded-pill"><i class="bi bi-check-lg me-1"></i>Disetujui</span>
                        @elseif($log->approval_status == 'REJECTED')
                          <span class="badge bg-danger text-white px-2 py-1 rounded-pill"><i class="bi bi-x-lg me-1"></i>Ditolak</span>
                        @else
                          <span class="badge bg-warning text-dark px-2 py-1 rounded-pill"><i class="bi bi-hourglass-split me-1"></i>Pending HR</span>
                        @endif
                      </td>
                      <td class="pe-4 text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="showEmployeeDetailModal({{ json_encode([
                          'date' => \Carbon\Carbon::parse($log->date)->translatedFormat('d F Y'),
                          'time' => $log->time,
                          'type' => $log->type,
                          'status' => $log->status,
                          'approval_status' => $log->approval_status,
                          'approval_notes' => $log->approval_notes,
                          'approver_name' => $log->approver->name ?? 'Admin HR',
                          'is_outside_radius' => $log->is_outside_radius,
                          'distance' => $log->distance_from_office,
                          'lat' => $log->lat,
                          'lng' => $log->lng,
                          'office' => $log->geofence->office_name ?? 'Kantor Utama',
                          'confidence' => $log->face_confidence_score,
                          'notes' => $log->notes,
                          'photo' => $log->photo_path ? asset($log->photo_path) : null
                        ]) }})">
                          <i class="bi bi-info-circle me-1"></i> Detail
                        </button>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="8" class="text-center text-muted py-4">Belum ada riwayat presensi recorded.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
            @if($logs->total() > 0)
            <div class="card-footer bg-transparent border-top px-4 py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
              <form action="{{ route('employee.history') }}" method="GET" class="mb-0">
                <div class="input-group input-group-sm">
                  <span class="input-group-text bg-body-tertiary">Tampilkan</span>
                  <select name="per_page" class="form-select" onchange="this.form.submit()">
                    <option value="5" {{ request('per_page') == 5 || !request('per_page') ? 'selected' : '' }}>5 baris</option>
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                  </select>
                </div>
              </form>
              <div>
                {{ $logs->links() }}
              </div>
            </div>
            @endif
          </div>

        </div>
      </main>
    </div>
  </div>

  <!-- Modal Detail Presensi Employee -->
  <div class="modal fade" id="employeeDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-0 pb-0">
          <h6 class="modal-title fw-bold"><i class="bi bi-info-circle text-primary me-2"></i>Detail Presensi</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-3 mb-3">
            <div class="col-6">
              <small class="text-muted d-block mb-1">Tanggal & Waktu</small>
              <span class="fw-medium" id="empDetailDatetime">-</span>
            </div>
            <div class="col-6">
              <small class="text-muted d-block mb-1">Tipe & Status</small>
              <span id="empDetailStatusBadge"></span>
            </div>
          </div>
          <div class="row g-3 mb-4">
            <div class="col-6">
              <small class="text-muted d-block mb-1">Biometrik Wajah</small>
              <span class="fw-medium text-success" id="empDetailBiometric"><i class="bi bi-shield-check me-1"></i>-</span>
            </div>
            <div class="col-6">
              <small class="text-muted d-block mb-1">Lokasi Geofence</small>
              <span class="fw-medium" id="empDetailLocation">-</span>
              <small class="d-block text-muted" id="empDetailCoords" style="font-size: 0.75rem;">-</small>
              <small class="d-block text-muted mt-1" id="empDetailOffice">-</small>
            </div>
          </div>
          <div class="mb-3">
            <small class="text-muted d-block mb-1">Catatan / Alasan Anda</small>
            <div class="p-2 bg-body-secondary rounded text-body border" id="empDetailNotes" style="font-size: 0.85rem;">-</div>
          </div>
          <div class="mb-4">
            <small class="text-muted d-block mb-1">Status Validasi & Balasan HR/Admin</small>
            <div class="p-2 rounded border" id="empDetailApprovalBox" style="font-size: 0.85rem;">-</div>
          </div>
          <div>
            <small class="text-muted d-block mb-2 text-center">Foto Bukti Presensi</small>
            <div class="p-2 bg-dark rounded-3 text-center">
              <img id="empDetailPhotoImg" src="" alt="Foto Presensi" class="img-fluid rounded-3" style="max-height: 250px; width: auto; object-fit: contain;">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script>
    function showEmployeeDetailModal(data) {
      document.getElementById('empDetailDatetime').textContent = data.date + ' ' + data.time + ' WIB';
      
      let statusBadge = '';
      if (data.status === 'TEPAT_WAKTU') {
        statusBadge = `<span class="badge bg-success px-2 py-1"><i class="bi bi-check-circle me-1"></i>Tepat Waktu</span>`;
      } else if (data.status === 'TERLAMBAT') {
        statusBadge = `<span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-clock-history me-1"></i>Terlambat</span>`;
      } else if (data.status === 'PULANG_CEPAT') {
        statusBadge = `<span class="badge bg-info px-2 py-1"><i class="bi bi-box-arrow-right me-1"></i>Pulang Cepat</span>`;
      } else {
        statusBadge = `<span class="badge bg-secondary px-2 py-1">${data.status}</span>`;
      }
      
      let typeBadge = data.type === 'MASUK' ? `<span class="badge bg-primary me-1 px-2 py-1">MASUK</span>` : `<span class="badge bg-danger me-1 px-2 py-1">PULANG</span>`;
      document.getElementById('empDetailStatusBadge').innerHTML = typeBadge + statusBadge;
      
      document.getElementById('empDetailBiometric').innerHTML = data.confidence ? `<i class="bi bi-shield-check me-1"></i>${data.confidence}% Match` : 'N/A';
      
      let locationClass = data.is_outside_radius ? 'text-danger' : 'text-success';
      let locationText = data.is_outside_radius ? 'Luar Radius' : 'Dalam Radius';
      document.getElementById('empDetailLocation').innerHTML = `<span class="${locationClass}">${data.distance}m (${locationText})</span>`;
      document.getElementById('empDetailCoords').textContent = `${data.lat}, ${data.lng}`;
      document.getElementById('empDetailOffice').textContent = data.office;
      
      document.getElementById('empDetailNotes').textContent = data.notes ? data.notes : '-';
      
      // Render Status Validasi & Balasan HR
      const approvalBox = document.getElementById('empDetailApprovalBox');
      if (data.approval_status === 'APPROVED') {
        approvalBox.className = 'p-2 bg-success-subtle text-success border border-success-subtle rounded';
        approvalBox.innerHTML = `<strong><i class="bi bi-check-circle me-1"></i>Disetujui oleh ${data.approver_name}</strong><br><span class="text-body small">${data.approval_notes || 'Alasan diterima.'}</span>`;
      } else if (data.approval_status === 'REJECTED') {
        approvalBox.className = 'p-2 bg-danger-subtle text-danger border border-danger-subtle rounded';
        approvalBox.innerHTML = `<strong><i class="bi bi-x-circle me-1"></i>Ditolak oleh ${data.approver_name}</strong><br><span class="text-body small">${data.approval_notes || 'Alasan tidak diterima.'}</span>`;
      } else {
        approvalBox.className = 'p-2 bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded';
        approvalBox.innerHTML = `<strong><i class="bi bi-hourglass-split me-1"></i>Menunggu Verifikasi HR</strong><br><span class="text-body small">Catatan alasan Anda sedang ditinjau oleh Admin HRIS.</span>`;
      }
      
      const photoEl = document.getElementById('empDetailPhotoImg');
      if (data.photo) {
        photoEl.src = data.photo;
        photoEl.parentElement.style.display = 'block';
      } else {
        photoEl.parentElement.style.display = 'none';
      }
      
      const modal = new bootstrap.Modal(document.getElementById('employeeDetailModal'));
      modal.show();
    }
  </script>
</body>
</html>
