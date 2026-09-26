<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('alats', function (Blueprint $table) {
            // Menambahkan kolom jadwal_hari setelah kolom lokasi
            $table->string('jadwal_hari')->nullable()->after('lokasi'); 
        });
    }
    
    public function down()
    {
        Schema::table('alats', function (Blueprint $table) {
            $table->dropColumn('jadwal_hari');
        });
    }
};
