@extends('layouts.master')

@push('styles')
<style>
    #tablePerbaikan {
        border-collapse: collapse !important;
        width: 100% !important;
    }

    #tablePerbaikan thead th {
        background-color: #f8f9fa !important;
        border: 1px solid #dee2e6 !important;
        padding: 10px 12px !important;
        vertical-align: middle !important;
        white-space: nowrap;
    }

    #tablePerbaikan tbody td {
        border: 1px solid #dee2e6 !important;
        padding: 8px 12px !important;
        vertical-align: middle !important;
    }

    .dt-buttons .btn {
        margin-right: 5px;
        border-radius: 4px !important;
        font-size: 13px;
    }

    .dataTables_filter input {
        border-radius: 6px !important;
        border: 1px solid #ced4da !important;
        padding: 4px 10px !important;
    }

    .dataTables_filter {
        text-align: right !important;
    }

    .dataTables_info,
    .dataTables_paginate {
        margin-top: 15px !important;
    }

    .dataTables_paginate .pagination {
        justify-content: flex-end !important;
    }

    .col-wrap {
        white-space: normal !important;
        min-width: 180px !important;
        word-break: break-word;
    }

    .col-status-fixed {
        min-width: 130px !important;
        text-align: center;
    }

    /* BREADCRUMB KATEGORI (Kategori > Sub Kategori > Alat) */
    .col-kategori-fixed {
        min-width: 170px !important;
        max-width: 220px;
    }
    .kategori-breadcrumb {
        display: flex;
        flex-direction: column;
        gap: 1px;
        text-align: left;
    }
    .kategori-breadcrumb .kb-row {
        display: flex;
        align-items: baseline;
        gap: 5px;
        line-height: 1.35;
    }
    .kategori-breadcrumb .kb-arrow {
        color: #adb5bd;
        font-size: 10px;
        width: 11px;
        flex-shrink: 0;
        text-align: center;
    }
    .kategori-breadcrumb .kb-item {
        font-size: 12px;
        color: #6c757d;
        word-break: break-word;
    }
    .kategori-breadcrumb .kb-row:first-child .kb-item {
        font-weight: 700;
        color: #003366;
        font-size: 12.5px;
    }
    .kategori-breadcrumb .kb-row:last-child .kb-item {
        font-weight: 700;
        color: #0d5c34;
        background: #e6f4ea;
        padding: 1px 7px;
        border-radius: 8px;
        display: inline-block;
    }

    /* FOTO */
    /* Wrapper dipasang agar ukuran gambar tetap konsisten kecil,
       walau ada CSS lain (mis. dari layouts.master / Bootstrap)
       yang mencoba override width gambar di dalam tabel. */
    .foto-thumb-wrap {
        width: 45px !important;
        height: 45px !important;
        max-width: 45px !important;
        max-height: 45px !important;
        overflow: hidden !important;
        border-radius: 6px !important;
        border: 1px solid #dee2e6 !important;
        margin: 0 auto !important;
        display: block !important;
    }

    .foto-thumb-wrap .foto-perbaikan,
    #tablePerbaikan .foto-perbaikan {
        width: 45px !important;
        height: 45px !important;
        max-width: 45px !important;
        max-height: 45px !important;
        min-width: 45px !important;
        min-height: 45px !important;
        object-fit: cover !important;
        border-radius: 6px !important;
        border: none !important;
        cursor: pointer;
        display: block !important;
        margin: 0 auto !important;
    }

    .foto-thumb-wrap:hover .foto-perbaikan {
        transform: scale(1.08);
    }

    .foto-placeholder {
        width: 45px;
        height: 45px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        background: #f8f9fa;
    }

    /* Kolom Foto di tabel jangan dibiarkan melebar otomatis */
    .col-foto-fixed {
        width: 60px !important;
        max-width: 60px !important;
    }

    /* Preview modal */
    .preview-label {
        font-size: 12px;
        text-transform: uppercase;
        color: #6c757d;
        font-weight: 600;
        letter-spacing: .03em;
        margin-bottom: 2px;
    }

    .preview-value {
        font-size: 14px;
        margin-bottom: 14px;
        word-break: break-word;
    }

    .preview-foto-box {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 8px;
        text-align: center;
        background: #f8f9fa;
        width: 100%;
        max-width: 100%;
        height: 160px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
    }

    .preview-foto-box img {
        width: auto;
        height: auto;
        max-width: 100% !important;
        max-height: 100% !important;
        object-fit: contain;
        border-radius: 6px;
        display: block;
    }

    #modalPreview .modal-body {
        max-height: 75vh;
        overflow-y: auto;
        overflow-x: hidden;
    }

    #modalPreview .modal-dialog {
        max-width: 700px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h3 class="text-uppercase font-weight-bold"
                    style="border-left: 5px solid #003366; padding-left: 15px; margin-bottom: 0;">
                    Permintaan Perbaikan
                </h3>

                @php
                    $roleBisaInput = ['kepala kelompok', 'observer', 'forecaster', 'tata usaha', 'koordinator'];
                    $userRole = strtolower(Auth::user()->role->nama_role ?? '');
                @endphp

                @if(in_array($userRole, $roleBisaInput))
                    <a href="{{ route('perbaikan.create') }}"
                       class="btn btn-primary shadow-sm"
                       style="border-radius: 8px;">
                        <i class="fas fa-plus mr-1"></i> Tambah Permintaan
                    </a>
                @endif
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-0">

                    <div style="overflow-x: auto; padding: 1rem;">

                        <table id="tablePerbaikan"
                               class="table table-bordered mb-0"
                               style="width:100%">

                            <thead class="bg-light text-center">
                                <tr>
                                    <th style="width:5%">No</th>
                                    <th class="col-foto-fixed" style="width:60px !important; max-width:60px !important;">Foto</th>
                                    <th>Tanggal Permintaan</th>
                                    <th>Tanggal Diterima</th>
                                    <th>Tanggal Selesai</th>
                                    <th>User</th>
                                    <th class="col-kategori-fixed">Kategori</th>
                                    <th class="col-wrap">Keterangan</th>
                                    <th style="width:10%">Validasi</th>
                                    <th class="col-wrap">Catatan</th>
                                    <th class="col-status-fixed">Status</th>
                                    <th style="width:12%">Verifikasi Koordinator</th>
                                    <th style="width:12%">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($perbaikans as $p)
                                @php
                                    // Tiket dianggap "Menunggu Verifikasi Koordinator" ketika
                                    // teknisi sudah set status selesai (+upload foto) tapi
                                    // koordinator belum ACC/Tolak (validasi_koordinator masih null).
$menungguVerifikasi = ($p->status === 'selesai' && is_null($p->validasi_koordinator));
    $sudahAcc = ($p->status === 'selesai' && $p->validasi_koordinator == true);
                                @endphp
                                <tr>

                                {{-- MODAL FOTO AWAL --}}
                                @if($p->foto_awal)
                                <div class="modal fade"
                                     id="modalFoto{{ $p->id }}"
                                     tabindex="-1"
                                     role="dialog"
                                     aria-hidden="true">

                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">

                                        <div class="modal-content border-0 shadow">

                                            <div class="modal-header bg-dark text-white">
                                                <h5 class="modal-title">
                                                    Foto Tiket #{{ $p->no_tiket }}
                                                </h5>

                                                <button type="button"
                                                        class="close text-white"
                                                        data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body text-center p-2">

                                                <img src="{{ asset('storage/' . $p->foto_awal) }}"
                                                     class="img-fluid rounded"
                                                     style="
                                                        max-height:80vh;
                                                        object-fit:contain;
                                                     ">

                                            </div>

                                        </div>

                                    </div>

                                </div>
                                @endif

                                    <td class="text-center font-weight-bold">
                                        {{ $p->no_tiket }}
                                    </td>

                                    <td class="text-center align-middle" style="width:60px !important; max-width:60px !important;">

                                        @if($p->foto_awal)

                                            <div class="foto-thumb-wrap"
                                                 data-toggle="modal"
                                                 data-target="#modalFoto{{ $p->id }}"
                                                 title="Klik untuk perbesar"
                                                 style="width:45px !important; height:45px !important; max-width:45px !important; max-height:45px !important; overflow:hidden !important; border-radius:6px !important; border:1px solid #dee2e6 !important; margin:0 auto !important;">
                                                <img src="{{ asset('storage/' . $p->foto_awal) }}"
                                                     alt="Foto Perbaikan"
                                                     class="foto-perbaikan"
                                                     style="width:45px !important; height:45px !important; max-width:45px !important; max-height:45px !important; object-fit:cover !important; border:none !important;">
                                            </div>

                                        @else

                                            <span class="text-muted small">-</span>

                                        @endif

                                    </td>

                                    <td class="text-center">
                                        {{ $p->tgl_permintaan }}
                                    </td>

                                    <td class="text-center font-weight-bold text-success">
                                        {{ $p->tgl_diterima ?? '-' }}
                                    </td>

                                    <td class="text-center font-weight-bold text-info">
                                        {{ $p->tgl_selesai ?? '-' }}
                                    </td>

                                    <td>{{ $p->user }}</td>

                                    <td class="col-kategori-fixed">
                                        @php
                                            // Pecah teks "Kategori > Sub Kategori > Alat" jadi breadcrumb.
                                            // Jika tidak mengandung " > " (misal data lama), tampilkan apa adanya.
                                            $kategoriParts = array_filter(array_map('trim', explode('>', $p->kategori_perbaikan ?? '')));
                                        @endphp

                                        @if(count($kategoriParts) > 1)
                                            <div class="kategori-breadcrumb">
                                                @foreach($kategoriParts as $i => $part)
                                                    <div class="kb-row">
                                                        <span class="kb-arrow">{{ $i > 0 ? '↳' : '' }}</span>
                                                        <span class="kb-item">{{ $part }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            {{ $p->kategori_perbaikan }}
                                        @endif
                                    </td>

                                    <td class="col-wrap">
                                        {{ $p->keterangan }}
                                    </td>

                                    {{-- VALIDASI --}}
                                    <td class="text-center" style="min-width:100px;">

                                        @if(Auth::user()->role && (Auth::user()->role->nama_role == 'admin' || Auth::user()->role->nama_role == 'teknisi'))

                                            @if(!$p->tgl_diterima)

                                                @if($p->validasi === 0)
                                                    <div class="small text-danger mb-1" style="font-size:11px;">
                                                        <i class="fas fa-undo"></i> Pernah ditolak
                                                    </div>
                                                @endif

                                                <form action="{{ route('perbaikan.validasi', $p->id) }}"
                                                      method="POST">

                                                    @csrf

                                                    <div class="btn-group">

                                                        <button type="submit"
                                                                name="action"
                                                                value="terima"
                                                                class="btn btn-sm btn-success shadow-sm"
                                                                title="Terima">
                                                            <i class="fas fa-check"></i>
                                                        </button>

                                                        <button type="submit"
                                                                name="action"
                                                                value="tolak"
                                                                class="btn btn-sm btn-danger shadow-sm"
                                                                title="Tolak">
                                                            <i class="fas fa-times"></i>
                                                        </button>

                                                    </div>

                                                </form>

                                            @else

                                                <span class="badge badge-pill badge-success px-2 py-1">
                                                    <i class="fas fa-check-circle"></i> Valid
                                                </span>

                                            @endif

                                        @else

                                            {!! $p->tgl_diterima
                                                ? '<span class="text-success small font-weight-bold">DITERIMA</span>'
                                                : '<span class="text-muted small">MENUNGGU</span>' !!}

                                        @endif

                                    </td>

                                    <td class="col-wrap small">
                                        {{ $p->catatan_teknisi ?? '-' }}
                                    </td>

                                    {{-- STATUS --}}
                                    {{--
                                        REVISI:
                                        - Saat opsi "Selesai" dipilih, JS akan mencegah submit langsung
                                          dan membuka modal upload foto bukti (modalFotoSelesai{{ $p->id }}).
                                        - Form select ini TIDAK auto-submit lagi untuk opsi 'selesai'.
                                          Untuk opsi lain (pending/onproses) tetap auto-submit seperti semula.
                                    --}}
                                    <td class="text-center col-status-fixed">

                                        @if(Auth::user()->role && (Auth::user()->role->nama_role == 'admin' || Auth::user()->role->nama_role == 'teknisi'))

                                            @if($menungguVerifikasi)

                                                {{-- Status terkunci selagi menunggu keputusan koordinator --}}
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> Menunggu Verifikasi
                                                </span>

                                            @else

                                            <form action="{{ route('perbaikan.update', $p->id) }}"
                                                  method="POST"
                                                  id="formStatus{{ $p->id }}">

                                                @csrf
                                                @method('PUT')

                                                <select name="status"
                                                        class="form-control form-control-sm border-0 font-weight-bold status-select {{ $p->status == 'selesai' ? 'text-success' : ($p->status == 'onproses' ? 'text-warning' : 'text-muted') }}"
                                                        data-id="{{ $p->id }}"
                                                        data-current="{{ $p->status }}"
                                                        style="cursor:pointer; min-width:130px;">

                                                    <option value="pending"
                                                        {{ $p->status == 'pending' ? 'selected' : '' }}>
                                                        ○ Pending
                                                    </option>

                                                    <option value="onproses"
                                                        {{ $p->status == 'onproses' ? 'selected' : '' }}>
                                                        ● On Proses
                                                    </option>

                                                    <option value="selesai"
                                                        {{ $p->status == 'selesai' ? 'selected' : '' }}>
                                                        ● Selesai
                                                    </option>

                                                </select>

                                                <input type="hidden"
                                                       name="catatan"
                                                       value="{{ $p->catatan_teknisi ?? '-' }}">

                                            </form>

                                            @endif

                                        @else

                                            @if($menungguVerifikasi)
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> Menunggu Verifikasi
                                                </span>
                                            @else
                                                <span class="badge {{ $p->status == 'selesai' ? 'badge-success' : ($p->status == 'onproses' ? 'badge-warning' : 'badge-secondary') }}">
                                                    {{ $p->status == 'selesai' ? '● Selesai' : ($p->status == 'onproses' ? '● On Proses' : '○ Pending') }}
                                                </span>
                                            @endif

                                        @endif

                                    </td>

                                    {{-- VERIFIKASI KOORDINATOR (BARU) --}}
                                    <td class="text-center" style="min-width:130px;">

                                        @if(strtolower(Auth::user()->role->nama_role ?? '') == 'koordinator')

                                            @if($menungguVerifikasi)

                                                <form action="{{ route('perbaikan.validasi-koordinator', $p->id) }}"
                                                      method="POST">
                                                    @csrf

                                                    <div class="btn-group">

                                                        <button type="submit"
                                                                name="action"
                                                                value="setuju"
                                                                class="btn btn-sm btn-success shadow-sm"
                                                                title="ACC / Setujui">
                                                            <i class="fas fa-check"></i> ACC
                                                        </button>

                                                        <button type="submit"
                                                                name="action"
                                                                value="tolak"
                                                                class="btn btn-sm btn-danger shadow-sm"
                                                                title="Tolak"
                                                                onclick="return confirm('Tolak hasil perbaikan ini? Tiket akan dikembalikan ke teknisi dan foto bukti selesai akan dihapus.');">
                                                            <i class="fas fa-times"></i> Tolak
                                                        </button>

                                                    </div>
                                                </form>

                                            @elseif($sudahAcc)

                                                <span class="badge badge-pill badge-success px-2 py-1">
                                                    <i class="fas fa-check-circle"></i> ACC
                                                </span>

                                            @else

                                                <span class="text-muted small">-</span>

                                            @endif

                                        @else

                                            @if($menungguVerifikasi)
                                                <span class="text-warning small font-weight-bold">MENUNGGU</span>
                                            @elseif($sudahAcc)
                                                <span class="text-success small font-weight-bold">ACC KOORDINATOR</span>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif

                                        @endif

                                    </td>

                                    {{-- AKSI --}}
                                    <td class="text-center">

                                        <div class="btn-group" role="group">

                                            {{-- BARU: Tombol Preview --}}
                                            <button type="button"
                                                    class="btn btn-sm btn-light border shadow-sm btn-preview"
                                                    data-id="{{ $p->id }}"
                                                    title="Preview Detail">
                                                <i class="fas fa-eye text-secondary"></i>
                                            </button>

                                            <a href="{{ route('perbaikan.download', $p->id) }}"
                                               class="btn btn-sm btn-light border shadow-sm"
                                               title="Download">
                                                <i class="fas fa-download text-primary"></i>
                                            </a>

                                            @if(Auth::user()->role && in_array(Auth::user()->role->nama_role, ['admin', 'teknisi']) && !$menungguVerifikasi)

                                                <button type="button"
                                                        class="btn btn-sm btn-light border shadow-sm"
                                                        data-toggle="modal"
                                                        data-target="#modalCatatan{{ $p->id }}"
                                                        title="Edit Catatan">
                                                    <i class="fas fa-edit text-warning"></i>
                                                </button>

                                            @endif

                                        </div>

                                    </td>

                                </tr>
                                @endforeach
                            </tbody>

                        </table>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

@foreach($perbaikans as $p)

<!-- Modal Edit Catatan -->
<div class="modal fade" id="modalCatatan{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('perbaikan.update', $p->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">
                        Edit Perbaikan #{{ $p->no_tiket }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="pending"
                                {{ $p->status == 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>

                            <option value="onproses"
                                {{ $p->status == 'onproses' ? 'selected' : '' }}>
                                On Proses
                            </option>

                            <option value="selesai"
                                {{ $p->status == 'selesai' ? 'selected' : '' }}>
                                Selesai
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea name="catatan"
                                  rows="4"
                                  class="form-control">{{ $p->catatan_teknisi ?? '-' }}</textarea>
                    </div>

                    @if(!$p->foto_selesai)
                    <div class="form-group">
                        <label>Foto Bukti Selesai <span class="text-danger">*</span></label>
                        <input type="file" name="foto_selesai" class="form-control" accept="image/png,image/jpeg,image/jpg">
                        <small class="text-muted">Wajib diisi jika status diubah menjadi "Selesai".</small>
                    </div>
                    @else
                    <div class="form-group">
                        <label>Foto Bukti Selesai (sudah ada)</label><br>
                        <img src="{{ asset('storage/' . $p->foto_selesai) }}" style="max-width:120px;border-radius:6px;border:1px solid #dee2e6;">
                        <div class="mt-2">
                            <label class="small text-muted">Ganti foto (opsional)</label>
                            <input type="file" name="foto_selesai" class="form-control" accept="image/png,image/jpeg,image/jpg">
                        </div>
                    </div>
                    @endif

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endforeach

{{--
    BARU: Modal Upload Foto Bukti Selesai
    Muncul otomatis ketika dropdown status diganti ke "Selesai" untuk tiket
    yang belum punya foto_selesai. Form ini submit ke route yang sama
    (perbaikan.update) dengan status=selesai + file foto_selesai (wajib).
--}}
@foreach($perbaikans as $p)
@if(!$p->foto_selesai)
<div class="modal fade" id="modalFotoSelesai{{ $p->id }}" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('perbaikan.update', $p->id) }}" method="POST" enctype="multipart/form-data" class="form-foto-selesai">
                @csrf
                @method('PUT')

                <input type="hidden" name="status" value="selesai">

                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white">
                        Konfirmasi Selesai &mdash; #{{ $p->no_tiket }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-revert="{{ $p->id }}">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <p class="small text-muted">
                        Unggah foto sebagai bukti bahwa permintaan perbaikan ini telah diselesaikan.
                        Status tidak akan tersimpan sebagai <strong>Selesai</strong> tanpa foto bukti.
                    </p>

                    <div class="form-group">
                        <label>Catatan Teknisi <span class="text-danger">*</span></label>

                        <textarea
                            name="catatan_teknisi"
                            class="form-control"
                            rows="4"
                            placeholder="Contoh: Kabel sensor diganti, konektor dibersihkan, alat kembali normal."
                            required>{{ old('catatan_teknisi') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Foto Bukti Selesai <span class="text-danger">*</span></label>
                        <input type="file"
                               name="foto_selesai"
                               class="form-control"
                               accept="image/png,image/jpeg,image/jpg"
                               required>
                        <small class="text-muted">Format JPG/PNG, maksimal 2MB.</small>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal"
                            data-revert="{{ $p->id }}">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-success">
                        <i class="fas fa-check mr-1"></i> Simpan &amp; Tandai Selesai
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endif
@endforeach

{{-- BARU: Modal Preview Detail (kontennya diisi via AJAX/fetch) --}}
<div class="modal fade" id="modalPreview" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:700px;">
        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="previewTitle">Detail Permintaan Perbaikan</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body" id="previewBody" style="max-height:75vh; overflow-y:auto; overflow-x:hidden;">
                <div class="text-center py-4 text-muted" id="previewLoading">
                    <i class="fas fa-spinner fa-spin mr-1"></i> Memuat data...
                </div>

                <div id="previewContent" style="display:none;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="preview-label">No. Tiket</div>
                            <div class="preview-value" id="pv-no_tiket"></div>

                            <div class="preview-label">Alat</div>
                            <div class="preview-value" id="pv-alat"></div>

                            <div class="preview-label">User Pelapor</div>
                            <div class="preview-value" id="pv-user"></div>

                            <div class="preview-label">Kategori</div>
                            <div class="preview-value" id="pv-kategori_perbaikan"></div>

                            <div class="preview-label">Status</div>
                            <div class="preview-value" id="pv-status"></div>

                            <div class="preview-label">Verifikasi Koordinator</div>
                            <div class="preview-value" id="pv-verifikasi"></div>
                        </div>

                        <div class="col-md-6">
                            <div class="preview-label">Tanggal Permintaan</div>
                            <div class="preview-value" id="pv-tgl_permintaan"></div>

                            <div class="preview-label">Tanggal Diterima</div>
                            <div class="preview-value" id="pv-tgl_diterima"></div>

                            <div class="preview-label">Tanggal Selesai</div>
                            <div class="preview-value" id="pv-tgl_selesai"></div>

                            <div class="preview-label">Catatan</div>
                            <div class="preview-value" id="pv-catatan"></div>
                        </div>
                    </div>

                    <hr>

                    <div class="preview-label">Keterangan</div>
                    <div class="preview-value" id="pv-keterangan"></div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="preview-label">Foto Awal (Laporan Kerusakan)</div>
                            <div class="preview-foto-box" id="pv-foto_awal_box"
                                 style="width:100% !important; height:160px !important; max-height:160px !important; overflow:hidden !important; display:flex !important; align-items:center !important; justify-content:center !important; border:1px solid #dee2e6; border-radius:8px; background:#f8f9fa; box-sizing:border-box;">
                                <span class="text-muted small">Tidak ada foto</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="preview-label">Foto Bukti Selesai</div>
                            <div class="preview-foto-box" id="pv-foto_selesai_box"
                                 style="width:100% !important; height:160px !important; max-height:160px !important; overflow:hidden !important; display:flex !important; align-items:center !important; justify-content:center !important; border:1px solid #dee2e6; border-radius:8px; background:#f8f9fa; box-sizing:border-box;">
                                <span class="text-muted small">Belum ada foto</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {

    /* =========================================================
       0) FIX PAKSA UKURAN THUMBNAIL FOTO
          Beberapa setup DataTables (autoWidth) menghitung ulang
          lebar kolom & bisa "menimpa" ukuran gambar setelah CSS
          ter-load. Fungsi ini memaksa ulang ukuran 45x45px setiap
          kali dipanggil, termasuk setelah DataTables init/redraw.
       ========================================================= */
    function fixFotoThumbSize() {
        $('#tablePerbaikan .foto-thumb-wrap').css({
            width: '45px', height: '45px',
            maxWidth: '45px', maxHeight: '45px',
            overflow: 'hidden'
        });
        $('#tablePerbaikan .foto-perbaikan').css({
            width: '45px', height: '45px',
            maxWidth: '45px', maxHeight: '45px',
            objectFit: 'cover'
        });
    }

    fixFotoThumbSize();

    // Jalankan lagi setelah DataTables (jika ada) selesai inisialisasi/redraw
    $('#tablePerbaikan').on('draw.dt init.dt', function () {
        fixFotoThumbSize();
    });

    // Jaga-jaga: jalankan sekali lagi setelah semua aset (termasuk gambar) selesai load
    $(window).on('load', fixFotoThumbSize);

    /* =========================================================
       1) Saat dropdown status diubah ke "Selesai"
          -> jika tiket belum punya foto_selesai, BLOK auto-submit,
             buka modal upload foto wajib dulu.
          -> jika sudah punya foto_selesai, atau opsi lain dipilih,
             submit form seperti biasa.
       ========================================================= */
    $('.status-select').on('change', function () {

        var $select   = $(this);
        var id        = $select.data('id');
        var newValue  = $select.val();
        var hasModalFotoSelesai = $('#modalFotoSelesai' + id).length > 0;

        if (newValue === 'selesai' && hasModalFotoSelesai) {
            // Cegah submit langsung, buka modal upload foto bukti dulu
            $('#modalFotoSelesai' + id).modal('show');
            return;
        }

        // Untuk pending/onproses, atau tiket yang sudah punya foto_selesai: submit langsung
        $('#formStatus' + id).submit();
    });

    /* =========================================================
       2) Jika modal upload foto bukti ditutup/dibatalkan,
          kembalikan dropdown ke status sebelumnya supaya
          tampilan tidak "nyangkut" di opsi Selesai.
       ========================================================= */
    $(document).on('click', '[data-revert]', function () {
        var id = $(this).data('revert');
        var $select = $('select.status-select[data-id="' + id + '"]');
        var current = $select.data('current');
        $select.val(current);
    });

    /* =========================================================
       3) Submit form foto bukti selesai via AJAX supaya halaman
          tidak reload kasar dan modal otomatis tertutup setelah sukses.
          (Jika project ini lebih nyaman tanpa AJAX, hapus blok ini
          dan form akan submit normal/reload halaman.)
       ========================================================= */
    $('.form-foto-selesai').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var formData = new FormData(this);
        var $modal = $form.closest('.modal');

        $form.find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                $modal.modal('hide');
                location.reload();
            },
            error: function (xhr) {
                var msg = 'Gagal menyimpan data.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                alert(msg);
                $form.find('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-check mr-1"></i> Simpan &amp; Tandai Selesai');
            }
        });
    });

    /* =========================================================
       4) Tombol Preview -> fetch detail via AJAX, tampilkan di modal
       ========================================================= */
    $(document).on('click', '.btn-preview', function () {

        var id = $(this).data('id');

        $('#previewContent').hide();
        $('#previewLoading').show();
        $('#modalPreview').modal('show');

        $.get('{{ url("perbaikan/show") }}/' + id, function (data) {

            $('#previewTitle').text('Detail Permintaan Perbaikan #' + data.no_tiket);

            var menungguVerifikasi = (data.status === 'selesai' && (data.validasi_koordinator === null || data.validasi_koordinator === undefined));

            $('#pv-no_tiket').text(data.no_tiket || '-');
            $('#pv-alat').text(data.alat || '-');
            $('#pv-user').text(data.user || '-');
            $('#pv-kategori_perbaikan').html(renderKategoriBreadcrumb(data.kategori_perbaikan));
            $('#pv-status').html(statusBadge(data.status, menungguVerifikasi));
            $('#pv-verifikasi').html(verifikasiBadge(data.validasi_koordinator, menungguVerifikasi));
            $('#pv-tgl_permintaan').text(data.tgl_permintaan || '-');
            $('#pv-tgl_diterima').text(data.tgl_diterima || '-');
            $('#pv-tgl_selesai').text(data.tgl_selesai || '-');
            $('#pv-catatan').text(data.catatan || '-');
            $('#pv-keterangan').text(data.keterangan || '-');

            var imgStyle = 'max-width:100% !important; max-height:160px !important; width:auto !important; height:auto !important; object-fit:contain !important; border-radius:6px; display:block;';

            if (data.foto_awal_url) {
                $('#pv-foto_awal_box').html('<img src="' + data.foto_awal_url + '" alt="Foto Awal" style="' + imgStyle + '">');
            } else {
                $('#pv-foto_awal_box').html('<span class="text-muted small">Tidak ada foto</span>');
            }

            if (data.foto_selesai_url) {
                $('#pv-foto_selesai_box').html('<img src="' + data.foto_selesai_url + '" alt="Foto Selesai" style="' + imgStyle + '">');
            } else {
                $('#pv-foto_selesai_box').html('<span class="text-muted small">Belum ada foto</span>');
            }

            $('#previewLoading').hide();
            $('#previewContent').show();

        }).fail(function () {
            $('#previewLoading').html('<span class="text-danger">Gagal memuat data.</span>');
        });
    });

    function renderKategoriBreadcrumb(teks) {
        if (!teks) {
            return '-';
        }

        var parts = teks.split('>').map(function (s) { return s.trim(); }).filter(Boolean);

        if (parts.length <= 1) {
            return $('<div>').text(teks).html(); // escape aman untuk teks biasa
        }

        var html = '<div class="kategori-breadcrumb">';
        parts.forEach(function (part, i) {
            var safePart = $('<div>').text(part).html(); // escape aman
            html += '<div class="kb-row">';
            html += '<span class="kb-arrow">' + (i > 0 ? '↳' : '') + '</span>';
            html += '<span class="kb-item">' + safePart + '</span>';
            html += '</div>';
        });
        html += '</div>';

        return html;
    }

    function statusBadge(status, menungguVerifikasi) {
        if (menungguVerifikasi) {
            return '<span class="badge badge-warning"><i class="fas fa-clock"></i> Menunggu Verifikasi</span>';
        }
        if (status === 'selesai') {
            return '<span class="badge badge-success">● Selesai</span>';
        } else if (status === 'onproses') {
            return '<span class="badge badge-warning">● On Proses</span>';
        }
        return '<span class="badge badge-secondary">○ Pending</span>';
    }

    function verifikasiBadge(validasiKoordinator, menungguVerifikasi) {
        if (menungguVerifikasi) {
            return '<span class="badge badge-warning"><i class="fas fa-hourglass-half"></i> Menunggu Koordinator</span>';
        }
        if (validasiKoordinator === 1) {
            return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> ACC Koordinator</span>';
        }
        if (validasiKoordinator === 0) {
            return '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Ditolak Koordinator</span>';
        }
        return '<span class="text-muted small">-</span>';
    }

});
</script>
@endpush