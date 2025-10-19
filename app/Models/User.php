<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // <-- Ini untuk login
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    // ... (use Notifiable, dll.)

    protected $fillable = ['name', 'email', 'password'];
    
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

    // Fungsi helper untuk cek role
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }
}