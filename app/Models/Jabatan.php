<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Import

class Jabatan extends Model
{
    use HasFactory;
    protected $table = 'jabatan';
    protected $fillable = [ 
        'nama_jabatan', 
        'bidang', 
        'parent_id', 
        'status', // Tambahkan status jika belum ada
        'is_singleton' // Tambahkan is_singleton
    ];

    /**
     * Relasi Many-to-Many ke Pegawai.
     * Satu jabatan bisa dimiliki banyak pegawai (kecuali singleton).
     */
     public function pegawais(): BelongsToMany
    {
        // Nama tabel pivot: pegawai_jabatan
        // Foreign key tabel ini (jabatan): jabatan_id
        // Foreign key tabel relasi (pegawai): pegawai_id
        return $this->belongsToMany(Pegawai::class, 'pegawai_jabatan', 'jabatan_id', 'pegawai_id');
    }

    // Relasi hierarki (tetap)
    public function children(): HasMany 
    {
         return $this->hasMany(Jabatan::class, 'parent_id');
    }
    public function parent(): BelongsTo 
    {
        return $this->belongsTo(Jabatan::class, 'parent_id');
    }
}