@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold text-dark" style="font-size: 1.6rem;">Input Jadwal Dinas</h1>
            <p class="text-muted small mb-0 mt-1">Halaman khusus Kepala kelompok untukmenambahkan penugasan tim lapangan.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-lg" role="alert">
            <i class="icon fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        {{-- SISI KIRI: FORMULIR INPUT JADWAL --}}
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0 rounded-lg overflow-hidden h-100" style="border-top: 3px solid #003366;">
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold text-dark m-0" style="font-size: 1.1rem;">Formulir Jadwal Baru</h3>
                </div>
                
                <form action="{{ route('jadwal_dinas.store') }}" method="POST" class="d-flex flex-column h-100">
                    @csrf
                    <div class="card-body text-sm flex-grow-1">
                        <div class="form-group mb-3">
                            <label for="nama" class="font-weight-bold text-muted text-uppercase tracking-wider mb-1" style="font-size: 0.7rem;">Nama Petugas</label>
                            <input type="text" name="nama" id="nama" required class="form-control rounded-lg shadow-none border" placeholder="Masukkan nama petugas">
                        </div>

                        <div class="form-group mb-3">
                            <label for="tanggal" class="font-weight-bold text-muted text-uppercase tracking-wider mb-1" style="font-size: 0.7rem;">Tanggal Dinas</label>
                            <input type="date" name="tanggal" id="tanggal" required class="form-control rounded-lg shadow-none border">
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label for="shift" class="font-weight-bold text-muted text-uppercase tracking-wider mb-1" style="font-size: 0.7rem;">Shift Kerja</label>
                                    <div class="input-group">
                                        <select name="shift_id"
                                                id="shift"
                                                required
                                                class="form-control rounded-lg shadow-none border">

                                            <option value="">-- Pilih Shift --</option>

                                            @foreach($masterShifts as $shift)
                                                <option value="{{ $shift->id }}"
                                                        data-jam="{{ \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i') }} WIB"
                                                        data-kode="{{ $shift->kode_shift }}">
                                                    {{ $shift->kode_shift }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label for="jam" class="font-weight-bold text-muted text-uppercase tracking-wider mb-1" style="font-size: 0.7rem;">Alokasi Jam</label>
                                    <input type="text" name="jam" id="jam" placeholder="Otomatis terisi..." required class="form-control rounded-lg shadow-none border bg-light" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light text-right border-0" style="gap: 8px;">
                        <a href="{{ route('jadwal_dinas.index') }}" class="btn btn-default font-weight-medium rounded-lg px-4 border">Kembali</a>
                        <button type="submit" class="btn btn-primary font-weight-bold rounded-lg px-4 shadow-sm" style="background-color: #003366; border-color: #003366;">Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>

<div class="col-md-6 mb-3">

    <div class="card shadow-sm border-0 rounded-lg h-100"
         style="border-top: 3px solid #6c757d;">

        <div class="card-header bg-white d-flex justify-content-between align-items-center">

            <h3 class="card-title font-weight-bold text-dark m-0">
                Shift
            </h3>

            <button type="button"
                    class="btn btn-success btn-sm"
                    data-toggle="modal"
                    data-target="#modalTambahShift">

                <i class="fas fa-plus"></i> Shift

            </button>

        </div>

        <div class="card-body">

            @foreach($masterShifts as $shift)

                <div class="mb-3 p-2 border rounded">

                    <div class="d-flex justify-content-between">

                        <div>
                            <span class="badge badge-primary">
                                {{ $shift->kode_shift }}
                            </span>

                            <strong>
                                {{ strtoupper($shift->nama_shift) }}
                            </strong>

                            <br>

                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i') }}
                                -
                                {{ \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i') }}
                                WIB
                            </small>

                            <br>

                            <small>
                                {{ $shift->keterangan }}
                            </small>
                        </div>

                        <div>

                        <button type="button"
                                class="btn btn-warning btn-sm"
                                data-toggle="modal"
                                data-target="#editShift{{ $shift->id }}">

                            <i class="fas fa-edit"></i>

                        </button>

                        <form action="{{ route('master_shift.destroy',$shift->id) }}"
                            method="POST"
                            class="d-inline">

                            @csrf
                            @method('DELETE')

                            <button type="button"
                                    class="btn btn-danger btn-sm"
                                    data-toggle="modal"
                                    data-target="#deleteShift{{ $shift->id }}">
                                <i class="fas fa-trash"></i>
                            </button>

                        </form>

                    </div>

                    </div>

                </div>

            @endforeach

            @foreach($masterShifts as $shift)

<div class="modal fade"
     id="editShift{{ $shift->id }}"
     tabindex="-1">

    <div class="modal-dialog">

        <form action="{{ route('master_shift.update',$shift->id) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="modal-content">

                <div class="modal-header bg-warning">

                    <h5 class="modal-title">
                        Edit Shift {{ $shift->kode_shift }}
                    </h5>

                    <button type="button"
                            class="close"
                            data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Kode Shift</label>

                        <input type="text"
                               name="kode_shift"
                               value="{{ $shift->kode_shift }}"
                               class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Waktu Shift</label>

                        <select name="nama_shift"
                                class="form-control"
                                required>

                            <option value="Pagi"
                                {{ $shift->nama_shift == 'Pagi' ? 'selected' : '' }}>
                                Pagi
                            </option>

                            <option value="Siang"
                                {{ $shift->nama_shift == 'Siang' ? 'selected' : '' }}>
                                Siang
                            </option>

                            <option value="Malam"
                                {{ $shift->nama_shift == 'Malam' ? 'selected' : '' }}>
                                Malam
                            </option>

                        </select>
                    </div>

                    <div class="form-group">
                        <label>Jam Mulai</label>

                        <input type="time"
                               name="jam_mulai"
                               value="{{ \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i') }}"
                               class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Jam Selesai</label>

                        <input type="time"
                               name="jam_selesai"
                               value="{{ \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i') }}"
                               class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>

                        <textarea name="keterangan"
                                  class="form-control"
                                  rows="3">{{ $shift->keterangan }}</textarea>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">

                        Batal

                    </button>

                    <button type="submit"
                            class="btn btn-warning">

                        Update Shift

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>
@foreach($masterShifts as $shift)

<!-- Modal Delete -->
<div class="modal fade"
     id="deleteShift{{ $shift->id }}"
     tabindex="-1"
     role="dialog">

    <div class="modal-dialog modal-dialog-centered"
         role="document">

        <div class="modal-content">

            <div class="modal-header bg-danger">

                <h5 class="modal-title text-white">
                    Hapus Shift
                </h5>

                <button type="button"
                        class="close text-white"
                        data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <div class="modal-body text-center">

                <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>

                <h5>
                    Yakin ingin menghapus shift
                    <strong>{{ $shift->kode_shift }}</strong>?
                </h5>

                <p class="text-muted mb-0">
                    Data shift yang dihapus tidak dapat dikembalikan.
                </p>

            </div>

            <div class="modal-footer justify-content-center">

                <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                    Batal
                </button>

                <form action="{{ route('master_shift.destroy', $shift->id) }}"
                      method="POST">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="btn btn-danger">
                        Ya, Hapus
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

@endforeach
@endforeach
        </div>

    </div>

</div>
    </div> 
</div>
<div class="modal fade"
     id="modalTambahShift"
     tabindex="-1">

    <div class="modal-dialog">

        <form action="{{ route('master_shift.store') }}"
              method="POST">

            @csrf

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Tambah Shift
                    </h5>

                    <button type="button"
                            class="close"
                            data-dismiss="modal">

                        <span>&times;</span>

                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Kode Shift</label>

                        <input type="text"
                               name="kode_shift"
                               class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Waktu Shift</label>

                        <select name="nama_shift"
                                class="form-control"
                                required>

                            <option value="">-- Pilih Waktu Shift --</option>

                            <option value="Pagi">Pagi</option>
                            <option value="Siang">Siang</option>
                            <option value="Malam">Malam</option>

                        </select>
                    </div>

                    <div class="form-group">
                        <label>Jam Mulai</label>

                        <input type="time"
                               name="jam_mulai"
                               class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Jam Selesai</label>

                        <input type="time"
                               name="jam_selesai"
                               class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>

                        <textarea name="keterangan"
                                  class="form-control"></textarea>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="submit"
                            class="btn btn-success">

                        Simpan Shift

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

{{-- SCRIPT JAVASCRIPT UNTUK OTOMATISASI FORM --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const shiftSelect = document.getElementById('shift');
    const jamInput = document.getElementById('jam');

    if (!shiftSelect || !jamInput) return;

    shiftSelect.addEventListener('change', function () {

        const selectedOption =
            this.options[this.selectedIndex];

        jamInput.value =
            selectedOption.getAttribute('data-jam') || '';

    });

});
</script>
@endsection