<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Absensiku Pro - Kelola Data Karyawan (CRUD)">
  <title>Data Karyawan | Absensiku Pro</title>

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
              <button class="btn btn-outline-secondary btn-sm" type="button"><i class="bi bi-download" aria-hidden="true"></i> Export</button>
              <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#employeeModal"><i class="bi bi-person-plus" aria-hidden="true"></i> Tambah Karyawan</button>
            </div>
          </div>

          <!-- Metrik Cards Data Karyawan -->
          <section class="row g-3 mt-1" aria-label="Employee metrics summary">
            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-primary">
                <div class="metric-top">
                  <span class="metric-label">TOTAL KARYAWAN</span>
                  <span class="metric-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">128</div>
                <div class="metric-meta">
                  <span class="text-success">+5.1%</span>
                  <span>bulan ini</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-success">
                <div class="metric-top">
                  <span class="metric-label">AKUN AKTIF</span>
                  <span class="metric-icon"><i class="bi bi-check2-circle" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">120</div>
                <div class="metric-meta">
                  <span class="text-success">94%</span>
                  <span>akun terverifikasi</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-warning">
                <div class="metric-top">
                  <span class="metric-label">PENDING BIOMETRIK</span>
                  <span class="metric-icon"><i class="bi bi-hourglass-split" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">5</div>
                <div class="metric-meta">
                  <span class="text-warning">5 orang</span>
                  <span>belum foto wajah</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-danger">
                <div class="metric-top">
                  <span class="metric-label">NON-AKTIF</span>
                  <span class="metric-icon"><i class="bi bi-slash-circle" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">3</div>
                <div class="metric-meta">
                  <span class="text-danger">3 akun</span>
                  <span>ditangguhkan</span>
                </div>
              </article>
            </div>
          </section>

          <!-- Table Karyawan (CRUD Interface) -->
          <section class="panel mt-3">
            <div class="panel-header">
              <div>
                <h2 class="h5 mb-1 section-title"><i class="bi bi-table" aria-hidden="true"></i><span>Daftar Karyawan</span></h2>
                <p class="text-muted mb-0">Cari, kelola, serta perbarui data akun dan akses karyawan.</p>
              </div>
              <div class="d-flex flex-wrap gap-2">
                <input class="form-control form-control-sm table-search" type="search" placeholder="Cari karyawan..." aria-label="Search employees">
              </div>
            </div>
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead>
                  <tr>
                    <th scope="col">Karyawan</th>
                    <th scope="col">NIP</th>
                    <th scope="col">Jabatan / Tim</th>
                    <th scope="col">Role</th>
                    <th scope="col">Status Akun</th>
                    <th scope="col" class="text-end">Aksi (CRUD)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <img class="avatar-img avatar-sm" src="{{ asset('assets/images/avatar/avatar-1.jpg') }}" alt="Budi Santoso">
                        <div>
                          <p class="fw-semibold mb-0">Budi Santoso</p>
                          <p class="text-muted small mb-0">budi.santoso@absensiku.com</p>
                        </div>
                      </div>
                    </td>
                    <td><span class="fw-mono text-muted">NIP-19920101</span></td>
                    <td>Operations Specialist</td>
                    <td><span class="badge text-bg-secondary">User</span></td>
                    <td><span class="badge text-bg-success">Aktif</span></td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-light btn-sm" title="Detail"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-light btn-sm text-primary" title="Edit"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-light btn-sm text-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <img class="avatar-img avatar-sm" src="{{ asset('assets/images/avatar/avatar-2.jpg') }}" alt="Siti Rahma">
                        <div>
                          <p class="fw-semibold mb-0">Siti Rahma</p>
                          <p class="text-muted small mb-0">siti.rahma@absensiku.com</p>
                        </div>
                      </div>
                    </td>
                    <td><span class="fw-mono text-muted">NIP-19930412</span></td>
                    <td>HR & Finance Officer</td>
                    <td><span class="badge text-bg-info">Admin HR</span></td>
                    <td><span class="badge text-bg-success">Aktif</span></td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-light btn-sm" title="Detail"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-light btn-sm text-primary" title="Edit"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-light btn-sm text-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <img class="avatar-img avatar-sm" src="{{ asset('assets/images/avatar/avatar-3.jpg') }}" alt="Ahmad Hidayat">
                        <div>
                          <p class="fw-semibold mb-0">Ahmad Hidayat</p>
                          <p class="text-muted small mb-0">ahmad.hidayat@absensiku.com</p>
                        </div>
                      </div>
                    </td>
                    <td><span class="fw-mono text-muted">NIP-19900823</span></td>
                    <td>Lead IT Engineer</td>
                    <td><span class="badge text-bg-secondary">User</span></td>
                    <td><span class="badge text-bg-warning">Pending Biometrik</span></td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-light btn-sm" title="Detail"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-light btn-sm text-primary" title="Edit"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-light btn-sm text-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <img class="avatar-img avatar-sm" src="{{ asset('assets/images/avatar/avatar.jpg') }}" alt="Nadia Islam">
                        <div>
                          <p class="fw-semibold mb-0">Nadia Islam</p>
                          <p class="text-muted small mb-0">nadia@absensiku.com</p>
                        </div>
                      </div>
                    </td>
                    <td><span class="fw-mono text-muted">NIP-19950711</span></td>
                    <td>Marketing Lead</td>
                    <td><span class="badge text-bg-secondary">User</span></td>
                    <td><span class="badge text-bg-danger">Non-Aktif</span></td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-light btn-sm" title="Detail"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-light btn-sm text-primary" title="Edit"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-light btn-sm text-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>
        </div>
      </main>
    </div>
  </div>

  <!-- Modal Form Tambah / Edit Karyawan (CRUD Front-end Preview) -->
  <div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="employeeModalLabel"><i class="bi bi-person-plus me-2 text-primary"></i>Form Data Karyawan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="employeeForm">
            <div class="mb-3">
              <label for="employeeName" class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" id="employeeName" placeholder="Contoh: Budi Santoso" required>
            </div>
            <div class="mb-3">
              <label for="employeeNip" class="form-label">NIP (Nomor Induk Pegawai)</label>
              <input type="text" class="form-control" id="employeeNip" placeholder="Contoh: 19920101" required>
            </div>
            <div class="mb-3">
              <label for="employeeEmail" class="form-label">Alamat Email</label>
              <input type="email" class="form-control" id="employeeEmail" placeholder="nama@perusahaan.com" required>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-md-6">
                <label for="employeeRole" class="form-label">Role Akses</label>
                <select class="form-select" id="employeeRole">
                  <option value="user" selected>Karyawan (User)</option>
                  <option value="admin">Administrator (HR)</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="employeeStatus" class="form-label">Status Akun</label>
                <select class="form-select" id="employeeStatus">
                  <option value="active" selected>Aktif</option>
                  <option value="pending">Pending Biometrik</option>
                  <option value="suspended">Non-Aktif</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="employeePassword" class="form-label">Password Akun</label>
              <input type="password" class="form-control" id="employeePassword" placeholder="••••••••">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i> Simpan Data</button>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
