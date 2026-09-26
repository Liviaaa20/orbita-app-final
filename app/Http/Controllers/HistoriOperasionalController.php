<?php

namespace App\Http\Controllers;

use App\Models\HistoriOperasional;
use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Perbaikan;
use Illuminate\Support\Facades\DB; // Tambahkan ini

class HistoriOperasionalController extends Controller
{
    public function index(Request $request)
    {
        $kategoris = Kategori::all();
        $alats = Alat::all();
        $jenisAktivitas = ['Maintenance Harian', 'Maintenance Mingguan', 'Kalibrasi', 'Gangguan', 'Pengujian', 'Lainnya'];
    
        // --- QUERY 1: Dari Tabel Histori ---
        $queryHistori = DB::table('histori_operasionals as h')
            ->leftJoin('alats as a', 'h.alat_id', '=', 'a.id')
            ->leftJoin('sub_kategoris as sk', 'a.sub_kategori_id', '=', 'sk.id')
            ->leftJoin('kategoris as k', 'sk.kategori_id', '=', 'k.id')
            ->leftJoin('users as u', 'h.user_id', '=', 'u.id')
            ->select(
                'h.id',
                'h.waktu as tanggal_raw',
                'h.jenis_aktivitas',
                'a.nama_alat',
                'k.nama_kategori',
                'sk.nama_sub_kategori',
                'a.lokasi',
                'h.deskripsi_hasil as deskripsi',
                'u.name as petugas',
                'h.dokumen',
                'h.alat_id'
            );
    
        // --- QUERY 2: Dari Tabel Pengecekan ---
        $queryPengecekan = DB::table('pengecekans as p')
            ->leftJoin('alats as a', 'p.alat_id', '=', 'a.id')
            ->leftJoin('sub_kategoris as sk', 'a.sub_kategori_id', '=', 'sk.id')
            ->leftJoin('kategoris as k', 'sk.kategori_id', '=', 'k.id')
            ->leftJoin('users as u', 'p.user_id', '=', 'u.id')
            ->select(
                'p.id',
                'p.tanggal as tanggal_raw',
                DB::raw("CASE WHEN p.waktu IN ('Pagi', 'Siang', 'Malam') THEN 'Maintenance Harian' ELSE 'Maintenance Mingguan' END as jenis_aktivitas"),
                'a.nama_alat',
                'k.nama_kategori',
                'sk.nama_sub_kategori',
                'a.lokasi',
                DB::raw("CONCAT('Kondisi: ', p.kondisi_akhir) as deskripsi"),
                'u.name as petugas',
                'p.foto_kegiatan as dokumen',
                'p.alat_id'
            );
    
        // Menggabungkan Query
        $combined = $queryHistori->unionAll($queryPengecekan);
    
        // Membungkus dalam subquery agar filter berfungsi global
        $finalQuery = DB::table(DB::raw("({$combined->toSql()}) as combined_table"))
            ->mergeBindings($combined);
    
        // Logika Filter
        if ($request->filled('periode')) {
            $range = explode(' - ', $request->periode);
            try {
                $start = Carbon::createFromFormat('d/m/Y', trim($range[0]))->startOfDay();
                $end = Carbon::createFromFormat('d/m/Y', trim($range[1]))->endOfDay();
                $finalQuery->whereBetween('tanggal_raw', [$start, $end]);
            } catch (\Exception $e) {}
        }
        
        if ($request->filled('jenis_aktivitas')) {
            $finalQuery->where('jenis_aktivitas', $request->jenis_aktivitas);
        }
        if ($request->filled('alat_id')) {
            $finalQuery->where('alat_id', $request->alat_id);
        }
        if ($request->filled('kategori')) {
            $finalQuery->where('nama_kategori', $request->kategori);
        }
        if ($request->filled('lokasi')) {
            $finalQuery->where('lokasi', $request->lokasi);
        }
    
        // Eksekusi Pagination
        $histori = $finalQuery->orderBy('tanggal_raw', 'desc')->paginate(15);
    
        // Hitung summary dari query yang sudah terfilter (Gunakan clone agar tidak merusak query utama)
        $summaryQuery = clone $finalQuery;
        $summary = [
            'total' => $summaryQuery->count(),
            'maintenance_harian' => (clone $summaryQuery)->where('jenis_aktivitas', 'Maintenance Harian')->count(),
            'maintenance_mingguan' => (clone $summaryQuery)->where('jenis_aktivitas', 'Maintenance Mingguan')->count(),
            'kalibrasi' => (clone $summaryQuery)->where('jenis_aktivitas', 'Kalibrasi')->count(),
            'gangguan' => (clone $summaryQuery)->where('jenis_aktivitas', 'Gangguan')->count(),
            'lainnya' => (clone $summaryQuery)->where('jenis_aktivitas', 'Lainnya')->count(),
        ];
    
        return view('histori.index', compact('histori', 'kategoris', 'alats', 'jenisAktivitas', 'summary'));
    }

    public function show($id, Request $request)
    {
        $jenis = trim($request->query('jenis'));
    
        if (in_array($jenis, ['Maintenance Harian', 'Maintenance Mingguan'])) {
    
            $data = \App\Models\Pengecekan::with([
                'alat.subKategori.kategori',
                'user'
            ])->findOrFail($id);
    
            // Tambahkan field virtual agar view bisa dipakai bersama
            $data->jenis_aktivitas   = $jenis;
            $data->waktu_pelaksanaan = $data->waktu;
            $data->shift             = $data->waktu;
            $data->dokumen           = $data->foto_kegiatan;
            $data->deskripsi_hasil   = $data->catatan;
            $data->tanggal_display   = Carbon::parse($data->tanggal)->translatedFormat('d F Y');
    
        } else {
    
            $data = HistoriOperasional::with([
                'alat.subKategori.kategori',
                'user'
            ])->findOrFail($id);
            $perbaikan = Perbaikan::where('alat_id', $data->alat_id)
    ->latest('tgl_permintaan')
    ->first();

if ($perbaikan) {

    $data->status_perbaikan = $perbaikan->status;

    $data->kondisi_akhir = match($perbaikan->status){
        'pending'  => 'Rusak Berat',
        'onproses' => 'Rusak Ringan',
        'selesai'  => 'Baik',
        default    => '-'
    };

    $data->catatan = $perbaikan->catatan_teknisi
        ?? $perbaikan->catatan;

}
            $data->tanggal_display = Carbon::parse($data->waktu)->translatedFormat('d F Y');
            $data->shift = Carbon::parse($data->waktu)->format('H:i');
            $data->catatan = $perbaikan->catatan_teknisi;
            $data->uraian_kerusakan = $perbaikan->keterangan;
            $data->tindakan_perbaikan = $perbaikan->kategori_perbaikan;
        }
    
        return view('histori.show', compact('data'));
    }

    // Export Laporan Bulanan (Landscape - Tabel Banyak Alat)
    public function export(Request $request)
{
    $bulan = now()->translatedFormat('F');
    $tahun = now()->format('Y');

    /*
    |--------------------------------------------------------------------------
    | QUERY HISTORI
    |--------------------------------------------------------------------------
    */

    $queryHistori = DB::table('histori_operasionals as h')
        ->leftJoin('alats as a', 'h.alat_id', '=', 'a.id')
        ->leftJoin('sub_kategoris as sk', 'a.sub_kategori_id', '=', 'sk.id')
        ->leftJoin('kategoris as k', 'sk.kategori_id', '=', 'k.id')
        ->leftJoin('users as u', 'h.user_id', '=', 'u.id')
        ->select(
            'h.id',
            'h.waktu as tanggal_raw',
            'h.jenis_aktivitas',
            'a.id as alat_id',
            'a.nama_alat',
            'a.lokasi',
            'k.nama_kategori',
            'sk.nama_sub_kategori',
            'h.deskripsi_hasil',
            'h.dokumen',
            'u.name as petugas',
            DB::raw('NULL as kondisi_akhir'),
            DB::raw('NULL as catatan')
        );

    /*
    |--------------------------------------------------------------------------
    | QUERY MAINTENANCE
    |--------------------------------------------------------------------------
    */

    $queryPengecekan = DB::table('pengecekans as p')
        ->leftJoin('alats as a', 'p.alat_id', '=', 'a.id')
        ->leftJoin('sub_kategoris as sk', 'a.sub_kategori_id', '=', 'sk.id')
        ->leftJoin('kategoris as k', 'sk.kategori_id', '=', 'k.id')
        ->leftJoin('users as u', 'p.user_id', '=', 'u.id')
        ->select(
            'p.id',
            'p.tanggal as tanggal_raw',
            DB::raw("CASE
                WHEN p.waktu IN ('Pagi','Siang','Malam')
                THEN 'Maintenance Harian'
                ELSE 'Maintenance Mingguan'
            END as jenis_aktivitas"),
            'a.id as alat_id',
            'a.nama_alat',
            'a.lokasi',
            'k.nama_kategori',
            'sk.nama_sub_kategori',
            DB::raw('NULL as deskripsi_hasil'),
            'p.foto_kegiatan as dokumen',
            'u.name as petugas',
            'p.kondisi_akhir',
            'p.catatan'
        );

    /*
    |--------------------------------------------------------------------------
    | UNION
    |--------------------------------------------------------------------------
    */

    $combined = $queryHistori->unionAll($queryPengecekan);

    $query = DB::table(DB::raw("({$combined->toSql()}) as laporan"))
        ->mergeBindings($combined);

    /*
    |--------------------------------------------------------------------------
    | FILTER PERIODE
    |--------------------------------------------------------------------------
    */

    if ($request->filled('periode')) {

        $range = explode(' - ', $request->periode);

        $start = Carbon::createFromFormat('d/m/Y', trim($range[0]))->startOfDay();

        $end = Carbon::createFromFormat('d/m/Y', trim($range[1]))->endOfDay();

        $query->whereBetween('tanggal_raw', [$start, $end]);

        $bulan = $start->translatedFormat('F');

        $tahun = $start->format('Y');
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER
    |--------------------------------------------------------------------------
    */

    if ($request->filled('jenis_aktivitas')) {
        $query->where('jenis_aktivitas', $request->jenis_aktivitas);
    }

    if ($request->filled('alat_id')) {
        $query->where('alat_id', $request->alat_id);
    }

    if ($request->filled('kategori')) {
        $query->where('nama_kategori', $request->kategori);
    }

    if ($request->filled('lokasi')) {
        $query->where('lokasi', $request->lokasi);
    }

    $data = $query
        ->orderBy('tanggal_raw', 'desc')
        ->get();

    return Pdf::loadView('histori.pdf_monitoring', compact(
        'data',
        'bulan',
        'tahun'
    ))
        ->setPaper('a4', 'landscape')
        ->setOption('isRemoteEnabled', true)
        ->setOption('isHtml5ParserEnabled', true)
        ->stream('Laporan_Monitoring.pdf');
}

    // Export Riwayat Satu Alat (Portrait - Per Alat)
    public function downloadRiwayat($id, Request $request)
    {
        // Ambil parameter jenis dari URL
        $jenis = $request->query('jenis');
    
        // Cek apakah data berasal dari tabel Pengecekan (Maintenance)
        if (in_array($jenis, ['Maintenance Harian', 'Maintenance Mingguan'])) {

            $data = \App\Models\Pengecekan::with([
                'alat.subKategori.kategori',
                'user'
            ])->findOrFail($id);
        
            $data->jenis_aktivitas = $jenis;
            $data->tanggal_display = $data->tanggal;
            $data->waktu_pelaksanaan = $data->waktu;
            $data->dokumen = $data->foto_kegiatan;
        
            // Mapping agar template PDF bisa dipakai bersama
            $data->uraian_kerusakan = '-';
            $data->tindakan_perbaikan = 'Maintenance Rutin';
            $data->deskripsi_hasil = $data->catatan;
            $data->catatan_khusus = $data->catatan;
        }
        else {

            $data = HistoriOperasional::with([
                'alat.subKategori.kategori',
                'user'
            ])->findOrFail($id);
        
            // Mapping supaya semua jenis aktivitas konsisten
            $data->uraian_kerusakan = $data->uraian_kerusakan ?? '-';
            $data->tindakan_perbaikan = $data->tindakan_perbaikan ?? '-';
            $data->catatan_khusus = $data->deskripsi_hasil;
        }
        // Ambil seluruh riwayat perbaikan alat
        $riwayatPerbaikan = Perbaikan::where('alat_id', $data->alat_id)
        ->orderBy('tgl_permintaan', 'desc')
        ->get();
        
        $pdf = Pdf::loadView(
            'histori.pdf_riwayat_alat',
            compact('data', 'riwayatPerbaikan')
        )              ->setPaper('a4', 'portrait')
              ->setOption('isRemoteEnabled', true)      // Tambahkan ini
              ->setOption('isHtml5ParserEnabled', true); // Tambahkan ini
        
        return $pdf->stream(
    'Riwayat_Alat_' . ($data->alat?->nama_alat ?? 'Tanpa_Alat') . '.pdf');
    }

    public function downloadSingle($id)
    {
        $data = HistoriOperasional::findOrFail($id);
        
        if (!$data->dokumen) {
            return back()->with('error', 'Dokumen tidak ditemukan.');
        }

        $path = storage_path('app/public/' . $data->dokumen);
        return response()->download($path);
    }
}