<aside class="admin-sidebar" id="adminSidebar" aria-label="Main navigation">
  <div class="sidebar-header">
    <a class="brand-mark" href="{{ route('admin.dashboard') }}" aria-label="Absensi FamilyMart dashboard">
      <img src="{{ asset('assets/images/logo/icon.png') }}" alt="Logo" style="height: 34px; width: 34px; object-fit: contain; flex-shrink: 0; margin-right: 10px; border-radius: 6px;">
      <span class="brand-copy">
        <span class="brand-title">Absensi FamilyMart</span>
        <span class="brand-subtitle">Panel Admin HRIS</span>
      </span>
    </a>
  </div>

  <nav class="sidebar-nav">
    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
      <span class="nav-icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></span>
      <span class="nav-text">Dashboard</span>
    </a>
    <a class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}" href="{{ route('admin.employees.index') }}">
      <span class="nav-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
      <span class="nav-text">Data Karyawan</span>
    </a>
    <a class="nav-link {{ request()->routeIs('admin.shifts.*') ? 'active' : '' }}" href="{{ route('admin.shifts.index') }}">
      <span class="nav-icon"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
      <span class="nav-text">Jadwal & Shift Kerja</span>
    </a>
    <a class="nav-link {{ request()->routeIs('admin.attendance_logs.*') ? 'active' : '' }}" href="{{ route('admin.attendance_logs.index') }}">
      <span class="nav-icon"><i class="bi bi-calendar-check" aria-hidden="true"></i></span>
      <span class="nav-text">Log Presensi</span>
    </a>
    <a class="nav-link {{ request()->routeIs('admin.geofences.*') ? 'active' : '' }}" href="{{ route('admin.geofences.index') }}">
      <span class="nav-icon"><i class="bi bi-geo-alt" aria-hidden="true"></i></span>
      <span class="nav-text">Geofencing & Lokasi</span>
    </a>
  </nav>

  <div class="sidebar-footer">
    <span class="status-dot"></span>
    <span class="sidebar-footer-text">Geofencing & Face Engine Ready</span>
  </div>
</aside>
