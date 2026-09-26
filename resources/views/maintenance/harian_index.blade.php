@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">

    {{-- ===== HEADER ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-uppercase font-weight-bold mb-0"
            style="border-left: 5px solid #003366; padding-left: 15px; color: #003366;">
            Maintenance Harian
        </h3>
        @if(auth()->user()->canManageMaintenance())
        <a href="{{ route('maintenance.harian.create') }}"
           class="btn btn-primary btn-sm px-4"
           style="background-color: #003366; border: none; border-radius: 8px;">
            <i class="fas fa-plus mr-1"></i> Pengecekan Baru
        </a>
        @endif
    </div>

    {{-- ===== FLASH MESSAGE ===== --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert"
             style="border-radius: 10px;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- ===== FILTER CARD ===== --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body py-3 px-4">
            <form method="GET" action="{{ route('maintenance.harian') }}" class="form-inline flex-wrap" style="gap: 12px;">
                <div class="form-group mb-2">
                    <label class="mr-2 font-weight-bold small text-muted text-uppercase">Dari</label>
                    <input type="date" name="dari" value="{{ request('dari', now()->startOfMonth()->format('Y-m-d')) }}"
                           class="form-control form-control-sm" style="border-radius: 8px;">
                </div>
                <div class="form-group mb-2">
                    <label class="mr-2 font-weight-bold small text-muted text-uppercase">Sampai</label>
                    <input type="date" name="sampai" value="{{ request('sampai', now()->format('Y-m-d')) }}"
                           class="form-control form-control-sm" style="border-radius: 8px;">
                </div>
                <div class="form-group mb-2">
                    <label class="mr-2 font-weight-bold small text-muted text-uppercase">Shift</label>
                    <select name="shift" class="form-control form-control-sm" style="border-radius: 8px;">
                        <option value="">Semua Shift</option>
                        <option value="Pagi"  {{ request('shift') == 'Pagi'  ? 'selected' : '' }}>Pagi</option>
                        <option value="Siang" {{ request('shift') == 'Siang' ? 'selected' : '' }}>Siang</option>
                        <option value="Malam" {{ request('shift') == 'Malam' ? 'selected' : '' }}>Malam</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label class="mr-2 font-weight-bold small text-muted text-uppercase">Status</label>
                    <select name="status" class="form-control form-control-sm" style="border-radius: 8px;">
                        <option value="">Semua Status</option>
                        <option value="proses"  {{ request('status') == 'proses'  ? 'selected' : '' }}>Proses</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                @if(auth()->user()->canManageMaintenance())
                <button type="submit" class="btn btn-primary btn-sm mb-2 px-4"
                        style="background-color: #003366; border: none; border-radius: 8px;">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                @endif
                <a href="{{ route('maintenance.harian') }}" class="btn btn-secondary btn-sm mb-2 px-3"
                   style="border-radius: 8px;">
                    <i class="fas fa-times mr-1"></i> Reset
                </a>
            </form>
        </div>
    </div>

    {{-- ===== SUMMARY CARD ===== --}}
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm text-center py-3" style="border-radius: 12px; background: #eef7ff;">
                <div class="card-body p-2">
                    <p class="mb-1 text-muted small text-uppercase font-weight-bold">Total Sesi</p>
                    <h4 class="font-weight-bold mb-0" style="color: #003366;">{{ $totalSesi }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm text-center py-3" style="border-radius: 12px; background: #fff8e1;">
                <div class="card-body p-2">
                    <p class="mb-1 text-muted small text-uppercase font-weight-bold">Total Alat Dicek</p>
                    <h4 class="font-weight-bold mb-0" style="color: #e67e22;">{{ $totalAlatDicek }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm text-center py-3" style="border-radius: 12px; background: #e8f5e9;">
                <div class="card-body p-2">
                    <p class="mb-1 text-muted small text-uppercase font-weight-bold">Selesai</p>
                    <h4 class="font-weight-bold mb-0 text-success">{{ $totalSelesai }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm text-center py-3" style="border-radius: 12px; background: #fff3e0;">
                <div class="card-body p-2">
                    <p class="mb-1 text-muted small text-uppercase font-weight-bold">Masih Proses</p>
                    <h4 class="font-weight-bold mb-0 text-warning">{{ $totalProses }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TABEL HISTORY ===== --}}
    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="m-0 font-weight-bold text-dark">
                <i class="fas fa-history mr-2 text-primary"></i> Riwayat Pengecekan Harian
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="tabelHarian" class="table table-hover mb-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr class="text-center small font-weight-bold text-uppercase text-muted">
                            <th class="px-4 py-3">#</th>
                            <th class="py-3">Tanggal</th>
                            <th class="py-3">Shift</th>
                            <th class="py-3">Jumlah Alat</th>
                            <th class="py-3">Selesai</th>
                            <th class="py-3">Proses</th>
                            <th class="py-3">Status Sesi</th>
                            <th class="py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sesiList as $index => $sesi)
                        <tr class="align-middle">
                            <td class="text-center px-4 text-muted">{{ $index + 1 }}</td>
                            <td class="text-center font-weight-bold">
                                {{ \Carbon\Carbon::parse($sesi->tanggal)->translatedFormat('d F Y') }}
                            </td>
                            <td class="text-center">
                                @php
                                    $shiftColor = match($sesi->shift) {
                                        'Pagi'  => 'warning',
                                        'Siang' => 'info',
                                        'Malam' => 'dark',
                                        default => 'secondary',
                                    };
                                    $shiftIcon = match($sesi->shift) {
                                        'Pagi'  => 'fa-sun',
                                        'Siang' => 'fa-cloud-sun',
                                        'Malam' => 'fa-moon',
                                        default => 'fa-clock',
                                    };
                                @endphp
                                <span class="badge badge-{{ $shiftColor }} px-3 py-2" style="border-radius: 20px; font-size: 0.8rem;">
                                    <i class="fas {{ $shiftIcon }} mr-1"></i> {{ $sesi->shift }}
                                </span>
                            </td>
                            <td class="text-center font-weight-bold">{{ $sesi->total_alat }}</td>
                            <td class="text-center">
                                <span class="text-success font-weight-bold">{{ $sesi->jumlah_selesai }}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-warning font-weight-bold">{{ $sesi->jumlah_proses }}</span>
                            </td>
                            <td class="text-center">
                                @if($sesi->jumlah_proses == 0)
                                    <span class="badge badge-success px-3 py-2" style="border-radius: 20px;">
                                        <i class="fas fa-check-circle mr-1"></i> Selesai
                                    </span>
                                @else
                                    <span class="badge badge-warning px-3 py-2" style="border-radius: 20px;">
                                        <i class="fas fa-spinner mr-1"></i> Proses
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    {{-- Tombol Lanjutkan (jika masih ada yang proses) --}}
                                    @if(auth()->user()->canManageMaintenance() && $sesi->jumlah_proses > 0)
                                        <a href="{{ route('maintenance.form-master', [
                                                'tanggal' => $sesi->tanggal,
                                                'waktu'   => $sesi->shift,
                                                'type'    => 'harian'
                                            ]) }}"
                                            class="btn btn-warning btn-sm"
                                            title="Lanjutkan Pengisian"
                                            style="border-radius: 6px 0 0 6px;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    {{-- Tombol Detail --}}
                                    <a href="{{ route('maintenance.detail', [
                                            'tanggal' => $sesi->tanggal,
                                            'shift'   => $sesi->shift,
                                            'type'    => 'harian'
                                        ]) }}"
                                        class="btn btn-info btn-sm"
                                        title="Lihat Detail"
                                        style="border-radius: {{ $sesi->jumlah_proses > 0 ? '0 6px 6px 0' : '6px' }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-clipboard-list fa-3x mb-3 d-block" style="opacity: 0.3;"></i>
                                Belum ada data maintenance harian.<br>
                                <small>Klik tombol <strong>Pengecekan Baru</strong> untuk memulai.</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($sesiList instanceof \Illuminate\Pagination\LengthAwarePaginator && $sesiList->hasPages())
        <div class="card-footer bg-white d-flex justify-content-end py-3">
            {{ $sesiList->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<style>
    .table thead th { border-top: none; }
    .table tbody tr:hover { background-color: #f0f7ff; }
    .badge { letter-spacing: 0.3px; }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function () {
        $('#tabelHarian').DataTable({
            paging: false,
            searching: false,
            info: false,
            order: [[1, 'desc']],
            language: {
                emptyTable: "Tidak ada data tersedia"
            }
        });
    });
</script>
@endpush