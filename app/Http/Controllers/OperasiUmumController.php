<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\SPD;
use App\Models\Mahasiswa;
use App\Models\OperasiUmum;
use App\Models\Pelanggaran;
use Illuminate\Http\Request;
use App\Exports\OperasiUmumExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class OperasiUmumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = OperasiUmum::join('mahasiswas', function ($join) {
            $join->on('operasi_umums.nim', '=', 'mahasiswas.nim')
                ->on('operasi_umums.tahun_akademik', '=', 'mahasiswas.tahun_akademik');
        })
            ->join('kelas', 'mahasiswas.kelas_id', '=', 'kelas.id') // Join ke tabel kelas untuk mendapatkan nama kelas
            ->join('pelanggarans', 'operasi_umums.pelanggaran', '=', 'pelanggarans.kodePelanggaran') // Join ke tabel pelanggarans
            ->where('operasi_umums.status_pelanggaran', '!=', 'Dibatalkan') // Filter status "Dibatalkan"
            ->select(
                'operasi_umums.created_at as tanggal',
                'operasi_umums.created_at',
                'operasi_umums.updated_at',
                'operasi_umums.nim',
                'mahasiswas.nama',
                'kelas.nama_kelas as kelas',
                'pelanggarans.namaPelanggaran as pelanggaran', // Mengambil namaPelanggaran dari tabel pelanggarans
                'operasi_umums.nama_pencatat',
                'operasi_umums.status_pelanggaran',
                'operasi_umums.id'
            )
            ->orderBy('operasi_umums.created_at', 'desc')
            ->paginate(30);

        // Mengirim data ke view
        return view('operasiumum.laporanumum', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pelanggarans = Pelanggaran::all();

        return view('operasiumum.catatumum', compact('pelanggarans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required|regex:/^[0-9]{9}$/', // NIM harus 9 digit angka
            'pelanggaran' => 'required',
        ]);

        $mahasiswa = Mahasiswa::where('nim', $request->nim)
            ->where('tahun_akademik', $request->tahun_akademik)
            ->first();

        if (!$mahasiswa) {
            return response()->json(['message' => 'Mahasiswa tidak ditemukan'], 404);
        }

        $pelanggaran = $request->pelanggaran;
        $pencatat = SPD::where('nas', Auth::user()->username)->value('nama_anggota');
        $tahunAkademik = $request->input('tahun_akademik');

        $data = [];

        if (is_array($pelanggaran)) {
            foreach ($pelanggaran as $kode) {
                $namaPelanggaran = Pelanggaran::where('kodePelanggaran', $kode)->value('namaPelanggaran');
                if (!$namaPelanggaran) {
                    return response()->json(['message' => "Pelanggaran dengan kode '$kode' tidak ditemukan"], 400);
                }

                array_push($data, [
                    'nim' => $mahasiswa->nim,
                    'pelanggaran' => $kode,
                    'nama_pencatat' => $pencatat,
                    'created_at' => now(),
                    'tahun_akademik' => $tahunAkademik,
                    'status_pelanggaran' => 'Ditambahkan'
                ]);
            }
        }

        OperasiUmum::insert($data);
        app(EmailController::class)->sendEmail($request);

        session()->flash('success', 'Data berhasil ditambahkan');
        return redirect()->route('catat.umum');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $operasiUmum = OperasiUmum::select(
            'operasi_umums.*',
            'mahasiswas.nama',
            'kelas.nama_kelas as kelas',
            'pelanggarans.namaPelanggaran as pelanggaran' // Ambil nama pelanggaran dari tabel pelanggarans
        )
            ->join('mahasiswas', function ($join) {
                $join->on('operasi_umums.nim', '=', 'mahasiswas.nim')
                    ->on('operasi_umums.tahun_akademik', '=', 'mahasiswas.tahun_akademik'); // Join berdasarkan NIM dan tahun akademik
            })
            ->join('kelas', 'mahasiswas.kelas_id', '=', 'kelas.id') // Join ke tabel kelas untuk mendapatkan nama kelas
            ->leftJoin('pelanggarans', 'operasi_umums.pelanggaran', '=', 'pelanggarans.kodePelanggaran') // Join ke pelanggarans untuk mendapatkan nama pelanggaran
            ->where('operasi_umums.id', $id) // Filter berdasarkan ID operasi_umums
            ->firstOrFail();

        // Ambil semua data pelanggarans untuk ditampilkan di dropdown/suggestion
        $pelanggarans = Pelanggaran::all();

        // Gunakan nama pelanggaran yang dipilih sebelumnya
        $selectedPelanggaran = $operasiUmum->pelanggaran;
        return view('operasiumum.editcatatumum', compact('operasiUmum', 'pelanggarans', 'selectedPelanggaran'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nim' => 'required|regex:/^[0-9]{9}$/',
            'pelanggaran' => 'required',
        ]);

        $pelanggarans = Pelanggaran::all();

        // Mencari data OperasiUmum berdasarkan ID
        $operasiUmum = OperasiUmum::where('id', $id)->firstOrFail();

        // Cek apakah mahasiswa dengan NIM dan tahun akademik sesuai ada
        $mahasiswa = Mahasiswa::where('nim', $request->nim)
            ->where('tahun_akademik', $operasiUmum->tahun_akademik)
            ->first();

        if (!$mahasiswa) {
            return redirect()->route('catateditumum', $id)->with('error', 'Mahasiswa tidak ditemukan atau tidak sesuai dengan tahun akademik');
        }

        $kodePelanggaran = $request->pelanggaran;
        $pencatat = SPD::where('nas', Auth::user()->username)->value('nama_anggota');
        $tahunAkademik = $request->input('tahun_akademik', $operasiUmum->tahun_akademik);

        $namaPelanggaran = null;

        // Cek apakah $kodePelanggaran adalah namaPelanggaran
        $existingPelanggaran = $pelanggarans->firstWhere('namaPelanggaran', $kodePelanggaran);

        if ($existingPelanggaran) {
            // Jika ditemukan sebagai nama pelanggaran
            $namaPelanggaran = $kodePelanggaran; // Pelanggaran yang dikirim adalah namaPelanggaran
        } else {
            // Jika tidak ditemukan, berarti pelanggaran adalah kodePelanggaran
            $namaPelanggaran = Pelanggaran::where('kodePelanggaran', $kodePelanggaran)->value('namaPelanggaran') ?? 'Unknown';
        }

        // Batalkan data lama dengan mengubah status menjadi "Dibatalkan"
        $operasiUmum->update([
            'status_pelanggaran' => 'Dibatalkan',
            'updated_at' => now()
        ]);

        // Tambahkan data baru
        $currentTime = now(); // Waktu yang sama untuk created_at dan updated_at
        OperasiUmum::create([
            'nim' => $mahasiswa->nim,
            'pelanggaran' => $kodePelanggaran, // Update pelanggaran sesuai dengan data yang diterima
            'nama_pencatat' => $pencatat,
            'created_at' => $currentTime,
            'updated_at' => $currentTime, // Waktu yang sama untuk created_at dan updated_at
            'tahun_akademik' => $tahunAkademik,
            'status_pelanggaran' => 'Diperbarui'
        ]);

        app(EmailController::class)->sendEmail($request);

        // Redirect ke halaman laporan dengan pesan sukses
        return redirect()->route('laporanumum')->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Cari data berdasarkan ID
            $operasiUmum = OperasiUmum::find($id);

            // Jika data tidak ditemukan, kembalikan respon dengan status 404
            if (!$operasiUmum) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            // Update status menjadi "dibatalkan"
            $operasiUmum->update([
                'status_pelanggaran' => 'Dibatalkan', // Pastikan kolom ini ada di tabel
                'updated_at' => now() // Perbarui waktu terakhir diupdate
            ]);

            return redirect()->route('laporanumum');
        } catch (Exception $e) {
            // Menangani kesalahan jika terjadi exception
            return redirect()->route('laporanumum')->with('error', 'Data gagal dihapus: ' . $e->getMessage());
        }
    }

    public function filter(Request $request)
    {
        $query = OperasiUmum::join('mahasiswas', function ($join) {
            $join->on('operasi_umums.nim', '=', 'mahasiswas.nim')
                ->on('operasi_umums.tahun_akademik', '=', 'mahasiswas.tahun_akademik');
        })
            ->join('pelanggarans', 'operasi_umums.pelanggaran', '=', 'pelanggarans.kodePelanggaran') // Join ke tabel pelanggarans
            ->where('operasi_umums.status_pelanggaran', '!=', 'Dibatalkan'); // Filter status "Dibatalkan"

        // Filter berdasarkan tanggal jika parameter 'tanggal' ada dan tidak kosong
        if ($request->filled('tanggal')) {
            $query->whereDate('operasi_umums.created_at', $request->tanggal);
        }

        // Filter berdasarkan tingkat (mengambil inisial kelas)
        if ($request->filled('tingkat')) {
            $query->whereRaw('LEFT(kelas.nama_kelas, 1) = ?', [$request->tingkat]);
        }

        // Filter berdasarkan nama mahasiswa
        if ($request->filled('nama')) {
            $query->where('mahasiswas.nama', 'like', '%' . $request->nama . '%');
        }

        // Ambil data berdasarkan filter
        $data = $query->select(
            'operasi_umums.created_at as tanggal',
            'operasi_umums.created_at',
            'operasi_umums.updated_at',
            'operasi_umums.nim',
            'mahasiswas.nama',
            'kelas.nama_kelas as kelas',
            'pelanggarans.namaPelanggaran as pelanggaran', // Mengambil namaPelanggaran dari tabel pelanggarans
            'operasi_umums.nama_pencatat',
            'operasi_umums.status_pelanggaran',
            'operasi_umums.id'
        )
            ->orderBy('operasi_umums.created_at', 'desc')
            ->paginate(30);

        return view('operasiumum.laporanumum', compact('data'));
    }

    public function downloadFilteredData(Request $request, $format, \Barryvdh\DomPDF\PDF $pdfInstance)
    {
        $tanggal = $request->input('tanggal', null); // Nilai default null jika tidak ada filter tanggal

        // Query untuk mendapatkan data dengan join ke tabel Mahasiswa dan Pelanggarans
        $query = OperasiUmum::join('mahasiswas', function ($join) {
            $join->on('operasi_umums.nim', '=', 'mahasiswas.nim')
                ->on('operasi_umums.tahun_akademik', '=', 'mahasiswas.tahun_akademik'); // Join berdasarkan NIM dan tahun akademik
        })
            ->join('pelanggarans', 'operasi_umums.pelanggaran', '=', 'pelanggarans.kodePelanggaran') // Join ke tabel pelanggarans
            ->where('operasi_umums.status_pelanggaran', '!=', 'Dibatalkan') // Abaikan data dengan status "Dibatalkan"
            ->select(
                'operasi_umums.*',
                'kelas.nama_kelas as kelas',
                'mahasiswas.nama',
                'pelanggarans.namaPelanggaran as pelanggaran' // Ambil nama pelanggaran
            )
            ->join('kelas', 'mahasiswas.kelas_id', '=', 'kelas.id'); // Join ke tabel kelas untuk mendapatkan nama kelas

        // Terapkan filter tanggal jika ada
        if (!empty($tanggal)) {
            $query->whereDate('operasi_umums.created_at', $tanggal);
        }

        // Eksekusi query
        $data = $query->orderBy('operasi_umums.created_at')->get();

        // Handle jika data kosong
        if ($data->isEmpty()) {
            return redirect()->back()->withErrors('Tidak ada data yang sesuai dengan filter yang diberikan.');
        }

        if ($format === 'excel') {
            return Excel::download(new OperasiUmumExport($data, $tanggal), "laporan_operasi_umum_{$tanggal}.xlsx");
        } elseif ($format === 'pdf') {
            $pdf = $pdfInstance->loadView('exports.laporanumum_pdf', compact('data', 'tanggal'));
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download("laporan_operasi_umum_{$tanggal}.pdf");
        } elseif ($format === 'csv') {
            return Excel::download(new OperasiUmumExport($data, $tanggal), "laporan_operasi_umum_{$tanggal}.csv", \Maatwebsite\Excel\Excel::CSV);
        } else {
            return redirect()->back()->withErrors('Format tidak valid!');
        }
    }
}
