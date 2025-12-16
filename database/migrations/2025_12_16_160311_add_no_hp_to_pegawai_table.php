<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('pegawai', function (Blueprint $table) {
        // Menambahkan kolom no_hp setelah kolom nip, boleh kosong (nullable)
        $table->string('no_hp')->nullable()->after('nip');
    });
}

public function down()
{
    Schema::table('pegawai', function (Blueprint $table) {
        $table->dropColumn('no_hp');
    });
}
};
