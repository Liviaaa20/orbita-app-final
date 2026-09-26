<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('perbaikans', function (Blueprint $table) {

            // =========================
            // FOTO (SAFE CHECK CONCEPT)
            // =========================
            if (Schema::hasColumn('perbaikans', 'foto')) {
                $table->renameColumn('foto', 'foto_awal');
            }

            if (!Schema::hasColumn('perbaikans', 'foto_selesai')) {
                $table->string('foto_selesai')->nullable();
            }

            // =========================
            // APPROVAL
            // =========================
            if (!Schema::hasColumn('perbaikans', 'validasi_koordinator')) {
                $table->boolean('validasi_koordinator')->nullable();
            }

            if (!Schema::hasColumn('perbaikans', 'verifikasi_selesai')) {
                $table->boolean('verifikasi_selesai')->nullable();
            }

            // =========================
            // TIMESTAMP FLOW
            // =========================
            if (!Schema::hasColumn('perbaikans', 'tgl_validasi')) {
                $table->timestamp('tgl_validasi')->nullable();
            }

            if (!Schema::hasColumn('perbaikans', 'tgl_verifikasi')) {
                $table->timestamp('tgl_verifikasi')->nullable();
            }

            // =========================
            // CATATAN TEKNISI
            // =========================
            if (!Schema::hasColumn('perbaikans', 'catatan_teknisi')) {
                $table->text('catatan_teknisi')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('perbaikans', function (Blueprint $table) {

            // =========================
            // REVERT FOTO
            // =========================
            if (Schema::hasColumn('perbaikans', 'foto_awal')) {
                $table->renameColumn('foto_awal', 'foto');
            }

            // =========================
            // DROP COLUMNS SAFELY
            // =========================
            $columns = [
                'foto_selesai',
                'validasi_koordinator',
                'verifikasi_selesai',
                'tgl_validasi',
                'tgl_verifikasi',
                'catatan_teknisi'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('perbaikans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};