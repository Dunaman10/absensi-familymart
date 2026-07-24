<nav class="navbar admin-navbar navbar-expand bg-white">
  <div class="container-fluid px-3 px-lg-4">
    <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-controls="adminSidebar" aria-expanded="true" aria-label="Toggle sidebar">
      <span></span>
      <span></span>
      <span></span>
    </button>

    <form class="d-none d-md-flex ms-3 flex-grow-1" role="search">
      <input class="form-control search-input" type="search" placeholder="Cari data presensi, karyawan..." aria-label="Search">
    </form>

    <div class="d-none d-lg-flex align-items-center me-2">
      <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 small d-flex align-items-center gap-1">
        <i class="bi bi-geo-alt-fill text-success"></i> <span>{{ \App\Models\GeofenceSetting::where('is_active', true)->value('office_name') ?? 'Kantor Utama' }} (Radius Aktif)</span>
      </span>
    </div>

    <div class="navbar-actions ms-auto">
      <button class="icon-button theme-toggle" type="button" data-theme-toggle aria-label="Switch color theme" title="Switch color theme">
        <i class="bi bi-moon-stars" data-theme-icon aria-hidden="true"></i>
      </button>
      <div class="dropdown">
        <button class="icon-button position-relative dropdown-notif-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications" id="notifBellBtn">
          <span class="notification-dot" id="notifDot" style="{{ count($adminNotifications ?? []) > 0 ? '' : 'display:none;' }}"></span>
          <i class="bi bi-bell" aria-hidden="true"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end notification-menu p-2" style="min-width: 280px; max-width: 340px;">
          <div class="dropdown-header fw-bold text-body border-bottom pb-2 mb-2 d-flex justify-content-between align-items-center">
            <span><i class="bi bi-broadcast me-1 text-primary"></i> Notifikasi Live Reverb</span>
            <span class="badge text-bg-primary rounded-pill" id="notifCountBadge">{{ count($adminNotifications ?? []) }}</span>
          </div>
          <div id="notifListContainer">
            @forelse($adminNotifications ?? [] as $notif)
            <a class="dropdown-item d-flex align-items-start gap-2 p-2 rounded mb-1" href="{{ $notif['link'] ?? '#' }}">
              <i class="bi {{ $notif['icon'] }} fs-5 mt-1"></i>
              <div>
                <span class="notification-title fw-semibold text-wrap d-block" style="font-size: 0.85rem; line-height: 1.2;">{{ $notif['title'] }}</span>
                <small class="text-muted text-wrap d-block" style="font-size: 0.75rem;">{{ $notif['subtitle'] }}</small>
                <small class="text-primary fw-medium" style="font-size: 0.7rem;">{{ $notif['time'] }}</small>
              </div>
            </a>
            @empty
            <div class="text-center text-muted small py-3" id="emptyNotifText">Tidak ada notifikasi baru</div>
            @endforelse
          </div>
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
              {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
            </div>
          @endif
          <span class="profile-name d-none d-sm-inline fw-semibold">{{ Auth::user()->name ?? 'Administrator' }}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item {{ request()->routeIs('admin.profile.index') ? 'active' : '' }}" href="{{ route('admin.profile.index') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
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

<!-- Live Reverb Real-Time Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
  <div id="liveAttendanceToast" class="toast align-items-center text-bg-primary border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body d-flex align-items-center gap-2">
        <i class="bi bi-bell-fill fs-4 text-warning"></i>
        <div>
          <strong class="d-block text-white" id="toastTitle">Presensi Real-Time Baru!</strong>
          <small class="text-white-50 d-block" id="toastMessage">Karyawan baru saja melakukan presensi.</small>
        </div>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Real-Time Reverb Event Broadcasting Listener via Echo / Pusher / Polling Fallback
    function notifyNewAttendance(logData) {
      const notifDot = document.getElementById('notifDot');
      if (notifDot) notifDot.style.display = 'block';

      const toastTitle = document.getElementById('toastTitle');
      const toastMessage = document.getElementById('toastMessage');
      const toastEl = document.getElementById('liveAttendanceToast');

      if (toastTitle && toastMessage && toastEl) {
        toastTitle.textContent = "🔔 Presensi " + logData.type + " Real-Time!";
        toastMessage.textContent = (logData.user_name || 'Karyawan') + " baru saja presensi (Status: " + logData.status + ")";
        const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();
      }

      // Dynamically add to dropdown list
      const container = document.getElementById('notifListContainer');
      const emptyText = document.getElementById('emptyNotifText');
      if (emptyText) emptyText.style.display = 'none';

      if (container) {
        const item = document.createElement('a');
        item.className = 'dropdown-item d-flex align-items-start gap-2 p-2 rounded mb-1 border-bottom';
        item.href = '{{ route("admin.attendance_logs.index") }}';
        item.innerHTML = `
          <i class="bi bi-clock-history text-primary fs-5 mt-1"></i>
          <div>
            <span class="notification-title fw-semibold text-wrap d-block" style="font-size: 0.85rem; line-height: 1.2;">Presensi ${logData.type} ${logData.user_name || 'Karyawan'}</span>
            <small class="text-muted text-wrap d-block" style="font-size: 0.75rem;">Status: ${logData.status} (${logData.distance || 0}m)</small>
            <small class="text-primary fw-medium" style="font-size: 0.7rem;">Baru saja (Realtime)</small>
          </div>
        `;
        container.insertBefore(item, container.firstChild);
      }
    }

    window.notifyNewAttendance = notifyNewAttendance;
  });
</script>
