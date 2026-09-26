<!DOCTYPE html>
<html>
<head>
    <title>Laporan Monitoring Peralatan - {{ $bulan }} {{ $tahun }}</title>
    <style>
        @page { size: A4 landscape; margin: 30px; }
        body { font-family: Arial, sans-serif; font-size: 9pt; }
        .kop-right { float: right; text-align: left; font-size: 7pt; width: 50%; margin-bottom: 20px; }
        .clear { clear: both; }
        .title { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 5px; }
        .subtitle { text-align: center; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; }
        table.border th, table.border td { border: 1px solid black; padding: 5px; }
        .bg-gray { background-color: #f2f2f2; }
        
        .footer-table { width: 100%; margin-top: 30px; }
        .footer-table td { text-align: center; width: 50%; }
        .footer-table{
    margin-top:30px;
    page-break-inside: avoid;
}
        .spacer { height: 80px; position: relative; vertical-align: middle; }
        
        .dokumentasi-grid { width: 100%; margin-top: 20px; }
        .dokumentasi-item { width: 31%; display: inline-block; vertical-align: top; margin: 1%; text-align: center; border: 1px solid #ddd; padding: 5px; }
        .foto-alat{
    width:180px;
    height:120px;
}        .caption { font-size: 8pt; font-weight: bold; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="kop-right">
        LAMPIRAN II PERATURAN KEPALA BADAN METEOROLOGI, KLIMATOLOGI, DAN GEOFISIKA<br>
        NOMOR 7 TAHUN 2014 TENTANG STANDAR TEKNIS DAN OPERASIONAL<br>
        PEMELIHARAAN PERALATAN METEOROLOGI, KLIMATOLOGI, DAN GEOFISIKA
    </div>
    <div class="clear"></div>

    <div style="text-align: center; font-weight: bold; margin-bottom: 20px;">KOP SURAT UPT</div>

    <div class="title">LAPORAN MONITORING PERALATAN</div>
    <div class="subtitle">Bulan: {{ $bulan }} {{ $tahun }}</div>

    <p><strong>I. Identitas</strong></p>
    <table style="width: 40%; margin-bottom: 15px;">
        <tr><td width="40%">Lokasi/Stasiun</td><td>: Maritim Semarang</td></tr>
        <tr><td>Petugas/Teknis</td><td>: {{ $data->first()->petugas ?? auth()->user()->name }}</td></tr>
        <tr><td>Waktu Pelaksanaan</td><td>: {{ $bulan }} {{ $tahun }}</td></tr>
    </table>

    <p><strong>II. Kondisi Peralatan MKG yang dioperasikan</strong></p>
    <table class="border">
        <thead class="bg-gray">
            <tr>
                <th rowspan="2" width="5%">No</th>
                <th rowspan="2" width="30%">Nama Peralatan</th>
                <th colspan="3">Kondisi</th>
                <th rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th width="8%">RR</th>
                <th width="8%">RB</th>
                <th width="8%">Baik</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data->unique('alat_id') as $item)
            @php
                $kondisi = strtoupper(
                    $item->kondisi_akhir
                    ?? $item->kondisi_fisik
                    ?? 'BAIK'
                );
            @endphp
            <tr>
                <td align="center">{{ $loop->iteration }}</td>
                <td>{{ $item->nama_alat ?? $item->nama_alat ?? 'Alat' }}</td>
                <td align="center">{{ ($kondisi == 'RR' || $kondisi == 'RUSAK RINGAN') ? 'V' : '' }}</td>
                <td align="center">{{ ($kondisi == 'RB' || $kondisi == 'RUSAK BERAT') ? 'V' : '' }}</td>
                <td align="center">{{ ($kondisi == 'BAIK') ? 'V' : '' }}</td>
                <td>
                    @if($kondisi == 'BAIK')
                        Baik
                    @elseif($kondisi == 'RUSAK RINGAN' || $kondisi == 'RR')
                        {{ $item->catatan
                            ?? $item->catatan_khusus
                            ?? 'Rusak Ringan.' }}
                    @elseif($kondisi == 'RUSAK BERAT' || $kondisi == 'RB')
                        {{ $item->catatan
                            ?? $item->catatan_khusus
                            ?? 'Rusak Berat.' }}
                    @else
                        {{ $item->catatan
                            ?? $item->catatan_khusus
                            ?? $item->deskripsi_hasil
                            ?? '-' }}
                    @endif
                </td>         
            </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>III. Catatan Khusus</strong></p>
    <div style="border: 1px solid black; padding: 10px; min-height: 40px; margin-bottom: 20px;">
        @php
    $catatan = $data
        ->pluck('catatan')
        ->filter()
        ->implode('; ');
    @endphp

    {{ $catatan ?: 'Seluruh peralatan dalam kondisi operasional normal.' }}    
    </div>

    <p><strong>IV. Dokumentasi Peralatan</strong></p>
    <div class="dokumentasi-grid">
    <table class="border" style="margin-top:10px;">
    <tr>
        @foreach($data->unique('alat_id') as $item)

            @if($item->dokumen)

                @php
                    $pathDoc = storage_path('app/public/'.$item->dokumen);
                @endphp

                <td width="33%" align="center">

                    @if(file_exists($pathDoc))

                        <img src="data:image/{{ pathinfo($pathDoc, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents($pathDoc)) }}"
                             style="width:180px;height:130px;object-fit:cover;">

                    @else

                        Tidak ada foto

                    @endif

                    <br>

                    <strong>{{ $item->nama_alat ?? '-' }}</strong>
                </td>

            @endif

        @endforeach
    </tr>
</table>
    </div>

    <table class="footer-table">
        <tr>
            <td>
                Mengetahui,<br>Ka. Stasiun<br>
                <div class="spacer"></div>
                <strong>( __________________________ )</strong><br>NIP. .................................
            </td>
            <td>
                Semarang, {{ now()->format('d F Y') }}<br>Petugas Pemeliharaan<br>
                <div class="spacer">
                    @php
                        // Nama file harus sama persis dengan Nama User di Database (misal: Hajirin Arafat.png)
                        $namaUser = auth()->user()->name;
                        $pathTTD = public_path('assets/dist/img/TTD/' . $namaUser . '.png');
                    @endphp

                    @if(file_exists($pathTTD))
                        <img src="{{ $pathTTD }}" style="height: 70px; width: auto;">
                    @else
                        <div style="height:70px;"></div> {{-- Spacer jika TTD tidak ada --}}
                    @endif
                </div>
                <strong>( {{ auth()->user()->name }} )</strong><br>
                NIP. {{ auth()->user()->nip ?? '.................................' }}
            </td>
        </tr>
    </table>
</body>
</html>