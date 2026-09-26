@extends('layouts.master')

@section('content')
<style>
    /* Styling Tabel Garis Tegas */
    .table-bordered {
        border: 1px solid #dee2e6 !important;
    }
    .table-bordered th, 
    .table-bordered td {
        border: 1px solid #dee2e6 !important;
        vertical-align: middle !important;
    }
    
    /* Header Tabel */
    .table-sub-kategori thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        color: #495057;
        text-align: center;
    }

    /* Baris Kategori Utama (Parent Dropdown) */
    .tr-kategori {
        background-color: #f1f5f9 !important;
        cursor: pointer;
        user-select: none;
    }
    .tr-kategori:hover {
        background-color: #e2e8f0 !important;
    }

    /* Animasi Icon Panah */
    .rotate-icon {
        transition: transform 0.3s ease;
    }
    .tr-kategori[aria-expanded="false"] .rotate-icon {
        transform: rotate(-90deg);
    }

    /* Indentasi Sub Kategori */
    .indent-sub {
        padding-left: 45px !important;
        background-color: #ffffff;
    }
    
    .kode-badge {
        font-family: 'Consolas', 'Monaco', monospace;
        background: #f1f1f1;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.85rem;
        border: 1px solid #ddd;
        color: #333;
    }

    /* Memastikan modal tampil di atas segalanya */
    .modal { z-index: 1060 !important; }
    .modal-backdrop { z-index: 1050 !important; }
    .modal-body #add_row {
    position: relative;
    z-index: 1070;
    pointer-events: auto !important;}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 font-weight-bold text-dark">Data Sub Kategori</h5>
                            <small class="text-muted">Kelola sub-peralatan berdasarkan kategori utama</small>
                        </div>
                        @if(auth()->user()->canManageMasterData())
                        <button class="btn btn-dark btn-sm px-4" data-toggle="modal" data-target="#modalTambahSub" style="border-radius: 8px;">
                            <i class="fas fa-plus mr-2"></i> Tambah Sub Kategori
                        </button>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                    <div class="card-body p-3">

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text"
                                id="searchSubKategori"
                                class="form-control"
                                placeholder="Cari sub kategori...">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="subKategoriTable" class="table table-bordered mb-0 table-sub-kategori">                            <thead>
                                <tr>
                                    <th width="15%">ID Sub</th>
                                    <th width="70%">Nama Sub Kategori</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kategoriWithSub as $kat)
                                    <!-- Baris Kategori Utama -->
                                    <tr class="tr-kategori" data-toggle="collapse" data-target=".group-{{ $kat->id }}" aria-expanded="true">
                                        <td class="text-center font-weight-bold bg-light">
                                            {{ $kat->kode_kategori }}
                                        </td>
                                        <td colspan="2" class="font-weight-bold">
                                            <i class="fas fa-chevron-down mr-2 rotate-icon text-muted"></i>
                                            <i class="fas fa-folder-open text-warning mr-1"></i> 
                                            {{ $kat->nama_kategori }}
                                            <span class="badge badge-pill badge-light border ml-2 text-muted" style="font-size: 0.7rem;">
                                                {{ $kat->subKategoris->count() }} Sub
                                            </span>
                                        </td>
                                    </tr>
                                     <!-- MODAL DELETE -->
<div class="modal fade" id="modalDeleteSub" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formDeleteSub" method="POST">
            @csrf
            @method('DELETE')

            <div class="modal-content border-0 shadow">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-trash-alt mr-2"></i>
                        Hapus Sub Kategori
                    </h5>

                    <button type="button"
                            class="close text-white"
                            data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body text-center">

                    <i class="fas fa-exclamation-triangle text-warning fa-4x mb-3"></i>

                    <h5 class="font-weight-bold">
                        Yakin ingin menghapus sub kategori ini?
                    </h5>

                    <div class="alert alert-light border text-left mt-3">

                        <p class="mb-2">
                            <strong>Kode :</strong>
                            <span id="delete_kode_sub"></span>
                        </p>

                        <p class="mb-2">
                            <strong>Sub Kategori :</strong>
                            <span id="delete_nama_sub"></span>
                        </p>

                        <p class="mb-0">
                            <strong>Kategori :</strong>
                            <span id="delete_kategori_sub"></span>
                        </p>

                    </div>

                    <small class="text-muted">
                        Data yang sudah dihapus tidak dapat dikembalikan.
                    </small>

                </div>

                <div class="modal-footer justify-content-center">

                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt mr-1"></i>
                        Ya, Hapus
                    </button>

                </div>

            </div>
        </form>
    </div>
</div>  

                                    <!-- Baris Sub Kategori -->
                                    @forelse($kat->subKategoris as $sub)
                                        <tr class="collapse show group-{{ $kat->id }}">
                                            <td class="text-center">
                                                <span class="kode-badge">{{ $sub->kode_sub_kategori }}</span>
                                            </td>
                                            <td class="indent-sub text-dark">
                                                {{ $sub->nama_sub_kategori }}
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-action-container">

                                                    {{-- View --}}
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm text-info btn-view-sub"
                                                        title="Lihat"
                                                        data-kode="{{ $sub->kode_sub_kategori }}"
                                                        data-nama="{{ $sub->nama_sub_kategori }}"
                                                        data-kategori="{{ $kat->nama_kategori }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>

                                                    @if(auth()->user()->canManageMasterData())
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm text-primary btn-edit-sub"
                                                        data-id="{{ $sub->id }}"
                                                        data-nama="{{ $sub->nama_sub_kategori }}"
                                                        data-kode="{{ $sub->kode_sub_kategori }}"
                                                        data-kategori="{{ $kat->id }}"
                                                        title="Edit">
                                                        <i class="fas fa-pen"></i>
                                                    </button>
                                                    @endif

                                                    @if(auth()->user()->canManageMasterData())
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm text-danger btn-delete-sub"
                                                        data-id="{{ $sub->id }}"
                                                        data-kode="{{ $sub->kode_sub_kategori }}"
                                                        data-nama="{{ $sub->nama_sub_kategori }}"
                                                        data-kategori="{{ $kat->nama_kategori }}"
                                                        title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    @endif

                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="collapse show group-{{ $kat->id }}">
                                            <td colspan="3" class="text-center text-muted small py-3">
                                                Belum ada data sub kategori untuk kategori ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalViewSub" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content shadow border-0">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye mr-2"></i>
                    Detail Sub Kategori
                </h5>

                <button class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <table class="table table-bordered">

                    <tr>
                        <th width="35%">Kode Sub Kategori</th>
                        <td id="view_kode_sub"></td>
                    </tr>

                    <tr>
                        <th>Nama Sub Kategori</th>
                        <td id="view_nama_sub"></td>
                    </tr>

                    <tr>
                        <th>Kategori</th>
                        <td id="view_kategori_sub"></td>
                    </tr>

                </table>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>

    </div>
</div>
<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambahSub" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <div class="text-center w-100">
                    <h5 class="modal-title font-weight-bold">Input Sub Kategori</h5>
                    <small class="text-muted">Tambahkan item baru ke dalam kategori terpilih</small>
                </div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('sub-kategori.store') }}" method="POST" id="formTambahSub">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Pilih Kategori Utama <span class="text-danger">*</span></label>
                        <select name="kategori_id" id="select_kategori" class="form-control" style="border-radius: 8px; background-color: #f8f9fa;" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="existing_subs_container" style="display: none;">
                        <label class="font-weight-bold">Sub Kategori yang sudah ada:</label>
                        <div id="existing_subs_list" class="border rounded p-3 mb-3" style="background-color: #f8f9fa; max-height: 150px; overflow-y: auto;">
                            <!-- Daftar sub kategori existing akan muncul di sini -->
                        </div>
                    </div>

                    <label class="font-weight-bold">Daftar Sub Kategori Baru <span class="text-danger">*</span></label>
                    <div id="sub_kategori_container" style="border: 1px solid #dee2e6; border-radius: 8px; overflow: hidden;">
                        <div class="list-group list-group-flush" id="input_list">
                            <div class="list-group-item text-center text-muted small py-3" id="placeholder_text">
                                Silakan pilih kategori terlebih dahulu, lalu tambahkan baris input
                            </div>
                        </div>
                        <button type="button" id="add_row" class="btn btn-block btn-sm py-2" style="background-color: #e9ecef; border-top: 1px solid #dee2e6; color: #495057;" disabled>
                            <i class="fas fa-plus-circle mr-1"></i> Tambah Baris Input
                        </button>
                    </div>
                    <small class="text-muted d-block mt-1">Minimal 1 baris input diisi</small>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-light rounded-pill px-5" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-5" id="submit_btn" disabled>Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEditSub" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">Edit Sub Kategori</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formEditSub" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="form-group">
                        <label class="font-weight-bold">Kategori Utama</label>
                        <select name="kategori_id" id="edit_kategori_id" class="form-control" required>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Kode Sub Kategori</label>
                        <input type="text" name="kode_sub_kategori" id="edit_kode_sub" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Sub Kategori</label>
                        <input type="text" name="nama_sub_kategori" id="edit_nama_sub" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('#searchSubKategori').on('keyup', function () {

let value = $(this).val().toLowerCase();

$('tbody tr').each(function () {

    if ($(this).text().toLowerCase().indexOf(value) > -1) {
        $(this).show();
    } else {
        if (!$(this).hasClass('tr-kategori')) {
            $(this).hide();
        }
    }

});

});
    const kategoriData = @json($kategoriWithSub);
    let inputCount = 0;

    // 1. Perbaiki Selector Anti-Collapse agar HANYA kena di tabel
    // Jangan pakai e.preventDefault() di sini karena akan mematikan tombol lain
    $(document).on('click',
    '.btn-action-container, .btn-edit-sub, .btn-delete-sub',
    function(e){
        e.stopPropagation();
    });

    // 2. Event saat Kategori dipilih
    $('#select_kategori').on('change', function() {
        const katId = $(this).val();
        
        // Load data existing (fungsi yang sudah kamu buat)
        loadExistingSubs(katId);
        
        // AKTIFKAN TOMBOL TAMBAH BARIS
        if (katId !== "") {
            $('#add_row').removeAttr('disabled').css('cursor', 'pointer');
            console.log("Tombol Tambah Baris Aktif"); 
        } else {
            $('#add_row').attr('disabled', 'disabled');
        }
    });

    // 3. Logika Klik Tombol Tambah Baris
    // Gunakan direct binding agar lebih kuat
    $('#add_row').on('click', function(e) {
        e.preventDefault(); // Mencegah form submit tidak sengaja
        
        const katId = $('#select_kategori').val();
        if (katId) {
            inputCount++;
            $('#placeholder_text').hide();
            
            const row = `
                <div class="list-group-item d-flex align-items-center py-2 input-row">
                    <input type="text" name="nama_sub_kategori[]" class="form-control form-control-sm flex-grow-1 mr-2" 
                           placeholder="Ketik nama sub kategori baru..." required>
                    <button type="button" class="btn btn-link btn-sm text-danger remove-row" title="Hapus baris">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            $('#input_list').append(row);
            $('#submit_btn').removeAttr('disabled'); // Aktifkan tombol simpan
        }
    });

    // 4. Hapus Baris
    $(document).on('click', '.remove-row', function() {
        $(this).closest('.input-row').remove();
        const remaining = $('.input-row').length;
        if (remaining === 0) {
            $('#placeholder_text').show();
            $('#submit_btn').attr('disabled', 'disabled');
        }
    });

    // --- SISANYA TETAP SAMA (Fungsi loadExistingSubs & Modal Edit) ---
    function loadExistingSubs(katId) {
        const container = $('#existing_subs_list');
        const existingContainer = $('#existing_subs_container');
        container.empty();
        if (katId) {
            const selectedKat = kategoriData.find(k => k.id == katId);
            if (selectedKat && selectedKat.subKategoris && selectedKat.subKategoris.length > 0) {
                selectedKat.subKategoris.forEach(sub => {
                    container.append(`
                        <div class="d-flex justify-content-between align-items-center py-1 small">
                            <span><i class="fas fa-check-circle text-success mr-1"></i>${sub.nama_sub_kategori}</span>
                            <span class="badge badge-light">${sub.kode_sub_kategori}</span>
                        </div>
                    `);
                });
                existingContainer.show();
            } else { existingContainer.hide(); }
        } else { existingContainer.hide(); }
    }
// Modal View
    $(document).on('click','.btn-view-sub',function(){

    $('#view_kode_sub').text($(this).data('kode'));
    $('#view_nama_sub').text($(this).data('nama'));
    $('#view_kategori_sub').text($(this).data('kategori'));

    $('#modalViewSub').modal('show');

    });
    $(document).on('click', '.btn-edit-sub', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const kode = $(this).data('kode');
        const katId = $(this).data('kategori');
        $('#edit_id').val(id);
        $('#edit_kategori_id').val(katId);
        $('#edit_kode_sub').val(kode);
        $('#edit_nama_sub').val(nama);
        $('#formEditSub').attr('action', `/sub-kategori/${id}`);
        $('#modalEditSub').modal('show');
    });
    // Modal Delete
$(document).on('click', '.btn-delete-sub', function () {

    const id = $(this).data('id');
    const kode = $(this).data('kode');
    const nama = $(this).data('nama');
    const kategori = $(this).data('kategori');

    $('#delete_kode_sub').text(kode);
    $('#delete_nama_sub').text(nama);
    $('#delete_kategori_sub').text(kategori);

    $('#formDeleteSub').attr('action', `/sub-kategori/${id}`);

    $('#modalDeleteSub').modal('show');
});
});
</script>
@endpush