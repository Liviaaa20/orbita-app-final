<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalDinas extends Model
{
    use HasFactory;
    protected $table = 'jadwal_dinas'; // Sesuaikan dengan nama tabel di database
    protected $guarded = [];

    public function shiftMaster()
{
    return $this->belongsTo(
        \App\Models\MasterShift::class,
        'shift_id'
    );
}
public function jadwalDinas()
{
    return $this->hasMany(
        JadwalDinas::class,
        'shift_id'
    );
}
}
