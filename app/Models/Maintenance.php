<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    protected $guarded = [];

    /**
     * Relasi ke model Alat.
     * Ini yang dicari oleh ->with('alat') di Controller kamu.
     */
    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }
}