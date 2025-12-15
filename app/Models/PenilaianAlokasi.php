<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenilaianAlokasi extends Model
{
    protected $table = 'penilaian_alokasi';
    protected $guarded = ['id'];

    // Relasi ke User Penilai
    public function penilai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke User yang Dinilai (Target)
    public function target(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    // --- TAMBAHKAN BAGIAN INI ---
    // Relasi ke Sesi Penilaian (Untuk cek status Open/Closed)
    public function penilaianSession(): BelongsTo
    {
        return $this->belongsTo(PenilaianSession::class, 'penilaian_session_id');
    }

    // app/Models/PenilaianAlokasi.php

public function jabatan()
{
    return $this->belongsTo(Jabatan::class, 'jabatan_id');
}

// Relasi ke Jabatan si Penilai (Untuk membedakan Ka BAK vs Ka Prodi)
public function penilaiJabatan()
{
    return $this->belongsTo(Jabatan::class, 'penilai_jabatan_id');
}
}