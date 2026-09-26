<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('histori_operasionals', function (Blueprint $table) {
            $table->id();
            
            // 1. Waktu Kejadian (Sesuai kolom 2 di wireframe)
            $table->datetime('waktu'); 

            // 2. Jenis Aktivitas (Maintenance Harian, Mingguan, Kalibrasi, Perbaikan, dll)
            // Pakai string karena pilihannya banyak sesuai wireframe
            $table->string('jenis_aktivitas'); 

            // 3. Kategori (Peralatan, dll)
            $table->string('kategori');

            // 4. Relasi ke Alat (Untuk kolom Alat/Sistem)
            $table->foreignId('alat_id')->constrained('alats')->onDelete('cascade');

            // 5. Lokasi (Bisa ambil dari tabel alat, tapi simpan di sini agar datanya statis/history)
            $table->string('lokasi');

            // 6. Deskripsi / Hasil (Kolom deskripsi panjang di wireframe)
            $table->text('deskripsi_hasil');

            // 7. Petugas (Relasi ke user yang mengerjakan)
            $table->foreignId('user_id')->constrained('users');

            // 8. Dokumen (Simpan path file PDF/Foto laporan)
            $table->string('dokumen')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('histori_operasionals');
    }
};