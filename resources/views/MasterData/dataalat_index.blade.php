@extends('layouts.master')

@push('styles')
<style>
    /* ── Fix gap DataTables ── */
    #example1_wrapper .dataTables_scroll { overflow: visible; }
    #example1 { border-collapse: collapse !important; }
    #example1 thead tr th,
    #example1 tbody tr td {
        padding: 10px 12px !important;
        vertical-align: middle !important;
        white-space: nowrap;
    }
    #example1 thead tr th { background-color: #f8f9fa; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Notifikasi --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <strong>Terjadi Kesalahan!</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- Judul & Tombol Tambah --}}
    <div class="row mb-3 align-items-center">
        <div class="col-md-6">
            <h3 class="m-0 text-navy font-weight-bold">Data Alat ORBITA</h3>
        </div>
        <div class="col-md-6 text-right">
            @if(auth()->user()->canManageMasterData())
            <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahAlat">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Alat
            </button>
            @endif
        </div>
    </div>

    {{-- Tabel Utama --}}
    <div class="card card-outline card-navy shadow-sm">
        <div class="card-body p-0">
            <div style="overflow-x: auto; padding: 1rem;">
                <table id="example1" class="table table-bordered table-hover mb-0" style="width:100%">
                    <thead class="bg-light text-center">
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Kategori</th>
                            <th>Nama Alat</th>
                            <th>No. Seri</th>
                            <th>Jenis</th>
                            <th>Lokasi</th>
                            <th>Merk/Type</th>
                            <th>Thn Pengadaan</th>
                            <th>Rentang Ukur</th>
                            <th>Resolusi</th>
                            <th>Akurasi</th>
                            <th>Status</th>
                            <th>Kondisi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alats as $key => $item)
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td class="text-center">
                                @if($item->foto_alat)
                                    <img src="{{ asset('assets/img/alat/'.$item->foto_alat) }}" width="50" height="50" style="object-fit:cover" class="img-thumbnail shadow-sm">
                                @else
                                    <div class="bg-light border rounded mx-auto d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
                                        <i class="fas fa-camera text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $item->subKategori->kategori->nama_kategori ?? '-' }}
                                </span>
                            </td>
                            <td class="text-navy font-weight-bold">{{ $item->nama_alat }}</td>
                            <td><code>{{ $item->nomor_seri }}</code></td>
                            <td>{{ $item->jenis }}</td>
                            <td>{{ $item->lokasi }}</td>
                            <td>{{ $item->merk_type ?? '-' }}</td>
                            <td class="text-center">{{ $item->tahun_pengadaan ?? '-' }}</td>
                            <td>{{ $item->rentang_ukur ?? '-' }}</td>
                            <td>{{ $item->resolusi ?? '-' }}</td>
                            <td>{{ $item->akurasi ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge {{ in_array($item->status, ['Aktif', 'Operasional']) ? 'badge-success' : 'badge-danger' }} px-2 py-1">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="font-weight-bold text-sm 
                                    @if($item->kondisi == 'Baik') text-success 
                                    @elseif($item->kondisi == 'Rusak Ringan') text-warning 
                                    @elseif($item->kondisi == 'Rusak Berat') text-danger 
                                    @endif">
                                    ● {{ $item->kondisi }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalViewAlat{{ $item->id }}" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if(auth()->user()->canManageMasterData())
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEditAlat{{ $item->id }}" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" title="Hapus" data-toggle="modal" data-target="#modalDeleteAlat{{ $item->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        {{-- MODAL DELETE --}}
    <div class="modal fade" id="modalDeleteAlat{{ $item->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('data-alat.destroy', $item->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-trash-alt mr-2"></i>
                            Hapus Data Alat
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fas fa-exclamation-triangle text-warning fa-4x mb-3"></i>
                        <h5 class="font-weight-bold">Yakin ingin menghapus alat ini?</h5>
                        <div class="alert alert-light border text-left mt-3 mb-0">
                            <p class="mb-2"><strong>Nama Alat :</strong> {{ $item->nama_alat }}</p>
                            <p class="mb-2"><strong>No. Seri :</strong> {{ $item->nomor_seri }}</p>
                            <p class="mb-2"><strong>Kategori :</strong> {{ $item->subKategori->kategori->nama_kategori ?? '-' }}</p>
                            <p class="mb-0"><strong>Sub Kategori :</strong> {{ $item->subKategori->nama_sub_kategori ?? '-' }}</p>
                        </div>
                        <small class="text-muted d-block mt-3">
                            Data alat yang dihapus tidak dapat dikembalikan.
                        </small>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt mr-1"></i> Ya, Hapus
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambahAlat" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('data-alat.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-navy text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-plus-circle mr-2"></i> Tambah Alat Baru</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body bg-light px-4">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="font-weight-bold text-navy">Foto Alat</label>
                            <div class="custom-file">
                                <input type="file" name="foto_alat" class="custom-file-input" id="customFile" accept="image/*" capture="camera">
                                <label class="custom-file-label" for="customFile">Pilih file atau ambil foto...</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-navy">Kategori Utama</label>
                            <select id="select_kategori_utama" class="form-control" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoris as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-navy">Sub Kategori</label>
                            <select name="sub_kategori_id" id="sub_kategori_id" class="form-control" required>
                                <option value="">-- Pilih Kategori Dulu --</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-navy">Nama Alat</label>
                            <input type="text" name="nama_alat" id="nama_alat_otomatis" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-navy">Nomor Seri</label>
                            <input type="text" name="nomor_seri" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-navy">Jenis</label>
                            <select name="jenis" class="form-control">
                                <option value="Harian">Harian</option>
                                <option value="Mingguan">Mingguan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-navy">Lokasi</label>
                            <input type="text" name="lokasi" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-navy">Merk / Type</label>
                            <input type="text" name="merk_type" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-navy">Tahun Pengadaan</label>
                            <input type="number" name="tahun_pengadaan" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold text-navy">Rentang Ukur</label>
                            <input type="text" name="rentang_ukur" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold text-navy">Resolusi</label>
                            <input type="text" name="resolusi" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold text-navy">Akurasi</label>
                            <input type="text" name="akurasi" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-navy">Status</label>
                            <select name="status" class="form-control">
                                <option value="Aktif">Aktif</option>
                                <option value="Non-Aktif">Non-Aktif</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-navy">Kondisi</label>
                            <select name="kondisi" class="form-control">
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Data Alat</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- MODAL VIEW, EDIT & DELETE LOOP --}}
@foreach($alats as $item)

{{-- MODAL VIEW --}}
<div class="modal fade" id="modalViewAlat{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-search mr-2"></i> Detail Alat
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body px-4">
                <div class="text-center mb-4 bg-light rounded border py-3">
                    @if($item->foto_alat)
                        <img src="{{ asset('assets/img/alat/'.$item->foto_alat) }}"
                             class="img-fluid rounded"
                             style="max-height:250px;">
                    @else
                        <i class="fas fa-camera fa-3x text-muted"></i>
                    @endif
                </div>

                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="40%">Nama Alat</th>
                        <td>: {{ $item->nama_alat }}</td>
                    </tr>

                    <tr>
                        <th>Kategori</th>
                        <td>: {{ $item->subKategori->kategori->nama_kategori ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Sub Kategori</th>
                        <td>: {{ $item->subKategori->nama_sub_kategori ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>No. Seri</th>
                        <td>: <span class="badge badge-secondary">{{ $item->nomor_seri }}</span></td>
                    </tr>

                    <tr>
                        <th>Lokasi</th>
                        <td>: {{ $item->lokasi }}</td>
                    </tr>

                    <tr>
                        <th>Merk/Type</th>
                        <td>: {{ $item->merk_type ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Tahun</th>
                        <td>: {{ $item->tahun_pengadaan ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Rentang Ukur</th>
                        <td>: {{ $item->rentang_ukur ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Resolusi</th>
                        <td>: {{ $item->resolusi ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Akurasi</th>
                        <td>: {{ $item->akurasi ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>:
                            <span class="badge {{ $item->status == 'Aktif' ? 'badge-success' : 'badge-danger' }}">
                                {{ $item->status }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>Kondisi</th>
                        <td>: <strong>{{ $item->kondisi }}</strong></td>
                    </tr>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

    {{-- MODAL EDIT --}}
    @if(auth()->user()->canManageMasterData())
    <div class="modal fade" id="modalEditAlat{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('data-alat.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i> Edit Data Alat</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body bg-light px-4">
                        <div class="row">
                            <div class="col-md-12 mb-3 text-center">
                                @if($item->foto_alat)
                                    <img src="{{ asset('assets/img/alat/'.$item->foto_alat) }}" class="img-thumbnail mb-2" width="100">
                                @endif
                                <div class="custom-file text-left">
                                    <input type="file" name="foto_alat" class="custom-file-input" id="fotoEdit{{$item->id}}">
                                    <label class="custom-file-label" for="fotoEdit{{$item->id}}">Ganti foto...</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Kategori Utama</label>
                                <select class="form-control kategori-edit" data-id="{{ $item->id }}">
                                    @foreach($kategoris as $k)
                                        <option value="{{ $k->id }}" {{ ($item->subKategori->kategori_id ?? '') == $k->id ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Sub Kategori</label>
                                <select name="sub_kategori_id" id="sub_kategori_edit_{{ $item->id }}" class="form-control" required>
                                    <option value="{{ $item->sub_kategori_id }}">{{ $item->subKategori->nama_sub_kategori ?? '-' }}</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Nama Alat</label>
                                <input type="text" name="nama_alat" class="form-control nama-alat-input" value="{{ $item->nama_alat }}" required readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Nomor Seri</label>
                                <input type="text" name="nomor_seri" class="form-control" value="{{ $item->nomor_seri }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Merk/Type</label>
                                <input type="text" name="merk_type" class="form-control" value="{{ $item->merk_type }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Tahun</label>
                                <input type="number" name="tahun_pengadaan" class="form-control" value="{{ $item->tahun_pengadaan }}">
                            </div>
                            <div class="col-md-4 mb-3"><label>Rentang Ukur</label><input type="text" name="rentang_ukur" class="form-control" value="{{ $item->rentang_ukur }}"></div>
                            <div class="col-md-4 mb-3"><label>Resolusi</label><input type="text" name="resolusi" class="form-control" value="{{ $item->resolusi }}"></div>
                            <div class="col-md-4 mb-3"><label>Akurasi</label><input type="text" name="akurasi" class="form-control" value="{{ $item->akurasi }}"></div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Status</label>
                                <select name="status" class="form-control">
                                    <option value="Aktif" {{ $item->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Non-Aktif" {{ $item->status == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Kondisi</label>
                                <select name="kondisi" class="form-control">
                                    <option value="Baik" {{ $item->kondisi == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Rusak Ringan" {{ $item->kondisi == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                    <option value="Rusak Berat" {{ $item->kondisi == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning btn-block font-weight-bold">SIMPAN PERUBAHAN</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    @endif {{-- canManageMasterData --}}

@endforeach

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        // 1. Inisialisasi DataTable
        if (!$.fn.DataTable.isDataTable('#example1')) {
            var table = $('#example1').DataTable({
                "responsive": false,
                "scrollX": false,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "language": {
                    "search": "Cari:",
                    "paginate": { "previous": "Sebelumnya", "next": "Selanjutnya" },
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"
                }
            });
            table.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example1_wrapper .col-md-6:eq(1)').addClass('text-right');
        }

        // 2. Dependent Dropdown (Tambah & Edit)
        $(document).on('change', '#select_kategori_utama, .kategori-edit', function() {
            var id = $(this).val();
            var container = $(this).closest('.modal-body');
            var dropSub = container.find('select[name="sub_kategori_id"]');

            if (id) {
                dropSub.empty().append('<option value="">Memuat...</option>');
                $.get("{{ url('get-sub-kategori') }}/" + id, function(res) {
                    dropSub.empty().append('<option value="">-- Pilih Sub Kategori --</option>');
                    $.each(res, function(k, v) {
                        dropSub.append('<option value="'+v.id+'">'+v.nama_sub_kategori+'</option>');
                    });
                });
            }
        });

        // 3. Nama Alat Otomatis
        $(document).on('change', '#sub_kategori_id, [id^="sub_kategori_edit_"]', function() {
            var selectedText = $(this).find('option:selected').text();
            var container = $(this).closest('.modal-body');

            if ($(this).val() !== "" && selectedText !== "-- Pilih Sub Kategori --" && selectedText !== "Memuat...") {
                container.find('input[name="nama_alat"]').val(selectedText);
            }
        });

        // 4. Custom File Input Label
        $(document).on('change', '.custom-file-input', function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });
    });
</script>
@endpush