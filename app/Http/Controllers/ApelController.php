<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Apel;
use App\Models\Presensi;
use App\Models\Mahasiswa;
use App\Models\Kelas;
use App\Models\SPD;

class ApelController extends Controller
{
    public function index()
    {
        $apels = Apel::orderBy('tanggal_apel', 'desc')->get();
        return view('presensi.admin.index', compact('apels'));
    }

    public function create()
    {
        // Mengambil semua data kelas untuk pilihan peserta apel
        $kelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        
        // Mengambil semua data petugas SPD untuk pilihan petugas jaga
        //$petugasSpd = SPD::orderBy('nas', 'asc')->get(); // Sesuaikan 'nas' dengan kolom nama jika ada

        return view('presensi.admin.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input form
        $request->validate([
            'nama_apel' => 'required|string|max:255',
            'tanggal_apel'   => 'required|date',
            // Pastikan name di input HTML adalah array: name="kelas_ids[]"
            'kelas_ids' => 'required|array', 
            'kelas_ids.*' => 'exists:kelas,id',
            // Pastikan name di input HTML adalah array: name="spd_nas[]"
            //'spd_nas'   => 'nullable|array', 
            //'spd_nas.*' => 'exists:spd,nas', // Memastikan NAS yang diinput benar-benar ada
        ]);

        // 2. Gunakan DB Transaction untuk mencegah data tersimpan separuh jika terjadi error
        DB::beginTransaction();
        try {
            // Buat master Apel
            $apel = Apel::create([
                'nama_apel' => $request->nama_apel,
                'tanggal_apel'   => $request->tanggal_apel,
                'waktu_apel' => $request->waktu_apel,
                // tambahkan field waktu/keterangan lain jika ada di tabel apel
            ]);

            // Simpan relasi kelas peserta (Otomatis masuk ke tabel apel_kelas)
            $apel->kelasPeserta()->attach($request->kelas_ids);

            // Simpan relasi petugas jaga (Otomatis masuk ke tabel apel_petugas)
            // if ($request->has('spd_nas')) {
            //     $apel->petugasSpd()->attach($request->spd_nas);
            // }

            DB::commit();
            return redirect()->route('apel.index')->with('success', 'Jadwal Apel dan Penugasan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            dd("ERROR DATABASE " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // Menampilkan halaman Edit
    public function edit($id)
    {
        $apel = Apel::with(['kelasPeserta'])->findOrFail($id);
        
        // Pengecekan Waktu: Kunci jika sudah H-90 menit (1.5 jam)
        $tz = 'Asia/Jakarta';
        $waktu_target = \Carbon\Carbon::parse($apel->tanggal_apel->format('Y-m-d') . ' ' . $apel->waktu_apel, $tz);
        $batas_edit = $waktu_target->copy()->subMinutes(90);

        if (now($tz) >= $batas_edit) {
            return redirect()->route('apel.index')->with('error', 'Jadwal sudah dikunci. Perubahan tidak diizinkan 2 jam sebelum apel dimulai.');
        }

        $kelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        //$petugasSpd = SPD::orderBy('nas', 'asc')->get();

        // Ambil array ID kelas dan NAS petugas yang sudah terpilih sebelumnya
        $selectedKelas = $apel->kelasPeserta->pluck('id')->toArray();
        //$selectedSpd = $apel->petugasSpd->pluck('nas')->toArray();

        return view('presensi.admin.edit', compact('apel', 'kelas', 'selectedKelas'));
    }

    // Menyimpan perubahan ke Database
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_apel'    => 'required|string|max:255',
            'tanggal_apel' => 'required|date',
            'waktu_apel'   => 'required',
            'kelas_ids'    => 'required|array',
            'kelas_ids.*'  => 'exists:kelas,id',
            //'spd_nas'      => 'nullable|array',
            //'spd_nas.*'    => 'exists:spd,nas',
        ]);

        DB::beginTransaction();
        try {
            $apel = Apel::findOrFail($id);

            // Proteksi Tambahan pada Method Update
            $tz = 'Asia/Jakarta';
            $waktu_target = \Carbon\Carbon::parse($apel->tanggal_apel->format('Y-m-d') . ' ' . $apel->waktu_apel, $tz);
            if (now($tz) >= $waktu_target->subMinutes(90)) {
                return redirect()->route('apel.index')->with('error', 'Gagal memperbarui: Waktu pengeditan sudah habis.');
            }
            
            // 1. Update data master Apel
            $apel->update([
                'nama_apel'    => $request->nama_apel,
                'tanggal_apel' => $request->tanggal_apel,
                'waktu_apel'   => $request->waktu_apel,
            ]);

            // 2. Sinkronisasi (Update) relasi kelas peserta
            $apel->kelasPeserta()->sync($request->kelas_ids);

            // 3. Sinkronisasi relasi petugas jaga
            // if ($request->has('spd_nas')) {
            //     $apel->petugasSpd()->sync($request->spd_nas);
            // } else {
            //     // Jika tidak ada petugas yang dipilih, kosongkan relasinya
            //     $apel->petugasSpd()->sync([]); 
            // }

            DB::commit();
            return redirect()->route('apel.index')->with('success', 'Jadwal Apel berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $apel = Apel::findOrFail($id);

            // Catatan: Jika di Migration kamu sudah menggunakan ->onDelete('cascade'),
            // data di tabel pivot apel_kelas dan apel_petugas akan terhapus otomatis.
            // Jika tidak, Laravel akan tetap menghapusnya dari tabel master 'apel'.
            
            $apel->delete();

            DB::commit();
            
            // Mengirim feedback sukses yang akan ditangkap oleh SweetAlert2 di view
            return redirect()->route('apel.index')->with('success', 'Jadwal Apel berhasil dihapus secara permanen.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Mengirim feedback error jika ada kendala database
            return redirect()->route('apel.index')->with('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }
}
