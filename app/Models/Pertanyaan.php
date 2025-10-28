<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import

class Pertanyaan extends Model
{
    use HasFactory;
    protected $table = 'pertanyaan'; // Nama tabel singular
    protected $fillable = [
        'kompetensi_id', 'teks_pertanyaan', 'untuk_diri', 'untuk_atasan',
        'untuk_rekan', 'untuk_bawahan', 'status',
    ];

    // Relasi ke model Kompetensi
    public function kompetensi(): BelongsTo
    {
        return $this->belongsTo(Kompetensi::class);
    }
}