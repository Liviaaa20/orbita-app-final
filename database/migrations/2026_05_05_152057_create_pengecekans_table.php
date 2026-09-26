<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengecekans', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel alats
            $table->foreignId('alat_id')->constrained('alats')->onDelete('cascade');
            
            // Relasi ke tabel users (Petugas)
            $table->foreignId('user_id')->constrained('users');
            
            // Data Waktu Pengecekan
            $table->date('tanggal'); 
            $table->enum('waktu', ['Pagi', 'Siang', 'Malam']); 
            
            // Status Checklist & Dokumentasi
            $table->boolean('is_checked')->default(false);
            $table->enum('kondisi_akhir', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik'); // Tambahan biar histori lebih informatif
            $table->string('foto_kegiatan')->nullable(); // INI UNTUK KOLOM DOKUMEN DI HISTORI
            
            // Catatan tambahan
            $table->text('catatan')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengecekans');
    }
};