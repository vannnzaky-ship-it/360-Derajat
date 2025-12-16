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

    // ==========================================================
    // BAGIAN INI SANGAT PENTING (DUA-DUANYA HARUS ADA)
    // ==========================================================

    // 1. UNTUK FITUR LAMA (JANGAN DIHAPUS)
    // Digunakan oleh form penilaian karyawan / random generator
    public function target()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    // 2. UNTUK FITUR BARU (MONITORING ADMIN)
    // Digunakan oleh Controller DetailProgress.php
    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function targetJabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }
    // ==========================================================

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

    // Relasi ke Skor
    public function skors()
    {
        return $this->hasMany(PenilaianSkor::class, 'penilaian_alokasi_id');
    }
}