<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jabatan;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        // Contoh dari Bidang 1
        $direktur = Jabatan::create(['nama_jabatan' => 'Direktur', 'bidang' => 'Bidang 1']);
        
        // Anak dari Direktur
        Jabatan::create([
            'nama_jabatan' => 'Wakil Direktur I', 
            'bidang' => 'Bidang 1', 
            'parent_id' => $direktur->id
        ]);
        Jabatan::create([
            'nama_jabatan' => 'Wakil Direktur II', 
            'bidang' => 'Bidang 1', 
            'parent_id' => $direktur->id
        ]);

        // Contoh dari Bidang 2
        Jabatan::create(['nama_jabatan' => 'Ka. SPI', 'bidang' => 'Bidang 2']);
        Jabatan::create(['nama_jabatan' => 'Sekretaris SPI', 'bidang' => 'Bidang 2']);
        
        // ... Lanjutkan mengisi data dari file CSV Anda ...
    }
}