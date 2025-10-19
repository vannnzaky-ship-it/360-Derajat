<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Jabatan;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Hash; // <-- Import 'Hash'
class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil Roles
        $roleSuperadmin = Role::where('name', 'superadmin')->first();
        $rolePeninjau = Role::where('name', 'peninjau')->first();

        // Ambil Jabatan (contoh)
        $jabatanDirektur = Jabatan::where('nama_jabatan', 'Direktur')->first();
        $jabatanWadir1 = Jabatan::where('nama_jabatan', 'Wakil Direktur I')->first();

        // 1. Buat User Super Admin
        $superadminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@polkam.ac.id',
            'password' => Hash::make('password') // Ganti password ini
        ]);
        // Hubungkan role
        $superadminUser->roles()->attach($roleSuperadmin);
        // Buat data pegawainya
        Pegawai::create([
            'user_id' => $superadminUser->id,
            'jabatan_id' => $jabatanDirektur->id,
            'nip' => '1234567890'
        ]);

        // 2. Buat User Peninjau
        $peninjauUser = User::create([
            'name' => 'Peninjau (Wadir 1)',
            'email' => 'wadir1@polkam.ac.id',
            'password' => Hash::make('password') // Ganti password ini
        ]);
        // Hubungkan role
        $peninjauUser->roles()->attach($rolePeninjau);
        // Buat data pegawainya
        Pegawai::create([
            'user_id' => $peninjauUser->id,
            'jabatan_id' => $jabatanWadir1->id,
            'nip' => '0987654321'
        ]);
    }
}
