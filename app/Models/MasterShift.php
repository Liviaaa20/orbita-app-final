<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterShift extends Model
{
    protected $table = 'master_shift';

    protected $fillable = [
        'kode_shift',
        'nama_shift',
        'jam_mulai',
        'jam_selesai',
        'keterangan'
    ];
}
