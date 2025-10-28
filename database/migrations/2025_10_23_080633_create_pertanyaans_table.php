<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pertanyaan', function (Blueprint $table) { // Nama tabel singular
            $table->id();
            $table->foreignId('kompetensi_id')->constrained('kompetensi')->onDelete('cascade'); // Relasi ke Kompetensi
            $table->text('teks_pertanyaan');
            $table->boolean('untuk_diri')->default(false); // Target Penilai
            $table->boolean('untuk_atasan')->default(false);
            $table->boolean('untuk_rekan')->default(false);
            $table->boolean('untuk_bawahan')->default(false);
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pertanyaan');
    }
};