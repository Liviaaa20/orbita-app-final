@extends('layouts.master')
@php
    // Gunakan trim() untuk menghindari error akibat spasi berlebih pada database
    $userRole = strtolower(trim(Auth::user()->role->nama_role ?? ''));
    $isAdmin = ($userRole == 'admin');
    $isTeknisi = ($userRole == 'teknisi');
    
    // Role yang HANYA BOLEH LIHAT (tidak boleh klik tombol maintenance/pengecekan)
    $isReadOnly = in_array($userRole, [
        'kepala lapangan', 'kepala_lapangan', 'kalap', 
        'koordinator', 
        'tu', 'tata usaha', 
        'observer', 
        'forecaster', 'forcaster'
    ]);
    
    // Role yang BOLEH INPUT
    $canMaintain = ($isAdmin || $isTeknisi);
@endphp
@section('content')
<style>
    .card { border: none !important; border-radius: 15px !important; box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important; transition: 0.3s; }
    .card:hover { transform: translateY(-5px); }
    .card-header { background: #f8f9fa !important; border-bottom: 1px solid #eee !important; padding: 1rem !important; border-radius: 15px 15px 0 0 !important; }
    .card-title { color: #333; font-weight: bold; font-size: 1rem; margin-bottom: 0; }
    .bg-light-custom { background-color: #f8f9fa; border-radius: 10px; padding: 10px; margin-bottom: 10px; }
    
    /* Style Box Pengecekan & Maintenance */
    .status-box { border: 1px solid #ddd; border-radius: 12px; padding: 15px; position: relative; overflow: hidden; height: 100%; transition: 0.3s; color: #fff !important; }
    .status-box h6 { font-weight: bold; font-size: 1.1rem; margin-bottom: 5px; color: #fff; }
    .status-box i.main-icon { position: absolute; right: 15px; top: 15px; color: rgba(255,255,255,0.3); font-size: 2.5rem; }
    .footer-link { background: rgba(0,0,0,0.1); display: block; padding: 5px 15px; margin: 15px -15px -15px -15px; border-radius: 0 0 12px 12px; font-size: 0.85rem; color: #fff !important; text-decoration: none; }
    .footer-link:hover { background: rgba(0,0,0,0.2); color: #fff; }

    .bg-status-done { background-color: #28a745 !important; border-color: #28a745 !important; }
    .bg-status-pending { background-color: #dc3545 !important; border-color: #dc3545 !important; }

    /* Status Colors */
    .card-pending { border: 2px solid #dc3545 !important; }
    .card-done { border: 2px solid #28a745 !important; }
    
    .footer-pending { background-color: #f8d7da !important; color: #721c24 !important; border-top: 1px solid #dc3545 !important; }
    .footer-done { background-color: #d4edda !important; color: #155724 !important; border-top: 1px solid #28a745 !important; }

    .icon-pending { color: #dc3545 !important; }
    .icon-done { color: #28a745 !important; }

    /* Container utama kartu */
    .maintenance-card {
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        height: 100%;
        transition: 0.3s;
    }

    .card-main-content {
        padding: 20px;
        position: relative;
        min-height: 120px;
    }

    .alat-name {
        font-size: 1.3rem;
        font-weight: 600;
        color: #333;
        line-height: 1.2;
        max-width: 70%;
    }

    .icon-wrapper {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
    }

    .card-footer-action {
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-decoration: none !important;
        font-weight: bold;
        font-size: 1rem;
    }
</style>


<div class="container-fluid">
    <h4 class="mb-4 font-weight-bold">Dashboard Monitoring Alat BMKG</h4>

    <div class="row">
        {{-- Peta Lokasi Alat --}}
        <div class="col-md-9 mb-4">
            <div class="card h-100">
                <div class="card-header"><h3 class="card-title">Peta Lokasi Alat</h3></div>
                <div class="card-body p-0 overflow-hidden">
                    <iframe src="http://202.90.199.132/aws-new/" width="100%" height="350" style="border:0;"></iframe>
                </div>
            </div>
        </div>

        {{-- Jadwal Dinas --}}
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-header"><h3 class="card-title">Jadwal Dinas Hari Ini</h3></div>
                <div class="card-body">
                    @forelse($jadwalDinas as $jd)
                        <div class="d-flex align-items-center bg-light-custom">
                            <i class="fas fa-user-circle fa-2x mr-2 text-secondary"></i>
                            <div>
                                <strong class="d-block small">{{ $jd->nama }}</strong>
                                <span class="text-muted" style="font-size: 9px;">{{ $jd->shift }} ({{ $jd->jam }})</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted small">Tidak ada jadwal</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Pengecekan Harian dengan Status Warna --}}
<div class="card shadow-sm mb-4" style="border-radius: 12px; border: 1px solid #d1d1d1;">
    {{-- Header Abu-abu sesuai gambar --}}
    <div class="card-header" style="background-color: #e0e0e0; border-radius: 12px 12px 0 0; border-bottom: 1px solid #ccc;">
        <h3 class="card-title font-weight-bold" style="color: #333; font-size: 1.15rem; margin-bottom: 0;">
            Pengecekan Harian Tanggal {{ $today->translatedFormat('d F Y') }}
        </h3>
    </div>
    
    <div class="card-body p-4">
        <div class="row">
        @foreach(['Pagi', 'Siang', 'Malam'] as $s)
    @php
        $dataShift = $statusHarian[$s];
        $isExist   = $dataShift['ada'];
        $isDone    = $dataShift['is_done'];

        // Pengaturan Variabel UI
        if (!$isExist) {
            $accentColor = '#6c757d'; // Abu-abu
            $bgColor     = '#f8f9fa';
            $icon        = 'fa-ellipsis-h';
            $statusLabel = "Belum Inisiasi";
        } elseif (!$isDone) {
            $accentColor = '#e74c3c'; // Merah Modern
            $bgColor     = '#fff5f5';
            $icon        = 'fa-exclamation-circle';
            $statusLabel = "Perlu Pengecekan";
        } else {
            $accentColor = '#27ae60'; // Hijau Modern
            $bgColor     = '#f0fff4';
            $icon        = 'fa-check-double';
            $statusLabel = "Selesai";
        }
    @endphp

    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm border-0" style="border-radius: 16px; overflow: hidden; transition: all 0.3s ease;">
            {{-- Bagian Atas / Header Kartu --}}
            <div class="p-4 d-flex justify-content-between align-items-start" style="background-color: {{ $bgColor }}; flex: 1;">
                <div>
                    <span class="badge mb-2" style="background: {{ $accentColor }}; color: #fff; border-radius: 6px; padding: 5px 10px; font-weight: 600; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;">
                        Shift {{ $s }}
                    </span>
                    <h4 class="font-weight-bold d-block mt-1" style="color: #2d3436; font-size: 1.4rem;">
                        {{ $s }}
                    </h4>
                    <p class="mb-0 small font-weight-bold" style="color: {{ $accentColor }};">
                        <i class="fas {{ $icon }} mr-1"></i> {{ $statusLabel }}
                    </p>
                </div>
                <div class="opacity-25">
                    <i class="fas fa-cloud-sun fa-3x" style="color: {{ $accentColor }}; opacity: 0.15;"></i>
                </div>
            </div>

            {{-- Bagian Bawah / Action --}}
<div class="px-4 py-3 bg-white border-top">
    @if($isExist)
        @if($canMaintain)
            {{-- Role Admin & Teknisi bisa klik --}}
            <a href="{{ route('maintenance.form-master', ['type' => 'harian', 'tanggal' => $today->format('Y-m-d'), 'waktu' => $s]) }}"
               class="btn btn-block d-flex justify-content-between align-items-center px-0" 
               style="color: #2d3436; font-weight: 700; text-decoration: none;">
                <span>{{ $isDone ? 'Lakukan Pengecekan' : 'Lakukan Pengecekan' }}</span>
                <div style="width: 32px; height: 32px; background: {{ $bgColor }}; color: {{ $accentColor }}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-arrow-right fa-sm"></i>
                </div>
            </a>
        @else
            {{-- Role Koordinator, Kepala Lapangan, TU, Observer, Forecaster hanya melihat teks status --}}
            <div class="d-flex justify-content-between align-items-center py-1">
                <span class="text-muted font-weight-bold">
                    <i class="fas {{ $isDone ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i> 
                    {{ $isDone ? 'Sudah Diperiksa' : 'Belum Diperiksa' }}
                </span>
                <i class="fas fa-lock text-muted small" title="Hanya Teknisi/Admin"></i>
            </div>
        @endif
    @else
        <div class="text-muted small py-1">
            <i class="fas fa-info-circle mr-1"></i> Menunggu plotting...
        </div>
    @endif
</div>
        </div>
    </div>
@endforeach
        </div>
    </div>
</div>


{{-- Section Maintenance Mingguan --}}
<div class="card shadow-sm mb-4" style="border-radius: 12px; border: 1px solid #d1d1d1;">
    <div class="card-header" style="background-color: #e0e0e0; border-radius: 12px 12px 0 0; border-bottom: 1px solid #ccc;">
        <h3 class="card-title font-weight-bold" style="color: #333; font-size: 1.15rem; margin-bottom: 0;">
            Maintenance Mingguan - {{ $hariIni }}, {{ $today->translatedFormat('d F Y') }}
        </h3>
    </div>
    
    <div class="card-body p-4">
        <div class="row">
            @forelse($alatMingguan as $kategori)
                @php
                    // Logika Warna mengikuti style Harian kamu
                    if ($kategori->is_done) {
                        $accentColor = '#27ae60'; // Hijau
                        $bgColor     = '#f0fff4';
                        $icon        = 'fa-check-circle';
                        $statusText  = "Selesai";
                    } else {
                        $accentColor = '#e74c3c'; // Merah
                        $bgColor     = '#fff5f5';
                        $icon        = 'fa-exclamation-circle';
                        $statusText  = "Perlu Maintenance";
                    }
                @endphp

                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0" style="border-radius: 16px; overflow: hidden; transition: all 0.3s ease; border: 1px solid {{ $accentColor }}22 !important;">
                        {{-- Bagian Atas --}}
                        <div class="p-4 d-flex justify-content-between align-items-start" style="background-color: {{ $bgColor }}; flex: 1;">
                            <div>
                                <span class="badge mb-2" style="background: {{ $accentColor }}; color: #fff; border-radius: 6px; padding: 5px 10px; font-weight: 600; font-size: 0.7rem; text-transform: uppercase;">
                                    Kategori
                                </span>
                                <h4 class="font-weight-bold d-block mt-1" style="color: #2d3436; font-size: 1.2rem; line-height: 1.3;">
                                    {{ $kategori->nama_kategori }}
                                </h4>
                                <p class="mb-0 small font-weight-bold" style="color: {{ $accentColor }};">
                                    <i class="fas {{ $icon }} mr-1"></i> {{ $statusText }}
                                </p>
                                <small class="text-muted">{{ $kategori->sudah_dicek }} dari {{ $kategori->jumlah_alat }} Alat</small>
                            </div>
                            <div class="opacity-25">
                                <i class="fas fa-tools fa-3x" style="color: {{ $accentColor }}; opacity: 0.15;"></i>
                            </div>
                        </div>

                        {{-- Bagian Bawah (Tombol) --}}
<div class="px-4 py-3 bg-white border-top">
    @if($canMaintain)
        {{-- Role Admin & Teknisi --}}
        <a href="{{ route('maintenance.form-master', [
        'type' => 'mingguan', 
        'tanggal' => $today->format('Y-m-d'), 
        'waktu' => $hariIni,
        'kategori_id' => $kategori->id_kategori 
        ]) }}" 
           class="btn btn-block d-flex justify-content-between align-items-center px-0" 
           style="color: #2d3436; font-weight: 700; text-decoration: none;">
            <span>{{ $kategori->is_done ? 'Lakukan Maintenance' : 'Lakukan Maintenance' }}</span>
            <div style="width: 32px; height: 32px; background: {{ $bgColor }}; color: {{ $accentColor }}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-arrow-right fa-sm"></i>
            </div>
        </a>
    @else
        {{-- Role Read-Only (Koordinator, Kepala Lapangan, dll) --}}
        <div class="d-flex justify-content-between align-items-center py-1">
            <span class="text-muted font-weight-bold">
                <i class="fas {{ $kategori->is_done ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i> 
                {{ $kategori->is_done ? 'Sudah Dimaintenance' : 'Belum Dimaintenance' }}
            </span>
            <i class="fas fa-lock text-muted small" title="Hanya Teknisi/Admin"></i>
        </div>
    @endif
</div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Tidak ada jadwal maintenance mingguan hari ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

    {{-- Grafik Statistik --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Grafik Kondisi Alat</h3>
            <form action="{{ route('dashboard') }}" method="GET" class="m-0">
                <select name="tahun" class="form-control form-control-sm" onchange="this.form.submit()" style="width: 100px;">
                    @foreach($listTahun as $t)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="card-body">
            <canvas id="barChart" style="min-height: 250px; height: 250px; max-width: 100%;"></canvas>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var canvas = document.getElementById('barChart');
        const labels = @json($months);
        const dataBaik = @json($dataNormal);
        const dataRusak = @json($dataRusak);

        if (canvas) {
            var ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Kondisi Baik', backgroundColor: '#28a745', data: dataBaik },
                        { label: 'Kondisi Rusak', backgroundColor: '#dc3545', data: dataRusak }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } } }
                }
            });
        }
    });
</script>
@endpush
@endsection