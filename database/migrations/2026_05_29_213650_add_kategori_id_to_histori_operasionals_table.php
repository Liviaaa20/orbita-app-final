<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKategoriIdToHistoriOperasionalsTable extends Migration
{
    public function up()
    {
        Schema::table('histori_operasionals', function (Blueprint $table) {
            $table->unsignedBigInteger('kategori_id')->nullable()->after('alat_id');
        });
    }

    public function down()
    {
        Schema::table('histori_operasionals', function (Blueprint $table) {
            $table->dropColumn('kategori_id');
        });
    }
}