<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Absensiku Pro - Kelola Shift & Jadwal Kerja Karyawan">
  <title>Jadwal & Shift Kerja | Absensiku Pro</title>
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

      <!-- Dashboard Content - Shift Kerja -->
      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">
          <!-- Page Heading -->
          <div class="page-heading">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1">OPERATIONAL</p>
                <h1 class="h3 mb-1">Jadwal & Shift Kerja</h1>
                <p class="text-muted mb-0">Kelola 3 shift kerja tetap Family Mart & penugasan jam kerja karyawan.</p>
              </div>
            </div>
          </div>

          @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          @if($errors->any())
          <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan:
            <ul class="mb-0 mt-1 small">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          <!-- Master Shift Cards -->
          <section class="row g-3 mt-1" aria-label="Master shift cards">
            @forelse($shifts as $shift)
            <div class="col-12 col-md-6 col-xl-4">
              <article class="metric-card h-100 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge text-bg-primary fs-6 fw-semibold"><i class="bi bi-clock me-1"></i>{{ $shift->name }}</span>
                    <span class="text-muted small"><i class="bi bi-people me-1"></i>{{ $shift->users_count }} Karyawan</span>
                  </div>
                  <h3 class="h4 mb-2 text-white font-monospace">
                    {{ substr($shift->start_time, 0, 5) }} - {{ substr($shift->end_time, 0, 5) }} <span class="fs-6 text-muted font-sans-serif">WIB</span>
                  </h3>
                  <p class="text-muted small mb-2">{{ $shift->description ?? 'Shift operasional kerja.' }}</p>
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge text-bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Toleransi {{ $shift->late_tolerance_minutes }} menit</span>
                  </div>
                </div>
                <div class="mt-3 pt-3 border-top d-flex justify-content-end">
                  <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="openEditShiftModal({{ $shift->id }}, '{{ addslashes($shift->name) }}', '{{ substr($shift->start_time, 0, 5) }}', '{{ substr($shift->end_time, 0, 5) }}', {{ $shift->late_tolerance_minutes }}, '{{ addslashes($shift->description ?? '') }}')">
                    <i class="bi bi-pencil me-1"></i> Edit Shift
                  </button>
                </div>
              </article>
            </div>
            @empty
            <div class="col-12">
              <div class="alert alert-info">Belum ada data shift kerja yang dibuat. Silakan tambahkan shift baru.</div>
            </div>
            @endforelse
          </section>

          <!-- Panels Table Penugasan Shift Karyawan -->
          <section class="panel mt-4">
            <div class="panel-header flex-wrap gap-2">
              <div>
                <h2 class="h5 mb-1 section-title"><i class="bi bi-person-workspace" aria-hidden="true"></i><span>Penugasan Shift Karyawan</span></h2>
                <p class="text-muted mb-0">Atur dan alokasikan jadwal shift kerja untuk masing-masing staf gerai Family Mart.</p>
              </div>
              <form action="{{ route('admin.shifts.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                <div class="input-group input-group-sm">
                  <input class="form-control table-search" type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIP, atau jabatan" aria-label="Search employees for shift">
                  <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                  @if(request('search'))
                    <a href="{{ route('admin.shifts.index') }}" class="btn btn-outline-secondary text-muted" title="Reset"><i class="bi bi-x-lg"></i></a>
                  @endif
                </div>
              </form>
            </div>
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th scope="col">Nama Karyawan</th>
                    <th scope="col">NIP</th>
                    <th scope="col">Jabatan</th>
                    <th scope="col">Shift Kerja Terpasang</th>
                    <th scope="col">Jam Operasional</th>
                    <th scope="col" class="text-end">Aksi Penugasan</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($employees as $emp)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <img class="avatar-img avatar-sm rounded-circle shadow-sm" src="{{ $emp->registered_face_photo ? asset($emp->registered_face_photo) : 'https://ui-avatars.com/api/?name='.urlencode($emp->name).'&background=0D8ABC&color=fff' }}" alt="{{ $emp->name }}">
                        <div>
                          <p class="fw-semibold mb-0">{{ $emp->name }}</p>
                          <small class="text-muted">{{ $emp->email }}</small>
                        </div>
                      </div>
                    </td>
                    <td><span class="fw-mono text-muted">{{ $emp->nip }}</span></td>
                    <td>{{ $emp->position ?? '-' }}</td>
                    <td>
                      @if($emp->shift)
                        <span class="badge text-bg-success"><i class="bi bi-clock me-1"></i>{{ $emp->shift->name }}</span>
                      @else
                        <span class="badge text-bg-secondary">Belum Di-assign</span>
                      @endif
                    </td>
                    <td>
                      @if($emp->shift)
                        <span class="fw-mono text-white">{{ substr($emp->shift->start_time, 0, 5) }} - {{ substr($emp->shift->end_time, 0, 5) }} WIB</span>
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                    <td class="text-end">
                      <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="openAssignShiftModal({{ $emp->id }}, '{{ addslashes($emp->name) }}', {{ $emp->shift_id ?? 'null' }})">
                        <i class="bi bi-arrow-repeat me-1"></i> Ubah Shift
                      </button>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada data karyawan terdaftar.</td>
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

  <!-- Modal Edit Shift -->
  <div class="modal fade" id="editShiftModal" tabindex="-1" aria-labelledby="editShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form id="editShiftForm" action="" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title fw-bold" id="editShiftModalLabel"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Master Shift</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="editShiftName" class="form-label">Nama Shift</label>
              <input type="text" class="form-control" id="editShiftName" name="name" required>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label for="editShiftStart" class="form-label">Jam Masuk (WIB)</label>
                <input type="time" class="form-control" id="editShiftStart" name="start_time" required>
              </div>
              <div class="col-6">
                <label for="editShiftEnd" class="form-label">Jam Pulang (WIB)</label>
                <input type="time" class="form-control" id="editShiftEnd" name="end_time" required>
              </div>
            </div>
            <div class="mb-3">
              <label for="editShiftTolerance" class="form-label">Toleransi Keterlambatan (Menit)</label>
              <input type="number" class="form-control" id="editShiftTolerance" name="late_tolerance_minutes" min="0" max="120" required>
            </div>
            <div class="mb-3">
              <label for="editShiftDesc" class="form-label">Deskripsi / Catatan (Opsional)</label>
              <textarea class="form-control" id="editShiftDesc" name="description" rows="2"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check2-circle me-1"></i> Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Assign Shift Karyawan -->
  <div class="modal fade" id="assignShiftModal" tabindex="-1" aria-labelledby="assignShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="{{ route('admin.shifts.assign') }}" method="POST">
          @csrf
          <input type="hidden" name="user_id" id="assignUserId">
          <div class="modal-header">
            <h5 class="modal-title fw-bold" id="assignShiftModalLabel"><i class="bi bi-person-workspace me-2 text-primary"></i>Ubah Shift Karyawan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="mb-3">Pilih jadwal shift kerja untuk karyawan: <strong id="assignUserName" class="text-primary"></strong></p>
            <div class="mb-3">
              <label for="assignShiftId" class="form-label fw-semibold">Pilihan Shift Kerja</label>
              <select class="form-select" id="assignShiftId" name="shift_id">
                <option value="">-- Tanpa Shift (Default Office Hours) --</option>
                @foreach($shifts as $s)
                  <option value="{{ $s->id }}">{{ $s->name }} ({{ substr($s->start_time,0,5) }} - {{ substr($s->end_time,0,5) }} WIB)</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i> Simpan Penugasan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    function openEditShiftModal(id, name, start, end, tolerance, desc) {
      document.getElementById('editShiftForm').action = `/admin/shifts/${id}`;
      document.getElementById('editShiftName').value = name;
      document.getElementById('editShiftStart').value = start;
      document.getElementById('editShiftEnd').value = end;
      document.getElementById('editShiftTolerance').value = tolerance;
      document.getElementById('editShiftDesc').value = desc;

      var modal = new bootstrap.Modal(document.getElementById('editShiftModal'));
      modal.show();
    }

    function openAssignShiftModal(userId, userName, currentShiftId) {
      document.getElementById('assignUserId').value = userId;
      document.getElementById('assignUserName').innerText = userName;
      document.getElementById('assignShiftId').value = currentShiftId || '';

      var modal = new bootstrap.Modal(document.getElementById('assignShiftModal'));
      modal.show();
    }
  </script>
</body>
</html>
