<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Apel extends Model
{
    use HasFactory;

    protected $table = 'apel';
    protected $fillable = [
        'nama_apel',
        'tingkat',
        'tanggal_apel',
        'waktu_apel',
    ];

    protected $casts = [
        'tingkat'=>'integer',
    ];
}
