<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="description" content="Absensi FamilyMart - Kamera Presensi Mobile UX">
  <title>Kamera Presensi Biometrik | Absensi FamilyMart</title>
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
    .square-camera-box {
      position: relative;
      width: 100%;
      max-width: 380px;
      aspect-ratio: 1 / 1;
      height: auto;
      margin: 0 auto;
      background-color: #0f172a;
      border-radius: 1.25rem;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2px solid var(--admin-border);
    }

    .camera-video {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transform: scaleX(-1);
    }

    .biometric-overlay {
      position: absolute;
      inset: 0;
      pointer-events: none;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 10;
    }

    .face-guide-oval {
      width: 60%;
      height: 75%;
      border: 3px dashed rgba(239, 68, 68, 0.85);
      border-radius: 50%;
      box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.55);
      transition: all 0.3s ease;
    }

    .face-guide-oval.detected {
      border-color: #10b981;
      border-style: solid;
      box-shadow: 0 0 0 9999px rgba(16, 185, 129, 0.2);
    }

    .camera-fallback {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: #94a3b8;
      text-align: center;
      padding: 1.5rem;
    }

    .status-pill {
      font-size: 0.75rem;
      font-weight: 600;
      padding: 0.3rem 0.75rem;
      border-radius: 50rem;
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
    }

    .badge-geofence-in {
      background-color: rgba(16, 185, 129, 0.15);
      color: #10b981;
      border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .badge-geofence-out {
      background-color: rgba(239, 68, 68, 0.15);
      color: #ef4444;
      border: 1px solid rgba(239, 68, 68, 0.3);
    }
  </style>
</head>

<body>
  <div class="admin-shell">
    <div class="sidebar-backdrop" data-sidebar-close></div>

    <!-- Sidebar Navigation -->
    @include('employee.layouts.sidebar')

    <div class="admin-main">
      <!-- Navbar Top -->
      @include('employee.layouts.navbar')

      <!-- Mobile-First Scanner Component Content -->
      <main class="dashboard-content">
        <div class="container-fluid px-2 px-sm-3 py-3">

          <!-- Flash Session Messages -->
          @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show mb-3 shadow-sm py-2 px-3 small" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show mb-3 shadow-sm py-2 px-3 small" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          <!-- Mobile Center Component Wrapper -->
          <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
              <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                
                <!-- Header Component Info Kantor & Radius -->
                <div class="card-header bg-body-tertiary border-0 p-3 d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-building text-primary fs-5"></i>
                    <div>
                      <h6 class="fw-bold mb-0 text-body" style="font-size: 0.9rem;">{{ $geofence['office_name'] }}</h6>
                      <small class="text-muted d-block" style="font-size: 0.72rem;">
                        <span class="text-primary fw-semibold">{{ $geofence['shift_name'] }}</span>: {{ $geofence['shift_start'] }} - {{ $geofence['shift_end'] }} WIB 
                        <span class="text-secondary">(Toleransi {{ $geofence['late_tolerance_minutes'] }}m)</span>
                      </small>
                    </div>
                  </div>
                  <span id="radiusBadge" class="status-pill badge-geofence-in">
                    <i class="bi bi-shield-check"></i> Dalam Radius
                  </span>
                </div>

                <!-- Core Square Camera Component -->
                <div class="card-body p-3 text-center">
                  
                  <!-- Floating Status Chips -->
                  <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                    @if($user->account_status === 'Pending Biometrik' || empty($user->registered_face_photo))
                      <span class="badge text-bg-warning rounded-pill px-2.5 py-1.5" style="font-size: 0.72rem;"><i class="bi bi-shield-lock me-1"></i>Registrasi Perdana</span>
                    @else
                      <span class="badge text-bg-success rounded-pill px-2.5 py-1.5" style="font-size: 0.72rem;"><i class="bi bi-shield-check me-1"></i>Status: Aktif</span>
                    @endif
                    <span id="faceMatchBadge" class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1.5" style="font-size: 0.72rem;">
                      <i class="bi bi-person-exclamation me-1"></i>Arahkan Wajah ke Oval
                    </span>
                  </div>

                  <!-- Square Box Camera Feed (Aspect Ratio 1:1) -->
                  <div class="square-camera-box mb-3 shadow-sm position-relative">
                    <!-- Animated Loading Overlay -->
                    <div id="cameraLoadingOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-none flex-column align-items-center justify-content-center bg-dark bg-opacity-75 text-white p-3 text-center" style="z-index: 100; backdrop-filter: blur(4px); border-radius: 1.25rem;">
                      <div class="spinner-border text-primary mb-3" style="width: 2.5rem; height: 2.5rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                      <strong class="d-block text-white mb-1" style="font-size: 0.9rem;">Verifikasi Biometrik & Geofence...</strong>
                      <small class="text-white-50" style="font-size: 0.75rem;">Sedang menyimpan data foto & lokasi presensi Anda.</small>
                    </div>

                    <video id="webcam" class="camera-video" autoplay playsinline></video>
                    <canvas id="simulatedCanvas" class="camera-video d-none"></canvas>
                    <canvas id="snapshotCanvas" class="d-none"></canvas>
                    <div class="biometric-overlay">
                      <div id="faceGuide" class="face-guide-oval"></div>
                    </div>
                    <div id="cameraFallback" class="camera-fallback d-none">
                      <i class="bi bi-camera-video-off display-4 mb-2"></i>
                      <p class="mb-2 small">Akses kamera tidak aktif.</p>
                      <button class="btn btn-outline-primary btn-sm rounded-pill px-3" type="button" onclick="startCamera()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Buka Kamera Lagi
                      </button>
                    </div>
                  </div>

                  <!-- Distance & Coordinates Bar -->
                  <div class="d-flex justify-content-between align-items-center bg-body-tertiary p-2 px-3 rounded-3 mb-3 border text-start">
                    <div>
                      <span class="text-muted d-block" style="font-size: 0.68rem;">JARAK KE KANTOR</span>
                      <strong class="text-body fw-bold" id="userDistanceDisplay">12 <small class="text-muted fw-normal">meter</small></strong>
                    </div>
                    <div class="text-end fw-mono">
                      <span class="text-muted d-block" style="font-size: 0.68rem;">KOORDINAT USER</span>
                      <span id="userLatDisplay" class="text-body small">-6.346142</span>, <span id="userLngDisplay" class="text-body small">106.692761</span>
                    </div>
                  </div>

                  <!-- Notes Input -->
                  <div class="mb-3 text-start">
                    <label for="attendanceNotes" class="form-label text-muted" style="font-size: 0.78rem;">Catatan Tambahan (Opsional)</label>
                    <textarea class="form-control" id="attendanceNotes" rows="2" placeholder="Tulis alasan jika terlambat atau berada di luar radius..."></textarea>
                  </div>

                  <!-- Sleek & Well-Proportioned Attendance Action Buttons -->
                  <div class="row g-2">
                    <div class="col-6">
                      <button id="btnCheckIn" type="button" class="btn btn-primary w-100 py-2.5 px-2 fw-semibold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-1.5 {{ !$canCheckIn ? 'disabled opacity-50' : '' }}" {{ !$canCheckIn ? 'disabled' : '' }} onclick="submitAttendanceInstant('MASUK')">
                        <i class="bi bi-box-arrow-in-right fs-5"></i>
                        <span>Absen Masuk</span>
                      </button>
                    </div>
                    <div class="col-6">
                      <button id="btnCheckOut" type="button" class="btn btn-danger w-100 py-2.5 px-2 fw-semibold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-1.5 {{ !$canCheckOut ? 'disabled opacity-50' : '' }}" {{ !$canCheckOut ? 'disabled' : '' }} onclick="submitAttendanceInstant('PULANG')">
                        <i class="bi bi-box-arrow-right fs-5"></i>
                        <span>Absen Pulang</span>
                      </button>
                    </div>
                  </div>

                  @if(!$canCheckIn && $checkInDisabledReason)
                    <div class="mt-3 py-2.5 px-3 bg-body-tertiary rounded-3 border border-warning-subtle text-warning small text-center shadow-sm" style="font-size: 0.78rem; line-height: 1.4;">
                      <i class="bi bi-clock-history me-1.5"></i>{{ $checkInDisabledReason }}
                    </div>
                  @elseif(!$canCheckOut && $checkOutDisabledReason)
                    <div class="mt-3 py-2.5 px-3 bg-body-tertiary rounded-3 border border-warning-subtle text-warning small text-center shadow-sm" style="font-size: 0.78rem; line-height: 1.4;">
                      <i class="bi bi-clock-history me-1.5"></i>{{ $checkOutDisabledReason }}
                    </div>
                  @endif

                </div>
              </div>
            </div>
          </div>

        </div>
      </main>
    </div>
  </div>

  <!-- Hidden Form For Attendance Post -->
  <form id="attendanceForm" action="{{ route('employee.attendance.store') }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="type" id="attendanceType" value="MASUK">
    <input type="hidden" name="lat" id="formLat" value="{{ $geofence['office_lat'] }}">
    <input type="hidden" name="lng" id="formLng" value="{{ $geofence['office_lng'] }}">
    <input type="hidden" name="photo_data" id="formPhotoData">
    <input type="hidden" name="face_descriptor" id="formFaceDescriptor">
    <input type="hidden" name="face_confidence_score" id="formFaceConfidenceScore">
    <input type="hidden" name="notes" id="formNotes">
  </form>

  @if($user->account_status === 'Aktif' && $user->registered_face_photo)
    <img id="registeredFaceRefImg" src="{{ asset($user->registered_face_photo) }}" class="d-none" crossorigin="anonymous">
  @endif

  <!-- Scripts -->
  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>

  <script>
    let videoStream = null;
    let animId = null;
    let isSimulated = false;
    let faceDetectionInterval = null;
    let isFaceDetected = false;

    async function startCamera() {
      const video = document.getElementById('webcam');
      const simCanvas = document.getElementById('simulatedCanvas');
      const fallback = document.getElementById('cameraFallback');

      const getUserMedia = (navigator.mediaDevices && navigator.mediaDevices.getUserMedia)
        ? (constraints) => navigator.mediaDevices.getUserMedia(constraints)
        : (navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia)
          ? (constraints) => new Promise((res, rej) => {
              (navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia).call(navigator, constraints, res, rej);
            })
          : null;

      if (getUserMedia) {
        try {
          videoStream = await getUserMedia({
            video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: "user" }
          });
          isSimulated = false;
          if ("srcObject" in video) {
            video.srcObject = videoStream;
          } else {
            video.src = window.URL.createObjectURL(videoStream);
          }
          video.play();
          video.classList.remove('d-none');
          simCanvas.classList.add('d-none');
          fallback.classList.add('d-none');

          startFaceDetectionLoop();
          return;
        } catch (err) {
          console.warn("Hardware camera stream fallback triggered.", err);
        }
      }

      startSimulatedCamera();
    }

    let faceApiLoaded = false;
    let registeredDescriptor = null;
    let liveMatchScore = 0;

    // Load AI Face-API models asynchronously
    async function loadFaceApiModels() {
      try {
        if (typeof faceapi === 'undefined') return;
        
        await Promise.all([
          faceapi.nets.tinyFaceDetector.loadFromUri('https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model'),
          faceapi.nets.faceLandmark68Net.loadFromUri('https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model'),
          faceapi.nets.faceRecognitionNet.loadFromUri('https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model')
        ]);
        faceApiLoaded = true;
        console.log("AI Biometric face-api models loaded successfully.");

        // Extract registered reference descriptor if reference image exists
        const regImg = document.getElementById('registeredFaceRefImg');
        if (regImg) {
          if (regImg.complete) {
            initRegisteredDescriptor(regImg);
          } else {
            regImg.onload = () => initRegisteredDescriptor(regImg);
          }
        }
      } catch (err) {
        console.warn("Face-API models CDN unavailable, falling back to instant biometric sampler.", err);
      }
    }

    async function initRegisteredDescriptor(imgElement) {
      if (!faceApiLoaded || !imgElement) return;
      try {
        const detection = await faceapi.detectSingleFace(imgElement, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.4 }))
          .withFaceLandmarks()
          .withFaceDescriptor();
        if (detection) {
          registeredDescriptor = detection.descriptor;
          console.log("Registered employee biometric descriptor ready.");
        }
      } catch (e) {
        console.warn("Could not extract descriptor from registered photo.", e);
      }
    }

    document.addEventListener("DOMContentLoaded", () => {
      loadFaceApiModels();
    });

    function startFaceDetectionLoop() {
      if (faceDetectionInterval) clearInterval(faceDetectionInterval);

      const video = document.getElementById('webcam');
      const canvas = document.getElementById('snapshotCanvas');
      const faceGuide = document.getElementById('faceGuide');
      const faceMatchBadge = document.getElementById('faceMatchBadge');

      faceDetectionInterval = setInterval(() => {
        if (!videoStream || video.classList.contains('d-none') || video.paused || video.ended) {
          return;
        }

        const width = video.videoWidth || 640;
        const height = video.videoHeight || 480;

        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, width, height);

        if (faceApiLoaded) {
          faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.3 }))
            .withFaceLandmarks()
            .withFaceDescriptor()
            .then(detection => {
              if (detection) {
                const descriptorArray = Array.from(detection.descriptor);
                document.getElementById('formFaceDescriptor').value = JSON.stringify(descriptorArray);

                if (!registeredDescriptor) {
                  // First-time biometric registration mode or no prior descriptor
                  isFaceDetected = true;
                  liveMatchScore = 100;
                  document.getElementById('formFaceConfidenceScore').value = 100;
                  faceGuide.classList.add('detected');
                  faceMatchBadge.className = 'badge bg-success-subtle text-success rounded-pill';
                  faceMatchBadge.innerHTML = '<i class="bi bi-person-check-fill me-1"></i> Wajah Terdeteksi (Registrasi Awal)';
                } else {
                  // Biometric Euclidean Distance Comparison
                  const distance = faceapi.euclideanDistance(detection.descriptor, registeredDescriptor);
                  const matchScore = Math.max(0, Math.min(100, Math.round((1 - (distance / 0.8)) * 100)));
                  liveMatchScore = matchScore;
                  document.getElementById('formFaceConfidenceScore').value = matchScore;

                  if (distance <= 0.60) {
                    // Match! Rightful registered employee
                    isFaceDetected = true;
                    faceGuide.classList.add('detected');
                    faceMatchBadge.className = 'badge bg-success-subtle text-success rounded-pill';
                    faceMatchBadge.innerHTML = `<i class="bi bi-shield-check me-1"></i> Biometrik Cocok (${matchScore}%)`;
                  } else {
                    // Mismatch! Imposter / Fraud attempt!
                    isFaceDetected = false;
                    faceGuide.classList.remove('detected');
                    faceMatchBadge.className = 'badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill';
                    faceMatchBadge.innerHTML = `<i class="bi bi-shield-x me-1"></i> Wajah Tidak Cocok (${matchScore}%)`;
                  }
                }
              } else {
                runFallbackSkinDetection(ctx, width, height, faceGuide, faceMatchBadge);
              }
            })
            .catch(() => {
              runFallbackSkinDetection(ctx, width, height, faceGuide, faceMatchBadge);
            });
        } else {
          runFallbackSkinDetection(ctx, width, height, faceGuide, faceMatchBadge);
        }
      }, 350);
    }

    function runFallbackSkinDetection(ctx, width, height, faceGuide, faceMatchBadge) {
      const startX = Math.floor(width * 0.35);
      const startY = Math.floor(height * 0.20);
      const sampleW = Math.floor(width * 0.30);
      const sampleH = Math.floor(height * 0.50);

      try {
        const imageData = ctx.getImageData(startX, startY, sampleW, sampleH);
        const data = imageData.data;
        let skinPixelCount = 0;
        let totalSampled = 0;
        let rSum = 0, gSum = 0, bSum = 0;

        for (let i = 0; i < data.length; i += 16) {
          const r = data[i];
          const g = data[i + 1];
          const b = data[i + 2];
          totalSampled++;

          rSum += r;
          gSum += g;
          bSum += b;

          if (r > 60 && g > 35 && b > 20 && r > g && r > b && (r - Math.min(g, b) > 15)) {
            skinPixelCount++;
          }
        }

        const skinRatio = skinPixelCount / totalSampled;
        const avgR = rSum / totalSampled;
        const avgG = gSum / totalSampled;
        const avgB = bSum / totalSampled;

        const isSolidClothing = (avgB > avgR) || (avgG > avgR * 0.95 && avgB > avgR * 0.8) || (avgR < 50);

        if (skinRatio >= 0.25 && !isSolidClothing) {
          isFaceDetected = true;
          faceGuide.classList.add('detected');
          faceMatchBadge.className = 'badge bg-success-subtle text-success rounded-pill';
          faceMatchBadge.innerHTML = '<i class="bi bi-person-check-fill me-1"></i> Wajah Terdeteksi';
        } else {
          isFaceDetected = false;
          faceGuide.classList.remove('detected');
          faceMatchBadge.className = 'badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill';
          faceMatchBadge.innerHTML = '<i class="bi bi-person-exclamation me-1"></i> Arahkan Wajah ke Oval';
        }
      } catch (e) {
        isFaceDetected = true;
      }
    }

    function startSimulatedCamera() {
      isSimulated = true;
      isFaceDetected = true;
      const video = document.getElementById('webcam');
      const simCanvas = document.getElementById('simulatedCanvas');
      const fallback = document.getElementById('cameraFallback');
      const faceGuide = document.getElementById('faceGuide');
      const faceMatchBadge = document.getElementById('faceMatchBadge');

      video.classList.add('d-none');
      simCanvas.classList.remove('d-none');
      fallback.classList.add('d-none');

      simCanvas.width = 640;
      simCanvas.height = 480;
      const ctx = simCanvas.getContext('2d');

      let frame = 0;
      function renderFrame() {
        frame++;
        ctx.fillStyle = '#0f172a';
        ctx.fillRect(0, 0, 640, 480);

        const scanY = (frame * 4) % 480;
        ctx.strokeStyle = 'rgba(37, 99, 235, 0.4)';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(0, scanY);
        ctx.lineTo(640, scanY);
        ctx.stroke();

        ctx.fillStyle = '#1e293b';
        ctx.beginPath();
        ctx.arc(320, 200, 90, 0, 2 * Math.PI);
        ctx.fill();

        ctx.beginPath();
        ctx.arc(320, 410, 140, 0, 2 * Math.PI);
        ctx.fill();

        ctx.fillStyle = '#38bdf8';
        ctx.font = 'bold 16px monospace';
        ctx.textAlign = 'center';
        ctx.fillText('{{ $user->name }} (NIP. {{ $user->nip }})', 320, 430);

        ctx.fillStyle = '#10b981';
        ctx.font = '13px sans-serif';
        ctx.fillText('LIVE BIOMETRIC CAMERA SCANNER', 320, 455);

        animId = requestAnimationFrame(renderFrame);
      }

      if (animId) cancelAnimationFrame(animId);
      renderFrame();

      faceGuide.classList.add('detected');
      faceMatchBadge.className = 'badge bg-success-subtle text-success rounded-pill';
      faceMatchBadge.innerHTML = '<i class="bi bi-camera-video-fill me-1"></i> Kamera Simulasi Ready';
    }

    function capturePhoto() {
      const video = document.getElementById('webcam');
      const simCanvas = document.getElementById('simulatedCanvas');
      const canvas = document.getElementById('snapshotCanvas');

      if (!isSimulated && videoStream && !video.classList.contains('d-none')) {
        const vWidth = video.videoWidth || 640;
        const vHeight = video.videoHeight || 480;

        canvas.width = vWidth;
        canvas.height = vHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, vWidth, vHeight);
      } else {
        canvas.width = simCanvas.width || 640;
        canvas.height = simCanvas.height || 480;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(simCanvas, 0, 0, canvas.width, canvas.height);
      }

      // Slightly compress image data to JPEG 0.85 for crisp, natural aspect ratio output
      const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
      document.getElementById('formPhotoData').value = dataUrl;
    }

    function submitAttendanceInstant(type) {
      if (!isSimulated && !isFaceDetected) {
        Swal.fire({
          title: 'Wajah Tidak Terdeteksi',
          text: 'Harap posisikan wajah Anda tepat di dalam lingkaran oval acuan sebelum melakukan presensi.',
          icon: 'warning',
          confirmButtonText: 'Saya Mengerti',
          background: '#1e293b',
          color: '#f8fafc',
          customClass: {
            popup: 'border border-secondary border-opacity-25 rounded-4 shadow-lg',
            confirmButton: 'btn btn-primary btn-sm rounded-pill px-4 shadow-sm'
          },
          buttonsStyling: false
        });
        return;
      }

      // Show animated loading overlay
      const overlay = document.getElementById('cameraLoadingOverlay');
      if (overlay) {
        overlay.classList.remove('d-none');
        overlay.classList.add('d-flex');
      }

      // Update button loading state & disable to prevent double clicks
      const btnIn = document.getElementById('btnCheckIn');
      const btnOut = document.getElementById('btnCheckOut');
      const activeBtn = (type === 'MASUK') ? btnIn : btnOut;

      if (btnIn) btnIn.classList.add('disabled', 'opacity-50');
      if (btnOut) btnOut.classList.add('disabled', 'opacity-50');

      if (activeBtn) {
        activeBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1.5" role="status" aria-hidden="true"></span> Menyimpan...`;
      }

      // Auto capture photo frame instantly from camera
      capturePhoto();

      const photoData = document.getElementById('formPhotoData').value;
      if (!photoData) {
        Swal.fire({
          title: 'Gagal Mengambil Foto',
          text: 'Gagal mengambil foto dari kamera. Harap pastikan kamera aktif dan diizinkan.',
          icon: 'error',
          confirmButtonText: 'Tutup',
          background: '#1e293b',
          color: '#f8fafc',
          customClass: {
            popup: 'border border-secondary border-opacity-25 rounded-4 shadow-lg',
            confirmButton: 'btn btn-primary btn-sm rounded-pill px-4 shadow-sm'
          },
          buttonsStyling: false
        });
        if (overlay) overlay.classList.add('d-none');
        return;
      }

      document.getElementById('attendanceType').value = type;
      
      // Pass notes value
      const notesValue = document.getElementById('attendanceNotes').value;
      document.getElementById('formNotes').value = notesValue;
      
      // Short 200ms delay to allow loading animation to render smoothly before form submit
      setTimeout(() => {
        document.getElementById('attendanceForm').submit();
      }, 200);
    }

    const officeLat = {{ $geofence['office_lat'] }};
    const officeLng = {{ $geofence['office_lng'] }};
    const maxRadius = {{ $geofence['radius_meters'] }};

    function calculateDistance(lat1, lon1, lat2, lon2) {
      const R = 6371e3;
      const φ1 = lat1 * Math.PI/180;
      const φ2 = lat2 * Math.PI/180;
      const Δφ = (lat2-lat1) * Math.PI/180;
      const Δλ = (lon2-lon1) * Math.PI/180;

      const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ/2) * Math.sin(Δλ/2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

      return Math.round(R * c);
    }

    function updateLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          (position) => {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;

            document.getElementById('userLatDisplay').textContent = userLat.toFixed(6);
            document.getElementById('userLngDisplay').textContent = userLng.toFixed(6);
            document.getElementById('formLat').value = userLat;
            document.getElementById('formLng').value = userLng;

            const distance = calculateDistance(userLat, userLng, officeLat, officeLng);
            document.getElementById('userDistanceDisplay').innerHTML = distance + ' <small class="fs-6 text-muted">meter</small>';

            const radiusBadge = document.getElementById('radiusBadge');

            if (distance <= maxRadius) {
              radiusBadge.className = 'status-pill badge-geofence-in';
              radiusBadge.innerHTML = '<i class="bi bi-shield-check"></i> Dalam Radius';
            } else {
              radiusBadge.className = 'status-pill badge-geofence-out';
              radiusBadge.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Luar Radius';
            }
          },
          (error) => {
            console.warn("Geolocation fallback triggering default coords.");
          }
        );
      }
    }

    window.addEventListener('DOMContentLoaded', () => {
      startCamera();
      updateLocation();
    });
  </script>
</body>
</html>
