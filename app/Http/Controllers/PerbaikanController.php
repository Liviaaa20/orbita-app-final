<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\SubKategori;
use App\Models\Perbaikan;
use App\Models\HistoriOperasional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;


class PerbaikanController extends Controller
{
    protected $roleBisaInput = [
        'Observer',
        'Tata Usaha',
        'Forecaster',
        'Kepala Kelompok',
        'Koordinator',
    ];

    public function index()
    {
        $perbaikans = Perbaikan::with('alat')
            ->latest()
            ->get();

        return view('perbaikan.index', compact('perbaikans'));
    }

    /*
    |-----------------------------------------
    | CREATE - Form Permintaan Perbaikan
    |-----------------------------------------
    | REVISI: dropdown bertingkat Kategori -> Sub Kategori -> Alat.
    | Semua data (kategori, sub kategori, alat) di-load sekaligus
    | saat halaman dibuka (volume data kecil), lalu difilter murni
    | via JavaScript di sisi client tanpa AJAX tambahan.
    |
    | Setiap Alat dibekali atribut 'sub_kategori_id' (sudah ada di
    | kolom tabel alats) dan SubKategori dibekali 'kategori_id'
    | (juga sudah ada di tabel sub_kategoris), sehingga JS bisa
    | langsung memfilter <option> berdasarkan kedua id tersebut
    | tanpa perlu query tambahan ke server.
    */
    public function create()
    {
        $userRole = Auth::user()->role->nama_role ?? '';

        if (!in_array($userRole, $this->roleBisaInput)) {
            abort(403, 'Otoritas tidak cukup.');
        }

        $kategoris = Kategori::orderBy('nama_kategori')->get();

        $subKategoris = SubKategori::orderBy('nama_sub_kategori')->get();

        $alats = Alat::orderBy('nama_alat')->get();

        return view('perbaikan.create', compact('kategoris', 'subKategoris', 'alats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'alat_id'            => 'nullable|exists:alats,id',
            'kategori_perbaikan' => 'required|string',
            'keterangan'         => 'required|string',
            'foto'               => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'alat_id.exists' => 'Data alat yang dipilih tidak valid. Silakan pilih ulang Kategori, Sub Kategori, dan Alat.',
        ]);

        DB::beginTransaction();

        try {

            $data = [
                'no_tiket'           => 'TKT-' . strtoupper(uniqid()),
                'alat_id'            => $request->alat_id,
                'tgl_permintaan'     => now(),
                'user'               => Auth::user()->name,
                'kategori_perbaikan' => $request->kategori_perbaikan,
                'keterangan'         => $request->keterangan,

                // workflow awal (TETAP DIPERTAHANKAN)
                'status'             => 'pending',

                // FIX: validasi memang tidak ada di migration → kita biarkan tapi tidak dipakai logic
                'validasi'           => null,

                'validasi_koordinator' => null,
            ];

            if ($request->hasFile('foto')) {
                $data['foto_awal'] = $request->file('foto')
                    ->store('perbaikan/foto_awal', 'public');
            }

            $perbaikan = Perbaikan::create($data);

            HistoriOperasional::create([
                'alat_id'         => $request->alat_id,
                'user_id'         => Auth::id(),
                'jenis_aktivitas' => 'Gangguan',
                'waktu'           => now(),
                'kategori'        => '-',
                'lokasi'          => 'Stasiun Meteorologi Maritim Semarang',
                'deskripsi_hasil' => '[LAPORAN KERUSAKAN] ' . $request->keterangan,
                'dokumen'         => $data['foto_awal'] ?? null,
            ]);

            DB::commit();

            return redirect()
                ->route('perbaikan.index')
                ->with('success', 'Permintaan berhasil dikirim.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /*
    |-----------------------------------------
    | VALIDASI ADMIN / TEKNISI
    |-----------------------------------------
    | FIX BUG: 'status' di tabel perbaikans adalah ENUM('pending','onproses','selesai').
    | Value 'ditolak' BUKAN bagian dari enum tersebut, sehingga MySQL men-truncate
    | dan melempar QueryException (1265 Data truncated). Status tetap di 'pending'
    | saat ditolak; kolom 'validasi' = 0 yang menjadi penanda bahwa permintaan ini
    | pernah ditolak (tampil sebagai badge "Tolak" di view, bukan ubah enum status).
    */
    public function validasi(Request $request, $id)
    {
        $role = strtolower(Auth::user()->role->nama_role ?? '');

        if (!in_array($role, ['admin', 'teknisi'])) {
            abort(403);
        }

        $perbaikan = Perbaikan::findOrFail($id);

        // FIX: validasi tetap dipertahankan tapi tidak jadi logic utama
        if ($request->action == 'terima') {

            $perbaikan->update([
                'validasi'     => 1,
                'tgl_diterima' => now(),

                // FIX penting: status harus sinkron migration
                'status'       => 'onproses'
            ]);

            return back()->with(
                'success',
                'Permintaan berhasil diterima.'
            );
        }

        $perbaikan->update([
            'validasi' => 0,

            // FIX: 'ditolak' bukan value enum yang valid, tetap 'pending'
            'status'   => 'pending'
        ]);

        return back()->with(
            'info',
            'Permintaan ditolak.'
        );
    }

    /*
    |-----------------------------------------
    | UPDATE TEKNISI
    |-----------------------------------------
    | REVISI:
    | - Status disesuaikan dengan enum DB asli (pending, onproses, selesai)
    | - Saat status diubah ke 'selesai', foto_selesai WAJIB diisi
    | - tgl_selesai otomatis terisi saat status = selesai
    | - BARU: saat teknisi set status='selesai', tiket otomatis naik ke
    |   koordinator untuk verifikasi. Kolom 'catatan' diisi otomatis
    |   "Menunggu Verifikasi Koordinator" dan validasi_koordinator
    |   direset ke null (supaya tidak kebawa status ACC/Tolak lama
    |   kalau ini adalah upload ulang setelah ditolak sebelumnya).
    */
    public function update(Request $request, $id)
    {
        $role = strtolower(Auth::user()->role->nama_role ?? '');

        if (!in_array($role, ['admin', 'teknisi'])) {
            abort(403);
        }

        $perbaikan = Perbaikan::findOrFail($id);

        $request->validate([
            'status'        => ['required', Rule::in(['pending', 'onproses', 'selesai'])],
            'catatan'       => 'nullable|string',

            // Wajib hanya kalau status yang dikirim = selesai DAN tiket ini belum punya foto_selesai sebelumnya
            'foto_selesai'  => [
                Rule::requiredIf(function () use ($request, $perbaikan) {
                    return $request->status === 'selesai' && !$perbaikan->foto_selesai;
                }),
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ], [
            'foto_selesai.required' => 'Foto bukti penyelesaian wajib diunggah saat status diubah ke Selesai.',
        ]);

        $updateData = [
            'status'  => $request->status,
        ];

        if ($request->hasFile('foto_selesai')) {
            $updateData['foto_selesai'] = $request->file('foto_selesai')
                ->store('perbaikan/foto_selesai', 'public');
        }

        if ($request->status == 'selesai') {

            $updateData['tgl_selesai'] = now();

            // BARU: kirim otomatis ke koordinator untuk verifikasi
            $updateData['catatan_teknisi']
            =$request->catatan_teknisi;

            $updateData['validasi_koordinator'] = null;

        } else {

            // Jika status dikembalikan ke onproses/pending secara manual oleh teknisi,
            // reset tanggal selesai & catatan custom dari request tetap dipakai
            $updateData['tgl_selesai'] = null;
            $updateData['catatan']     = $request->catatan;
        }

        $perbaikan->update($updateData);

        return back()->with(
            'success',
            'Data perbaikan berhasil diperbarui.'
        );
    }

    /*
    |-----------------------------------------
    | VALIDASI KOORDINATOR
    |-----------------------------------------
    | BARU - Alur verifikasi:
    | - Tiket berstatus 'selesai' dengan validasi_koordinator masih null
    |   dianggap "Menunggu Verifikasi Koordinator" (lihat kolom catatan).
    | - ACC  -> validasi_koordinator = 1, catatan = "ACC Koordinator".
    |           Tiket final selesai.
    | - Tolak -> validasi_koordinator = 0, catatan = "Ditolak Koordinator",
    |            status balik ke 'onproses', foto_selesai & tgl_selesai
    |            DIRESET supaya teknisi wajib upload ulang dari awal.
    */
    public function validasiKoordinator(Request $request, $id)
    {
        $role = strtolower(Auth::user()->role->nama_role ?? '');

        if ($role != 'koordinator') {
            abort(403);
        }

        $perbaikan = Perbaikan::findOrFail($id);

        if ($request->action == 'setuju') {

    $perbaikan->update([
        'validasi_koordinator' => 1,
        'tgl_verifikasi'       => now(),
    ]);

    return back()->with(
        'success',
        'Perbaikan telah divalidasi koordinator.'
    );
}

        // Hapus file foto_selesai lama dari storage sebelum direset
        if ($perbaikan->foto_selesai) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($perbaikan->foto_selesai);
        }

        $perbaikan->update([
    'validasi_koordinator' => 0,
    'tgl_verifikasi'       => now(),

    // Kembalikan ke teknisi
    'status'       => 'onproses',
    'foto_selesai' => null,
    'tgl_selesai'  => null,
]);

        return back()->with(
            'warning',
            'Perbaikan dikembalikan ke teknisi.'
        );
    }

    /*
    |-----------------------------------------
    | PREVIEW / DETAIL (BARU)
    |-----------------------------------------
    | Dipanggil via AJAX (fetch/axios) dari modal Preview di halaman index.
    | Mengembalikan JSON berisi seluruh detail tiket + url foto + label alat.
    */
    public function show($id)
    {
        $perbaikan = Perbaikan::with('alat')->findOrFail($id);

        return response()->json([
            'no_tiket'             => $perbaikan->no_tiket,
            'alat'                 => $perbaikan->alat->nama_alat ?? '-',
            'user'                 => $perbaikan->user,
            'kategori_perbaikan'   => $perbaikan->kategori_perbaikan,
            'keterangan'           => $perbaikan->keterangan,
            'status'               => $perbaikan->status,
            'catatan'              => $perbaikan->catatan_teknisi,
            'validasi_koordinator' => $perbaikan->validasi_koordinator,
            'tgl_permintaan'       => optional($perbaikan->tgl_permintaan)->format('d-m-Y H:i'),
            'tgl_diterima'         => optional($perbaikan->tgl_diterima)->format('d-m-Y H:i'),
            'tgl_selesai'          => optional($perbaikan->tgl_selesai)->format('d-m-Y H:i'),
            'foto_awal_url'        => $perbaikan->foto_awal ? asset('storage/' . $perbaikan->foto_awal) : null,
            'foto_selesai_url'     => $perbaikan->foto_selesai ? asset('storage/' . $perbaikan->foto_selesai) : null,
        ]);
    }

    /*
    |-----------------------------------------
    | DOWNLOAD PDF
    |-----------------------------------------
    */
    public function download($id)
    {
        \Carbon\Carbon::setLocale('id');

        $perbaikan = Perbaikan::with('alat')
            ->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'perbaikan.pdf_perbaikan',
            compact('perbaikan')
        );

        return $pdf->download(
            'Laporan_Perbaikan_' .
            $perbaikan->no_tiket .
            '.pdf'
        );
    }
}