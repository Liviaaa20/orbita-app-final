<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveTahunPengadaanAndMerkFromKategorisTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('kategoris', function (Blueprint $table) {
            // Menghapus kolom tahun_pengadaan dan merk
            $table->dropColumn(['tahun_pengadaan', 'merk']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('kategoris', function (Blueprint $table) {
            // Pilihan untuk mengembalikan jika di-rollback
            $table->string('tahun_pengadaan')->nullable();
            $table->string('merk')->nullable();
        });
    }
}