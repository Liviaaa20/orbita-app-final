@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="font-weight-bold text-dark"><i class="fas fa-history mr-2"></i>Histori Operasional</h4>
    </div>

    {{-- BARIS FILTER --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('histori.index') }}" method="GET" id="filterForm">
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <label class="small font-weight-bold">Periode</label>
                        <input type="text" name="periode" class="form-control form-control-sm" id="daterange" value="{{ request('periode') }}" autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <label class="small font-weight-bold">Jenis Aktivitas</label>
                        <select name="jenis_aktivitas" class="form-control form-control-sm">
                            <option value="">Semua Aktivitas</option>
                            @foreach($jenisAktivitas as $ja)
                                <option value="{{ $ja }}" {{ request('jenis_aktivitas') == $ja ? 'selected' : '' }}>{{ $ja }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small font-weight-bold">Kategori</label>
                        <select name="kategori" class="form-control form-control-sm">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoris as $k)
                                <option value="{{ $k->nama_kategori }}" {{ request('kategori') == $k->nama_kategori ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small font-weight-bold">Jenis Alat</label>
                        <select name="alat_id" class="form-control form-control-sm">
                            <option value="">Semua Jenis Alat</option>
                            @foreach($alats as $a)
                                <option value="{{ $a->id }}" {{ request('alat_id') == $a->id ? 'selected' : '' }}>{{ $a->nama_alat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small font-weight-bold">Lokasi</label>
                        <select name="lokasi" class="form-control form-control-sm">
                            <option value="">Semua Lokasi</option>
                            @foreach($alats->unique('lokasi') as $l)
                                <option value="{{ $l->lokasi }}" {{ request('lokasi') == $l->lokasi ? 'selected' : '' }}>{{ $l->lokasi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark btn-sm btn-block shadow-sm">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="row mb-4">
        @foreach([
            ['L' => 'Total Aktivitas', 'C' => $summary['total'], 'I' => 'fa-chart-line'],
            ['L' => 'Maintenance Harian', 'C' => $summary['maintenance_harian'], 'I' => 'fa-tools'],
            ['L' => 'Maintenance Mingguan', 'C' => $summary['maintenance_mingguan'], 'I' => 'fa-calendar-check'],
            ['L' => 'Kalibrasi', 'C' => $summary['kalibrasi'], 'I' => 'fa-satellite-dish'],
            ['L' => 'Gangguan', 'C' => $summary['gangguan'], 'I' => 'fa-exclamation-triangle'],
            ['L' => 'Lainnya', 'C' => $summary['lainnya'], 'I' => 'fa-file-alt']
        ] as $card)
        <div class="col-md-2 mb-3">
            <div class="card shadow-sm border-0 text-center py-3 h-100">
                <i class="fas {{ $card['I'] }} text-muted mb-2"></i>
                <h3 class="font-weight-bold mb-0">{{ $card['C'] }}</h3>
                <small class="text-muted font-weight-bold small text-uppercase">{{ $card['L'] }}</small>
            </div>
        </div>
        @endforeach
    </div>

    {{-- TABEL UTAMA --}}
    <div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-white py-3">
        <div class="d-flex align-items-center justify-content-between">
            <h6 class="font-weight-bold m-0 text-dark">Daftar Aktivitas</h6>
            
            {{-- Tombol diletakkan di dalam div ini agar otomatis terdorong ke kanan --}}
            <div>
                <a href="{{ route('histori.export', request()->query()) }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                    <i class="fas fa-download mr-1"></i> Unduh Laporan
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light small font-weight-bold text-center">
                        <tr>
                            <th>No</th>
                            <th>Waktu</th>
                            <th>Jenis</th>
                            <th class="text-left">Kategori & Sub</th>
                            <th>Alat</th>
                            <th>Lokasi</th>
                            <th class="text-left">Deskripsi</th>
                            <th>Petugas</th>
                            <th>Dokumen</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histori as $item)
                        <tr class="text-center small">
                            <td class="align-middle">{{ $loop->iteration + ($histori->firstItem() - 1) }}</td>
                            <td class="align-middle font-weight-bold">{{ \Carbon\Carbon::parse($item->tanggal_raw)->format('d/m/Y') }}</td>
                            <td class="align-middle">
                                @php
                                    $badge = match($item->jenis_aktivitas) {
                                        'Maintenance Harian' => 'badge-info',
                                        'Maintenance Mingguan' => 'badge-primary',
                                        'Gangguan' => 'badge-danger',
                                        default => 'badge-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badge }} shadow-sm px-2 py-1 text-white">{{ $item->jenis_aktivitas }}</span>
                            </td>
                            <td class="align-middle text-left">
                                <span class="font-weight-bold text-uppercase d-block text-xs">{{ $item->nama_kategori ?? 'N/A' }}</span>
                                <span class="text-muted small italic">{{ $item->nama_sub_kategori ?? '' }}</span>
                            </td>
                            <td class="align-middle font-weight-bold text-primary">
                                {{ $item->nama_alat ?? 'Tanpa Alat' }}
                            </td>
                            <td class="align-middle">{{ $item->lokasi }}</td>
                            <td class="align-middle text-left">
                                <span title="{{ $item->deskripsi }}">{{ Str::limit($item->deskripsi, 40) }}</span>
                            </td>
                            <td class="align-middle font-weight-bold text-dark">{{ $item->petugas }}</td>
                            <td class="align-middle">
                                @if($item->dokumen)
                                    @php 
                                        $ext = pathinfo($item->dokumen, PATHINFO_EXTENSION);
                                        $imgExt = ['jpg', 'jpeg', 'png', 'webp'];
                                    @endphp
                                    @if(in_array(strtolower($ext), $imgExt))
                                        <a href="{{ asset('storage/' . $item->dokumen) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $item->dokumen) }}" 
                                                 class="rounded shadow-sm border" 
                                                 style="width: 35px; height: 35px; object-fit: cover;"
                                                 onerror="this.src='https://placehold.co/100x100?text=No+Img'">
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $item->dokumen) }}" target="_blank" class="text-danger">
                                            <i class="fas fa-file-pdf fa-2x"></i>
                                        </a>
                                    @endif
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div class="btn-group">
                                    {{-- Link Detail dengan parameter jenis --}}
                                    <a href="{{ route('histori.show', $item->id) }}?jenis={{ urlencode($item->jenis_aktivitas) }}" 
                                    class="btn btn-sm btn-light text-info border shadow-sm" 
                                    title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Link Cetak dengan parameter jenis --}}
                                    <a href="{{ route('histori.riwayat', $item->id) }}?jenis={{ urlencode($item->jenis_aktivitas) }}" 
                                    target="_blank" 
                                    class="btn btn-sm btn-light text-primary border shadow-sm" 
                                    title="Cetak PDF">
                                    <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">Data tidak ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top-0 py-3">
    <div class="d-flex justify-content-between align-items-center">
        {{-- Teks Info (Kiri) --}}
        <div class="text-muted small">
            Menampilkan <strong>{{ $histori->firstItem() ?? 0 }}</strong> 
            sampai <strong>{{ $histori->lastItem() ?? 0 }}</strong> 
            dari <strong>{{ $histori->total() }}</strong> entri
        </div>

        {{-- Tombol Halaman (Kanan) --}}
        <div class="pagination-sm">
            {{ $histori->appends(request()->query())->links() }}
        </div>
    </div>
</div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
    .badge { font-size: 10px; }
    .text-xs { font-size: 11px; }
    .pagination { margin-bottom: 0; }
</style>
@endpush

@push('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
$(function() {
    $('#daterange').daterangepicker({
        autoUpdateInput: false,
        locale: { format: 'DD/MM/YYYY', cancelLabel: 'Clear' }
    });
    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    });
});
</script>
@endpush