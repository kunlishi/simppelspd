# Attendance & Permit Service (Spring Boot)

Sistem presensi apel mahasiswa dan pengajuan izin/saKit dengan autentikasi JWT dan dokumentasi API.

## Fitur
- Manajemen pengguna: registrasi, login, profil, edit profil, ganti password, hapus akun
- Presensi: petugas men-scan NIM (via input), tandai hadir per tanggal
- Perizinan: mahasiswa ajukan izin/sakit (unggah bukti foto), admin setujui/tolak
- Auth token-based (JWT) untuk semua endpoint selain login/registrasi
- Dokumentasi API via Swagger UI
- Frontend sederhana HTML+JS murni

## Menjalankan
1. Pastikan Java 17 dan Maven terpasang
2. Jalankan:
   ```bash
   mvn spring-boot:run -f attendance-service/pom.xml
   ```
3. Akses:
   - Halaman demo: `http://localhost:8081/`
   - Swagger UI: `http://localhost:8081/swagger-ui/index.html`
   - H2 Console: `http://localhost:8081/h2-console` (jdbc url: `jdbc:h2:mem:attendancedb`)

## Akun Awal (DataInitializer)
- Admin: `admin` / `admin123`
- Petugas: `petugas` / `petugas123`
- Mahasiswa contoh: `23000001` / `student123`

## API Utama
- `POST /api/auth/register` — registrasi mahasiswa
- `POST /api/auth/login` — login, balas token JWT
- `GET /api/user/me` — profil saat ini
- `PUT /api/user/profile` — update profil
- `POST /api/user/change-password` — ganti password
- `DELETE /api/user/delete` — hapus akun
- `POST /api/attendance/scan/{nim}` — tandai hadir (role PETUGAS/ADMIN)
- `GET /api/attendance/me` — riwayat presensi saya
- `POST /api/permits` (multipart) — ajukan izin/sakit dgn bukti
- `GET /api/permits` — daftar pengajuan saya
- `GET /api/permits/pending` — daftar pending (ADMIN)
- `POST /api/permits/{id}/approve|reject` — setujui/tolak (ADMIN)

## Catatan
- Kunci JWT berada di `application.yml` (`app.jwt.secret`). Ubah ke string panjang acak.
- Upload disimpan pada folder `uploads/` (dibuat otomatis). Endpoint bukti: `GET /api/permits/proof/{filename}`.
