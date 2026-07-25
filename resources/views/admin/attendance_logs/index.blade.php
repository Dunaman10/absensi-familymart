<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Absensiku Pro - Log Presensi Karyawan">
  <title>Log Presensi Realtime | Absensiku Pro</title>
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

    <!-- Sidebar Admin -->
    @include('admin.layouts.sidebar')

    <div class="admin-main">
      <!-- Navbar Top -->
      @include('admin.layouts.navbar')

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
            <div class="heading-actions flex-wrap gap-2">
              <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#filterDateModal">
                <i class="bi bi-funnel me-1" aria-hidden="true"></i> Filter Tanggal
              </button>
              <a class="btn btn-primary btn-sm" href="{{ route('admin.attendance_logs.export', request()->all()) }}">
                <i class="bi bi-file-earmark-excel me-1" aria-hidden="true"></i> Export Laporan
              </a>
            </div>
          </div>

          <!-- Active Filter Badge Info -->
          @if(request('date_from') || request('date_to') || request('search'))
          <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center justify-content-between">
            <span class="small">
              <i class="bi bi-info-circle me-1"></i> Filtering Aktif:
              @if(request('search')) <strong>Search: "{{ request('search') }}"</strong> @endif
              @if(request('date_from')) <strong>Dari: {{ request('date_from') }}</strong> @endif
              @if(request('date_to')) <strong>Sampai: {{ request('date_to') }}</strong> @endif
            </span>
            <a href="{{ route('admin.attendance_logs.index') }}" class="btn btn-sm btn-outline-dark py-0" style="font-size: 0.75rem;">Reset Filter</a>
          </div>
          @endif

          <!-- Metric Cards Log Presensi -->
          <section class="row g-3 mt-1" aria-label="Attendance logs summary">
            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-primary">
                <div class="metric-top">
                  <span class="metric-label">TOTAL LOG PRESENSI</span>
                  <span class="metric-icon"><i class="bi bi-card-checklist" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value" id="metricTotalLogs">{{ $totalLogs }}</div>
                <div class="metric-meta">
                  <span class="text-success">Tercatat</span>
                  <span>di database</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-success">
                <div class="metric-top">
                  <span class="metric-label">PRESENSI MASUK</span>
                  <span class="metric-icon"><i class="bi bi-box-arrow-in-right" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value" id="metricCheckIn">{{ $checkInLogs }}</div>
                <div class="metric-meta">
                  <span class="text-success">Sesi Masuk</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-warning">
                <div class="metric-top">
                  <span class="metric-label">PRESENSI PULANG</span>
                  <span class="metric-icon"><i class="bi bi-box-arrow-left" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value" id="metricCheckOut">{{ $checkOutLogs }}</div>
                <div class="metric-meta">
                  <span class="text-warning">Sesi Selesai</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-danger">
                <div class="metric-top">
                  <span class="metric-label">LUAR RADIUS</span>
                  <span class="metric-icon"><i class="bi bi-geo-alt" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value" id="metricOutOfRadius">{{ $outOfRadiusLogs }}</div>
                <div class="metric-meta">
                  <span class="text-danger">Di luar geofence</span>
                </div>
              </article>
            </div>
          </section>

          <!-- Table Log Presensi Karyawan -->
          <section class="panel mt-3">
            <div class="panel-header flex-wrap gap-2">
              <div>
                <h2 class="h5 mb-1 section-title"><i class="bi bi-table" aria-hidden="true"></i><span>Riwayat Audit Presensi</span> <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill ms-2" style="font-size: 0.7rem;">Live Realtime</span></h2>
                <p class="text-muted mb-0">Tabel log presensi read-only beserta tangkapan foto biometrik wajah dan koordinat.</p>
              </div>
              <form action="{{ route('admin.attendance_logs.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                @if(request('per_page')) <input type="hidden" name="per_page" value="{{ request('per_page') }}"> @endif
                @if(request('date_from')) <input type="hidden" name="date_from" value="{{ request('date_from') }}"> @endif
                @if(request('date_to')) <input type="hidden" name="date_to" value="{{ request('date_to') }}"> @endif
                <div class="input-group input-group-sm">
                  <input class="form-control table-search" type="search" name="search" value="{{ request('search') }}" placeholder="Cari log" aria-label="Search logs">
                  <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                  @if(request('search'))
                    <a href="{{ route('admin.attendance_logs.index') }}" class="btn btn-outline-secondary text-muted" title="Reset"><i class="bi bi-x-lg"></i></a>
                  @endif
                </div>
              </form>
            </div>
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th scope="col">Karyawan</th>
                    <th scope="col">Tanggal & Waktu</th>
                    <th scope="col">Tipe Presensi</th>
                    <th scope="col">Status Kehadiran</th>
                    <th scope="col">Aksi</th>
                  </tr>
                </thead>
                <tbody id="logsTableBody">
                  @forelse($logs as $log)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <img class="avatar-img avatar-sm rounded-circle shadow-sm" src="{{ $log->user->registered_face_photo ? asset($log->user->registered_face_photo) : 'https://ui-avatars.com/api/?name='.urlencode($log->user->name).'&background=0D8ABC&color=fff' }}" alt="{{ $log->user->name }}">
                        <div>
                          <p class="fw-semibold mb-0">{{ $log->user->name }}</p>
                          <small class="text-muted">NIP. {{ $log->user->nip }}</small>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="fw-semibold d-block">{{ \Carbon\Carbon::parse($log->date)->translatedFormat('d F Y') }}</span>
                      <small class="text-muted">{{ $log->time }} WIB</small>
                    </td>
                    <td>
                      @if($log->type == 'MASUK')
                        <span class="badge text-bg-primary">MASUK</span>
                      @else
                        <span class="badge text-bg-danger">PULANG</span>
                      @endif
                    </td>
                    <td>
                      @if($log->status == 'TEPAT_WAKTU')
                        <span class="badge text-bg-success"><i class="bi bi-check-circle me-1"></i>Tepat Waktu</span>
                      @elseif($log->status == 'TERLAMBAT')
                        <span class="badge text-bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Terlambat</span>
                      @elseif($log->status == 'PULANG_CEPAT')
                        <span class="badge text-bg-info"><i class="bi bi-box-arrow-right me-1"></i>Pulang Cepat</span>
                      @else
                        <span class="badge text-bg-secondary">{{ $log->status }}</span>
                      @endif
                    </td>
                    <td>
                      <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="showAdminDetailModal({{ json_encode([
                        'name' => $log->user->name ?? 'Karyawan',
                        'nip' => $log->user->nip,
                        'date' => \Carbon\Carbon::parse($log->date)->translatedFormat('d F Y'),
                        'time' => $log->time,
                        'type' => $log->type,
                        'status' => $log->status,
                        'is_outside_radius' => $log->is_outside_radius,
                        'distance' => $log->distance_from_office,
                        'lat' => $log->lat,
                        'lng' => $log->lng,
                        'confidence' => $log->face_confidence_score,
                        'notes' => $log->notes,
                        'photo' => $log->photo_path ? asset($log->photo_path) : null,
                        'profile_photo' => $log->user->registered_face_photo ? asset($log->user->registered_face_photo) : 'https://ui-avatars.com/api/?name='.urlencode($log->user->name).'&background=0D8ABC&color=fff'
                      ]) }})">
                        <i class="bi bi-info-circle me-1"></i> Detail
                      </button>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada data log presensi ditemukan.</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            @if($logs->total() > 0)
            <div class="panel-footer border-top px-3 py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
              <form action="{{ route('admin.attendance_logs.index') }}" method="GET" class="mb-0">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                @if(request('date_from')) <input type="hidden" name="date_from" value="{{ request('date_from') }}"> @endif
                @if(request('date_to')) <input type="hidden" name="date_to" value="{{ request('date_to') }}"> @endif
                <div class="d-flex align-items-center gap-2">
                  <span class="text-muted small">Tampilkan:</span>
                  <select name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                    <option value="5" {{ request('per_page') == 5 || !request('per_page') ? 'selected' : '' }}>5 baris</option>
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
                  </select>
                </div>
              </form>
              <div>
                {{ $logs->links() }}
              </div>
            </div>
            @endif
          </section>
        </div>
      </main>
    </div>
  </div>

  <!-- Modal Filter Tanggal -->
  <div class="modal fade" id="filterDateModal" tabindex="-1" aria-labelledby="filterDateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="{{ route('admin.attendance_logs.index') }}" method="GET">
          @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
          <div class="modal-header">
            <h5 class="modal-title fw-bold" id="filterDateModalLabel"><i class="bi bi-funnel me-2 text-primary"></i>Filter Rentang Tanggal Presensi</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-6">
                <label for="date_from" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
              </div>
              <div class="col-6">
                <label for="date_to" class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <a href="{{ route('admin.attendance_logs.index') }}" class="btn btn-outline-secondary btn-sm">Reset Filter</a>
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i> Terapkan Filter</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Detail Log Presensi -->
  <div class="modal fade" id="adminDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-0 pb-0">
          <h6 class="modal-title fw-bold"><i class="bi bi-info-circle text-primary me-2"></i>Detail Presensi</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="d-flex align-items-center gap-3 mb-4">
            <img id="detailProfileImg" src="" alt="Foto Profil" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
            <div>
              <p class="fw-bold mb-0" id="detailName">-</p>
              <small class="text-muted" id="detailNip">-</small>
            </div>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-6">
              <small class="text-muted d-block mb-1">Tanggal & Waktu</small>
              <span class="fw-medium" id="detailDatetime">-</span>
            </div>
            <div class="col-6">
              <small class="text-muted d-block mb-1">Tipe & Status</small>
              <span id="detailStatusBadge"></span>
            </div>
          </div>
          <div class="row g-3 mb-4">
            <div class="col-6">
              <small class="text-muted d-block mb-1">Biometrik Wajah</small>
              <span class="fw-medium text-success" id="detailBiometric"><i class="bi bi-shield-check me-1"></i>-</span>
            </div>
            <div class="col-6">
              <small class="text-muted d-block mb-1">Lokasi Geofence</small>
              <span class="fw-medium" id="detailLocation">-</span>
              <small class="d-block text-muted" id="detailCoords" style="font-size: 0.75rem;">-</small>
            </div>
          </div>
          <div class="mb-4">
            <small class="text-muted d-block mb-1">Catatan Karyawan</small>
            <div class="p-2 bg-body-secondary rounded text-body border" id="detailNotes" style="font-size: 0.85rem;">-</div>
          </div>
          <div>
            <small class="text-muted d-block mb-2 text-center">Foto Bukti Presensi</small>
            <div class="p-2 bg-dark rounded-3 text-center">
              <img id="detailPhotoImg" src="" alt="Foto Presensi" class="img-fluid rounded-3" style="max-height: 250px; width: auto; object-fit: contain;">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>

  <script>
    function showAdminDetailModal(data) {
      document.getElementById('detailName').textContent = data.name;
      document.getElementById('detailNip').textContent = 'NIP. ' + data.nip;
      document.getElementById('detailProfileImg').src = data.profile_photo;
      document.getElementById('detailDatetime').textContent = data.date + ' ' + data.time + ' WIB';
      
      let statusBadge = '';
      if (data.status === 'TEPAT_WAKTU') {
        statusBadge = `<span class="badge text-bg-success"><i class="bi bi-check-circle me-1"></i>Tepat Waktu</span>`;
      } else if (data.status === 'TERLAMBAT') {
        statusBadge = `<span class="badge text-bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Terlambat</span>`;
      } else if (data.status === 'PULANG_CEPAT') {
        statusBadge = `<span class="badge text-bg-info"><i class="bi bi-box-arrow-right me-1"></i>Pulang Cepat</span>`;
      } else {
        statusBadge = `<span class="badge text-bg-secondary">${data.status}</span>`;
      }
      
      let typeBadge = data.type === 'MASUK' ? `<span class="badge text-bg-primary me-1">MASUK</span>` : `<span class="badge text-bg-danger me-1">PULANG</span>`;
      document.getElementById('detailStatusBadge').innerHTML = typeBadge + statusBadge;
      
      document.getElementById('detailBiometric').innerHTML = data.confidence ? `<i class="bi bi-shield-check me-1"></i>${data.confidence}% Match` : 'N/A';
      
      let locationClass = data.is_outside_radius ? 'text-danger' : 'text-success';
      let locationText = data.is_outside_radius ? 'Luar Radius' : 'Dalam Radius';
      document.getElementById('detailLocation').innerHTML = `<span class="${locationClass}">${data.distance}m (${locationText})</span>`;
      document.getElementById('detailCoords').textContent = `${data.lat}, ${data.lng}`;
      
      document.getElementById('detailNotes').textContent = data.notes ? data.notes : '-';
      
      const photoEl = document.getElementById('detailPhotoImg');
      if (data.photo) {
        photoEl.src = data.photo;
        photoEl.parentElement.style.display = 'block';
      } else {
        photoEl.parentElement.style.display = 'none';
      }
      
      const modal = new bootstrap.Modal(document.getElementById('adminDetailModal'));
      modal.show();
    }

    // Real-Time Live Auto Sync Engine for Log Presensi Table & Metrics
    let lastKnownLogId = null;

    async function fetchLatestLogs() {
      try {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('json', '1');
        
        const res = await fetch('{{ route("admin.attendance_logs.index") }}?' + urlParams.toString(), {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!res.ok) return;
        const data = await res.json();

        // Update metric counter cards
        const mTotal = document.getElementById('metricTotalLogs');
        const mIn = document.getElementById('metricCheckIn');
        const mOut = document.getElementById('metricCheckOut');
        const mGeo = document.getElementById('metricOutOfRadius');

        if (mTotal) mTotal.textContent = data.totalLogs;
        if (mIn) mIn.textContent = data.checkInLogs;
        if (mOut) mOut.textContent = data.checkOutLogs;
        if (mGeo) mGeo.textContent = data.outOfRadiusLogs;

        const tbody = document.getElementById('logsTableBody');
        if (!tbody) return;

        if (data.logs.length === 0) {
          tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-4">Belum ada data log presensi ditemukan.</td></tr>`;
          return;
        }

        // Trigger toast notification if new log arrives
        if (lastKnownLogId !== null && data.logs.length > 0 && data.logs[0].id !== lastKnownLogId) {
          const newLog = data.logs[0];
          if (window.notifyNewAttendance) {
            window.notifyNewAttendance(newLog);
          }
        }
        if (data.logs.length > 0) {
          lastKnownLogId = data.logs[0].id;
        }

        // Re-render rows dynamically without full page reload
        let html = '';
        data.logs.forEach(log => {
          let statusBadge = '';
          if (log.status === 'TEPAT_WAKTU') {
            statusBadge = `<span class="badge text-bg-success"><i class="bi bi-check-circle me-1"></i>Tepat Waktu</span>`;
          } else if (log.status === 'TERLAMBAT') {
            statusBadge = `<span class="badge text-bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Terlambat</span>`;
          } else if (log.status === 'PULANG_CEPAT') {
            statusBadge = `<span class="badge text-bg-info"><i class="bi bi-box-arrow-right me-1"></i>Pulang Cepat</span>`;
          } else {
            statusBadge = `<span class="badge text-bg-secondary">${log.status}</span>`;
          }

          let detailData = {
            name: log.user_name,
            nip: log.nip,
            date: log.date,
            time: log.time,
            type: log.type,
            status: log.status,
            is_outside_radius: log.is_outside_radius,
            distance: log.distance,
            lat: log.lat,
            lng: log.lng,
            confidence: log.face_confidence,
            notes: log.notes,
            photo: log.photo_url,
            profile_photo: log.registered_face_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(log.user.name)}&background=0D8ABC&color=fff`
          };

          html += `
            <tr>
              <td class="ps-3">
                <div class="d-flex align-items-center gap-2">
                  <img class="avatar-img avatar-sm" src="${detailData.profile_photo}" alt="${log.user_name}">
                  <div>
                    <p class="fw-semibold mb-0 text-body">${log.user_name}</p>
                    <small class="text-muted">NIP. ${log.nip}</small>
                  </div>
                </div>
              </td>
              <td>
                <span class="fw-semibold d-block text-body">${log.date}</span>
                <small class="text-muted">${log.time} WIB</small>
              </td>
              <td><span class="badge text-bg-dark">${log.type}</span></td>
              <td>${statusBadge}</td>
              <td>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick='showAdminDetailModal(${JSON.stringify(detailData).replace(/'/g, "&#39;")})'>
                  <i class="bi bi-info-circle me-1"></i> Detail
                </button>
              </td>
            </tr>
          `;
        });
        tbody.innerHTML = html;
      } catch (e) {}
    }

    // Auto-poll every 3 seconds for instant real-time live data sync
    setInterval(fetchLatestLogs, 3000);
  </script>
</body>
</html>
