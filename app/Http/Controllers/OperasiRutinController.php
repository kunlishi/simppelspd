<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\SPD;
use App\Models\Mahasiswa;
use App\Models\Pelanggaran;
use Termwind\Components\Dd;
use App\Models\OperasiRutin;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\PDF;
use App\Exports\OperasiRutinExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class OperasiRutinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Mengambil data dari tabel operasi_rutin dengan join ke tabel mahasiswa, kelas, dan pelanggarans
        $data = OperasiRutin::join('mahasiswas', function ($join) {
            $join->on('operasi_rutin.nim', '=', 'mahasiswas.nim')
                ->on('operasi_rutin.tahun_akademik', '=', 'mahasiswas.tahun_akademik');
        })
            ->join('kelas', 'mahasiswas.kelas_id', '=', 'kelas.id') // Join ke tabel kelas
            ->join('pelanggarans', 'operasi_rutin.pelanggaran', '=', 'pelanggarans.kodePelanggaran') // Join ke tabel pelanggarans
            ->where('operasi_rutin.status_pelanggaran', '!=', 'Dibatalkan') // Filter status "Dibatalkan"
            ->select(
                'operasi_rutin.created_at as tanggal',
                'operasi_rutin.created_at',
                'operasi_rutin.updated_at',
                'operasi_rutin.nim',
                'mahasiswas.nama',
                'kelas.nama_kelas as kelas', // Mengambil nama_kelas dari tabel kelas
                'pelanggarans.namaPelanggaran as pelanggaran', // Mengambil namaPelanggaran dari tabel pelanggarans
                'operasi_rutin.nama_pencatat',
                'operasi_rutin.status_pelanggaran',
                'operasi_rutin.id'
            )
            ->orderBy('operasi_rutin.created_at', 'desc')
            ->paginate(30);

        // Mengirim data ke view
        return view('operasirutin.laporanrutin', compact('data'));
    }



    public function showForm(Request $request)
    {
        $pelanggarans = Pelanggaran::all();

        // Jika tidak berada di halaman showForm dan session tingkat ada, maka hapus session tingkat
        if (!request()->routeIs('catat')) {
            session()->forget('tingkat');
        }

        // Ambil nilai tingkat dari query string atau session
        $tingkat = $request->query('tingkat') ?? session('tingkat');

        // Jika tingkat ada di query string, simpan di session
        if ($tingkat) {
            session(['tingkat' => $tingkat]);
        }
        return view('operasirutin.catat', compact('pelanggarans', 'tingkat'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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

        OperasiRutin::insert($data);
        app(EmailController::class)->sendEmail($request);

        session()->flash('success', 'Data berhasil ditambahkan.');
        return redirect()->route('catat');
    }

    public function edit($id)
    {
        // Ambil data operasi_rutin beserta informasi mahasiswa dan kelas
        $operasiRutin = OperasiRutin::select(
            'operasi_rutin.*',
            'mahasiswas.nama',
            'kelas.nama_kelas as kelas', // Mengambil nama_kelas dari tabel kelas
            'pelanggarans.namaPelanggaran as pelanggaran' // Ambil nama pelanggaran dari tabel pelanggarans
        )
            ->join('mahasiswas', function ($join) {
                $join->on('operasi_rutin.nim', '=', 'mahasiswas.nim')
                    ->on('operasi_rutin.tahun_akademik', '=', 'mahasiswas.tahun_akademik'); // Join berdasarkan NIM dan tahun akademik
            })
            ->join('kelas', 'mahasiswas.kelas_id', '=', 'kelas.id') // Join ke tabel kelas
            ->leftJoin('pelanggarans', 'operasi_rutin.pelanggaran', '=', 'pelanggarans.kodePelanggaran') // Join ke pelanggarans untuk mendapatkan nama pelanggaran
            ->where('operasi_rutin.id', $id) // Filter berdasarkan ID operasi_rutin
            ->firstOrFail();

        // Ambil semua data pelanggarans untuk ditampilkan di dropdown/suggestion
        $pelanggarans = Pelanggaran::all();

        // Gunakan nama pelanggaran yang dipilih sebelumnya
        $selectedPelanggaran = $operasiRutin->pelanggaran;

        return view('operasirutin.editcatatrutin', compact('operasiRutin', 'pelanggarans', 'selectedPelanggaran'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nim' => 'required|regex:/^[0-9]{9}$/',
            'pelanggaran' => 'required',
        ]);

        $pelanggarans = Pelanggaran::all();

        // Mencari data OperasiRutin berdasarkan ID
        $operasiRutin = OperasiRutin::where('id', $id)->firstOrFail();

        // Cek apakah mahasiswa dengan NIM dan tahun akademik sesuai ada
        $mahasiswa = Mahasiswa::where('nim', $request->nim)
            ->where('tahun_akademik', $operasiRutin->tahun_akademik)
            ->first();

        if (!$mahasiswa) {
            return redirect()->route('catateditrutin', $id)->with('error', 'Mahasiswa tidak ditemukan atau tidak sesuai dengan tahun akademik');
        }

        $kodePelanggaran = $request->pelanggaran;
        $pencatat = SPD::where('nas', Auth::user()->username)->value('nama_anggota');
        $tahunAkademik = $request->input('tahun_akademik', $operasiRutin->tahun_akademik);


        // Batalkan data lama dengan mengubah status menjadi "Dibatalkan"
        $operasiRutin->update([
            'status_pelanggaran' => 'Dibatalkan',
            'updated_at' => now()
        ]);

        // Tambahkan data baru
        $currentTime = now(); // Waktu yang sama untuk created_at dan updated_at
        OperasiRutin::create([
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
        return redirect()->route('laporanrutin')->with('success', 'Data berhasil diperbarui');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // Cari data berdasarkan ID
            $operasiRutin = OperasiRutin::find($id);

            // Jika data tidak ditemukan, kembalikan respon dengan status 404
            if (!$operasiRutin) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            // Update status menjadi "dibatalkan"
            $operasiRutin->update([
                'status_pelanggaran' => 'Dibatalkan', // Pastikan kolom ini ada di tabel
                'updated_at' => now() // Perbarui waktu terakhir diupdate
            ]);

            return redirect()->route('laporanrutin');
        } catch (Exception $e) {
            return redirect()->route('laporanrutin')->with('error', 'Data gagal dihapus: ' . $e->getMessage());
        }
    }

    public function downloadFilteredData(Request $request, $format)
    {
        // Ambil filter dari request
        $tanggal = $request->input('tanggal', null); // Nilai default null jika tidak ada filter tanggal
        $tingkat = $request->input('tingkat', null); // Nilai default null jika tidak ada filter tingkat

        // Query untuk mendapatkan data dengan join ke tabel Mahasiswa dan Kelas
        $query = OperasiRutin::join('mahasiswas', function ($join) {
            $join->on('operasi_rutin.nim', '=', 'mahasiswas.nim')
                ->on('operasi_rutin.tahun_akademik', '=', 'mahasiswas.tahun_akademik'); // Join berdasarkan NIM dan tahun akademik
        })
            ->join('kelas', 'mahasiswas.kelas_id', '=', 'kelas.id') // Join ke tabel kelas
            ->join('pelanggarans', 'operasi_rutin.pelanggaran', '=', 'pelanggarans.kodePelanggaran') // Join ke tabel pelanggarans
            ->where('operasi_rutin.status_pelanggaran', '!=', 'dibatalkan') // Abaikan data dengan status "dibatalkan"
            ->select(
                'operasi_rutin.*',
                'kelas.nama_kelas as kelas', // Ambil nama_kelas
                'mahasiswas.nama',
                'pelanggarans.namaPelanggaran as pelanggaran' // Ambil nama pelanggaran
            );

        // Terapkan filter tanggal jika ada
        if (!empty($tanggal)) {
            $query->whereDate('operasi_rutin.created_at', $tanggal);
        }

        // Terapkan filter tingkat jika ada menggunakan LEFT pada nama_kelas
        if (!empty($tingkat)) {
            $query->whereRaw('LEFT(kelas.nama_kelas, 1) = ?', [$tingkat]);
        }

        // Eksekusi query
        $data = $query->orderBy('operasi_rutin.created_at')->get();

        // Handle jika data kosong
        if ($data->isEmpty()) {
            return redirect()->back()->withErrors('Tidak ada data yang sesuai dengan filter yang diberikan.');
        }

        // Ekspor data berdasarkan format
        if ($format === 'excel') {
            return Excel::download(new OperasiRutinExport($data, $tanggal, $tingkat), "laporan_operasi_rutin_{$tanggal}.xlsx");
        } elseif ($format === 'pdf') {
            $pdf = PDF::loadView('exports.laporanrutin_pdf', compact('data', 'tanggal', 'tingkat'));
            return $pdf->download("laporan_operasi_rutin_{$tanggal}.pdf");
        } elseif ($format === 'csv') {
            return Excel::download(new OperasiRutinExport($data, $tanggal, $tingkat), "laporan_operasi_rutin_{$tanggal}.csv", \Maatwebsite\Excel\Excel::CSV);
        } else {
            return redirect()->back()->withErrors('Format tidak valid!');
        }
    }


    public function filter(Request $request)
    {
        // Ambil query builder untuk OperasiRutin dengan join ke tabel Mahasiswa, Kelas, dan Pelanggarans
        $query = OperasiRutin::join('mahasiswas', function ($join) {
            $join->on('operasi_rutin.nim', '=', 'mahasiswas.nim')
                ->on('operasi_rutin.tahun_akademik', '=', 'mahasiswas.tahun_akademik');
        })
            ->join('kelas', 'mahasiswas.kelas_id', '=', 'kelas.id') // Join ke tabel kelas
            ->join('pelanggarans', 'operasi_rutin.pelanggaran', '=', 'pelanggarans.kodePelanggaran') // Join ke tabel pelanggarans
            ->where('operasi_rutin.status_pelanggaran', '!=', 'Dibatalkan'); // Filter status "Dibatalkan"

        // Filter berdasarkan tanggal jika parameter 'tanggal' ada dan tidak kosong
        if ($request->filled('tanggal')) {
            $query->whereDate('operasi_rutin.created_at', $request->tanggal);
        }

        // Filter berdasarkan tingkat (mengambil inisial dari nama_kelas)
        if ($request->filled('tingkat')) {
            $query->whereRaw('LEFT(kelas.nama_kelas, 1) = ?', [$request->tingkat]);
        }

        // Filter berdasarkan nama mahasiswa
        if ($request->filled('nama')) {
            $query->where('mahasiswas.nama', 'like', '%' . $request->nama . '%');
        }

        // Ambil data berdasarkan filter
        $data = $query->select(
            'operasi_rutin.created_at as tanggal',
            'operasi_rutin.created_at',
            'operasi_rutin.updated_at',
            'operasi_rutin.nim',
            'mahasiswas.nama',
            'kelas.nama_kelas as kelas', // Mengambil nama_kelas dari tabel kelas
            'pelanggarans.namaPelanggaran as pelanggaran', // Mengambil namaPelanggaran dari tabel pelanggarans
            'operasi_rutin.nama_pencatat',
            'operasi_rutin.status_pelanggaran',
            'operasi_rutin.id'
        )
            ->orderBy('operasi_rutin.created_at', 'desc')
            ->paginate(30);

        // Kirim data ke view
        return view('operasirutin.laporanrutin', compact('data'));
    }
}