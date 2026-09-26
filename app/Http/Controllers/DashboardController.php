<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alat;
use App\Models\JadwalDinas;
use App\Models\Maintenance;
use App\Models\Pengecekan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            Carbon::setLocale('id'); 
            $today = Carbon::today();
            $tahun = $request->get('tahun', date('Y'));

            // 1. Jadwal Dinas Hari Ini
            $jadwalDinas = JadwalDinas::whereDate('tanggal', $today)->get();

            // 2. Status Harian (Shift)
$statusHarian = [];
foreach (['Pagi', 'Siang', 'Malam'] as $s) {
    $m = Maintenance::where('jenis_maintenance', 'harian')
            ->whereDate('tanggal', $today)
            ->where('shift', $s)
            ->first();
            
    $statusHarian[$s] = [
        'ada'     => !is_null($m),
        'is_done' => ($m && $m->status === 'selesai'), // HANYA TRUE JIKA SELESAI
    ];
}

            // 3. Status Mingguan (Filter berdasarkan Hari Indonesia)
            // Di DashboardController.php bagian Status Mingguan
$hariIni = $today->translatedFormat('l'); 

// Di DashboardController.php
// Di DashboardController.php
// Di DashboardController.php (Bagian $alatMingguan)
$alatMingguan = Maintenance::where('jenis_maintenance', 'mingguan')
    ->whereDate('tanggal', $today)
    ->where('shift', $hariIni)
    ->with(['alat.subKategori.kategori'])
    ->get()
    ->groupBy(function($m) {
        return $m->alat->subKategori->kategori->nama_kategori ?? 'Lainnya';
    })
    ->map(function($group) {
        $totalAlat = $group->count();
        $selesai   = $group->where('status', 'selesai')->count();
        
        // Ambil data kategori untuk dilempar ke form
        $kategori = $group->first()->alat->subKategori->kategori ?? null;
        
        return (object)[
            'id_kategori'   => $kategori ? $kategori->id : null, // ID Kategori penting!
            'nama_kategori' => $kategori ? $kategori->nama_kategori : 'Lainnya',
            'is_done'       => ($totalAlat > 0 && $totalAlat == $selesai),
            'jumlah_alat'   => $totalAlat,
            'sudah_dicek'   => $selesai
        ];
    });

            // 4. Statistik Grafik
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $dataNormal = array_fill(0, 12, 0);
            $dataRusak = array_fill(0, 12, 0);

            $stats = DB::table('pengecekans')
                ->join('alats', 'pengecekans.alat_id', '=', 'alats.id')
                ->select(
                    DB::raw('MONTH(pengecekans.tanggal) as bulan'),
                    DB::raw('SUM(CASE WHEN alats.kondisi = "Baik" THEN 1 ELSE 0 END) as total_baik'),
                    DB::raw('SUM(CASE WHEN alats.kondisi != "Baik" THEN 1 ELSE 0 END) as total_rusak')
                )
                ->whereYear('pengecekans.tanggal', $tahun)
                ->groupBy('bulan')
                ->get();

            foreach ($stats as $stat) {
                if ($stat->bulan >= 1 && $stat->bulan <= 12) {
                    $dataNormal[$stat->bulan - 1] = (int)$stat->total_baik;
                    $dataRusak[$stat->bulan - 1] = (int)$stat->total_rusak;
                }
            }

            $listTahun = DB::table('pengecekans')->selectRaw('YEAR(tanggal) as tahun')->distinct()->pluck('tahun');
            if($listTahun->isEmpty()) $listTahun = collect([(int)date('Y')]);
      
            return view('Dashboard.dashboard_adm', compact(
                'jadwalDinas', 'statusHarian', 'alatMingguan', 'months', 
                'dataNormal', 'dataRusak', 'today', 'tahun', 'listTahun', 'hariIni'
            ));

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}