<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            // Hati-hati: Nama constraint mungkin berbeda jika Anda membuatnya manual.
            // Nama default Laravel: namatabel_namakolom_foreign
            // Cek nama constraint di database Anda jika terjadi error.
            if (Schema::hasColumn('pegawai', 'jabatan_id')) {
                 // Coba hapus foreign key (abaikan error jika tidak ada)
                try {
                    $table->dropForeign(['jabatan_id']);
                } catch (\Exception $e) {
                    // Abaikan error jika foreign key tidak ada
                }
                 // Hapus kolomnya
                $table->dropColumn('jabatan_id');
            }
        });
    }

    // Fungsi down() untuk rollback (mengembalikan kolom jika perlu)
    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            if (!Schema::hasColumn('pegawai', 'jabatan_id')) {
                $table->foreignId('jabatan_id')->nullable()->constrained('jabatan');
            }
        });
    }
};