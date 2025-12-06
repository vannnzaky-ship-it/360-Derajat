<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siklus extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika berbeda dari 'siklus' (plural)
    protected $table = 'siklus'; 

    protected $fillable = [
        'tahun_ajaran',
        'semester',
        'persen_diri', // Tambahkan
        'persen_atasan',
        'persen_rekan',
        'persen_bawahan',
        'status',
    ];

    public function skemaPenilaians()
    {
        return $this->hasMany(SkemaPenilaian::class);
    }
}