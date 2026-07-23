

Product Requirements Document (PRD)
## Sistem Presensi: Absensiku Pro
## 1. Informasi Dokumen
AtributKeterangan
Nama ProdukAbsensiku Pro
Penulis Dokumen / EngineerSyarif Hidayatullah
Target PlatformWeb-Based (Dioptimalkan untuk Mobile Device)
StatusDraft (Tahap Perencanaan Teknis) - V2
- Ringkasan Eksekutif (Executive Summary)
Absensiku Pro adalah perangkat lunak Human Resources Information System (HRIS) berskala
menengah yang berfokus pada penyelesaian masalah kecurangan kehadiran (seperti "titip absen" atau
penggunaan fake GPS) pada lingkungan kerja terpusat maupun lapangan. Aplikasi ini menggunakan
mekanisme otentikasi ganda: pelacakan koordinat Real-Time Geofencing (radius meter) dan pemindaian
Biometrik (Face Detection) menggunakan kamera perangkat sebelum karyawan diizinkan untuk
mencatatkan waktu kehadirannya.
- Arsitektur dan Tech Stack Utama
Backend Framework: Laravel (PHP)
Database: MySQL (Relational Database)
Administration Panel: Laravel Blade dengan Custom Template
Frontend Styling: Tailwind CSS (Utility-first styling)
Frontend Interactivity: Alpine.js (Manajemen state klien)
Sistem Pemetaan: Leaflet.js dengan basemap OpenStreetMap (OSM)
Engine Biometrik Klien: face-api.js atau MediaPipe
- Pengguna Target (User Personas)
Karyawan (User): Pengguna akhir yang akan mengakses aplikasi melalui browser mobile setiap hari.
## •
## •
## •
## •
## •
## •
## •
## 1.

Administrator (Admin / HR): Pengelola sistem yang menggunakan dashboard desktop/tablet.
- Kebutuhan Fungsional (Functional Requirements)
5.1 Modul Karyawan (Front-End Presensi)
ID FiturNama FiturDeskripsi Detail
F-EMP-01Status Harian InteraktifMenampilkan informasi status kehadiran terkini ("Belum
Presensi", "Sudah Absen Masuk", atau "Sudah Absen Pulang").
F-EMP-02Akses Kamera WebRTCAplikasi meminta izin kamera, melakukan deteksi wajah, dan
mem-capture gambar Base64 secara otomatis saat presensi.
F-EMP-03Live GPS & HaversineMelacak koordinat dan menghitung jarak dari kantor. Tombol
absensi hanya aktif jika jarak_user <= radius_kantor.
F-EMP-04Riwayat Presensi
Tabel read-only yang menampilkan riwayat presensi karyawan
(30 hari terakhir) beserta pratinjau foto.
5.2 Modul Administrator (Custom Blade Template)
ID FiturNama FiturDeskripsi Detail
F-ADM-01Statistik DashboardMenampilkan agregasi data harian: Total Karyawan, Hadir,
Terlambat, dan Presensi di Luar Radius.
F-ADM-02Peta Persebaran LiveIntegrasi Leaflet.js memplot marker koordinat karyawan yang
melakukan presensi hari ini.
F-ADM-03Konfigurasi GeofencingForm interaktif dengan Leaflet marker untuk mengupdate lat/lng
kantor dan menyimpannya ke MySQL.
F-ADM-04Manajemen KaryawanFasilitas CRUD untuk master data Karyawan, role admin/user,
dan password.
F-ADM-05Ekspor Data Laporan
Menyaring tabel attendance_logs dan mengekspor hasilnya
dalam format .CSV/.XLSX.
- Kebutuhan Non-Fungsional (Non-Functional Requirements)
Mobile-First Constraints: Mengunci skala viewport (maximum-scale=1.0, user-scalable=no).
Privasi Peramban: Aplikasi web di-host pada environment HTTPS atau localhost.
## 2.
## •
## •

Penyimpanan Database Efisien: Gambar tangkapan layar dikompres menjadi file standar (.jpg/.png)
oleh Laravel. Tabel log hanya menyimpan path file-nya.
Keamanan Laravel: Middleware otentikasi serta perlindungan CSRF Token.
## 7. Desain Skema Database Konseptual
- Table users (id, nip, name, email, password, role)
- Table attendance_logs (id, user_id, date, time, type [MASUK/PULANG], lat, lng,
distance_from_office, face_confidence_score, photo_path)
- Table geofence_settings (id, office_name, lat, lng, radius_in_meters, is_active)
## •
## •