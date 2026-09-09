<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Absensi FamilyMart - Kelola Perizinan Karyawan">
  <title>Perizinan | Admin Absensi FamilyMart</title>
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
    .filter-bar { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; }
    .filter-bar .form-select, .filter-bar .form-control { max-width: 200px; }
    @media (max-width: 576px) { .filter-bar .form-select, .filter-bar .form-control { max-width: 100%; } }
    .timeline-item { position: relative; padding-left: 1.5rem; padding-bottom: 1rem; border-left: 2px solid var(--bs-border-color); }
    .timeline-item:last-child { border-left: 2px solid transparent; padding-bottom: 0; }
    .timeline-dot { position: absolute; left: -7px; top: 4px; width: 12px; height: 12px; border-radius: 50%; border: 2px solid var(--bs-body-bg); }
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

          <!-- Page Heading with Setting Action -->
          <div class="page-heading d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="bi bi-file-earmark-text" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1">MANAJEMEN PERIZINAN</p>
                <h1 class="h3 mb-1">Kelola Perizinan Karyawan</h1>
                <p class="text-muted mb-0">Persetujuan dan monitoring pengajuan izin, cuti, dan sakit seluruh karyawan.</p>
              </div>
            </div>
            <div class="d-flex align-items-center gap-2">
              <button class="btn btn-outline-primary rounded-3 px-3 py-2 fw-medium shadow-sm" data-bs-toggle="modal" data-bs-target="#settingModal">
                <i class="bi bi-gear-fill me-1.5"></i> Atur Batas Cuti ({{ $leaveSetting->max_annual_leave_days }} Hari)
              </button>
            </div>
          </div>

          <!-- Metric Cards -->
          <section class="row g-3 mt-1" aria-label="Leave metrics">
            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-primary">
                <div class="metric-top">
                  <span class="metric-label">TOTAL PENGAJUAN</span>
                  <span class="metric-icon"><i class="bi bi-file-earmark-text-fill" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $totalRequests }}</div>
                <div class="metric-meta">
                  <span class="text-primary">Database</span>
                  <span>seluruh riwayat</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-warning">
                <div class="metric-top">
                  <span class="metric-label">MENUNGGU PERSETUJUAN</span>
                  <span class="metric-icon"><i class="bi bi-hourglass-split" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $pendingCount }}</div>
                <div class="metric-meta">
                  <span class="text-warning">Pending</span>
                  <span>perlu ditindaklanjuti</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-success">
                <div class="metric-top">
                  <span class="metric-label">DISETUJUI</span>
                  <span class="metric-icon"><i class="bi bi-check-circle-fill" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $approvedCount }}</div>
                <div class="metric-meta">
                  <span class="text-success">Approved</span>
                  <span>telah disetujui</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-danger">
                <div class="metric-top">
                  <span class="metric-label">DITOLAK</span>
                  <span class="metric-icon"><i class="bi bi-x-circle-fill" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $rejectedCount }}</div>
                <div class="metric-meta">
                  <span class="text-danger">Rejected</span>
                  <span>pengajuan ditolak</span>
                </div>
              </article>
            </div>
          </section>

          <!-- Filter & Table Panel -->
          <section class="panel mt-3">
            <div class="panel-header">
              <div>
                <h2 class="h5 mb-1 section-title"><i class="bi bi-funnel" aria-hidden="true"></i><span>Daftar Pengajuan Perizinan</span></h2>
                <p class="text-muted mb-0">Data pengajuan izin, cuti, dan sakit dari seluruh karyawan.</p>
              </div>
            </div>

            <!-- Filter Bar -->
            <div class="px-3 pt-3 pb-2">
              <form action="{{ route('admin.leaves.index') }}" method="GET" class="filter-bar">
                <select class="form-select form-select-sm" name="status" id="filterStatus" onchange="this.form.submit()">
                  <option value="">Semua Status</option>
                  <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Pending</option>
                  <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Disetujui</option>
                  <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <select class="form-select form-select-sm" name="type" id="filterType" onchange="this.form.submit()">
                  <option value="">Semua Tipe</option>
                  <option value="IZIN" {{ request('type') === 'IZIN' ? 'selected' : '' }}>Izin</option>
                  <option value="CUTI" {{ request('type') === 'CUTI' ? 'selected' : '' }}>Cuti</option>
                  <option value="SAKIT" {{ request('type') === 'SAKIT' ? 'selected' : '' }}>Sakit</option>
                </select>
                <input type="text" class="form-control form-control-sm" name="search" placeholder="Cari nama atau NIP..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-primary px-3 rounded-pill">
                  <i class="bi bi-search me-1"></i> Cari
                </button>
                @if(request()->hasAny(['status', 'type', 'search']))
                <a href="{{ route('admin.leaves.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                  Reset
                </a>
                @endif
              </form>
            </div>

            <!-- Data Table -->
            <div class="table-responsive">
              <table class="table align-middle mb-0" id="leavesTable">
                <thead>
                  <tr>
                    <th scope="col">Karyawan</th>
                    <th scope="col">Tipe</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Durasi</th>
                    <th scope="col">Alasan</th>
                    <th scope="col">Status</th>
                    <th scope="col">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($leaveRequests as $leave)
                  <tr data-status="{{ strtolower($leave->status) }}" data-type="{{ strtolower($leave->type) }}">
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <img class="avatar-img avatar-sm rounded-circle shadow-sm" src="{{ $leave->user->registered_face_photo ? asset($leave->user->registered_face_photo) : 'https://ui-avatars.com/api/?name='.urlencode($leave->user->name).'&background=0D8ABC&color=fff' }}" alt="{{ $leave->user->name }}">
                        <div>
                          <p class="fw-semibold mb-0">{{ $leave->user->name }}</p>
                          <small class="text-muted">NIP. {{ $leave->user->nip }}</small>
                        </div>
                      </div>
                    </td>
                    <td>
                      @if($leave->type === 'CUTI')
                        <span class="badge leave-type-cuti rounded-pill px-2 py-1"><i class="bi bi-calendar-event me-1"></i>Cuti</span>
                      @elseif($leave->type === 'SAKIT')
                        <span class="badge leave-type-sakit rounded-pill px-2 py-1"><i class="bi bi-heart-pulse me-1"></i>Sakit</span>
                      @else
                        <span class="badge leave-type-izin rounded-pill px-2 py-1"><i class="bi bi-door-open me-1"></i>Izin</span>
                      @endif
                    </td>
                    <td>
                      <span class="small fw-medium">{{ \Carbon\Carbon::parse($leave->start_date)->translatedFormat('d M Y') }}</span>
                      @if($leave->start_date != $leave->end_date)
                        <span class="text-muted small">s/d</span>
                        <span class="small fw-medium">{{ \Carbon\Carbon::parse($leave->end_date)->translatedFormat('d M Y') }}</span>
                      @endif
                    </td>
                    <td><span class="badge text-bg-secondary">{{ $leave->duration_days }} Hari</span></td>
                    <td>
                      <span class="text-truncate d-inline-block small" style="max-width:180px;" title="{{ $leave->reason }}">
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
                      <div class="d-flex gap-1">
                        @if($leave->status === 'PENDING')
                        <button class="btn btn-sm btn-outline-success rounded-pill px-2" title="Setujui" onclick="openApproveModal({{ $leave->id }}, '{{ addslashes($leave->user->name) }}')">
                          <i class="bi bi-check-lg"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Tolak" onclick="openRejectModal({{ $leave->id }}, '{{ addslashes($leave->user->name) }}')">
                          <i class="bi bi-x-lg"></i>
                        </button>
                        @endif
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-2" title="Detail" onclick="showLeaveDetail({{ json_encode([
                          'id' => $leave->id,
                          'name' => $leave->user->name,
                          'nip' => $leave->user->nip,
                          'photo' => $leave->user->registered_face_photo ? asset($leave->user->registered_face_photo) : 'https://ui-avatars.com/api/?name='.urlencode($leave->user->name).'&background=0D8ABC&color=fff',
                          'type' => $leave->type,
                          'date' => \Carbon\Carbon::parse($leave->start_date)->translatedFormat('d M Y') . ($leave->start_date != $leave->end_date ? ' s/d ' . \Carbon\Carbon::parse($leave->end_date)->translatedFormat('d M Y') : ''),
                          'duration' => $leave->duration_days . ' Hari',
                          'reason' => $leave->reason,
                          'status' => $leave->status,
                          'attachment_url' => $leave->attachment_path ? asset('storage/' . $leave->attachment_path) : null,
                          'attachment_name' => $leave->attachment_path ? basename($leave->attachment_path) : null,
                          'submitted' => \Carbon\Carbon::parse($leave->created_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y — H:i') . ' WIB',
                          'reviewer_name' => $leave->reviewer ? $leave->reviewer->name : null,
                          'reviewed_at' => $leave->reviewed_at ? \Carbon\Carbon::parse($leave->reviewed_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y — H:i') . ' WIB' : null,
                          'reject_reason' => $leave->reject_reason,
                        ]) }})">
                          <i class="bi bi-eye"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="7" class="text-center text-muted py-4">Tidak ada data pengajuan perizinan ditemukan.</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            @if($leaveRequests->total() > 0)
            <div class="panel-footer border-top px-3 py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
              <span class="text-muted small">Menampilkan {{ $leaveRequests->firstItem() }} sampai {{ $leaveRequests->lastItem() }} dari {{ $leaveRequests->total() }} pengajuan</span>
              <div>
                {{ $leaveRequests->links() }}
              </div>
            </div>
            @endif
          </section>

        </div>
      </main>
    </div>
  </div>

  <!-- Modal: Pengaturan Batas Cuti Tahunan -->
  <div class="modal fade" id="settingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-0 pb-0">
          <h6 class="modal-title fw-bold"><i class="bi bi-sliders text-primary me-2"></i>Pengaturan Batas Cuti Tahunan</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('admin.leaves.settings.update') }}" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-body p-4">
            <p class="text-muted small mb-3">Tentukan batas kuota hak cuti tahunan maksimal yang dapat digunakan oleh seluruh karyawan dalam satu tahun kalender.</p>

            <div class="mb-3">
              <label for="maxLeaveDaysInput" class="form-label fw-semibold">Batas Cuti Tahunan (Hari) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                <input type="number" class="form-control" id="maxLeaveDaysInput" name="max_annual_leave_days" min="1" max="365" value="{{ $leaveSetting->max_annual_leave_days }}" required>
                <span class="input-group-text">Hari / Tahun</span>
              </div>
              <div class="form-text">Nilai ini otomatis dijadikan dasar kalkulasi sisa kuota cuti untuk seluruh karyawan.</div>
            </div>
          </div>
          <div class="modal-footer border-0 pt-0 gap-2">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary rounded-pill px-4">
              <i class="bi bi-save me-1"></i> Simpan Pengaturan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal: Konfirmasi Approve -->
  <div class="modal fade" id="approveConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-0 pb-0">
          <h6 class="modal-title fw-bold text-success"><i class="bi bi-check-circle-fill me-2"></i>Setujui Pengajuan</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="approveForm" method="POST" action="">
          @csrf
          <div class="modal-body p-4">
            <p class="mb-0">Apakah Anda yakin ingin menyetujui pengajuan perizinan untuk <strong id="approveEmployeeName">-</strong>?</p>
            <small class="text-muted d-block mt-2">Status pengajuan akan diperbarui menjadi <strong>DISETUJUI</strong> dan kuota cuti karyawan akan diupdate.</small>
          </div>
          <div class="modal-footer border-0 pt-0 gap-2">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-success rounded-pill px-4">
              <i class="bi bi-check-lg me-1"></i> Ya, Setujui
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal: Tolak Pengajuan -->
  <div class="modal fade" id="rejectConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-0 pb-0">
          <h6 class="modal-title fw-bold text-danger"><i class="bi bi-x-circle-fill me-2"></i>Tolak Pengajuan</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="rejectForm" method="POST" action="">
          @csrf
          <div class="modal-body p-4">
            <p class="mb-2">Tolak pengajuan perizinan untuk <strong id="rejectEmployeeName">-</strong>:</p>
            <div class="mb-2">
              <label for="rejectReasonInput" class="form-label fw-semibold small">Alasan Penolakan <span class="text-danger">*</span></label>
              <textarea class="form-control" id="rejectReasonInput" name="reject_reason" rows="3" placeholder="Contoh: Sisa kuota cuti habis, atau lampiran tidak lengkap..." required></textarea>
            </div>
            <small class="text-muted">Alasan penolakan akan dapat dilihat oleh karyawan bersangkutan.</small>
          </div>
          <div class="modal-footer border-0 pt-0 gap-2">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger rounded-pill px-4">
              <i class="bi bi-x-lg me-1"></i> Tolak Pengajuan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Detail Perizinan -->
  <div class="modal fade" id="leaveDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header border-0 pb-0">
          <h6 class="modal-title fw-bold"><i class="bi bi-file-earmark-text text-primary me-2"></i>Detail Pengajuan Perizinan</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <!-- Employee Info -->
          <div class="d-flex align-items-center gap-3 mb-4">
            <img class="rounded-circle shadow-sm" id="modalAvatarImg" src="" alt="Avatar" style="width:48px; height:48px; object-fit: cover;">
            <div>
              <p class="fw-bold mb-0 fs-6" id="modalName">-</p>
              <small class="text-muted" id="modalNip">-</small>
            </div>
            <div class="ms-auto" id="modalStatusBadge"></div>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-sm-4">
              <div class="p-3 bg-body-tertiary rounded-3 border h-100">
                <small class="text-muted d-block mb-1"><i class="bi bi-tag me-1"></i>Tipe Perizinan</small>
                <span class="fw-semibold" id="modalType">-</span>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="p-3 bg-body-tertiary rounded-3 border h-100">
                <small class="text-muted d-block mb-1"><i class="bi bi-calendar-range me-1"></i>Tanggal</small>
                <span class="fw-semibold" id="modalDate">-</span>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="p-3 bg-body-tertiary rounded-3 border h-100">
                <small class="text-muted d-block mb-1"><i class="bi bi-clock me-1"></i>Durasi</small>
                <span class="fw-semibold" id="modalDuration">-</span>
              </div>
            </div>
          </div>

          <div class="mb-4">
            <small class="text-muted d-block mb-2"><i class="bi bi-chat-left-text me-1"></i>Alasan Pengajuan</small>
            <div class="p-3 bg-body-secondary rounded-3 border" id="modalReason" style="font-size: 0.9rem;">-</div>
          </div>

          <!-- Lampiran -->
          <div class="mb-4" id="modalAttachmentSection" style="display: none;">
            <small class="text-muted d-block mb-2"><i class="bi bi-paperclip me-1"></i>Lampiran</small>
            <div class="d-flex align-items-center gap-2 p-2 bg-body-tertiary rounded-3 border">
              <i class="bi bi-file-earmark-arrow-down text-primary fs-4"></i>
              <div class="flex-grow-1">
                <span class="fw-medium small d-block" id="modalAttachmentName">-</span>
              </div>
              <a href="#" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="modalAttachmentLink" target="_blank">
                <i class="bi bi-download me-1"></i> Unduh
              </a>
            </div>
          </div>

          <!-- Timeline Riwayat -->
          <div>
            <small class="text-muted d-block mb-2"><i class="bi bi-clock-history me-1"></i>Riwayat Pengajuan</small>
            <div class="ps-2">
              <div class="timeline-item">
                <span class="timeline-dot bg-primary"></span>
                <div>
                  <p class="fw-semibold small mb-0" id="modalTimelineSubmit">-</p>
                  <small class="text-muted">Diajukan oleh karyawan</small>
                </div>
              </div>
              <div class="timeline-item" id="modalTimelineAction" style="display: none;">
                <span class="timeline-dot" id="modalTimelineDot"></span>
                <div>
                  <p class="fw-semibold small mb-0" id="modalTimelineActionText">-</p>
                  <small class="text-muted" id="modalTimelineActionBy">-</small>
                </div>
              </div>
            </div>
          </div>

          <!-- Rejection Note -->
          <div class="mt-3 p-3 bg-danger-subtle rounded-3 border border-danger-subtle" id="modalRejectSection" style="display: none;">
            <small class="text-danger fw-semibold d-block mb-1"><i class="bi bi-exclamation-triangle me-1"></i>Catatan Penolakan</small>
            <span class="small" id="modalRejectNote">-</span>
          </div>
        </div>

        <!-- Modal Footer with Actions (only for Pending) -->
        <div class="modal-footer border-0 pt-0 gap-2" id="modalActions" style="display: none;">
          <button type="button" class="btn btn-outline-danger rounded-pill px-4" id="modalRejectBtn">
            <i class="bi bi-x-lg me-1"></i>Tolak
          </button>
          <button type="button" class="btn btn-success rounded-pill px-4" id="modalApproveBtn">
            <i class="bi bi-check-lg me-1"></i>Setujui
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script>
    function openApproveModal(id, name) {
      document.getElementById('approveEmployeeName').textContent = name;
      document.getElementById('approveForm').action = `{{ url('/admin/leaves') }}/${id}/approve`;
      const modal = new bootstrap.Modal(document.getElementById('approveConfirmModal'));
      modal.show();
    }

    function openRejectModal(id, name) {
      document.getElementById('rejectEmployeeName').textContent = name;
      document.getElementById('rejectForm').action = `{{ url('/admin/leaves') }}/${id}/reject`;
      document.getElementById('rejectReasonInput').value = '';
      const modal = new bootstrap.Modal(document.getElementById('rejectConfirmModal'));
      modal.show();
    }

    function showLeaveDetail(d) {
      document.getElementById('modalAvatarImg').src = d.photo;
      document.getElementById('modalName').textContent = d.name;
      document.getElementById('modalNip').textContent = 'NIP. ' + d.nip;

      let typeBadge = '';
      if (d.type === 'CUTI') {
        typeBadge = '<span class="badge leave-type-cuti rounded-pill px-2 py-1"><i class="bi bi-calendar-event me-1"></i>Cuti</span>';
      } else if (d.type === 'SAKIT') {
        typeBadge = '<span class="badge leave-type-sakit rounded-pill px-2 py-1"><i class="bi bi-heart-pulse me-1"></i>Sakit</span>';
      } else {
        typeBadge = '<span class="badge leave-type-izin rounded-pill px-2 py-1"><i class="bi bi-door-open me-1"></i>Izin</span>';
      }
      document.getElementById('modalType').innerHTML = typeBadge;

      document.getElementById('modalDate').textContent = d.date;
      document.getElementById('modalDuration').textContent = d.duration;
      document.getElementById('modalReason').textContent = d.reason;
      document.getElementById('modalTimelineSubmit').textContent = 'Diajukan pada ' + d.submitted;

      // Status badge
      let statusHtml = '';
      if (d.status === 'PENDING') {
        statusHtml = '<span class="badge text-bg-warning text-dark rounded-pill px-3 py-2"><i class="bi bi-hourglass-split me-1"></i>Pending</span>';
      } else if (d.status === 'APPROVED') {
        statusHtml = '<span class="badge text-bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle me-1"></i>Disetujui</span>';
      } else {
        statusHtml = '<span class="badge text-bg-danger rounded-pill px-3 py-2"><i class="bi bi-x-circle me-1"></i>Ditolak</span>';
      }
      document.getElementById('modalStatusBadge').innerHTML = statusHtml;

      // Attachment
      const attachSection = document.getElementById('modalAttachmentSection');
      if (d.attachment_url) {
        attachSection.style.display = 'block';
        document.getElementById('modalAttachmentName').textContent = d.attachment_name;
        document.getElementById('modalAttachmentLink').href = d.attachment_url;
      } else {
        attachSection.style.display = 'none';
      }

      // Timeline action
      const timelineAction = document.getElementById('modalTimelineAction');
      if (d.status !== 'PENDING' && d.reviewed_at) {
        timelineAction.style.display = 'block';
        const isApproved = d.status === 'APPROVED';
        const dot = document.getElementById('modalTimelineDot');
        dot.className = 'timeline-dot ' + (isApproved ? 'bg-success' : 'bg-danger');
        document.getElementById('modalTimelineActionText').textContent = (isApproved ? 'Disetujui' : 'Ditolak') + ' pada ' + d.reviewed_at;
        document.getElementById('modalTimelineActionBy').textContent = 'Oleh: ' + (d.reviewer_name || 'Admin HRIS');
      } else {
        timelineAction.style.display = 'none';
      }

      // Reject note
      const rejectSec = document.getElementById('modalRejectSection');
      if (d.status === 'REJECTED' && d.reject_reason) {
        rejectSec.style.display = 'block';
        document.getElementById('modalRejectNote').textContent = d.reject_reason;
      } else {
        rejectSec.style.display = 'none';
      }

      // Actions in modal (only for Pending)
      const modalActions = document.getElementById('modalActions');
      if (d.status === 'PENDING') {
        modalActions.style.display = 'flex';
        document.getElementById('modalApproveBtn').onclick = function() {
          const detailModal = bootstrap.Modal.getInstance(document.getElementById('leaveDetailModal'));
          detailModal.hide();
          openApproveModal(d.id, d.name);
        };
        document.getElementById('modalRejectBtn').onclick = function() {
          const detailModal = bootstrap.Modal.getInstance(document.getElementById('leaveDetailModal'));
          detailModal.hide();
          openRejectModal(d.id, d.name);
        };
      } else {
        modalActions.style.display = 'none';
      }

      const modal = new bootstrap.Modal(document.getElementById('leaveDetailModal'));
      modal.show();
    }
  </script>
</body>
</html>
