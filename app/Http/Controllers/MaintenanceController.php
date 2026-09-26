<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Kategori;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
       private function checkMaintenanceAccess()
   {
    if (!auth()->user()->canManageMaintenance()) {
        abort(403, 'Akses ditolak');
    }
    } 

    // =========================================================
    //  INDEX HARIAN  →  menampilkan riwayat sesi maintenance
    // =========================================================
    public function indexHarian(Request $request)
    {
        $dari   = $request->dari   ?? now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->sampai ?? now()->format('Y-m-d');
        $shift  = $request->shift;
        $status = $request->status;

        // Query: group by tanggal + shift, hitung jumlah alat per sesi
        $query = Maintenance::select(
                'tanggal',
                'shift',
                DB::raw('COUNT(*) as total_alat'),
                DB::raw("SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as jumlah_selesai"),
                DB::raw("SUM(CASE WHEN status = 'proses'  THEN 1 ELSE 0 END) as jumlah_proses")
            )
            ->where('jenis_maintenance', 'harian')
            ->whereBetween('tanggal', [$dari, $sampai]);

        if ($shift)  $query->where('shift', $shift);

        // Filter status: 'selesai' = semua alat selesai, 'proses' = masih ada yang proses
        if ($status == 'selesai') {
            $query->havingRaw("jumlah_proses = 0");
        } elseif ($status == 'proses') {
            $query->havingRaw("jumlah_proses > 0");
        }

        $sesiList = $query->groupBy('tanggal', 'shift')
                          ->orderBy('tanggal', 'desc')
                          ->orderBy('shift', 'asc')
                          ->get();

        // Summary untuk card atas
        $totalSesi      = $sesiList->count();
        $totalAlatDicek = $sesiList->sum('total_alat');
        $totalSelesai   = $sesiList->where('jumlah_proses', 0)->count();
        $totalProses    = $sesiList->where('jumlah_proses', '>', 0)->count();

        return view('maintenance.harian_index', compact(
            'sesiList',
            'totalSesi',
            'totalAlatDicek',
            'totalSelesai',
            'totalProses'
        ));
    }

    // =========================================================
    //  INDEX MINGGUAN  →  menampilkan riwayat sesi maintenance
    // =========================================================
    public function indexMingguan(Request $request)
    {
        $dari   = $request->dari   ?? now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->sampai ?? now()->format('Y-m-d');
        $shift  = $request->shift;  // shift di sini = nama hari (Senin, Selasa, ...)
        $status = $request->status;

        $query = Maintenance::select(
                'tanggal',
                'shift',
                DB::raw('COUNT(*) as total_alat'),
                DB::raw("SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as jumlah_selesai"),
                DB::raw("SUM(CASE WHEN status = 'proses'  THEN 1 ELSE 0 END) as jumlah_proses")
            )
            ->where('jenis_maintenance', 'mingguan')
            ->whereBetween('tanggal', [$dari, $sampai]);

        if ($shift)  $query->where('shift', $shift);

        if ($status == 'selesai') {
            $query->havingRaw("jumlah_proses = 0");
        } elseif ($status == 'proses') {
            $query->havingRaw("jumlah_proses > 0");
        }

        $sesiList = $query->groupBy('tanggal', 'shift')
                          ->orderBy('tanggal', 'desc')
                          ->get();

        $totalSesi      = $sesiList->count();
        $totalAlatDicek = $sesiList->sum('total_alat');
        $totalSelesai   = $sesiList->where('jumlah_proses', 0)->count();
        $totalProses    = $sesiList->where('jumlah_proses', '>', 0)->count();

        return view('maintenance.mingguan_index', compact(
            'sesiList',
            'totalSesi',
            'totalAlatDicek',
            'totalSelesai',
            'totalProses'
        ));
    }

    // =========================================================
    //  FORM INPUT BARU  (dulu indexHarian / indexMingguan)
    // =========================================================
    public function createHarian()
    {
        $this->checkMaintenanceAccess();
        return view('maintenance.harian_create');   // file lama: harian_index.blade.php → rename jadi harian_create
    }

    public function createMingguan()
    {
        $this->checkMaintenanceAccess();
        return view('maintenance.mingguan_create'); // file lama: mingguan_index.blade.php → rename jadi mingguan_create
    }

    // =========================================================
    //  SHOW PENGECEKAN  (pilih lokasi / kategori)
    // =========================================================
    public function showPengecekan(Request $request)
    {
        $this->checkMaintenanceAccess();
        $type    = $request->type ?? 'harian';
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $waktu   = $request->waktu;

        if ($type == 'mingguan' && !$waktu) {
            $waktu = date('l', strtotime($tanggal));
            $hariMap = [
                'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
            ];
            $waktu = $hariMap[$waktu] ?? $waktu;
        } elseif (!$waktu) {
            $waktu = 'Pagi';
        }

        $kategoris = Kategori::whereHas('subKategoris.alats', function ($query) use ($type) {
            $query->where('jenis', $type);
        })->with(['subKategoris.alats' => function ($query) use ($type) {
            $query->where('jenis', $type);
        }])->get();

        return view('MasterData.pengecekan_index', compact('kategoris', 'tanggal', 'waktu', 'type'));
    }

    // =========================================================
    //  STORE INISIASI
    // =========================================================
    public function storeInisiasi(Request $request)
    {
        $this->checkMaintenanceAccess();
        $request->validate([
            'tanggal' => 'required|date',
            'waktu'   => 'required',
            'type'    => 'required|in:harian,mingguan',
            'lokasi'  => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->input('lokasi') as $kategori_id => $lokasis) {
                $alats = \App\Models\Alat::whereIn('lokasi', $lokasis)
                    ->whereHas('subKategori', function ($query) use ($kategori_id) {
                        $query->where('kategori_id', $kategori_id);
                    })->get();

                foreach ($alats as $alat) {
                    Maintenance::updateOrCreate(
                        [
                            'tanggal' => $request->tanggal,
                            'shift'   => $request->waktu,
                            'alat_id' => $alat->id,
                        ],
                        [
                            'jenis_maintenance' => $request->type,
                            'status'            => 'proses',
                        ]
                    );
                }
            }

            DB::commit();

            $route = $request->type == 'mingguan'
                ? route('maintenance.mingguan')
                : route('maintenance.harian');

            return redirect($route)->with('success', 'Sesi pengecekan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // =========================================================
    //  FORM MASTER  (isi hasil pengecekan per alat)
    // =========================================================
    public function formMaster(Request $request)
    {
        $this->checkMaintenanceAccess();
        $tanggal    = $request->tanggal;
        $waktu      = $request->waktu;
        $type       = $request->type;
        $kategoriId = $request->kategori_id;

        $queryPending = Maintenance::where('tanggal', $tanggal)
            ->where('shift', $waktu)
            ->where('status', 'proses')
            ->where('jenis_maintenance', $type);

        $alatIdsPending = $queryPending->pluck('alat_id');

        $querySub = \App\Models\SubKategori::with(['kategori', 'alats' => function ($query) use ($alatIdsPending) {
            $query->whereIn('id', $alatIdsPending);
        }])->whereHas('alats', function ($query) use ($alatIdsPending) {
            $query->whereIn('id', $alatIdsPending);
        });

        if ($type == 'mingguan' && $kategoriId) {
            $querySub->where('kategori_id', $kategoriId);
        }

        $subKategoris = $querySub->get();

        $groupedByKategori = $subKategoris->groupBy(function ($sub) {
            return $sub->kategori->nama_kategori ?? 'Tanpa Kategori';
        });

        return view('maintenance.form_master', compact('groupedByKategori', 'tanggal', 'waktu', 'type'));
    }

    // =========================================================
    //  STORE HASIL (simpan hasil pengecekan + update status)
    // =========================================================
    public function storeHasilFisik(Request $request)
    {
        $this->checkMaintenanceAccess();
        $request->validate([
            'tanggal' => 'required|date',
            'waktu'   => 'required',
            'type'    => 'required',
            'alat'    => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->alat as $alatId => $data) {
                $pathFoto = null;
                if ($request->hasFile("alat.$alatId.foto_kegiatan")) {
                    $file     = $request->file("alat.$alatId.foto_kegiatan");
                    $namaFile = time() . '_' . $alatId . '.' . $file->getClientOriginalExtension();
                    $pathFoto = $file->storeAs('uploads/maintenance', $namaFile, 'public');
                }

                Maintenance::where('tanggal', $request->tanggal)
                    ->where('shift', $request->waktu)
                    ->where('alat_id', $alatId)
                    ->update(['status' => 'selesai']);

                DB::table('pengecekans')->insert([
                    'alat_id'       => $alatId,
                    'user_id'       => auth()->id(),
                    'tanggal'       => $request->tanggal,
                    'waktu'         => $request->waktu,
                    'is_checked'    => true,
                    'kondisi_akhir' => $data['kondisi_fisik'],
                    'foto_kegiatan' => $pathFoto,
                    'catatan'       => $data['catatan_khusus'] ?? null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            DB::commit();

            // Redirect kembali ke index sesuai jenis maintenance
            $route = $request->type == 'mingguan'
                ? route('maintenance.mingguan')
                : route('maintenance.harian');

            return redirect($route)->with('success', 'Hasil pengecekan berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // =========================================================
    //  DETAIL SESI  (lihat hasil per sesi tanggal + shift)
    // =========================================================
    public function detailSesi(Request $request)
    {
        $tanggal = $request->tanggal;
        $shift   = $request->shift;
        $type    = $request->type;

        $dataMaintenance = Maintenance::with(['alat.subKategori.kategori'])
            ->where('tanggal', $tanggal)
            ->where('shift', $shift)
            ->where('jenis_maintenance', $type)
            ->get();

        // Gabungkan dengan data pengecekan (kondisi fisik, catatan, foto)
        $alatIds  = $dataMaintenance->pluck('alat_id');
        $pengecekan = DB::table('pengecekans')
            ->whereIn('alat_id', $alatIds)
            ->where('tanggal', $tanggal)
            ->where('waktu', $shift)
            ->get()
            ->keyBy('alat_id');

        $groupedByKategori = $dataMaintenance->groupBy(function ($item) {
            return $item->alat->subKategori->kategori->nama_kategori ?? 'Tanpa Kategori';
        });

        return view('maintenance.detail_sesi', compact(
            'groupedByKategori',
            'pengecekan',
            'tanggal',
            'shift',
            'type'
        ));
    }
}