## Dokumen Terkait
* [Laporan Milestone 4]()
* [Buku Manual Penggunaan]()
* [Dokumen Alih Hak Sistem](https://drive.google.com/file/d/1Uq9osOrnu0u0-f1HPT-GhERo1FsqVoxY/view?usp=sharing)
* [Dokumen Berita Acara](https://drive.google.com/file/d/1S7ScD8-jKJmJBfhT95wvIagEBLFV8eBc/view?usp=sharing)

## Penggunaan Repositori
Unduh repository ke dalam komputer menggunakan perintah `git clone`. Url
repository dapat dilihat di dalam repository yang diinginkan.

```
git clone <url repository> <folder tujuan>
```

Pastikan pengguna memiliki Laragon untuk mengakses website secara lokal
Jalankan beberapa perintah berikut pada terminal Laragon yang direktorinya sudah mengarah ke folder hasil clone
```
composer install
npm install
npm run build
php artisan key:generate
```
Lakukan pula seeding untuk database awal dengan menjalankan perinta berikut pada terminal
```
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=AutentikasiSeeder
php artisan db:seed --class=DummyFaqSeeder
php artisan db:seed --class=DummyPeraturanSeeder
php artisan db:seed --class=DummySeederMahasiswa
php artisan db:seed --class=PelanggaranSeeder
php artisan db:seed --class=PemonitorSeeder
php artisan db:seed --class=SPDSeeder
```

Pada terminal baru jalankan
```
php artisan queue:work

```

## Struktur Proyek
### Folder app
Struktur folder app digunakan untuk mengelola logika aplikasi. Folder ini terdiri dari banyak-banyak folder di dalamnya dengan fungsi masing-masing. Berikut merupakan penjelasan singkat folder di dalamnya.
* [Exports]: Berisi logika untuk mengekspor data pelaporan pelanggaran, baik dalam format csv, excel, maupun pdf. 
* [Http]
   - Controllers: Berisi logika untuk mengontrol request dan response.
   - Middleware: Mengelola middle ware dan akses.
* [Model]: Berisi logika untuk mengatur model yang mewakili database.
* [View] : Berisi komponen blade yang mengatur komponen UI yang dapat digunakan kembali.
```
📦app
 ┣ 📂Exports
 ┃ ┣ 📜OperasiRutinExport.php
 ┃ ┣ 📜OperasiUmumExport.php
 ┃ ┗ 📜PenindakanHarianExport.php
 ┣ 📂Http
 ┃ ┣ 📂Controllers
 ┃ ┃ ┣ 📂Api
 ┃ ┃ ┃ ┗ 📜PelanggaranController.php
 ┃ ┃ ┣ 📜Controller.php
 ┃ ┃ ┣ 📜DashboardController.php
 ┃ ┃ ┣ 📜EmailController.php
 ┃ ┃ ┣ 📜FaqController.php
 ┃ ┃ ┣ 📜KlaimPelanggaranController.php
 ┃ ┃ ┣ 📜KritikSaranController.php
 ┃ ┃ ┣ 📜MahasiswaController.php
 ┃ ┃ ┣ 📜OperasiRutinController.php
 ┃ ┃ ┣ 📜OperasiUmumController.php
 ┃ ┃ ┣ 📜PelanggaranController.php
 ┃ ┃ ┣ 📜PemonitorController.php
 ┃ ┃ ┣ 📜PenindakanHarianController.php
 ┃ ┃ ┣ 📜PeraturanController.php
 ┃ ┃ ┣ 📜SesiController.php
 ┃ ┃ ┣ 📜SPDController.php
 ┃ ┃ ┣ 📜TokenController.php
 ┃ ┃ ┗ 📜UbahPasswordController.php
 ┃ ┗ 📂Middleware
 ┃ ┃ ┣ 📜admin.php
 ┃ ┃ ┣ 📜laporan.php
 ┃ ┃ ┗ 📜spd.php
 ┣ 📂Models
 ┃ ┣ 📜Admin.php
 ┃ ┣ 📜Autentikasi.php
 ┃ ┣ 📜faq.php
 ┃ ┣ 📜KritikSaran.php
 ┃ ┣ 📜Mahasiswa.php
 ┃ ┣ 📜OperasiRutin.php
 ┃ ┣ 📜OperasiUmum.php
 ┃ ┣ 📜Pelanggaran.php
 ┃ ┣ 📜Pemonitor.php
 ┃ ┣ 📜PenindakanHarian.php
 ┃ ┣ 📜peraturan.php
 ┃ ┣ 📜SPD.php
 ┃ ┗ 📜Token.php
 ┣ 📂Providers
 ┃ ┗ 📜AppServiceProvider.php
 ┗ 📂View
 ┃ ┗ 📂Components
 ┃ ┃ ┣ 📜Footer.php
 ┃ ┃ ┣ 📜Header.php
 ┃ ┃ ┣ 📜Layout.php
 ┃ ┃ ┗ 📜Sidebar.php
 ```
### Folder database
Struktur folder database digunakan untuk mengelola logika database aplikasi. Folder ini terdiri dari banyak-banyak folder di dalamnya dengan fungsi masing-masing. Berikut merupakan penjelasan singkat folder di dalamnya.
* [Factories]: Digunakan untuk membuat data dummy dengan model factory, berguna untuk pengujian dan pengisian awal database. 
* [Migrations] : Berisi file migration untuk mendefinisikan struktur tabel database.
* [Seeders]: Digunakan untuk mengisi database dengan data awal atau dummy secara otomatis.
 ```
📦database
 ┣ 📂factories
 ┃ ┗ 📜UserFactory.php
 ┣ 📂migrations
 ┃ ┣ 📜0001_01_01_000000_create_users_table.php
 ┃ ┣ 📜0001_01_01_000001_create_cache_table.php
 ┃ ┣ 📜0001_01_01_000002_create_jobs_table.php
 ┃ ┣ 📜2024_12_14_034642_create_operasi_rutin_table.php
 ┃ ┣ 📜2024_12_15_035511_create_operasi_umums_table.php
 ┃ ┣ 📜2024_12_27_015921_create_faq_table.php
 ┃ ┣ 📜2024_12_27_020036_create_peraturan_table.php
 ┃ ┣ 📜2024_12_27_041523_create_kritiksaran_table.php
 ┃ ┣ 📜2024_12_29_050626_create_tokens_table.php
 ┃ ┣ 📜2024_12_29_131735_create_pelanggarans_table.php
 ┃ ┣ 📜2024_12_31_060632_create_penindakan_harian.php
 ┃ ┣ 📜2025_01_03_155114_create_klaim_pelanggarans_table.php
 ┃ ┣ 📜2025_01_11_113038_create_mahasiswas_table.php
 ┃ ┣ 📜2025_01_26_200936_create_s_p_d_s_table.php
 ┃ ┣ 📜2025_01_26_201109_create_admins_table.php
 ┃ ┗ 📜2025_01_26_201326_create_pemonitors_table.php
 ┣ 📂seeders
 ┃ ┣ 📜AdminSeeder.php
 ┃ ┣ 📜AutentikasiSeeder.php
 ┃ ┣ 📜DatabaseSeeder.php
 ┃ ┣ 📜DummyFaqSeeder.php
 ┃ ┣ 📜DummyPeraturanSeeder.php
 ┃ ┣ 📜DummySeederMahasiswa.php
 ┃ ┣ 📜DummyUserSeeder.php
 ┃ ┣ 📜PelanggaranSeeder.php
 ┃ ┣ 📜PemonitorSeeder.php
 ┃ ┗ 📜SPDSeeder.php
 ┗ 📜.gitignore
 ```
### Folder public
Struktur folder public merupakan folder utama yang berisi aset publik yang bisa diakses langsung oleh pengguna. Folder ini terdiri dari banyak-banyak folder di dalamnya dengan fungsi masing-masing. Berikut merupakan penjelasan singkat folder di dalamnya.
* [Build]: Berisi file hasil build dari aplikasi. 
* [Css] : Berisi Folder untuk file CSS yang digunakan dalam tampilan website.
* [Img]: Folder yang menyimpan berbagai gambar yang digunakan dalam website.
 ```
📦public
 ┣ 📂build
 ┃ ┣ 📂assets
 ┃ ┃ ┣ 📜app-CCB23rUt.css
 ┃ ┃ ┗ 📜app-DEdkgso6.js
 ┃ ┗ 📜manifest.json
 ┣ 📂css
 ┃ ┣ 📜select2.min.css
 ┃ ┗ 📜style.css
 ┣ 📂img
 ┃ ┣ 📂galeri
 ┃ ┃ ┣ 📜image (1).jpg
 ┃ ┃ ┣ 📜image (10).jpg
 ┃ ┃ ┣ 📜image (11).jpg
 ┃ ┃ ┣ 📜image (12).jpg
 ┃ ┃ ┣ 📜image (2).jpg
 ┃ ┃ ┣ 📜image (3).jpg
 ┃ ┃ ┣ 📜image (4).jpg
 ┃ ┃ ┣ 📜image (5).jpg
 ┃ ┃ ┣ 📜image (6).jpg
 ┃ ┃ ┣ 📜image (7).jpg
 ┃ ┃ ┣ 📜image (8).JPG
 ┃ ┃ ┗ 📜image (9).jpg
 ┃ ┣ 📂ikonurusan
 ┃ ┃ ┣ 📜Ikon AKLH.png
 ┃ ┃ ┣ 📜Ikon AOPP.png
 ┃ ┃ ┣ 📜Ikon DIKPER.png
 ┃ ┃ ┗ 📜Ikon PROK.png
 ┃ ┣ 📜banner1.jpg
 ┃ ┣ 📜banner2.jpg
 ┃ ┣ 📜logo.png
 ┃ ┣ 📜logospd.png
 ┃ ┗ 📜logostis.png
 ┣ 📜.htaccess
 ┣ 📜favicon.ico
 ┣ 📜index.php
 ┗ 📜robots.txt
 ```
### Folder resources
Struktur folder resources menunjukkan bahwa proyek yang dikembangkan menggunakan laravel. Folder ini meyimpan sumber daya yang digunakan dalam aplikasi web seperti view (tampilan), css, javascript, dan data. Folder ini terdiri dari banyak-banyak folder di dalamnya dengan fungsi masing-masing. Berikut merupakan penjelasan singkat folder di dalamnya.
* [Css]: Berisi file CSS utama yang digunakan untuk styling halaman web. 
* [Data] : Menyimpan data dalam format php.
* [Js]: Berisi file JavaScript.
* [Views]: Tempat penyimpanan template tampilan aplikasi menggunakan Blade (.blade.php), templating engine bawaan Laravel.
 ```
 📦resources
 ┣ 📂css
 ┃ ┗ 📜app.css
 ┣ 📂data
 ┃ ┗ 📜faq.php
 ┣ 📂js
 ┃ ┣ 📜app.js
 ┃ ┗ 📜bootstrap.js
 ┗ 📂views
 ┃ ┣ 📂admin
 ┃ ┃ ┣ 📂faq
 ┃ ┃ ┃ ┣ 📜edit.blade.php
 ┃ ┃ ┃ ┣ 📜tambah.blade.php
 ┃ ┃ ┃ ┗ 📜tampil.blade.php
 ┃ ┃ ┗ 📂peraturan
 ┃ ┃ ┃ ┣ 📜edit.blade.php
 ┃ ┃ ┃ ┣ 📜tambah.blade.php
 ┃ ┃ ┃ ┗ 📜tampil.blade.php
 ┃ ┣ 📂components
 ┃ ┃ ┣ 📜faq-button.blade.php
 ┃ ┃ ┣ 📜footer.blade.php
 ┃ ┃ ┣ 📜header.blade.php
 ┃ ┃ ┣ 📜layout.blade.php
 ┃ ┃ ┣ 📜sidebar.blade.php
 ┃ ┃ ┣ 📜sidebarcomp.blade.php
 ┃ ┃ ┣ 📜sidebarlink.blade.php
 ┃ ┃ ┗ 📜tabellapor.blade.php
 ┃ ┣ 📂emails
 ┃ ┃ ┣ 📜pelanggaran.blade.php
 ┃ ┃ ┗ 📜update.blade.php
 ┃ ┣ 📂exports
 ┃ ┃ ┣ 📜laporanharian_pdf.blade.php
 ┃ ┃ ┣ 📜laporanrutin_pdf.blade.php
 ┃ ┃ ┗ 📜laporanumum_pdf.blade.php
 ┃ ┣ 📂operasirutin
 ┃ ┃ ┣ 📜catat.blade.php
 ┃ ┃ ┣ 📜catatrutinpilih.blade.php
 ┃ ┃ ┣ 📜editcatatrutin.blade.php
 ┃ ┃ ┗ 📜laporanrutin.blade.php
 ┃ ┣ 📂operasiumum
 ┃ ┃ ┣ 📜catatumum.blade.php
 ┃ ┃ ┣ 📜editcatatumum.blade.php
 ┃ ┃ ┗ 📜laporanumum.blade.php
 ┃ ┣ 📂penindakanharian
 ┃ ┃ ┣ 📜catatharian.blade.php
 ┃ ┃ ┗ 📜laporanharian.blade.php
 ┃ ┣ 📜buat-token.blade.php
 ┃ ┣ 📜dashboard.blade.php
 ┃ ┣ 📜faq.blade.php
 ┃ ┣ 📜input-token.blade.php
 ┃ ┣ 📜klaim-pelanggaran.blade.php
 ┃ ┣ 📜kritiksaran.blade.php
 ┃ ┣ 📜landingpage.blade.php
 ┃ ┣ 📜login.blade.php
 ┃ ┣ 📜peraturan.blade.php
 ┃ ┣ 📜ubahpassword.blade.php
 ┃ ┗ 📜viewkritiksaran.blade.php
 ```
### Folder routes
Struktur folder routes berisi file-file yang mendefinisikan rute atau jalur akses untuk aplikasi web. Setiap file dalam folder ini memiliki fungsi spesifik dalam menangani permintaan (request) dari pengguna. Folder ini terdiri dari banyak-banyak folder di dalamnya dengan fungsi masing-masing. Berikut merupakan penjelasan singkat folder di dalamnya.
* [Api]: Berisi rute untuk API yang digunakan untuk menangani permintaan berbasis API, seperti permintaan dari aplikasi mobile atau frontend berbasis JavaScript yang menggunakan AJAX.
* [Web] : Berisi rute untuk aplikasi berbasis web yang menggunakan Blade template atau tampilan UI lainnya. File ini digunakan untuk mendefinisikan halaman dan fungsionalitas dalam aplikasi Laravel.
 ```
 📦routes
 ┣ 📜api.php
 ┣ 📜console.php
 ┗ 📜web.php
 ```
