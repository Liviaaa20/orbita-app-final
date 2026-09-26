@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0 text-center font-weight-bold">Input Permintaan Perbaikan</h5>
                    <p class="text-muted small text-center mb-0">Lengkapi detail informasi alat di bawah ini!</p>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('perbaikan.store') }}" method="POST" enctype="multipart/form-data" id="formPerbaikan">
                        @csrf
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Tanggal Permintaan</label>
                                    <input type="text" class="form-control bg-light" value="{{ now()->format('d/m/Y') }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">User Pelapor</label>
                                    <input type="text" class="form-control bg-light" value="{{ Auth::user()->name }}" readonly>
                                </div>
                            </div>
                        </div>

                        {{-- ============================================================ --}}
                        {{-- REVISI: Dropdown Bertingkat Kategori -> Sub Kategori -> Alat   --}}
                        {{-- Semua data Kategori/SubKategori/Alat sudah di-load dari         --}}
                        {{-- controller (volume kecil), filter dilakukan murni di JS tanpa   --}}
                        {{-- request AJAX tambahan.                                          --}}
                        {{-- ============================================================ --}}
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Kategori Alat</label>
                            <select id="selectKategori" class="form-control">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}">
                                        {{ $kategori->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Pilih kategori alat terlebih dahulu.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Sub Kategori</label>
                            <select id="selectSubKategori" class="form-control" disabled>
                                <option value="">-- Pilih Kategori Dahulu --</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Data Alat (Opsional)</label>
                            <select name="alat_id" id="selectAlat" class="form-control @error('alat_id') is-invalid @enderror" disabled>
                                <option value="">-- Pilih Sub Kategori Dahulu --</option>
                            </select>
                            <small class="text-muted italic">
                                Pilih "Gangguan Umum" pada opsi terakhir jika kerusakan tidak terkait alat spesifik.
                            </small>
                            @error('alat_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Data sumber lengkap untuk filtering JS. Disimpan sebagai JSON
                             di dalam atribut data-* pada elemen tersembunyi, supaya tidak
                             bercampur dengan logic Blade di bagian script. --}}
                        <script type="application/json" id="dataSubKategori">
                            {!! $subKategoris->map(function ($sk) {
                                return [
                                    'id' => $sk->id,
                                    'kategori_id' => $sk->kategori_id,
                                    'nama' => $sk->nama_sub_kategori,
                                ];
                            })->values()->toJson() !!}
                        </script>

                        <script type="application/json" id="dataAlat">
                            {!! $alats->map(function ($a) {
                                return [
                                    'id' => $a->id,
                                    'sub_kategori_id' => $a->sub_kategori_id,
                                    'nama' => $a->nama_alat,
                                    'lokasi' => $a->lokasi,
                                ];
                            })->values()->toJson() !!}
                        </script>

                        {{-- Ringkasan pilihan -> otomatis mengisi kategori_perbaikan (readonly) --}}
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Kategori Singkat</label>
                            <input type="text"
                                   name="kategori_perbaikan"
                                   id="inputKategoriPerbaikan"
                                   class="form-control bg-light @error('kategori_perbaikan') is-invalid @enderror"
                                   placeholder="Otomatis terisi dari pilihan Kategori / Sub Kategori / Alat di atas"
                                   value="{{ old('kategori_perbaikan') }}"
                                   readonly
                                   required>
                            <small class="text-muted">Terisi otomatis berdasarkan Kategori, Sub Kategori, dan Alat yang dipilih.</small>
                            @error('kategori_perbaikan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Keterangan Detail Kerusakan</label>
                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="4" placeholder="Jelaskan secara detail kronologi atau gejala kerusakan..." required>{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Foto Bukti Kerusakan</label>
                            <div class="custom-file">
                                <input type="file" name="foto" class="custom-file-input @error('foto') is-invalid @enderror" id="customFile" accept="image/*">
                                <label class="custom-file-label" for="customFile">Pilih Gambar...</label>
                            </div>
                            <small class="text-muted">Format: JPG/PNG, maks 2MB</small>
                            @error('foto')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('perbaikan.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm">
                                <i class="fas fa-paper-plane mr-1"></i> Kirim Permintaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Agar nama file muncul saat dipilih
    $('#customFile').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    /* =========================================================
       DROPDOWN BERTINGKAT: Kategori -> Sub Kategori -> Alat
       Filter murni via JavaScript, tanpa request AJAX tambahan,
       karena volume data Kategori/SubKategori/Alat relatif kecil.
       ========================================================= */
    document.addEventListener('DOMContentLoaded', function () {

        var dataSubKategori = JSON.parse(document.getElementById('dataSubKategori').textContent);
        var dataAlat         = JSON.parse(document.getElementById('dataAlat').textContent);

        var $selectKategori    = $('#selectKategori');
        var $selectSubKategori = $('#selectSubKategori');
        var $selectAlat        = $('#selectAlat');
        var $inputKategoriPerbaikan = $('#inputKategoriPerbaikan');

        function resetSelect($select, placeholderText, disabled) {
            $select.html('<option value="">' + placeholderText + '</option>');
            $select.prop('disabled', disabled);
        }

        // Saat Kategori dipilih -> isi ulang Sub Kategori, reset Alat
        $selectKategori.on('change', function () {

            var kategoriId = $(this).val();

            resetSelect($selectAlat, '-- Pilih Sub Kategori Dahulu --', true);

            if (!kategoriId) {
                resetSelect($selectSubKategori, '-- Pilih Kategori Dahulu --', true);
                updateKategoriPerbaikan();
                return;
            }

            var filtered = dataSubKategori.filter(function (sk) {
                return String(sk.kategori_id) === String(kategoriId);
            });

            $selectSubKategori.html('<option value="">-- Pilih Sub Kategori --</option>');

            if (filtered.length === 0) {
                $selectSubKategori.append('<option value="" disabled>Tidak ada sub kategori untuk kategori ini</option>');
            } else {
                filtered.forEach(function (sk) {
                    $selectSubKategori.append(
                        $('<option>', { value: sk.id, text: sk.nama, 'data-nama': sk.nama })
                    );
                });
            }

            $selectSubKategori.prop('disabled', false);
            updateKategoriPerbaikan();
        });

        // Saat Sub Kategori dipilih -> isi ulang Alat
        $selectSubKategori.on('change', function () {

            var subKategoriId = $(this).val();

            if (!subKategoriId) {
                resetSelect($selectAlat, '-- Pilih Sub Kategori Dahulu --', true);
                updateKategoriPerbaikan();
                return;
            }

            var filteredAlat = dataAlat.filter(function (a) {
                return String(a.sub_kategori_id) === String(subKategoriId);
            });

            $selectAlat.html('<option value="">-- Pilih Alat (Opsional) --</option>');

            filteredAlat.forEach(function (a) {
                var label = a.nama + (a.lokasi ? ' (' + a.lokasi + ')' : '');
                $selectAlat.append(
                    $('<option>', { value: a.id, text: label, 'data-nama': a.nama })
                );
            });

            // Opsi terakhir: gangguan umum, tidak terkait alat spesifik
            $selectAlat.append(
                $('<option>', { value: '', text: '-- Gangguan Umum (Bukan Alat Spesifik) --', 'data-nama': '' })
            );

            $selectAlat.prop('disabled', false);
            updateKategoriPerbaikan();
        });

        // Saat Alat dipilih -> update ringkasan
        $selectAlat.on('change', function () {
            updateKategoriPerbaikan();
        });

        // Susun teks ringkasan "Kategori > Sub Kategori > Alat" ke field kategori_perbaikan
        function updateKategoriPerbaikan() {

            var namaKategori    = $selectKategori.find('option:selected').text();
            var namaSubKategori = $selectSubKategori.find('option:selected').text();
            var namaAlat        = $selectAlat.find('option:selected').text();

            var parts = [];

            if ($selectKategori.val()) {
                parts.push(namaKategori);
            }
            if ($selectSubKategori.val()) {
                parts.push(namaSubKategori);
            }
            if ($selectAlat.val() && namaAlat.indexOf('Gangguan Umum') === -1) {
                parts.push(namaAlat);
            }

            $inputKategoriPerbaikan.val(parts.join(' > '));
        }

    });
</script>
@endpush