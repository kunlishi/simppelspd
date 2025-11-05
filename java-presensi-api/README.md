# Presensi & Izin API (Spring Boot)

Sistem presensi apel mahasiswa dan pengajuan izin/sakit.

## Fitur
- Manajemen pengguna: registrasi, login, profil, edit profil, ganti password, hapus akun
- Presensi via scan NIM (oleh SPD/Admin)
- Pengajuan izin/sakit (mahasiswa) dengan upload bukti, review (admin)
- JWT auth, role-based (STUDENT, SPD, ADMIN)
- OpenAPI/Swagger UI di `/swagger-ui.html`

## Jalankan dengan Docker
```bash
cd java-presensi-api
docker build -t presensi-api .
docker run --rm -p 8080:8080 -v $PWD/uploads:/app/uploads -v $PWD/data:/app/data presensi-api
```

## Struktur Endpoint (ringkas)
- POST `/api/auth/register-student|register-spd|register-admin`
- POST `/api/auth/login`
- GET `/api/user/me`, PUT `/api/user/me`, POST `/api/user/change-password`, DELETE `/api/user/me`
- POST `/api/attendance/scan` (SPD/Admin)
- GET `/api/attendance/my` (Mahasiswa)
- POST `/api/permit/create` (Mahasiswa, multipart)
- GET `/api/permit/my` (Mahasiswa)
- GET `/api/permit/pending` (Admin)
- POST `/api/permit/{id}/review?approve=true|false` (Admin)
- GET `/api/permit/evidence/{id}` (Owner/Admin)

## Klien Sederhana
Buka file HTML di folder `clients/` (student.html, spd.html, admin.html). Pastikan API berjalan di `http://localhost:8080` (ubah `clients/common.js` jika berbeda).

## Konfigurasi
- `application.yml` memuat `app.jwt.secret` dan lokasi upload `app.uploadDir`.
- Default database: H2 file `./data/presensi-db`.

## Catatan
- Endpoint admin register sementara dibuka untuk demo. Dalam produksi, batasi dengan seeding/admin awal.
- Validasi akses file bukti minimal (owner/admin).
```
