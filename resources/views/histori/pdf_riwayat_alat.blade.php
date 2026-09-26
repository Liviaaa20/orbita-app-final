<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Peralatan -{{ optional($data->alat)->nama_alat ?? 'Tanpa Alat' }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.4; }
        .kop { text-align: center; border-bottom: 2px solid black; padding-bottom: 10px; margin-bottom: 15px; }
        .kop h2 { margin: 0; font-size: 14pt; }
        .title { text-align: center; text-decoration: underline; font-weight: bold; margin-bottom: 10px; }
        
        .sub-title { font-weight: bold; margin-top: 15px; margin-bottom: 5px; display: block; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.border th, table.border td { border: 1px solid black; padding: 5px; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bg-gray { background-color: #f2f2f2; }
        
        .footer { margin-top: 30px; float: right; width: 40%; text-align: center; }
        .spacer { height: 60px; }
    .signature-img {
        width: 100px; /* Atur lebar TTD agar pas */
        height: auto;
        margin-bottom: -15px; /* Menumpuk sedikit di atas garis nama */
        margin-top: -10px;
    }
    </style>
</head>
<body>
    <div class="kop">
        <h2>KOP SURAT UPT</h2>
        <p>STASIUN METEOROLOGI MARITIM KELAS II SEMARANG</p>
    </div>

    <div class="title">RIWAYAT PERALATAN</div>
    <div class="text-center">
        Nama Alat: {{ $data->alat->nama_alat ?? '-' }} <br>
        Tahun Pengadaan: {{ $data->alat->tahun_pengadaan ?? '-' }}
    </div>

    <span class="sub-title">I. Identitas</span>
    <table class="border">
        <tr class="bg-gray">
            <th width="5%">No</th>
            <th width="35%">Uraian</th>
            <th>Data Teknis</th>
        </tr>
        <tr><td class="text-center">1</td><td>Nama Pemilik Alat</td><td>BMKG Semarang</td></tr>
        <tr><td class="text-center">2</td><td>Lokasi Penempatan Alat</td><td>{{ $data->lokasi ?? optional($data->alat)->lokasi ?? '-' }}</td></tr>
        <tr><td class="text-center">3</td><td>Merk/Type/Series</td><td>{{ $data->alat->merk_type ?? '-' }}</td></tr>
        <tr><td class="text-center">4</td><td>Nomor Serial (S/N)</td><td>{{ $data->alat->nomor_seri ?? '-' }}</td></tr>
        <tr><td class="text-center">5</td><td>Rentang Ukur</td><td>{{ $data->alat->rentang_ukur ?? '-' }}</td></tr>
        <tr><td class="text-center">6</td><td>Resolusi/Skala Terkecil</td><td>{{ $data->alat->resolusi ?? '-' }}</td></tr>
        <tr><td class="text-center">7</td><td>Akurasi</td><td>{{ $data->alat->akurasi ?? '-' }}</td></tr>
    </table>

    <span class="sub-title">II. Riwayat Perbaikan / Maintenance</span>
    <table class="border">
        <thead class="bg-gray text-center">
            <tr>
                <th>Tanggal</th>
                <th>Uraian Kerusakan</th>
                <th>Tindakan Perbaikan</th>
                <th>Hasil</th>
            </tr>
        </thead>
        <tbody>

@forelse($riwayatPerbaikan as $item)

<tr>

    <td class="text-center">
        {{ optional($item->tgl_selesai)->format('d/m/Y') ?? '-' }}
    </td>

    <td>
        {{ $item->keterangan }}
    </td>

    <td>
        {{ $item->catatan_teknisi ?? $item->catatan ?? '-' }}
    </td>

    <td class="text-center">

        @switch($item->status)
            @case('selesai')
                Selesai
                @break

            @case('onproses')
                Sedang Dikerjakan
                @break

            @default
                Menunggu
        @endswitch

    </td>

</tr>

@empty

<tr>

    <td colspan="4" class="text-center">
        Belum ada riwayat perbaikan.
    </td>

</tr>

@endforelse

</tbody>
    </table>

    @if(($data->jenis_aktivitas ?? '') == 'Kalibrasi')
        <span class="sub-title">III. Riwayat Kalibrasi</span>
    <table class="border">
        <thead class="bg-gray text-center">
            <tr>
                <th>Tanggal</th>
                <th>Nilai Koreksi</th>
                <th>Ketidakpastian</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            <tr>
            <td class="text-center">
    @php $tglKalibrasi = $data->tanggal ?? $data->waktu; @endphp
    {{ strtotime($tglKalibrasi) ? \Carbon\Carbon::parse($tglKalibrasi)->format('d/m/Y') : $tglKalibrasi }}
</td>                <td class="text-center">{{ $data->nilai_koreksi ?? '-' }}</td>
                <td class="text-center">{{ $data->nilai_ketidakpastian ?? '-' }}</td>
                <td class="text-center">{{ $data->user->name }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    <span class="sub-title">IV. Catatan Khusus</span>
    <div style="border:1px solid black; padding:10px; min-height:50px;">
    @if(in_array($data->jenis_aktivitas ?? '', ['Maintenance Harian','Maintenance Mingguan']))
        {{ $data->catatan ?? 'Tidak ada catatan.' }}
    @else
        {{ $data->catatan_khusus ?? $data->deskripsi_hasil ?? '-' }}
    @endif
</div>

<div class="footer">
    <p>Semarang, {{ now()->translatedFormat('d F Y') }}</p>
    <p><strong>Petugas Teknis</strong></p>

    <div class="spacer">
        @php
            $namaUser = optional($data->user)->name;
            $pathTTD = public_path('assets/dist/img/TTD/' . $namaUser . '.png');
        @endphp

        @if($namaUser && file_exists($pathTTD))
            <img src="{{ $pathTTD }}" class="signature-img">
        @else
            <br><br>
        @endif
    </div>

    <p style="margin-top:0;">
        <strong>( {{ optional($data->user)->name ?? '-' }} )</strong><br>
        NIP. {{ optional($data->user)->nip ?? '.................................' }}
    </p>
</div>
</body>
</html>