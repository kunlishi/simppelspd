<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KritikSaran extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'kritiksaran';

    protected $fillable = [
        'nama',
        'kritiksaran', // Kolom yang boleh diisi
    ];
}
