<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Jabatan;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Hash; 

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil Roles
        $roleSuperadmin = Role::where('name', 'superadmin')->firstOrFail();
        $roleAdmin = Role::where('name', 'admin')->firstOrFail(); // <-- Ambil role admin
        // Ambil Jabatan (contoh)
        // $jabatanDirektur = Jabatan::where('nama_jabatan', 'Direktur')->first(); // Pastikan jabatan ini ada
        // $jabatanWadir1 = Jabatan::where('nama_jabatan', 'Wakil Direktur I')->first(); // Pastikan jabatan ini ada
        // $jabatanStaff = Jabatan::where('nama_jabatan', 'Sekretaris SPI')->first(); // Contoh jabatan lain, pastikan ada

        // 1. Buat User Super Admin (Hanya 1 Role)
        $superadminUser = User::updateOrCreate(
            ['email' => 'superadmin360@polkam.ac.id'],
            [
                'name' => 'Super Admin Utama',
                'password' => Hash::make('password') // Ganti password ini
            ]
        );
        $superadminUser->roles()->sync([$roleSuperadmin->id]); // <-- Sync HANYA role superadmin
        // Buat data pegawainya (jika Superadmin juga pegawai)
        Pegawai::updateOrCreate(
            ['user_id' => $superadminUser->id],
            [
                // 'jabatan_id' => $jabatanDirektur->id ?? null, // Sesuaikan jabatannya
                'nip' => '-'
            ]
        );

        // 2. Buat User Administrator (Contoh)
        $adminUser = User::updateOrCreate(
            ['email' => 'admin.360@polkam.ac.id'], // <-- Email baru untuk admin
            [
                'name' => 'Administrator',
                'password' => Hash::make('password') // Ganti password ini
            ]
        );
        $adminUser->roles()->sync([$roleAdmin->id]); // <-- Beri role Admin & Karyawan
        // Buat data pegawainya
        Pegawai::updateOrCreate(
            ['user_id' => $adminUser->id],
            [
                // 'jabatan_id' => $jabatanStaff->id ?? null, // Sesuaikan jabatannya
                'nip' => '--'
            ]
        );
    }
}