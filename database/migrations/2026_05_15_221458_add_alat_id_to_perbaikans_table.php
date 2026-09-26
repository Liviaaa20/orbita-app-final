<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('perbaikans', function (Blueprint $table) {
            // Tambahkan kolom alat_id setelah no_tiket, set sebagai nullable
            $table->unsignedBigInteger('alat_id')->nullable()->after('no_tiket');
    
            // Opsional: Tambahkan foreign key agar relasi terjaga
            $table->foreign('alat_id')->references('id')->on('alats')->onDelete('set null');
        });
    }
    
    public function down()
    {
        Schema::table('perbaikans', function (Blueprint $table) {
            $table->dropForeign(['alat_id']);
            $table->dropColumn('alat_id');
        });
    }
};
