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
    Schema::create('penilaian_skor', function (Blueprint $table) {
        $table->id();
        $table->foreignId('penilaian_alokasi_id')->constrained('penilaian_alokasi')->onDelete('cascade');
        $table->foreignId('pertanyaan_id')->constrained('pertanyaan')->onDelete('cascade');
        $table->tinyInteger('nilai')->comment('Skala 1-5');
        $table->timestamps();

        // Mencegah double entry untuk pertanyaan yang sama di sesi yang sama
        $table->unique(['penilaian_alokasi_id', 'pertanyaan_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_skor');
    }
};
