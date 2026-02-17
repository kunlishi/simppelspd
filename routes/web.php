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


// halaman awal
Route::get('/', function () {
    if (Auth::check()) {
        // Jika sudah login, arahkan ke dashboard
        return redirect('/dashboard');
    }
    // Jika belum login, tampilkan landing page
    return view('landingpage');
});

// hanya bisa diakses oleh guest 
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [SesiController::class, 'index'])->name('login');
    Route::post('/login', [SesiController::class, 'login']);
    Route::get('/kirim-kritik-saran', [KritikSaranController::class, 'index'])->name('kritiksaran');
    Route::post('/kirim-kritik-saran/submit', [KritikSaranController::class, 'submit'])->name('kritiksaran.submit');
    Route::get('/peraturan', [PeraturanController::class, 'tampil'])->name('tampil.peraturan');
});

//Route::get('/home', function () {
//  return redirect('/admin');
//});

// hanya bisa diakses yang login (login sebagai apapun)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/logout', [SesiController::class, 'logout']);
    // Ubah Password
    Route::get('/ubah-password', [UbahPasswordController::class, 'index']);
    Route::post('/ubah-password', [UbahPasswordController::class, 'ubahPassword']);
    // Kritik Saran
    Route::get('/dashboard/data', [DashboardController::class, 'StatDesk']);
});



// Buat Token (Admin)
Route::get('/buat-token', [TokenController::class, 'index'])->middleware('admin');
Route::get('/generate-token', [TokenController::class, 'generateToken'])->middleware('admin');

// Masukin Token (SPD)
Route::get('/enter-token', [TokenController::class, 'showEnterTokenForm'])->middleware('spd')->name('enter-token');
Route::post('/enter-token', [TokenController::class, 'processToken'])->middleware('spd');
Route::get('/restricted-page', [TokenController::class, 'showRestrictedPage'])->middleware('spd')->name('restricted-page');
Route::get('/klaim-pelanggaran', function () {
    return view('klaim-pelanggaran', ['title' => 'Klaim Pelanggaran']);
})->middleware('spd')->name('klaim-pelanggaran');

// Route manage FAQ (admin)
Route::get('/admin-faq', [FaqController::class, 'admin_tampil'])->middleware('admin')->name('admin.tampil.faq');
Route::get('/admin-faq/tambah', [FaqController::class, 'admin_tambah'])->middleware('admin')->name('admin.tambah.faq');
Route::post('/admin-faq/submit', [FaqController::class, 'admin_submit'])->middleware('admin')->name('admin.submit.faq');
Route::get('/admin-faq/edit/{id}', [FaqController::class, 'admin_edit'])->middleware('admin')->name('admin.edit.faq');
Route::post('/admin-faq/update/{id}', [FaqController::class, 'admin_update'])->middleware('admin')->name('admin.update.faq');
Route::delete('/admin-faq/delete/{id}', [FaqController::class, 'admin_delete'])->middleware('admin')->name('admin.delete.faq');

// Route manage peraturan (admin)
Route::get('/admin-peraturan', [PeraturanController::class, 'admin_tampil'])->middleware('admin')->name('admin.tampil.peraturan');
Route::get('/admin-peraturan/tambah', [PeraturanController::class, 'admin_tambah'])->middleware('admin')->name('admin.tambah.peraturan');
Route::post('/admin-peraturan/submit', [PeraturanController::class, 'admin_submit'])->middleware('admin')->name('admin.submit.peraturan');
Route::get('/admin-peraturan/edit/{id}', [PeraturanController::class, 'admin_edit'])->middleware('admin')->name('admin.edit.peraturan');
Route::post('/admin-peraturan/update/{id}', [PeraturanController::class, 'admin_update'])->middleware('admin')->name('admin.update.peraturan');
Route::delete('/admin-peraturan/delete/{id}', [PeraturanController::class, 'admin_delete'])->middleware('admin')->name('admin.delete.peraturan');

// filter data dengan ajax bisa dilakukan pada menu laporan (oleh spd atau pemonitor)
// Route::get('/filter-by-date-ajax', [CatatController::class, 'filterByDateAjax'])->middleware('laporan')->name('filter.date.ajax');

// Melihat kritik saran hanya bisa oleh spd
Route::get('/kritik-saran', [KritikSaranController::class, 'view'])->middleware('spd')->name('lihatkritiksaran');

// Melihat FAQ bisa oleh semua role baik guest maupun login sebagai role apapun
Route::get('/faq', [FaqController::class, 'index']);

// Mengirim email hanya bisa untuk SPD saat mencatat
Route::post('/send-email', [EmailController::class, 'sendEmail'])->middleware('spd')->name('send.email');

//Route Tampil Pelanggaran
Route::get('pelanggaran', [PelanggaranController::class, 'index'])->name('pelanggaran.index');
Route::get('pelanggaran/edit', [PelanggaranController::class, 'munculedit'])->name('pelanggaran.edit');

//Route Operasi Rutin
//Pencatatan hanya untuk SPD saja
//Pelaporan untuk SPD dan pemonitor yang disimpan dalam middleware laporan
Route::get('/catat-rutin', function () {
    return view('operasirutin.catatrutinpilih');
})->middleware('spd');
Route::get('/catat', [OperasiRutinController::class, 'showForm'])->middleware('spd')->name('catat');
Route::post('/operasi-rutin', [OperasiRutinController::class, 'store'])->middleware('spd')->name('operasi-rutin.store');
Route::delete('/delete-rutin/{id}', [OperasiRutinController::class, 'destroy'])->middleware('spd')->name('deleteRoute');
Route::get('catat-rutin/{id}/edit', [OperasiRutinController::class, 'edit'])->middleware('spd')->name('catatedit');
Route::put('operasi-rutin/{id}/update', [OperasiRutinController::class, 'update'])->middleware('spd')->name('operasi-rutin.update');
Route::get('/laporan-rutin', [OperasiRutinController::class, 'index'])->middleware('laporan')->name('laporanrutin');
Route::get('/laporan-rutin/data', [OperasiRutinController::class, 'fetchData'])->middleware('laporan')->name('operasi-rutin.data');
Route::get('/laporan-rutin/filter', [OperasiRutinController::class, 'filter'])->middleware('laporan')->name('operasi-rutin.filter');
Route::get('/laporan-rutin/download/{format}', [OperasiRutinController::class, 'downloadFilteredData'])->middleware('laporan')->name('operasi-rutin.download');

//Route Operasi Umum
//Pencatatan hanya untuk SPD saja
//Pelaporan untuk SPD dan pemonitor yang disimpan dalam middleware laporan
Route::get('/catat-umum', [OperasiUmumController::class, 'create'])->middleware('spd')->name('catat.umum');
Route::post('/operasi-umum', [OperasiUmumController::class, 'store'])->middleware('spd')->name('operasi-umum.store');
Route::delete('/delete-umum/{id}', [OperasiUmumController::class, 'destroy'])->middleware('spd')->name('delete.umum');
Route::get('catat-umum/{id}/edit', [OperasiUmumController::class, 'edit'])->middleware('spd')->name('catatedit.umum');
Route::put('operasi-umum/{id}/update', [OperasiUmumController::class, 'update'])->middleware('spd')->name('operasi-umum.update');
Route::get('/laporan-umum', [OperasiUmumController::class, 'index'])->middleware('laporan')->name('laporanumum');
Route::get('/laporan-umum/filter', [OperasiUmumController::class, 'filter'])->middleware('laporan')->name('operasi-umum.filter');
Route::get('/laporan-umum/download/{format}', [OperasiUmumController::class, 'downloadFilteredData'])->middleware('laporan')->name('operasi-umum.download');

//Route Penindakan Harian
//Baik pencatatan maupun pelaporan hanya untuk SPD saja
Route::get('/catat-harian', [PenindakanHarianController::class, 'create'])->middleware('spd')->name('catat.harian');
Route::post('/penindakan-harian', [PenindakanHarianController::class, 'store'])->middleware('spd')->name('penindakan-harian.store');
Route::get('/laporan-harian', [PenindakanHarianController::class, 'index'])->middleware('spd')->name('laporanharian');
Route::get('/laporan-harian/filter', [PenindakanHarianController::class, 'filter'])->middleware('spd')->name('penindakan-harian.filter');
Route::get('/laporan-harian/download/{format}', [PenindakanHarianController::class, 'downloadFilteredData'])->middleware('spd')->name('penindakan-harian.download');
Route::delete('/delete-harian/{id}', [PenindakanHarianController::class, 'destroy'])->middleware('spd')->name('delete.harian');


// Route Apel (Admin)
Route::get('/apel-baru', [ApelController::class, 'create'])->middleware('admin')->name('apel.create');
Route::post('/apel-baru', [ApelController::class, 'store'])->middleware('admin')->name('apel.store');
Route::get('/apel/{id}/edit', [ApelController::class, 'edit'])->middleware('admin')->name('apel.edit');
Route::put('/apel/{id}/update', [ApelController::class, 'update'])->middleware('admin')->name('apel.update');
Route::delete('/apel/{id}/delete', [ApelController::class, 'destroy'])->middleware('admin')->name('apel.delete');
Route::get('/daftar-apel', [ApelController::class, 'index'])->middleware('admin')->name('apel.index');
Route::get('/presensi/admin-report', [PresensiController::class, 'reportIndex'])->middleware('admin')->name('apel.report');
Route::post('/presensi/update-status/{id}', [PresensiController::class, 'updateStatusInline'])->middleware('admin')->name('presensi.updateStatus');
Route::delete('/presensi/{id}', [PresensiController::class, 'destroy'])->middleware('admin')->name('presensi.destroy');

// Route Presensi (SPD)
Route::get('/presensi', [PresensiController::class, 'pencatatanIndex'])->middleware('spd')->name('presensi.index');
Route::get('/presensi/scan/{apel_id}', [PresensiController::class, 'scanPage'])->middleware('spd')->name('presensi.scan');
Route::post('/presensi/scan/{apel_id}', [PresensiController::class, 'storeScan'])->middleware('spd')->name('presensi.store');
Route::get('/presensi/report', [PresensiController::class, 'reportIndex'])->middleware('spd')->name('presensi.report');
Route::get('/presensi/download/{format}', [PresensiController::class, 'downloadFilteredData'])->name('presensi.download');

// Route Klaim Pelanggaran untuk SPD
Route::get('/klaim-pelanggaran', [KlaimPelanggaranController::class, 'index'])->middleware('spd')->name('klaim-pelanggaran');
Route::get('/klaim-pelanggaran/filter', [KlaimPelanggaranController::class, 'filter'])->middleware('spd')->name('klaim-pelanggaran.filter');


//Route::get('/landing', function () {
//  return view('landingpage');
//});



Route::get('/get-mahasiswa', [MahasiswaController::class, 'getMahasiswa'])->name('get.mahasiswa');
Route::get('/get-mahasiswa-rutin', [MahasiswaController::class, 'getMahasiswaRutin'])->name('get.mahasiswarutin');
