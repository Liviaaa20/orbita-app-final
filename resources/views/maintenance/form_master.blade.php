@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h3 class="text-uppercase font-weight-bold" style="border-left: 5px solid #003366; padding-left: 15px;">
            Input Hasil Pengecekan {{ ucfirst($type) }}
        </h3>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ route('maintenance.store-hasil') }}" method="POST" enctype="multipart/form-data">        @csrf
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
        <input type="hidden" name="waktu" value="{{ $waktu }}">
        <input type="hidden" name="type" value="{{ $type }}">

        <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-3">
                <h6 class="m-0 font-weight-bold text-dark">Form Checklist Peralatan</h6>
                <small class="text-muted">
                    Tanggal: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }} | Shift: {{ $waktu }}
                </small>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="bg-light text-center small font-weight-bold">
                            <tr>
                                <th>Nama Alat</th>
                                <th width="180">Status Operasional</th>
                                <th width="180">Kondisi Fisik</th>
                                <th>Catatan Khusus / Keterangan</th>
                                <th width="150">Foto Dokumentasi</th>
                            </tr>
                        </thead>
                        <tbody>
    @forelse($groupedByKategori as $namaKategori => $kumpulanSub)
        
        {{-- HEADER KATEGORI (Gelap) --}}
        <tr style="background-color: #343a40;">
            <td colspan="5" class="py-2 px-4 text-white font-weight-bold" style="font-size: 1.1rem;">
                <i class="fas fa-layer-group mr-2 text-warning"></i> KATEGORI: {{ strtoupper($namaKategori) }}
            </td>
        </tr>

        @foreach($kumpulanSub as $sub)
            {{-- HEADER SUB-KATEGORI (Terang) --}}
            <tr style="background-color: #e9ecef;">
                <td colspan="5" class="font-weight-bold text-dark pl-5 py-2" style="font-size: 0.95rem; border-left: 4px solid #003366;">
                    <i class="fas fa-tag mr-2 text-secondary"></i> Sub-Kategori: {{ $sub->nama_sub_kategori }}
                </td>
            </tr>
                
            {{-- DAFTAR ALAT --}}
            @foreach($sub->alats as $alat)
                <tr>
                    <td class="pl-5 align-middle">
                        <span class="font-weight-bold text-primary">{{ $alat->nama_alat }}</span><br>
                        <small class="text-muted">S/N: {{ $alat->nomor_seri ?? '-' }} | Loc: {{ $alat->lokasi }}</small>
                    </td>
                    <td class="align-middle">
                        <select name="alat[{{ $alat->id }}][status]" class="form-control form-control-sm border-secondary" required>
                            <option value="Aktif" {{ $alat->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Non-Aktif" {{ $alat->status == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                    </td>
                    <td class="align-middle">
                        <select name="alat[{{ $alat->id }}][kondisi_fisik]" class="form-control form-control-sm border-secondary" required>
                            <option value="Baik">Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </td>
                    <td class="align-middle">
                        <textarea name="alat[{{ $alat->id }}][catatan_khusus]" class="form-control form-control-sm" rows="1" placeholder="Masukkan keterangan..."></textarea>
                    </td>
                    <td class="align-middle text-center">
                        <div class="custom-file">
                            <input type="file" name="alat[{{ $alat->id }}][foto_kegiatan]" class="custom-file-input" id="foto_{{ $alat->id }}" accept="image/*">
                            <label class="custom-file-label small text-left shadow-sm" for="foto_{{ $alat->id }}">Ambil Foto</label>
                        </div>
                    </td>
                </tr>
            @endforeach
        @endforeach
        
    @empty
        <tr>
            <td colspan="5" class="text-center text-muted py-5">
                <i class="fas fa-clipboard-check fa-3x mb-3 opacity-25"></i>
                <br>Tidak ada data alat yang perlu dicek.
            </td>
        </tr>
    @endforelse
</tbody>
                    </table>
                </div>
            </div>
            
            <div class="card-footer bg-white d-flex justify-content-end py-3">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm px-4 mr-2">Batal</a>
                <button type="submit" class="btn btn-primary btn-sm px-4" style="background-color: #003366;">
                    Simpan Hasil
                </button>
            </div>
        </div>
    </form>
</div>
@endsection