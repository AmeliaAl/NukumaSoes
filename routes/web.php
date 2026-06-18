<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\TenagaKerjaController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PermintaanBahanBakuController;
use App\Http\Controllers\PenerimaanBahanBakuController;
use App\Http\Controllers\PermintaanProduksiController;
use App\Http\Controllers\PemakaianBahanBakuController;
use App\Http\Controllers\BiayaTenagaKerjaController;
use App\Http\Controllers\BiayaOverheadPabrikController;
use App\Http\Controllers\PengeluaranBopController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\StokProdukController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\PenerimaanOrderProduksiController;
use App\Http\Controllers\KehadiranController;
use App\Http\Controllers\KategoriBopController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =============================================
// FIRST TIME SETUP (Public - No Auth Required)
// =============================================
Route::get('/setup/first-admin', [AuthController::class, 'showFirstAdminSetup'])->name('setup.first-admin');
Route::post('/setup/first-admin', [AuthController::class, 'storeFirstAdmin'])->name('setup.store-first-admin');

// =============================================
// AUTHENTICATION ROUTES (Public)
// =============================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// =============================================
// PROTECTED ROUTES (Require Authentication + Admin Role)
// =============================================
Route::middleware(['auth:admin', 'check.admin'])->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Change Password
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('change-password');
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Profile & Settings
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.edit');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/settings', [AuthController::class, 'showSettings'])->name('settings');

    // =============================================
    // DASHBOARD
    // =============================================
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/stok-alert', [DashboardController::class, 'getStokAlert'])->name('dashboard.stok-alert');

    // =============================================
    // MASTER DATA
    // =============================================
    
    // Bahan Baku
    Route::resource('bahan-baku', BahanBakuController::class)->parameters([
        'bahan-baku' => 'id'
    ]);
    Route::get('bahan-baku/{id}/kartu-stok', [BahanBakuController::class, 'kartuStok'])->name('bahan-baku.kartu-stok');
    Route::get('bahan-baku/{id}/stok-fifo', [BahanBakuController::class, 'getStokFifo'])->name('bahan-baku.stok-fifo');

    // Tenaga Kerja
    Route::resource('tenaga-kerja', TenagaKerjaController::class)->parameters([
        'tenaga-kerja' => 'id'
    ]);

    // Produk
    Route::resource('produk', ProdukController::class)->parameters([
        'produk' => 'id'
    ]);

    // Master Akun (COA)
    Route::resource('akun', AkunController::class)->parameters([
        'akun' => 'id'
    ]);

    // Kategori BOP
    Route::resource('kategori-bop', KategoriBopController::class)->parameters([
        'kategori-bop' => 'id'
    ]);

    // =============================================
    // API ROUTES (Internal - Authenticated)
    // =============================================
    
    // API untuk cari permintaan bahan baku (HARUS SEBELUM resource routes)
    Route::get('/api/permintaan-bahan-baku/cari', [PenerimaanBahanBakuController::class, 'cariPermintaan'])
         ->name('api.permintaan.cari');

    // =============================================
    // TRANSAKSI
    // =============================================
    
    // ========== PERMINTAAN BAHAN BAKU ==========
    // Resource route untuk CRUD standar
    Route::resource('permintaan-bahan-baku', PermintaanBahanBakuController::class)->parameters([
        'permintaan-bahan-baku' => 'id'
    ]);

    // ========== PENERIMAAN BAHAN BAKU ==========
    Route::resource('penerimaan-bahan-baku', PenerimaanBahanBakuController::class)->parameters([
        'penerimaan-bahan-baku' => 'id'
    ]);

    // ========== PENERIMAAN ORDER PRODUKSI (dari bagian lain) ==========
    Route::resource('penerimaan-order-produksi', PenerimaanOrderProduksiController::class)->parameters([
        'penerimaan-order-produksi' => 'id'
    ])->only(['index', 'create', 'store', 'show']);

    // ========== PERMINTAAN PRODUKSI (JOB ORDER) ==========
    // Route spesifik di atas resource route
    Route::post('permintaan-produksi/{id}/complete', [PermintaanProduksiController::class, 'complete'])
         ->name('permintaan-produksi.complete');
    Route::post('permintaan-produksi/{id}/recalculate', [PermintaanProduksiController::class, 'recalculate'])
         ->name('permintaan-produksi.recalculate');
    
    // Resource route
    Route::resource('permintaan-produksi', PermintaanProduksiController::class)->parameters([
        'permintaan-produksi' => 'id'
    ]);

    // =============================================
    // PEMAKAIAN BAHAN BAKU (FIFO) - CRITICAL FIX
    // =============================================
    // PENTING: Route spesifik HARUS di atas resource route
    // untuk menghindari konflik dengan route parameter {id}
    
    // Route untuk get FIFO batches (AJAX)
    Route::get('pemakaian-bahan-baku/get-fifo-batches/{id_bahan}', [PemakaianBahanBakuController::class, 'getFifoBatches'])
         ->name('pemakaian-bahan-baku.get-fifo-batches');

    // Route untuk get BOM dari Job Order (AJAX)
    Route::get('pemakaian-bahan-baku/get-bom-for-job/{id}', [PemakaianBahanBakuController::class, 'getBomForJob'])
         ->name('pemakaian-bahan-baku.get-bom-for-job');

    // Route untuk get detail pemakaian (AJAX)
    Route::get('pemakaian-bahan-baku/{id}/detail', [PemakaianBahanBakuController::class, 'getDetail'])
         ->name('pemakaian-bahan-baku.detail');

    // Resource route untuk CRUD standar (create, store, index, show, destroy)
    Route::resource('pemakaian-bahan-baku', PemakaianBahanBakuController::class)->parameters([
        'pemakaian-bahan-baku' => 'id'
    ])->except(['edit', 'update']); // Tidak ada edit/update karena menggunakan FIFO
    
    // =============================================
    // BIAYA TENAGA KERJA & OVERHEAD
    // =============================================
    
    // Biaya Tenaga Kerja
    Route::resource('biaya-tenaga-kerja', BiayaTenagaKerjaController::class)->parameters([
        'biaya-tenaga-kerja' => 'id'
    ])->except(['show']);

    // Kehadiran Karyawan
    Route::get('kehadiran', [KehadiranController::class, 'index'])->name('kehadiran.index');
    Route::post('kehadiran', [KehadiranController::class, 'store'])->name('kehadiran.store');
    Route::get('kehadiran/rekap-mingguan', [KehadiranController::class, 'rekapMingguan'])->name('kehadiran.rekap-mingguan');
    Route::post('kehadiran/insentif', [KehadiranController::class, 'storeIncentive'])->name('kehadiran.insentif');

    // Biaya Overhead Pabrik
    Route::resource('biaya-overhead-pabrik', BiayaOverheadPabrikController::class)->parameters([
        'biaya-overhead-pabrik' => 'id'
    ])->except(['show']);

    // Pengeluaran BOP Aktual (Jurnal Langsung)
    Route::resource('pengeluaran-bop', PengeluaranBopController::class)->parameters([
        'pengeluaran-bop' => 'id'
    ])->except(['show', 'edit', 'update']);

    // =============================================
    // STOK PRODUK (WIP & JADI)
    // =============================================
    Route::resource('stok-produk', StokProdukController::class)->parameters([
        'stok-produk' => 'id'
    ])->only(['index', 'show', 'destroy']);

    // =============================================
    // LAPORAN
    // =============================================
    
    // Laporan Biaya Produksi
    Route::get('laporan/biaya-produksi', [LaporanController::class, 'index'])
         ->name('laporan.biaya-produksi.index');
    Route::get('laporan/biaya-produksi/{id}', [LaporanController::class, 'show'])
         ->name('laporan.biaya-produksi.show');
    Route::get('laporan/biaya-produksi/{id}/pdf', [LaporanController::class, 'exportPdf'])
         ->name('laporan.biaya-produksi.pdf');
    Route::get('laporan/biaya-produksi/{id}/kartu-biaya', [LaporanController::class, 'kartuBiaya'])
         ->name('laporan.biaya-produksi.kartu-biaya');
    // Laporan Ringkasan (Dinonaktifkan)
    // Route::get('laporan/summary', [LaporanController::class, 'summary'])
    //      ->name('laporan.summary');

    // Laporan Neraca Saldo
    Route::get('laporan/neraca-saldo', [LaporanController::class, 'neracaSaldo'])
         ->name('laporan.neraca-saldo');
    
    // Analisis Varians
    Route::get('laporan/analisis-varians/{id}', [LaporanController::class, 'analisisVarians'])
         ->name('laporan.analisis-varians');

    // Jurnal Umum
    Route::get('laporan/jurnal-umum', [LaporanController::class, 'jurnalUmum'])
         ->name('jurnal-umum.index');
    Route::get('laporan/jurnal-umum/{id}', [LaporanController::class, 'jurnalUmumShow'])
         ->name('jurnal-umum.show');

    // Buku Besar
    Route::get('laporan/buku-besar', [LaporanController::class, 'bukuBesar'])
         ->name('buku-besar.index');
});