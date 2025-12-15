<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianAlokasi extends Model
{
    use HasFactory;

    protected $table = 'penilaian_alokasi';
    protected $guarded = ['id'];

    // Relasi ke User Penilai
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke User Target
    public function target()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    // Relasi ke Jabatan Target
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }
    
    // Relasi ke Jabatan Penilai
    public function penilaiJabatan()
    {
        return $this->belongsTo(Jabatan::class, 'penilai_jabatan_id');
    }

    // Relasi ke Sesi
    public function penilaianSession()
    {
        return $this->belongsTo(PenilaianSession::class, 'penilaian_session_id');
    }

    // ==== TAMBAHKAN KODE INI (YANG HILANG) ====
    public function skors()
    {
        // Ini menghubungkan Alokasi ke tabel 'penilaian_skor'
        return $this->hasMany(PenilaianSkor::class, 'penilaian_alokasi_id');
    }
    // ===========================================
}