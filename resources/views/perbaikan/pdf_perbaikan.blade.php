<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Perbaikan {{ $perbaikan->no_tiket }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; }

        .header {
            text-align: center;
            border-bottom: 2px solid #003366;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .header h2 { font-size: 15px; color: #003366; text-transform: uppercase; letter-spacing: 1px; }
        .header p { font-size: 10px; color: #555; margin-top: 3px; }

        .tiket-badge {
            text-align: center;
            margin-bottom: 10px;
        }
        .tiket-badge span {
            background: #003366;
            color: #fff;
            padding: 4px 16px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        table.info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.info td {
            padding: 5px 8px;
            border: 1px solid #ccc;
            vertical-align: top;
        }
        table.info td:first-child {
            width: 35%;
            font-weight: bold;
            background: #f0f4f8;
            color: #003366;
        }

        .section-title {
            background: #003366;
            color: #fff;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 5px;
            margin-top: 8px;
        }

        .keterangan-box {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px 14px;
            background: #fafafa;
            min-height: 35px;
            margin-bottom: 8px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 14px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 12px;
        }
        .status-selesai  { background: #d4edda; color: #155724; }
        .status-onproses { background: #fff3cd; color: #856404; }
        .status-pending  { background: #e2e3e5; color: #41464b; }

        /* FOTO SEBELUM / SESUDAH - dua kolom berdampingan */
        .foto-grid {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0 10px;
            page-break-inside: avoid;
        }
        .foto-grid td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 3px;
        }
        .foto-caption {
            font-size: 11px;
            font-weight: bold;
            color: #003366;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .foto-box {
            border: 1px solid #ccc;
            border-radius: 6px;
            background: #fafafa;
            padding: 8px;
            height: 190px;
        }
        .foto-box img {
            max-width: 180px;
            max-height: 120px;
        }
        .foto-empty {
            color: #999;
            font-size: 11px;
            line-height: 170px;
        }

        .footer {
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            font-size: 10px;
            color: #888;
            text-align: center;
        }

        .ttd-section {
            margin-top: 10px;
            width: 100%;
            page-break-inside: avoid;
        }
        .ttd-section table {
            width: 100%;
        }
        .ttd-section td {
            text-align: center;
            width: 33%;
            padding-top: 8px;
        }
        .ttd-line {
            border-top: 1px solid #333;
            margin: 6px auto 4px;
            width: 110px;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <h2>Stasiun Meteorologi Maritim Semarang</h2>
        <p>Laporan Permintaan Perbaikan Peralatan</p>
    </div>

    {{-- NOMOR TIKET --}}
    <div class="tiket-badge">
        <span>{{ $perbaikan->no_tiket }}</span>
    </div>

    {{-- INFORMASI TIKET --}}
    <div class="section-title">Informasi Tiket</div>
    <table class="info">
        <tr>
            <td>Tanggal Permintaan</td>
            <td>{{ $perbaikan->tgl_permintaan ? \Carbon\Carbon::parse($perbaikan->tgl_permintaan)->translatedFormat('d F Y, H:i') : '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal Diterima</td>
            <td>
                @if($perbaikan->tgl_diterima)
                    {{ \Carbon\Carbon::parse($perbaikan->tgl_diterima)->translatedFormat('d F Y, H:i') }}
                @else
                    <span style="color:#888;">Belum diterima</span>
                @endif
            </td>
        </tr>
        <tr>
            <td>Tanggal Selesai</td>
            <td>
                @if($perbaikan->tgl_selesai)
                    {{ \Carbon\Carbon::parse($perbaikan->tgl_selesai)->translatedFormat('d F Y, H:i') }}
                @else
                    <span style="color:#888;">Belum selesai</span>
                @endif
            </td>
        </tr>
        <tr>
            <td>Pelapor</td>
            <td>{{ $perbaikan->user }}</td>
        </tr>
        <tr>
            <td>Nama Alat</td>
            <td>{{ $perbaikan->alat->nama_alat ?? '-' }}</td>
        </tr>
        <tr>
            <td>Kategori Perbaikan</td>
            <td>{{ $perbaikan->kategori_perbaikan }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>
                @php
                    $statusClass = $perbaikan->status == 'selesai'
                        ? 'status-selesai'
                        : ($perbaikan->status == 'onproses' ? 'status-onproses' : 'status-pending');

                    $statusLabel = $perbaikan->status == 'selesai'
                        ? '● Selesai'
                        : ($perbaikan->status == 'onproses' ? '● On Proses' : '○ Pending');
                @endphp
                <span class="status-badge {{ $statusClass }}">
                    {{ $statusLabel }}
                </span>
            </td>
        </tr>
    </table>

    {{-- KETERANGAN --}}
    <div class="section-title">Keterangan / Deskripsi Kerusakan</div>
    <div class="keterangan-box">{{ $perbaikan->keterangan }}</div>

    {{-- CATATAN TEKNISI --}}
    <div class="section-title">Catatan Teknisi</div>
    <div class="keterangan-box">
{{ $perbaikan->catatan_teknisi ?? 'Belum ada catatan dari teknisi.' }}
    </div>

    {{-- DOKUMENTASI FOTO: SEBELUM & SESUDAH --}}
    @if($perbaikan->foto_awal || $perbaikan->foto_selesai)
    <div class="section-title">Dokumentasi Foto</div>
    <table class="foto-grid">
        <tr>
            <td>
                <div class="foto-caption">Sebelum (Laporan Kerusakan)</div>
                <div class="foto-box">
                    @if($perbaikan->foto_awal)
                        <img src="{{ public_path('storage/' . $perbaikan->foto_awal) }}" alt="Foto Sebelum">
                    @else
                        <div class="foto-empty">Tidak ada foto</div>
                    @endif
                </div>
            </td>
            <td>
                <div class="foto-caption">Sesudah (Bukti Selesai)</div>
                <div class="foto-box">
                    @if($perbaikan->foto_selesai)
                        <img src="{{ public_path('storage/' . $perbaikan->foto_selesai) }}" alt="Foto Sesudah">
                    @else
                        <div class="foto-empty">Belum ada foto</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>
    @endif

    {{-- TTD --}}
    <div class="ttd-section">
        <table>
            <tr>
                <td>
                    Pelapor,
                    <div class="ttd-line"></div>
                    <strong>{{ $perbaikan->user }}</strong>
                </td>
                <td>
                    Teknisi,

                    <div style="height:45px;">
                        <img src="{{ public_path('assets/dist/img/TTD/Triyono.png') }}"
                            style="height:40px;">
                    </div>

                    <div class="ttd-line"></div>
                    <strong>Triyono</strong>
                </td>
                <td>
                    Mengetahui,
                    <div class="ttd-line"></div>
                    <strong>Koordinator</strong>
                </td>
            </tr>
        </table>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        Dicetak pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} &mdash;
        Sistem ORBITA &bull; Stasiun Meteorologi Maritim Semarang
    </div>

</body>
</html>