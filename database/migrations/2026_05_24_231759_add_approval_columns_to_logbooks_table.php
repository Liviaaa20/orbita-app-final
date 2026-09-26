<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {
            $table->enum('status', [
                'draft',
                'pending_kapok',
                'approved_kapok',
                'rejected_kapok',
                'pending_koordinator',
                'approved_final',
                'rejected_koordinator',
            ])->default('draft')->after('terakhir_diperbarui');

            // Kapok
            $table->foreignId('approved_kapok_by')
                  ->nullable()->after('status')
                  ->constrained('users')->onDelete('set null');
            $table->timestamp('approved_kapok_at')->nullable()->after('approved_kapok_by');
            $table->text('catatan_kapok')->nullable()->after('approved_kapok_at');

            // Koordinator
            $table->foreignId('approved_koordinator_by')
                  ->nullable()->after('catatan_kapok')
                  ->constrained('users')->onDelete('set null');
            $table->timestamp('approved_koordinator_at')->nullable()->after('approved_koordinator_by');
            $table->text('catatan_koordinator')->nullable()->after('approved_koordinator_at');
        });
    }

    public function down(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {
            $table->dropForeign(['approved_kapok_by']);
            $table->dropForeign(['approved_koordinator_by']);
            $table->dropColumn([
                'status',
                'approved_kapok_by',
                'approved_kapok_at',
                'catatan_kapok',
                'approved_koordinator_by',
                'approved_koordinator_at',
                'catatan_koordinator',
            ]);
        });
    }
};