<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jabatan;
use Illuminate\Support\Facades\Schema;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        // PENTING: Matikan foreign key check dulu biar bisa truncate (hapus bersih)
        // Supaya urutan ID dan struktur benar-benar fresh
        Schema::disableForeignKeyConstraints();
        Jabatan::truncate(); 
        Schema::enableForeignKeyConstraints();

        // ============================================================
        // GROUP 1: DIREKTORAT (LEVEL PUNCAK & UNIT PUSAT)
        // Range Urutan: 1 - 10
        // ============================================================
        
        // 1. Direktur (Top Level)
        $direktur = Jabatan::create([
            'nama_jabatan' => 'Direktur',
            'parent_id' => null, 
            'is_singleton' => true, 
            'status' => 'Aktif', 
            'bidang' => 'Direktorat', 
            'level' => 1, 
            'urutan' => 1
        ]);

        // 2. Unit Langsung Bawah Direktur
        $kaBPM = Jabatan::create([
            'nama_jabatan' => 'Ka BPM', 
            'parent_id' => $direktur->id, 
            'is_singleton' => true, 
            'status' => 'Aktif', 
            'bidang' => 'Direktorat', 
            'level' => 2, 
            'urutan' => 2
        ]);
        Jabatan::create(['nama_jabatan' => 'Staff Ka BPM', 'parent_id' => $kaBPM->id, 'is_singleton' => false, 'status' => 'Aktif', 'bidang' => 'Direktorat', 'level' => 3, 'urutan' => 3]);

        $kaKoperasi = Jabatan::create([
            'nama_jabatan' => 'Ka Koperasi', 
            'parent_id' => $direktur->id, 
            'is_singleton' => true, 
            'status' => 'Aktif', 
            'bidang' => 'Direktorat', 
            'level' => 2, 
            'urutan' => 4
        ]);
        Jabatan::create(['nama_jabatan' => 'Staff Ka Koperasi', 'parent_id' => $kaKoperasi->id, 'is_singleton' => false, 'status' => 'Aktif', 'bidang' => 'Direktorat', 'level' => 3, 'urutan' => 5]);

        $badanPP = Jabatan::create([
            'nama_jabatan' => 'Badan Perencanaan dan Pengembangan', 
            'parent_id' => $direktur->id, 
            'is_singleton' => true, 
            'status' => 'Aktif', 
            'bidang' => 'Direktorat', 
            'level' => 2, 
            'urutan' => 6
        ]);
        Jabatan::create(['nama_jabatan' => 'Staf Badan PP', 'parent_id' => $badanPP->id, 'is_singleton' => false, 'status' => 'Aktif', 'bidang' => 'Direktorat', 'level' => 3, 'urutan' => 7]);


        // ============================================================
        // GROUP 2: BIDANG 1 (AKADEMIK)
        // Range Urutan: 11 - 49
        // ============================================================
        
        // Wadir 1
        $wadir1 = Jabatan::create([
            'nama_jabatan' => 'Wadir I', 
            'parent_id' => $direktur->id, 
            'is_singleton' => true, 
            'status' => 'Aktif', 
            'bidang' => 'Bidang 1', 
            'level' => 2, 
            'urutan' => 11
        ]);

        $no = 12; // Counter urutan otomatis untuk Bidang 1

        // Fungsi helper kecil biar codingan gak panjang
        $buatUnit = function($namaJabatan, $parent, $bidang, &$urutan) {
            $kepala = Jabatan::create(['nama_jabatan' => $namaJabatan, 'parent_id' => $parent->id, 'is_singleton' => true, 'status' => 'Aktif', 'bidang' => $bidang, 'level' => 3, 'urutan' => $urutan++]);
            Jabatan::create(['nama_jabatan' => 'Staff ' . $namaJabatan, 'parent_id' => $kepala->id, 'is_singleton' => false, 'status' => 'Aktif', 'bidang' => $bidang, 'level' => 4, 'urutan' => $urutan++]);
            return $kepala;
        };

        // -- Unit-Unit Bidang 1 --
        $buatUnit('Ka BAK', $wadir1, 'Bidang 1', $no);
        $buatUnit('Ka BAA', $wadir1, 'Bidang 1', $no);
        $buatUnit('Ka Perpustakaan', $wadir1, 'Bidang 1', $no);
        $buatUnit('Ka ICT', $wadir1, 'Bidang 1', $no);
        $buatUnit('Ka P3M', $wadir1, 'Bidang 1', $no);

        // -- Prodi & Lab (Spesial ada Lab-nya) --
        $listProdi = ['TPS', 'TIF', 'PPM'];
        foreach($listProdi as $prodi) {
            $kaProdi = Jabatan::create(['nama_jabatan' => "Ka Prodi $prodi", 'parent_id' => $wadir1->id, 'is_singleton' => true, 'status' => 'Aktif', 'bidang' => 'Bidang 1', 'level' => 3, 'urutan' => $no++]);
            $kaLab = Jabatan::create(['nama_jabatan' => "Ka Lab $prodi", 'parent_id' => $kaProdi->id, 'is_singleton' => true, 'status' => 'Aktif', 'bidang' => 'Bidang 1', 'level' => 4, 'urutan' => $no++]);
            Jabatan::create(['nama_jabatan' => "Staff Ka Lab $prodi", 'parent_id' => $kaLab->id, 'is_singleton' => false, 'status' => 'Aktif', 'bidang' => 'Bidang 1', 'level' => 5, 'urutan' => $no++]);
        }

        // -- Prodi Tanpa Lab --
        $prodiLain = ['ABI', 'TPKS', 'TRL', 'MAB', 'PP'];
        foreach($prodiLain as $prodi) {
            Jabatan::create(['nama_jabatan' => "Ka Prodi $prodi", 'parent_id' => $wadir1->id, 'is_singleton' => true, 'status' => 'Aktif', 'bidang' => 'Bidang 1', 'level' => 3, 'urutan' => $no++]);
        }


        // ============================================================
        // GROUP 3: BIDANG 2 (KEUANGAN & SDM)
        // Range Urutan: 50 - 79
        // ============================================================
        
        $wadir2 = Jabatan::create([
            'nama_jabatan' => 'Wadir II', 
            'parent_id' => $direktur->id, 
            'is_singleton' => true, 
            'status' => 'Aktif', 
            'bidang' => 'Bidang 2', 
            'level' => 2, 
            'urutan' => 50
        ]);

        $no = 51;

        // -- BAKKU --
        $kaBAKKU = Jabatan::create(['nama_jabatan' => 'Ka BAKKU', 'parent_id' => $wadir2->id, 'is_singleton' => true, 'status' => 'Aktif', 'bidang' => 'Bidang 2', 'level' => 3, 'urutan' => $no++]);
        Jabatan::create(['nama_jabatan' => 'Ka.sub bag Umum', 'parent_id' => $kaBAKKU->id, 'is_singleton' => true, 'status' => 'Aktif', 'bidang' => 'Bidang 2', 'level' => 4, 'urutan' => $no++]);
        Jabatan::create(['nama_jabatan' => 'Staff Keuangan', 'parent_id' => $kaBAKKU->id, 'is_singleton' => false, 'status' => 'Aktif', 'bidang' => 'Bidang 2', 'level' => 5, 'urutan' => $no++]);
        Jabatan::create(['nama_jabatan' => 'Staff Kepegawaian', 'parent_id' => $kaBAKKU->id, 'is_singleton' => false, 'status' => 'Aktif', 'bidang' => 'Bidang 2', 'level' => 5, 'urutan' => $no++]);
        Jabatan::create(['nama_jabatan' => 'Cleaning Service', 'parent_id' => $kaBAKKU->id, 'is_singleton' => false, 'status' => 'Aktif', 'bidang' => 'Bidang 2', 'level' => 4, 'urutan' => $no++]);

        // -- PPA & Bisnis --
        $buatUnit('Ka PPA', $wadir2, 'Bidang 2', $no);
        $buatUnit('Ka Bisnis', $wadir2, 'Bidang 2', $no);


        // ============================================================
        // GROUP 4: BIDANG 3 (KEMAHASISWAAN)
        // Range Urutan: 80 - 100
        // ============================================================

        $wadir3 = Jabatan::create([
            'nama_jabatan' => 'Wadir III', 
            'parent_id' => $direktur->id, 
            'is_singleton' => true, 
            'status' => 'Aktif', 
            'bidang' => 'Bidang 3', 
            'level' => 2, 
            'urutan' => 80
        ]);

        $no = 81;

        // -- Unit Bidang 3 --
        $buatUnit('Ka BAKHA', $wadir3, 'Bidang 3', $no);
        $buatUnit('Ka Pusat Karir', $wadir3, 'Bidang 3', $no);
        $buatUnit('Ka PMB', $wadir3, 'Bidang 3', $no);
        $buatUnit('Ka Media Center', $wadir3, 'Bidang 3', $no);
    }
}