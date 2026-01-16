<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
//use Spatie\Permission\Traits\HasRoles; // Opsional jika pakai Spatie murni

class User extends Authenticatable
{
    use HasFactory, Notifiable; 
    // use HasRoles; // Matikan dulu jika ingin pakai relasi manual di bawah

    protected $fillable = [
        'name', 
        'email', 
        'password',
        'profile_photo_path',
        'otp_code',
        'otp_expires_at',
    ];

    // Relasi ke Pegawai (Satu User punya satu data Pegawai)
    public function pegawai(): HasOne
    {
        return $this->hasOne(Pegawai::class);
    }

    // Relasi ke Role (Satu User bisa punya banyak Role)
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    // Fungsi helper untuk cek role (Manual Check)
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }
}