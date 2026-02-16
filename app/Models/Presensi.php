<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    protected $fillable = [
        'apel_id',
        'nim',
        'nama',
        'kelas',
        'status',
        'nama_petugas',
    ];

    public function apel()
    {
        return $this->belongsTo(Apel::class, 'apel_id', 'id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }
}
