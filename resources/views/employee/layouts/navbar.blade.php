<nav class="navbar admin-navbar navbar-expand bg-white">
  <div class="container-fluid px-3 px-lg-4">
    <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-controls="adminSidebar" aria-expanded="true" aria-label="Toggle sidebar">
      <span></span>
      <span></span>
      <span></span>
    </button>

    <div class="d-none d-md-flex align-items-center gap-2 ms-3">
      <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 small d-flex align-items-center gap-1">
        <i class="bi bi-geo-alt-fill text-success"></i> <span>{{ \App\Models\GeofenceSetting::where('is_active', true)->value('office_name') ?? 'Kantor Utama' }} (Radius Aktif)</span>
      </span>
    </div>

    <div class="navbar-actions ms-auto">
      <button class="icon-button theme-toggle" type="button" data-theme-toggle aria-label="Switch color theme" title="Switch color theme">
        <i class="bi bi-moon-stars" data-theme-icon aria-hidden="true"></i>
      </button>

      <div class="dropdown">
        <button class="icon-button position-relative dropdown-notif-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
          @if(count($employeeNotifications ?? []) > 0)
            <span class="notification-dot"></span>
          @endif
          <i class="bi bi-bell" aria-hidden="true"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end notification-menu p-2" style="min-width: 280px; max-width: 320px;">
          <div class="dropdown-header fw-bold text-body border-bottom pb-2 mb-2 d-flex justify-content-between align-items-center">
            <span>Notifikasi Presensi</span>
            <span class="badge text-bg-primary rounded-pill">{{ count($employeeNotifications ?? []) }}</span>
          </div>
          @forelse($employeeNotifications ?? [] as $notif)
          <div class="dropdown-item d-flex align-items-start gap-2 p-2 rounded mb-1">
            <i class="bi {{ $notif['icon'] }} fs-5 mt-1"></i>
            <div>
              <span class="notification-title fw-semibold text-wrap d-block" style="font-size: 0.85rem; line-height: 1.2;">{{ $notif['title'] }}</span>
              <small class="text-muted text-wrap d-block" style="font-size: 0.75rem;">{{ $notif['subtitle'] }}</small>
              <small class="text-primary fw-medium" style="font-size: 0.7rem;">{{ $notif['time'] }}</small>
            </div>
          </div>
          @empty
          <div class="text-center text-muted small py-3">Tidak ada notifikasi</div>
          @endforelse
        </div>
      </div>

      <style>
        .profile-button::after, .profile-button.dropdown-toggle::after { display: none !important; content: none !important; }
      </style>
      <div class="dropdown">
        <button class="profile-button dropdown-toggle d-flex align-items-center gap-2 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: transparent;">
          @if(Auth::user() && Auth::user()->registered_face_photo)
            <img class="avatar-img avatar-sm rounded-circle shadow-sm" src="{{ asset(Auth::user()->registered_face_photo) }}" alt="{{ Auth::user()->name }}" style="width: 32px; height: 32px; object-fit: cover;">
          @else
            <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width:32px; height:32px; font-size: 0.85rem; flex-shrink: 0;">
              {{ strtoupper(substr(Auth::user()->name ?? 'K', 0, 1)) }}
            </div>
          @endif
          <span class="profile-name d-none d-sm-inline fw-semibold">{{ Auth::user()->name ?? 'Karyawan' }}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item {{ request()->routeIs('employee.profile.index') ? 'active' : '' }}" href="{{ route('employee.profile.index') }}"><i class="bi bi-person me-2"></i>Profil Saya</a></li>
          <li><a class="dropdown-item {{ request()->routeIs('employee.history') ? 'active' : '' }}" href="{{ route('employee.history') }}"><i class="bi bi-clock-history me-2"></i>Riwayat Presensi</a></li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <a class="dropdown-item text-danger" href="{{ route('login') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <i class="bi bi-box-arrow-right me-2"></i>Sign out
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
            </form>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>
