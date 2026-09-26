<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ strtoupper($logbook->jenis_logbook) }} - {{ $bulanCarbon->isoFormat('MMMM YYYY') }}</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family:'DejaVu Sans', sans-serif;
            font-size:7pt;
            color:#111;
        }

        .page{
            width:100%;
            padding:8mm 6mm 6mm 6mm;
        }

        /* ======================================================
           HEADER
        ====================================================== */

        .header-table{
            width:100%;
            border-collapse:collapse;
            margin-bottom:6px;
        }

        .header-table td{
            vertical-align:middle;
            padding:2px;
        }

        .header-logo{
            width:60px;
            text-align:center;
        }

        .header-logo img{
            width:45px;
        }

        .header-title{
            text-align:center;
        }

        .judul-utama{
            font-size:11pt;
            font-weight:bold;
            text-transform:uppercase;
        }

        .sub-judul{
            font-size:8pt;
            margin-top:2px;
        }

        .meta-info{
            font-size:7pt;
            margin-top:2px;
            color:#444;
        }

        .header-kode{
            width:75px;
            text-align:center;
            font-size:6pt;
            color:#555;
        }

        .divider-header{
            border:none;
            border-top:2px solid #000;
            margin:5px 0 7px 0;
        }

        /* ======================================================
           RINGKASAN
        ====================================================== */

        .ringkasan-table{
            width:100%;
            border-collapse:collapse;
            margin-bottom:8px;
            font-size:7pt;
        }

        .ringkasan-table td{
            border:1px solid #ccc;
            padding:4px 6px;
        }

        .label-col{
            background:#f5f5f5;
            font-weight:bold;
            width:25%;
        }

        /* ======================================================
           TABEL LOGBOOK
        ====================================================== */

        .tabel-logbook{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
            word-wrap:break-word;
            font-size:6pt;
        }

        .tabel-logbook th{
            background:#f0f0f0;
            border:1px solid #999;
            padding:2px;
            text-align:center;
            font-weight:bold;
            text-transform:uppercase;
            font-size:5.8pt;
            vertical-align:middle;
            overflow:hidden;
        }

        .tabel-logbook td{
            border:1px solid #bbb;
            padding:2px;
            text-align:center;
            vertical-align:middle;
            overflow:hidden;
        }

        .col-tgl{
            width:22px;
            font-weight:bold;
            background:#fafafa;
        }

        .col-ket{
            text-align:left !important;
            font-size:5.8pt;
            padding-left:3px !important;
        }

        .col-teknisi{
            font-size:5.8pt;
        }

        .row-kosong td{
            background:#fafafa;
            color:#aaa;
        }

        /* ======================================================
           STATUS
        ====================================================== */

        .status-baik{
            color:#155724;
            font-weight:bold;
        }

        .status-rusak-ringan{
            color:#856404;
            font-weight:bold;
        }

        .status-rusak-berat{
            color:#721c24;
            font-weight:bold;
        }

        .status-off{
            color:#444;
            font-weight:bold;
        }

        .status-na{
            color:#aaa;
            font-size:5pt;
        }

        /* ======================================================
           LEGENDA
        ====================================================== */

        .legenda{
            margin-top:5px;
            border:1px solid #ddd;
            background:#fafafa;
            padding:4px 6px;
            font-size:6pt;
            color:#555;
        }

        /* ======================================================
           TTD
        ====================================================== */

        .section-paraf{
            margin-top:10px;
            width:100%;
        }

        .paraf-table{
            width:100%;
            border-collapse:collapse;
        }

        .paraf-table td{
            width:33.33%;
            border:1px solid #ccc;
            text-align:center;
            vertical-align:top;
            padding:6px;
        }

        .paraf-label{
            font-size:6.5pt;
            font-weight:bold;
            text-transform:uppercase;
        }

        .paraf-tanggal{
            font-size:6pt;
            color:#555;
            margin-top:2px;
            margin-bottom:3px;
        }

        .paraf-img-wrapper{
            height:55px;
            width:120px;
            margin:0 auto 4px auto;
            text-align:center;
        }

        .paraf-img-wrapper img{
            display:block;
            width:auto;
            height:50px;
            margin:0 auto;
        }

        .paraf-kosong-line{
            height:45px;
            display:block;
        }

        .paraf-garis{
            border-top:1px solid #333;
            margin-top:3px;
            padding-top:3px;
            font-size:7pt;
            font-weight:bold;
        }

        .paraf-nip{
            font-size:6pt;
            color:#555;
        }

        .paraf-ditolak{
            font-size:7pt;
            color:red;
            font-weight:bold;
            padding:10px 0;
        }

        /* ======================================================
           FOOTER
        ====================================================== */

        .footer-doc{
            margin-top:8px;
            border-top:1px solid #ddd;
            padding-top:4px;
            text-align:center;
            font-size:5.8pt;
            color:#777;
        }
    </style>
</head>

<body>

<div class="page">

    {{-- HEADER --}}
    <table class="header-table">
        <tr>

            <td class="header-logo">
                @php
                    $logo = base64_encode(file_get_contents(public_path('assets/dist/img/logo.png')));
                @endphp
                <img src="data:image/png;base64,{{ $logo }}" width="45">
            </td>

            <td class="header-title">
                <div class="judul-utama">
                    {{ $logbook->jenis_logbook }}
                </div>

                <div class="sub-judul">
                    {{ strtoupper($logbook->jenis_alat) }}
                </div>

                <div class="meta-info">
                    {{ strtoupper($logbook->lokasi_tempat) }}<br>

                    Periode:
                    {{ strtoupper($logbook->periode_tersedia) }}

                    &nbsp;|&nbsp;

                    Bulan:
                    {{ strtoupper($bulanCarbon->isoFormat('MMMM YYYY')) }}
                </div>
            </td>

            <td class="header-kode">

                @if($logbook->subKategori)
                    <div style="font-weight:bold;">
                        {{ $logbook->subKategori->nama_sub_kategori }}
                    </div>
                @endif

                <div style="margin-top:3px;">
                    Dicetak:
                    {{ now()->isoFormat('D MMM YYYY HH:mm') }}
                </div>
            </td>

        </tr>
    </table>

    <hr class="divider-header">

    {{-- RINGKASAN --}}
    <table class="ringkasan-table">
        <tr>
            <td class="label-col">Sub Kategori</td>
            <td>{{ $logbook->subKategori->nama_sub_kategori ?? '-' }}</td>

            <td class="label-col">Jumlah Alat</td>
            <td>{{ $alats->count() }} alat</td>
        </tr>

        <tr>
            <td class="label-col">Hari Terisi</td>
            <td>{{ $jumlahTerisi }} dari {{ $jumlahHari }}</td>

            <td class="label-col">Status</td>
            <td>
                <strong>{{ $logbook->getLabelStatus() }}</strong>
            </td>
        </tr>
    </table>

    {{-- TABEL --}}
    @if(!$alats->isEmpty())

    <table class="tabel-logbook">

        <thead>
            <tr>

                <th style="width:22px;">
                    TGL
                </th>

                @foreach($alats as $alat)

                    <th style="width:55px;">
                        {{ strtoupper($alat->nama_alat) }}

                        <br>

                        <span style="font-size:5pt; font-weight:normal;">
                            {{ $alat->nomor_seri }}
                        </span>
                    </th>

                @endforeach

                <th style="width:90px;">
                    KETERANGAN
                </th>

                <th style="width:55px;">
                    TEKNISI
                </th>

            </tr>
        </thead>

        <tbody>

        @for($hari = 1; $hari <= $jumlahHari; $hari++)

            @php
                $dataHari = $dataHarian[$hari] ?? [];
                $adaData = !empty($dataHari);
            @endphp

            <tr class="{{ !$adaData ? 'row-kosong' : '' }}">

                {{-- TANGGAL --}}
                <td class="col-tgl">
                    {{ $hari }}
                </td>

                {{-- ALAT --}}
                @foreach($alats as $alat)

                    @php
                        $kondisi = $dataHari[$alat->id] ?? null;
                    @endphp

                    <td>

                        @if($kondisi)

                            @php
                                $labelKondisi = App\Models\Logbook::getLabelKondisi($kondisi);

                                $cssKondisi = match($kondisi) {
                                    'baik' => 'status-baik',
                                    'rusak_ringan' => 'status-rusak-ringan',
                                    'rusak_berat' => 'status-rusak-berat',
                                    'off' => 'status-off',
                                    default => ''
                                };
                            @endphp

                            <span class="{{ $cssKondisi }}">
                                {{ $labelKondisi }}
                            </span>

                        @else

                            <span class="status-na">
                                #N/A
                            </span>

                        @endif

                    </td>

                @endforeach

                {{-- KETERANGAN --}}
                <td class="col-ket">
                    {{ $keteranganHarian[$hari] ?? '' }}
                </td>

                {{-- TEKNISI --}}
                <td class="col-teknisi">
                    {{ $teknisiHarian[$hari] ?? '' }}
                </td>

            </tr>

        @endfor

        </tbody>

    </table>

    <div class="legenda">
        <strong>Keterangan Status:</strong>

        BAIK |
        RUSAK RINGAN |
        RUSAK BERAT |
        OFF |
        #N/A = Belum ada data
    </div>

    @endif

{{-- TTD --}}
<div class="section-paraf">

    <table class="paraf-table">
        <tr>

            {{-- DIBUAT OLEH --}}
            <td>

                <div class="paraf-label">
                    Dibuat Oleh
                </div>

                <div class="paraf-tanggal">
                    {{ $logbook->created_at?->isoFormat('D MMMM YYYY') }}
                </div>

                @php
                $ttd = base64_encode(file_get_contents(public_path('assets/dist/img/TTD/Triyono.png')));
                @endphp

               <div class="paraf-img-wrapper">
                    <img src="data:image/png;base64,{{ $ttd }}" height="45">
                </div>

                <div class="paraf-garis">
                    Triyono
                </div>

                <div class="paraf-nip">
                    NIP. {{ $logbook->createdBy->nip ?? '-' }}
                </div>

            </td>

            {{-- KEPALA KELOMPOK --}}
            <td>

                <div class="paraf-label">
                    Mengetahui, Kepala Kelompok
                </div>

                <div class="paraf-tanggal">
                    {{ $logbook->approved_kapok_at?->isoFormat('D MMMM YYYY') }}
                </div>

                @php
                    $ttd = base64_encode(file_get_contents(public_path('assets/dist/img/TTD/joko.png')));
                @endphp

                <div class="paraf-img-wrapper">
                    <img src="data:image/png;base64,{{ $ttd }}" height="45">
                </div>

                <div class="paraf-garis">
                    {{ $logbook->approvedKapokOleh->name ?? 'Joko' }}
                </div>

                <div class="paraf-nip">
                    NIP. {{ $logbook->approvedKapokOleh->nip ?? '-' }}
                </div>

            </td>

            {{-- KOORDINATOR --}}
            <td>

                <div class="paraf-label">
                    Menyetujui, Koordinator
                </div>

                <div class="paraf-tanggal">
                    &nbsp;
                </div>

                <div class="paraf-img-wrapper">
                    {{-- Kosong --}}
                </div>

                <span class="paraf-kosong-line"></span>

                <div class="paraf-garis">
                    ( __________________ )
                </div>

                <div class="paraf-nip">
                    NIP.
                </div>

            </td>

        </tr>
    </table>

</div>

    {{-- FOOTER --}}
    <div class="footer-doc">

        Dokumen ini digenerate otomatis oleh sistem ORBITA —
        {{ now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB

        &nbsp;|&nbsp;

        Status:
        <strong>
            {{ strtoupper($logbook->getLabelStatus()) }}
        </strong>

    </div>

</div>

</body>
</html>