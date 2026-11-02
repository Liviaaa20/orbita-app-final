<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MasterData\KategoriController;
use App\Http\Controllers\MasterData\SubKategoriController;
use App\Http\Controllers\MasterData\AlatController;
use App\Http\Controllers\MasterData\PengecekanController;
use App\Http\Controllers\HistoriOperasionalController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PerbaikanController;
use App\Http\Controllers\KalibrasiController;
use App\Http\Controllers\CetakKalibrasiController;
use App\Http\Controllers\JadwalDinasController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\MasterShiftController;

// --- AUTHENTICATION ---
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

// --- PROTECTED ROUTES (HARUS LOGIN) ---
Route::middleware(['auth'])->group(function () {
    
    // Dashboard & Profile
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    // Manajemen Akses & User
    Route::resource('role', RoleController::class);
    Route::resource('user', UserController::class)->except(['show']);

    // Master Data
    Route::resource('kategori', KategoriController::class);
    Route::resource('sub-kategori', SubKategoriController::class);
// Master Data
    Route::get('/get-sub-kategori/{kategori_id}', [AlatController::class, 'getSubKategori'])->name('get-sub-kategori');
    Route::resource('data-alat', AlatController::class);    // Pengecekan
    Route::get('/pengecekan', [PengecekanController::class, 'index'])->name('pengecekan.index');
    Route::post('/pengecekan/store', [PengecekanController::class, 'store'])->name('pengecekan.store');
    Route::get('/pengecekan', [MaintenanceController::class, 'showPengecekan'])->name('pengecekan.index');

// /// --- MAINTENANCE (HARIAN & MINGGUAN) ---
// Route::prefix('maintenance')->name('maintenance.')->group(function () {
//     // Pastikan mengarah ke showPengecekan
//     Route::get('/harian', [MaintenanceController::class, 'indexHarian'])->name('harian');
//     Route::get('/mingguan', [MaintenanceController::class, 'indexMingguan'])->name('mingguan');
    
//     // Halaman pilih lokasi
//     Route::get('/form-pengecekan', [MaintenanceController::class, 'showPengecekan'])->name('show-pengecekan');
//     Route::get('/form-pengecekan-alt', [MaintenanceController::class, 'showPengecekan'])->name('showPengecekan');

//     // TAHAP 2: Simpan Inisiasi (Plotting)
//     Route::post('/store-inisiasi', [MaintenanceController::class, 'storeInisiasi'])->name('store-inisiasi');
    
//     // TAHAP 3: Form Checklist Fisik
//     Route::get('/form-master', [MaintenanceController::class, 'formMaster'])->name('form-master');
    
//     // TAHAP 4: Simpan Hasil Akhir
//     Route::post('/store-hasil', [MaintenanceController::class, 'storeHasilFisik'])->name('store-hasil');
//     Route::post('/store', [MaintenanceController::class, 'storeInisiasi'])->name('store');
// });

Route::prefix('maintenance')->name('maintenance.')->group(function () {
 
    // ---- INDEX (halaman riwayat) ----
    Route::get('/harian',   [MaintenanceController::class, 'indexHarian'])  ->name('harian');
    Route::get('/mingguan', [MaintenanceController::class, 'indexMingguan'])->name('mingguan');
 
    // ---- FORM INPUT BARU ----
    // (view lama harian_index.blade.php sekarang dipanggil sebagai harian_create)
    Route::get('/harian/baru',   [MaintenanceController::class, 'createHarian'])  ->name('harian.create');
    Route::get('/mingguan/baru', [MaintenanceController::class, 'createMingguan'])->name('mingguan.create');
 
    // ---- SHOW PENGECEKAN (pilih lokasi) ----
    Route::get('/pengecekan',  [MaintenanceController::class, 'showPengecekan'])->name('show-pengecekan');
 
    // ---- STORE INISIASI ----
    Route::post('/inisiasi',   [MaintenanceController::class, 'storeInisiasi']) ->name('store-inisiasi');
 
    // ---- FORM MASTER (isi hasil per alat) ----
    Route::get('/form-master', [MaintenanceController::class, 'formMaster'])   ->name('form-master');
 
    // ---- STORE HASIL ----
    Route::post('/store-hasil',[MaintenanceController::class, 'storeHasilFisik'])->name('store-hasil');
 
    // ---- DETAIL SESI ----
    Route::get('/detail',      [MaintenanceController::class, 'detailSesi'])   ->name('detail');
});
    // Group Route untuk Permintaan Perbaikan
    Route::prefix('perbaikan')->name('perbaikan.')->group(function () {
    // Halaman Utama Tabel Perbaikan
    Route::get('/', [PerbaikanController::class, 'index'])->name('index');

    // --- TAMBAHKAN DUA BARIS INI (BARU) ---
    Route::get('/create', [PerbaikanController::class, 'create'])->name('create'); // Menampilkan form
    Route::post('/store', [PerbaikanController::class, 'store'])->name('store');   // Proses simpan data
    // --------------------------------------
    
    // Route untuk Validasi Cepat (Tombol Centang/Silang)
    Route::post('/validasi/{id}', [PerbaikanController::class, 'validasi'])->name('validasi');    
    // Route untuk Update Catatan & Status (Simpan Modal)
    Route::put('/update/{id}', [PerbaikanController::class, 'update'])->name('update');
    
    // Route untuk Download Laporan (Opsional)
    Route::get('/download/{id}', [PerbaikanController::class, 'download'])->name('download');
    Route::get('/show/{id}', [PerbaikanController::class, 'show'])->name('show');
    Route::post('/validasi-koordinator/{id}', [PerbaikanController::class, 'validasiKoordinator'])->name('validasi-koordinator');
    });

    // --- KALIBRASI ALAT ---
    Route::get('/kalibrasi', [KalibrasiController::class, 'index'])->name('kalibrasi.index');
    Route::post('/kalibrasi/store', [KalibrasiController::class, 'store'])->name('kalibrasi.store');
    Route::get('/kalibrasi/sertifikat/{id}/view', [KalibrasiController::class, 'viewSertifikat'])->name('kalibrasi.sertifikat_view');
    Route::get('/kalibrasi/cetak-pdf', [KalibrasiController::class, 'cetakPdf'])->name('kalibrasi.cetak_pdf');
    
    // --- HISTORI OPERASIONAL (Mandiri) ---
    // Halaman Utama & Filter
    Route::get('/histori-operasional', [HistoriOperasionalController::class, 'index'])->name('histori.index');
    
    // --- JADWAL DINAS (Baru) ---
    Route::get('/jadwal-dinas', [JadwalDinasController::class, 'index'])->name('jadwal_dinas.index');
    Route::get('/jadwal-dinas/create', [JadwalDinasController::class, 'create'])->name('jadwal_dinas.create');
    Route::post('/jadwal-dinas/store', [JadwalDinasController::class, 'store'])->name('jadwal_dinas.store');
    Route::post('/jadwal-dinas/import', [JadwalDinasController::class, 'import'])->name('jadwal_dinas.import');
    Route::get('/jadwal-dinas/riwayat', [JadwalDinasController::class, 'riwayat'])->name('jadwal_dinas.riwayat');
    Route::post(
        '/master-shift/store',
        [MasterShiftController::class, 'store']
    )->name('master_shift.store');

    Route::put(
        '/master-shift/update/{id}',
        [MasterShiftController::class, 'update']
    )->name('master_shift.update');

    Route::delete(
        '/master-shift/delete/{id}',
        [MasterShiftController::class, 'destroy']
    )->name('master_shift.destroy');
    Route::get('/jadwal-dinas/download', [JadwalDinasController::class, 'download'])->name('jadwal-dinas.download');

    // Export Laporan Keseluruhan (Tombol Unduh di atas tabel)
    Route::get('/histori-operasional/export', [HistoriOperasionalController::class, 'export'])->name('histori.export');
    
    // Detail & Download Per Baris (Tombol di kolom Aksi)
    Route::get('/histori-operasional/{id}', [HistoriOperasionalController::class, 'show'])->name('histori.show');
    Route::get('/histori-operasional/download/{id}', [HistoriOperasionalController::class, 'downloadSingle'])->name('histori.download-single');
    Route::get('/histori/{id}/riwayat', [HistoriOperasionalController::class, 'downloadRiwayat'])->name('histori.riwayat');

    Route::prefix('logbook')->name('logbook.')->group(function () {
    Route::resource('logbook', LogbookController::class);    

        // Index & Show
        Route::get('/',          [LogbookController::class, 'index'])->name('index');
        Route::get('/{id}',      [LogbookController::class, 'show'])->name('show');

        // CRUD
        Route::post('/store',        [LogbookController::class, 'store'])->name('store');
        Route::put('/update/{id}',   [LogbookController::class, 'update'])->name('update');
        Route::delete('/delete/{id}',[LogbookController::class, 'destroy'])->name('destroy');

        // Alur Approval
        Route::post('/submit/{id}',              [LogbookController::class, 'submit'])->name('submit');
        Route::post('/approve-kapok/{id}',       [LogbookController::class, 'approveKapok'])->name('approve-kapok');
        Route::post('/reject-kapok/{id}',        [LogbookController::class, 'rejectKapok'])->name('reject-kapok');
        Route::post('/approve-koordinator/{id}', [LogbookController::class, 'approveKoordinator'])->name('approve-koordinator');
        Route::post('/reject-koordinator/{id}',  [LogbookController::class, 'rejectKoordinator'])->name('reject-koordinator');

        Route::get('/logbook/get-alat/{kategori}',
            [LogbookController::class, 'getAlatByKategori']
        )->name('logbook.getAlat');
    });
    
         // Download PDF (hanya approved_final)
        Route::get('/download-pdf/{id}', [LogbookController::class, 'downloadPdf'])->name('download-pdf');
});