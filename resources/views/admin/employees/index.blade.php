<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Absensi FamilyMart - Kelola Data Karyawan (CRUD)">
  <title>Data Karyawan | Absensi FamilyMart</title>
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
</head>

<body>
  <div class="admin-shell">
    <div class="sidebar-backdrop" data-sidebar-close></div>

    <!-- Sidebar Admin -->
    @include('admin.layouts.sidebar')

    <div class="admin-main">
      <!-- Navbar Top -->
      @include('admin.layouts.navbar')

      <!-- Dashboard Content - Data Karyawan -->
      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">
          <!-- Page Heading -->
          <div class="page-heading">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1">MANAGEMENT</p>
                <h1 class="h3 mb-1">Data Karyawan</h1>
                <p class="text-muted mb-0">Kelola master data karyawan, NIP, jabatan, status akun, dan registrasi biometrik.</p>
              </div>
            </div>
            <div class="heading-actions">
              <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.employees.export', ['search' => request('search')]) }}"><i class="bi bi-file-earmark-excel me-1" aria-hidden="true"></i> Export Excel</a>
              <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#employeeModal"><i class="bi bi-person-plus me-1" aria-hidden="true"></i> Tambah Karyawan</button>
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
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan pada input form:
            <ul class="mb-0 mt-1 small">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          <!-- Metrik Cards Data Karyawan -->
          <section class="row g-3 mt-1" aria-label="Employee metrics summary">
            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-primary">
                <div class="metric-top">
                  <span class="metric-label">TOTAL KARYAWAN</span>
                  <span class="metric-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $totalEmployees }}</div>
                <div class="metric-meta">
                  <span class="text-success">Terdaftar</span>
                  <span>di database</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-success">
                <div class="metric-top">
                  <span class="metric-label">AKUN AKTIF</span>
                  <span class="metric-icon"><i class="bi bi-person-check" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $activeEmployees }}</div>
                <div class="metric-meta">
                  <span class="text-success">Siap Absen</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-warning">
                <div class="metric-top">
                  <span class="metric-label">PENDING BIOMETRIK</span>
                  <span class="metric-icon"><i class="bi bi-person-exclamation" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $pendingBiometric }}</div>
                <div class="metric-meta">
                  <span class="text-warning">Belum mendaftarkan foto</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-danger">
                <div class="metric-top">
                  <span class="metric-label">NON-AKTIF</span>
                  <span class="metric-icon"><i class="bi bi-person-x" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $inactiveEmployees }}</div>
                <div class="metric-meta">
                  <span class="text-danger">Akun ditangguhkan</span>
                </div>
              </article>
            </div>
          </section>

          <!-- Panels Table Karyawan -->
          <section class="panel mt-3">
            <div class="panel-header flex-wrap gap-2">
              <div>
                <h2 class="h5 mb-1 section-title"><i class="bi bi-person-lines-fill" aria-hidden="true"></i><span>Daftar Karyawan</span></h2>
                <p class="text-muted mb-0">Master data akun karyawan terdaftar di sistem HRIS.</p>
              </div>
              <form action="{{ route('admin.employees.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                @if(request('per_page')) <input type="hidden" name="per_page" value="{{ request('per_page') }}"> @endif
                <div class="input-group input-group-sm">
                  <input class="form-control table-search" type="search" name="search" value="{{ request('search') }}" placeholder="Cari karyawan" aria-label="Search employees" id="employeeSearchInput">
                  <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                  @if(request('search'))
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary text-muted" title="Reset"><i class="bi bi-x-lg"></i></a>
                  @endif
                </div>
              </form>
            </div>
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th scope="col">Nama Karyawan</th>
                    <th scope="col">NIP (Username)</th>
                    <th scope="col">Jabatan / Tim</th>
                    <th scope="col">Shift Kerja</th>
                    <th scope="col">Status Akun</th>
                    <th scope="col" class="text-end">Aksi</th>
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
                          <small class="text-muted">NIP. {{ $emp->nip }}</small>
                        </div>
                      </div>
                    </td>
                    <td><span class="fw-mono text-muted">{{ $emp->nip }}</span></td>
                    <td>{{ $emp->position ?? '-' }}</td>
                    <td>
                      @if($emp->shift)
                        <span class="badge text-bg-info"><i class="bi bi-clock me-1"></i>{{ $emp->shift->name }}</span>
                      @else
                        <span class="badge text-bg-secondary">Default</span>
                      @endif
                    </td>
                    <td>
                      @if($emp->account_status == 'Aktif')
                        <span class="badge text-bg-success">Aktif</span>
                      @elseif($emp->account_status == 'Pending Biometrik')
                        <span class="badge text-bg-warning text-dark">Pending Biometrik</span>
                      @else
                        <span class="badge text-bg-danger">Non-Aktif</span>
                      @endif
                    </td>
                    <td class="text-end">
                      <a href="{{ route('admin.employees.show', $emp->id) }}" class="btn btn-light btn-sm text-info me-1" title="Detail Presensi & Penggajian">
                        <i class="bi bi-eye"></i>
                      </a>
                      <button type="button" class="btn btn-light btn-sm text-primary me-1" title="Edit Karyawan" onclick="openEditEmpModal({{ $emp->id }}, '{{ addslashes($emp->name) }}', '{{ $emp->nip }}', '{{ addslashes($emp->position ?? '') }}', '{{ addslashes($emp->email ?? '') }}', '{{ $emp->account_status }}', {{ $emp->shift_id ?? 'null' }})">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <form id="deleteFormEmp_{{ $emp->id }}" action="{{ route('admin.employees.destroy', $emp->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-light btn-sm text-danger" title="Hapus Karyawan" onclick="confirmDeleteEmp('deleteFormEmp_{{ $emp->id }}', '{{ addslashes($emp->name) }}')"><i class="bi bi-trash"></i></button>
                      </form>
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
            @if($employees->total() > 0)
            <div class="panel-footer border-top px-3 py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
              <form action="{{ route('admin.employees.index') }}" method="GET" class="mb-0">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
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
                {{ $employees->links() }}
              </div>
            </div>
            @endif
          </section>
        </div>
      </main>
    </div>
  </div>

  <!-- Modal Form Tambah Karyawan Baru -->
  <div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="{{ route('admin.employees.store') }}" method="POST" id="employeeForm">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title fw-bold" id="employeeModalLabel"><i class="bi bi-person-plus me-2 text-primary"></i>Tambah Karyawan Baru</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="employeeNip" class="form-label">NIP (Auto-Generated)</label>
              <input type="text" class="form-control fw-mono bg-body-tertiary" id="employeeNip" name="nip" value="{{ $nextNip }}" readonly title="NIP dibuat otomatis oleh sistem">
              <small class="text-muted">NIP dibuat otomatis berurutan oleh sistem (Format: EMP{{ date('Y') }}xxx).</small>
            </div>
            <div class="mb-3">
              <label for="employeeName" class="form-label">Nama Lengkap Karyawan</label>
              <input type="text" class="form-control" id="employeeName" name="name" placeholder="Contoh: Ahmad Fauzi" required>
            </div>
            <div class="mb-3">
              <label for="employeeEmail" class="form-label">Alamat Email (OTP Reset Sandi)</label>
              <input type="email" class="form-control" id="employeeEmail" name="email" placeholder="Contoh: fauzi@familymart.com" required>
            </div>
            <div class="mb-3">
              <label for="employeePosition" class="form-label">Jabatan / Tim</label>
              <input type="text" class="form-control" id="employeePosition" name="position" placeholder="Contoh: Operations Specialist">
            </div>
            <div class="mb-3">
              <label for="employeeShift" class="form-label">Penugasan Shift Kerja</label>
              <select class="form-select" id="employeeShift" name="shift_id">
                <option value="">-- Tanpa Shift (Default Office) --</option>
                @foreach($shifts as $s)
                  <option value="{{ $s->id }}">{{ $s->name }} ({{ substr($s->start_time,0,5) }} - {{ substr($s->end_time,0,5) }} WIB)</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Status Akun Awal</label>
              <input type="text" class="form-control text-warning fw-semibold bg-body-tertiary" value="Pending Biometrik" readonly>
              <small class="text-muted" style="font-size: 0.75rem;">Status otomatis Pending Biometrik sampai foto wajah didaftarkan saat presensi pertama.</small>
            </div>
            <div class="mb-3">
              <label for="employeePassword" class="form-label">Password Login</label>
              <div class="input-group">
                <input type="password" class="form-control" id="employeePassword" name="password" placeholder="••••••••" required minlength="6">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('employeePassword', 'eyeIconEmp')" title="Tampilkan/Sembunyikan Kata Sandi">
                  <i class="bi bi-eye" id="eyeIconEmp" aria-hidden="true"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i> Simpan Karyawan Baru</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit Data Karyawan -->
  <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form id="editEmployeeForm" action="" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title fw-bold" id="editEmployeeModalLabel"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Data Karyawan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="editEmpNip" class="form-label fw-semibold">NIP (Username)</label>
              <input type="text" class="form-control fw-mono bg-body-tertiary" id="editEmpNip" readonly disabled title="NIP tidak dapat diubah">
            </div>
            <div class="mb-3">
              <label for="editEmpName" class="form-label fw-semibold">Nama Lengkap Karyawan</label>
              <input type="text" class="form-control" id="editEmpName" name="name" required placeholder="Contoh: Ahmad Fauzi">
            </div>
            <div class="mb-3">
              <label for="editEmpEmail" class="form-label fw-semibold">Alamat Email</label>
              <input type="email" class="form-control" id="editEmpEmail" name="email" required placeholder="Contoh: fauzi@familymart.com">
            </div>
            <div class="mb-3">
              <label for="editEmpPosition" class="form-label fw-semibold">Jabatan / Tim</label>
              <input type="text" class="form-control" id="editEmpPosition" name="position" placeholder="Contoh: Operations Specialist">
            </div>
            <div class="mb-3">
              <label for="editEmpShift" class="form-label fw-semibold">Penugasan Shift Kerja</label>
              <select class="form-select" id="editEmpShift" name="shift_id">
                <option value="">-- Tanpa Shift (Default Office) --</option>
                @foreach($shifts as $s)
                  <option value="{{ $s->id }}">{{ $s->name }} ({{ substr($s->start_time,0,5) }} - {{ substr($s->end_time,0,5) }} WIB)</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label for="editEmpStatus" class="form-label fw-semibold">Status Akun</label>
              <select class="form-select" id="editEmpStatus" name="account_status" required>
                <option value="Aktif">Aktif</option>
                <option value="Pending Biometrik">Pending Biometrik</option>
                <option value="Non-Aktif">Non-Aktif</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="editEmpPassword" class="form-label fw-semibold">Reset Password Baru (Opsional)</label>
              <div class="input-group">
                <input type="password" class="form-control" id="editEmpPassword" name="password" placeholder="••••••••" minlength="6">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('editEmpPassword', 'eyeIconEditEmp')" title="Tampilkan/Sembunyikan Kata Sandi">
                  <i class="bi bi-eye" id="eyeIconEditEmp" aria-hidden="true"></i>
                </button>
              </div>
              <small class="text-muted" style="font-size: 0.75rem;">Biarkan kosong jika tidak ingin mengganti password karyawan.</small>
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

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    function openEditEmpModal(id, name, nip, position, email, status, shiftId) {
      document.getElementById('editEmployeeForm').action = `/admin/employees/${id}`;
      document.getElementById('editEmpNip').value = nip;
      document.getElementById('editEmpName').value = name;
      document.getElementById('editEmpEmail').value = email;
      document.getElementById('editEmpPosition').value = position;
      if (document.getElementById('editEmpShift')) {
        document.getElementById('editEmpShift').value = shiftId || '';
      }
      if (document.getElementById('editEmpStatus')) {
        document.getElementById('editEmpStatus').value = status || 'Aktif';
      }
      if (document.getElementById('editEmpPassword')) {
        document.getElementById('editEmpPassword').value = '';
      }

      var editModal = new bootstrap.Modal(document.getElementById('editEmployeeModal'));
      editModal.show();
    }

    function confirmDeleteEmp(formId, empName) {
      Swal.fire({
        title: 'Konfirmasi Hapus Data',
        html: `Apakah Anda yakin ingin menghapus akun karyawan <strong class="text-white">${empName}</strong>? Data yang dihapus tidak dapat dikembalikan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#475569',
        confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus',
        cancelButtonText: 'Batal',
        background: '#1e293b',
        color: '#f8fafc',
        customClass: {
          popup: 'border border-secondary border-opacity-25 rounded-4 shadow-lg',
          confirmButton: 'btn btn-danger btn-sm rounded-pill px-4 me-2 shadow-sm',
          cancelButton: 'btn btn-outline-secondary btn-sm rounded-pill px-4 shadow-sm'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById(formId).submit();
        }
      });
    }
  </script>
</body>
</html>
