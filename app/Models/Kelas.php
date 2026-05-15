<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $fillable = ['nama_kelas'];

    // Satu kelas memiliki banyak mahasiswa
    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class, 'kelas_id');
    }

    // Relasi Many-to-Many ke jadwal Apel
    public function apel()
    {
        return $this->belongsToMany(Apel::class, 'apel_kelas', 'kelas_id', 'apel_id');
    }
}
