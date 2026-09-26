<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Kategori;
use App\Models\SubKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AlatController extends Controller
{
      private function checkMasterDataAccess()
   {
    if (!auth()->user()->canManageMasterData()) {
        abort(403, 'Akses ditolak');
    }
    }
    public function index()
    {
        $alats = Alat::with('subKategori.kategori')->latest()->get();
        $kategoris = Kategori::all();
        return view('MasterData.dataalat_index', compact('alats', 'kategoris'));
    }

    public function getSubKategori($kategori_id)
    {
        // Ambil sub kategori berdasarkan kategori_id
        $subKategori = SubKategori::where('kategori_id', $kategori_id)->get();
        
        // Kembalikan sebagai JSON
        return response()->json($subKategori);
    }
    
    public function store(Request $request)
    {
        $this->checkMasterDataAccess();
        $request->validate([
            'sub_kategori_id' => 'required',
            'nama_alat'       => 'required',
            'nomor_seri'      => 'required|unique:alats,nomor_seri',
            'foto_alat'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
    
        $namaFoto = null;
        if ($request->hasFile('foto_alat')) {
            $file = $request->file('foto_alat');
            $namaFoto = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/img/alat'), $namaFoto);
        }
    
        // PERBAIKAN: Masukkan semua kolom baru ke sini
        Alat::create([
            'sub_kategori_id' => $request->sub_kategori_id,
            'nama_alat'       => $request->nama_alat,
            'nomor_seri'      => $request->nomor_seri,
            'jenis'           => $request->jenis,
            'lokasi'          => $request->lokasi,
            'status'          => $request->status,
            'kondisi'         => $request->kondisi,
            'foto_alat'       => $namaFoto,
            // KOLOM BARU WAJIB ADA DI SINI:
            'merk_type'       => $request->merk_type,
            'tahun_pengadaan' => $request->tahun_pengadaan,
            'rentang_ukur'    => $request->rentang_ukur,
            'resolusi'        => $request->resolusi,
            'akurasi'         => $request->akurasi,
        ]);
    
        return redirect()->back()->with('success', 'Data alat berhasil ditambahkan!');
    }
    
    public function update(Request $request, $id)
    {
        $this->checkMasterDataAccess();
        $alat = Alat::findOrFail($id);
    
        $request->validate([
            'sub_kategori_id' => 'required',
            'nama_alat'       => 'required',
            'nomor_seri'      => 'required|unique:alats,nomor_seri,' . $id,
            'foto_alat'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
    
        // Menggunakan all() sudah benar, tapi pastikan Model mengizinkan (Fillable)
        $data = $request->all();
    
        if ($request->hasFile('foto_alat')) {
            if ($alat->foto_alat && File::exists(public_path('assets/img/alat/' . $alat->foto_alat))) {
                File::delete(public_path('assets/img/alat/' . $alat->foto_alat));
            }
    
            $file = $request->file('foto_alat');
            $namaFoto = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/img/alat'), $namaFoto);
            $data['foto_alat'] = $namaFoto;
        }
    
        $alat->update($data);
    
        return redirect()->back()->with('success', 'Data alat berhasil diperbarui!');
    }

        public function destroy($id)
        {
        $this->checkMasterDataAccess();

        $alat = Alat::findOrFail($id);
    
    // Hapus file foto dari folder public
    if ($alat->foto_alat && File::exists(public_path('assets/img/alat/' . $alat->foto_alat))) {
        File::delete(public_path('assets/img/alat/' . $alat->foto_alat));
    }

    $alat->delete();
    return redirect()->back()->with('success', 'Data berhasil dihapus');
}
}