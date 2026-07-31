<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Absensiku Pro - Detail Tracking Presensi & Penggajian Karyawan">
  <title>Detail Presensi & Penggajian - {{ $employee->name }} | Absensiku Pro</title>
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

  <style>
    @page {
      size: A4 landscape;
      margin: 15mm 20mm;
    }

    @media print {
      /* Sembunyikan elemen navigasi, sidebar, navbar, tombol & filter */
      .admin-sidebar,
      .sidebar-backdrop,
      .admin-navbar,
      .heading-actions,
      .btn,
      .card.panel.p-3.mb-4.mt-3,
      a[href*="employees"] {
        display: none !important;
      }

      body, .admin-shell, .admin-main, .dashboard-content {
        background: #ffffff !important;
        color: #000000 !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
      }

      .page-heading {
        border-bottom: 2px solid #333;
        padding-bottom: 15px;
        margin-bottom: 25px;
      }

      .page-heading h1 {
        color: #000000 !important;
        font-size: 20pt !important;
      }

      .eyebrow, .text-muted {
        color: #555555 !important;
      }

      .fw-mono, .text-white {
        color: #000000 !important;
      }

      .metric-card {
        border: 1px solid #ddd !important;
        background: #f8f9fa !important;
        color: #000 !important;
        box-shadow: none !important;
        padding: 15px !important;
      }

      .metric-value {
        color: #000000 !important;
      }

      .panel {
        border: 1px solid #ccc !important;
        background: #ffffff !important;
        box-shadow: none !important;
        padding: 15px !important;
      }

      .table {
        color: #000000 !important;
        border-color: #ddd !important;
      }

      .table th, .table td {
        border-bottom: 1px solid #ddd !important;
        color: #000000 !important;
        padding: 10px 12px !important;
      }

      .print-history-section {
        page-break-before: always !important;
        break-before: page !important;
        margin-top: 30px !important;
        padding-top: 20px !important;
      }
    }
  </style>
</head>

<body>
  <div class="admin-shell">
    <div class="sidebar-backdrop" data-sidebar-close></div>

    <!-- Sidebar Admin -->
    @include('admin.layouts.sidebar')

    <div class="admin-main">
      <!-- Navbar Top -->
      @include('admin.layouts.navbar')

      <!-- Dashboard Content - Detail Presensi Karyawan -->
      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">
          <!-- Back Link & Page Heading -->
          <div class="mb-3">
            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="bi bi-arrow-left me-1"></i> Kembali ke Data Karyawan</a>
          </div>

          <div class="page-heading">
            <div class="page-heading-copy d-flex align-items-center gap-3">
              <img class="avatar-img avatar-lg rounded-circle shadow print-avatar" src="{{ $employee->registered_face_photo ? asset($employee->registered_face_photo) : 'https://ui-avatars.com/api/?name='.urlencode($employee->name).'&background=0D8ABC&color=fff' }}" alt="{{ $employee->name }}" style="width: 64px; height: 64px; object-fit: cover;">
              <div>
                <p class="eyebrow mb-1">LAPORAN REKAP PRESENSI</p>
                <h1 class="h3 mb-1">{{ $employee->name }}</h1>
                <p class="text-muted mb-0">NIP: <span class="fw-mono text-white me-3">{{ $employee->nip }}</span> Jabatan: <span class="text-white">{{ $employee->position ?? '-' }}</span> | Shift: <span class="badge text-bg-info">{{ $employee->shift->name ?? 'Default Office' }}</span></p>
                <p class="d-none d-print-block fw-bold text-dark mt-1 mb-0" style="font-size: 0.9rem;">
                  Periode Laporan: {{ \Carbon\Carbon::parse($dateFrom)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y') }}
                </p>
              </div>
            </div>
            <div class="heading-actions">
              <button class="btn btn-outline-secondary btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i> Cetak / Save PDF</button>
            </div>
          </div>

          <!-- Date Filter Card -->
          <div class="card panel p-3 mb-4 mt-3">
            <form action="{{ route('admin.employees.show', $employee->id) }}" method="GET" class="row g-2 align-items-end">
              <div class="col-12 col-sm-4 col-md-3">
                <label class="form-label small fw-semibold">Tanggal Mulai</label>
                <input type="date" class="form-control form-control-sm" name="date_from" value="{{ $dateFrom }}">
              </div>
              <div class="col-12 col-sm-4 col-md-3">
                <label class="form-label small fw-semibold">Tanggal Akhir</label>
                <input type="date" class="form-control form-control-sm" name="date_to" value="{{ $dateTo }}">
              </div>
              <div class="col-12 col-sm-4 col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-filter me-1"></i> Filter Periode</button>
              </div>
            </form>
          </div>

          <!-- Summary Metrics Cards for Payroll -->
          <section class="row g-3" aria-label="Payroll metrics summary">
            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-primary">
                <div class="metric-top">
                  <span class="metric-label">HARI KERJA VALID (PAYROLL)</span>
                  <span class="metric-icon"><i class="bi bi-cash-stack" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $validDaysForPayroll }} <span class="fs-6 text-muted font-sans-serif">Hari</span></div>
                <div class="metric-meta">
                  <span class="text-success">Tepat waktu + Alasan disetujui</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-success">
                <div class="metric-top">
                  <span class="metric-label">TEPAT WAKTU</span>
                  <span class="metric-icon"><i class="bi bi-check-circle" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $totalTepatWaktu }} <span class="fs-6 text-muted font-sans-serif">Hari</span></div>
                <div class="metric-meta">
                  <span class="text-success">Hadir sesuai shift</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-warning">
                <div class="metric-top">
                  <span class="metric-label">TERLAMBAT (DISETUJUI)</span>
                  <span class="metric-icon"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $terlambatDisetujui }} <span class="fs-6 text-muted font-sans-serif">Hari</span></div>
                <div class="metric-meta">
                  <span class="text-warning">Alasan diizinkan HR</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-danger">
                <div class="metric-top">
                  <span class="metric-label">TERLAMBAT DITOLAK / PENDING</span>
                  <span class="metric-icon"><i class="bi bi-x-circle" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $terlambatDitolak + $terlambatPending }} <span class="fs-6 text-muted font-sans-serif">Hari</span></div>
                <div class="metric-meta">
                  <span class="text-danger">Potong / evaluasi gaji</span>
                </div>
              </article>
            </div>
          </section>

          <!-- History Table Log Presensi Karyawan -->
          <section class="panel print-history-section mt-4">
            <div class="panel-header flex-wrap gap-2">
              <div>
                <h2 class="h5 mb-1 section-title"><i class="bi bi-journal-text" aria-hidden="true"></i><span>Histori Presensi Periode Ini</span></h2>
                <p class="text-muted mb-0">Rincian log masuk & pulang karyawan untuk acuan perhitungan penggajian.</p>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th scope="col">Tanggal & Waktu</th>
                    <th scope="col">Tipe Presensi</th>
                    <th scope="col">Status Waktu</th>
                    <th scope="col">Foto Biometrik</th>
                    <th scope="col">Catatan / Alasan</th>
                    <th scope="col">Status Validasi HR</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($logs as $log)
                  <tr>
                    <td>
                      <span class="fw-semibold">{{ \Carbon\Carbon::parse($log->date)->translatedFormat('d F Y') }}</span>
                      <div class="small font-monospace text-muted">{{ $log->time }} WIB</div>
                    </td>
                    <td>
                      @if($log->type == 'MASUK')
                        <span class="badge text-bg-primary"><i class="bi bi-box-arrow-in-right me-1"></i>MASUK</span>
                      @else
                        <span class="badge text-bg-info"><i class="bi bi-box-arrow-left me-1"></i>PULANG</span>
                      @endif
                    </td>
                    <td>
                      @if($log->status == 'TEPAT_WAKTU')
                        <span class="badge text-bg-success"><i class="bi bi-check-lg me-1"></i>Tepat Waktu</span>
                      @elseif($log->status == 'TERLAMBAT')
                        <span class="badge text-bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Terlambat</span>
                      @elseif($log->status == 'PULANG_CEPAT')
                        <span class="badge text-bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Pulang Cepat</span>
                      @else
                        <span class="badge text-bg-danger"><i class="bi bi-geo me-1"></i>Luar Radius</span>
                      @endif
                    </td>
                    <td>
                      @if($log->photo_path)
                        <a href="{{ asset($log->photo_path) }}" target="_blank">
                          <img class="rounded shadow-sm" src="{{ asset($log->photo_path) }}" alt="Foto Presensi" style="width: 42px; height: 42px; object-fit: cover;">
                        </a>
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                    <td>
                      <small class="text-white">{{ $log->notes ?? '-' }}</small>
                    </td>
                    <td>
                      @if($log->approval_status == 'APPROVED')
                        <span class="badge text-bg-success"><i class="bi bi-check-circle me-1"></i>DISETUJUI</span>
                      @elseif($log->approval_status == 'REJECTED')
                        <span class="badge text-bg-danger"><i class="bi bi-x-circle me-1"></i>DITOLAK</span>
                      @else
                        <span class="badge text-bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>MENUNGGU VALIDASI</span>
                      @endif
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="6" class="text-center text-muted py-4">Tidak ada log presensi pada periode tanggal yang dipilih.</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </section>

          <!-- Print Footer Notice -->
          <div class="d-none d-print-block text-end mt-4 pt-3" style="font-size: 0.8rem; color: #666;">
            Dicetak dari sistem Absensiku Pro pada {{ now()->timezone('Asia/Jakarta')->translatedFormat('d F Y') }}
          </div>
        </div>
      </main>
    </div>
  </div>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
