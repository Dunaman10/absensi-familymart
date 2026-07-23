<aside class="admin-sidebar" id="adminSidebar" aria-label="Main navigation">
  <div class="sidebar-header">
    <a class="brand-mark" href="{{ route('admin.dashboard') }}" aria-label="Absensiku Pro dashboard">
      <span class="brand-icon"><i class="bi bi-grid-1x2-fill" aria-hidden="true"></i></span>
      <span class="brand-copy">
        <span class="brand-title">Absensiku Pro</span>
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
