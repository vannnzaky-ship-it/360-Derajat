<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Import

class Pegawai extends Model
{
    use HasFactory;
    protected $table = 'pegawai';
    protected $fillable = ['user_id', 'nip','no_hp']; // Hapus 'jabatan_id'

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi Many-to-Many ke Jabatan.
     * Satu pegawai bisa punya banyak jabatan.
     */
    public function jabatans(): BelongsToMany
    {
        // Nama tabel pivot: pegawai_jabatan
        // Foreign key tabel ini (pegawai): pegawai_id
        // Foreign key tabel relasi (jabatan): jabatan_id
        return $this->belongsToMany(Jabatan::class, 'pegawai_jabatan', 'pegawai_id', 'jabatan_id'); 
    }
}