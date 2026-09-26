<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Kalibrasi Alat</title>
    <style>
        /* ── RESET & BASE ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #1a1a2e;
            background: #fff;
            padding: 28px 32px 24px 32px; /* margin lega dari tepi kertas */
        }

        /* ── HEADER ── */
        .header {
            display: table;
            width: 100%;
            padding-bottom: 14px;
            border-bottom: 3px solid #003366;
            margin-bottom: 18px;
        }
        .header-logo {
            display: table-cell;
            vertical-align: middle;
            width: 64px;
        }
        .header-logo img {
            width: 52px;
            height: 52px;
            object-fit: contain;
        }
        .header-text {
            display: table-cell;
            vertical-align: middle;
            padding-left: 12px;
        }
        .header-instansi {
            font-size: 7.5px;
            color: #5a6a80;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .header-title {
            font-size: 15px;
            font-weight: bold;
            color: #003366;
            line-height: 1.2;
            margin-bottom: 2px;
        }
        .header-subtitle {
            font-size: 8.5px;
            color: #6b7c99;
        }
        .header-meta {
            display: table-cell;
            vertical-align: middle;
            width: 200px;
            text-align: right;
        }
        .meta-box {
            display: inline-block;
            background: #f0f5ff;
            border: 1px solid #c5d3e8;
            border-radius: 6px;
            padding: 8px 12px;
            text-align: left;
            font-size: 8.5px;
            line-height: 1.8;
            color: #444;
        }
        .meta-box .meta-key {
            color: #003366;
            font-weight: bold;
        }

        /* ── BANNER PERIODE ── */
        .periode-banner {
            background: linear-gradient(135deg, #003366 0%, #0055aa 100%);
            border-radius: 8px;
            padding: 10px 16px;
            margin-bottom: 18px;
            display: table;
            width: 100%;
        }
        .periode-left {
            display: table-cell;
            vertical-align: middle;
        }
        .periode-label {
            font-size: 7.5px;
            color: rgba(255,255,255,0.65);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 2px;
        }
        .periode-value {
            font-size: 13px;
            font-weight: bold;
            color: #fff;
        }
        .periode-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }
        .periode-badge {
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 8px;
            color: #fff;
            display: inline-block;
        }

        /* ── SUMMARY CARDS ── */
        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-spacing: 0;
        }
        .summary-cell {
            display: table-cell;
            padding-right: 10px;
        }
        .summary-cell:last-child { padding-right: 0; }

        .summary-card {
            border: 1px solid #d8e4f0;
            border-radius: 8px;
            border-top: 3px solid #003366;
            padding: 10px 12px;
            text-align: center;
            background: #fafcff;
        }
        .summary-card.accent-green  { border-top-color: #1a7a4a; }
        .summary-card.accent-indigo { border-top-color: #4a3db5; }
        .summary-card.accent-teal   { border-top-color: #0a7a7a; }

        .summary-num {
            font-size: 22px;
            font-weight: bold;
            color: #003366;
            line-height: 1;
            margin-bottom: 4px;
        }
        .summary-card.accent-green  .summary-num { color: #1a7a4a; }
        .summary-card.accent-indigo .summary-num { color: #4a3db5; }
        .summary-card.accent-teal   .summary-num { color: #0a7a7a; }

        .summary-lbl {
            font-size: 7.5px;
            color: #7a8ea8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
        }

        /* ── TABLE ── */
        .section-title {
            font-size: 9px;
            font-weight: bold;
            color: #003366;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
            padding-left: 2px;
        }
        .section-title::before {
            content: '';
            display: inline-block;
            width: 3px;
            height: 10px;
            background: #003366;
            border-radius: 2px;
            margin-right: 6px;
            vertical-align: middle;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        thead tr {
            background: #003366;
        }
        thead th {
            padding: 9px 10px;
            font-size: 8.5px;
            font-weight: bold;
            color: #fff;
            text-align: left;
            border: none;
            white-space: nowrap;
        }
        thead th.r { text-align: right; }
        thead th.c { text-align: center; }

        tbody tr { border-bottom: 1px solid #e8eef8; }
        tbody tr:nth-child(even) { background: #f6f9ff; }
        tbody tr:nth-child(odd)  { background: #ffffff; }
        tbody tr:last-child { border-bottom: none; }

        tbody td {
            padding: 9px 10px;
            font-size: 9px;
            vertical-align: middle;
            color: #2a3a50;
        }
        tbody td.r   { text-align: right; }
        tbody td.c   { text-align: center; }
        tbody td.dim { color: #8898aa; }

        .badge-kat {
            background: #ddeeff;
            color: #003366;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 8px;
            font-weight: bold;
            display: inline-block;
            white-space: nowrap;
        }
        .badge-pet {
            background: #f0f0f6;
            color: #444;
            padding: 2px 7px;
            border-radius: 20px;
            font-size: 8px;
            border: 1px solid #ddd;
            display: inline-block;
        }
        .sert-ada {
            color: #1a7a4a;
            font-weight: bold;
            font-size: 9px;
        }
        .sert-tdk { color: #bbb; }

        .no-cell {
            color: #aab;
            font-weight: bold;
            font-size: 9px;
            width: 28px;
        }

        /* Baris kosong */
        .empty-row td {
            text-align: center;
            padding: 32px 0;
            color: #aaa;
            font-size: 9px;
        }

        /* ── TABLE WRAPPER — border luar ── */
        .table-wrap {
            border: 1px solid #d0dcee;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 18px;
        }

        /* ── FOOTER ── */
        .footer {
            border-top: 1px solid #d8e4f0;
            padding-top: 10px;
            margin-top: 4px;
            display: table;
            width: 100%;
        }
        .footer-left {
            display: table-cell;
            font-size: 7.5px;
            color: #9aacbe;
            vertical-align: middle;
        }
        .footer-right {
            display: table-cell;
            text-align: right;
            font-size: 7.5px;
            color: #9aacbe;
            vertical-align: middle;
        }
        .footer-divider {
            display: inline-block;
            margin: 0 6px;
            color: #ccc;
        }
    </style>
</head>
<body>

    {{-- ══════════════════════════════════════════════════════════
         HEADER: Logo + Judul + Meta
    ══════════════════════════════════════════════════════════ --}}
    <div class="header">
        <div class="header-logo">
            {{-- Logo BMKG dari public/assets --}}
            @php
                $logoPath = public_path('assets/dist/img/logo.png');
                $logoSrc  = '';
                if (file_exists($logoPath)) {
                    $logoData = base64_encode(file_get_contents($logoPath));
                    $logoMime = 'image/png';
                    $logoSrc  = 'data:' . $logoMime . ';base64,' . $logoData;
                }
            @endphp
            @if($logoSrc)
                <img src="{{ $logoSrc }}" alt="Logo BMKG">
            @else
                {{-- Fallback: placeholder teks --}}
                <div style="width:52px;height:52px;border-radius:50%;background:#003366;
                            display:flex;align-items:center;justify-content:center;">
                    <span style="color:#fff;font-weight:bold;font-size:9px;">BMKG</span>
                </div>
            @endif
        </div>
        <div class="header-text">
            <div class="header-instansi">Badan Meteorologi, Klimatologi, dan Geofisika</div>
            <div class="header-title">Daftar Riwayat Kalibrasi Alat</div>
            <div class="header-subtitle">Laporan Arsip Kalibrasi Peralatan Laboratorium &mdash; ORBITA</div>
        </div>
        <div class="header-meta">
            <div class="meta-box">
                <span class="meta-key">Tanggal Cetak</span>&nbsp;&nbsp;
                {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}<br>
                <span class="meta-key">Dicetak Oleh</span>&nbsp;
                {{ $user->name ?? '-' }}<br>
                <span class="meta-key">Total Record</span>&nbsp;&nbsp;
                {{ $kalibrasis->count() }} data
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         BANNER PERIODE
    ══════════════════════════════════════════════════════════ --}}
    <div class="periode-banner">
        <div class="periode-left">
            <div class="periode-label">Rentang Periode Laporan</div>
            <div class="periode-value">
                @if($labelPeriode)
                    {{ $labelPeriode }}
                @else
                    Semua Periode
                @endif
            </div>
        </div>
        <div class="periode-right">
            <span class="periode-badge">
                <span style="opacity:.7;">&#9679;</span>&nbsp;
                Kalibrasi Peralatan
            </span>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         SUMMARY CARDS
    ══════════════════════════════════════════════════════════ --}}
    @php
        $totalData      = $kalibrasis->count();
        $denganSertif   = $kalibrasis->whereNotNull('sertifikat_pdf')->count();
        $kategoriUniq   = $kalibrasis->pluck('kategori.nama_kategori')->filter()->unique()->count();
        $kalibratorUniq = $kalibrasis->pluck('kalibrator')->filter()->unique()->count();
    @endphp
    <div class="summary-row">
        <div class="summary-cell">
            <div class="summary-card">
                <div class="summary-num">{{ $totalData }}</div>
                <div class="summary-lbl">Total Kalibrasi</div>
            </div>
        </div>
        <div class="summary-cell">
            <div class="summary-card accent-indigo">
                <div class="summary-num">{{ $kategoriUniq }}</div>
                <div class="summary-lbl">Kategori Alat</div>
            </div>
        </div>
        <div class="summary-cell">
            <div class="summary-card accent-teal">
                <div class="summary-num">{{ $kalibratorUniq }}</div>
                <div class="summary-lbl">Institusi Kalibrator</div>
            </div>
        </div>
        <div class="summary-cell">
            <div class="summary-card accent-green">
                <div class="summary-num">{{ $denganSertif }}</div>
                <div class="summary-lbl">Dengan Sertifikat</div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         TABEL RIWAYAT
    ══════════════════════════════════════════════════════════ --}}
    <div class="section-title">Data Riwayat Kalibrasi</div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:28px;" class="c">No</th>
                    <th style="width:115px;">Kategori Alat</th>
                    <th style="width:130px;">Periode Kalibrasi</th>
                    <th>Institusi Kalibrator</th>
                    <th class="r" style="width:72px;">Koreksi</th>
                    <th class="r" style="width:86px;">Ketidakpastian</th>
                    <th style="width:96px;">Petugas</th>
                    <th class="c" style="width:58px;">Sertifikat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kalibrasis as $i => $data)
                <tr>
                    <td class="c no-cell">{{ $i + 1 }}</td>
                    <td>
                        <span class="badge-kat">{{ $data->kategori->nama_kategori ?? '-' }}</span>
                    </td>
                    <td class="dim">
                        {{ \Carbon\Carbon::parse($data->tanggal_mulai)->locale('id')->translatedFormat('d M Y') }}
                        &ndash;
                        {{ \Carbon\Carbon::parse($data->tanggal_selesai)->locale('id')->translatedFormat('d M Y') }}
                    </td>
                    <td style="font-weight:600;">{{ $data->kalibrator }}</td>
                    <td class="r" style="font-weight:bold; color:#003366;">
                        {{ $data->nilai_koreksi !== null ? number_format($data->nilai_koreksi, 4) : '-' }}
                    </td>
                    <td class="r dim">
                        {{ $data->nilai_ketidakpastian !== null ? number_format($data->nilai_ketidakpastian, 4) : '-' }}
                    </td>
                    <td>
                        @if($data->petugas)
                            <span class="badge-pet">{{ $data->petugas }}</span>
                        @else
                            <span class="dim">-</span>
                        @endif
                    </td>
                    <td class="c">
                        @if($data->sertifikat_pdf)
                            <span class="sert-ada">&#10003; Ada</span>
                        @else
                            <span class="sert-tdk">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="8">Tidak ada data kalibrasi pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         FOOTER
    ══════════════════════════════════════════════════════════ --}}
    <div class="footer">
        <div class="footer-left">
            <strong>ORBITA</strong> &mdash; Sistem Monitoring Peralatan BMKG
            <span class="footer-divider">|</span>
            Dokumen ini digenerate otomatis oleh sistem, tidak memerlukan tanda tangan basah
        </div>
        <div class="footer-right">
            {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
            <span class="footer-divider">|</span>
            {{ $user->name ?? '-' }}
        </div>
    </div>

</body>
</html>