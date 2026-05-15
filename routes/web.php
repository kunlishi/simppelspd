<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\SesiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PeraturanController;
use App\Http\Controllers\KritikSaranController;
use App\Http\Controllers\OperasiUmumController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\OperasiRutinController;
use App\Http\Controllers\UbahPasswordController;
use App\Http\Controllers\KlaimPelanggaranController;
use App\Http\Controllers\PenindakanHarianController;
use App\Http\Controllers\ApelController;
use App\Http\Controllers\PresensiController;
use Illuminate\Support\Facades\Auth;


// ==========================================
// RUTE PUBLIK (GUEST / SEBELUM LOGIN)
// ==========================================

// Halaman awal
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return view('landingpage');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [SesiController::class, 'index'])->name('login');
    Route::post('/login', [SesiController::class, 'login']);
    Route::get('/kirim-kritik-saran', [KritikSaranController::class, 'index'])->name('kritiksaran');
    Route::post('/kirim-kritik-saran/submit', [KritikSaranController::class, 'submit'])->name('kritiksaran.submit');
    Route::get('/peraturan', [PeraturanController::class, 'tampil'])->name('tampil.peraturan');
});

// Melihat FAQ bisa oleh semua role (termasuk guest)
Route::get('/faq', [FaqController::class, 'index']);


// ==========================================
// RUTE GLOBAL (UNTUK SEMUA USER YANG LOGIN)
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/logout', [SesiController::class, 'logout']);
    
    // Ubah Password
    Route::get('/ubah-password', [UbahPasswordController::class, 'index']);
    Route::post('/ubah-password', [UbahPasswordController::class, 'ubahPassword']);
    
    // API/Data Fetching
    Route::get('/dashboard/data', [DashboardController::class, 'StatDesk']);
    Route::get('/get-mahasiswa', [MahasiswaController::class, 'getMahasiswa'])->name('get.mahasiswa');
    Route::get('/get-mahasiswa-rutin', [MahasiswaController::class, 'getMahasiswaRutin'])->name('get.mahasiswarutin');
    
    // Fitur Pelanggaran Global
    Route::get('pelanggaran', [PelanggaranController::class, 'index'])->name('pelanggaran.index');
    Route::get('pelanggaran/edit', [PelanggaranController::class, 'munculedit'])->name('pelanggaran.edit');

    // Fitur download laporan presensi
    Route::get('/presensi/download/{format}', [PresensiController::class, 'downloadFilteredData'])->name('presensi.download');
});


// ==========================================
// RUTE KHUSUS ADMIN
// ==========================================
Route::middleware(['auth', 'admin'])->group(function () {
    // Manage Token
    Route::get('/buat-token', [TokenController::class, 'index']);
    Route::get('/generate-token', [TokenController::class, 'generateToken']);

    // Manage FAQ
    Route::get('/admin-faq', [FaqController::class, 'admin_tampil'])->name('admin.tampil.faq');
    Route::get('/admin-faq/tambah', [FaqController::class, 'admin_tambah'])->name('admin.tambah.faq');
    Route::post('/admin-faq/submit', [FaqController::class, 'admin_submit'])->name('admin.submit.faq');
    Route::get('/admin-faq/edit/{id}', [FaqController::class, 'admin_edit'])->name('admin.edit.faq');
    Route::post('/admin-faq/update/{id}', [FaqController::class, 'admin_update'])->name('admin.update.faq');
    Route::delete('/admin-faq/delete/{id}', [FaqController::class, 'admin_delete'])->name('admin.delete.faq');

    // Manage Peraturan
    Route::get('/admin-peraturan', [PeraturanController::class, 'admin_tampil'])->name('admin.tampil.peraturan');
    Route::get('/admin-peraturan/tambah', [PeraturanController::class, 'admin_tambah'])->name('admin.tambah.peraturan');
    Route::post('/admin-peraturan/submit', [PeraturanController::class, 'admin_submit'])->name('admin.submit.peraturan');
    Route::get('/admin-peraturan/edit/{id}', [PeraturanController::class, 'admin_edit'])->name('admin.edit.peraturan');
    Route::post('/admin-peraturan/update/{id}', [PeraturanController::class, 'admin_update'])->name('admin.update.peraturan');
    Route::delete('/admin-peraturan/delete/{id}', [PeraturanController::class, 'admin_delete'])->name('admin.delete.peraturan');

    // Manage Apel (Fase 1: Lazy Insertion)
    Route::get('/daftar-apel', [ApelController::class, 'index'])->name('apel.index');
    Route::get('/apel-baru', [ApelController::class, 'create'])->name('apel.create');
    Route::post('/apel-baru', [ApelController::class, 'store'])->name('apel.store');
    Route::get('/apel/{id}/edit', [ApelController::class, 'edit'])->name('apel.edit');
    Route::put('/apel/{id}/update', [ApelController::class, 'update'])->name('apel.update');
    Route::delete('/apel/{id}/delete', [ApelController::class, 'destroy'])->name('apel.delete');

    // Laporan Presensi Admin (Fase 3: Dynamic Report & Inline Update)
    // Route ini yang sempat error 'Route [presensi.reportIndex] not defined'
    #Route::get('/presensi/report', [PresensiController::class, 'reportIndex'])->name('presensi.reportIndex');
    Route::get('/presensi/admin-report', [PresensiController::class, 'reportIndex'])->name('presensi.reportAdmin');
    Route::match(['post', 'put'], '/presensi/update-inline/{id}', [PresensiController::class, 'updateStatusInline'])->name('presensi.update-inline');
    Route::delete('/presensi/{id}', [PresensiController::class, 'destroy'])->name('presensi.destroy');

    // Route untuk fitur email
    Route::post('/presensi/get-alpa-list', [PresensiController::class, 'getAlpaList'])->name('presensi.get-alpa-list');
    Route::post('/presensi/send-alpa-single', [PresensiController::class, 'sendAlpaSingle'])->name('presensi.send-alpa-single');
});


// ==========================================
// RUTE KHUSUS PETUGAS SPD
// ==========================================
Route::middleware(['auth', 'spd'])->group(function () {
    // Validasi Token & Akses Terbatas
    Route::get('/enter-token', [TokenController::class, 'showEnterTokenForm'])->name('enter-token');
    Route::post('/enter-token', [TokenController::class, 'processToken']);
    Route::get('/restricted-page', [TokenController::class, 'showRestrictedPage'])->name('restricted-page');
    
    // Fitur Tambahan SPD
    Route::get('/kritik-saran', [KritikSaranController::class, 'view'])->name('lihatkritiksaran');
    Route::post('/send-email', [EmailController::class, 'sendEmail'])->name('send.email');

    // Pencatatan Operasi Rutin
    Route::get('/catat-rutin', function () { return view('operasirutin.catatrutinpilih'); });
    Route::get('/catat', [OperasiRutinController::class, 'showForm'])->name('catat');
    Route::post('/operasi-rutin', [OperasiRutinController::class, 'store'])->name('operasi-rutin.store');
    Route::get('catat-rutin/{id}/edit', [OperasiRutinController::class, 'edit'])->name('catatedit');
    Route::put('operasi-rutin/{id}/update', [OperasiRutinController::class, 'update'])->name('operasi-rutin.update');
    Route::delete('/delete-rutin/{id}', [OperasiRutinController::class, 'destroy'])->name('deleteRoute');

    // Pencatatan Operasi Umum
    Route::get('/catat-umum', [OperasiUmumController::class, 'create'])->name('catat.umum');
    Route::post('/operasi-umum', [OperasiUmumController::class, 'store'])->name('operasi-umum.store');
    Route::get('catat-umum/{id}/edit', [OperasiUmumController::class, 'edit'])->name('catatedit.umum');
    Route::put('operasi-umum/{id}/update', [OperasiUmumController::class, 'update'])->name('operasi-umum.update');
    Route::delete('/delete-umum/{id}', [OperasiUmumController::class, 'destroy'])->name('delete.umum');

    // Penindakan Harian (Hanya SPD)
    Route::get('/catat-harian', [PenindakanHarianController::class, 'create'])->name('catat.harian');
    Route::post('/penindakan-harian', [PenindakanHarianController::class, 'store'])->name('penindakan-harian.store');
    Route::get('/laporan-harian', [PenindakanHarianController::class, 'index'])->name('laporanharian');
    Route::get('/laporan-harian/filter', [PenindakanHarianController::class, 'filter'])->name('penindakan-harian.filter');
    Route::get('/laporan-harian/download/{format}', [PenindakanHarianController::class, 'downloadFilteredData'])->name('penindakan-harian.download');
    Route::delete('/delete-harian/{id}', [PenindakanHarianController::class, 'destroy'])->name('delete.harian');

    // Klaim Pelanggaran
    Route::get('/klaim-pelanggaran', [KlaimPelanggaranController::class, 'index'])->name('klaim-pelanggaran');
    Route::get('/klaim-pelanggaran/filter', [KlaimPelanggaranController::class, 'filter'])->name('klaim-pelanggaran.filter');

    // Presensi Apel (Fase 2: Scanning)
    Route::get('/presensi', [PresensiController::class, 'pencatatanIndex'])->name('presensi.index');
    Route::get('/presensi/scan/{apel_id}', [PresensiController::class, 'scanPage'])->name('presensi.scan');
    Route::post('/presensi/scan/{apel_id}', [PresensiController::class, 'storeScan'])->name('presensi.store');
});


// ==========================================
// RUTE LAPORAN (DIAKSES OLEH SPD & PEMONITOR)
// ==========================================
Route::middleware(['auth', 'laporan'])->group(function () {
    // Laporan Operasi Rutin
    Route::get('/laporan-rutin', [OperasiRutinController::class, 'index'])->name('laporanrutin');
    Route::get('/laporan-rutin/data', [OperasiRutinController::class, 'fetchData'])->name('operasi-rutin.data');
    Route::get('/laporan-rutin/filter', [OperasiRutinController::class, 'filter'])->name('operasi-rutin.filter');
    Route::get('/laporan-rutin/download/{format}', [OperasiRutinController::class, 'downloadFilteredData'])->name('operasi-rutin.download');

    // Laporan Operasi Umum
    Route::get('/laporan-umum', [OperasiUmumController::class, 'index'])->name('laporanumum');
    Route::get('/laporan-umum/filter', [OperasiUmumController::class, 'filter'])->name('operasi-umum.filter');
    Route::get('/laporan-umum/download/{format}', [OperasiUmumController::class, 'downloadFilteredData'])->name('operasi-umum.download');

    // Laporan Presensi Operasi
    Route::get('/presensi/spd-report', [PresensiController::class, 'reportIndex'])->name('presensi.reportSpd');
});