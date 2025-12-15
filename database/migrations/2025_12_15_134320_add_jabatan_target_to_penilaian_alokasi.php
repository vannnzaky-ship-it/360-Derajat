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
    Schema::table('penilaian_alokasi', function (Blueprint $table) {
        // Kita simpan ID Jabatan TARGET (Jabatan orang yang dinilai)
        $table->foreignId('jabatan_id')->nullable()->after('target_user_id')->constrained('jabatan')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('penilaian_alokasi', function (Blueprint $table) {
        $table->dropForeign(['jabatan_id']);
        $table->dropColumn('jabatan_id');
    });
}
};
