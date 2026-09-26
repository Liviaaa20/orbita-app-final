<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\SubKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubKategoriController extends Controller
    {
    private function checkMasterDataAccess()
   {
    if (!auth()->user()->canManageMasterData()) {
        abort(403, 'Akses ditolak');
    }
    } 
    public function index()
    {
        $kategori = Kategori::orderByRaw('CAST(SUBSTRING(kode_kategori,2) AS UNSIGNED)')
            ->get();
    
        $kategoriWithSub = Kategori::with([
            'subKategoris' => function ($query) {
                $query->orderByRaw('CAST(SUBSTRING(kode_sub_kategori,3) AS UNSIGNED)');
            }
        ])
        ->orderByRaw('CAST(SUBSTRING(kode_kategori,2) AS UNSIGNED)')
        ->get();
    
        return view('MasterData.subkategori_index', compact(
            'kategoriWithSub',
            'kategori'
        ));
    }

    public function store(Request $request)
    {
        $this->checkMasterDataAccess();
        $request->validate([
            'kategori_id' => 'required|exists:kategoris,id', // Tambahkan validasi exists agar aman
            'nama_sub_kategori' => 'required|array',
        ]);
    
        $countInserted = 0;
    
        foreach ($request->nama_sub_kategori as $nama) {
            if ($nama) {
                // Cek apakah sudah ada (case-insensitive agar 'Gps' dan 'GPS' dianggap sama)
                $exists = SubKategori::where('kategori_id', $request->kategori_id)
                                     ->where('nama_sub_kategori', 'LIKE', $nama)
                                     ->exists();
    
                if (!$exists) {
                    // Generate Kode yang lebih aman
                    $latest = SubKategori::latest('id')->first();
                    $nextNumber = $latest ? ((int) substr($latest->kode_sub_kategori, 2)) + 1 : 1;
                    $kodeBaru = 'SK' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    
                    SubKategori::create([
                        'kategori_id' => $request->kategori_id,
                        'nama_sub_kategori' => $nama,
                        'kode_sub_kategori' => $kodeBaru
                    ]);
                    $countInserted++;
                }
            }
        }
    
        if ($countInserted > 0) {
            return redirect()->back()->with('success', "$countInserted Sub Kategori berhasil ditambahkan!");
        }
    
        return redirect()->back()->with('error', 'Tidak ada data baru yang ditambahkan (mungkin sudah ada).');
    }

    public function update(Request $request, $id)
    {
        $this->checkMasterDataAccess();
        $sub = SubKategori::findOrFail($id);
        $sub->update($request->all());

        return redirect()->back()->with('success', 'Sub Kategori berhasil diupdate!');
    }

    public function destroy($id)
    {
        $this->checkMasterDataAccess();
        SubKategori::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Sub Kategori berhasil dihapus!');
    }

}