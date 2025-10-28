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
    Schema::create('siklus', function (Blueprint $table) {
        $table->id();
        $table->year('tahun_ajaran'); // Format YYYY
        $table->enum('semester', ['Ganjil', 'Genap']);
        $table->unsignedTinyInteger('persen_diri')->default(0); // Tambahkan Diri Sendiri
        $table->unsignedTinyInteger('persen_atasan')->default(0);
        $table->unsignedTinyInteger('persen_rekan')->default(0);
        $table->unsignedTinyInteger('persen_bawahan')->default(0);
        $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Tidak Aktif');
        $table->timestamps();

        // Opsional: Pastikan kombinasi tahun & semester unik
        $table->unique(['tahun_ajaran', 'semester']); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sikluses');
    }
};
