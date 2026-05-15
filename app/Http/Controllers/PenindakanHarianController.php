<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\SPD;
use App\Models\Mahasiswa;
use App\Models\Pelanggaran;
use Illuminate\Http\Request;
use App\Models\PenindakanHarian;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PenindakanHarianExport;
use App\Http\Requests\StorePenindakanHarianRequest;
use App\Http\Requests\UpdatePenindakanHarianRequest;

class PenindakanHarianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = PenindakanHarian::join('mahasiswas', function ($join) {
            $join->on('penindakan_harian.nim', '=', 'mahasiswas.nim')
                ->on('penindakan_harian.tahun_akademik', '=', 'mahasiswas.tahun_akademik');
        })
            ->join('kelas', 'mahasiswas.kelas_id', '=', 'kelas.id') // Join ke tabel kelas untuk mendapatkan nama kelas
            ->join('pelanggarans', 'penindakan_harian.pelanggaran', '=', 'pelanggarans.kodePelanggaran') // Join ke tabel pelanggarans
            ->where('penindakan_harian.status_pelanggaran', '!=', 'Dibatalkan') // Filter status "Dibatalkan"
            ->select(
                'penindakan_harian.created_at as tanggal',
                'penindakan_harian.created_at',
                'penindakan_harian.updated_at',
                'penindakan_harian.nim',
                'mahasiswas.nama',
                'kelas.nama_kelas as kelas',
                'pelanggarans.namaPelanggaran as pelanggaran', // Mengambil namaPelanggaran dari tabel pelanggarans
                'penindakan_harian.nama_pencatat',
                'penindakan_harian.status_pelanggaran',
                'penindakan_harian.id'
            )
            ->orderBy('penindakan_harian.created_at', 'desc')
            ->paginate(30);

        return view('penindakanharian.laporanharian', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pelanggarans = Pelanggaran::all();

        return view('penindakanharian.catatharian', compact('pelanggarans'));
    }

    /**
     * Store a newly created resource in storage.
     */
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

        PenindakanHarian::insert($data);
        app(EmailController::class)->sendEmail($request);

        session()->flash('success', 'Data berhasil ditambahkan');
        return redirect()->route('catat.harian');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $penindakanHarian = PenindakanHarian::select(
            'penindakan_harian.*',
            'mahasiswas.nama',
            'kelas.nama_kelas as kelas',
            'pelanggarans.namaPelanggaran as pelanggaran' // Ambil nama pelanggaran dari tabel pelanggarans
        )
            ->join('mahasiswas', function ($join) {
                $join->on('penindakan_harian.nim', '=', 'mahasiswas.nim')
                    ->on('penindakan_harian.tahun_akademik', '=', 'mahasiswas.tahun_akademik'); // Join berdasarkan NIM dan tahun akademik
            })
            ->join('kelas', 'mahasiswas.kelas_id', '=', 'kelas.id') // Join ke tabel kelas untuk mendapatkan nama kelas
            ->leftJoin('pelanggarans', 'penindakan_harian.pelanggaran', '=', 'pelanggarans.kodePelanggaran') // Join ke pelanggarans untuk mendapatkan nama pelanggaran
            ->where('penindakan_harian.id', $id) // Filter berdasarkan ID
            ->firstOrFail();

        $pelanggarans = Pelanggaran::all();
        $selectedPelanggaran = $penindakanHarian->pelanggaran;
        return view('penindakanharian.editcatatharian', compact('operasiUmum', 'pelanggarans', 'selectedPelanggaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nim' => 'required|regex:/^[0-9]{9}$/',
            'pelanggaran' => 'required',
        ]);

        $pelanggarans = Pelanggaran::all();

        // Mencari data PenindakanHarian berdasarkan ID
        $penindakanHarian = PenindakanHarian::where('id', $id)->firstOrFail();

        // Cek apakah mahasiswa dengan NIM dan tahun akademik sesuai ada
        $mahasiswa = Mahasiswa::where('nim', $request->nim)
            ->where('tahun_akademik', $penindakanHarian->tahun_akademik)
            ->first();

        if (!$mahasiswa) {
            return redirect()->route('catateditharian', $id)->with('error', 'Mahasiswa tidak ditemukan atau tidak sesuai dengan tahun akademik');
        }

        $kodePelanggaran = $request->pelanggaran;
        $pencatat = SPD::where('nas', Auth::user()->username)->value('nama_anggota');
        $tahunAkademik = $request->input('tahun_akademik', $penindakanHarian->tahun_akademik);

        // Batalkan data lama dengan mengubah status menjadi "Dibatalkan"
        $penindakanHarian->update([
            'status_pelanggaran' => 'Dibatalkan',
            'updated_at' => now()
        ]);

        // Tambahkan data baru
        $currentTime = now(); // Waktu yang sama untuk created_at dan updated_at
        PenindakanHarian::create([
            'nim' => $mahasiswa->nim,
            'pelanggaran' => $kodePelanggaran, // Update pelanggaran sesuai dengan data yang diterima
            'nama_pencatat' => $pencatat,
            'created_at' => $currentTime,
            'updated_at' => $currentTime, // Waktu yang sama untuk created_at dan updated_at
            'tahun_akademik' => $tahunAkademik,
            'status_pelanggaran' => 'Ditambahkan'
        ]);

        app(EmailController::class)->sendEmail($request);

        // Redirect ke halaman laporan dengan pesan sukses
        return redirect()->route('laporanharian')->with('success', 'Data berhasil diperbarui');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $penindakanHarian = PenindakanHarian::find($id);

            // Jika data tidak ditemukan, kembalikan respon dengan status 404
            if (!$penindakanHarian) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            // Update status menjadi "dibatalkan"
            $penindakanHarian->update([
                'status_pelanggaran' => 'Dibatalkan', // Pastikan kolom ini ada di tabel
                'updated_at' => now() // Perbarui waktu terakhir diupdate
            ]);

            return redirect()->route('laporanharian');
        } catch (Exception $e) {
            return redirect()->route('laporanharian')->with('error', 'Data gagal dihapus: ' . $e->getMessage());
        }
    }

    public function filter(Request $request)
    {
        $query = PenindakanHarian::join('mahasiswas', function ($join) {
            $join->on('penindakan_harian.nim', '=', 'mahasiswas.nim')
                ->on('penindakan_harian.tahun_akademik', '=', 'mahasiswas.tahun_akademik');
        })
            ->join('pelanggarans', 'penindakan_harian.pelanggaran', '=', 'pelanggarans.kodePelanggaran') // Join ke tabel pelanggarans
            ->where('penindakan_harian.status_pelanggaran', '!=', 'Dibatalkan'); // Filter status "Dibatalkan"

        // Filter berdasarkan tanggal jika parameter 'tanggal' ada dan tidak kosong
        if ($request->filled('tanggal')) {
            $query->whereDate('penindakan_harian.created_at', $request->tanggal);
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
            'penindakan_harian.created_at as tanggal',
            'penindakan_harian.created_at',
            'penindakan_harian.updated_at',
            'penindakan_harian.nim',
            'mahasiswas.nama',
            'kelas.nama_kelas as kelas',
            'pelanggarans.namaPelanggaran as pelanggaran', // Mengambil namaPelanggaran dari tabel pelanggarans
            'penindakan_harian.nama_pencatat',
            'penindakan_harian.status_pelanggaran',
            'penindakan_harian.id'
        )
            ->orderBy('penindakan_harian.created_at', 'desc')
            ->paginate(30);

        // Kirim data ke view
        return view('penindakanharian.laporanharian', compact('data'));
    }

    public function downloadFilteredData(Request $request, $format, \Barryvdh\DomPDF\PDF $pdfInstance)
    {
        $tanggal = $request->input('tanggal', null); // Nilai default null jika tidak ada filter tanggal

        // Query untuk mendapatkan data dengan join ke tabel Mahasiswa dan Pelanggarans
        $query = PenindakanHarian::join('mahasiswas', function ($join) {
            $join->on('penindakan_harian.nim', '=', 'mahasiswas.nim')
                ->on('penindakan_harian.tahun_akademik', '=', 'mahasiswas.tahun_akademik'); // Join berdasarkan NIM dan tahun akademik
        })
            ->join('pelanggarans', 'penindakan_harian.pelanggaran', '=', 'pelanggarans.kodePelanggaran') // Join ke tabel pelanggarans
            ->where('penindakan_harian.status_pelanggaran', '!=', 'Dibatalkan') // Abaikan data dengan status "Dibatalkan"
            ->select(
                'penindakan_harian.*',
                'kelas.nama_kelas as kelas',
                'mahasiswas.nama',
                'pelanggarans.namaPelanggaran as pelanggaran' // Ambil nama pelanggaran
            )
            ->join('kelas', 'mahasiswas.kelas_id', '=', 'kelas.id'); // Join ke tabel kelas untuk mendapatkan nama kelas

        // Terapkan filter tanggal jika ada
        if (!empty($tanggal)) {
            $query->whereDate('penindakan_harian.created_at', $tanggal);
        }

        // Eksekusi query
        $data = $query->orderBy('penindakan_harian.created_at')->get();

        // Handle jika data kosong
        if ($data->isEmpty()) {
            return redirect()->back()->withErrors('Tidak ada data yang sesuai dengan filter yang diberikan.');
        }

        if ($format === 'excel') {
            return Excel::download(new PenindakanHarianExport($data, $tanggal), "laporan_penindakan_harian_{$tanggal}.xlsx");
        } elseif ($format === 'pdf') {
            $pdf = $pdfInstance->loadView('exports.laporanharian_pdf', compact('data', 'tanggal'));
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download("laporan_penindakan_harian_{$tanggal}.pdf");
        } elseif ($format === 'csv') {
            return Excel::download(new PenindakanHarianExport($data, $tanggal), "laporan_penindakan_harian_{$tanggal}.csv", \Maatwebsite\Excel\Excel::CSV);
        } else {
            return redirect()->back()->withErrors('Format tidak valid!');
        }
    }
}
