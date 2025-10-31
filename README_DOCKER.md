## Menjalankan Proyek dengan Docker

Dokumen ini menjelaskan cara menjalankan proyek ini menggunakan Docker (tanpa perlu memasang PHP, Composer, atau Node.js secara lokal). Image sudah menyiapkan vendor dan build aset frontend melalui multi-stage build di Dockerfile.

### Prasyarat
- Docker dan Docker Compose v2 terpasang
- Port `8000` kosong di mesin Anda

### Ringkas (jika sudah paham)
1) Buat `.env` (lihat contoh MySQL di bawah) dan `docker-compose.override.yml` yang menambahkan service MySQL.
2) Jalankan:
```bash
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
# opsional: seeding
docker compose exec app php artisan db:seed --class=AdminSeeder
```
Akses aplikasi: `http://localhost:8000`.

---

### Detail Langkah

#### 1) Siapkan environment (.env)
Jika proyek belum memiliki `.env`, gunakan contoh minimal MySQL di bawah ini. Simpan sebagai file `.env` di root proyek.

```dotenv
APP_NAME=SIMPPEL
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=simppe
DB_USERNAME=simppe
DB_PASSWORD=secret

CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Sesuaikan jika diperlukan
# MAIL_MAILER=smtp
# MAIL_HOST=mailhog
# MAIL_PORT=1025
# MAIL_FROM_ADDRESS="noreply@example.com"
# MAIL_FROM_NAME="SIMPPEL"
```

#### 2) Jalankan dengan Docker Compose (disarankan)
Repo ini sudah punya `docker-compose.yml` dasar. Tambahkan file `docker-compose.override.yml` berikut agar port dan service MySQL terpasang (Compose akan menggabungkannya otomatis):

```yaml
services:
  app:
    ports:
      - "8000:8000"
    volumes:
      - ./.env:/var/www/html/.env:ro
    depends_on:
      mysql:
        condition: service_healthy

  mysql:
    image: mysql:8.0
    command: ["--default-authentication-plugin=mysql_native_password"]
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: simppe
      MYSQL_USER: simppe
      MYSQL_PASSWORD: secret
      MYSQL_ROOT_PASSWORD: rootsecret
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "127.0.0.1", "-uroot", "-prootsecret"]
      interval: 5s
      timeout: 5s
      retries: 20
    ports:
      - "3306:3306"  # opsional, jika butuh akses dari host
    volumes:
      - mysql-data:/var/lib/mysql

volumes:
  mysql-data:
```

Lalu build dan jalankan:
```bash
docker compose up -d --build
```

Inisialisasi aplikasi (sekali saat pertama):
```bash
docker compose exec app php artisan key:generate
```

Migrasi dan seeding data (opsional, sesuai kebutuhan):
```bash
docker compose exec app php artisan migrate
# Pilih seeder yang diperlukan:
docker compose exec app php artisan db:seed --class=AdminSeeder
docker compose exec app php artisan db:seed --class=AutentikasiSeeder
docker compose exec app php artisan db:seed --class=DummyFaqSeeder
docker compose exec app php artisan db:seed --class=DummyPeraturanSeeder
docker compose exec app php artisan db:seed --class=DummySeederMahasiswa
docker compose exec app php artisan db:seed --class=PelanggaranSeeder
docker compose exec app php artisan db:seed --class=PemonitorSeeder
docker compose exec app php artisan db:seed --class=SPDSeeder
```

Buka `http://localhost:8000`.

Perintah berguna:
- Lihat log: `docker compose logs -f app`
- Stop: `docker compose down`
- Rebuild setelah ada perubahan: `docker compose up -d --build`

#### 3) Jalankan layanan dan inisialisasi
```bash
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
# opsional: seeding
docker compose exec app php artisan db:seed --class=AdminSeeder
```

### Catatan
- Image final menjalankan `php artisan serve --host=0.0.0.0` pada port `8000`.
- Build aset frontend dilakukan di dalam Dockerfile; Anda tidak perlu menjalankan `npm/pnpm` di host.
- Gunakan MySQL sesuai konfigurasi pada `.env` dan `docker-compose.override.yml`.

### Troubleshooting
- Port 8000 sudah dipakai: ubah mapping ke `"8080:8000"` pada Compose/run, lalu akses `http://localhost:8080`.
- Perubahan .env tidak terbaca: restart container `docker compose restart app`.
- Error koneksi DB (SQLSTATE[HY000] [2002]): pastikan service `mysql` healthy, lalu ulangi `php artisan migrate`.

### Menjalankan Test (Lokal)
Anda bisa menjalankan test melalui Docker maupun langsung di host (jika Composer/PHP terpasang) dengan MySQL.

1) Siapkan `.env.testing` untuk MySQL:
```dotenv
APP_ENV=testing
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=simppe_test
DB_USERNAME=simppe
DB_PASSWORD=secret

CACHE_STORE=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
```

2) Buat database test (sekali):
```bash
docker compose exec mysql mysql -uroot -prootsecret -e "CREATE DATABASE IF NOT EXISTS simppe_test;"
```

3) Jalankan migrasi dan test (Docker):
```bash
docker compose exec app php artisan migrate --env=testing
docker compose exec app php artisan test
# atau langsung phpunit
docker compose exec app ./vendor/bin/phpunit
```

4) Alternatif (tanpa Docker, di host):
```bash
composer install
cp .env .env.testing # lalu sesuaikan ke MySQL lokal/Compose
php artisan key:generate --env=testing
php artisan migrate --env=testing
php artisan test
# atau
./vendor/bin/phpunit
```

5) Menjalankan subset test:
```bash
# hanya Unit
docker compose exec app php artisan test --testsuite=Unit
# hanya Feature
docker compose exec app php artisan test --testsuite=Feature
```
