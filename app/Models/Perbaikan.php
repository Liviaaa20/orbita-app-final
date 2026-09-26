<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perbaikan extends Model
{
    use HasFactory;

    protected $table = 'perbaikans';

    protected $fillable = [
        'no_tiket',
        'alat_id',
        'foto_awal',
        'tgl_permintaan',
        'tgl_diterima',
        'tgl_selesai',
        'user',
        'kategori_perbaikan',
        'keterangan',
        'validasi',
        'catatan',
        'status',
        'foto_selesai',
        'validasi_koordinator',
        'verifikasi_selesai',
        'tgl_validasi',
        'tgl_verifikasi',
        'catatan_teknisi',
    ];

    protected $casts = [
        'tgl_permintaan' => 'datetime',
        'tgl_diterima'   => 'datetime',
        'tgl_selesai'    => 'datetime',
        'tgl_validasi'   => 'datetime',
        'tgl_verifikasi' => 'datetime',
        'validasi'             => 'boolean',
        'validasi_koordinator' => 'boolean',
        'verifikasi_selesai'   => 'boolean',
    ];

    public function alat()
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }

    /*
    |----------------------------------------------------------------------
    | ACCESSOR STATUS LABEL
    |----------------------------------------------------------------------
    | FIX: kolom 'status' di DB adalah ENUM('pending','onproses','selesai')
    | SAJA. Value 'disetujui', 'ditolak', dan 'menunggu_verifikasi' BUKAN
    | bagian dari enum tersebut dan tidak akan pernah benar-benar tersimpan
    | di kolom 'status' (lihat PerbaikanController::validasi() &
    | ::update() — keduanya sudah disesuaikan untuk hanya memakai 3 value
    | enum yang valid). Arm-arm tersebut dihapus dari match supaya tidak
    | menyesatkan; kondisi "ditolak"/"menunggu verifikasi" sebenarnya
    | direpresentasikan lewat kombinasi kolom lain:
    |   - Ditolak di awal       -> status='pending' & validasi=0
    |   - Menunggu verifikasi   -> status='selesai' & validasi_koordinator=null
    |   - Ditolak koordinator   -> status='onproses' & catatan='Ditolak Koordinator'
    | Gunakan helper isMenungguVerifikasiKoordinator() / isPernahDitolak()
    | di bawah untuk mengecek kondisi-kondisi tersebut di Blade.
    */
    public function getStatusLabelAttribute()
    {
        if ($this->isMenungguVerifikasiKoordinator()) {
            return 'Menunggu Verifikasi Koordinator';
        }

        return match ($this->status) {
            'pending'  => 'Menunggu Validasi',
            'onproses' => 'Sedang Dikerjakan',
            'selesai'  => 'Selesai',
            default    => ucfirst($this->status),
        };
    }

    /*
    |----------------------------------------------------------------------
    | HELPER STATUS CHECK (biar enak di blade)
    |----------------------------------------------------------------------
    */

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isOnProses()
    {
        return $this->status === 'onproses';
    }

    public function isSelesai()
    {
        return $this->status === 'selesai';
    }

    /** Tiket pernah ditolak saat validasi awal (status tetap 'pending', validasi=0) */
    public function isPernahDitolak()
    {
        return $this->status === 'pending' && $this->validasi === false;
    }

    /** Tiket sudah selesai dikerjakan teknisi, menunggu ACC/Tolak dari koordinator */
    public function isMenungguVerifikasiKoordinator()
    {
        return $this->status === 'selesai' && is_null($this->validasi_koordinator);
    }

    /** Tiket sudah final disetujui koordinator */
    public function isAccKoordinator()
    {
        return $this->status === 'selesai' && $this->validasi_koordinator === true;
    }
}