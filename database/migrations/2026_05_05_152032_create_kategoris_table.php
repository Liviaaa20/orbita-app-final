<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kategoris', function (Blueprint $table) {
            $table->id(); // Ini akan jadi auto-increment
            $table->string('kode_kategori')->unique(); // Untuk ID K001, K002, dll
            $table->string('nama_kategori');
            $table->year('tahun_pengadaan');
            $table->string('merk');
            $table->enum('jenis', ['Sistem', 'Non Sistem']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategoris');
    }
};
