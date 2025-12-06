<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel skema penilaian baru
        Schema::create('skema_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siklus_id')->constrained('siklus')->onDelete('cascade');
            
            $table->string('nama_skema'); // Contoh: "Skema Struktural"
            $table->json('level_target'); // Menyimpan array level, contoh: [1, 2, 3, 4]
            
            // Kolom Persentase
            $table->unsignedTinyInteger('persen_diri')->default(0);
            $table->unsignedTinyInteger('persen_atasan')->default(0);
            $table->unsignedTinyInteger('persen_rekan')->default(0);
            $table->unsignedTinyInteger('persen_bawahan')->default(0);
            
            $table->timestamps();
        });

        // 2. Hapus kolom persen lama di tabel siklus (agar tidak bingung)
        Schema::table('siklus', function (Blueprint $table) {
            // Kita cek dulu apakah kolomnya ada sebelum drop, untuk menghindari error
            if (Schema::hasColumn('siklus', 'persen_diri')) {
                $table->dropColumn(['persen_diri', 'persen_atasan', 'persen_rekan', 'persen_bawahan']);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skema_penilaian');
        
        // Kembalikan kolom jika rollback (opsional)
        Schema::table('siklus', function (Blueprint $table) {
            $table->unsignedTinyInteger('persen_diri')->default(0);
            $table->unsignedTinyInteger('persen_atasan')->default(0);
            $table->unsignedTinyInteger('persen_rekan')->default(0);
            $table->unsignedTinyInteger('persen_bawahan')->default(0);
        });
    }
};