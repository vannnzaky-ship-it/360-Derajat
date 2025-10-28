<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kompetensi', function (Blueprint $table) { // Nama tabel singular
            $table->id();
            $table->string('nama_kompetensi');
            $table->text('deskripsi')->nullable();
            $table->unsignedTinyInteger('bobot')->default(0); // Bobot 0-100
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kompetensi');
    }
};