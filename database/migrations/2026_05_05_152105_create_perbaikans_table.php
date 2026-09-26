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
        Schema::create('perbaikans', function (Blueprint $table) {

            $table->id();

            // =========================
            // RELASI
            // =========================
            $table->foreignId('alat_id')
                ->nullable()
                ->constrained('alats')
                ->nullOnDelete();

            // =========================
            // TIKET INFO
            // =========================
            $table->string('no_tiket')->unique();
            $table->string('user');

            // =========================
            // DETAIL PERBAIKAN
            // =========================
            $table->string('kategori_perbaikan');
            $table->longText('keterangan');

            // =========================
            // DOKUMENTASI
            // =========================
            $table->string('foto_awal')->nullable();
            $table->string('foto_selesai')->nullable();

            // =========================
            // CATATAN
            // =========================
            $table->text('catatan')->nullable();
            $table->text('catatan_teknisi')->nullable();

            // =========================
            // TIMESTAMP PROSES
            // =========================
            $table->timestamp('tgl_permintaan')->nullable();
            $table->timestamp('tgl_diterima')->nullable();
            $table->timestamp('tgl_selesai')->nullable();
            $table->timestamp('tgl_validasi')->nullable();
            $table->timestamp('tgl_verifikasi')->nullable();

            // =========================
            // APPROVAL
            // =========================
            $table->boolean('validasi_koordinator')->nullable();
            $table->boolean('verifikasi_selesai')->nullable();

            // =========================
            // STATUS WORKFLOW
            // =========================
            $table->enum('status', [
                'pending',              // baru dibuat
                'disetujui',            // diterima admin/teknisi
                'ditolak',              // ditolak admin/teknisi
                'onproses',             // sedang dikerjakan teknisi
                'menunggu_verifikasi',  // menunggu koordinator
                'selesai'               // selesai final
            ])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perbaikans');
    }
};