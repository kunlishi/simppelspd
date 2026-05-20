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
}