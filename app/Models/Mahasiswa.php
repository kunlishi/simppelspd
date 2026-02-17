<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mahasiswa extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'mahasiswas';
    protected $primaryKey = 'nim'; // Primary key, biasanya 'id'

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'nim',
        'nama',
        'kelas',
        'tahun_akademik'
    ];

    // Format default untuk timestamps (jika diperlukan custom)
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
