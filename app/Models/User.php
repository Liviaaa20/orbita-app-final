<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'kode_user', // Tambahan sesuai wireframe (U001, dst)
        'name',
        'email',
        'username',  // Tambahan untuk login
        'password',
        'nip',
        'status',    // Tambahan sesuai wireframe (aktif/nonaktif)
        'role_id',   // Foreign key ke tabel roles
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke model Role
     * Satu User memiliki satu Role
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Role::class, 'role_id');
    }
    public function historis() {
        return $this->hasMany(HistoriOperasional::class);
    }

    public function canManageMaintenance()
{
    return $this->role &&
        in_array(
            strtolower($this->role->nama_role),
            ['admin', 'teknisi']
        );
}
    public function canViewMaintenance()
{
    return in_array($this->role->nama_role, [
        'Admin',
        'Teknisi',
        'Kepala kelompok',
        'Koordinator',
    ]);
}
    public function canManageMasterData()
{
    return $this->role &&
        in_array(
            strtolower($this->role->nama_role),
            ['admin', 'teknisi']
        );
}
    public function canViewMasterData()
{
    return in_array($this->role->nama_role, [
        'Admin',
        'Teknisi',
        'Kepala Kelompok',
        'Koordinator',
    ]);
}
}