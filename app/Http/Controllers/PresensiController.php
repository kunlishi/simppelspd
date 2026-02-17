<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Apel;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\SPD;
use App\Exports\PresensiExport;
use Illuminate\Http\Request;
use Meatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    public function storeScan(Request $request, $apel_id){
        $request->validate([
            'nim' => 'required|exists:mahasiswas,nim',
        ]);

        $presensi = Presensi::where('apel_id', $apel_id)
                            ->where('nim', $request->nim)
                            ->first();

        if (!$presensi) {
            return response()->json(['message' => 'Presensi not found.'], 404);
        }

        if ($presensi->status === 'hadir') {
            return response()->json(['status' => 'error', 'message' => 'Mahasiswa sudah hadir.'], 400);
        }

        $apel = Apel::findOrFail($apel_id);
        $tanggal_apel = $apel->tanggal_apel;
        $waktu_apel = $apel->waktu_apel;

        if($tanggal_apel) {
            $current_date = now()->toDateString();
            if ($current_date !== $tanggal_apel->toDateString()) {
                return response()->json(['status' => 'error', 'message' => 'Presensi hanya dapat dilakukan pada tanggal apel.'], 400);
            }
        }

        if ($waktu_apel) {
            $mulai = $waktu_apel->copy()->subMinutes(150);
            $akhir = $waktu_apel->copy()->addMinutes(30);

            $current_time = now();
            if($current_time < $mulai){
                return response()->json(['status' => 'error', 'message' => 'Waktu presensi belum dimulai.'], 400);
            } elseif ($current_time > $akhir){
                return response()->json(['status' => 'error', 'message' => 'Waktu presensi sudah berakhir.'], 400);
            } elseif ($current_time > $waktu_apel){
                $presensi->status = 'terlambat';
            } else {
                $presensi->status = 'hadir';
            }
        }

        $petugas = SPD::where('nas', Auth::user()->username)->first();
        $presensi->nama_petugas = optional($petugas)->nama_anggota ?? 'Petugas Tidak Dikenal';
        $presensi->save();
        return response()->json([
            'status' => 'success', 
            'message' => 'Presensi berhasil dicatat.', 
            'data' => [
                'waktu' => $presensi->updated_at->format('H:i:s'),
                'nim' => $presensi->nim,
                'nama' => $presensi->mahasiswa->nama ?? '-',
                'status' => strtoupper($presensi->status)
            ]
        ]); 
    }

    public function reportIndex(Request $request)
    {
        // Menggunakan JOIN agar filter tingkat (LEFT(kelas, 1)) dan tanggal berjalan efisien
        $query = Presensi::join('apel', 'presensi.apel_id', '=', 'apel.id')
            ->leftJoin('mahasiswas', 'presensi.nim', '=', 'mahasiswas.nim')
            ->select(
                'presensi.*',
                'apel.tanggal_apel as tanggal',
                'mahasiswas.kelas',
                'mahasiswas.nama as nama_mhs'
            );

        if ($request->filled('tanggal')) {
            $query->whereDate('apel.tanggal_apel', $request->tanggal);
        }

        if ($request->filled('tingkat')) {
            $query->whereRaw('LEFT(mahasiswas.kelas, 1) = ?', [$request->tingkat]);
        }

        if ($request->filled('nama')) {
            $query->where('presensi.nama', 'like', '%' . $request->nama . '%');
        }

        // Mengambil data lengkap (hadir, tidak_hadir, dll)
        $data = $query->orderBy('apel.tanggal_apel', 'desc')
                    ->orderBy('presensi.kelas', 'asc')
                    ->orderBy('presensi.nim', 'asc')
                    ->paginate(30);

        return view('presensi.report', compact('data'));
    }

    public function downloadFilteredData(Request $request, $format)
    {
        // 1. Definisikan Query dengan Join agar filter berfungsi
        $query = Presensi::join('apel', 'presensi.apel_id', '=', 'apel.id')
            ->leftJoin('mahasiswas', 'presensi.nim', '=', 'mahasiswas.nim')
            ->select('presensi.*', 'apel.tanggal_apel as tanggal', 'mahasiswas.kelas');

        // 2. Terapkan Filter (sama seperti di reportIndex)
        if ($request->filled('tanggal')) { 
            $query->whereDate('apel.tanggal_apel', $request->tanggal); 
        }
        if ($request->filled('tingkat')) { 
            $query->whereRaw('LEFT(mahasiswas.kelas, 1) = ?', [$request->tingkat]); 
        }

        // 3. Ambil data hasil filter
        $exportData = $query->get();
        
        // 4. Ambil variabel tanggal dari input untuk dikirim ke Export Class
        $tanggal = $request->input('tanggal'); 

        // Tentukan nama file
        $fileName = "laporan_presensi_" . ($tanggal ?? now()->format('Ymd'));

        // PERBAIKAN: Kirim 2 argumen ($exportData DAN $tanggal)
        if ($format === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\PresensiExport($exportData, $tanggal), 
                "{$fileName}.xlsx"
            );
        } else {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\PresensiExport($exportData, $tanggal), 
                "{$fileName}.csv", 
                \Maatwebsite\Excel\Excel::CSV
            );
        }
    }

    public function pencatatanIndex(){
        $apels = Apel::orderBy('tanggal_apel', 'desc')->get();
        return view('presensi.petugas.index', compact('apels'));
    }

    public function scanPage($apel_id){
        $apel = Apel::findOrFail($apel_id);
        $myScans = Presensi::where('apel_id', $apel_id)
                    ->where('nama_petugas', SPD::where('nas', Auth::user()->username)->first()->nama_anggota ?? 'Petugas Tidak Dikenal')
                    ->whereIn('status', ['hadir', 'terlambat'])
                    ->with('mahasiswa')
                    ->orderBy('updated_at', 'desc')
                    ->get(); 
        return view('presensi.petugas.scan', compact('apel', 'myScans'));
    }

    public function updateStatusInline(Request $request, $id) {
        $request->validate([
            'status' => 'required|in:hadir,izin,kurang_cukup_bukti_izin,sakit,kurang_cukup_bukti_sakit,tidak_hadir',
        ]);

        $presensi = Presensi::findOrFail($id);
        $presensi->status = $request->status;
        $presensi->nama_petugas = $request->nama_petugas;
        $presensi->save();

        return response()->json(['message' => 'Status presensi berhasil diperbarui.']);
    }

    public function destroy($id) {
        $presensi = Presensi::findOrFail($id);
        $presensi->delete();

        return response()->json(['message' => 'Presensi berhasil dihapus.']);
    }
}
