<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('jadwal_dinas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');     
            $table->date('tanggal');     
            $table->string('shift');     
            $table->string('jam');       
            $table->timestamps();        
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('jadwal_dinas');
    }
};
