<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\PersediaanController;
use App\Http\Controllers\ProdukKeluarController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\KartuStokController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\FlavorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InventoryEntryController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // COA (Daftar Akun)
    Route::get('coa/import', [CoaController::class, 'importForm'])->name('coa.import-form');
    Route::post('coa/import', [CoaController::class, 'import'])->name('coa.import');
    Route::resource('coa', CoaController::class);

    // Kategori Produk
    Route::resource('kategori', CategoryController::class);

    // Flavors (Rasa Produk)
    Route::resource('rasa', FlavorController::class);

    // Produk
    Route::get('persediaan-produk', [ProductController::class, 'inventory'])->name('persediaan-produk.index');
    Route::resource('produk', ProductController::class);



    // Monitoring FEFO
    Route::get('monitoring/fefo', [MonitoringController::class, 'fefo'])->name('monitoring.fefo');
    Route::post('monitoring/update-expiry', [MonitoringController::class, 'updateProductExpiry'])->name('monitoring.update-expiry');
    Route::delete('monitoring/expired-history/{id}', [MonitoringController::class, 'destroyExpiredHistory'])->name('monitoring.expired-history.destroy');
    Route::get('monitoring/export-excel', [MonitoringController::class, 'exportExcelExpiredHistory'])->name('monitoring.expired-history.export.excel');
    Route::get('monitoring/export-pdf', [MonitoringController::class, 'exportPdfExpiredHistory'])->name('monitoring.expired-history.export.pdf');
    Route::post('monitoring/add-to-journal/{id}', [MonitoringController::class, 'addToJournal'])->name('monitoring.add-to-journal');

    // Transaksi
    Route::get('inventory-entry/{inventory}/masuk', [InventoryEntryController::class, 'masuk'])->name('inventory-entry.masuk');
    Route::post('inventory-entry/{inventory}/masuk', [InventoryEntryController::class, 'storeMasuk'])->name('inventory-entry.store-masuk');
    Route::get('inventory-entry/{inventory}/keluar', [InventoryEntryController::class, 'keluar'])->name('inventory-entry.keluar');
    Route::post('inventory-entry/{inventory}/keluar', [InventoryEntryController::class, 'storeKeluar'])->name('inventory-entry.store-keluar');
    Route::resource('inventory-entry', InventoryEntryController::class);

    Route::resource('persediaan', PersediaanController::class);
    Route::resource('produk-keluar', ProdukKeluarController::class);
    Route::resource('pengeluaran', PengeluaranController::class);

    // Laporan
    // Kartu Stok
    Route::get('kartu-stok', [KartuStokController::class, 'index'])->name('kartu-stok.index');
    Route::get('kartu-stok/pdf', [KartuStokController::class, 'downloadPdf'])->name('kartu-stok.pdf');

    // Financial Reports
    Route::get('laporan/keuangan', [LaporanController::class, 'keuangan'])->name('laporan.keuangan');
    Route::get('laporan/jurnal-umum', [LaporanController::class, 'jurnalUmum'])->name('laporan.jurnal-umum');
    Route::post('laporan/jurnal-umum', [LaporanController::class, 'storeJurnalUmum'])->name('laporan.jurnal-umum-store');
    Route::delete('laporan/jurnal-umum/{id}', [LaporanController::class, 'destroyJurnalUmum'])->name('laporan.jurnal-umum.destroy');
    Route::get('laporan/jurnal-umum/excel', [LaporanController::class, 'exportExcelJurnalUmum'])->name('laporan.jurnal-umum.export.excel');
    Route::get('laporan/jurnal-umum/pdf', [LaporanController::class, 'exportPdfJurnalUmum'])->name('laporan.jurnal-umum.export.pdf');

    Route::get('laporan/buku-besar', [LaporanController::class, 'bukuBesar'])->name('laporan.buku-besar');
    Route::get('laporan/buku-besar/excel', [LaporanController::class, 'exportExcelBukuBesar'])->name('laporan.buku-besar.export.excel');
    Route::get('laporan/buku-besar/pdf', [LaporanController::class, 'exportPdfBukuBesar'])->name('laporan.buku-besar.export.pdf');



    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
