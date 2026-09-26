<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Jadwal Dinas - ORBITA</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body{
            background:#fff;
            color:#000;
            font-family:'Arial',sans-serif;
        }

        .kop-surat{
            border-bottom:2px solid #000;
            padding-bottom:12px;
            margin-bottom:25px;
        }

        /* =========================
           MINGGUAN
        ==========================*/
        .table-jadwal th{
            background-color:#f8f9fa !important;
            color:#333 !important;
            vertical-align:middle !important;
            font-weight:bold;
            font-size:0.85rem;
        }

        .table-jadwal td{
            vertical-align:top !important;
            padding:10px 8px !important;
            height:100px;
            width:14.28%;
        }

        .tanggal-angka{
            font-size:1rem;
            font-weight:bold;
            color:#495057;
            margin-bottom:6px;
            display:block;
            text-align:right;
        }

        .box-petugas{
            background-color:#e9ecef !important;
            border-left:3px solid #007bff;
            padding:4px 6px;
            border-radius:4px;
            font-size:0.8rem;
            text-align:left;
            margin-bottom:4px;
            font-weight:bold;
            color:#212529;
        }

        .badge-shift{
            font-size:0.7rem;
            font-weight:bold;
            float:right;
            margin-top:2px;
        }

        /* =========================
           BULANAN
        ==========================*/
        .table-bulanan{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
            font-size:11px;
        }

        .table-bulanan th,
        .table-bulanan td{
            border:1px solid #dee2e6;
            text-align:center;
            padding:6px 4px;
        }

        .table-bulanan thead th{
            background:#f8f9fa;
            font-weight:bold;
        }

        .table-bulanan .nama-col{
            width:210px;
            text-align:left;
            font-weight:bold;
            padding-left:10px;
        }

        .hari-text{
            font-size:10px;
            color:#6c757d;
            font-weight:normal;
        }

        .shift-cell{
            font-weight:bold;
            color:#212529;
            height:42px;
            vertical-align:middle;
        }

        /* =========================
           KETERANGAN
        ==========================*/
        .ket-item{
            font-size:0.75rem;
            color:#333;
            margin-bottom:5px;
        }

        .ket-badge{
            display:inline-block;
            width:32px;
            text-align:center;
            font-weight:bold;
            background:#e9ecef;
            border:1px solid #ced4da;
            border-radius:4px;
            margin-right:5px;
            font-size:0.7rem;
            padding:1px 0;
        }

        /* =========================
           PRINT
        ==========================*/
        @media print{
            .no-print,
            .d-print-none{
                display:none !important;
            }

            body{
                -webkit-print-color-adjust:exact;
                print-color-adjust:exact;
            }

            .table-jadwal th{
                background-color:#f8f9fa !important;
            }

            .box-petugas{
                background-color:#e9ecef !important;
            }

            @page{
                size:landscape;
                margin:1cm;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid mt-3">

    {{-- ALERT PRINT --}}
    <div class="d-print-none alert alert-light border d-flex justify-content-between align-items-center mb-4 shadow-sm no-print">
        <span>
            <i class="fas fa-info-circle text-info mr-1"></i>
            Dokumen siap cetak dalam mode <strong>Landscape</strong>.
        </span>

        <button onclick="window.print()" class="btn btn-primary btn-sm font-weight-bold shadow-sm">
            <i class="fas fa-print mr-1"></i> Cetak Sekarang
        </button>
    </div>

    {{-- HEADER --}}
    <div class="kop-surat text-center">
        <h4 class="font-weight-bold text-uppercase mb-1">
            JADWAL DINAS PETUGAS OPERASIONAL
        </h4>

        <h5 class="font-weight-bold text-uppercase mb-1">
            SISTEM MONITORING ALAT BMKG (ORBITA)
        </h5>

        <p class="text-muted m-0 font-weight-bold text-uppercase" style="font-size:0.9rem;">
            {{ $labelPeriode }}
        </p>
    </div>

    {{-- =========================================================
         MODE CETAK MINGGUAN
    ==========================================================--}}
    @if(request('tipe_periode') == 'mingguan')

        @php
            $jadwalPerMinggu = $jadwal->groupBy(function($item) {
                return \Carbon\Carbon::parse($item->tanggal)->format('W');
            });
        @endphp

        <table class="table table-bordered table-jadwal mb-4">
            <thead>
                <tr>
                    <th>SENIN</th>
                    <th>SELASA</th>
                    <th>RABU</th>
                    <th>KAMIS</th>
                    <th>JUMAT</th>
                    <th>SABTU</th>
                    <th>MINGGU</th>
                </tr>
            </thead>

            <tbody>
                @forelse($jadwalPerMinggu as $minggu => $daftarJadwalHari)
                    <tr>

                        @for($i = 1; $i <= 7; $i++)

                            @php
                                $jadwalHariIni = $daftarJadwalHari->filter(function($item) use ($i) {
                                    return \Carbon\Carbon::parse($item->tanggal)->dayOfWeekIso == $i;
                                });

                                $infoHari = $jadwalHariIni->first();
                            @endphp

                            <td>
                                @if($infoHari)

                                    <span class="tanggal-angka">
                                        {{ \Carbon\Carbon::parse($infoHari->tanggal)->format('d') }}
                                    </span>

                                    @foreach($jadwalHariIni as $j)
                                        <div class="box-petugas">

                                            {{ $j->nama_petugas ?? $j->nama }}

                                            @if(!empty($j->keterangan_shift) || !empty($j->shift))
                                                <span class="badge badge-dark badge-shift">
                                                    {{ $j->keterangan_shift ?? $j->shift }}
                                                </span>
                                            @endif

                                        </div>
                                    @endforeach

                                @else

                                    <span class="tanggal-angka text-muted small">-</span>

                                @endif
                            </td>

                        @endfor

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-calendar-times d-block mb-2" style="font-size:2rem;"></i>
                            Tidak ada data jadwal dinas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    @else

    {{-- =========================================================
         MODE CETAK BULANAN
    ==========================================================--}}

        @php
            $tanggalPertama = \Carbon\Carbon::parse($jadwal->min('tanggal'));
            $jumlahHari = $tanggalPertama->daysInMonth;

            $groupedJadwal = $jadwal->groupBy('nama');
        @endphp

        <div class="table-responsive mb-4">
            <table class="table-bulanan">

                <thead>
                    <tr>
                        <th rowspan="2" style="width:50px;">No</th>
                        <th rowspan="2" class="nama-col">Nama</th>

                        @for($d = 1; $d <= $jumlahHari; $d++)
                            <th>{{ $d }}</th>
                        @endfor
                    </tr>

                    <tr>

                        @for($d = 1; $d <= $jumlahHari; $d++)

                            @php
                                $tanggalLoop = \Carbon\Carbon::create(
                                    $tanggalPertama->year,
                                    $tanggalPertama->month,
                                    $d
                                );

                                $hari = substr($tanggalLoop->isoFormat('dddd'),0,1);
                            @endphp

                            <th class="hari-text">
                                {{ $hari }}
                            </th>

                        @endfor

                    </tr>
                </thead>

                <tbody>

                    @forelse($groupedJadwal as $namaPetugas => $jadwalPetugas)

                        <tr>

                            <td>{{ $loop->iteration }}</td>

                            <td class="nama-col">
                                {{ $namaPetugas }}
                            </td>

                            @for($d = 1; $d <= $jumlahHari; $d++)

                                @php
                                    $tanggalCari =
                                        $tanggalPertama->format('Y-m-') .
                                        str_pad($d,2,'0',STR_PAD_LEFT);

                                    $jadwalHari =
                                        $jadwalPetugas->firstWhere('tanggal',$tanggalCari);
                                @endphp

                                <td class="shift-cell">
                                    {{ $jadwalHari->shift ?? '' }}
                                </td>

                            @endfor

                        </tr>

                    @empty

                        <tr>
                            <td colspan="{{ $jumlahHari + 2 }}" class="text-center py-5 text-muted">
                                <i class="fas fa-calendar-times d-block mb-2" style="font-size:2rem;"></i>
                                Tidak ada data jadwal dinas.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>
        </div>

    @endif

    {{-- KETERANGAN --}}
    <div class="card bg-light border p-3 mb-4 rounded-lg">

        <h6 class="font-weight-bold text-uppercase small text-muted mb-2">
            Keterangan Shift / Dinas:
        </h6>

        <div class="row">

            <div class="col-md-6">

                <div class="ket-item">
                    <span class="ket-badge">R</span>
                    REGULER NON SHIFT DARI JAM 07:30 - 16:00 WIB
                </div>

                <div class="ket-item">
                    <span class="ket-badge">P</span>
                    DINAS TEKNISI DARI JAM 07:30 - 14:00 WIB
                </div>

                <div class="ket-item">
                    <span class="ket-badge">S</span>
                    DINAS TEKNISI DARI JAM 13:30 - 20:00 WIB
                </div>

                <div class="ket-item">
                    <span class="ket-badge">PS</span>
                    DINAS TEKNISI DARI JAM 07:30 - 20:00 WIB
                </div>

            </div>

            <div class="col-md-6">

                <div class="ket-item">
                    <span class="ket-badge">PS1</span>
                    DINAS TEKNISI DARI JAM 07:30 - 20:00 WIB
                </div>

                <div class="ket-item">
                    <span class="ket-badge">TP</span>
                    DINAS TEKNISI DARI JAM 07:30 - 16:00 WIB
                </div>

                <div class="ket-item">
                    <span class="ket-badge">TP1</span>
                    DINAS TEKNISI DARI JAM 07:30 - 16:00 WIB
                </div>

                <div class="ket-item">
                    <span class="ket-badge">S2</span>
                    DINAS TEKNISI DARI JAM 10:30 - 18:30 WIB
                </div>

            </div>

        </div>
    </div>

    {{-- TTD --}}
    <div class="row mt-4 pt-2">

        <div class="col-8"></div>

        <div class="col-4 text-center">

            <p class="mb-5">
                Kepala kelompok,
            </p>

            <br><br>

            <p class="font-weight-bold m-0">
                _______________________
            </p>

            <p class="text-muted small">
                NIP. .............................
            </p>

        </div>

    </div>

</div>

<script>
    window.onload = function () {
        setTimeout(function () {
            window.print();
        }, 500);
    }
</script>

</body>
</html>