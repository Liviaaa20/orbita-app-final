<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        Kategori::create([
            'kode_kategori' => 'K001',
            'nama_kategori' => 'Automatic Weather Station (AWS)',
            'tahun_pengadaan' => 2024,
            'merk' => 'Lambrecht',
            'jenis' => 'Sistem',
        ]);

        Kategori::create([
            'kode_kategori' => 'K002',
            'nama_kategori' => 'Digital Barometer',
            'tahun_pengadaan' => 2023,
            'merk' => 'Vaisala',
            'jenis' => 'Non Sistem',
        ]);
    }
}