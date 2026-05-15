<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    // public function getMahasiswaRutin(Request $request)
    // {

    //     $nim = $request->input('nim');
    //     $sessionTingkat = session('tingkat'); // Ambil tingkat dari sesi pengguna (bisa null)

    //     // Cari mahasiswa berdasarkan NIM
    //     $mahasiswa = Mahasiswa::where('nim', $nim)->first();

    //     if ($mahasiswa) {
    //         // Tentukan tingkat berdasarkan kelas (contoh: karakter pertama dari kelas)
    //         $mahasiswaTingkat = substr($mahasiswa->kelas, 0, 1);

    //         // Validasi hanya jika session tingkat tersedia
    //         if ($sessionTingkat === null || $mahasiswaTingkat == $sessionTingkat) {
    //             return response()->json([
    //                 'nama' => $mahasiswa->nama,
    //                 'kelas' => $mahasiswa->kelas,
    //                 'tingkat' => $mahasiswaTingkat,
    //             ]);
    //         } else {
    //             // Jika tingkat tidak sesuai, kembalikan error 404
    //             return response()->json([
    //                 'message' => 'Tingkat tidak sesuai dengan sesi.',
    //             ], 404);
    //         }
    //     } else {
    //         // Jika mahasiswa tidak ditemukan, kembalikan error 404
    //         return response()->json([
    //             'message' => 'Mahasiswa tidak ditemukan.',
    //         ], 404);
    //     }
    // }
    public function getMahasiswa(Request $request)
    {
        // if (session()->has('tingkat')) {
        //     session()->forget('tingkat'); // Hapus sesi tingkat
        // }

        $nim = $request->input('nim');
        // $sessionTingkat = session('tingkat'); // Ambil tingkat dari sesi pengguna (bisa null)

        // Cari mahasiswa berdasarkan NIM
        $mahasiswa = Mahasiswa::with('kelas')->where('nim', $nim)->first();

        if ($mahasiswa) {
            // Karena tabel kelas terpisah, kita ambil 'nama_kelas' dari relasinya
            $nama_kelas = $mahasiswa->kelas ? $mahasiswa->kelas->nama_kelas : '-';
            
            return response()->json([
                'nama' => $mahasiswa->nama,
                'kelas' => $nama_kelas, // Kirimkan string "1KS1", bukan ID
                'tingkat' => substr($nama_kelas, 0, 1) // Kirimkan inisial tingkat '1', '2', dst
            ]);
        } else {
            // Jika mahasiswa tidak ditemukan, kembalikan error 404
            return response()->json([
                'message' => 'Mahasiswa tidak ditemukan.',
            ], 404);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Mahasiswa $mahasiswa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mahasiswa $mahasiswa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mahasiswa $mahasiswa)
    {
        //
    }
}
