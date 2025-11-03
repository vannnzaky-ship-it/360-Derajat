<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jabatan', function (Blueprint $table) {
            // Tambah kolom boolean, default false (tidak unik)
            // 'after' bersifat opsional, hanya untuk kerapian
            // BARU (SUDAH DIPERBAIKI)
            $table->boolean('is_singleton')->default(false); 
        });
    }

    public function down(): void
    {
        Schema::table('jabatan', function (Blueprint $table) {
            $table->dropColumn('is_singleton');
        });
    }
};