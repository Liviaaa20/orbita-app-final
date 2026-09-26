@extends('layouts.master')

@section('content')
@php
    $userRole      = strtolower(Auth::user()->role->nama_role ?? '');
    $isTeknisi     = $userRole === 'teknisi';
    $isKepalaKelompok = in_array($userRole, ['kepala kelompok', 'kepala_kelompok', 'kapok']);
    $isKoordinator = $userRole === 'koordinator';
@endphp

<div class="container-fluid" id="kontenLogbook">

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- BREADCRUMB & JUDUL                                     --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="d-flex align-items-center justify-content-between mb-3 no-print">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-1">
                    <li class="breadcrumb-item">
                        <a href="{{ route('logbook.index') }}" class="text-muted">
                            <i class="fas fa-book mr-1"></i>Logbook
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-dark font-weight-bold">
                        {{ $logbook->jenis_logbook }}
                    </li>
                </ol>
            </nav>
            <h4 class="font-weight-bold text-dark mb-0">
                {{ strtoupper($logbook->jenis_logbook) }}
            </h4>
            <small class="text-muted">
                {{ $logbook->lokasi_tempat }} &mdash; {{ $bulanCarbon->isoFormat('MMMM YYYY') }}
                @if($logbook->subKategori)
                    &mdash; <span class="badge badge-info">{{ $logbook->subKategori->nama_sub_kategori }}</span>
                @endif
            </small>
        </div>
        <div class="d-flex" style="gap:8px;">
            <a href="{{ route('logbook.index') }}" class="btn btn-light border shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
            @if($logbook->bisaDownload())
                <a href="{{ route('download-pdf', $logbook->id) }}"
                   class="btn btn-danger shadow-sm font-weight-bold"
                   target="_blank">
                    <i class="fas fa-file-pdf mr-1"></i> Download PDF
                </a>
            @endif
            <button onclick="window.print()" class="btn btn-light border shadow-sm">
                <i class="fas fa-print mr-1"></i> Cetak
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- PANEL STATUS APPROVAL                                  --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="card shadow-sm border-0 rounded-lg mb-4 no-print">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:12px;">

                {{-- Status Badge --}}
                <div class="d-flex align-items-center" style="gap:10px;">
                    <span class="font-weight-bold text-muted small text-uppercase">Status Logbook:</span>
                    <span class="badge badge-{{ $logbook->getBadgeStatus() }} px-3 py-2" style="font-size:0.85rem;">
                        {{ $logbook->getLabelStatus() }}
                    </span>
                </div>

                {{-- Timeline Progress --}}
                <div class="d-flex align-items-center flex-wrap" style="gap:6px;">
                    {{-- Step 1: Draft / Dibuat --}}
                    <div class="d-flex align-items-center" style="gap:4px;">
                        <span class="badge px-2 py-1
                            {{ in_array($logbook->status, ['draft','pending_kapok','approved_kapok','rejected_kapok','pending_koordinator','approved_final','rejected_koordinator']) ? 'badge-success' : 'badge-light border' }}">
                            <i class="fas fa-edit mr-1"></i>Dibuat
                        </span>
                        <i class="fas fa-chevron-right text-muted" style="font-size:0.65rem;"></i>
                    </div>

                    {{-- Step 2: Diajukan ke Kepala Kelompok --}}
                    <div class="d-flex align-items-center" style="gap:4px;">
                        <span class="badge px-2 py-1
                            @if($logbook->status === 'rejected_kapok') badge-danger
                            @elseif(in_array($logbook->status, ['pending_kapok','approved_kapok','pending_koordinator','approved_final','rejected_koordinator'])) badge-success
                            @else badge-light border @endif">
                            <i class="fas fa-paper-plane mr-1"></i>Kepala Kelompok
                        </span>
                        <i class="fas fa-chevron-right text-muted" style="font-size:0.65rem;"></i>
                    </div>

                    {{-- Step 3: Koordinator --}}
                    <div class="d-flex align-items-center" style="gap:4px;">
                        <span class="badge px-2 py-1
                            @if($logbook->status === 'rejected_koordinator') badge-danger
                            @elseif(in_array($logbook->status, ['pending_koordinator','approved_final'])) badge-success
                            @else badge-light border @endif">
                            <i class="fas fa-user-tie mr-1"></i>Koordinator
                        </span>
                        <i class="fas fa-chevron-right text-muted" style="font-size:0.65rem;"></i>
                    </div>

                    {{-- Step 4: Final --}}
                    <span class="badge px-2 py-1 {{ $logbook->status === 'approved_final' ? 'badge-success' : 'badge-light border' }}">
                        <i class="fas fa-check-double mr-1"></i>Final
                    </span>
                </div>

                {{-- Tombol Aksi Approval (sesuai role & status) --}}
                <div class="d-flex align-items-center flex-wrap" style="gap:6px;">

                    {{-- TEKNISI: Submit --}}
                    @if($isTeknisi && $logbook->bisaSubmit())
                        <button type="button"
                                class="btn btn-primary btn-sm font-weight-bold shadow-sm px-3"
                                data-toggle="modal"
                                data-target="#modalSubmitKapok">
                            <i class="fas fa-paper-plane mr-1"></i> Ajukan ke Kepala Kelompok
                        </button>
                    @endif

                    {{-- KAPOK: Approve / Reject --}}
                    @if($isKapok && $logbook->status === 'pending_kapok')
                        <button type="button"
                                class="btn btn-success btn-sm font-weight-bold shadow-sm px-3"
                                data-toggle="modal" data-target="#modalApproveKapok"
                                onclick="setModalId('modalApproveKapokForm', '{{ route('logbook.approve-kapok', $logbook->id) }}')">
                            <i class="fas fa-check mr-1"></i> Setujui
                        </button>
                        <button type="button"
                                class="btn btn-danger btn-sm font-weight-bold shadow-sm px-3"
                                data-toggle="modal" data-target="#modalRejectKapok"
                                onclick="setModalId('modalRejectKapokForm', '{{ route('logbook.reject-kapok', $logbook->id) }}')">
                            <i class="fas fa-times mr-1"></i> Tolak
                        </button>
                    @endif

                    {{-- KOORDINATOR: Approve / Reject --}}
                    @if($isKoordinator && $logbook->status === 'pending_koordinator')
                        <button type="button"
                                class="btn btn-success btn-sm font-weight-bold shadow-sm px-3"
                                data-toggle="modal" data-target="#modalApproveKoordinator"
                                onclick="setModalId('modalApproveKoordinatorForm', '{{ route('logbook.approve-koordinator', $logbook->id) }}')">
                            <i class="fas fa-check-double mr-1"></i> Setujui Final
                        </button>
                        <button type="button"
                                class="btn btn-danger btn-sm font-weight-bold shadow-sm px-3"
                                data-toggle="modal" data-target="#modalRejectKoordinator"
                                onclick="setModalId('modalRejectKoordinatorForm', '{{ route('logbook.reject-koordinator', $logbook->id) }}')">
                            <i class="fas fa-times mr-1"></i> Tolak
                        </button>
                    @endif

                </div>
            </div>

            {{-- Catatan Kapok / Koordinator (jika ada) --}}
            @if($logbook->catatan_kapok || $logbook->catatan_koordinator)
                <hr class="my-3">
                <div class="row">
                    @if($logbook->catatan_kapok)
                        <div class="col-md-6">
                            <div class="alert
                                {{ in_array($logbook->status, ['rejected_kapok']) ? 'alert-danger' : 'alert-info' }}
                                py-2 px-3 mb-0 small">
                                <strong>
                                    <i class="fas fa-comment-alt mr-1"></i>
                                    Catatan Kepala Kelompok
                                    ({{ $logbook->approvedKapokOleh->name ?? '-' }},
                                     {{ $logbook->approved_kapok_at ? $logbook->approved_kapok_at->isoFormat('D MMM YYYY HH:mm') : '-' }}):
                                </strong>
                                <br>{{ $logbook->catatan_kapok }}
                            </div>
                        </div>
                    @endif
                    @if($logbook->catatan_koordinator)
                        <div class="col-md-6">
                            <div class="alert
                                {{ in_array($logbook->status, ['rejected_koordinator']) ? 'alert-danger' : 'alert-success' }}
                                py-2 px-3 mb-0 small">
                                <strong>
                                    <i class="fas fa-comment-alt mr-1"></i>
                                    Catatan Koordinator
                                    ({{ $logbook->approvedKoordinatorOleh->name ?? '-' }},
                                     {{ $logbook->approved_koordinator_at ? $logbook->approved_koordinator_at->isoFormat('D MMM YYYY HH:mm') : '-' }}):
                                </strong>
                                <br>{{ $logbook->catatan_koordinator }}
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- INFO CARDS                                             --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="row mb-3 no-print">
        <div class="col-md-3">
            <div class="info-box shadow-sm border-0 mb-0">
                <span class="info-box-icon bg-info"><i class="fas fa-calendar-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted small">Periode</span>
                    <span class="info-box-number" style="font-size:1rem;">
                        {{ $bulanCarbon->isoFormat('MMMM YYYY') }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box shadow-sm border-0 mb-0">
                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted small">Hari Terisi</span>
                    <span class="info-box-number" style="font-size:1rem;">
                        {{ $jumlahTerisi }} / {{ $jumlahHari }} hari
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box shadow-sm border-0 mb-0">
                <span class="info-box-icon bg-warning"><i class="fas fa-microchip"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted small">Jumlah Alat</span>
                    <span class="info-box-number" style="font-size:1rem;">
                        {{ $alats->count() }} alat
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box shadow-sm border-0 mb-0">
                <span class="info-box-icon bg-secondary"><i class="fas fa-map-marker-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted small">Lokasi</span>
                    <span class="info-box-number" style="font-size:0.85rem; line-height:1.3;">
                        {{ $logbook->lokasi_tempat }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- FILTER BULAN                                           --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="card shadow-sm border-0 rounded-lg mb-3 no-print">
        <div class="card-body p-3">
            <form action="{{ route('logbook.show', $logbook->id) }}" method="GET" class="d-flex align-items-end">
                <div class="mr-3">
                    <label class="mb-1 small font-weight-bold text-muted text-uppercase">Pilih Bulan</label>
                    <select name="bulan" class="form-control shadow-none border custom-select"
                            style="min-width:200px;" onchange="this.form.submit()">
                        @foreach($bulanList as $bln)
                            <option value="{{ $bln['value'] }}" {{ $bulanParam == $bln['value'] ? 'selected' : '' }}>
                                {{ $bln['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-secondary shadow-sm">
                    <i class="fas fa-filter mr-1"></i> Tampilkan
                </button>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- HEADER CETAK (hanya muncul saat print/PDF)            --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="print-only mb-3">
        <table width="100%" style="border:none;">
            <tr>
                <td style="width:15%; text-align:center; vertical-align:middle;">
                    {{-- Logo BMKG (sesuaikan path jika ada) --}}
                    <img src="{{ public_path('assets/dist/img/logo.png') }}" height="70" alt="Logo">
                </td>
                <td style="text-align:center; vertical-align:middle;">
                    <div style="font-size:14pt; font-weight:bold; text-transform:uppercase; letter-spacing:1px;">
                        {{ $logbook->jenis_logbook }}
                    </div>
                    <div style="font-size:10pt; margin-top:4px;">
                        {{ strtoupper($logbook->jenis_alat) }}
                    </div>
                    <div style="font-size:10pt;">
                        {{ strtoupper($logbook->lokasi_tempat) }}
                    </div>
                    <div style="font-size:10pt;">
                        PERIODE: {{ strtoupper($logbook->periode_tersedia) }}
                    </div>
                    <div style="font-size:10pt;">
                        BULAN: {{ strtoupper($bulanCarbon->isoFormat('MMMM YYYY')) }}
                    </div>
                </td>
                <td style="width:15%;"></td>
            </tr>
        </table>
        <hr style="border-top: 2px solid #000; margin-top:8px;">
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- TABEL LOGBOOK HARIAN                                   --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="card shadow-sm border-0 rounded-lg overflow-hidden mb-4 logbook-card-screen">
        <div class="card-header bg-white border-bottom p-3 no-print">
            <h5 class="font-weight-bold m-0 text-dark">
                {{ strtoupper($logbook->jenis_logbook) }}
            </h5>
            <small class="text-muted">
                {{ strtoupper($logbook->lokasi_tempat) }} &mdash;
                BULAN {{ strtoupper($bulanCarbon->isoFormat('MMMM YYYY')) }}
            </small>
        </div>

        @if($alats->isEmpty())
            <div class="card-body text-center py-5 text-muted no-print">
                <i class="fas fa-exclamation-triangle mb-3 d-block text-warning" style="font-size:2.5rem;"></i>
                <p class="mb-1">Belum ada alat yang terdaftar di sub kategori ini.</p>
                <small>
                    Tambahkan alat dengan sub kategori
                    <strong>{{ $logbook->subKategori->nama_sub_kategori ?? '-' }}</strong>
                    melalui menu <a href="{{ route('data-alat.index') }}">Data Alat</a>.
                </small>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center mb-0 logbook-table"
                       style="font-size:0.8rem; min-width:900px;">

                    {{-- HEADER --}}
                    <thead class="bg-light text-dark font-weight-bold text-uppercase" style="font-size:0.75rem;">
                        <tr>
                            <th class="align-middle" style="width:40px;">TGL</th>
                            @foreach($alats as $alat)
                                <th class="align-middle px-2">
                                    {{ strtoupper($alat->nama_alat) }}
                                    <div class="text-muted font-weight-normal" style="font-size:0.65rem;">
                                        S/N: {{ $alat->nomor_seri }}
                                    </div>
                                </th>
                            @endforeach
                            <th class="align-middle text-left px-3" style="min-width:180px;">KETERANGAN</th>
                            <th class="align-middle" style="min-width:120px;">TEKNISI</th>
                        </tr>
                    </thead>

                    {{-- BODY --}}
                    <tbody>
                        @for($hari = 1; $hari <= $jumlahHari; $hari++)
                            @php
                                $dataHari = $dataHarian[$hari] ?? [];
                                $adaData  = !empty($dataHari);
                            @endphp
                            <tr class="{{ !$adaData ? 'table-light' : '' }}">
                                <td class="font-weight-bold align-middle text-dark">{{ $hari }}</td>

                                @foreach($alats as $alat)
                                    @php
                                        $kondisi = $dataHari[$alat->id] ?? null;
                                    @endphp
                                    <td class="align-middle">
                                        @if($kondisi)
                                            @php
                                                $badge        = App\Models\Logbook::getBadgeKondisi($kondisi);
                                                $labelKondisi = App\Models\Logbook::getLabelKondisi($kondisi);
                                            @endphp
                                            <span class="badge badge-{{ $badge }} px-2 py-1">
                                                {{ $labelKondisi }}
                                            </span>
                                        @else
                                            <span class="text-muted" style="font-size:0.75rem;">#N/A</span>
                                        @endif
                                    </td>
                                @endforeach

                                <td class="text-left px-3 align-middle" style="font-size:0.78rem;">
                                    {{ $keteranganHarian[$hari] ?? '' }}
                                </td>

                                <td class="align-middle">
                                    @if(!empty($teknisiHarian[$hari]))
                                        <span class="badge badge-light border text-dark px-2">
                                            {{ $teknisiHarian[$hari] }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            {{-- LEGENDA --}}
            <div class="card-footer bg-white border-top p-3 d-flex align-items-center flex-wrap no-print">
                <small class="text-muted font-weight-bold mr-3 text-uppercase">Keterangan Status:</small>
                <span class="badge badge-success px-2 py-1 mr-2">BAIK</span>
                <span class="badge badge-warning px-2 py-1 mr-2">RUSAK RINGAN</span>
                <span class="badge badge-danger px-2 py-1 mr-2">RUSAK BERAT</span>
                <span class="badge badge-secondary px-2 py-1 mr-2">OFF</span>
                <span class="text-muted ml-2" style="font-size:0.8rem;">
                    <i class="fas fa-info-circle mr-1"></i>#N/A = Belum ada data pengecekan
                </span>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- SECTION PARAF / TANDA TANGAN                          --}}
    {{-- Ditampilkan di layar (info) dan di print (dengan TTD) --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div class="card shadow-sm border-0 rounded-lg mb-4" id="sectionParaf">
        <div class="card-header bg-white border-bottom p-3 no-print">
            <h6 class="font-weight-bold m-0 text-dark">
                <i class="fas fa-signature mr-2 text-muted"></i>
                Tanda Tangan & Persetujuan
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row text-center">

                {{-- Kolom Dibuat Oleh / Teknisi --}}
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <p class="font-weight-bold text-uppercase small text-muted mb-1">Dibuat Oleh</p>
                        <p class="small text-muted mb-1">Teknisi</p>

                        {{-- Spacer TTD --}}
                        <div style="height:70px;" class="d-flex align-items-center justify-content-center">
                            <span class="text-muted small">&nbsp;</span>
                        </div>

                        <div class="border-top pt-2">
                            <p class="font-weight-bold mb-0 small text-dark">{{ Auth::user()->name }}</p>
                            <p class="text-muted mb-0" style="font-size:0.75rem;">NIP. {{ Auth::user()->nip ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kepala Kelompok --}}
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100
                        @if($logbook->status === 'rejected_kapok') border-danger
                        @elseif(in_array($logbook->status, ['approved_kapok','pending_koordinator','approved_final'])) border-success
                        @endif">
                        <p class="font-weight-bold text-uppercase small text-muted mb-1">Kepala Kelompok</p>

                        @if(in_array($logbook->status, ['approved_kapok', 'pending_koordinator', 'approved_final']))
                            <p class="small text-muted mb-1">
                                {{ $logbook->approved_kapok_at ? $logbook->approved_kapok_at->isoFormat('D MMM YYYY') : '-' }}
                            </p>
                            {{-- Paraf Kapok --}}
                            <div style="height:70px;" class="d-flex align-items-center justify-content-center">
                                <img src="{{ public_path('assets/dist/img/TTD/parafKapok.png') }}"
                                     onerror="this.style.display='none'"
                                     height="60" alt="Paraf Kapok"
                                     class="paraf-img">
                            </div>
                            <div class="border-top pt-2">
                                <p class="font-weight-bold mb-0 small text-dark text-success">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    {{ $logbook->approvedKapokOleh->name ?? '-' }}
                                </p>
                                <p class="text-muted mb-0" style="font-size:0.75rem;">
                                    NIP. {{ $logbook->approvedKapokOleh->nip ?? '-' }}
                                </p>
                            </div>
                        @elseif($logbook->status === 'rejected_kapok')
                            <div style="height:70px;" class="d-flex align-items-center justify-content-center">
                                <span class="badge badge-danger px-3 py-2">
                                    <i class="fas fa-times-circle mr-1"></i>Ditolak
                                </span>
                            </div>
                            <div class="border-top pt-2">
                                <p class="text-danger mb-0 small font-weight-bold">
                                    {{ $logbook->approvedKapokOleh->name ?? '-' }}
                                </p>
                                @if($logbook->catatan_kapok)
                                    <p class="text-muted mb-0" style="font-size:0.7rem;">
                                        \"{{ $logbook->catatan_kapok }}\"
                                    </p>
                                @endif
                            </div>
                        @else
                            {{-- Belum diproses --}}
                            <div style="height:70px;" class="d-flex align-items-center justify-content-center">
                                <span class="text-muted small">
                                    @if($logbook->status === 'pending_kapok')
                                        <span class="badge badge-warning">Menunggu Persetujuan</span>
                                    @else
                                        <i class="fas fa-clock text-muted"></i> Belum diproses
                                    @endif
                                </span>
                            </div>
                            <div class="border-top pt-2">
                                <p class="text-muted mb-0 small">( _________________ )</p>
                                <p class="text-muted mb-0" style="font-size:0.75rem;">NIP.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Kolom Koordinator --}}
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100
                        @if($logbook->status === 'rejected_koordinator') border-danger
                        @elseif($logbook->status === 'approved_final') border-success
                        @endif">
                        <p class="font-weight-bold text-uppercase small text-muted mb-1">Koordinator</p>

                        @if($logbook->status === 'approved_final')
                            <p class="small text-muted mb-1">
                                {{ $logbook->approved_koordinator_at ? $logbook->approved_koordinator_at->isoFormat('D MMM YYYY') : '-' }}
                            </p>
                            {{-- Paraf Koordinator --}}
                            <div style="height:70px;" class="d-flex align-items-center justify-content-center">
                                <img src="{{ public_path('assets/dist/img/TTD/parafKoordinator.png') }}"
                                     onerror="this.style.display='none'"
                                     height="60" alt="Paraf Koordinator"
                                     class="paraf-img">
                            </div>
                            <div class="border-top pt-2">
                                <p class="font-weight-bold mb-0 small text-dark text-success">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    {{ $logbook->approvedKoordinatorOleh->name ?? '-' }}
                                </p>
                                <p class="text-muted mb-0" style="font-size:0.75rem;">
                                    NIP. {{ $logbook->approvedKoordinatorOleh->nip ?? '-' }}
                                </p>
                            </div>
                        @elseif($logbook->status === 'rejected_koordinator')
                            <div style="height:70px;" class="d-flex align-items-center justify-content-center">
                                <span class="badge badge-danger px-3 py-2">
                                    <i class="fas fa-times-circle mr-1"></i>Ditolak
                                </span>
                            </div>
                            <div class="border-top pt-2">
                                <p class="text-danger mb-0 small font-weight-bold">
                                    {{ $logbook->approvedKoordinatorOleh->name ?? '-' }}
                                </p>
                                @if($logbook->catatan_koordinator)
                                    <p class="text-muted mb-0" style="font-size:0.7rem;">
                                        "{{ $logbook->catatan_koordinator }}"
                                    </p>
                                @endif
                            </div>
                        @else
                            <div style="height:70px;" class="d-flex align-items-center justify-content-center">
                                <span class="text-muted small">
                                    @if($logbook->status === 'pending_koordinator')
                                        <span class="badge badge-warning">Menunggu Persetujuan</span>
                                    @else
                                        <i class="fas fa-clock text-muted"></i> Belum diproses
                                    @endif
                                </span>
                            </div>
                            <div class="border-top pt-2">
                                <p class="text-muted mb-0 small">( _________________ )</p>
                                <p class="text-muted mb-0" style="font-size:0.75rem;">NIP.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>{{-- /row --}}
        </div>
    </div>

</div>{{-- /container-fluid --}}


{{-- ══════════════════════════════════════════════════════════ --}}
{{-- MODAL APPROVE KAPOK                                        --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalApproveKapok" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-check-circle mr-2"></i> Setujui Logbook
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" id="modalApproveKapokForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">Logbook akan diteruskan ke Koordinator setelah disetujui.</p>
                    <div class="form-group">
                        <label class="font-weight-bold small text-uppercase">Catatan (opsional)</label>
                        <textarea name="catatan_kapok" class="form-control shadow-none" rows="3"
                                  placeholder="Catatan persetujuan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success font-weight-bold px-4">
                        <i class="fas fa-check mr-1"></i> Ya, Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- MODAL REJECT KAPOK                                         --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalRejectKapok" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-times-circle mr-2"></i> Tolak Logbook
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" id="modalRejectKapokForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">Teknisi akan diminta merevisi dan mengajukan ulang.</p>
                    <div class="form-group">
                        <label class="font-weight-bold small text-uppercase">
                            Alasan Penolakan <span class="text-danger">*</span>
                        </label>
                        <textarea name="catatan_kapok" class="form-control shadow-none" rows="3"
                                  placeholder="Tuliskan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger font-weight-bold px-4">
                        <i class="fas fa-times mr-1"></i> Ya, Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- MODAL APPROVE KOORDINATOR                                  --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalApproveKoordinator" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-check-double mr-2"></i> Setujui Final
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" id="modalApproveKoordinatorForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Logbook akan berstatus <strong>Disetujui Final</strong> dan PDF dapat diunduh.
                    </p>
                    <div class="form-group">
                        <label class="font-weight-bold small text-uppercase">Catatan (opsional)</label>
                        <textarea name="catatan_koordinator" class="form-control shadow-none" rows="3"
                                  placeholder="Catatan persetujuan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success font-weight-bold px-4">
                        <i class="fas fa-check-double mr-1"></i> Ya, Setujui Final
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- MODAL REJECT KOORDINATOR                                   --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalRejectKoordinator" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-times-circle mr-2"></i> Tolak (Koordinator)
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" id="modalRejectKoordinatorForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">Logbook akan dikembalikan untuk direvisi.</p>
                    <div class="form-group">
                        <label class="font-weight-bold small text-uppercase">
                            Alasan Penolakan <span class="text-danger">*</span>
                        </label>
                        <textarea name="catatan_koordinator" class="form-control shadow-none" rows="3"
                                  placeholder="Tuliskan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger font-weight-bold px-4">
                        <i class="fas fa-times mr-1"></i> Ya, Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalSubmitKapok" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
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

            <form action="{{ route('logbook.submit', $logbook->id) }}" method="POST">
                @csrf

                <div class="modal-body">

                    <div class="text-center mb-3">
                        <i class="fas fa-file-upload fa-3x text-primary"></i>
                    </div>

                    <h5 class="text-center font-weight-bold">
                        Ajukan logbook ke Kepala Kelompok?
                    </h5>

                    <p class="text-center text-muted mb-0">
                        Logbook
                        <strong>{{ $logbook->jenis_logbook }}</strong>
                        akan dikirim ke Kepala Kelompok (Kapok) untuk proses
                        pemeriksaan dan persetujuan.
                        pemeriksaan dan persetujuan.
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
@endsection

@push('styles')
<style>
    /* ── Elemen yang disembunyikan saat print ── */
    .no-print { display: block; }
    .print-only { display: none; }

    @media print {
        /* Sembunyikan elemen UI */
        .no-print,
        .sidebar,
        .main-header,
        .main-footer,
        .btn,
        nav,
        form,
        .card-footer,
        .info-box,
        .breadcrumb,
        .modal { display: none !important; }

        /* Tampilkan elemen khusus print */
        .print-only { display: block !important; }

        /* Reset card styling */
        .card {
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
            margin-bottom: 12px !important;
        }
        .card-header {
            border-bottom: 2px solid #000 !important;
            padding: 6px 12px !important;
            background-color: #f8f9fa !important;
        }

        /* Tabel kompak */
        .logbook-table {
            font-size: 8pt !important;
        }
        .logbook-table th,
        .logbook-table td {
            padding: 3px 5px !important;
        }

        /* Section paraf selalu tampil di print */
        #sectionParaf {
            display: block !important;
            page-break-inside: avoid;
        }

        /* Pastikan paraf img tampil */
        .paraf-img {
            display: block !important;
            max-height: 60px;
        }

        /* Warna badge tetap kelihatan di print */
        .badge-success { background-color: #28a745 !important; color: #fff !important; }
        .badge-warning { background-color: #ffc107 !important; color: #212529 !important; }
        .badge-danger  { background-color: #dc3545 !important; color: #fff !important; }
        .badge-secondary { background-color: #6c757d !important; color: #fff !important; }
        .badge-info    { background-color: #17a2b8 !important; color: #fff !important; }

        body { font-size: 9pt; }
        .container-fluid { padding: 0 !important; }
    }
</style>
@endpush

@push('scripts')
<script>
    /**
     * Tetapkan action form modal sesuai ID logbook yang di-klik.
     * Fungsi ini sama dengan yang dipakai di index.blade.php.
     */
    function setModalId(formId, action) {
        document.getElementById(formId).setAttribute('action', action);
    }
</script>
@endpush