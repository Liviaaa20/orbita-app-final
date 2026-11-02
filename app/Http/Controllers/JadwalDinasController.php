<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalDinas;
use App\Models\MasterShift;
use App\Imports\JadwalDinasImport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class JadwalDinasController extends Controller
{
    private function getUserRole(): string
    {
        return strtolower(trim(auth()->user()->role->nama_role ?? ''));
    }

    private function bisaLihat(): bool
    {
        return in_array($this->getUserRole(), [
            'admin', 'koordinator', 'kepala kelompok', 'Kepala Kelompok', 'kepala_kelompok', 'kapok', 'teknisi'
        ]);
    }

    private function bisaInput(): bool
    {
        return in_array($this->getUserRole(), [
             'kepala kelompok', 'Kepala Kelompok', 'kepala_kelompok', 'kapok'
        ]);
    }

    public function index(Request $request)
    {
        if (!$this->bisaLihat()) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman Jadwal Dinas.');
        }

        $periodeInput = $request->input(
            'periode',
            Carbon::now()->format('Y-m-d')
        );

        try {
            $date = Carbon::parse($periodeInput);
        } catch (\Exception $e) {
            $date = Carbon::now();
        }

        $jadwals = JadwalDinas::whereMonth('tanggal', $date->month)
            ->whereYear('tanggal', $date->year)
            ->get();

        $masterShifts = MasterShift::orderBy('kode_shift')->get();

        $bisaInput = $this->bisaInput();

        return view(
            'jadwal_dinas.index',
            compact(
                'jadwals',
                'periodeInput',
                'bisaInput',
                'masterShifts'
            )
        );
    }

    /*
    |-----------------------------------------------------------------
    | RIWAYAT JADWAL DINAS (BARU)
    |-----------------------------------------------------------------
    | Tabel flat: 1 baris = 1 entry (nama, tanggal, shift, jam),
    | berbeda dari tampilan kalender per minggu/bulan di index().
    | Bisa difilter per nama petugas, rentang tanggal, dan kode shift.
    | Akses: sama seperti index() -> semua role yang bisaLihat().
    */
    public function riwayat(Request $request)
    {
        if (!$this->bisaLihat()) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman Riwayat Jadwal Dinas.');
        }

        $query = JadwalDinas::query();

        // Filter nama petugas (partial match, case-insensitive)
        if ($request->filled('nama')) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }

        // Filter rentang tanggal
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal', [
                $request->tanggal_mulai,
                $request->tanggal_selesai,
            ]);
        } elseif ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        } elseif ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        }

        // Filter kode shift
        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        // Default: tampilkan riwayat 30 hari terakhir s/d hari ini jika
        // belum ada filter tanggal apapun, supaya tabel tidak langsung
        // memuat seluruh histori sejak awal yang bisa sangat panjang.
        $adaFilterTanggal = $request->filled('tanggal_mulai') || $request->filled('tanggal_selesai');

        if (!$adaFilterTanggal) {
            $query->whereBetween('tanggal', [
                Carbon::now()->subDays(30)->format('Y-m-d'),
                Carbon::now()->format('Y-m-d'),
            ]);
        }

        $riwayatJadwal = $query->orderBy('tanggal', 'desc')
            ->orderBy('nama')
            ->paginate(25)
            ->withQueryString();

        // Daftar nama unik untuk dropdown filter (datalist)
        $daftarNama = JadwalDinas::select('nama')
            ->distinct()
            ->orderBy('nama')
            ->pluck('nama');

        $masterShifts = MasterShift::orderBy('kode_shift')->get();

        return view('jadwal_dinas.riwayat', compact(
            'riwayatJadwal',
            'daftarNama',
            'masterShifts',
            'adaFilterTanggal'
        ));
    }

    public function create()
    {
        if (!$this->bisaInput()) {
            return redirect()->route('jadwal_dinas.index')
                ->with('error', 'Anda tidak memiliki akses untuk menginput jadwal.');
        }

        $masterShifts = MasterShift::orderBy('kode_shift')->get();

        return view(
            'jadwal_dinas.create',
            compact('masterShifts')
        );
    }

    public function store(Request $request)
    {
        if (!$this->bisaInput()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'shift_id' => 'required|exists:master_shift,id',
        ]);

        try {

            $shift = MasterShift::findOrFail(
                $request->shift_id
            );

            JadwalDinas::create([
                'nama' => $request->nama,
                'tanggal' => $request->tanggal,

                'shift_id' => $shift->id,

                'shift' => $shift->kode_shift,

                'jam' => Carbon::parse($shift->jam_mulai)->format('H:i')
                    . ' - ' .
                    Carbon::parse($shift->jam_selesai)->format('H:i')
                    . ' WIB',
            ]);

            return redirect()
                ->route('jadwal_dinas.index')
                ->with(
                    'success',
                    'Jadwal dinas berhasil ditambahkan!'
                );

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Gagal menyimpan: ' . $e->getMessage()
                );
        }
    }

    /*
    |-----------------------------------------------------------------
    | IMPORT JADWAL DINAS DARI CSV / XLSX
    |-----------------------------------------------------------------
    | Format kolom file (header baris pertama): nama | tanggal | shift
    | - 'shift' wajib cocok dengan kode_shift di Master Shift.
    | - Kombinasi (nama, tanggal) yang sudah ada di DB akan di-SKIP.
    | - Jika ADA baris lain yang gagal validasi (selain duplikat),
    |   SELURUH import dibatalkan (rollback total) dan daftar error
    |   ditampilkan kembali ke user — tidak ada data yang tersimpan
    |   sebagian.
    */
    public function import(Request $request)
    {
        if (!$this->bisaInput()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'file_jadwal' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
        ], [
            'file_jadwal.required' => 'Silakan pilih file CSV atau XLSX terlebih dahulu.',
            'file_jadwal.mimes'    => 'Format file harus CSV, XLS, atau XLSX.',
            'file_jadwal.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        $import = new JadwalDinasImport();

        try {
            Excel::import($import, $request->file('file_jadwal'));
        } catch (\Exception $e) {
            return back()->with(
                'error',
                'Gagal membaca file: ' . $e->getMessage()
            );
        }

        // Jika ada baris yang gagal validasi -> ROLLBACK TOTAL, tidak ada yang disimpan
        if (!empty($import->errors)) {
            return back()->with([
                'import_errors' => $import->errors,
                'error' => 'Import dibatalkan. Ditemukan ' . count($import->errors) . ' baris bermasalah pada file. Perbaiki file lalu unggah ulang.',
            ]);
        }

        // Tidak ada error sama sekali -> baru simpan semua baris valid
        DB::transaction(function () use ($import) {
            $import->simpanSemua();
        });

        $pesanSukses = "Import berhasil: {$import->successCount} jadwal baru ditambahkan.";

        if ($import->skippedCount > 0) {
            $pesanSukses .= " {$import->skippedCount} baris dilewati karena sudah ada (duplikat nama & tanggal).";
        }

        return redirect()
            ->route('jadwal_dinas.index')
            ->with('success', $pesanSukses)
            ->with('import_success', [
                'success_count' => $import->successCount,
                'skipped_count' => $import->skippedCount,
                'message'       => $pesanSukses,
            ]);
    }

    public function download(Request $request)
    {
        if (!$this->bisaLihat()) {
            return abort(403, 'Anda tidak memiliki akses untuk mengunduh jadwal.');
        }

        $tipe  = $request->input('tipe_periode');
        $query = JadwalDinas::query();

        if ($tipe === 'mingguan') {
            $mulai   = $request->input('tanggal_mulai');
            $selesai = $request->input('tanggal_selesai');

            $jadwal = $query->whereBetween('tanggal', [$mulai, $selesai])
                            ->orderBy('tanggal', 'asc')
                            ->get();

            $labelPeriode = 'Periode: '
                . Carbon::parse($mulai)->isoFormat('D MMMM YYYY')
                . ' s/d '
                . Carbon::parse($selesai)->isoFormat('D MMMM YYYY');
        } else {
            $bulanTahun = $request->input('bulan_pilihan');
            [$tahun, $bulan] = explode('-', $bulanTahun);

            $jadwal = $query->whereMonth('tanggal', $bulan)
                            ->whereYear('tanggal', $tahun)
                            ->orderBy('tanggal', 'asc')
                            ->get();

            $labelPeriode = 'Periode Bulan: '
                . Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM YYYY');
        }

        return view('jadwal_dinas.cetak', compact('jadwal', 'labelPeriode'));
    }
}