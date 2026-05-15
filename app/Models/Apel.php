<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Apel extends Model
{
    use HasFactory;

    protected $table = 'apel';

    // Menggunakan fillable lebih aman untuk memastikan kolom baru (waktu_apel) terdaftar
    protected $fillable = [
        'nama_apel',
        'tanggal_apel',
        'waktu_apel'
    ];

    // Casting agar Laravel otomatis mengubah string tanggal menjadi objek Carbon
    protected $casts = [
        'tanggal_apel' => 'date',
    ];

    /**
     * Relasi ke Kelas peserta apel (Many-to-Many)
     * Tabel Pivot: apel_kelas
     */
    public function kelasPeserta()
    {
        return $this->belongsToMany(Kelas::class, 'apel_kelas', 'apel_id', 'kelas_id');
    }

    /**
     * Relasi ke Petugas SPD (Many-to-Many)
     * Tabel Pivot: apel_petugas
     * Menggunakan spd_nas sebagai pengenal unik petugas
     */
    public function petugasSpd()
    {
        // Parameter: (ModelTujuan, NamaTabelPivot, FK_Tabel_Ini, FK_Tabel_Tujuan, Key_Tabel_Ini, Key_Tabel_Tujuan)
        return $this->belongsToMany(SPD::class, 'apel_petugas', 'apel_id', 'spd_nas', 'id', 'nas');
    }
}

// class Apel extends Model
// {
//     use HasFactory;

//     protected $table = 'apel';
//     protected $fillable = [
//         'nama_apel',
//         'tingkat',
//         'tanggal_apel',
//         'waktu_apel',
//     ];

//     protected $casts = [
//         'tingkat'=>'integer',
//         'tanggal_apel'=>'date',
//         'waktu_apel'=>'datetime',
//     ];
// }
