<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategoris';

    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
        'jenis'
    ];

    /**
     * Relasi ke Sub Kategori
     */
    public function subKategoris()
    {
        // Tambahkan 'kategori_id' secara eksplisit jika nanti kamu mengubah nama foreign key-nya
        return $this->hasMany(SubKategori::class, 'kategori_id');
    }

    /**
     * Relasi Jarak Jauh ke Alat (Has Many Through)
     * Kategori -> SubKategori -> Alat
     */
    public function alats()
    {
        return $this->hasManyThrough(
            Alat::class, 
            SubKategori::class,
            'kategori_id',     // Foreign key di tabel sub_kategoris
            'sub_kategori_id', // Foreign key di tabel alats
            'id',              // Local key di tabel kategoris
            'id'               // Local key di tabel sub_kategoris
        );
    }
}