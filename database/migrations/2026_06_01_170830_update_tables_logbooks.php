<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {
            // Hapus foreign key lama jika ada
            // Sesuaikan nama constraint jika berbeda di DB kamu
            try {
                $table->dropForeign(['sub_kategori_id']);
            } catch (\Exception $e) {
                // abaikan jika constraint tidak ditemukan
            }

            // Hapus kolom lama
            $table->dropColumn('sub_kategori_id');

            // Tambah kolom baru
            $table->unsignedBigInteger('kategori_id')->nullable()->after('id');
            $table->foreign('kategori_id')
                  ->references('id')
                  ->on('kategoris')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {
            try {
                $table->dropForeign(['kategori_id']);
            } catch (\Exception $e) {}

            $table->dropColumn('kategori_id');

            $table->unsignedBigInteger('sub_kategori_id')->nullable()->after('id');
            $table->foreign('sub_kategori_id')
                  ->references('id')
                  ->on('sub_kategoris')
                  ->onDelete('set null');
        });
    }
};