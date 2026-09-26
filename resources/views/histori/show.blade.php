@extends('layouts.master')

@push('css')
<style>
    .bg-navy { background-color: #003366 !important; }
    .text-navy { color: #003366 !important; }
    .card-detail { transition: all 0.3s ease; border-radius: 12px; }
    .card-detail:hover { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important; }
    .table-detail th { font-weight: 600; color: #555; }
    .table-detail td, .table-detail th { padding: 0.75rem 0.5rem; vertical-align: top; }
    .border-dashed { border: 2px dashed #dee2e6 !important; }

    /* ==========================================================================
       TAMPILAN KHUSUS SAAT CETAK/PRINT (KERTAS FISIK)
       ========================================================================== */
    @media print {
        .btn, .btn-group, .main-sidebar, .main-header, .main-footer, .nav, .navbar, .navbar-nav, .breadcrumb, .btn-back-container, .page-actions {
            display: none !important;
        }
        .container-fluid { padding: 0 !important; }
        .row { display: block !important; }
        .col-lg-8, .col-md-12, .col-lg-4 {
            width: 100% !important;
            flex: 0 0 100% !important;
            max-width: 100% !important;
            margin-bottom: 20px !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #ccc !important;
            border-radius: 8px !important;
            page-break-inside: avoid;
        }
        .card-header {
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #ccc !important;
        }
        body {
            background-color: #fff !important;
            color: #000 !important;
            font-size: 13px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    
    {{-- HEADER PAGE --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h4 class="font-weight-bold text-dark mb-0">
            <a href="{{ route('histori.index') }}" class="btn btn-sm btn-light mr-2 shadow-sm rounded-circle btn-back-container" title="Kembali">
                <i class="fas fa-arrow-left"></i>
            </a>
            Detail Hasil Pengecekan Peralatan
        </h4>
        <div class="btn-group shadow-sm page-actions" style="border-radius: 8px;">
            {{-- PERBAIKAN PRINT: Menggunakan onclick langsung + window.focus() agar bypass error JS luar --}}
            <button type="button" onclick="window.focus(); window.print();" class="btn btn-light btn-sm border-right">
                <i class="fas fa-print mr-1 text-secondary"></i> Print Page
            </button>
            
            {{-- PERBAIKAN ROUTE: Diarahkan ke 'histori.download-single' sesuai web.php kamu --}}
            <a href="{{ route('histori.riwayat', $data->id, ['jenis' => $data->jenis_aktivitas]) }}"
            target="_blank"
            class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf mr-1"></i>
                Cetak Report
            </a>
        </div>
    </div>

    <div class="row">
        {{-- KOLOM KIRI (INFORMASI UTAMA) --}}
        <div class="col-lg-8 col-md-12 mb-4">
            
            {{-- KARTU 1: INFORMASI PERALATAN --}}
            <div class="card shadow-sm border-0 mb-4 card-detail">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="font-weight-bold m-0 text-navy">
                        <i class="fas fa-info-circle mr-2"></i>Informasi Peralatan
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-borderless table-detail mb-0">
                            <tr>
                                <th width="220">Nama Alat</th>
                                <td width="10">:</td>
                                <td>
                                    <strong class="text-primary" style="font-size: 1.1rem;">
                                        {{ optional($data->alat)->nama_alat ?? 'Tanpa Alat' }}
                                    </strong>
                                </td>
                            </tr>
                            <tr>
                                <th>Kategori / Sub</th>
                                <td>:</td>
                                <td>
                                    @if($data->alat && $data->alat->subKategori)
                                        <span class="badge badge-light border">
                                            {{ $data->alat->subKategori->kategori->nama_kategori ?? '-' }}
                                        </span>
                                        <i class="fas fa-chevron-right mx-1 text-muted" style="font-size: 10px;"></i>
                                        <span class="badge badge-light border">
                                            {{ $data->alat->subKategori->nama_sub_kategori ?? '-' }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Nomor Seri (S/N)</th>
                                <td>:</td>
                                <td>
                                    <code class="text-dark bg-light px-2 py-1 rounded border">
                                        {{ optional($data->alat)->nomor_seri ?? '-' }}
                                    </code>
                                </td>
                            </tr>
                            <tr>
                                <th>Lokasi Penempatan</th>
                                <td>:</td>
                                <td>
                                    <i class="fas fa-map-marker-alt text-danger mr-1"></i> 
                                    {{ optional($data->alat)->lokasi ?? '-' }}
                                </td>
                            </tr>
                            <tr class="border-top">
                                <th class="pt-3">Tanggal Pengecekan</th>
                                <td class="pt-3">:</td>
                                <td class="pt-3">
                                    <i class="far fa-calendar-alt text-muted mr-1"></i>
                                    @if(!empty($data->tanggal))
                                        {{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d F Y') }}
                                    @elseif(!empty($data->waktu))
                                        {{ \Carbon\Carbon::parse($data->waktu)->translatedFormat('d F Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Waktu / Shift</th>
                                <td>:</td>
                                <td>
                                    <span class="badge badge-info px-3 py-1">
                                        <i class="far fa-clock mr-1"></i> {{ $data->waktu_pelaksanaan ?? $data->waktu ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Kondisi Akhir</th>
                                <td>:</td>
                                <td>
                                    @php
                                        $badge = 'badge-secondary';
                                        $icon = 'fa-question-circle';

                                        if ($data->kondisi_akhir == 'Baik') {
                                            $badge = 'badge-success';
                                            $icon = 'fa-check-circle';
                                        } elseif ($data->kondisi_akhir == 'Rusak Ringan') {
                                            $badge = 'badge-warning';
                                            $icon = 'fa-exclamation-triangle';
                                        } elseif ($data->kondisi_akhir == 'Rusak Berat') {
                                            $badge = 'badge-danger';
                                            $icon = 'fa-times-circle';
                                        }
                                    @endphp
                                    <span class="badge {{ $badge }} px-3 py-1" style="font-size: 0.9rem;">
                                        <i class="fas {{ $icon }} mr-1"></i> {{ $data->kondisi_akhir ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- KARTU 2: HASIL PEMERIKSAAN --}}
            <div class="card shadow-sm border-0 card-detail">
                <div class="card-header bg-light py-3">
                    <h6 class="font-weight-bold m-0 text-dark">
                        <i class="fas fa-clipboard-check mr-2 text-success"></i>Hasil Pemeriksaan & Catatan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-detail mb-0">
                            <tr>
                                <th width="220">Jenis Aktivitas</th>
                                <td width="10">:</td>
                                <td>
                                    <span class="font-weight-bold text-navy">
                                        {{ $data->jenis_aktivitas ?? 'Lainnya' }}
                                    </span>
                                </td>
                            </tr>

                            @if(in_array($data->jenis_aktivitas ?? '', ['Maintenance Harian','Maintenance Mingguan']))
                                <tr>
                                    <th>Kondisi Akhir</th>
                                    <td>:</td>
                                    <td><span class="badge badge-success">{{ $data->kondisi_akhir }}</span></td>
                                </tr>
                                <tr>
                                    <th>Catatan Petugas</th>
                                    <td>:</td>
                                    <td>
                                        <div class="p-3 bg-light rounded border-left border-primary text-muted">
                                            {{ $data->catatan ?? 'Tidak ada catatan.' }}
                                        </div>
                                    </td>
                                </tr>
                            @elseif($data->jenis_aktivitas == 'Kalibrasi')
                                <tr>
                                    <th>Hasil Kalibrasi</th>
                                    <td>:</td>
                                    <td>{{ $data->deskripsi_hasil }}</td>
                                </tr>
                                @elseif($data->jenis_aktivitas == 'Gangguan')

<tr>
    <th>Kondisi Akhir</th>
    <td>:</td>
    <td>

        @php
            $badge = 'badge-secondary';
            $icon = 'fa-question-circle';

            if (($data->kondisi_akhir ?? '') == 'Baik') {
                $badge = 'badge-success';
                $icon = 'fa-check-circle';
            } elseif (($data->kondisi_akhir ?? '') == 'Rusak Ringan') {
                $badge = 'badge-warning';
                $icon = 'fa-tools';
            } elseif (($data->kondisi_akhir ?? '') == 'Rusak Berat') {
                $badge = 'badge-danger';
                $icon = 'fa-times-circle';
            }
        @endphp

        <span class="badge {{ $badge }}">
            <i class="fas {{ $icon }}"></i>
            {{ $data->kondisi_akhir ?? '-' }}
        </span>

    </td>
</tr>

<tr>
    <th>Laporan Gangguan</th>
    <td>:</td>
    <td>
        <div class="p-3 bg-light rounded border-left border-danger text-danger">
            {{ $data->catatan ?? $data->deskripsi_hasil ?? '-' }}
        </div>
    </td>
</tr>
                            @elseif($data->jenis_aktivitas == 'Pengujian')
                                <tr>
                                    <th>Hasil Pengujian</th>
                                    <td>:</td>
                                    <td>{{ $data->deskripsi_hasil }}</td>
                                </tr>
                            @else
                                <tr>
                                    <th>Keterangan</th>
                                    <td>:</td>
                                    <td>{{ $data->deskripsi_hasil ?? '-' }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN (SIDEBAR) --}}
        <div class="col-lg-4 col-md-12">
            
            {{-- KARTU 3: PETUGAS --}}
            <div class="card shadow-sm border-0 mb-4 card-detail">
                <div class="card-body">
                    <label class="small font-weight-bold text-muted d-block mb-3 border-bottom pb-2">PETUGAS PELAKSANA</label>
                    <div class="d-flex align-items-center">
                        <div class="bg-navy text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 50px; height: 50px;">
                            <i class="fas fa-user-check fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 font-weight-bold text-dark">{{ $data->user->username ?? 'Petugas' }}</h6>
                            <small class="text-muted"><i class="fas fa-id-badge mr-1"></i> ID: {{ $data->user_id ?? '-' }}</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KARTU 4: DOKUMEN / FOTO --}}
            <div class="card shadow-sm border-0 card-detail">
                <div class="card-header bg-white py-3 text-center border-bottom-0">
                    <h6 class="font-weight-bold m-0 text-dark">
                        <i class="fas fa-camera mr-1 text-muted"></i> Foto Dokumentasi
                    </h6>
                </div>
                <div class="card-body text-center pt-0">
                    @if($data->dokumen)
                        @php
                            $ext = strtolower(pathinfo($data->dokumen, PATHINFO_EXTENSION));
                            $imgExt = ['jpg', 'jpeg', 'png', 'webp'];
                        @endphp

                        @if(in_array($ext, $imgExt))
                            <div class="position-relative mb-3">
                                <img src="{{ asset('storage/' . $data->dokumen) }}"
                                     class="img-fluid rounded shadow-sm w-100"
                                     alt="Foto Dokumentasi"
                                     style="max-height:220px; object-fit:cover; border: 1px solid #ddd;">
                            </div>
                            <a href="{{ asset('storage/' . $data->dokumen) }}"
                               target="_blank"
                               class="btn btn-outline-primary btn-block btn-sm shadow-sm">
                                <i class="fas fa-search-plus mr-1"></i> Lihat Foto Penuh
                            </a>
                        @else
                            <div class="py-4 bg-light rounded border mb-3">
                                <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                <p class="text-muted small mb-0">Format Dokumen PDF/File</p>
                            </div>
                            <a href="{{ asset('storage/' . $data->dokumen) }}"
                               target="_blank"
                               class="btn btn-danger btn-block shadow-sm">
                                <i class="fas fa-download mr-1"></i> Buka / Unduh Dokumen
                            </a>
                        @endif
                    @else
                        <div class="py-5 bg-light rounded border border-dashed">
                            <i class="fas fa-image fa-3x text-muted opacity-50 mb-3"></i>
                            <p class="text-muted small mb-0 font-italic">
                                Tidak ada dokumentasi diunggah.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection