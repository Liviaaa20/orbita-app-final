<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Update Tabel Alats
        Schema::table('alats', function (Blueprint $table) {
            $table->string('merk_type')->after('nama_alat')->nullable();
            $table->string('tahun_pengadaan', 4)->after('nomor_seri')->nullable();
            $table->string('rentang_ukur')->nullable();
            $table->string('resolusi')->nullable();
            $table->string('akurasi')->nullable();
        });
    
        // Update Tabel Histori Operasionals
        Schema::table('histori_operasionals', function (Blueprint $table) {
            $table->enum('kondisi_fisik', ['Baik', 'RR', 'RB'])->default('Baik')->after('lokasi');
            $table->text('uraian_kerusakan')->nullable();
            $table->text('tindakan_perbaikan')->nullable();
            $table->string('nilai_koreksi')->nullable();
            $table->string('nilai_ketidakpastian')->nullable();
            $table->text('catatan_khusus')->nullable();
        });
    }
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
};
