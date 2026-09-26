<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriOperasional extends Model
{
    use HasFactory;

    protected $fillable = [
    'alat_id',
    'kategori_id',
    'user_id',
    'jenis_aktivitas',
    'waktu',
    'kategori',
    'lokasi',
    'deskripsi_hasil', // Tambahkan ini
    'dokumen'
];

    public function alat()
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

        public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }
}