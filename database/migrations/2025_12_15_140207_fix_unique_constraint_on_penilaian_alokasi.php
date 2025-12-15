<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penilaian_alokasi', function (Blueprint $table) {
            
            // 1. LEPAS SEMUA CONSTRAINT LAMA (BERSIH-BERSIH)
            // Kita drop Foreign Key dulu biar Index-nya bisa dihapus
            $table->dropForeign(['penilaian_session_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['target_user_id']);
            
            // Hapus index unique lama (yang cuma membolehkan 1 penilai per target)
            $table->dropUnique('unique_penilaian');

            // 2. TAMBAH KOLOM BARU (Penilai Jabatan)
            // Kita tambahkan di sini sekalian agar strukturnya lengkap
            if (!Schema::hasColumn('penilaian_alokasi', 'penilai_jabatan_id')) {
                $table->foreignId('penilai_jabatan_id')->nullable()->after('user_id')->constrained('jabatan')->onDelete('cascade');
            }

            // 3. PASANG KEMBALI FOREIGN KEY
            $table->foreign('penilaian_session_id')->references('id')->on('penilaian_sessions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('target_user_id')->references('id')->on('users')->onDelete('cascade');

            // 4. BUAT UNIQUE INDEX FINAL (YANG PALING LENGKAP)
            // Kombinasi: Sesi + Penilai + JABATAN PENILAI + Target + JABATAN TARGET
            // Ini membuat Wadir bisa muncul 2x (sebagai atasan Ka BAK & atasan Ka Prodi)
            $table->unique(['penilaian_session_id', 'user_id', 'penilai_jabatan_id', 'target_user_id', 'jabatan_id'], 'unique_full_context');
        });
    }

    public function down(): void
    {
        Schema::table('penilaian_alokasi', function (Blueprint $table) {
            // Rollback: Hapus index baru & kolom baru
            $table->dropUnique('unique_full_context');
            $table->dropForeign(['penilai_jabatan_id']);
            $table->dropColumn('penilai_jabatan_id');

            // Kembalikan index lama (Sesuai kondisi awal)
            $table->unique(['penilaian_session_id', 'user_id', 'target_user_id'], 'unique_penilaian');
        });
    }
};