<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jabatan', function (Blueprint $table) {
            // Cek dulu, kalau kolom 'bidang' BELUM ada, baru buat
            if (!Schema::hasColumn('jabatan', 'bidang')) {
                $table->string('bidang')->nullable()->after('nama_jabatan');
            }
            
            // Cek dulu, kalau kolom 'level' BELUM ada, baru buat
            if (!Schema::hasColumn('jabatan', 'level')) {
                $table->integer('level')->default(4)->after('nama_jabatan')->comment('1:Direktur, 2:Wadir, 3:KaUnit, 4:Staff');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jabatan', function (Blueprint $table) {
            // Hapus kolom jika ada (untuk rollback)
            if (Schema::hasColumn('jabatan', 'bidang')) {
                $table->dropColumn('bidang');
            }
            if (Schema::hasColumn('jabatan', 'level')) {
                $table->dropColumn('level');
            }
        });
    }
};