## Menjalankan Proyek dengan Docker

Dokumen ini merangkum cara men-deploy stack lengkap (PHP-FPM + Nginx + MySQL + phpMyAdmin) dengan SSL otomatis menggunakan Docker Compose.

### Prasyarat
- Docker Desktop / Docker Engine dan Docker Compose v2 terpasang
- Port berikut bebas di host (dapat diubah dengan variabel environment):
  - `APP_HTTP_PORT` (default 8080)
  - `APP_HTTPS_PORT` (default 8443)
  - `PHPMYADMIN_PORT` (default 8088)
  - `FORWARD_DB_PORT` (default 33060)

### Langkah Cepat
```bash
cp .env.example .env            # sesuaikan bila perlu
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

- Aplikasi: `https://localhost:8443`
- phpMyAdmin: `http://localhost:8088` (user `laravel`, password `secret`)
- MySQL (opsional dari host): `127.0.0.1:33060`

### Penjelasan Detail

#### 1. Konfigurasi `.env`
File `.env.example` sudah disiapkan dengan konfigurasi MySQL & port default. Jika ingin override port atau kredensial, ubah variabel berikut:

```dotenv
APP_HTTP_PORT=8080
APP_HTTPS_PORT=8443
FORWARD_DB_PORT=33060
PHPMYADMIN_PORT=8088

DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

SSL_CERT_CN=localhost
SSL_CERT_DAYS=365
```

Jika Anda ingin menggunakan domain lokal berbeda (mis. `simppelspd.local`), ubah `SSL_CERT_CN` dan tambahkan entri ke `hosts` di OS Anda.

#### 2. Menjalankan Stack
```bash
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

Perintah penting lainnya:
- Restart service tertentu: `docker compose restart web`
- Melihat log: `docker compose logs -f web` atau `app`
- Menghentikan semua layanan: `docker compose down`
- Menghapus volume (data MySQL hilang): `docker compose down -v`

#### 3. SSL & Sertifikat
- Kontainer Nginx (`web`) otomatis membuat sertifikat self-signed di `/etc/nginx/ssl`.
- Untuk menyalin sertifikat ke host (supaya bisa ditambahkan ke trust store):
  ```bash
  docker compose cp web:/etc/nginx/ssl/server.crt ./docker/nginx/server.crt
  docker compose cp web:/etc/nginx/ssl/server.key ./docker/nginx/server.key
  ```
- Untuk memakai sertifikat sendiri:
  ```bash
  mkdir -p docker/nginx/ssl
  cp <path-ke-sertifikat-anda>/server.crt docker/nginx/ssl/
  cp <path-ke-sertifikat-anda>/server.key docker/nginx/ssl/
  docker compose up -d --force-recreate web
  ```
  Volume tersebut akan menimpa sertifikat bawaan ketika kontainer berjalan.

#### 4. phpMyAdmin
- Akses melalui `http://localhost:8088`
- Kredensial mengikuti variabel `DB_USERNAME` dan `DB_PASSWORD`
- Jika port bentrok, ubah `PHPMYADMIN_PORT` di `.env` lalu jalankan `docker compose up -d --force-recreate phpmyadmin`

#### 5. Tips Migrasi & Seeding
- Migrasi standar: `docker compose exec app php artisan migrate`
- Seeders individual:
  ```bash
  docker compose exec app php artisan db:seed --class=AdminSeeder
  docker compose exec app php artisan db:seed --class=AutentikasiSeeder
  # ... dst
  ```

#### 6. Pengujian
- Jalankan semua test: `docker compose exec app php artisan test`
- Jalankan PHPUnit langsung: `docker compose exec app ./vendor/bin/phpunit`
- Gunakan `.env.testing` dengan koneksi MySQL bila diperlukan (lihat README utama untuk contoh konfigurasi).

#### 7. Troubleshooting
- **Sertifikat tidak dipercaya**: tambahkan pengecualian di browser atau trust `server.crt` yang diekspor dari kontainer.
- **Port bentrok**: ubah variabel port di `.env`, lalu `docker compose up -d --force-recreate` pada service terkait.
- **phpMyAdmin gagal start (port sudah dipakai)**: pastikan variabel `PHPMYADMIN_PORT` tidak conflict, kemudian recreate.
- **Gagal migrasi (MySQL belum ready)**: tunggu healthcheck MySQL `healthy`, lalu ulangi perintah migrasi.

---

Dengan mengikuti panduan ini, seluruh stack dapat dijalankan tanpa perlu memasang PHP/Node.js lokal, sekaligus menyediakan akses HTTPS dan phpMyAdmin secara otomatis.
