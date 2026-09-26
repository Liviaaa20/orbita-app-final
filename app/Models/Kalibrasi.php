<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kalibrasi extends Model
{
    protected $fillable = [
        'kategori_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'kalibrator',
        'nilai_koreksi',
        'nilai_ketidakpastian',
        'sertifikat_pdf',
        'petugas',
    ];

/**
     * Relasi ke Kategori Alat
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    // Relasi ke Alat ( Opsional, mungkin needed untuk histori, tapi sekarang fokus pada Kategori )
    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }
}