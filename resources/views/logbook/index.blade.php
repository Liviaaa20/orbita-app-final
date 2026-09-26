@extends('layouts.master')

@section('content')
@php
    $userRole      = strtolower(Auth::user()->role->nama_role ?? '');
    $isTeknisi       = $userRole === 'teknisi';
    $isKapok = in_array($userRole, ['kepalakelompok', 'kepala kelompok', 'kepala_kelompok', 'kapok']);
    $isKoordinator = $userRole === 'koordinator';

    // BARU: data untuk dropdown Periode (Bulan + Tahun), menggantikan input manual.
    $namaBulanIndo = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];
    $tahunSekarangPeriode = now()->year;
    $daftarTahunPeriode   = range($tahunSekarangPeriode - 3, $tahunSekarangPeriode + 2);
@endphp

<style>
    .btn-gradient-submit {
        background: linear-gradient(135deg, #003366, #004d99);
        border: none; color: #fff;
        padding: 6px 14px; border-radius: 8px;
        font-weight: 600; font-size: 13px;
    }
    .btn-gradient-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,51,102,0.3);
        color: #fff;
    }
    .stat-card { border-radius: 12px; transition: all .25s ease; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
    .icon-stat {
        width: 45px; height: 45px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
    }

    /* BARU: Panel info alat (preview) di modal Tambah/Edit Logbook */
    .panel-info-alat {
        background: #f8f9fa;
        border: 1px solid #e3e6ea;
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 1rem;
    }
    .panel-info-alat .info-alat-title {
        font-size: 0.8rem;
        font-weight: 700;
        color: #003366;
        text-transform: uppercase;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .panel-info-alat .info-alat-row {
        display: flex;
        font-size: 0.8rem;
        padding: 3px 0;
        border-bottom: 1px dashed #e3e6ea;
    }
    .panel-info-alat .info-alat-row:last-child { border-bottom: none; }
    .panel-info-alat .info-alat-label {
        width: 40%;
        color: #6c757d;
        font-weight: 600;
    }
    .panel-info-alat .info-alat-value {
        width: 60%;
        color: #212529;
        font-weight: 600;
        word-break: break-word;
    }
    .panel-info-alat-empty {
        text-align: center;
        color: #adb5bd;
        font-size: 0.8rem;
        padding: 10px 0;
    }

    /* BARU: Field yang terisi otomatis (Jenis Logbook & Lokasi) — readonly, bukan disabled,
       supaya nilainya tetap ikut ke-submit ke server. */
    .field-otomatis {
        background: #f1f3f5 !important;
        cursor: not-allowed;
    }
</style>

<div class="container-fluid px-4 py-3">

    {{-- ===== HEADER ===== --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="m-0 text-dark font-weight-bold" style="font-size: 1.5rem;">
                <i class="fas fa-book-open mr-2" style="color: #003366;"></i>Logbook Operasional
            </h4>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Manajemen data logbook laboratorium</p>
        </div>
        <div class="col-md-6">
            <ol class="breadcrumb float-md-right bg-transparent m-0 p-0" style="font-size: 0.8rem;">
                <li class="breadcrumb-item text-muted">Master Data</li>
                <li class="breadcrumb-item active font-weight-bold" style="color: #003366;">Logbook</li>
            </ol>
        </div>
    </div>

    {{-- ===== NOTIFIKASI ===== --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center" style="border-radius: 8px;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center" style="border-radius: 8px;">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- ===== STATISTIK ===== --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card" style="border-left: 4px solid #003366;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-stat" style="background: #003366; color: #fff;"><i class="fas fa-list"></i></div>
                        <div class="ml-3">
                            <p class="mb-0 text-muted" style="font-size: 0.75rem;">Total Logbook</p>
                            <h5 class="mb-0 font-weight-bold">{{ $logbooks->total() }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card" style="border-left: 4px solid #ffc107;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-stat" style="background: #ffc107; color: #fff;"><i class="fas fa-clock"></i></div>
                        <div class="ml-3">
                            <p class="mb-0 text-muted" style="font-size: 0.75rem;">Menunggu</p>
                            <h5 class="mb-0 font-weight-bold">
                                {{ $logbooks->where('status', 'pending_kapok')->count() + $logbooks->where('status', 'pending_koordinator')->count() }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card" style="border-left: 4px solid #28a745;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-stat" style="background: #28a745; color: #fff;"><i class="fas fa-check-circle"></i></div>
                        <div class="ml-3">
                            <p class="mb-0 text-muted" style="font-size: 0.75rem;">Disetujui</p>
                            <h5 class="mb-0 font-weight-bold">{{ $logbooks->where('status', 'approved_final')->count() }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card" style="border-left: 4px solid #dc3545;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-stat" style="background: #dc3545; color: #fff;"><i class="fas fa-times-circle"></i></div>
                        <div class="ml-3">
                            <p class="mb-0 text-muted" style="font-size: 0.75rem;">Ditolak</p>
                            <h5 class="mb-0 font-weight-bold">
                                {{ $logbooks->whereIn('status', ['rejected_kapok', 'rejected_koordinator'])->count() }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== FILTER ===== --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form action="{{ route('logbook.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <label class="text-muted font-weight-semibold mb-2" style="font-size: 0.8rem;">Jenis Logbook</label>
                        <select name="jenis_logbook" class="form-control" style="border-radius: 8px; height: 42px;" onchange="this.form.submit()">
                            <option value="Semua Logbook">Semua</option>
                            @foreach($opsiJenisLogbook as $opt)
                                <option value="{{ $opt }}" {{ request('jenis_logbook') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        {{-- DIUBAH: filter kategori, bukan sub_kategori --}}
                        <label class="text-muted font-weight-semibold mb-2" style="font-size: 0.8rem;">Kategori</label>
                        <select name="kategori_id" class="form-control" style="border-radius: 8px; height: 42px;" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoris as $kat)
                                <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="text-muted font-weight-semibold mb-2" style="font-size: 0.8rem;">Status</label>
                        <select name="status" class="form-control" style="border-radius: 8px; height: 42px;" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="draft"                {{ request('status') == 'draft'                ? 'selected' : '' }}>Draft</option>
                            <option value="pending_kapok"        {{ request('status') == 'pending_kapok'        ? 'selected' : '' }}>Menunggu Kepala Kelompok</option>
                            <option value="pending_koordinator"  {{ request('status') == 'pending_koordinator'  ? 'selected' : '' }}>Menunggu Koordinator</option>
                            <option value="approved_final"       {{ request('status') == 'approved_final'       ? 'selected' : '' }}>Disetujui Final</option>
                            <option value="rejected_kapok"       {{ request('status') == 'rejected_kapok'       ? 'selected' : '' }}>Ditolak Kepala Kelompok</option>
                            <option value="rejected_koordinator" {{ request('status') == 'rejected_koordinator' ? 'selected' : '' }}>Ditolak Koordinator</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted font-weight-semibold mb-2" style="font-size: 0.8rem;">Pencarian</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                   style="border-radius: 8px 0 0 8px; height: 42px;"
                                   placeholder="Cari logbook..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-light border" type="submit" style="border-radius: 0 8px 8px 0;">
                                    <i class="fas fa-search text-muted"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        @if($isTeknisi)
                            <button type="button" class="btn btn-gradient-submit btn-block shadow-sm"
                                    data-toggle="modal" data-target="#modalTambahLogbook">
                                <i class="fas fa-plus mr-2"></i>Tambah
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== TABEL ===== --}}
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header border-0 bg-white py-3">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-list text-primary mr-2"></i>Daftar Logbook
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size: 0.875rem;">
                    <thead class="bg-light font-weight-semibold border-bottom">
                        <tr>
                            <th class="border-0 py-3 pl-4" style="width: 50px;">No</th>
                            <th class="border-0 py-3">Jenis Logbook</th>
                            <th class="border-0 py-3">Kategori</th>  {{-- DIUBAH dari Sub Kategori --}}
                            <th class="border-0 py-3">Lokasi</th>
                            <th class="border-0 py-3">Periode</th>
                            <th class="border-0 py-3">Status</th>
                            <th class="border-0 py-3">Approval</th>
                            <th class="border-0 py-3 text-center pr-4" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logbooks as $log)
                        <tr>
                            <td class="py-3 pl-4 text-muted font-weight-bold">
                                {{ ($logbooks->currentPage() - 1) * $logbooks->perPage() + $loop->iteration }}
                            </td>
                            <td class="py-3">
                                <div class="font-weight-semibold">{{ $log->jenis_logbook }}</div>
                                <small class="text-muted">{{ $log->jenis_alat }}</small>
                            </td>
                            <td class="py-3">
                                {{-- DIUBAH: tampilkan kategori --}}
                                @if($log->kategori)
                                    <span class="badge px-2 py-1"
                                          style="background: #e3f2fd; color: #003366; border-radius: 6px;">
                                        <i class="fas fa-layer-group mr-1"></i>{{ $log->kategori->nama_kategori }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="py-3 text-muted">{{ $log->lokasi_tempat }}</td>
                            <td class="py-3">{{ $log->periode_tersedia }}</td>
                            <td class="py-3">
                                @switch($log->status)
                                    @case('draft')
                                        <span class="badge badge-secondary">Draft</span> @break
                                    @case('pending_kapok')
                                        <span class="badge badge-warning">Menunggu Kepala Kelompok</span> @break
                                    @case('pending_koordinator')
                                        <span class="badge badge-warning">Menunggu Koord</span> @break
                                    @case('approved_final')
                                        <span class="badge badge-success">Disetujui</span> @break
                                    @case('rejected_kapok')
                                        <span class="badge badge-danger">Ditolak Kepala Kelompok</span> @break
                                    @case('rejected_koordinator')
                                        <span class="badge badge-danger">Ditolak Koord</span> @break
                                    @default
                                        <span class="badge badge-light">{{ strtoupper($log->status) }}</span>
                                @endswitch
                            </td>
                            <td class="py-3" style="font-size: 0.8rem;">
                                @if($log->approved_kapok_by && $log->approvedKapokOleh)
                                    <div class="text-success">
                                        <i class="fas fa-check mr-1"></i>{{ $log->approvedKapokOleh->name }}
                                    </div>
                                @endif
                                @if($log->approved_koordinator_by && $log->approvedKoordinatorOleh)
                                    <div class="text-success">
                                        <i class="fas fa-check-double mr-1"></i>{{ $log->approvedKoordinatorOleh->name }}
                                    </div>
                                @endif
                                @if(empty($log->approved_kapok_by) && empty($log->approved_koordinator_by))
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="py-3 text-center pr-4">
                                <div class="d-flex justify-content-center" style="gap: 4px;">
                                    <a href="{{ route('logbook.show', $log->id) }}"
                                       class="btn btn-sm"
                                       style="border-radius: 6px; background: #17a2b8; color: #fff;"
                                       title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($isTeknisi && $log->status == 'draft')
                                        <button type="button" class="btn btn-sm btn-light border" title="Edit"
                                                data-toggle="modal" data-target="#modalEditLogbook"
                                                onclick="pemicuEdit(this)"
                                                data-id="{{ $log->id }}"
                                                data-kategori_id="{{ $log->kategori_id }}"
                                                data-jenis_logbook="{{ $log->jenis_logbook }}"
                                                data-jenis_alat="{{ $log->jenis_alat }}"
                                                data-lokasi_tempat="{{ $log->lokasi_tempat }}"
                                                data-periode_tersedia="{{ $log->periode_tersedia }}">
                                            <i class="fas fa-pencil-alt text-warning"></i>
                                        </button>

                                        <button type="button"
                                                class="btn btn-sm btn-gradient-submit"
                                                title="Ajukan"
                                                data-toggle="modal"
                                                data-target="#modalSubmitLogbook"
                                                data-id="{{ $log->id }}"
                                                data-jenis="{{ $log->jenis_logbook }}">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm btn-light border btn-delete"
                                                title="Hapus" data-toggle="modal" data-target="#modalDeleteLogbook"
                                                data-id="{{ $log->id }}"
                                                data-nama="{{ $log->jenis_logbook }}">
                                            <i class="fas fa-trash-alt text-danger"></i>
                                        </button>
                                    @endif

                                    @if($isKapok && $log->status == 'pending_kapok')
                                        <button type="button" class="btn btn-sm btn-success" title="Setuju"
                                                data-toggle="modal" data-target="#modalApproveKapok"
                                                onclick="setModalId('modalApproveKapokForm', '{{ route('logbook.approve-kapok', $log->id) }}')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" title="Tolak"
                                                data-toggle="modal" data-target="#modalRejectKapok"
                                                onclick="setModalId('modalRejectKapokForm', '{{ route('logbook.reject-kapok', $log->id) }}')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif

                                    @if($isKoordinator && $log->status == 'pending_koordinator')
                                        <button type="button" class="btn btn-sm btn-success" title="Setuju Final"
                                                data-toggle="modal" data-target="#modalApproveKoordinator"
                                                onclick="setModalId('modalApproveKoordinatorForm', '{{ route('logbook.approve-koordinator', $log->id) }}')">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" title="Tolak"
                                                data-toggle="modal" data-target="#modalRejectKoordinator"
                                                onclick="setModalId('modalRejectKoordinatorForm', '{{ route('logbook.reject-koordinator', $log->id) }}')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif

                                    @if($log->status == 'approved_final')
                                        <a href="{{ route('download-pdf', $log->id) }}"
                                           class="btn btn-sm"
                                           style="border-radius: 6px; background: #dc3545; color: #fff;"
                                           title="Download PDF" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-book-open fa-2x mb-3 d-block text-light"></i>
                                Belum ada data logbook
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logbooks->hasPages())
        <div class="card-footer bg-light py-3">
            {{ $logbooks->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>

{{-- Data sumber untuk dropdown bertingkat (filter murni via JS, tanpa AJAX) --}}
<script type="application/json" id="dataSubKategoriLogbook">
    {!! $subKategoris->map(function ($sk) {
        return [
            'id' => $sk->id,
            'kategori_id' => $sk->kategori_id,
            'nama' => $sk->nama_sub_kategori,
        ];
    })->values()->toJson() !!}
</script>

<script type="application/json" id="dataAlatLogbook">
    {!! $semuaAlat->map(function ($a) {
        return [
            'id' => $a->id,
            'sub_kategori_id' => $a->sub_kategori_id,
            'nama' => $a->nama_alat,
            'merk_type' => $a->merk_type,
            'nomor_seri' => $a->nomor_seri,
            'tahun_pengadaan' => $a->tahun_pengadaan,
            'rentang_ukur' => $a->rentang_ukur,
            'resolusi' => $a->resolusi,
            'akurasi' => $a->akurasi,
            'lokasi' => $a->lokasi,
            'kondisi' => $a->kondisi,
            'status' => $a->status,
        ];
    })->values()->toJson() !!}
</script>

{{-- ===== MODAL TAMBAH ===== --}}
<div class="modal fade" id="modalTambahLogbook" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header border-0" style="background: #f8f9fa; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold">Tambah Logbook Baru</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('logbook.store') }}" method="POST" class="form-logbook-bertingkat" data-prefix="tambah">
                @csrf
                <div class="modal-body">

                    {{-- 1. KATEGORI --}}
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">1. Kategori <span class="text-danger">*</span></label>
                        <select name="kategori_id" class="form-control select-kategori" data-prefix="tambah"
                                style="border-radius: 8px; height: 46px;" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. SUB KATEGORI (tidak disimpan ke logbook, hanya untuk filter alat) --}}
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">2. Sub Kategori <span class="text-danger">*</span></label>
                        <select class="form-control select-sub-kategori" data-prefix="tambah"
                                style="border-radius: 8px; height: 46px;" disabled>
                            <option value="">-- Pilih Kategori Dahulu --</option>
                        </select>
                    </div>

                    {{-- 3. ALAT (preview saja, TIDAK dikirim ke server) --}}
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">3. Data Alat (Referensi) <span class="text-danger">*</span></label>
                        <select class="form-control select-alat" data-prefix="tambah"
                                style="border-radius: 8px; height: 46px;" disabled>
                            <option value="">-- Pilih Sub Kategori Dahulu --</option>
                        </select>
                        <small class="text-muted">Dipilih sebagai contoh/acuan informasi alat pada kategori ini. Lokasi di bawah otomatis ikut terisi dari alat ini.</small>
                    </div>

                    {{-- DIV INFO ALAT --}}
                    <div class="panel-info-alat panel-info-alat-tambah">
                        <div class="panel-info-alat-empty">
                            <i class="fas fa-info-circle mr-1"></i> Pilih alat untuk melihat informasi lengkapnya
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- 4. JENIS LOGBOOK (BARU: otomatis, readonly) --}}
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">4. Jenis Logbook <span class="text-danger">*</span></label>
                        <input type="text" name="jenis_logbook" class="form-control input-jenis-logbook field-otomatis" data-prefix="tambah"
                               style="border-radius: 8px; height: 46px;"
                               placeholder="Otomatis terisi setelah Kategori dipilih" readonly required>
                        <small class="text-muted">Format otomatis: "Logbook Peralatan [Kategori]"</small>
                    </div>

                    {{-- jenis_alat: otomatis terisi dari Sub Kategori, readonly --}}
                    <input type="hidden" name="jenis_alat" class="input-jenis-alat-hidden" data-prefix="tambah">

                    {{-- BARU: Lokasi otomatis dari data Alat, readonly --}}
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">Lokasi <span class="text-danger">*</span></label>
                        <input type="text" name="lokasi_tempat" class="form-control input-lokasi-tempat field-otomatis" data-prefix="tambah"
                               style="border-radius: 8px; height: 46px;"
                               placeholder="Otomatis terisi setelah Alat dipilih" readonly required>
                        <small class="text-muted">Diambil dari data lokasi pada menu Data Alat.</small>
                    </div>

                    {{-- 5. PERIODE (BARU: pilih Bulan + Tahun, bukan ketik manual) --}}
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">5. Periode <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-7">
                                <select class="form-control select-periode-bulan" data-prefix="tambah"
                                        style="border-radius: 8px; height: 46px;" required>
                                    <option value="">-- Pilih Bulan --</option>
                                    @foreach($namaBulanIndo as $nb)
                                        <option value="{{ $nb }}">{{ $nb }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-5">
                                <select class="form-control select-periode-tahun" data-prefix="tambah"
                                        style="border-radius: 8px; height: 46px;" required>
                                    <option value="">-- Tahun --</option>
                                    @foreach($daftarTahunPeriode as $thn)
                                        <option value="{{ $thn }}">{{ $thn }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="periode_tersedia" class="input-periode-tersedia" data-prefix="tambah">
                    </div>
                </div>
                <div class="modal-footer border-0" style="background: #f8f9fa; border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-outline-secondary" style="border-radius: 8px;" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-submit-logbook text-white" style="border-radius: 8px; background: #003366;" disabled>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL EDIT ===== --}}
<div class="modal fade" id="modalEditLogbook" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header border-0" style="background: #f8f9fa; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold">Perbarui Logbook</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="" method="POST" id="formEditLogbook" class="form-logbook-bertingkat" data-prefix="edit">
                @csrf
                @method('PUT')
                <div class="modal-body">

                    {{-- 1. KATEGORI --}}
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">1. Kategori <span class="text-danger">*</span></label>
                        <select name="kategori_id" id="edit_kategori_id" class="form-control select-kategori" data-prefix="edit"
                                style="border-radius: 8px; height: 46px;" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. SUB KATEGORI --}}
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">2. Sub Kategori <span class="text-danger">*</span></label>
                        <select class="form-control select-sub-kategori" data-prefix="edit"
                                style="border-radius: 8px; height: 46px;" disabled>
                            <option value="">-- Pilih Kategori Dahulu --</option>
                        </select>
                    </div>

                    {{-- 3. ALAT (preview saja) --}}
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">3. Data Alat (Referensi) <span class="text-danger">*</span></label>
                        <select class="form-control select-alat" data-prefix="edit"
                                style="border-radius: 8px; height: 46px;" disabled>
                            <option value="">-- Pilih Sub Kategori Dahulu --</option>
                        </select>
                        <small class="text-muted">Pilih ulang Alat hanya kalau mau memperbarui Lokasi. Kalau tidak diubah, data lama tetap dipakai.</small>
                    </div>

                    {{-- DIV INFO ALAT --}}
                    <div class="panel-info-alat panel-info-alat-edit">
                        <div class="panel-info-alat-empty">
                            <i class="fas fa-info-circle mr-1"></i> Pilih alat untuk melihat informasi lengkapnya
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- 4. JENIS LOGBOOK (BARU: otomatis, readonly. Nilai lama dipertahankan
                         kecuali Kategori benar-benar diganti) --}}
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">4. Jenis Logbook <span class="text-danger">*</span></label>
                        <input type="text" name="jenis_logbook" id="edit_jenis_logbook" class="form-control input-jenis-logbook field-otomatis" data-prefix="edit"
                               style="border-radius: 8px; height: 46px;" readonly required>
                        <small class="text-muted">Otomatis terisi ulang hanya kalau Kategori diganti.</small>
                    </div>

                    <input type="hidden" name="jenis_alat" id="edit_jenis_alat" class="input-jenis-alat-hidden" data-prefix="edit">

                    {{-- BARU: Lokasi otomatis dari data Alat, readonly. Nilai lama dipertahankan
                         kecuali Alat dipilih ulang --}}
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">Lokasi <span class="text-danger">*</span></label>
                        <input type="text" name="lokasi_tempat" id="edit_lokasi_tempat" class="form-control input-lokasi-tempat field-otomatis" data-prefix="edit"
                               style="border-radius: 8px; height: 46px;" readonly required>
                        <small class="text-muted">Otomatis terisi ulang hanya kalau Alat dipilih ulang.</small>
                    </div>

                    {{-- 5. PERIODE (BARU: pilih Bulan + Tahun, bukan ketik manual) --}}
                    <div class="form-group">
                        <label class="font-weight-semibold mb-2">5. Periode <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-7">
                                <select class="form-control select-periode-bulan" data-prefix="edit"
                                        style="border-radius: 8px; height: 46px;" required>
                                    <option value="">-- Pilih Bulan --</option>
                                    @foreach($namaBulanIndo as $nb)
                                        <option value="{{ $nb }}">{{ $nb }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-5">
                                <select class="form-control select-periode-tahun" data-prefix="edit"
                                        style="border-radius: 8px; height: 46px;" required>
                                    <option value="">-- Tahun --</option>
                                    @foreach($daftarTahunPeriode as $thn)
                                        <option value="{{ $thn }}">{{ $thn }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="periode_tersedia" id="edit_periode_tersedia" class="input-periode-tersedia" data-prefix="edit">
                        <small class="text-muted">Kalau data lama formatnya tidak standar, dropdown ini akan kosong — tinggal pilih ulang Bulan & Tahun-nya.</small>
                    </div>
                </div>
                <div class="modal-footer border-0" style="background: #f8f9fa; border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-outline-secondary" style="border-radius: 8px;" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-white" style="border-radius: 8px; background: #003366;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL DELETE ===== --}}
<div class="modal fade" id="modalDeleteLogbook" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-danger text-white border-0" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i>Hapus Logbook</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash-alt text-danger mb-3 d-block" style="font-size: 3rem;"></i>
                <h5 class="font-weight-bold">Hapus Data Logbook?</h5>
                <p class="text-muted">Data berikut akan dihapus permanen:</p>
                <div class="alert alert-light border mt-3">
                    <strong id="delete_nama_logbook"></strong>
                </div>
                <small class="text-danger">Tindakan ini tidak dapat dibatalkan.</small>
            </div>
            <div class="modal-footer border-0" style="background: #f8f9fa; border-radius: 0 0 12px 12px;">
                <button type="button" class="btn btn-outline-secondary px-4" style="border-radius: 8px;" data-dismiss="modal">Batal</button>
                <form method="POST" id="formDeleteLogbook">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4 font-weight-bold" style="border-radius: 8px;">
                        <i class="fas fa-trash-alt mr-1"></i> Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Ajukan ke Kepala Kelompok -->
<div class="modal fade" id="modalSubmitLogbook" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Ajukan Logbook
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form id="formSubmitLogbook" method="POST">
                @csrf

                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-file-upload fa-3x text-primary"></i>
                    </div>

                    <p class="text-center mb-1">
                        Apakah Anda yakin ingin mengajukan logbook:
                    </p>

                    <h6 id="namaLogbookSubmit"
                        class="text-center font-weight-bold text-primary">
                    </h6>

                    <p class="text-center text-muted mt-3 mb-0">
                        Logbook akan dikirim ke <strong>Kepala Kelompok (kapok)</strong>
                        untuk proses approval.
                    </p>
                </div>

                <div class="modal-footer justify-content-center">
                    <button type="button"
                            class="btn btn-light border"
                            data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-1"></i>
                        Ya, Ajukan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@include('logbook._modals_approval')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function pemicuEdit(element) {
        document.getElementById('edit_kategori_id').value    = element.getAttribute('data-kategori_id');
        document.getElementById('edit_jenis_logbook').value  = element.getAttribute('data-jenis_logbook');
        document.getElementById('edit_jenis_alat').value     = element.getAttribute('data-jenis_alat');
        document.getElementById('edit_lokasi_tempat').value  = element.getAttribute('data-lokasi_tempat');

        // Set hidden periode_tersedia ke nilai lama dulu (fallback aman kalau parsing gagal).
        var periodeLama = (element.getAttribute('data-periode_tersedia') || '').trim();
        document.getElementById('edit_periode_tersedia').value = periodeLama;

        // Coba parse format "NamaBulan Tahun" (misal "Januari 2026") dari data lama,
        // supaya dropdown Bulan & Tahun di form Edit ikut ke-preselect. Kalau data lama
        // formatnya tidak cocok (misal hasil ketik manual sebelum fitur ini ada),
        // dropdown dibiarkan kosong dan teknisi tinggal pilih ulang Bulan & Tahunnya
        // (tidak masalah, karena hidden field tetap pegang nilai lama sampai diganti).
        var potonganPeriode = periodeLama.split(' ');
        var tahunLama = potonganPeriode.length > 1 ? potonganPeriode.pop() : '';
        var bulanLama = potonganPeriode.join(' ');

        var $editBulanPeriode = $('.select-periode-bulan[data-prefix="edit"]');
        var $editTahunPeriode = $('.select-periode-tahun[data-prefix="edit"]');

        var bulanCocok = $editBulanPeriode.find('option').filter(function () {
            return $(this).val() === bulanLama;
        }).length > 0;
        var tahunCocok = $editTahunPeriode.find('option').filter(function () {
            return $(this).val() === tahunLama;
        }).length > 0;

        $editBulanPeriode.val(bulanCocok ? bulanLama : '');
        $editTahunPeriode.val(tahunCocok ? tahunLama : '');

        document.getElementById('formEditLogbook').setAttribute('action', '/logbook/update/' + element.getAttribute('data-id'));

        // Trigger ulang dropdown Sub Kategori berdasarkan kategori_id yang sudah tersimpan,
        // supaya tampilan tetap konsisten saat modal Edit dibuka. Dikirim dengan flag
        // { init: true } supaya nilai LAMA (jenis_logbook, jenis_alat, lokasi_tempat)
        // TIDAK ikut ke-reset/overwrite otomatis. Field-field tersebut hanya berubah
        // kalau teknisi benar-benar memilih ulang Kategori / Sub Kategori / Alat di form edit.
        $('.select-kategori[data-prefix="edit"]').trigger('change', { init: true });
    }

    function setModalId(formId, action) {
        document.getElementById(formId).setAttribute('action', action);
    }

    $(document).on('click', '.btn-delete', function () {
        $('#delete_nama_logbook').text($(this).data('nama'));
        $('#formDeleteLogbook').attr('action', '/logbook/delete/' + $(this).data('id'));
    });

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil', text: '{{ session('success') }}', timer: 3000, showConfirmButton: false });
    @endif
    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Gagal', text: '{{ session('error') }}', timer: 3000, showConfirmButton: false });
    @endif
    $('#modalSubmitLogbook').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);

        let id = button.data('id');
        let jenis = button.data('jenis');

        $('#namaLogbookSubmit').text(jenis);

        $('#formSubmitLogbook').attr(
            'action',
            '/logbook/submit/' + id
        );
    });

    /* =========================================================
       DROPDOWN BERTINGKAT Kategori -> Sub Kategori -> Alat
       untuk modal Tambah & Edit Logbook.
       - Kategori & Sub Kategori: nilai 'kategori_id' Sub Kategori
         dikirim sebagai 'jenis_alat' otomatis (nama sub kategori).
       - Alat: HANYA preview info, tidak dikirim ke server sama sekali.
       - BARU: Jenis Logbook otomatis terisi "Logbook Peralatan [Kategori]"
         saat Kategori dipilih. Lokasi otomatis terisi dari data Alat
         yang dipilih (field 'lokasi' pada master Data Alat).
       - Kedua auto-fill di atas HANYA jalan untuk aksi NYATA dari user.
         Saat modal Edit baru dibuka (trigger { init: true }), nilai LAMA
         tetap dipertahankan; baru ter-update kalau teknisi benar-benar
         mengganti Kategori/Sub Kategori/Alat di form edit.
       Dipakai untuk DUA form sekaligus (Tambah & Edit) via atribut
       data-prefix, supaya kode tidak duplikat.
       ========================================================= */
    document.addEventListener('DOMContentLoaded', function () {

        var dataSubKategori = JSON.parse(document.getElementById('dataSubKategoriLogbook').textContent);
        var dataAlat         = JSON.parse(document.getElementById('dataAlatLogbook').textContent);

        function resetSelect($select, placeholderText, disabled) {
            $select.html('<option value="">' + placeholderText + '</option>');
            $select.prop('disabled', disabled);
        }

        function renderInfoAlat(prefix, alatData) {
            var $panel = $('.panel-info-alat-' + prefix);

            if (!alatData) {
                $panel.html('<div class="panel-info-alat-empty"><i class="fas fa-info-circle mr-1"></i> Pilih alat untuk melihat informasi lengkapnya</div>');
                return;
            }

            var rows = [
                ['Nama Alat', alatData.nama],
                ['Merk / Type', alatData.merk_type],
                ['Nomor Seri', alatData.nomor_seri],
                ['Tahun Pengadaan', alatData.tahun_pengadaan],
                ['Rentang Ukur', alatData.rentang_ukur],
                ['Resolusi', alatData.resolusi],
                ['Akurasi', alatData.akurasi],
                ['Lokasi', alatData.lokasi],
                ['Kondisi', alatData.kondisi],
                ['Status', alatData.status],
            ];

            var html = '<div class="info-alat-title"><i class="fas fa-microchip"></i> Informasi Alat</div>';

            rows.forEach(function (row) {
                var label = row[0];
                var value = row[1];
                if (value === null || value === undefined || value === '') {
                    value = '-';
                }
                html += '<div class="info-alat-row">';
                html += '<div class="info-alat-label">' + label + '</div>';
                html += '<div class="info-alat-value">' + $('<div>').text(value).html() + '</div>';
                html += '</div>';
            });

            $panel.html(html);
        }

        $('.select-kategori').on('change', function (e, extra) {

            // isInit = true HANYA saat modal Edit baru dibuka (lihat pemicuEdit()).
            // Dalam kondisi ini, field turunan (jenis_logbook, jenis_alat, lokasi_tempat)
            // tidak boleh ikut di-reset/overwrite — biarkan nilai lama tetap tampil.
            var isInit = !!(extra && extra.init);

            var prefix     = $(this).data('prefix');
            var kategoriId = $(this).val();
            var namaKategori = $(this).find('option:selected').text().trim();

            var $selectSub  = $('.select-sub-kategori[data-prefix="' + prefix + '"]');
            var $selectAlat = $('.select-alat[data-prefix="' + prefix + '"]');
            var $hiddenJenisAlat = $('.input-jenis-alat-hidden[data-prefix="' + prefix + '"]');
            var $inputJenisLogbook = $('.input-jenis-logbook[data-prefix="' + prefix + '"]');
            var $inputLokasi = $('.input-lokasi-tempat[data-prefix="' + prefix + '"]');
            var $btnSubmit  = $(this).closest('form').find('.btn-submit-logbook');

            // Reset tampilan dropdown Alat & panel info (boleh selalu, karena Alat
            // memang tidak bisa "ditebak" otomatis dari data lama).
            resetSelect($selectAlat, '-- Pilih Sub Kategori Dahulu --', true);
            renderInfoAlat(prefix, null);

            if (!isInit) {
                // Aksi nyata user mengganti Kategori -> field turunan ikut reset,
                // supaya tidak ada data basi (jenis_alat/lokasi dari kategori sebelumnya).
                $hiddenJenisAlat.val('');
                $inputLokasi.val('');
                if ($btnSubmit.length) $btnSubmit.prop('disabled', true);
            }

            if (!kategoriId) {
                resetSelect($selectSub, '-- Pilih Kategori Dahulu --', true);
                if (!isInit) $inputJenisLogbook.val('');
                return;
            }

            var filtered = dataSubKategori.filter(function (sk) {
                return String(sk.kategori_id) === String(kategoriId);
            });

            $selectSub.html('<option value="">-- Pilih Sub Kategori --</option>');

            if (filtered.length === 0) {
                $selectSub.append('<option value="" disabled>Tidak ada sub kategori untuk kategori ini</option>');
            } else {
                filtered.forEach(function (sk) {
                    $selectSub.append($('<option>', { value: sk.id, text: sk.nama }));
                });
            }

            $selectSub.prop('disabled', false);

            // BARU: auto-fill Jenis Logbook = "Logbook Peralatan [Nama Kategori]".
            // Hanya jalan untuk aksi nyata user (bukan saat modal Edit baru dibuka).
            if (!isInit) {
                $inputJenisLogbook.val('Logbook Peralatan ' + namaKategori);
            }
        });

        $('.select-sub-kategori').on('change', function () {
            // Handler ini selalu hasil aksi nyata user (tidak pernah di-trigger
            // programatically saat init), jadi tidak perlu flag isInit.

            var prefix       = $(this).data('prefix');
            var subKategoriId = $(this).val();
            var namaSubKategori = $(this).find('option:selected').text();

            var $selectAlat = $('.select-alat[data-prefix="' + prefix + '"]');
            var $hiddenJenisAlat = $('.input-jenis-alat-hidden[data-prefix="' + prefix + '"]');
            var $inputLokasi = $('.input-lokasi-tempat[data-prefix="' + prefix + '"]');
            var $btnSubmit  = $(this).closest('form').find('.btn-submit-logbook');

            renderInfoAlat(prefix, null);

            // Sub Kategori berubah -> pilihan Alat (sumber Lokasi) ikut berubah,
            // jadi Lokasi yang sudah terisi sebelumnya direset, menunggu Alat baru dipilih.
            $inputLokasi.val('');

            if (!subKategoriId) {
                resetSelect($selectAlat, '-- Pilih Sub Kategori Dahulu --', true);
                $hiddenJenisAlat.val('');
                if ($btnSubmit.length) $btnSubmit.prop('disabled', true);
                return;
            }

            // jenis_alat otomatis terisi dari nama Sub Kategori
            $hiddenJenisAlat.val(namaSubKategori);

            var filteredAlat = dataAlat.filter(function (a) {
                return String(a.sub_kategori_id) === String(subKategoriId);
            });

            $selectAlat.html('<option value="">-- Pilih Alat --</option>');

            filteredAlat.forEach(function (a) {
                $selectAlat.append($('<option>', { value: a.id, text: a.nama }));
            });

            $selectAlat.prop('disabled', filteredAlat.length === 0);

            // Boleh disubmit begitu Sub Kategori sudah dipilih (Alat tetap wajib dipilih dulu
            // secara UX, tapi tidak memblokir submit form karena tidak dikirim ke server).
            if ($btnSubmit.length) $btnSubmit.prop('disabled', filteredAlat.length === 0);
        });

        $('.select-alat').on('change', function () {
            // Handler ini selalu hasil aksi nyata user.

            var prefix = $(this).data('prefix');
            var alatId = $(this).val();
            var $inputLokasi = $('.input-lokasi-tempat[data-prefix="' + prefix + '"]');

            if (!alatId) {
                renderInfoAlat(prefix, null);
                $inputLokasi.val('');
                return;
            }

            var alatData = dataAlat.find(function (a) {
                return String(a.id) === String(alatId);
            });

            renderInfoAlat(prefix, alatData);

            // BARU: auto-fill Lokasi dari data Alat yang dipilih.
            if (alatData) {
                $inputLokasi.val(alatData.lokasi || '');
            }
        });

        /* =========================================================
           BARU: Periode — gabungkan dropdown Bulan + Tahun jadi satu
           nilai "NamaBulan Tahun" (misal "Januari 2026") yang dikirim
           lewat hidden input periode_tersedia. Berlaku untuk form
           Tambah maupun Edit (lewat data-prefix yang sama).
           ========================================================= */
        $('.select-periode-bulan, .select-periode-tahun').on('change', function () {
            var prefix = $(this).data('prefix');
            var bulan = $('.select-periode-bulan[data-prefix="' + prefix + '"]').val();
            var tahun = $('.select-periode-tahun[data-prefix="' + prefix + '"]').val();
            var $hiddenPeriode = $('.input-periode-tersedia[data-prefix="' + prefix + '"]');

            $hiddenPeriode.val((bulan && tahun) ? (bulan + ' ' + tahun) : '');
        });

    });
</script>
@endpush