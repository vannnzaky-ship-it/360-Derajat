<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kompetensi extends Model
{
    use HasFactory;
    protected $table = 'kompetensi'; // Nama tabel singular
    protected $fillable = [
        'nama_kompetensi', 'deskripsi', 'bobot', 'status',
    ];
}