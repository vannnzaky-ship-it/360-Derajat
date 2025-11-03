<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai_jabatan', function (Blueprint $table) {
            $table->id();
            // Foreign key ke tabel pegawai
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            // Foreign key ke tabel jabatan
            $table->foreignId('jabatan_id')->constrained('jabatan')->onDelete('cascade');
            // Pastikan kombinasi pegawai & jabatan unik
            $table->unique(['pegawai_id', 'jabatan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai_jabatan');
    }
};