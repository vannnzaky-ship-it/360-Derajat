<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kompetensi extends Model
{
    use HasFactory;

    protected $table = 'kompetensi'; // Sesuai database kamu
    protected $guarded = ['id'];

    // --- TAMBAHKAN FUNGSI INI ---
    public function pertanyaans(): HasMany
    {
        return $this->hasMany(Pertanyaan::class);
    }
}