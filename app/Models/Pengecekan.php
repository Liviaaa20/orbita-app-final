<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengecekan extends Model
{
    use HasFactory;

    protected $fillable = [
        'alat_id',
        'user_id',
        'tanggal',
        'waktu',
        'is_checked',
        'kondisi_akhir', // Tambahkan ini
        'foto_kegiatan', // Tambahkan ini
        'catatan'
    ];

    // Relasi balik: Pengecekan ini milik alat yang mana?
    public function alat()
    {
        return $this->belongsTo(Alat::class);
    }

    // Relasi balik: Siapa petugas yang melakukan pengecekan ini?
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}