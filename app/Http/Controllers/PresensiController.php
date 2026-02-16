<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Apel;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\SPD;
use App\Exports\PresensiExport;
use Illuminate\Http\Request;
use Meaatwebsite\Excel\Facades\Excel;
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
            return response()->json(['message' => 'Mahasiswa sudah hadir.'], 400);
        }

        if($tanggal_apel = Apel::find($apel_id)->tanggal_apel) {
            $current_date = now()->toDateString();
            if ($current_date !== $tanggal_apel->toDateString()) {
                return redirect()->back()->with('message', 'Presensi hanya bisa dicatat pada tanggal apel.');
            }
        }

        if ($waktu_apel = Apel::find($apel_id)->waktu_apel) {
            $current_time = now();
            if($current_time < $waktu_apel->subMinutes(150)){
                return redirect()->back()->with('message', 'Presensi belum bisa dicatat. Tunggu hingga 2 jam 30 menit sebelum waktu apel.');
            } elseif ($current_time > $waktu_apel->addMinutes(30)){
                return redirect()->back()->with('message', 'Presensi sudah tidak bisa dicatat. Waktu apel sudah lewat 30 menit.');
            } elseif ($current_time > $waktu_apel){
                $presensi->status = 'terlambat';
            } else {
                $presensi->status = 'hadir';
            }
        }

        $presensi->nama_petugas = SPD::where('nas', Auth::user()->username)->first()->nama_anggota ?? 'Petugas Tidak Dikenal';
        $presensi->save();

        return redirect()->back()->with('message', 'Presensi berhasil dicatat.');  
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
                    ->orderBy('presensi.nim', 'asc')
                    ->paginate(30);

        return view('presensi.report', compact('data'));
    }

    public function downloadFilteredData(Request $request, $format)
    {
        $query = Presensi::join('apel', 'presensi.apel_id', '=', 'apel.id')
            ->leftJoin('mahasiswas', 'presensi.nim', '=', 'mahasiswas.nim')
            ->select('presensi.*', 'apel.tanggal_apel as tanggal', 'mahasiswas.kelas');

        if ($request->filled('tanggal')) { $query->whereDate('apel.tanggal_apel', $request->tanggal); }
        if ($request->filled('tingkat')) { $query->whereRaw('LEFT(mahasiswas.kelas, 1) = ?', [$request->tingkat]); }

        $exportData = $query->get();
        $tanggal = $request->input('tanggal');

        if ($format === 'excel') {
            return Excel::download(new PresensiExport($exportData, $tanggal), "laporan_presensi_{$tanggal}.xlsx");
        } else {
            return Excel::download(new PresensiExport($exportData, $tanggal), "laporan_presensi_{$tanggal}.csv", \Maatwebsite\Excel\Excel::CSV);
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

    public function downloadFilteredData_(Request $request, $format)
    {
        // Gunakan query filter yang sama dengan reportIndex
        $query = Presensi::with('apel', 'mahasiswa');
        // ... (tambahkan filter tanggal & tingkat di sini) ...
        $data = $query->get();

        $fileName = "laporan_presensi_" . now()->format('Ymd');

        if ($format === 'excel') {
            return Excel::download(new PresensiExport($data), "{$fileName}.xlsx");
        } else {
            return Excel::download(new PresensiExport($data), "{$fileName}.csv", \Maatwebsite\Excel\Excel::CSV);
        }
    }

    public function destroy($id) {
        $presensi = Presensi::findOrFail($id);
        $presensi->delete();

        return response()->json(['message' => 'Presensi berhasil dihapus.']);
    }
}
