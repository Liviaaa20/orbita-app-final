<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKategoriIdToKalibrasisTable extends Migration
{
    public function up()
    {
        Schema::table('kalibrasis', function (Blueprint $table) {
            // Menambahkan kolom kategori_id setelah alat_id (jika kolom alat_id ada)
            // Jika ingin kolom baru dibuat nullable dan tidak perlu foreign key constrain, gunakan:
            $table->unsignedBigInteger('kategori_id')->nullable()->after('alat_id');
            
            // Opsional: Jika ingin Foreign Key Constraint
            // $table->foreign('kategori_id')->references('id')->on('kategoris')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('kalibrasis', function (Blueprint $table) {
            $table->dropColumn('kategori_id');
        });
    }
}