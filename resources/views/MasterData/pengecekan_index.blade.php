@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h1 class="h3 mb-4 text-navy font-weight-bold">
                 Pengecekan {{ ucfirst($type) }}
            </h1>

            <form action="{{ route('maintenance.store-inisiasi') }}" method="POST">
                @csrf
                
                <input type="hidden" name="tanggal" value="{{ $tanggal ?? old('tanggal', date('Y-m-d')) }}">
                <input type="hidden" name="type" value="{{ $type ?? old('type', 'harian') }}">
                
                <div class="card shadow-sm border-0" style="border-radius: 12px;">
                    <div class="card-body px-5 py-4">
                        
                        {{-- Dropdown Waktu --}}
                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Pilih {{ $type == 'harian' ? 'Shift' : 'Jadwal Hari' }}</label>
                            <select name="waktu" id="selectWaktu" class="form-control custom-select bg-light shadow-sm" style="border-radius: 8px;" required>
                                @if(($type ?? 'harian') == 'harian')
                                    <option value="Pagi" {{ $waktu == 'Pagi' ? 'selected' : '' }}>Pagi</option>
                                    <option value="Siang" {{ $waktu == 'Siang' ? 'selected' : '' }}>Siang</option>
                                    <option value="Malam" {{ $waktu == 'Malam' ? 'selected' : '' }}>Malam</option>
                                @else
                                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                        <option value="{{ $hari }}" {{ $waktu == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        {{-- Tabel Pengecekan dengan Accordion --}}
                        <div class="border rounded shadow-sm" style="overflow: hidden;">
                            <div class="bg-light p-3 text-center border-bottom">
                                <h6 class="mb-1 font-weight-bold text-dark">Daftar Lokasi Pengecekan</h6>
                                <small class="text-muted">Ceklis lokasi yang akan dilakukan pemeliharaan {{ $type }}</small>
                            </div>

                            <div id="accordionPengecekan">
                                @foreach($kategoris as $kat)
                                <div class="border-bottom">
                                    <div class="bg-white p-0" id="heading{{ $kat->id }}">
                                        <button class="btn btn-link btn-block text-left text-dark d-flex justify-content-between align-items-center py-3 px-4 font-weight-bold" 
                                                type="button" data-toggle="collapse" data-target="#collapse{{ $kat->id }}" 
                                                style="text-decoration: none; font-size: 1.1rem;">
                                            {{ $kat->nama_kategori }}
                                            <i class="fas fa-chevron-down small text-muted"></i>
                                        </button>
                                    </div>

                                    <div id="collapse{{ $kat->id }}" class="collapse" data-parent="#accordionPengecekan">
                                        <div class="card-body p-0">
                                            <table class="table table-hover mb-0">
                                                <thead class="bg-gray-100 small font-weight-bold text-uppercase">
                                                    <tr>
                                                        <th width="85%" class="pl-4">Titik Lokasi</th>
                                                        <th class="text-center">Ceklis</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $daftarLokasi = $kat->subKategoris->flatMap->alats->pluck('lokasi')->unique();
                                                    @endphp

                                                    @forelse($daftarLokasi as $index => $lokasi)
                                                    <tr>
                                                        <td class="pl-4 align-middle">
                                                            <i class="fas fa-map-marker-alt text-primary mr-2"></i> {{ $lokasi }}
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <div class="custom-control custom-checkbox scale-checkbox">
                                                                {{-- Auto-checked dicopot, dan name disesuaikan agar Kategori_ID ikut terkirim --}}
                                                                <input type="checkbox" name="lokasi[{{ $kat->id }}][]" value="{{ $lokasi }}" 
                                                                       class="custom-control-input" id="check{{ $kat->id }}-{{ $index }}">
                                                                <label class="custom-control-label" for="check{{ $kat->id }}-{{ $index }}"></label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="2" class="text-center text-muted py-3">Tidak ada alat untuk jadwal ini</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Tombol --}}
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary px-5 mr-2 font-weight-bold shadow-sm" style="border-radius: 8px;">Batal</a>
                            <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow-sm" style="border-radius: 8px;">
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection