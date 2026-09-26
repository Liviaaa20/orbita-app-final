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
        Schema::create('kalibrasis', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel kategoris (bukan alats)
            $table->foreignId('kategori_id')->constrained('kategoris')->onDelete('cascade');

            // Informasi Kalibrasi
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('kalibrator');
            $table->double('nilai_koreksi', 10, 4)->nullable();
            $table->double('nilai_ketidakpastian', 10, 4)->nullable();
            $table->string('sertifikat_pdf')->nullable();
            $table->string('petugas')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kalibrasis');
    }
};