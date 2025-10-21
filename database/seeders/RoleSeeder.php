<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role; 

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'superadmin'], ['label' => 'Super Admin']);
        Role::firstOrCreate(['name' => 'admin'],      ['label' => 'Administrator']); // <-- TAMBAHKAN INI
        Role::firstOrCreate(['name' => 'peninjau'],   ['label' => 'Peninjau']);
        Role::firstOrCreate(['name' => 'karyawan'],   ['label' => 'Karyawan']);
    }
}