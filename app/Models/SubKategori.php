<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubKategori extends Model
{
    use HasFactory;

    /**
     * Field yang boleh diisi secara massal.
     * PENTING: kategori_id harus ada di sini agar tidak error lagi.
     */
    protected $fillable = [
        'kategori_id',
        'nama_sub_kategori',
        'kode_sub_kategori',
    ];

    /**
     * Relasi balik ke Kategori Utama.
     * Satu Sub Kategori dimiliki oleh satu Kategori.
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /**
     * Relasi ke model Alat.
     * Satu Sub Kategori memiliki banyak Alat.
     */
    public function alats()
    {
        return $this->hasMany(Alat::class, 'sub_kategori_id');
    }
}