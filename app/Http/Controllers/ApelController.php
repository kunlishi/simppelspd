<?php

namespace App\Http\Controllers;

use App\Models\Apel;
use App\Models\Presensi;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class ApelController extends Controller
{
    public function index()
    {
        $apels = Apel::orderBy('tanggal_apel', 'desc')->get();
        return view('presensi.admin.index', compact('apels'));
    }

    public function create()
    {
        return view('presensi.admin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_apel' => 'required|string|max:255',
            'tingkat' => 'required|integer|between:0,4',
            'tanggal_apel' => 'required|date',
            'waktu_apel' => 'required',
        ]);

        $apel = Apel::create($request->all());

        Mahasiswa::whereRaw('LEFT(kelas, 1) = ?', [$apel->tingkat])->chunk(100, function ($mahasiswas) use ($apel) {
        foreach ($mahasiswas as $mahasiswa) {
            Presensi::create([
                'apel_id' => $apel->id,
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->nama,
                'kelas' => $mahasiswa->kelas,
                'status' => 'tidak_hadir',
            ]);
        }
        });

        return redirect()->route('apel.index')->with('success', 'Apel created successfully.');
    }

    public function edit($id)
    {
        $apel = Apel::findOrFail($id);
        return view('presensi.admin.edit', compact('apel'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_apel' => 'required|string|max:255',
            'tingkat' => 'required|integer|between:0,4',
            'tanggal_apel' => 'required|date',
            'waktu_apel' => 'required',
        ]);

        $apel = Apel::findOrFail($id);
        $apel->update($request->all());

        return redirect()->route('apel.index')->with('success', 'Apel updated successfully.');
    }

    public function destroy($id)
    {
        $apel = Apel::findOrFail($id);
        $apel->delete();

        return redirect()->route('apel.index')->with('success', 'Apel deleted successfully.');
    }   
}
