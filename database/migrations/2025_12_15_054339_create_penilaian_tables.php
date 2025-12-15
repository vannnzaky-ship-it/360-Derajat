<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Sesi Penilaian (History & Batas Waktu)
        Schema::create('penilaian_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siklus_id')->constrained('siklus')->onDelete('cascade');
            $table->date('tanggal_mulai');
            $table->dateTime('batas_waktu');
            $table->integer('limit_rekan')->default(5)->comment('Batas max rekan yang menilai');
            $table->enum('status', ['Open', 'Closed'])->default('Open');
            $table->timestamps();
        });

        // 2. Tabel Alokasi (Siapa Menilai Siapa)
        Schema::create('penilaian_alokasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilaian_session_id')->constrained('penilaian_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Si Penilai');
            $table->foreignId('target_user_id')->constrained('users')->onDelete('cascade')->comment('Yang Dinilai');
            $table->enum('sebagai', ['Diri Sendiri', 'Atasan', 'Bawahan', 'Rekan']);
            $table->enum('status_nilai', ['Belum', 'Sudah'])->default('Belum');
            $table->timestamps();

            // Mencegah duplikasi: 1 User hanya boleh menilai 1 Target di 1 Sesi yang sama
            $table->unique(['penilaian_session_id', 'user_id', 'target_user_id'], 'unique_penilaian');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_alokasi');
        Schema::dropIfExists('penilaian_sessions');
    }
};