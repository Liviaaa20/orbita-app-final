@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">

            {{-- Header --}}
            <div class="mb-4">
                <h3 class="text-uppercase font-weight-bold"
                    style="border-left: 5px solid #003366; padding-left: 15px; color: #003366;">
                    Maintenance Mingguan
                </h3>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">

                    <div class="card shadow-sm border-0" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom-0 text-center">
                            <h5 class="m-0 font-weight-bold text-dark">
                                <i class="fas fa-plus-circle mr-2 text-primary"></i> Pengecekan Baru
                            </h5>
                        </div>

                        <div class="card-body px-4 pb-5 pt-2">
                            <form action="{{ route('maintenance.show-pengecekan') }}" method="GET">

                                <div class="form-group mb-5">
                                    <label class="font-weight-bold text-muted small text-uppercase">
                                        Pilih Tanggal Pengecekan Mingguan
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0">
                                                <i class="fas fa-calendar-alt text-primary"></i>
                                            </span>
                                        </div>
                                        <input type="date" name="tanggal"
                                               value="{{ date('Y-m-d') }}"
                                               class="form-control form-control-lg bg-light border-left-0 font-weight-bold"
                                               style="border-radius: 0 8px 8px 0;">
                                    </div>
                                    <small class="text-muted mt-2 d-block">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Hari akan otomatis terdeteksi dari tanggal yang dipilih.
                                    </small>
                                </div>

                                <input type="hidden" name="type" value="mingguan">
                                {{-- waktu (nama hari) dikirim kosong, controller akan auto-detect --}}

                                <hr class="mt-5">

                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('maintenance.mingguan') }}"
                                       class="btn btn-secondary btn-lg mr-2 px-5">
                                        Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg px-5"
                                            style="background-color: #003366; border: none;">
                                        Proses
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Info Box --}}
                    <div class="alert border-0 shadow-sm mt-4 p-3"
                         style="border-radius: 10px; background-color: #eef7ff;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle text-primary mr-3 fa-lg"></i>
                            <p class="mb-0 small text-dark">
                                Anda akan diarahkan ke halaman <strong>Pengecekan Data Alat</strong>
                                untuk memilih lokasi pengecekan setelah menekan tombol proses.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-control-lg {
        height: 55px;
        border: 1px solid #ebedef;
        font-size: 1rem;
    }
    .input-group-text {
        border: 1px solid #ebedef;
        padding-left: 20px;
        padding-right: 20px;
    }
    .form-control:focus {
        border-color: #003366;
        box-shadow: none;
        background-color: #fff !important;
    }
</style>
@endpush