<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Absensiku Pro - Geofencing & Lokasi Kantor">
  <title>Geofencing & Lokasi | Absensiku Pro</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  
  <!-- Leaflet.js CSS for Interactive Geofence Map -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>

<body>
  <div class="admin-shell">
    <div class="sidebar-backdrop" data-sidebar-close></div>

    <!-- Sidebar Admin -->
    @include('admin.layouts.sidebar')

    <div class="admin-main">
      <!-- Navbar Top -->
      @include('admin.layouts.navbar')

      <!-- Dashboard Content - Geofencing & Lokasi -->
      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">
          <!-- Page Heading -->
          <div class="page-heading">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="bi bi-geo-alt" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1">CONFIGURATION</p>
                <h1 class="h3 mb-1">Geofencing & Lokasi Kantor</h1>
                <p class="text-muted mb-0">Atur titik koordinat lokasi kantor (Latitude/Longitude) dan batas radius aman (meter) untuk presensi karyawan.</p>
              </div>
            </div>
            <div class="heading-actions flex-wrap gap-2">
              <button class="btn btn-outline-secondary btn-sm" type="button" id="resetMapBtn">
                <i class="bi bi-arrow-clockwise me-1" aria-hidden="true"></i> Reset Peta
              </button>
            </div>
          </div>

          @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          <!-- Metrik Ringkasan Geofencing (Equal Height Metric Cards) -->
          <section class="row g-3 mt-1" aria-label="Geofencing metrics">
            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-primary h-100 d-flex flex-column justify-content-between">
                <div class="metric-top">
                  <span class="metric-label">LOKASI TERDAFTAR</span>
                  <span class="metric-icon"><i class="bi bi-building" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value my-2">{{ $totalLocations }}</div>
                <div class="metric-meta">
                  <span class="text-success">{{ $activeLocations }} aktif</span>
                  <span>area presensi</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-success h-100 d-flex flex-column justify-content-between">
                <div class="metric-top">
                  <span class="metric-label">RADIUS AMAN UTAMA</span>
                  <span class="metric-icon"><i class="bi bi-bounding-box-circles" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value my-2">{{ $primaryGeofence ? $primaryGeofence->radius_in_meters . 'm' : '50m' }}</div>
                <div class="metric-meta text-truncate">
                  <span class="text-success">Toleransi Haversine</span>
                  <span>{{ $primaryGeofence->office_name ?? 'Kantor Utama' }}</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-warning h-100 d-flex flex-column justify-content-between">
                <div class="metric-top">
                  <span class="metric-label">KOORDINAT KANTOR</span>
                  <span class="metric-icon"><i class="bi bi-pin-map" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value my-2" style="font-size: 1.1rem;">{{ $primaryGeofence ? $primaryGeofence->lat . ', ' . $primaryGeofence->lng : '-6.2088, 106.8456' }}</div>
                <div class="metric-meta">
                  <span class="text-warning">GPS High-Accuracy</span>
                  <span>Lat / Lng</span>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
              <article class="metric-card metric-danger h-100 d-flex flex-column justify-content-between">
                <div class="metric-top">
                  <span class="metric-label">JAM KERJA DEFAULT</span>
                  <span class="metric-icon"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value my-2" style="font-size: 1.1rem;">{{ $primaryGeofence ? $primaryGeofence->shift_start . ' - ' . $primaryGeofence->shift_end : '08:00 - 17:00' }}</div>
                <div class="metric-meta">
                  <span class="text-danger">Toleransi {{ $primaryGeofence->late_tolerance_minutes ?? 15 }}m</span>
                </div>
              </article>
            </div>
          </section>

          <!-- Panels Map & Form Geofencing -->
          <section class="row g-3 mt-1">
            <div class="col-12 col-xl-8">
              <div class="panel h-100">
                <div class="panel-header">
                  <div>
                    <h2 class="h5 mb-1 section-title"><i class="bi bi-map" aria-hidden="true"></i><span>Visualisasi Radius Peta Geofence</span></h2>
                    <p class="text-muted mb-0">Klik pada peta atau geser marker untuk menyesuaikan titik pusat lokasi kantor dan radius secara real-time.</p>
                  </div>
                  <span class="badge text-bg-success"><i class="bi bi-geo-alt-fill me-1"></i>Leaflet OpenStreetMap Active</span>
                </div>

                <div class="p-3">
                  <div id="map" style="height: 380px; width: 100%; border-radius: 8px;" class="shadow-sm border"></div>
                </div>
              </div>
            </div>

            <div class="col-12 col-xl-4">
              <div class="panel h-100">
                <div class="panel-header">
                  <div>
                    <h2 class="h5 mb-1 section-title"><i class="bi bi-sliders" aria-hidden="true"></i><span>Form Titik Presensi</span></h2>
                    <p class="text-muted mb-0">Pengaturan parameter geofencing aktif.</p>
                  </div>
                </div>

                <div class="p-3">
                  <form action="{{ route('admin.geofences.update', $primaryGeofence->id ?? 1) }}" method="POST" id="geofenceForm">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                      <label for="officeName" class="form-label">Nama Lokasi / Cabang Kantor</label>
                      <input type="text" class="form-control" id="officeName" name="office_name" value="{{ $primaryGeofence->office_name ?? '' }}" required>
                    </div>

                    <div class="row g-2 mb-3">
                      <div class="col-md-6">
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="text" class="form-control fw-mono" id="latitude" name="lat" value="{{ $primaryGeofence->lat ?? '' }}" required>
                      </div>
                      <div class="col-md-6">
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="text" class="form-control fw-mono" id="longitude" name="lng" value="{{ $primaryGeofence->lng ?? '' }}" required>
                      </div>
                    </div>

                    <div class="mb-3">
                      <label for="radiusMeters" class="form-label">Batas Radius Presensi (Meter)</label>
                      <div class="input-group">
                        <input type="number" class="form-control" id="radiusMeters" name="radius_in_meters" value="{{ $primaryGeofence->radius_in_meters ?? 50 }}" min="5" max="5000" required>
                        <span class="input-group-text">Meter</span>
                      </div>
                    </div>

                    <div class="row g-2 mb-3">
                      <div class="col-6">
                        <label for="shiftStart" class="form-label">Jam Masuk</label>
                        <input type="time" class="form-control" id="shiftStart" name="shift_start" value="{{ $primaryGeofence->shift_start ?? '08:00' }}">
                      </div>
                      <div class="col-6">
                        <label for="shiftEnd" class="form-label">Jam Pulang</label>
                        <input type="time" class="form-control" id="shiftEnd" name="shift_end" value="{{ $primaryGeofence->shift_end ?? '17:00' }}">
                      </div>
                    </div>

                    <div class="mb-4">
                      <label for="lateTolerance" class="form-label">Toleransi Keterlambatan (Menit)</label>
                      <input type="number" class="form-control" id="lateTolerance" name="late_tolerance_minutes" value="{{ $primaryGeofence->late_tolerance_minutes ?? 15 }}" min="0">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                      <i class="bi bi-save me-1"></i> Simpan Konfigurasi Geofencing
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </section>

          </section>
        </div>
      </main>
    </div>
  </div>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  
  <!-- Leaflet.js JS Script for Geofencing Interactive Map -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      var defaultLat = {{ $primaryGeofence->lat ?? -6.2087634 }};
      var defaultLng = {{ $primaryGeofence->lng ?? 106.8455992 }};
      var defaultRadius = {{ $primaryGeofence->radius_in_meters ?? 50 }};
      var officeName = "{{ $primaryGeofence->office_name ?? 'Kantor Utama' }}";

      var map = L.map('map').setView([defaultLat, defaultLng], 16);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
      }).addTo(map);

      var marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
      marker.bindPopup("<b>" + officeName + "</b><br>Titik Pusat Geofence").openPopup();

      var circle = L.circle([defaultLat, defaultLng], {
        color: '#2563eb',
        fillColor: '#3b82f6',
        fillOpacity: 0.25,
        radius: defaultRadius
      }).addTo(map);

      function updateInputs(lat, lng) {
        document.getElementById('latitude').value = parseFloat(lat).toFixed(7);
        document.getElementById('longitude').value = parseFloat(lng).toFixed(7);
      }

      marker.on('dragend', function (e) {
        var coord = marker.getLatLng();
        circle.setLatLng(coord);
        updateInputs(coord.lat, coord.lng);
      });

      map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        circle.setLatLng(e.latlng);
        updateInputs(e.latlng.lat, e.latlng.lng);
      });

      var radiusInput = document.getElementById('radiusMeters');
      if (radiusInput) {
        radiusInput.addEventListener('input', function () {
          var r = parseInt(this.value) || 50;
          circle.setRadius(r);
        });
      }

      var resetBtn = document.getElementById('resetMapBtn');
      if (resetBtn) {
        resetBtn.addEventListener('click', function () {
          map.setView([defaultLat, defaultLng], 16);
          marker.setLatLng([defaultLat, defaultLng]);
          circle.setLatLng([defaultLat, defaultLng]);
          circle.setRadius(defaultRadius);
          updateInputs(defaultLat, defaultLng);
        });
      }
    });
  </script>
</body>
</html>
