@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">

    {{-- ===== HEADER ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-uppercase font-weight-bold mb-0"
            style="border-left: 5px solid #003366; padding-left: 15px; color: #003366;">
            Detail Pengecekan {{ ucfirst($type) }}
        </h3>
        <a href="{{ $type == 'mingguan' ? route('maintenance.mingguan') : route('maintenance.harian') }}"
           class="btn btn-secondary btn-sm px-4" style="border-radius: 8px;">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    {{-- ===== INFO SESI ===== --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #eef7ff;">
        <div class="card-body py-3 px-4">
            <div class="row align-items-center">
                <div class="col-auto">
                    <i class="fas fa-calendar-check fa-2x" style="color: #003366;"></i>
                </div>
                <div class="col">
                    <p class="mb-0 font-weight-bold" style="color: #003366; font-size: 1.1rem;">
                        {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                    </p>
                    <p class="mb-0 text-muted small">
                        @if($type == 'harian')
                            <i class="fas fa-clock mr-1"></i> Shift: <strong>{{ $shift }}</strong>
                        @else
                            <i class="fas fa-calendar-day mr-1"></i> Hari: <strong>{{ $shift }}</strong>
                        @endif
                        &nbsp;|&nbsp;
                        <i class="fas fa-tools mr-1"></i> Jenis: <strong>{{ ucfirst($type) }}</strong>
                        &nbsp;|&nbsp;
                        <i class="fas fa-box mr-1"></i> Total Alat: <strong>{{ $groupedByKategori->flatten()->count() }}</strong>
                    </p>
                </div>
                {{-- Status Sesi --}}
                @php
                    $adaProses = $groupedByKategori->flatten()->contains(function($item) {
                        return $item->status == 'proses';
                    });
                @endphp
                <div class="col-auto">
                    @if($adaProses)
                        <span class="badge badge-warning px-3 py-2" style="border-radius: 20px; font-size: 0.85rem;">
                            <i class="fas fa-spinner mr-1"></i> Masih Proses
                        </span>
                    @else
                        <span class="badge badge-success px-3 py-2" style="border-radius: 20px; font-size: 0.85rem;">
                            <i class="fas fa-check-circle mr-1"></i> Selesai
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TOMBOL LANJUTKAN (jika masih proses) ===== --}}
    @if($adaProses)
    <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex justify-content-between align-items-center"
         style="border-radius: 10px;">
        <div>
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Sesi ini belum selesai.</strong> Masih ada alat yang belum diisi hasil pengecekannya.
        </div>
        @if(auth()->user()->canManageMaintenance())
        <a href="{{ route('maintenance.form-master', [
                'tanggal' => $tanggal,
                'waktu'   => $shift,
                'type'    => $type
            ]) }}"
        class="btn btn-warning btn-sm px-4 ml-3">
            <i class="fas fa-edit mr-1"></i> Lanjutkan Pengisian
        </a>
        @endif
    </div>
    @endif

    {{-- ===== TABEL DETAIL PER KATEGORI ===== --}}
    @forelse($groupedByKategori as $namaKategori => $items)

        {{-- Header Kategori --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header py-2 px-4 text-white font-weight-bold"
                 style="background-color: #343a40; font-size: 1rem;">
                <i class="fas fa-layer-group mr-2 text-warning"></i>
                {{ strtoupper($namaKategori) }}
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: #f8f9fa;">
                            <tr class="text-center small font-weight-bold text-uppercase text-muted">
                                <th class="px-4 py-3 text-left">Nama Alat</th>
                                <th class="py-3">Sub Kategori</th>
                                <th class="py-3">Status Operasional</th>
                                <th class="py-3">Kondisi Fisik</th>
                                <th class="py-3">Catatan</th>
                                <th class="py-3">Foto</th>
                                <th class="py-3">Status Cek</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                            @php
                                $cek = $pengecekan[$item->alat_id] ?? null;
                            @endphp
                            <tr class="align-middle">
                                {{-- Nama Alat --}}
                                <td class="px-4">
                                    <span class="font-weight-bold text-primary">
                                        {{ $item->alat->nama_alat ?? '-' }}
                                    </span><br>
                                    <small class="text-muted">
                                        S/N: {{ $item->alat->nomor_seri ?? '-' }}
                                        &nbsp;|&nbsp; Loc: {{ $item->alat->lokasi ?? '-' }}
                                    </small>
                                </td>

                                {{-- Sub Kategori --}}
                                <td class="text-center">
                                    <small class="text-muted">
                                        {{ $item->alat->subKategori->nama_sub_kategori ?? '-' }}
                                    </small>
                                </td>

                                {{-- Status Operasional (dari tabel alat) --}}
                                <td class="text-center">
                                    @php $statusAlat = $item->alat->status ?? '-'; @endphp
                                    <span class="badge badge-{{ $statusAlat == 'Aktif' ? 'success' : 'secondary' }} px-2 py-1"
                                          style="border-radius: 20px;">
                                        {{ $statusAlat }}
                                    </span>
                                </td>

                                {{-- Kondisi Fisik --}}
                                <td class="text-center">
                                    @if($cek)
                                        @php
                                            $kondisiColor = match($cek->kondisi_akhir ?? '') {
                                                'Baik'         => 'success',
                                                'Rusak Ringan' => 'warning',
                                                'Rusak Berat'  => 'danger',
                                                default        => 'secondary',
                                            };
                                        @endphp
                                        <span class="badge badge-{{ $kondisiColor }} px-2 py-1"
                                              style="border-radius: 20px;">
                                            {{ $cek->kondisi_akhir ?? '-' }}
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>

                                {{-- Catatan --}}
                                <td class="text-center">
                                    @if($cek && $cek->catatan)
                                        <span class="small text-dark">{{ $cek->catatan }}</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>

                                {{-- Foto --}}
                                <td class="text-center">
                                    @if($cek && $cek->foto_kegiatan)
                                        <a href="{{ asset('storage/' . $cek->foto_kegiatan) }}"
                                           target="_blank"
                                           class="btn btn-outline-info btn-sm"
                                           style="border-radius: 6px;">
                                            <i class="fas fa-image"></i>
                                        </a>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>

                                {{-- Status Cek --}}
                                <td class="text-center">
                                    @if($item->status == 'selesai')
                                        <span class="badge badge-success px-2 py-1" style="border-radius: 20px;">
                                            <i class="fas fa-check mr-1"></i> Selesai
                                        </span>
                                    @else
                                        <span class="badge badge-warning px-2 py-1" style="border-radius: 20px;">
                                            <i class="fas fa-hourglass-half mr-1"></i> Proses
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @empty
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-clipboard-list fa-3x mb-3 d-block" style="opacity: 0.3;"></i>
                Tidak ada data pengecekan untuk sesi ini.
            </div>
        </div>
    @endforelse

</div>
@endsection

@push('styles')
<style>
    .table thead th { border-top: none; }
    .table tbody tr:hover { background-color: #f0f7ff; }
</style>
@endpush