<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbooks', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_logbook'); // Contoh: LOG BOOK PERALATAN KONVENSIONAL
            $table->string('jenis_alat');     // Contoh: Konvensional / Otomatis
            $table->string('lokasi_tempat');  // Contoh: Stasiun Maritim Tanjung Emas Semarang
            $table->string('periode_tersedia'); // Contoh: Jan 2026 - April 2026
            $table->integer('jumlah_data')->default(0); // Contoh: 1234 entri
            $table->date('terakhir_diperbarui');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbooks');
    }
};