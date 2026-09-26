<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearTestingData extends Command
{
    protected $signature   = 'db:clear-testing';
    protected $description = 'Hapus semua data testing dari semua tabel kecuali yang dikecualikan';

    /**
     * Tabel yang TIDAK akan dihapus datanya
     */
    protected array $excluded = [
        'migrations',
        'users',
        'roles',
        // Tambahkan nama tabel lain yang ingin dikecualikan di sini
    ];

    public function handle()
    {
        $this->warn('⚠️  Peringatan: Semua data testing akan dihapus permanen!');

        if (!$this->confirm('Lanjutkan?')) {
            $this->info('Dibatalkan.');
            return;
        }

        // Ambil semua nama tabel di database aktif
        $tables = DB::select('SHOW TABLES');
        $dbName = DB::getDatabaseName();
        $key    = 'Tables_in_' . $dbName;

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($tables as $table) {
            $tableName = $table->$key;

            if (in_array($tableName, $this->excluded)) {
                $this->line("  <fg=yellow>SKIP</>  $tableName");
                continue;
            }

            DB::table($tableName)->truncate();
            $this->line("  <fg=green>CLEAR</> $tableName");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('✅ Selesai! Semua data testing telah dihapus.');
    }
}