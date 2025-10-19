<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jabatan extends Model
{
    protected $table = 'jabatan';
    protected $fillable = ['nama_jabatan', 'bidang', 'parent_id'];

    public function pegawai(): HasMany
    {
        return $this->hasMany(Pegawai::class);
    }
    
    // Relasi untuk hierarki
    public function children(): HasMany
    {
        return $this->hasMany(Jabatan::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'parent_id');
    }
}
