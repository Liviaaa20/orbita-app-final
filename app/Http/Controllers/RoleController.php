<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('MasterUser.role_index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_role' => 'required|unique:roles',
            'nama_role' => 'required'
        ]);

        Role::create($request->all());
        return redirect()->back()->with('success', 'Role berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->update($request->all());
        return redirect()->back()->with('success', 'Role berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Role::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Role berhasil dihapus!');
    }
}
