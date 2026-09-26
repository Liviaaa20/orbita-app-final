<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {
            // Tambah sub_kategori_id setelah id
            $table->foreignId('sub_kategori_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('sub_kategoris')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {
            $table->dropForeign(['sub_kategori_id']);
            $table->dropColumn('sub_kategori_id');
        });
    }
};