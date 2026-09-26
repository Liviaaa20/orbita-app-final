<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('auth.register', compact('roles'));
    }

    public function store(Request $request)
    {
        // 1. Tambahkan validasi NIP dan Email
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'nip'      => 'required|numeric|unique:users', // Validasi NIP 18 digit
            'email'    => 'required|email|unique:users', // Email wajib divalidasi juga
            'password' => 'required|string|min:4|confirmed',
            'role_id'  => 'required|exists:roles,id'
        ]);
    
        // 2. Logika pembuatan Kode User (U001, dst)
        $latestUser = User::orderBy('id', 'desc')->first();

        if (!$latestUser) {
            $kode_user = 'U001';
        } else {
            // Pastikan menggunakan fallback jika kode_user terakhir formatnya beda
            $lastNumber = preg_replace('/[^0-9]/', '', $latestUser->kode_user);
            $number = intval($lastNumber) + 1;
            $kode_user = 'U' . str_pad($number, 3, '0', STR_PAD_LEFT);
        }

        // 3. Simpan data ke Database
        User::create([
            'kode_user' => $kode_user,
            'username'  => $request->username,
            'name'      => $request->username, 
            'nip'       => $request->nip, // SIMPAN NIP DISINI
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role_id'   => $request->role_id,
            'status'    => 'aktif',
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }
}