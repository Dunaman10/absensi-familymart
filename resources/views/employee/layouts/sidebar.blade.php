<aside class="admin-sidebar" id="adminSidebar" aria-label="Employee navigation">
  <div class="sidebar-header">
    <a class="brand-mark" href="{{ route('employee.dashboard') }}" aria-label="Absensi FamilyMart Employee Portal">
      <img src="{{ asset('assets/images/logo/icon.png') }}" alt="Logo" style="height: 34px; width: 34px; object-fit: contain; flex-shrink: 0; margin-right: 10px; border-radius: 6px;">
      <span class="brand-copy">
        <span class="brand-title">Absensi FamilyMart</span>
        <span class="brand-subtitle">Portal Presensi Karyawan</span>
      </span>
    </a>
  </div>

  <nav class="sidebar-nav">
    <a class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}" href="{{ route('employee.dashboard') }}">
      <span class="nav-icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></span>
      <span class="nav-text">Dashboard</span>
    </a>
    <a class="nav-link {{ request()->routeIs('employee.attendance.scan') ? 'active' : '' }}" href="{{ route('employee.attendance.scan') }}">
      <span class="nav-icon"><i class="bi bi-camera-video" aria-hidden="true"></i></span>
      <span class="nav-text">Kamera Presensi</span>
    </a>
    <a class="nav-link {{ request()->routeIs('employee.history') ? 'active' : '' }}" href="{{ route('employee.history') }}">
      <span class="nav-icon"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
      <span class="nav-text">Riwayat Presensi</span>
    </a>
    <a class="nav-link {{ request()->routeIs('employee.leaves.*') ? 'active' : '' }}" href="{{ route('employee.leaves.index') }}">
      <span class="nav-icon"><i class="bi bi-file-earmark-text" aria-hidden="true"></i></span>
      <span class="nav-text">Perizinan</span>
    </a>
    <a class="nav-link {{ request()->routeIs('employee.profile.index') ? 'active' : '' }}" href="{{ route('employee.profile.index') }}">
      <span class="nav-icon"><i class="bi bi-person-badge" aria-hidden="true"></i></span>
      <span class="nav-text">Profil Saya</span>
    </a>
  </nav>

  <div class="sidebar-footer">
    <span class="status-dot bg-success"></span>
    <span class="sidebar-footer-text">Live GPS & Face Biometric Active</span>
  </div>
</aside>
