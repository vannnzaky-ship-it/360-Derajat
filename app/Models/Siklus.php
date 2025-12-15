<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne; // <--- JANGAN LUPA IMPORT INI

class Siklus extends Model
{
    use HasFactory;

    protected $table = 'siklus'; 

    protected $fillable = [
        'tahun_ajaran',
        'semester',
        'persen_diri',
        'persen_atasan',
        'persen_rekan',
        'persen_bawahan',
        'status',
    ];

    public function skemaPenilaians()
    {
        return $this->hasMany(SkemaPenilaian::class);
    }

    // --- TAMBAHKAN BAGIAN INI (PENTING) ---
    public function penilaianSession(): HasOne
    {
        // Satu Siklus memiliki Satu Sesi Penilaian (Random Penilai)
        return $this->hasOne(PenilaianSession::class, 'siklus_id');
    }
}