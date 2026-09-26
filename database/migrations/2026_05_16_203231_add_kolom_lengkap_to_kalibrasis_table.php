<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kalibrasis', function (Blueprint $table) {
            // Kita suntikkan semua kolom yang dibutuhkan di sini
            $table->unsignedBigInteger('alat_id')->after('id')->nullable();
            $table->date('tanggal_mulai')->after('alat_id')->nullable();
            $table->date('tanggal_selesai')->after('tanggal_mulai')->nullable();
            $table->string('kalibrator')->after('tanggal_selesai')->nullable();
            $table->double('nilai_koreksi', 8, 2)->after('kalibrator')->nullable();
            $table->double('nilai_ketidakpastian', 8, 2)->after('nilai_koreksi')->nullable();
            $table->string('sertifikat_pdf')->after('nilai_ketidakpastian')->nullable();
            $table->string('petugas')->after('sertifikat_pdf')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('kalibrasis', function (Blueprint $table) {
            $table->dropColumn([
                'alat_id', 'tanggal_mulai', 'tanggal_selesai', 
                'kalibrator', 'nilai_koreksi', 'nilai_ketidakpastian', 
                'sertifikat_pdf', 'petugas'
            ]);
        });
    }
};