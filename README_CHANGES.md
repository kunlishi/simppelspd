## Ringkasan Perubahan Docker & Infrastruktur

Dokumen ini merangkum daftar perubahan utama yang dilakukan pada konfigurasi container projek beserta alasan di balik setiap keputusan.

### 1. Dockerfile Baru Berbasis PHP-FPM & Build Terpisah
- **Apa yang berubah:** Dockerfile dirombak menjadi multi-stage (frontend ? php-base ? vendor ? runtime) dengan basis `php:8.3-fpm-bookworm`.
- **Alasan:**
  - Memastikan ekstensi PHP (seperti `pdo_mysql`, `gd`, `intl`, `zip`) tersedia saat Composer dijalankan.
  - Memisahkan build aset Vite dan instalasi vendor untuk menghasilkan image runtime yang lebih kecil dan konsisten.
  - FPM memudahkan integrasi dengan Nginx reverse proxy dan dukungan SSL.

### 2. Konfigurasi `.env` Otomatis Saat Build
- **Apa yang berubah:** Tahap `vendor` membuat salinan `.env` sementara dengan koneksi SQLite.
- **Alasan:** Skrip artisan yang dieksekusi oleh Composer membutuhkan koneksi database; menggunakan SQLite lokal mencegah kegagalan build sebelum MySQL tersedia.

### 3. Nginx Reverse Proxy dengan SSL Otomatis
- **Apa yang berubah:** Ditambahkan image Nginx kustom (`docker/nginx`) yang meng-generate sertifikat self-signed jika belum tersedia, memaksa HTTP ? HTTPS, dan meneruskan trafik ke PHP-FPM.
- **Alasan:**
  - Menghadirkan HTTPS secara default untuk lingkungan pengembangan.
  - Memudahkan penggantian sertifikat dengan menyalin file custom ke volume host.

### 4. `docker-compose.yml` Terintegrasi
- **Apa yang berubah:** Compose kini menyediakan empat service: `app` (PHP-FPM), `web` (Nginx SSL), `mysql`, dan `phpmyadmin`, lengkap dengan healthcheck, volume, dan opsi konfigurasi port.
- **Alasan:** Menyajikan stack lengkap siap pakai (aplikasi + database + tooling administrasi) dengan satu perintah `docker compose up`.

### 5. Port & Environment Variable Default
- **Apa yang berubah:** `.env.example` sekarang mencantumkan variabel port (`APP_HTTP_PORT`, `APP_HTTPS_PORT`, `PHPMYADMIN_PORT`, `FORWARD_DB_PORT`).
- **Alasan:** Mempermudah kustomisasi mapping port tanpa mengedit `docker-compose.yml` secara langsung.

### 6. Dokumentasi Diperbarui
- **Apa yang berubah:**
  - README utama menambahkan bagian panduan Docker, HTTPS, dan cara mengekspor/memasang sertifikat.
  - `README_DOCKER.md` diganti dengan instruksi lengkap mengenai stack SSL, override port, dan troubleshooting.
- **Alasan:** Memberi panduan operasional yang jelas bagi tim ketika menjalankan environment Docker dengan SSL dan layanan pendukung.

### 7. Lockfile Frontend Disinkronkan
- **Apa yang berubah:** `pnpm-lock.yaml` diperbarui agar selaras dengan `package.json`.
- **Alasan:** Menghindari error `ERR_PNPM_OUTDATED_LOCKFILE` saat build Docker menjalankan `pnpm install --frozen-lockfile`.

---

Perubahan di atas bertujuan menjadikan lingkungan container lebih stabil, aman (HTTPS), dan mudah direplikasi oleh seluruh anggota tim, tanpa memerlukan setup manual di mesin lokal.
