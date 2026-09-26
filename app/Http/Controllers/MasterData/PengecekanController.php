<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller; // Wajib ada karena sekarang di sub-folder
use App\Models\Alat;
use App\Models\Kategori;     // <--- Pastikan ini merujuk ke folder Models
use App\Models\Pengecekan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengecekanController extends Controller
{
      private function checkMasterDataAccess()
   {
    if (!auth()->user()->canManageMasterData()) {
        abort(403, 'Akses ditolak');
    }
    }
    public function index()
    {
        $this->checkMasterDataAccess();
        // Hapus 'MasterData' dari dalam array with()
        // Karena MasterData adalah FOLDER, bukan RELASI database.
        $kategoris = Kategori::with(['subKategoris.alats'])->get();
        
        return view('MasterData.pengecekan_index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $this->checkMasterDataAccess();
        $request->validate([
            'waktu' => 'required|in:Pagi,Siang,Sore',
            'alat_id' => 'required|array', // Menangkap banyak ID alat sekaligus
        ]);

        $tanggal = now()->format('Y-m-d');
        $waktu = $request->waktu;
        $userID = Auth::id();

        // Looping untuk menyimpan setiap alat yang dicentang
        foreach ($request->alat_id as $idAlat) {
            // Cek dulu apakah hari ini di waktu yang sama alat sudah dicek (biar nggak double)
            $exists = Pengecekan::where('alat_id', $idAlat)
                                ->where('tanggal', $tanggal)
                                ->where('waktu', $waktu)
                                ->exists();

            if (!$exists) {
                Pengecekan::create([
                    'alat_id'   => $idAlat,
                    'user_id'   => $userID,
                    'tanggal'   => $tanggal,
                    'waktu'     => $waktu,
                    'is_checked' => true,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Data pengecekan berhasil disimpan!');
    }
}
