<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Jabatan; 

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        // Kosongkan tabel dulu? Hati-hati jika sudah ada relasi.
        // Schema::disableForeignKeyConstraints(); // Nonaktifkan cek foreign key
        // Jabatan::truncate();
        // Schema::enableForeignKeyConstraints(); // Aktifkan lagi

        // Gunakan firstOrCreate agar aman dijalankan ulang
        
        // === Pimpinan Puncak ===
        $direktur = Jabatan::firstOrCreate(
            ['nama_jabatan' => 'Direktur'], 
            ['parent_id' => null, 'is_singleton' => true, 'status' => 'Aktif', 'bidang' => 'Pimpinan']
        );

        // === Bidang Lain (Langsung di bawah Direktur) ===
        $kaBPM = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka BPM'], ['parent_id' => $direktur->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Staff Ka BPM'], ['parent_id' => $kaBPM->id, 'is_singleton' => false, 'status' => 'Aktif']); // Beri nama unik untuk staff

        $kaKoperasi = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka Koperasi'], ['parent_id' => $direktur->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Staff Ka Koperasi'], ['parent_id' => $kaKoperasi->id, 'is_singleton' => false, 'status' => 'Aktif']);
        
        $badanPP = Jabatan::firstOrCreate(['nama_jabatan' => 'Badan Perencanaan dan Pengembangan'], ['parent_id' => $direktur->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Staf Badan PP'], ['parent_id' => $badanPP->id, 'is_singleton' => false, 'status' => 'Aktif']);


        // === Bidang 1 (Di bawah Wadir I) ===
        $wadir1 = Jabatan::firstOrCreate(['nama_jabatan' => 'Wadir I'], ['parent_id' => $direktur->id, 'is_singleton' => true, 'status' => 'Aktif', 'bidang' => 'Bidang 1']);
        
        $kaBAK = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka BAK'], ['parent_id' => $wadir1->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Staff Ka BAK'], ['parent_id' => $kaBAK->id, 'is_singleton' => false, 'status' => 'Aktif']);

        $kaBAA = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka BAA'], ['parent_id' => $wadir1->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Staff Ka BAA'], ['parent_id' => $kaBAA->id, 'is_singleton' => false, 'status' => 'Aktif']);
        
        $kaPERPUS = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka Perpustakaan'], ['parent_id' => $wadir1->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Staff Ka Perpustakaan'], ['parent_id' => $kaPERPUS->id, 'is_singleton' => false, 'status' => 'Aktif']);
        
        $kaProdiTPS = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka Prodi TPS'], ['parent_id' => $wadir1->id, 'is_singleton' => true, 'status' => 'Aktif']);
        $kaLabTPS = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka Lab TPS'], ['parent_id' => $kaProdiTPS->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Staff Ka Lab TPS'], ['parent_id' => $kaLabTPS->id, 'is_singleton' => false, 'status' => 'Aktif']);

        $kaProdiTIF = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka Prodi TIF'], ['parent_id' => $wadir1->id, 'is_singleton' => true, 'status' => 'Aktif']);
        $kaLabTIF = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka Lab TIF'], ['parent_id' => $kaProdiTIF->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Staff Ka Lab TIF'], ['parent_id' => $kaLabTIF->id, 'is_singleton' => false, 'status' => 'Aktif']);

        $kaProdiPPM = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka Prodi PPM'], ['parent_id' => $wadir1->id, 'is_singleton' => true, 'status' => 'Aktif']);
        $kaLabPPM = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka Lab PPM'], ['parent_id' => $kaProdiPPM->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Staff Ka Lab PPM'], ['parent_id' => $kaLabPPM->id, 'is_singleton' => false, 'status' => 'Aktif']);

        Jabatan::firstOrCreate(['nama_jabatan' => 'Ka Prodi ABI'], ['parent_id' => $wadir1->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Ka Prodi TPKS'], ['parent_id' => $wadir1->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Ka Prodi TRL'], ['parent_id' => $wadir1->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Ka Prodi MAB'], ['parent_id' => $wadir1->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Ka Prodi PP'], ['parent_id' => $wadir1->id, 'is_singleton' => true, 'status' => 'Aktif']);

        $kaICT = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka ICT'], ['parent_id' => $wadir1->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Staff Ka ICT'], ['parent_id' => $kaICT->id, 'is_singleton' => false, 'status' => 'Aktif']);

        $kaP3M = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka P3M'], ['parent_id' => $wadir1->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Staff Ka P3M'], ['parent_id' => $kaP3M->id, 'is_singleton' => false, 'status' => 'Aktif']);


        // === Bidang 2 (Di bawah Wadir II) ===
        $wadir2 = Jabatan::firstOrCreate(['nama_jabatan' => 'Wadir II'], ['parent_id' => $direktur->id, 'is_singleton' => true, 'status' => 'Aktif', 'bidang' => 'Bidang 2']);
        
        $kaBAKKU = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka BAKKU'], ['parent_id' => $wadir2->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Ka.sub bag Umum'], ['parent_id' => $kaBAKKU->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Staff Keuangan'], ['parent_id' => $kaBAKKU->id, 'is_singleton' => false, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Staff Kepegawaian dan Umum'], ['parent_id' => $kaBAKKU->id, 'is_singleton' => false, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Cleaning Service'], ['parent_id' => $kaBAKKU->id, 'is_singleton' => false, 'status' => 'Aktif']);

        $kaPPA = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka PPA'], ['parent_id' => $wadir2->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Kasi PPA'], ['parent_id' => $kaPPA->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Staff PPA'], ['parent_id' => $kaPPA->id, 'is_singleton' => false, 'status' => 'Aktif']);

        $kaBisnis = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka Bisnis'], ['parent_id' => $wadir2->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Staff Ka Bisnis'], ['parent_id' => $kaBisnis->id, 'is_singleton' => false, 'status' => 'Aktif']);
         // --- AKHIR BIDANG 2 ---

        // === Bidang 3 (Di bawah Wadir III) ===
        $wadir3 = Jabatan::firstOrCreate(['nama_jabatan' => 'Wadir III'], ['parent_id' => $direktur->id, 'is_singleton' => true, 'status' => 'Aktif', 'bidang' => 'Bidang 3']);

        $kaBAKHA = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka BAKHA'], ['parent_id' => $wadir3->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Staff Ka BAKHA'], ['parent_id' => $kaBAKHA->id, 'is_singleton' => false, 'status' => 'Aktif']);

        $kaPusatKarir = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka Pusat Karir'], ['parent_id' => $wadir3->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Staff Ka Pusat Karir'], ['parent_id' => $kaPusatKarir->id, 'is_singleton' => false, 'status' => 'Aktif']);

        $kaPMB = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka PMB'], ['parent_id' => $wadir3->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Kasi PMB'], ['parent_id' => $kaPMB->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Staff PMB'], ['parent_id' => $kaPMB->id, 'is_singleton' => false, 'status' => 'Aktif']);

        $kaMediaCenter = Jabatan::firstOrCreate(['nama_jabatan' => 'Ka Media Center'], ['parent_id' => $wadir3->id, 'is_singleton' => true, 'status' => 'Aktif']);
        Jabatan::firstOrCreate(['nama_jabatan' => 'Staff Ka Media Center'], ['parent_id' => $kaMediaCenter->id, 'is_singleton' => false, 'status' => 'Aktif']);

         // --- AKHIR BIDANG 3 ---

         // Catatan: Pastikan nama staff dibuat unik jika perlu (misal: "Staff Ka BAK 1", "Staff Ka BAK 2")
         // Atau tambahkan kolom 'unit_kerja' di tabel jabatan
    }
}