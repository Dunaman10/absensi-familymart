# Absensiku Pro 📸📍

**Absensiku Pro** adalah sistem presensi modern berbasis web yang dilengkapi dengan teknologi **Deteksi Biometrik Wajah (Face Recognition)** dan **Validasi Lokasi (Geofencing)**. Aplikasi ini dibangun dengan Laravel dan bertujuan untuk mencegah kecurangan dalam absensi karyawan dengan memastikan bahwa karyawan yang melakukan presensi berada di lokasi kantor yang benar dan memverifikasi wajah mereka secara real-time.

---

## 🌟 Fitur Utama

### 🏢 Untuk Admin (HR / Management)
- **Dashboard Analitik**: Pantau ringkasan presensi harian secara real-time.
- **Manajemen Karyawan**: Tambah, edit, hapus, dan lihat data karyawan beserta status pendaftaran biometrik (aktif / pending).
- **Log Presensi Realtime**: Lihat riwayat presensi semua karyawan lengkap dengan foto hasil scan, koordinat lokasi, waktu presensi, dan status (Masuk / Pulang).
- **Pengaturan Geofencing**: Tentukan koordinat titik tengah kantor (Latitude & Longitude) serta radius toleransi jarak maksimal untuk karyawan bisa melakukan presensi.
- **Profil Admin**: Pengaturan profil (nama, avatar maksimal 1MB) dan kata sandi.

### 👤 Untuk Karyawan (User)
- **Kamera Presensi Biometrik**: 
  - Registrasi biometrik pada saat pertama kali presensi.
  - Verifikasi wajah *real-time* dengan teknologi **Face-API.js** (AI Biometrics) saat melakukan absen Masuk & Pulang.
  - Liveness detection & fallback skin-color detection.
- **Validasi Lokasi (Geofence)**: Presensi hanya dapat dilakukan jika jarak karyawan ke kantor sesuai dengan radius yang ditetapkan Admin.
- **Riwayat Presensi Saya**: Memantau catatan riwayat waktu kerja.
- **Profil Karyawan**: Mengubah detail pribadi, foto biometrik (dengan batasan ukuran 1MB), dan password.

---

## 🛠️ Teknologi yang Digunakan

- **Backend**: [Laravel](https://laravel.com/) (PHP)
- **Frontend**: Blade Templating, HTML5, Vanilla JavaScript, [Bootstrap 5](https://getbootstrap.com/)
- **Biometrik**: [face-api.js](https://github.com/vladmandic/face-api) (TensorFlow.js berbasis browser)
- **Database**: MySQL / SQLite (Sesuai Konfigurasi Laravel)
- **Styling Tambahan**: Custom CSS Modern (Dark & Light Mode Toggle), Bootstrap Icons
- **Notifikasi/Alert**: [SweetAlert2](https://sweetalert2.github.io/)

---

## 🚀 Cara Instalasi ke Local (Local Development)

Ikuti panduan berikut dari nol hingga aplikasi berhasil dijalankan di komputer lokal Anda:

### 1. Persyaratan Sistem
Pastikan komputer Anda sudah terinstal:
- **PHP** (>= 8.2 direkomendasikan)
- **Composer** (Package Manager PHP)
- **MySQL / MariaDB** (atau database lokal yang mendukung Laravel, seperti SQLite)
- **Git**
- Browser modern (Chrome/Edge/Firefox) yang mendukung API Kamera (`navigator.mediaDevices.getUserMedia`).

### 2. Langkah Instalasi

1. **Clone Repository**
   Buka terminal/command prompt dan jalankan perintah berikut:
   ```bash
   git clone https://github.com/Dunaman10/absensiku-pro.git
   cd absensiku_pro
   ```

2. **Install Dependencies PHP**
   Unduh semua library framework Laravel melalui Composer:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment (.env)**
   Copy file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` dan atur koneksi database Anda. Contoh untuk MySQL:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=absensiku_pro
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate Application Key**
   Buat application key unik untuk enkripsi sesi Laravel:
   ```bash
   php artisan key:generate
   ```

5. **Migrasi Database & Seeder**
   Buat struktur tabel ke dalam database beserta data awal (Default Admin & Pengaturan Geofence):
   ```bash
   php artisan migrate --seed
   ```
   *(Pastikan Anda membuat database kosong bernama `absensiku_pro` terlebih dahulu di MySQL/phpMyAdmin sebelum perintah ini dijalankan)*

6. **Link Storage (Upload Foto)**
   Agar foto profil karyawan dapat diakses dari browser, hubungkan folder storage:
   ```bash
   php artisan storage:link
   ```

7. **Jalankan Aplikasi**
   Nyalakan server development bawaan Laravel:
   ```bash
   php artisan serve
   ```
   Aplikasi Anda kini berjalan di `http://127.0.0.1:8000`.

---

## 🔑 Akses Login Default

Setelah proses migrasi dan seeder selesai, Anda dapat login menggunakan akun berikut:

**Admin HR:**
- **NIP:** `ADMIN001`
- **Password:** `password`

*(Anda bisa mendaftarkan akun karyawan (User) baru melalui halaman Dashboard Admin).*

---

## ⚠️ Catatan Penting Saat Development

- **Izin Akses Kamera dan Lokasi**: Saat mengakses halaman presensi (Scan Karyawan), browser akan meminta izin (Prompt) untuk menggunakan **Kamera** dan **Lokasi (GPS)**. Anda harus mengizinkannya agar validasi biometrik & geofencing bisa bekerja.
- **Konteks Keamanan Browser (HTTPS)**: API Kamera web (WebRTC/`getUserMedia`) dan API Lokasi (`navigator.geolocation`) biasanya membutuhkan jaringan aman **HTTPS**, KECUALI jika diakses dari `localhost` atau `127.0.0.1`. Jika Anda mengakses lewat IP jaringan (contoh: `192.168.x.x`), fitur kamera mungkin akan di-blokir oleh browser. Solusinya, gunakan *port-forwarding* lokal (seperti ngrok) atau instal SSL lokal.
- **Ukuran File Upload**: Upload foto biometrik karyawan sudah dikunci maksimal **1 MB** pada Frontend maupun Backend. 

---

Dikembangkan dengan ❤️ untuk modernisasi presensi karyawan.
