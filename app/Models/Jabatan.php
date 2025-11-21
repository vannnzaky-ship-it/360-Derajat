<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Jabatan extends Model
{
    use HasFactory;

    // PENTING: Karena nama tabel Anda 'jabatan' (bukan jabatans), ini wajib ada.
    protected $table = 'jabatan';

    protected $fillable = [ 
    'nama_jabatan', 
    'bidang', 
    'parent_id', 
    'status', 
    'is_singleton',
    // TAMBAHKAN INI:
    'level',
    'urutan'
];

    /**
     * Relasi Many-to-Many ke Pegawai.
     */
    public function pegawais(): BelongsToMany
    {
        return $this->belongsToMany(Pegawai::class, 'pegawai_jabatan', 'jabatan_id', 'pegawai_id');
    }

    /**
     * Relasi untuk mengambil BAWAHAN.
     * Ditambahkan orderBy('urutan') agar saat dipanggil, bawahan langsung urut sesuai tata letak.
     */
    public function children(): HasMany 
    {
         return $this->hasMany(Jabatan::class, 'parent_id')->orderBy('urutan', 'asc');
    }

    /**
     * Relasi untuk mengambil ATASAN.
     */
    public function parent(): BelongsTo 
    {
        return $this->belongsTo(Jabatan::class, 'parent_id');
    }
    
    /**
     * Helper untuk mengambil semua bawahan secara rekursif (bertingkat).
     * Berguna untuk tampilan pohon struktur organisasi nanti.
     */
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }
}