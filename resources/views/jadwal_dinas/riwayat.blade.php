@extends('layouts.master')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="m-0 font-weight-bold" style="font-size:1.5rem; color:#1a1a2e;">
                <i class="fas fa-history mr-2" style="color:#003366;"></i>Riwayat Jadwal Dinas
            </h4>
            <p class="text-muted mb-0" style="font-size:0.85rem;">
                Daftar lengkap seluruh penugasan jadwal dinas yang telah diinput
            </p>
        </div>
        <div class="col-md-6">
            <ol class="breadcrumb float-md-right bg-transparent m-0 p-0" style="font-size:0.8rem;">
                <li class="breadcrumb-item">
                    <a href="{{ route('jadwal_dinas.index') }}" class="text-muted">Jadwal Dinas</a>
                </li>
                <li class="breadcrumb-item active font-weight-bold" style="color:#003366;">Riwayat</li>
            </ol>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center" style="border-radius:8px;">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Info: default 30 hari terakhir jika belum ada filter tanggal --}}
    @if(!$adaFilterTanggal)
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center" style="border-radius:8px;">
            <i class="fas fa-info-circle mr-2"></i>
            Menampilkan data 30 hari terakhir secara default. Gunakan filter tanggal di bawah untuk melihat periode lain.
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- FILTER --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="card shadow-sm border-0 rounded-lg mb-3">
        <div class="card-body p-3">
            <form action="{{ route('jadwal_dinas.riwayat') }}" method="GET">
                <div class="row align-items-end">

                    {{-- Nama Petugas --}}
                    <div class="col-md-3 form-group mb-2">
                        <label class="font-weight-bold text-muted small text-uppercase mb-1">Nama Petugas</label>
                        <input type="text"
                               name="nama"
                               list="daftarNamaPetugas"
                               class="form-control shadow-none border"
                               placeholder="Cari nama..."
                               value="{{ request('nama') }}">
                        <datalist id="daftarNamaPetugas">
                            @foreach($daftarNama as $nm)
                                <option value="{{ $nm }}">
                            @endforeach
                        </datalist>
                    </div>

                    {{-- Tanggal Mulai --}}
                    <div class="col-md-3 form-group mb-2">
                        <label class="font-weight-bold text-muted small text-uppercase mb-1">Dari Tanggal</label>
                        <input type="date"
                               name="tanggal_mulai"
                               class="form-control shadow-none border"
                               value="{{ request('tanggal_mulai') }}">
                    </div>

                    {{-- Tanggal Selesai --}}
                    <div class="col-md-3 form-group mb-2">
                        <label class="font-weight-bold text-muted small text-uppercase mb-1">Sampai Tanggal</label>
                        <input type="date"
                               name="tanggal_selesai"
                               class="form-control shadow-none border"
                               value="{{ request('tanggal_selesai') }}">
                    </div>

                    {{-- Shift --}}
                    <div class="col-md-3 form-group mb-2">
                        <label class="font-weight-bold text-muted small text-uppercase mb-1">Shift</label>
                        <select name="shift_id" class="form-control shadow-none border">
                            <option value="">Semua Shift</option>
                            @foreach($masterShifts as $shift)
                                <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>
                                    {{ $shift->kode_shift }} &mdash; {{ $shift->nama_shift }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="d-flex justify-content-end mt-2" style="gap:8px;">
                    <a href="{{ route('jadwal_dinas.riwayat') }}" class="btn btn-outline-secondary font-weight-semibold px-4" style="border-radius:8px;">
                        <i class="fas fa-redo-alt mr-1"></i> Reset
                    </a>
                    <button type="submit" class="btn text-white font-weight-semibold px-4 shadow-sm" style="border-radius:8px; background-color:#003366;">
                        <i class="fas fa-filter mr-1"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- TABEL RIWAYAT --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-list text-primary mr-2"></i>Daftar Riwayat
            </h6>
            <small class="text-muted">
                Total: <strong>{{ $riwayatJadwal->total() }}</strong> entri
            </small>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:0.875rem;">
                    <thead class="bg-light text-dark font-weight-semibold border-bottom">
                        <tr>
                            <th class="border-0 py-3 pl-4" style="width:50px;">No</th>
                            <th class="border-0 py-3">Tanggal</th>
                            <th class="border-0 py-3">Hari</th>
                            <th class="border-0 py-3">Nama Petugas</th>
                            <th class="border-0 py-3">Shift</th>
                            <th class="border-0 py-3">Jam</th>
                            <th class="border-0 py-3 pr-4">Diinput Pada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatJadwal as $jw)
                        <tr>
                            <td class="py-3 pl-4 text-muted font-weight-bold">
                                {{ ($riwayatJadwal->currentPage() - 1) * $riwayatJadwal->perPage() + $loop->iteration }}
                            </td>
                            <td class="py-3 font-weight-semibold">
                                {{ \Carbon\Carbon::parse($jw->tanggal)->format('d/m/Y') }}
                            </td>
                            <td class="py-3 text-muted">
                                {{ \Carbon\Carbon::parse($jw->tanggal)->isoFormat('dddd') }}
                            </td>
                            <td class="py-3">
                                <span class="font-weight-semibold">{{ $jw->nama }}</span>
                            </td>
                            <td class="py-3">
                                <span class="badge badge-primary px-2 py-1">{{ $jw->shift }}</span>
                            </td>
                            <td class="py-3 text-muted">{{ $jw->jam }}</td>
                            <td class="py-3 pr-4 text-muted" style="font-size:0.78rem;">
                                {{ $jw->created_at ? $jw->created_at->format('d/m/Y H:i') : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-calendar-times fa-2x mb-3 d-block" style="opacity:.3;"></i>
                                <span class="font-weight-medium">Tidak ada data riwayat jadwal untuk filter ini</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($riwayatJadwal->hasPages())
        <div class="card-footer bg-light py-3">
            {{ $riwayatJadwal->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>

</div>
@endsection