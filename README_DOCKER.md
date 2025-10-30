## Menjalankan Proyek dengan Docker

Dokumen ini menjelaskan cara menjalankan proyek ini menggunakan Docker (tanpa perlu memasang PHP, Composer, atau Node.js secara lokal). Image sudah menyiapkan vendor dan build aset frontend melalui multi-stage build di Dockerfile.

### Prasyarat
- Docker dan Docker Compose v2 terpasang
- Port `8000` kosong di mesin Anda

### Ringkas (jika sudah paham)
1) Buat file `.env` minimal (lihat contoh di bawah) dan file database SQLite:
```bash
mkdir -p database && touch database/database.sqlite
```
2) Jalankan dengan Docker Compose (gunakan override di bawah ini):
```bash
docker compose up -d --build
```
3) Inisialisasi aplikasi (sekali saat pertama):
```bash
docker compose exec app php artisan key:generate
```
4) Migrasi + seeding (opsional, sesuai kebutuhan data awal):
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
Akses aplikasi: `http://localhost:8000`

---

### Detail Langkah

#### 1) Siapkan environment (.env)
Jika proyek belum memiliki `.env`, gunakan contoh minimal untuk SQLite di bawah ini. Simpan sebagai file `.env` di root proyek.

```dotenv
APP_NAME=SIMPPEL
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_LEVEL=debug

# Gunakan SQLite sesuai Dockerfile
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

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

Buat file database SQLite (jika belum ada):
```bash
mkdir -p database && touch database/database.sqlite
```

#### 2) Jalankan dengan Docker Compose (disarankan)
Repo ini sudah punya `docker-compose.yml` dasar. Tambahkan file `docker-compose.override.yml` berikut agar port dan volume terpasang dengan benar (Compose akan menggabungkannya otomatis):

```yaml
services:
  app:
    ports:
      - "8000:8000"
    volumes:
      - ./.env:/var/www/html/.env:ro
      - ./database/database.sqlite:/var/www/html/database/database.sqlite:rw
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

#### 3) Alternatif: Jalankan tanpa Compose
Jika tidak ingin membuat override Compose, Anda bisa langsung build dan run:
```bash
docker build -t simppe-spd:latest .
```
```bash
docker run -d \
  --name simppe-spd \
  -p 8000:8000 \
  -v "$(pwd)/.env:/var/www/html/.env:ro" \
  -v "$(pwd)/database/database.sqlite:/var/www/html/database/database.sqlite:rw" \
  simppe-spd:latest
```
Inisialisasi (sekali):
```bash
docker exec -it simppe-spd php artisan key:generate
```
Migrasi + seeding (opsional):
```bash
docker exec -it simppe-spd php artisan migrate
# Jalankan seeder sesuai kebutuhan
```

### Menggunakan MySQL sebagai Database
Anda bisa memakai MySQL alih-alih SQLite. Tambahkan service MySQL pada Compose dan ubah konfigurasi `.env`.

1) Ubah `.env` untuk MySQL:
```dotenv
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=simppe
DB_USERNAME=simppe
DB_PASSWORD=secret
```

2) Gunakan `docker-compose.override.yml` dengan service MySQL (gantikan override sebelumnya jika memakai SQLite):
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

3) Jalankan layanan dan inisialisasi:
```bash
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
# opsional: seeding
docker compose exec app php artisan db:seed --class=AdminSeeder
```

Catatan:
- Pastikan variabel `.env` sesuai dengan nilai di Compose (`DB_HOST=mysql`).
- Hilangkan volume SQLite dari override jika beralih ke MySQL.

### Catatan
- Image final menjalankan `php artisan serve --host=0.0.0.0` pada port `8000`.
- Build aset frontend dilakukan di dalam Dockerfile; Anda tidak perlu menjalankan `npm/pnpm` di host.
- Menggunakan SQLite memudahkan setup lokal. Untuk MySQL/pgsql, sesuaikan `.env` dan tambahkan service DB pada Compose (lihat bagian MySQL di atas).

### Troubleshooting
- Port 8000 sudah dipakai: ubah mapping ke `"8080:8000"` pada Compose/run, lalu akses `http://localhost:8080`.
- Permission SQLite: pastikan file `database/database.sqlite` ada dan bisa ditulis. Jika perlu di Linux/Mac: `chmod 664 database/database.sqlite` atau `chown $USER database/database.sqlite`.
- Perubahan .env tidak terbaca: restart container `docker compose restart app`.
 - Error koneksi DB (SQLSTATE[HY000] [2002]): pastikan service `mysql` healthy, lalu ulangi `php artisan migrate`.

### Menjalankan Test (Lokal)
Anda bisa menjalankan test baik melalui Docker maupun langsung di host (jika Composer/PHP terpasang).

1) Siapkan environment testing dengan `.env.testing` (disarankan SQLite in-memory agar cepat):
```dotenv
APP_ENV=testing
APP_DEBUG=true

DB_CONNECTION=sqlite
DB_DATABASE=:memory:

CACHE_STORE=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
```

2) Jalankan test dengan Docker (disarankan):
```bash
docker compose exec app php artisan test
# atau langsung phpunit
docker compose exec app ./vendor/bin/phpunit
```

3) Alternatif (tanpa Docker, di host):
```bash
composer install
php -v # pastikan PHP versi kompatibel Laravel
cp .env .env.testing # lalu edit seperti di atas
php artisan key:generate --env=testing
php artisan test
# atau
./vendor/bin/phpunit
```

4) Menjalankan subset test:
```bash
# hanya Unit
docker compose exec app php artisan test --testsuite=Unit
# hanya Feature
docker compose exec app php artisan test --testsuite=Feature
```

Opsional: Testing memakai MySQL
- Buat DB khusus testing dan konfigurasi di `.env.testing`:
```dotenv
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=simppe_test
DB_USERNAME=simppe
DB_PASSWORD=secret
```
```bash
# buat database test (sekali)
docker compose exec mysql mysql -uroot -prootsecret -e "CREATE DATABASE IF NOT EXISTS simppe_test;"
# jalankan migrasi untuk environment testing
docker compose exec app php artisan migrate --env=testing
# jalankan test
docker compose exec app php artisan test
```
