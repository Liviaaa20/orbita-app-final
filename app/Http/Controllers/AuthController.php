<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi menggunakan username, bukan email
        $credentials = $request->validate([
            'username' => ['required'], // Mengikuti wireframe login kamu
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ]);
    }

    public function logout(Request $request)
    {
        \Auth::logout();
    
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        // Pastikan diarahkan ke route 'login' yang bertipe GET
        return redirect()->route('login'); 
    }
}