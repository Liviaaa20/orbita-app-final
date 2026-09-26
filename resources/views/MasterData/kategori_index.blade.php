@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-bold text-dark" style="letter-spacing: -0.02em;">Data Kategori</h5>
                        @if(auth()->user()->canManageMasterData())
                        <button class="btn btn-dark btn-sm px-3 font-weight-medium"
                        style="border-radius: 6px;"
                        data-toggle="modal"
                        data-target="#modalTambahKategori">
                         <i class="fas fa-plus mr-2"></i> Tambah Kategori
                        </button>
                        @endif                    
                    </div>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <table id="table-kategori" class="table table-hover align-middle mb-0" style="width:100%;">
                        <thead class="bg-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                            <tr>
                                <th width="100px" class="text-center border-bottom-0 py-3">ID</th>
                                <th class="border-bottom-0 py-3">Kategori</th>
                                <th width="150px" class="text-center border-bottom-0 py-3">Jenis</th>
                                <th width="120px" class="text-center border-bottom-0 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kategori as $item)
                            <tr>
                                <td class="text-center font-weight-bold text-secondary py-3">{{ $item->kode_kategori }}</td>
                                <td class="text-dark font-weight-medium py-3">{{ $item->nama_kategori }}</td>
                                <td class="text-center py-3">
                                    <span class="badge {{ $item->jenis == 'Sistem' ? 'badge-primary' : 'badge-secondary' }} px-3 py-2" style="border-radius: 6px; font-weight: 600; font-size: 0.75rem;">
                                        {{ $item->jenis }}
                                    </span>
                                </td>
                                <td class="text-center py-3">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-link text-secondary btn-view" 
                                            data-toggle="modal" 
                                            data-target="#modalDetailKategori"
                                            data-kode="{{ $item->kode_kategori }}"
                                            data-nama="{{ $item->nama_kategori }}"
                                            data-jenis="{{ $item->jenis }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if(auth()->user()->canManageMasterData())
                                        <button class="btn btn-sm btn-link text-secondary btn-edit" 
                                            data-toggle="modal" 
                                            data-target="#modalEditKategori"
                                            data-id="{{ $item->id }}"
                                            data-kode="{{ $item->kode_kategori }}"
                                            data-nama="{{ $item->nama_kategori }}"
                                            data-jenis="{{ $item->jenis }}">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        @endif
                                        @if(auth()->user()->canManageMasterData())
                                        <button class="btn btn-sm btn-link text-danger btn-delete"
                                            data-toggle="modal"
                                            data-target="#modalDeleteKategori"
                                            data-id="{{ $item->id }}"
                                            data-kode="{{ $item->kode_kategori }}"
                                            data-nama="{{ $item->nama_kategori }}"
                                            data-jenis="{{ $item->jenis }}">
                                            <i class="fas fa-trash"></i>
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

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambahKategori" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-dark">Form Tambah Kategori</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('kategori.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="form-group mb-3">
                        <label class="text-secondary font-weight-medium mb-1" style="font-size: 0.85rem;">Kode Kategori</label>
                        <input type="text" name="kode_kategori" class="form-control" placeholder="Contoh: K001" style="border-radius: 6px;" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-secondary font-weight-medium mb-1" style="font-size: 0.85rem;">Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control" style="border-radius: 6px;" required>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-secondary font-weight-medium mb-1" style="font-size: 0.85rem;">Jenis</label>
                        <select name="jenis" class="form-control" style="border-radius: 6px;" required>
                            <option value="Sistem">Sistem</option>
                            <option value="Non Sistem">Non Sistem</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4 font-weight-medium" style="border-radius: 6px;" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark px-4 font-weight-medium" style="border-radius: 6px;">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div class="modal fade" id="modalDetailKategori" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-dark">Detail Kategori</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body px-4 pb-4">
                <table class="table table-sm table-borderless mb-0">
                    <tr class="border-bottom"><th width="150" class="text-secondary py-2">Kode</th><td id="view_kode" class="text-dark font-weight-bold py-2"></td></tr>
                    <tr class="border-bottom"><th class="text-secondary py-2">Nama Kategori</th><td id="view_nama" class="text-dark py-2"></td></tr>
                    <tr><th class="text-secondary py-2">Jenis</th><td class="py-2"><span id="view_jenis"></span></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal fade" id="modalEditKategori" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-dark">Edit Kategori</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formEditKategori" method="POST">
                @csrf @method('PUT')
                <div class="modal-body px-4">
                    <div class="form-group mb-3">
                        <label class="text-secondary font-weight-medium mb-1" style="font-size: 0.85rem;">Kode</label>
                        <input type="text" id="edit_kode" class="form-control bg-light" style="border-radius: 6px;" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-secondary font-weight-medium mb-1" style="font-size: 0.85rem;">Nama Kategori</label>
                        <input type="text" name="nama_kategori" id="edit_nama" class="form-control" style="border-radius: 6px;" required>
                    </div>
                    <div class="form-group mb-2">
                        <label class="text-secondary font-weight-medium mb-1" style="font-size: 0.85rem;">Jenis</label>
                        <select name="jenis" id="edit_jenis" class="form-control" style="border-radius: 6px;">
                            <option value="Sistem">Sistem</option>
                            <option value="Non Sistem">Non Sistem</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4 font-weight-medium" style="border-radius: 6px;" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark px-4 font-weight-medium" style="border-radius: 6px;">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDeleteKategori" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <form id="formDeleteKategori" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title font-weight-bold text-dark">
                        <i class="fas fa-trash-alt text-danger mr-2"></i> Hapus Kategori
                    </h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body text-center px-4">
                    <i class="fas fa-exclamation-circle text-danger fa-3x mb-3"></i>
                    <h6 class="font-weight-bold text-dark mb-3" style="font-size: 1.05rem;">Apakah Anda yakin ingin menghapus kategori ini?</h6>
                    
                    <div class="alert alert-light border text-left p-3" style="border-radius: 8px; font-size: 0.9rem;">
                        <p class="mb-1 text-secondary"><strong>Kode :</strong> <span id="delete_kode" class="text-dark font-weight-bold"></span></p>
                        <p class="mb-1 text-secondary"><strong>Nama :</strong> <span id="delete_nama" class="text-dark"></span></p>
                        <p class="mb-0 text-secondary"><strong>Jenis :</strong> <span id="delete_jenis"></span></p>
                    </div>
                    <small class="text-muted d-block mt-3">Data yang dihapus tidak dapat dipulihkan kembali.</small>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4 justify-content-center">
                    <button type="button" class="btn btn-light px-4 font-weight-medium" style="border-radius: 6px;" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 font-weight-medium" style="border-radius: 6px;">Ya, Hapus Data</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        // Inisialisasi DataTable
        if (!$.fn.DataTable.isDataTable('#table-kategori')) {
            var table = $('#table-kategori').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "language": {
                    "search": "Cari:",
                    "paginate": {
                        "previous": "<i class='fas fa-angle-left'></i>",
                        "next": "<i class='fas fa-angle-right'></i>"
                    }
                }
            });
            table.buttons().container().appendTo('#table-kategori_wrapper .col-md-6:eq(0)');
            $('#table-kategori_wrapper .col-md-6:eq(1)').addClass('text-right');
            $('#table-kategori_wrapper .row:eq(2) .col-md-7').addClass('d-flex justify-content-end');
        }

        // JS Handler: View Modal
        $(document).on('click', '.btn-view', function() {
            var kode  = $(this).attr('data-kode');
            var nama  = $(this).attr('data-nama');
            var jenis = $(this).attr('data-jenis');

            $('#view_kode').text(kode);
            $('#view_nama').text(nama);
            
            var badgeClass = (jenis === 'Sistem') ? 'badge badge-primary px-3 py-1' : 'badge badge-secondary px-3 py-1';
            $('#view_jenis').text(jenis).attr('class', badgeClass).css('border-radius', '6px');
        });

        // JS Handler: Edit Modal
        $(document).on('click', '.btn-edit', function() {
            var id    = $(this).attr('data-id');
            var kode  = $(this).attr('data-kode');
            var nama  = $(this).attr('data-nama');
            var jenis = $(this).attr('data-jenis');

            $('#edit_kode').val(kode);
            $('#edit_nama').val(nama);
            $('#edit_jenis').val(jenis);
            
            $('#formEditKategori').attr('action', '/kategori/' + id);
        });

        // BARU - JS Handler: Delete Modal Dinamis
        $(document).on('click', '.btn-delete', function() {
            var id    = $(this).attr('data-id');
            var kode  = $(this).attr('data-kode');
            var nama  = $(this).attr('data-nama');
            var jenis = $(this).attr('data-jenis');

            $('#delete_kode').text(kode);
            $('#delete_nama').text(nama);
            
            var badgeClass = (jenis === 'Sistem') ? 'badge badge-primary px-2 py-1' : 'badge badge-secondary px-2 py-1';
            $('#delete_jenis').text(jenis).attr('class', badgeClass).css('border-radius', '4px');
            
            // Set action URL form secara dinamis menuju Route Destroy
            $('#formDeleteKategori').attr('action', '/kategori/' + id);
        });
    });
</script>
@endpush