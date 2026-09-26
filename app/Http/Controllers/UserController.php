<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        $roles = Role::all();
        return view('MasterUser.user_index', compact('users', 'roles'));
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required',
        // NIP dibuat optional dan tidak dibatasi jumlah digitnya
        'nip' => 'required' 
    ]);

    User::create([
        'kode_user' => $request->kode_user,
        'name' => $request->name,
        'username' => $request->name,
        'email' => $request->email,
        'nip' => $request->nip,
        'password' => Hash::make($request->password),
        'role_id' => $request->role_id,
        'status' => 'aktif',
    ]);

    return back()->with('success', 'User berhasil ditambah!');
}

public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $user->update([
        'name' => $request->name,
        'email' => $request->email,
        'nip' => $request->nip,
        'role_id' => $request->role_id,
        'status' => $request->status,
    ]);

    if ($request->filled('password')) {
        $user->update(['password' => Hash::make($request->password)]);
    }

    return back()->with('success', 'User berhasil diupdate!');
}

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus');
    }
}