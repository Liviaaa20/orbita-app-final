@extends('layouts.master')

@section('content')
<div class="container-fluid">

    {{-- BARU: Notifikasi hasil import (error per baris jika ada) --}}
    @if(session('import_errors'))
    <div class="alert alert-danger shadow-sm border-0 rounded-lg">
        <h6 class="font-weight-bold mb-2">
            <i class="fas fa-exclamation-triangle mr-1"></i>
            Import Dibatalkan &mdash; Ditemukan {{ count(session('import_errors')) }} Baris Bermasalah
        </h6>
        <p class="small text-muted mb-2">
            Tidak ada data yang disimpan. Perbaiki baris-baris di bawah ini pada file Anda, lalu unggah ulang.
        </p>
        <ul class="mb-0 small" style="max-height:200px; overflow-y:auto;">
            @foreach(session('import_errors') as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    {{-- Atas: Filter Periode & Tombol Unduh --}}
    <div class="card shadow-sm border-0 rounded-lg mb-3">
        <div class="card-body p-3">
            <form action="{{ route('jadwal_dinas.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="font-weight-bold text-muted small text-uppercase mb-1">Periode</label>
                        <div class="input-group">
                            {{-- Value otomatis terisi tanggal hari ini atau tanggal yang sedang difilter --}}
                            <input type="date" name="periode" class="form-control rounded-left shadow-none border" 
                                value="{{ $periodeInput }}" 
                                onchange="this.form.submit()">
                            <div class="input-group-append" onclick="this.closest('form').submit();" style="cursor: pointer;">
                                <span class="input-group-text bg-white border-left-0"><i class="fas fa-calendar-alt text-muted"></i></span>
                            </div>
                        </div>
                    </div>
<div class="col-md-8 text-right mt-3 mt-md-0">
    @if($bisaInput)
        <a href="{{ route('jadwal_dinas.create') }}"
           class="btn btn-primary font-weight-bold px-3 shadow-sm rounded-lg mr-2"
           style="background-color:#003366; border-color:#003366;">
            <i class="fas fa-plus mr-1"></i> Input Jadwal
        </a>

        {{-- BARU: Tombol Import Jadwal --}}
        <button type="button"
                class="btn btn-light border font-weight-bold text-dark px-3 shadow-sm rounded-lg mr-2"
                data-toggle="modal" data-target="#modalImportJadwal">
            <i class="fas fa-file-upload mr-1 text-success"></i> Import Jadwal
        </button>
    @endif
    <button type="button"
            class="btn btn-light border font-weight-bold text-dark px-3 shadow-sm rounded-lg"
            data-toggle="modal" data-target="#modalUnduhJadwal">
        <i class="fas fa-download mr-1"></i> Unduh Jadwal
    </button>
</div>
                </div>
            </form>
        </div>
    </div>
    
    {{-- Konten Utama dengan Sistem Tab Konten --}}
    <div class="card shadow-sm border-0 rounded-lg overflow-hidden mb-3">
        {{-- Header Sub-Menu: Navigasi Minggu/Bulan Menggunakan Nav-Pills Bootstrap --}}
        <div class="card-header bg-white border-bottom p-3 d-flex align-items-center justify-content-between">
            <ul class="nav nav-pills" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link btn-sm font-weight-bold px-3 border active" id="pills-minggu-tab" data-toggle="pill" href="#pills-minggu" role="tab" aria-controls="pills-minggu" aria-selected="true" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">Minggu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn-sm font-weight-bold px-3 border" id="pills-bulan-tab" data-toggle="pill" href="#pills-bulan" role="tab" aria-controls="pills-bulan" aria-selected="false" style="border-top-left-radius: 0; border-bottom-left-radius: 0; margin-left: -1px;">Bulan</a>
                </li>
            </ul>
            <h3 class="card-title font-weight-bold m-0 text-dark text-center flex-grow-1" style="font-size: 1.3rem; padding-right: 80px;">
                {{ \Carbon\Carbon::parse($periodeInput)->isoFormat('MMMM YYYY') }}
            </h3>
        </div>

        <div class="tab-content" id="pills-tabContent">
            
            @if($jadwals->isEmpty())
                {{-- Tampilan jika Kepala kelompok belum mengisi jadwal sama sekali --}}
                <div class="text-center py-5 bg-white">
                    <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 3rem;"></i>
                    <h5 class="font-weight-bold text-dark">Belum Ada Jadwal Dinas</h5>
                    <p class="text-muted small">Kepala kelompok belum merilis atau mengisi penugasan dinas untuk periode ini.</p>
                </div>
            @else
                @php
                    // Mengelompokkan data berdasarkan nama petugas
                    $groupedJadwal = $jadwals->groupBy('nama');
                    
                    // Menentukan awal minggu (Senin) secara dinamis berdasarkan tanggal yang sedang aktif
                    $currentDate = \Carbon\Carbon::parse($periodeInput);
                    $startOfWeek = $currentDate->copy()->startOfWeek(); // Otomatis cari hari Senin di minggu tersebut
                    $endOfWeek = $startOfWeek->copy()->addDays(6);
                @endphp

                {{-- ================= TAB MODE MINGGUAN ================= --}}
                <div class="tab-pane fade show active" id="pills-minggu" role="tabpanel" aria-labelledby="pills-minggu-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle mb-0">
                            <thead class="bg-light text-dark font-weight-bold" style="font-size: 0.9rem;">
                                <tr>
                                    <th style="width: 5%; vertical-align: middle;">No</th>
                                    <th style="width: 25%; vertical-align: middle;" class="text-left px-4">Nama</th>
                                    @for($i = 0; $i < 7; $i++)
                                        @php $day = $startOfWeek->copy()->addDays($i); @endphp
                                        <th style="width: 10%;">{{ $day->isoFormat('dddd, D MMMM') }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.85rem;">
                                @foreach($groupedJadwal as $namaPetugas => $daftarJadwal)
                                    <tr>
                                        <td class="text-muted font-weight-medium align-middle">{{ $loop->iteration }}</td>
                                        <td class="text-left px-4 align-middle">
                                            <div class="font-weight-bold text-dark">{{ $namaPetugas }}</div>
                                            <small class="text-muted font-mono">NIP. {{ $daftarJadwal->first()->nip ?? '199807232008011015' }}</small>
                                        </td>
                                        
                                        {{-- Looping 7 hari secara dinas dinamis --}}
                                        @for($i = 0; $i < 7; $i++)
                                            @php
                                                $tanggalTarget = $startOfWeek->copy()->addDays($i)->format('Y-m-d');
                                                $jadwalHariIni = $daftarJadwal->firstWhere('tanggal', $tanggalTarget);
                                            @endphp
                                            
                                            @if($jadwalHariIni)
                                                <td class="bg-light align-middle font-weight-bold py-3" style="border: 1px solid #dee2e6;">
                                                    <div style="font-size: 1.1rem; color: #111; font-weight: 800;">{{ $jadwalHariIni->shift }}</div>
                                                    <small class="text-muted font-weight-normal d-block mt-1" style="font-size: 0.7rem;">{{ $jadwalHariIni->jam }}</small>
                                                </td>
                                            @else
                                                <td class="align-middle" style="background-color: #ffffff;"></td>
                                            @endif
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ================= TAB MODE BULANAN ================= --}}
                <div class="tab-pane fade" id="pills-bulan" role="tabpanel" aria-labelledby="pills-bulan-tab">
                    <div class="table-responsive">
                        @php
                            $totalDaysInMonth = $currentDate->daysInMonth;
                            $yearMonth = $currentDate->format('Y-m-');
                        @endphp
                        <table class="table table-bordered text-center align-middle mb-0" style="min-width: 1200px; font-size: 0.8rem;">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th rowspan="2" style="width: 3%; vertical-align: middle;">No</th>
                                    <th rowspan="2" style="width: 15%; vertical-align: middle;" class="text-left px-3">Nama</th>
                                    @for($d = 1; $d <= $totalDaysInMonth; $d++)
                                        <th class="p-1 font-weight-bold" style="width: 2.6%;">{{ $d }}</th>
                                    @endfor
                                </tr>
                                <tr class="text-muted" style="font-size: 0.72rem;">
                                    @for($d = 1; $d <= $totalDaysInMonth; $d++)
                                        @php 
                                            $dateOnMonth = \Carbon\Carbon::parse($yearMonth . str_pad($d, 2, '0', STR_PAD_LEFT));
                                            $shortDayName = substr($dateOnMonth->isoFormat('dddd'), 0, 1);
                                        @endphp
                                        <th class="p-1 font-weight-normal border-top-0">{{ $shortDayName }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupedJadwal as $namaPetugas => $daftarJadwal)
                                    <tr>
                                        <td class="align-middle text-muted">{{ $loop->iteration }}</td>
                                        <td class="text-left px-3 align-middle">
                                            <div class="font-weight-bold text-dark" style="font-size: 0.85rem;">{{ $namaPetugas }}</div>
                                        </td>
                                        
                                        @for($i = 1; $i <= $totalDaysInMonth; $i++)
                                            @php
                                                $tanggalTargetBulanan = $yearMonth . str_pad($i, 2, '0', STR_PAD_LEFT);
                                                $jadwalTanggalIni = $daftarJadwal->firstWhere('tanggal', $tanggalTargetBulanan);
                                            @endphp
                                            
                                            <td class="align-middle p-0 {{ $jadwalTanggalIni ? 'bg-light font-weight-bold text-dark' : '' }}" style="height: 48px; font-weight: 700;">
                                                {{ $jadwalTanggalIni ? $jadwalTanggalIni->shift : '' }}
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>

{{-- Bagian Bawah: Referensi Shift --}}
<div class="card shadow-sm border-0 rounded-lg">
    <div class="card-body p-3">

        <div class="font-weight-bold text-dark mb-3 text-uppercase"
             style="font-size:0.85rem;">
            Referensi Shift
        </div>

        <div class="row">

            @forelse($masterShifts as $shift)

                <div class="col-md-6 mb-2">

                    <div class="border rounded p-2 h-100">

                        <div class="mb-1">
                            <span class="badge badge-primary">
                                {{ $shift->kode_shift }}
                            </span>

                            <strong>
                                {{ strtoupper($shift->nama_shift) }}
                            </strong>
                        </div>

                        <small class="text-muted d-block">
                            {{ \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i') }}
                            -
                            {{ \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i') }}
                            WIB
                        </small>

                        @if($shift->keterangan)
                            <small class="d-block mt-1">
                                {{ $shift->keterangan }}
                            </small>
                        @endif

                    </div>

                </div>

            @empty

                <div class="col-12">
                    <div class="alert alert-warning mb-0">
                        Belum ada data shift.
                    </div>
                </div>

            @endforelse

        </div>

    </div>
</div>

{{-- ==================== POP-UP MODAL UNDUH JADWAL ==================== --}}
<div class="modal fade" id="modalUnduhJadwal" tabindex="-1" role="dialog" aria-labelledby="modalUnduhJadwalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title font-weight-bold text-dark" id="modalUnduhJadwalLabel">Konfigurasi Unduh Jadwal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('jadwal-dinas.download') }}" method="GET" target="_blank">
                <div class="modal-body text-dark font-weight-bold small text-muted text-uppercase text-left">
                    
                    {{-- Pilihan Jenis Laporan --}}
                    <div class="form-group">
                        <label class="mb-1 text-dark">Pilih Jenis Laporan</label>
                        <select name="tipe_periode" id="tipe_periode" class="form-control shadow-none border custom-select" onchange="aturFormUnduh()" required>
                            <option value="mingguan">Mingguan (Rentang Tanggal)</option>
                            <option value="bulanan">Bulanan</option>
                        </select>
                    </div>

                    {{-- BARIS INPUT MINGGUAN --}}
                    <div id="group_mingguan" class="row">
                        <div class="col-6 form-group">
                            <label class="mb-1 text-dark">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control text-dark font-weight-normal shadow-none" value="{{ isset($startOfWeek) ? $startOfWeek->format('Y-m-d') : date('Y-m-d') }}">
                        </div>
                        <div class="col-6 form-group">
                            <label class="mb-1 text-dark">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control text-dark font-weight-normal shadow-none" value="{{ isset($endOfWeek) ? $endOfWeek->format('Y-m-d') : date('Y-m-d', strtotime('+6 days')) }}">
                        </div>
                    </div>

                    {{-- BARIS INPUT BULANAN --}}
                    <div id="group_bulanan" class="form-group d-none">
                        <label class="mb-1 text-dark">Pilih Bulan</label>
                        <select name="bulan_pilihan" id="bulan_pilihan" class="form-control text-dark font-weight-normal shadow-none border custom-select">
                            @php
                                $bulanIndo = [
                                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                ];
                                $bulanSekarang = isset($currentDate) ? $currentDate->format('m') : date('m');
                                $tahunSekarang = isset($currentDate) ? $currentDate->format('Y') : date('Y');
                            @endphp
                            @foreach($bulanIndo as $value => $namaBulan)
                                <option value="{{ $tahunSekarang }}-{{ $value }}" {{ $bulanSekarang == $value ? 'selected' : '' }}>
                                    {{ $namaBulan }} {{ $tahunSekarang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary font-weight-bold shadow-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold shadow-sm px-4">
                        <i class="fas fa-file-download mr-1"></i> Mulai Unduh
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==================== POP-UP MODAL IMPORT JADWAL (BARU) ==================== --}}
<div class="modal fade" id="modalImportJadwal" tabindex="-1" role="dialog" aria-labelledby="modalImportJadwalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">

            <form action="{{ route('jadwal_dinas.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title font-weight-bold text-dark" id="modalImportJadwalLabel">
                        Import Jadwal Dinas (CSV / XLSX)
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body text-left">

                    <div class="alert alert-info small mb-3">
                        <strong class="d-block mb-1"><i class="fas fa-info-circle mr-1"></i>Format Kolom File</strong>
                        Baris pertama wajib header. Kolom yang dibutuhkan: <code>nama</code>, <code>tanggal</code> (YYYY-MM-DD), <code>shift</code> (kode shift).
                        <br>
                        Contoh: <code>Budi Santoso, 2026-07-01, P</code>
                        <div class="mt-2">
                            <a href="{{ asset('contoh/contoh_import_jadwal_dinas.xlsx') }}" class="font-weight-bold mr-3" download>
                                <i class="fas fa-file-excel text-success mr-1"></i>Unduh Contoh .xlsx
                            </a>
                            <a href="{{ asset('contoh/contoh_import_jadwal_dinas.csv') }}" class="font-weight-bold" download>
                                <i class="fas fa-file-csv text-primary mr-1"></i>Unduh Contoh .csv
                            </a>
                        </div>
                    </div>

                    {{-- BARU: Catatan penting soal kode shift wajib dicek dulu --}}
                    <div class="alert alert-warning small mb-3">
                        <strong class="d-block mb-1"><i class="fas fa-exclamation-triangle mr-1"></i>Penting: Cek Kode Shift Dahulu</strong>
                        Kolom <code>shift</code> pada file wajib persis sama dengan salah satu kode di
                        <strong>Referensi Shift</strong> (lihat tabel di bagian bawah halaman ini).
                        Jika ada satu baris saja dengan kode shift yang tidak ditemukan, <strong>seluruh proses import akan dibatalkan</strong> dan tidak ada data yang tersimpan.
                        <br>
                        Kode shift yang saat ini tersedia:
                        <span class="d-inline-block mt-1">
                            @forelse($masterShifts as $shift)
                                <span class="badge badge-primary mr-1">{{ $shift->kode_shift }}</span>
                            @empty
                                <span class="text-muted">Belum ada data Master Shift.</span>
                            @endforelse
                        </span>
                    </div>

                    <div class="form-group">
                        <label class="mb-1 text-dark font-weight-bold small text-uppercase">Pilih File</label>
                        <input type="file"
                               name="file_jadwal"
                               class="form-control-file"
                               accept=".csv,.txt,.xlsx,.xls"
                               required>
                        <small class="text-muted d-block mt-1">Format: CSV, XLS, atau XLSX. Maksimal 5MB.</small>
                    </div>

                    <div class="small text-muted">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Jadwal dengan kombinasi nama &amp; tanggal yang sudah ada akan dilewati otomatis (tidak ditimpa).
                    </div>

                </div>

                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary font-weight-bold shadow-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success font-weight-bold shadow-sm px-4">
                        <i class="fas fa-file-upload mr-1"></i> Import Sekarang
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- ==================== POP-UP MODAL SUKSES IMPORT (BARU) ==================== --}}
@if(session('import_success'))
<div class="modal fade" id="modalImportSukses" tabindex="-1" role="dialog" aria-labelledby="modalImportSuksesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg text-center">

            <div class="modal-body py-5 px-4">

                <div class="mb-3">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>

                <h5 class="font-weight-bold text-dark mb-2">Import Berhasil!</h5>

                <p class="text-muted mb-1">
                    <strong class="text-success">{{ session('import_success')['success_count'] }}</strong>
                    jadwal baru berhasil ditambahkan.
                </p>

                @if(session('import_success')['skipped_count'] > 0)
                    <p class="text-muted small mb-0">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ session('import_success')['skipped_count'] }} baris dilewati karena sudah ada (duplikat nama &amp; tanggal).
                    </p>
                @endif

            </div>

            <div class="modal-footer bg-light border-top justify-content-center">
                <button type="button" class="btn btn-primary font-weight-bold shadow-sm px-4" data-dismiss="modal" style="background-color:#003366; border-color:#003366;">
                    Selesai
                </button>
            </div>

        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    function aturFormUnduh() {
        const tipePeriode = document.getElementById('tipe_periode').value;
        
        const groupMingguan = document.getElementById('group_mingguan');
        const groupBulanan = document.getElementById('group_bulanan');
        
        const tglMulai = document.getElementById('tanggal_mulai');
        const tglSelesai = document.getElementById('tanggal_selesai');
        const bulanPilihan = document.getElementById('bulan_pilihan');

        if (tipePeriode === 'mingguan') {
            groupMingguan.classList.remove('d-none');
            groupBulanan.classList.add('d-none');
            
            tglMulai.required = true;
            tglSelesai.required = true;
            bulanPilihan.required = false;
        } else if (tipePeriode === 'bulanan') {
            groupMingguan.classList.add('d-none');
            groupBulanan.classList.remove('d-none');
            
            tglMulai.required = false;
            tglSelesai.required = false;
            bulanPilihan.required = true;
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        aturFormUnduh();

        // BARU: jika import berhasil, otomatis tampilkan pop-up sukses
        @if(session('import_success'))
            $('#modalImportSukses').modal('show');
        @endif
    });
</script>
@endpush