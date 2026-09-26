<!DOCTYPE html>
<html>
<head>
    <title>Laporan Histori Operasional</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 9pt; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; vertical-align: top; }
        th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 8pt; }
        
        .text-left { text-align: left; }
        .footer { margin-top: 30px; text-align: right; }
        .signature { margin-top: 60px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>BADAN METEOROLOGI, KLIMATOLOGI, DAN GEOFISIKA</h2>
        <h3>STASIUN METEOROLOGI MARITIM KELAS II SEMARANG</h3>
        <p>Laporan Histori Operasional Peralatan Meteorologi</p>
    </div>

    <p><strong>Dicetak pada:</strong> {{ now()->format('d/m/Y H:i') }} WIB</p>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Waktu</th>
                <th width="15%">Jenis Aktivitas</th>
                <th width="15%">Alat / Sistem</th>
                <th width="12%">Lokasi</th>
                <th>Deskripsi Hasil</th>
                <th width="12%">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($item->waktu)->format('d/m/Y H:i') }}</td>
                <td>{{ $item->jenis_aktivitas }}</td>
                <td>{{ $item->alat->nama_alat }}</td>
                <td>{{ $item->lokasi }}</td>
                <td class="text-left">{{ $item->deskripsi_hasil }}</td>
                <td>{{ $item->user->username ?? $item->user->name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Semarang, {{ now()->format('d F Y') }}</p>
        <div class="signature">
            <p><strong>__________________________</strong></p>
            <p>Petugas Operasional</p>
        </div>
    </div>
</body>
</html>