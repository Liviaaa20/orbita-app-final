<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    use HasFactory;

    protected $table = 'alats';
    protected $fillable = [
        'sub_kategori_id',
        'nama_alat',
        'merk_type',        // Tambahan
        'nomor_seri',
        'tahun_pengadaan',  // Tambahan
        'rentang_ukur',     // Tambahan
        'resolusi',         // Tambahan
        'akurasi',          // Tambahan
        'jenis',
        'lokasi',
        'status',
        'kondisi',
        'foto_alat'
    ];

    /**
     * Relasi ke Kategori (TAMBAHKAN INI)
     */
    public function kategori()
{
    // Pastikan foreign key-nya sesuai dengan yang ada di tabel alats (biasanya kategori_id)
    return $this->belongsTo(Kategori::class, 'kategori_id');
}

    // Relasi ke Sub Kategori
    public function subKategori() {
    return $this->belongsTo(SubKategori::class, 'sub_kategori_id');
}

    // Satu alat bisa punya banyak riwayat pengecekan
    public function pengecekans()
    {
        return $this->hasMany(Pengecekan::class);
    }

    public function historis() 
    {
        return $this->hasMany(HistoriOperasional::class);
    }
}