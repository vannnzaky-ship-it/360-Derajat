<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'superadmin', 'label' => 'Super Admin']);
        Role::create(['name' => 'peninjau', 'label' => 'Peninjau']);
        Role::create(['name' => 'karyawan', 'label' => 'Karyawan']);
    }
}
