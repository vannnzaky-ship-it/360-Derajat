<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianSkor extends Model
{
    use HasFactory;

    protected $table = 'penilaian_skor';
    protected $guarded = ['id'];

    // Relasi balik ke Alokasi (Induknya)
    public function alokasi()
    {
        return $this->belongsTo(PenilaianAlokasi::class, 'penilaian_alokasi_id');
    }

    // Relasi ke Pertanyaan (Untuk tahu ini nilai kompetensi apa)
    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class, 'pertanyaan_id');
    }
}