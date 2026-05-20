<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Apel;
use App\Models\Mahasiswa;
use App\Models\Kelas;
use App\Models\SPD;
use App\Exports\PresensiExport;
use App\Mail\NotifikasiPresensiMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class PresensiController extends Controller
{
    public function storeScan(Request $request, $apel_id)
    {
        $request->validate([
            'nim' => 'required|exists:mahasiswas,nim',
            'status_manual' => 'nullable|in:hadir,terlambat', // Opsi untuk petugas override status
        ]);

        $apel = Apel::with(['kelasPeserta'])->findOrFail($apel_id);
        $mahasiswa = Mahasiswa::where('nim', $request->nim)->first();

        // 1. Validasi apakah mahasiswa ini adalah peserta apel (dari kelas terpilih) ATAU petugas SPD
        $isPeserta = $apel->kelasPeserta->contains('id', $mahasiswa->kelas_id);
        //$isPetugas = $apel->petugasSpd->contains('nas', $mahasiswa->nim); // Asumsi NIM = NAS untuk petugas

        if (!$isPeserta) {
            return response()->json(['status' => 'error', 'message' => 'Mahasiswa tidak ditugaskan pada apel ini.'], 403);
        }

        // 2. Cek apakah sudah presensi
        $presensi = Presensi::where('apel_id', $apel_id)->where('nim', $request->nim)->first();
        if ($presensi && in_array($presensi->status, ['hadir', 'terlambat'])) {
            return response()->json(['status' => 'error', 'message' => 'Mahasiswa sudah hadir.'], 400);
        }

        // 3. Validasi Tanggal dan Waktu
        $tanggal_apel = $apel->tanggal_apel;
        $waktu_apel = $apel->waktu_apel;

        if ($tanggal_apel && now()->toDateString() !== \Carbon\Carbon::parse($tanggal_apel)->toDateString()) {
            return response()->json(['status' => 'error', 'message' => 'Presensi hanya dapat dilakukan pada tanggal apel.'], 400);
        }

        $status = 'hadir';
        $is_late_zone = false;

        if ($waktu_apel) {
            // Asumsi waktu_apel adalah string waktu (misal: "07:00:00")
            $tz = config('app.timezone', 'UTC');
            // Gabungkan tanggal apel dengan waktu apel agar perbandingannya akurat
            $waktu_target_string = \Carbon\Carbon::parse($tanggal_apel)->format('Y-m-d') . ' ' . $waktu_apel;
            $waktu_target = \Carbon\Carbon::parse($waktu_target_string);
            
            $mulai = $waktu_target->copy()->subMinutes(120); // Scanner dibuka
            $akhir = $waktu_target->copy()->addMinutes(30);  // Scanner ditutup
            $current_time = now($tz);

            if ($current_time < $mulai) {
                return response()->json(['status' => 'error', 'message' => 'Waktu presensi belum dimulai.'], 400);
            } elseif ($current_time > $akhir) {
                return response()->json(['status' => 'error', 'message' => 'Waktu presensi sudah berakhir.'], 400);
            } elseif ($current_time > $waktu_target) {
                $status = 'terlambat';
                $is_late_zone = true; // Tandai bahwa saat ini sudah masuk zona terlambat
            }
        }

        // 4. OVERRIDE STATUS: Jika petugas memilih status manual DAN memang sedang di zona terlambat
        if ($is_late_zone && $request->filled('status_manual')) {
            $status = $request->status_manual;
        }

        // 5. Lazy Insertion
        $nasPetugas = Auth::user()->username; // Asumsi username menyimpan NAS
        
        $presensi = Presensi::updateOrCreate(
            ['apel_id' => $apel_id, 'nim' => $request->nim],
            [
                'status' => $status,
                'petugas_nas' => $nasPetugas,
                //'waktu_scan' => now() 
            ]
        );

        // Eksekusi pengiriman email ke antrean (Queue)
        if ($mahasiswa) {
            // Rakit email otomatis menggunakan format STIS
            $emailTujuan = $mahasiswa->nim . '@stis.ac.id';
            Mail::to($emailTujuan)->queue(new NotifikasiPresensiMail('scan', $mahasiswa, $apel, $status));
        }

        return response()->json([
            'status' => 'success', 
            'message' => 'Presensi berhasil dicatat sebagai ' . strtoupper($status) . '.', 
            'data' => [
                'waktu' => $presensi->updated_at->format('H:i:s'),
                'nim' => $presensi->nim,
                'nama' => $mahasiswa->nama ?? '-',
                'status' => strtoupper($presensi->status)
            ]
        ]); 
    }

    public function reportIndex(Request $request)
    {
        // Pastikan ada apel_id yang dipilih, atau ambil apel terakhir
        $apel_id = $request->input('apel_id', null);
        if (!$apel_id) {
            $apelTerakhir = Apel::orderBy('tanggal_apel', 'desc')->first();
            $apel_id = $apelTerakhir ? $apelTerakhir->id : null;
        }

        if (!$apel_id) {
            return view('presensi.report', ['data' => collect(), 'apels' => Apel::orderBy('tanggal_apel', 'desc')->get()]);
        }

        $apel = Apel::with(['kelasPeserta'])->findOrFail($apel_id);
        $kelasTarget = $apel->kelasPeserta->pluck('id')->toArray();

        // Query LEFT JOIN Dinamis
        $query = DB::table('mahasiswas')
            ->whereIn('mahasiswas.kelas_id', $kelasTarget)
            ->leftJoin('presensi', function($join) use ($apel_id) {
                $join->on('mahasiswas.nim', '=', 'presensi.nim')
                     ->where('presensi.apel_id', '=', $apel_id);
            })
            ->leftJoin('kelas', 'mahasiswas.kelas_id', '=', 'kelas.id')
            ->leftJoin('spd', 'presensi.petugas_nas', '=', 'spd.nas')
            ->select(
                'mahasiswas.nim',
                'mahasiswas.nama as nama_mhs',
                'kelas.nama_kelas as kelas',
                'presensi.id as presensi_id',
                'presensi.updated_at as waktu_scan',
                'presensi.status as status_kehadiran',
                'spd.nama_anggota as nama_petugas',
                DB::raw("COALESCE(presensi.status, 'tidak_hadir') as status_kehadiran")
            );

        // Filter Tambahan
        // if ($request->filled('tingkat')) {
        //     $query->whereRaw('LEFT(kelas.nama_kelas, 1) = ?', [$request->tingkat]);
        // }

        if ($request->filled('status')) {
            if ($request->status === 'tidak_hadir') {
                // Jika tidak hadir, berarti data presensinya kosong (NULL)
                $query->whereNull('presensi.status');
            } else {
                // Untuk status lainnya (hadir, terlambat, izin, sakit)
                $query->where('presensi.status', $request->status);
            }
        }

        if ($request->filled('nama')) {
            $query->where(function($q) use ($request) {
                $q->where('mahasiswas.nama', 'like', '%' . $request->nama . '%')
                  ->orWhere('mahasiswas.nim', 'like', '%' . $request->nama . '%');
            });
        }

        if ($request->filled('tingkat')) {
            $query->whereRaw('LEFT(kelas.nama_kelas, 1) = ?', [$request->tingkat]);
        }

        $data = $query->orderBy('kelas.nama_kelas', 'asc')->orderBy('mahasiswas.nama', 'asc')->paginate(50);
        
        // Kirim list apels untuk dropdown filter di view
        $apels = Apel::orderBy('tanggal_apel', 'desc')->get();

        return view('presensi.report', compact('data', 'apels', 'apel_id'));
    }

    public function downloadFilteredData(Request $request, $format)
    {
        $apel_id = $request->input('apel_id');
        
        if (!$apel_id) {
            return back()->with('error', 'Pilih jadwal apel terlebih dahulu.');
        }

        $apel = Apel::with(['kelasPeserta'])->findOrFail($apel_id);
        $kelasTarget = $apel->kelasPeserta->pluck('id')->toArray();

        $query = DB::table('mahasiswas')
            ->whereIn('mahasiswas.kelas_id', $kelasTarget)
            ->leftJoin('presensi', function($join) use ($apel_id) {
                $join->on('mahasiswas.nim', '=', 'presensi.nim')
                     ->where('presensi.apel_id', '=', $apel_id);
            })
            ->leftJoin('kelas', 'mahasiswas.kelas_id', '=', 'kelas.id')
            // Tambahkan Join untuk Petugas
            ->leftJoin('spd', 'presensi.petugas_nas', '=', 'spd.nas')
            ->select(
                'mahasiswas.nim',
                'mahasiswas.nama as nama_mhs',
                'kelas.nama_kelas as kelas',
                'presensi.updated_at as waktu_scan',
                // Tambahkan Select untuk Petugas
                'spd.nama_anggota as nama_petugas',
                DB::raw("COALESCE(presensi.status, 'tidak_hadir') as status_kehadiran")
            );

        // Filter Tingkat
        if ($request->filled('tingkat')) {
            $query->whereRaw('LEFT(kelas.nama_kelas, 1) = ?', [$request->tingkat]);
        }

        if ($request->filled('status')) {
            if ($request->status === 'tidak_hadir') {
                $query->whereNull('presensi.status');
            } else {
                $query->where('presensi.status', $request->status);
            }
        }

        // Opsional: Ikutkan pencarian nama jika kamu ingin hasil export sama dengan hasil search
        if ($request->filled('nama')) {
            $query->where(function($q) use ($request) {
                $q->where('mahasiswas.nama', 'like', '%' . $request->nama . '%')
                  ->orWhere('mahasiswas.nim', 'like', '%' . $request->nama . '%');
            });
        }

        // Eksekusi Query dan Urutkan
        $exportData = $query->orderBy('kelas.nama_kelas', 'asc')
                            ->orderBy('mahasiswas.nama', 'asc')
                            ->get();
                            
        $tanggal_filter = $apel->tanggal_apel; 
        $fileName = "laporan_presensi_" . ($tanggal_filter ?? now()->format('Ymd'));

        if ($format === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PresensiExport($exportData, $tanggal_filter), "{$fileName}.xlsx");
        } else {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PresensiExport($exportData, $tanggal_filter), "{$fileName}.csv", \Maatwebsite\Excel\Excel::CSV);
        }
    }

    public function pencatatanIndex()
    {
        $tz = 'Asia/Jakarta';
        $now = now($tz);
        
        // 1. Ambil jadwal apel HARI INI saja
        // Kita tidak perlu menampilkan jadwal besok atau kemarin di halaman Scanner
        $semuaApelHariIni = Apel::whereDate('tanggal_apel', $now->toDateString())->get();

        // 2. Filter menggunakan Collection Laravel
        $apels = $semuaApelHariIni->filter(function ($apel) use ($now, $tz) {
            if (!$apel->waktu_apel) return false;

            // Gabungkan tanggal dan waktu agar presisi
            $waktu_target_string = \Carbon\Carbon::parse($apel->tanggal_apel)->format('Y-m-d') . ' ' . $apel->waktu_apel;
            $waktu_target = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $waktu_target_string, $tz);

            // Tentukan jendela waktu aktif
            $mulai = $waktu_target->copy()->subMinutes(120);
            $akhir = $waktu_target->copy()->addMinutes(30);

            // Kembalikan true (tampilkan) JIKA waktu sekarang berada di antara $mulai dan $akhir
            return $now->between($mulai, $akhir);
        });

        // 3. Kirim data yang sudah difilter ke view
        return view('presensi.petugas.index', compact('apels'));
    }

    public function scanPage($apel_id){
        $apel = Apel::findOrFail($apel_id);
        $nasPetugas = Auth::user()->username;

        $myScans = Presensi::where('apel_id', $apel_id)
                    ->where('petugas_nas', $nasPetugas)
                    ->whereIn('status', ['hadir', 'terlambat'])
                    ->with('mahasiswa')
                    ->orderBy('updated_at', 'desc')
                    ->get(); 

        return view('presensi.petugas.scan', compact('apel', 'myScans'));
    }

    public function updateStatusInline(Request $request, $id)
    {
        $request->validate([
            'status'  => 'required|in:hadir,tidak_hadir,terlambat,izin,sakit',
            'nim'     => 'required',
            'apel_id' => 'required'
        ]);

        DB::beginTransaction(); // Gunakan transaksi agar data DB dan email tetap sinkron
        try {
            $petugasNas = (Auth::user()->role == 'spd') ? Auth::user()->username : null;

            // Ambil data mahasiswa beserta relasi kelasnya agar tidak error di template email
            $mahasiswa = Mahasiswa::with('kelas')->where('nim', $request->nim)->first();
            $apel = Apel::find($request->apel_id);

            if (!$mahasiswa || !$apel) {
                return response()->json(['success' => false, 'message' => 'Data Mahasiswa atau Jadwal tidak ditemukan.'], 404);
            }

            $presensi = Presensi::where('apel_id', $request->apel_id)
                                ->where('nim', $request->nim)
                                ->first();

            $statusFinal = $request->status;

            if ($statusFinal === 'tidak_hadir') {
                if ($presensi) {
                    $presensi->delete();
                }
                $new_id = 'new';
            } else {
                if ($presensi) {
                    $presensi->status = $statusFinal;
                    $presensi->petugas_nas = $petugasNas;
                    $presensi->save();
                } else {
                    $presensi = new Presensi();
                    $presensi->apel_id = $request->apel_id;
                    $presensi->nim = $request->nim;
                    $presensi->status = $statusFinal;
                    $presensi->petugas_nas = $petugasNas;
                    $presensi->save();
                }
                $new_id = $presensi->id;
            }

            DB::commit(); // Simpan perubahan ke Database terlebih dahulu

            // PROSES PENGIRIMAN EMAIL (Setelah DB Sukses)
            try {
                $emailTujuan = $mahasiswa->nim . '@stis.ac.id';
                $statusEmail = strtoupper(str_replace('_', ' ', $statusFinal));
                
                // Gunakan queue agar tidak memperlambat respon UI
                Mail::to($emailTujuan)->queue(new NotifikasiPresensiMail(
                    ($statusFinal === 'tidak_hadir' ? 'update' : 'update'), // atau 'alpa' jika diinginkan
                    $mahasiswa, 
                    $apel, 
                    $statusEmail
                ));
            } catch (\Exception $e) {
                // Jika email gagal, jangan batalkan update DB, cukup catat di log
                Log::error("Gagal antre email untuk {$mahasiswa->nim}: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Status presensi berhasil diperbarui.',
                'new_id'  => $new_id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Fungsi untuk mengambil daftar mahasiswa yang Alpa
    public function getAlpaList(Request $request)
    {
        $request->validate(['apel_id' => 'required|exists:apel,id']);
        
        $apel = Apel::findOrFail($request->apel_id);
        $kelasTarget = DB::table('apel_kelas')->where('apel_id', $apel->id)->pluck('kelas_id');
        
        $mahasiswaAlpa = DB::table('mahasiswas')
            ->whereIn('kelas_id', $kelasTarget)
            ->whereNotIn('nim', function($query) use ($apel) {
                $query->select('nim')->from('presensi')->where('apel_id', $apel->id);
            })->get();

        return response()->json([
            'success' => true,
            'data' => $mahasiswaAlpa
        ]);
    }

    // Fungsi untuk mengirim 1 email saja (Akan dieksekusi berulang kali oleh JavaScript)
    public function sendAlpaSingle(Request $request)
    {
        $request->validate([
            'apel_id' => 'required',
            'nim'     => 'required'
        ]);

        try {
            $apel = Apel::find($request->apel_id);
            $mahasiswa = Mahasiswa::where('nim', $request->nim)->first();

            if ($mahasiswa && $apel) {
                $emailTujuan = $mahasiswa->nim . '@stis.ac.id';
                
                // Karena dikirim satu-satu oleh frontend, kita gunakan send() agar statusnya akurat
                Mail::to($emailTujuan)->send(new NotifikasiPresensiMail('alpa', $mahasiswa, $apel, 'TIDAK HADIR'));
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id) {
        $presensi = Presensi::findOrFail($id);
        $presensi->delete();

        return response()->json(['message' => 'Presensi berhasil dihapus.']);
    }
}