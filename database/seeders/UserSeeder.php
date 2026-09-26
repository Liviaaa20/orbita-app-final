<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder // Nama class harus UserSeeder
{
    public function run(): void
    {
        // 1. Buat Role (Pastikan ID-nya nanti dirujuk oleh User)
        $adminRole = Role::create([
            'kode_role' => 'R001',
            'nama_role' => 'admin'
        ]);

        Role::create([
            'kode_role' => 'R002',
            'nama_role' => 'teknisi'
        ]);

        // 2. Buat User contoh (Hajirin Arafat) sesuai wireframe
        User::create([
            'kode_user' => 'U001',
            'name'      => 'Hajirin Arafat',
            'username'  => 'admin',
            'email'     => 'admin@gmail.com',
            'password'  => Hash::make('admin123'),
            'role_id'   => $adminRole->id,
            'status'    => 'aktif',
        ]);
    }
}