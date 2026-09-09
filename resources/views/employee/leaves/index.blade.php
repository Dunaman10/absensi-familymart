<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Absensi FamilyMart - Pengajuan Perizinan Karyawan">
  <title>Perizinan | Portal Karyawan FamilyMart</title>
  <link rel="icon" type="image/png" href="{{ asset('assets/images/logo/icon.png') }}">
  <link rel="shortcut icon" href="{{ asset('assets/images/logo/icon.png') }}">

  <!-- PWA Meta Tags -->
  <link rel="manifest" href="{{ asset('manifest.json') }}">
  <meta name="theme-color" content="#0a0c10">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Absensi FamilyMart">
  <link rel="apple-touch-icon" href="{{ asset('assets/images/logo/icon.png') }}">

  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

  <style>
    .leave-type-izin { background-color: rgba(13, 202, 240, 0.15); color: #0dcaf0; border: 1px solid rgba(13, 202, 240, 0.3); }
    .leave-type-cuti { background-color: rgba(37, 99, 235, 0.15); color: #6ea8fe; border: 1px solid rgba(37, 99, 235, 0.3); }
    .leave-type-sakit { background-color: rgba(220, 53, 69, 0.15); color: #ea868f; border: 1px solid rgba(220, 53, 69, 0.3); }

    .leave-type-selector { display: flex; gap: 0.75rem; flex-wrap: wrap; }
    .leave-type-option { flex: 1; min-width: 100px; cursor: pointer; }
    .leave-type-option input[type="radio"] { display: none; }
    .leave-type-option .type-card {
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      padding: 1rem; border-radius: 12px; border: 2px solid var(--bs-border-color);
      background: var(--bs-body-bg); transition: all 0.2s ease;
      text-align: center; gap: 0.5rem;
    }
    .leave-type-option .type-card:hover { border-color: var(--bs-primary); }
    .leave-type-option input:checked + .type-card {
      border-color: var(--bs-primary); background: rgba(37, 99, 235, 0.08);
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .leave-type-option .type-icon { font-size: 1.75rem; }
    .leave-type-option .type-label { font-weight: 600; font-size: 0.85rem; }

    .quota-progress { height: 8px; border-radius: 4px; overflow: hidden; background: var(--bs-border-color); }
    .quota-progress-bar { height: 100%; border-radius: 4px; transition: width 0.5s ease; }

    .timeline-item { position: relative; padding-left: 1.5rem; padding-bottom: 1rem; border-left: 2px solid var(--bs-border-color); }
    .timeline-item:last-child { border-left: 2px solid transparent; padding-bottom: 0; }
    .timeline-dot { position: absolute; left: -7px; top: 4px; width: 12px; height: 12px; border-radius: 50%; border: 2px solid var(--bs-body-bg); }
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

      <!-- Main Content -->
      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">

          <!-- Alert Flash Messages -->
          @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-check-circle-fill fs-5"></i>
              <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-exclamation-triangle-fill fs-5"></i>
              <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          @if($errors->any())
          <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-1">
              <i class="bi bi-exclamation-octagon-fill fs-5"></i>
              <strong>Terjadi kesalahan input:</strong>
            </div>
            <ul class="mb-0 ps-3">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          <!-- Page Heading -->
          <div class="page-heading mb-4">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="bi bi-file-earmark-text" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1">PERIZINAN KARYAWAN</p>
                <h1 class="h3 mb-1">Pengajuan Perizinan</h1>
                <p class="text-muted mb-0">Ajukan izin, cuti, atau sakit dan pantau status pengajuan Anda.</p>
              </div>
            </div>
          </div>

          <!-- Banner CTA -->
          <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, rgba(37, 99, 235, 0.15), rgba(16, 185, 129, 0.1)); border: 1px solid rgba(37, 99, 235, 0.3) !important;">
            <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
              <div>
                <div class="d-flex align-items-center gap-2 mb-3">
                  <span class="badge text-bg-primary py-1.5 px-3 rounded-pill fw-medium"><i class="bi bi-file-earmark-plus me-1.5"></i>Perizinan</span>
                </div>
                <h5 class="fw-bold mb-2 fs-6 fs-md-5">Butuh Izin, Cuti, atau Sakit?</h5>
                <p class="text-muted mb-0 small">Ajukan permohonan perizinan secara online. Admin akan meninjau dan menyetujui pengajuan Anda.</p>
              </div>
              <button class="btn btn-primary rounded-3 px-4 py-2.5 fw-semibold shadow-sm flex-shrink-0" data-bs-toggle="modal" data-bs-target="#newLeaveModal">
                <i class="bi bi-plus-lg me-2"></i> Ajukan Perizinan Baru <i class="bi bi-arrow-right ms-1"></i>
              </button>
            </div>
          </div>

          <!-- Metric Cards -->
          <section class="row g-3 mb-4" aria-label="Leave quota metrics">
            <div class="col-12 col-sm-6 col-xl-4">
              <article class="metric-card metric-primary">
                <div class="metric-top">
                  <span class="metric-label">SISA CUTI TAHUNAN</span>
                  <span class="metric-icon"><i class="bi bi-calendar2-check-fill" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $remainingLeaveDays }} <small class="fs-6 fw-normal text-muted">/ {{ $maxLeaveDays }} Hari</small></div>
                <div class="metric-meta">
                  <span class="text-primary">{{ $usedLeaveDays }} hari terpakai</span>
                  <span>sisa {{ $remainingLeaveDays }} hari tahun {{ now()->year }}</span>
                </div>
                <div class="mt-2 quota-progress">
                  @php
                    $quotaPercent = $maxLeaveDays > 0 ? min(100, round(($remainingLeaveDays / $maxLeaveDays) * 100)) : 0;
                  @endphp
                  <div class="quota-progress-bar bg-primary" style="width: {{ $quotaPercent }}%;"></div>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-4">
              <article class="metric-card metric-warning">
                <div class="metric-top">
                  <span class="metric-label">PENGAJUAN BULAN INI</span>
                  <span class="metric-icon"><i class="bi bi-send-fill" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $monthlyRequestCount }}</div>
                <div class="metric-meta">
                  <span class="text-warning">{{ now()->translatedFormat('F') }}</span>
                  <span>{{ now()->year }}</span>
                </div>
              </article>
            </div>

            @php
              $lastStatus = $lastRequest?->status;
              $cardClass = match($lastStatus) {
                'APPROVED' => 'metric-success',
                'REJECTED' => 'metric-danger',
                'PENDING' => 'metric-warning',
                default => 'metric-primary',
              };
              $iconClass = match($lastStatus) {
                'APPROVED' => 'bi-patch-check-fill',
                'REJECTED' => 'bi-x-circle-fill',
                'PENDING' => 'bi-hourglass-split',
                default => 'bi-dash-circle',
              };
            @endphp
            <div class="col-12 col-sm-6 col-xl-4">
              <article class="metric-card {{ $cardClass }}">
                <div class="metric-top">
                  <span class="metric-label">STATUS TERAKHIR</span>
                  <span class="metric-icon"><i class="bi {{ $iconClass }}" aria-hidden="true"></i></span>
                </div>
                @if($lastRequest)
                  <div class="metric-value" style="font-size: 1.35rem;">
                    @if($lastRequest->status === 'APPROVED')
                      <span class="text-success"><i class="bi bi-check-circle me-1"></i>Disetujui</span>
                    @elseif($lastRequest->status === 'REJECTED')
                      <span class="text-danger"><i class="bi bi-x-circle me-1"></i>Ditolak</span>
                    @else
                      <span class="text-warning"><i class="bi bi-hourglass-split me-1"></i>Menunggu</span>
                    @endif
                  </div>
                  <div class="metric-meta">
                    <span class="fw-semibold">{{ $lastRequest->type }}</span>
                    <span>{{ \Carbon\Carbon::parse($lastRequest->start_date)->translatedFormat('d M') }}{{ $lastRequest->start_date != $lastRequest->end_date ? ' — '.\Carbon\Carbon::parse($lastRequest->end_date)->translatedFormat('d M Y') : ' '.\Carbon\Carbon::parse($lastRequest->start_date)->translatedFormat('Y') }}</span>
                  </div>
                @else
                  <div class="metric-value" style="font-size: 1.25rem;">Belum Ada</div>
                  <div class="metric-meta">
                    <span class="text-muted">Belum pernah mengajukan</span>
                  </div>
                @endif
              </article>
            </div>
          </section>

          <!-- Riwayat Pengajuan Table -->
          <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
              <div>
                <h5 class="card-title h6 mb-1 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Pengajuan Saya</h5>
                <p class="card-subtitle text-muted small">Semua pengajuan perizinan yang pernah Anda buat.</p>
              </div>
              <div class="d-flex align-items-center gap-2">
                <form action="{{ route('employee.leaves.index') }}" method="GET" class="d-flex align-items-center gap-2">
                  <select name="status" class="form-select form-select-sm rounded-pill" style="width: auto;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Pending</option>
                    <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Disetujui</option>
                    <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Ditolak</option>
                  </select>
                  @if(request('status'))
                  <a href="{{ route('employee.leaves.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                    Reset
                  </a>
                  @endif
                </form>
              </div>
            </div>

            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table align-middle table-hover mb-0" id="empLeavesTable">
                  <thead class="table-light">
                    <tr>
                      <th class="ps-4">Tipe</th>
                      <th>Tanggal</th>
                      <th>Durasi</th>
                      <th>Alasan</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($leaveRequests as $leave)
                    <tr>
                      <td class="ps-4">
                        @if($leave->type === 'CUTI')
                          <span class="badge leave-type-cuti rounded-pill px-2 py-1"><i class="bi bi-calendar2-week me-1"></i>Cuti</span>
                        @elseif($leave->type === 'SAKIT')
                          <span class="badge leave-type-sakit rounded-pill px-2 py-1"><i class="bi bi-heart-pulse me-1"></i>Sakit</span>
                        @else
                          <span class="badge leave-type-izin rounded-pill px-2 py-1"><i class="bi bi-door-open me-1"></i>Izin</span>
                        @endif
                      </td>
                      <td>
                        <strong class="d-block text-body small">
                          {{ \Carbon\Carbon::parse($leave->start_date)->translatedFormat('d M Y') }}
                          @if($leave->start_date != $leave->end_date)
                            — {{ \Carbon\Carbon::parse($leave->end_date)->translatedFormat('d M Y') }}
                          @endif
                        </strong>
                      </td>
                      <td><span class="badge text-bg-secondary">{{ $leave->duration_days }} Hari</span></td>
                      <td>
                        <span class="text-truncate d-inline-block small" style="max-width:220px;" title="{{ $leave->reason }}">
                          {{ $leave->reason }}
                        </span>
                      </td>
                      <td>
                        @if($leave->status === 'APPROVED')
                          <span class="badge text-bg-success rounded-pill px-2 py-1"><i class="bi bi-check-circle me-1"></i>Disetujui</span>
                        @elseif($leave->status === 'REJECTED')
                          <span class="badge text-bg-danger rounded-pill px-2 py-1"><i class="bi bi-x-circle me-1"></i>Ditolak</span>
                        @else
                          <span class="badge text-bg-warning text-dark rounded-pill px-2 py-1"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                        @endif
                      </td>
                      <td>
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="showEmpLeaveDetail({{ json_encode([
                          'type' => $leave->type,
                          'date' => \Carbon\Carbon::parse($leave->start_date)->translatedFormat('d M Y') . ($leave->start_date != $leave->end_date ? ' — ' . \Carbon\Carbon::parse($leave->end_date)->translatedFormat('d M Y') : ''),
                          'duration' => $leave->duration_days . ' Hari',
                          'reason' => $leave->reason,
                          'status' => $leave->status,
                          'attachment_url' => $leave->attachment_path ? asset('storage/' . $leave->attachment_path) : null,
                          'attachment_name' => $leave->attachment_path ? basename($leave->attachment_path) : null,
                          'submitted' => \Carbon\Carbon::parse($leave->created_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y — H:i') . ' WIB',
                          'action_date' => $leave->reviewed_at ? \Carbon\Carbon::parse($leave->reviewed_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y — H:i') . ' WIB' : null,
                          'action_by' => $leave->reviewer ? $leave->reviewer->name : 'Admin HRIS',
                          'reject_note' => $leave->reject_reason,
                        ]) }})">
                          <i class="bi bi-eye me-1"></i> Detail
                        </button>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="6" class="text-center text-muted py-4">Belum ada pengajuan perizinan yang dibuat.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

            @if($leaveRequests->total() > 0)
            <div class="card-footer bg-transparent border-top px-4 py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
              <span class="text-muted small">Menampilkan {{ $leaveRequests->firstItem() }} sampai {{ $leaveRequests->lastItem() }} dari {{ $leaveRequests->total() }} pengajuan</span>
              <div>
                {{ $leaveRequests->links() }}
              </div>
            </div>
            @endif
          </div>

        </div>
      </main>
    </div>
  </div>

  <!-- Modal: Ajukan Perizinan Baru -->
  <div class="modal fade" id="newLeaveModal" tabindex="-1" aria-hidden="true" aria-labelledby="newLeaveModalLabel">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-0 pb-0">
          <h6 class="modal-title fw-bold" id="newLeaveModalLabel"><i class="bi bi-file-earmark-plus text-primary me-2"></i>Ajukan Perizinan Baru</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('employee.leaves.store') }}" method="POST" enctype="multipart/form-data" id="newLeaveForm">
          @csrf
          <div class="modal-body p-4">
            <!-- Tipe Perizinan -->
            <div class="mb-4">
              <label class="form-label fw-semibold mb-3">Tipe Perizinan <span class="text-danger">*</span></label>
              <div class="leave-type-selector">
                <label class="leave-type-option">
                  <input type="radio" name="type" value="IZIN" checked onchange="toggleQuotaVisibility()">
                  <div class="type-card">
                    <i class="bi bi-door-open type-icon text-info"></i>
                    <span class="type-label">Izin</span>
                    <small class="text-muted">Keperluan pribadi</small>
                  </div>
                </label>
                <label class="leave-type-option">
                  <input type="radio" name="type" value="CUTI" onchange="toggleQuotaVisibility()">
                  <div class="type-card">
                    <i class="bi bi-calendar2-week type-icon text-primary"></i>
                    <span class="type-label">Cuti</span>
                    <small class="text-muted">Cuti tahunan</small>
                  </div>
                </label>
                <label class="leave-type-option">
                  <input type="radio" name="type" value="SAKIT" onchange="toggleQuotaVisibility()">
                  <div class="type-card">
                    <i class="bi bi-heart-pulse type-icon text-danger"></i>
                    <span class="type-label">Sakit</span>
                    <small class="text-muted">Sakit / rawat</small>
                  </div>
                </label>
              </div>
            </div>

            <!-- Tanggal -->
            <div class="row g-3 mb-4">
              <div class="col-sm-6">
                <label class="form-label fw-semibold" for="startDate">Tanggal Mulai <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                  <input type="date" class="form-control" id="startDate" name="start_date" min="{{ now()->toDateString() }}" required>
                </div>
              </div>
              <div class="col-sm-6">
                <label class="form-label fw-semibold" for="endDate">Tanggal Selesai <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                  <input type="date" class="form-control" id="endDate" name="end_date" min="{{ now()->toDateString() }}" required>
                </div>
              </div>
            </div>

            <!-- Durasi Info -->
            <div class="alert alert-info border-0 rounded-3 py-2 px-3 mb-4 d-flex align-items-center gap-2" id="durationAlert" style="display: none;">
              <i class="bi bi-info-circle fs-5"></i>
              <span class="small fw-medium" id="durationText">Durasi: 1 hari kerja</span>
            </div>

            <!-- Alasan -->
            <div class="mb-4">
              <label class="form-label fw-semibold" for="leaveReason">Alasan <span class="text-danger">*</span></label>
              <textarea class="form-control" id="leaveReason" name="reason" rows="3" placeholder="Jelaskan alasan perizinan Anda secara jelas..." required style="resize: vertical;"></textarea>
              <div class="form-text">Berikan alasan yang spesifik untuk memudahkan verifikasi admin.</div>
            </div>

            <!-- Upload Lampiran -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Lampiran <small class="text-muted fw-normal">(opsional)</small></label>
              <div class="border rounded-3 p-4 text-center bg-body-tertiary" style="border-style: dashed !important; cursor: pointer;" id="dropZone">
                <i class="bi bi-cloud-arrow-up fs-1 text-muted d-block mb-2"></i>
                <p class="text-muted mb-1 small">Drag & drop file di sini atau <span class="text-primary fw-semibold">klik untuk upload</span></p>
                <small class="text-muted">Surat dokter, undangan, dll. Maks. 5MB (PDF, JPG, PNG)</small>
                <input type="file" class="d-none" id="attachmentInput" name="attachment" accept=".pdf,.jpg,.jpeg,.png">
              </div>
              <div class="mt-2" id="filePreview" style="display: none;">
                <div class="d-flex align-items-center gap-2 p-2 bg-body-secondary rounded-3 border">
                  <i class="bi bi-file-earmark-check text-success fs-4"></i>
                  <div class="flex-grow-1">
                    <span class="fw-medium small d-block" id="fileName">file_lampiran.pdf</span>
                    <small class="text-muted" id="fileSize">0 KB</small>
                  </div>
                  <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2" id="removeFile" title="Hapus file">
                    <i class="bi bi-x-lg"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- Info Sisa Cuti -->
            <div class="p-3 bg-body-tertiary rounded-3 border mb-0" id="quotaBox">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <small class="text-muted d-block fw-semibold mb-1"><i class="bi bi-calendar2-check me-1"></i>SISA CUTI TAHUNAN ANDA</small>
                  <span class="fw-bold text-primary">{{ $remainingLeaveDays }} Hari</span> <small class="text-muted">dari {{ $maxLeaveDays }} hari</small>
                </div>
                <div class="text-end">
                  <small class="text-muted d-block fw-semibold mb-1"><i class="bi bi-clock me-1"></i>TERPAKAI</small>
                  <span class="fw-bold">{{ $usedLeaveDays }} Hari</span>
                </div>
              </div>
              <div class="mt-2 quota-progress">
                <div class="quota-progress-bar bg-primary" style="width: {{ $quotaPercent }}%;"></div>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0 pt-0 gap-2">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary rounded-pill px-4">
              <i class="bi bi-send me-1"></i> Kirim Pengajuan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal: Detail Pengajuan -->
  <div class="modal fade" id="empLeaveDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-0 pb-0">
          <h6 class="modal-title fw-bold"><i class="bi bi-file-earmark-text text-primary me-2"></i>Detail Pengajuan</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
              <span class="badge rounded-pill px-2 py-1 mb-2" id="empDetailTypeBadge">Cuti</span>
              <h6 class="fw-bold mb-0" id="empDetailDate">-</h6>
              <small class="text-muted" id="empDetailDuration">-</small>
            </div>
            <div id="empDetailStatusBadge"></div>
          </div>

          <div class="mb-4">
            <small class="text-muted d-block mb-2"><i class="bi bi-chat-left-text me-1"></i>Alasan Pengajuan</small>
            <div class="p-3 bg-body-secondary rounded-3 border" id="empDetailReason" style="font-size: 0.9rem;">-</div>
          </div>

          <div class="mb-4" id="empDetailAttachSection" style="display: none;">
            <small class="text-muted d-block mb-2"><i class="bi bi-paperclip me-1"></i>Lampiran</small>
            <div class="d-flex align-items-center gap-2 p-2 bg-body-tertiary rounded-3 border">
              <i class="bi bi-file-earmark-arrow-down text-primary fs-4"></i>
              <div class="flex-grow-1">
                <span class="fw-medium small d-block" id="empDetailAttachName">-</span>
              </div>
              <a href="#" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="empDetailAttachLink" target="_blank">
                <i class="bi bi-download me-1"></i> Unduh
              </a>
            </div>
          </div>

          <!-- Timeline -->
          <div>
            <small class="text-muted d-block mb-2"><i class="bi bi-clock-history me-1"></i>Riwayat Status</small>
            <div class="ps-2">
              <div class="timeline-item">
                <span class="timeline-dot bg-primary"></span>
                <div>
                  <p class="fw-semibold small mb-0" id="empDetailSubmitted">-</p>
                  <small class="text-muted">Diajukan oleh Anda</small>
                </div>
              </div>
              <div class="timeline-item" id="empDetailActionTimeline" style="display: none;">
                <span class="timeline-dot" id="empDetailActionDot"></span>
                <div>
                  <p class="fw-semibold small mb-0" id="empDetailActionText">-</p>
                  <small class="text-muted" id="empDetailActionBy">-</small>
                </div>
              </div>
            </div>
          </div>

          <!-- Rejection Note -->
          <div class="mt-3 p-3 bg-danger-subtle rounded-3 border border-danger-subtle" id="empDetailRejectNote" style="display: none;">
            <small class="text-danger fw-semibold d-block mb-1"><i class="bi bi-exclamation-triangle me-1"></i>Catatan Penolakan dari Admin</small>
            <span class="small" id="empDetailRejectText">-</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script>
    function showEmpLeaveDetail(d) {
      let typeBadge = '';
      if (d.type === 'CUTI') {
        typeBadge = '<span class="badge leave-type-cuti rounded-pill px-2 py-1"><i class="bi bi-calendar2-week me-1"></i>Cuti</span>';
      } else if (d.type === 'SAKIT') {
        typeBadge = '<span class="badge leave-type-sakit rounded-pill px-2 py-1"><i class="bi bi-heart-pulse me-1"></i>Sakit</span>';
      } else {
        typeBadge = '<span class="badge leave-type-izin rounded-pill px-2 py-1"><i class="bi bi-door-open me-1"></i>Izin</span>';
      }
      document.getElementById('empDetailTypeBadge').outerHTML = `<span id="empDetailTypeBadge">${typeBadge}</span>`;
      document.getElementById('empDetailDate').textContent = d.date;
      document.getElementById('empDetailDuration').textContent = 'Durasi: ' + d.duration;
      document.getElementById('empDetailReason').textContent = d.reason;
      document.getElementById('empDetailSubmitted').textContent = 'Diajukan pada ' + d.submitted;

      // Status badge
      let statusHtml = '';
      if (d.status === 'PENDING') {
        statusHtml = '<span class="badge text-bg-warning text-dark rounded-pill px-3 py-2"><i class="bi bi-hourglass-split me-1"></i>Pending</span>';
      } else if (d.status === 'APPROVED') {
        statusHtml = '<span class="badge text-bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle me-1"></i>Disetujui</span>';
      } else {
        statusHtml = '<span class="badge text-bg-danger rounded-pill px-3 py-2"><i class="bi bi-x-circle me-1"></i>Ditolak</span>';
      }
      document.getElementById('empDetailStatusBadge').innerHTML = statusHtml;

      // Attachment
      const attachSection = document.getElementById('empDetailAttachSection');
      if (d.attachment_url) {
        attachSection.style.display = 'block';
        document.getElementById('empDetailAttachName').textContent = d.attachment_name;
        document.getElementById('empDetailAttachLink').href = d.attachment_url;
      } else {
        attachSection.style.display = 'none';
      }

      // Timeline action
      const timelineAction = document.getElementById('empDetailActionTimeline');
      if (d.status !== 'PENDING' && d.action_date) {
        timelineAction.style.display = 'block';
        const isApproved = d.status === 'APPROVED';
        const dot = document.getElementById('empDetailActionDot');
        dot.className = 'timeline-dot ' + (isApproved ? 'bg-success' : 'bg-danger');
        document.getElementById('empDetailActionText').textContent = (isApproved ? 'Disetujui' : 'Ditolak') + ' pada ' + d.action_date;
        document.getElementById('empDetailActionBy').textContent = 'Oleh: ' + (d.action_by || 'Admin');
      } else {
        timelineAction.style.display = 'none';
      }

      // Rejection note
      const rejectNote = document.getElementById('empDetailRejectNote');
      if (d.status === 'REJECTED' && d.reject_note) {
        rejectNote.style.display = 'block';
        document.getElementById('empDetailRejectText').textContent = d.reject_note;
      } else {
        rejectNote.style.display = 'none';
      }

      const modal = new bootstrap.Modal(document.getElementById('empLeaveDetailModal'));
      modal.show();
    }

    function toggleQuotaVisibility() {
      // Keep quota box visible for all or emphasize for CUTI
    }

    // File upload UI interactions & duration calculator
    document.addEventListener('DOMContentLoaded', function() {
      const dropZone = document.getElementById('dropZone');
      const fileInput = document.getElementById('attachmentInput');
      const filePreview = document.getElementById('filePreview');
      const removeFileBtn = document.getElementById('removeFile');

      dropZone.addEventListener('click', () => fileInput.click());
      dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.style.borderColor = 'var(--bs-primary)'; });
      dropZone.addEventListener('dragleave', () => { dropZone.style.borderColor = ''; });
      dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '';
        if (e.dataTransfer.files.length) {
          fileInput.files = e.dataTransfer.files;
          handleFile(e.dataTransfer.files[0]);
        }
      });

      fileInput.addEventListener('change', function() {
        if (this.files.length) {
          handleFile(this.files[0]);
        }
      });

      removeFileBtn.addEventListener('click', function() {
        fileInput.value = '';
        filePreview.style.display = 'none';
        dropZone.style.display = '';
      });

      function handleFile(file) {
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
        filePreview.style.display = 'block';
        dropZone.style.display = 'none';
      }

      // Duration calculator
      const startDate = document.getElementById('startDate');
      const endDate = document.getElementById('endDate');
      const durationAlert = document.getElementById('durationAlert');
      const durationText = document.getElementById('durationText');

      function calcDuration() {
        if (startDate.value && endDate.value) {
          const start = new Date(startDate.value);
          const end = new Date(endDate.value);
          if (end >= start) {
            const diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
            durationText.textContent = `Durasi: ${diff} hari`;
            durationAlert.style.display = 'flex';
          } else {
            durationAlert.style.display = 'none';
          }
        } else {
          durationAlert.style.display = 'none';
        }
      }

      startDate.addEventListener('change', function() {
        if (endDate.value && endDate.value < this.value) {
          endDate.value = this.value;
        }
        endDate.min = this.value;
        calcDuration();
      });
      endDate.addEventListener('change', calcDuration);
    });
  </script>
</body>
</html>
