<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Kategori; 
use Illuminate\Http\Request;

class KategoriController extends Controller
{
   private function checkMasterDataAccess()
   {
    if (!auth()->user()->canManageMasterData()) {
        abort(403, 'Akses ditolak');
    }
    } 
    public function index()
    {
        $kategori = Kategori::all();
        return view('MasterData.kategori_index', compact('kategori'));
    }

    public function store(Request $request)
    {
    $this->checkMasterDataAccess();

    $request->validate([
        'kode_kategori' => 'required|unique:kategoris,kode_kategori',
        'nama_kategori' => 'required',
        'jenis'         => 'required',
    ]);

    Kategori::create([
        'kode_kategori' => $request->kode_kategori,
        'nama_kategori' => $request->nama_kategori,
        'jenis'         => $request->jenis,
    ]);

    return redirect()->back()->with('success', 'Kategori Berhasil Ditambah');
    }
    public function update(Request $request, $id)
    {
        $this->checkMasterDataAccess();

        $request->validate([
            'nama_kategori'   => 'required',
            'jenis'           => 'required',
        ]);

        $kategori = Kategori::findOrFail($id);
        
        $kategori->update([
            'nama_kategori'   => $request->nama_kategori,
            'jenis'           => $request->jenis,
        ]);

        return redirect()->back()->with('success', 'Data kategori berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->checkMasterDataAccess();

        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        return redirect()->back()->with('success', 'Data kategori berhasil dihapus!');
    }
}