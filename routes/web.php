<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HargaProdukController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\PersediaanController;
use App\Http\Controllers\InventoryEntryController;
use App\Http\Controllers\ProdukKeluarController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\KartuStokController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\AsetController;
use App\Http\Controllers\KategoriAsetController;
use App\Http\Controllers\FlavorController;
use App\Http\Controllers\SalesOrderPdfController;
use App\Http\Controllers\JurnalUmumController;

Route::get('/', function () {
    return view('welcome');
});

// Auth routes
require __DIR__.'/auth.php';

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // COA (Chart of Accounts)
    Route::resource('coa', CoaController::class);
    Route::get('coa/import/form', [CoaController::class, 'importForm'])->name('coa.import.form');
    Route::post('coa/import', [CoaController::class, 'import'])->name('coa.import');
    
    // Kategori Produk
    Route::resource('kategori', CategoryController::class);
    
    // Produk
    Route::resource('produk', ProductController::class);
    
    // Harga Produk
    Route::resource('harga-produk', HargaProdukController::class);
    
    // Monitoring FEFO
    Route::get('monitoring-fefo', [MonitoringController::class, 'index'])->name('monitoring.fefo');
    
    // Persediaan Produk (uses ProductController@inventory to show produk/inventory.blade.php)
    Route::get('persediaan-produk', [ProductController::class, 'inventory'])->name('persediaan-produk.index');
    
    // Inventory Entry (for creating/editing inventory entries)
    Route::resource('inventory-entry', InventoryEntryController::class)->except(['index']);
    Route::get('inventory-entry/{inventory}/masuk', [InventoryEntryController::class, 'masuk'])->name('inventory-entry.masuk');
    Route::post('inventory-entry/{inventory}/masuk', [InventoryEntryController::class, 'storeMasuk'])->name('inventory-entry.storeMasuk');
    Route::get('inventory-entry/{inventory}/keluar', [InventoryEntryController::class, 'keluar'])->name('inventory-entry.keluar');
    Route::post('inventory-entry/{inventory}/keluar', [InventoryEntryController::class, 'storeKeluar'])->name('inventory-entry.storeKeluar');
    
    // Produk Masuk (Persediaan)
    Route::resource('persediaan', PersediaanController::class);
    
    // Produk Keluar
    Route::resource('produk-keluar', ProdukKeluarController::class);
    
    // Pengeluaran
    Route::resource('pengeluaran', PengeluaranController::class);
    
    // Kartu Stok
    Route::get('kartu-stok', [KartuStokController::class, 'index'])->name('kartu-stok.index');
    
    // Laporan
    Route::get('laporan/jurnal-umum', [LaporanController::class, 'jurnalUmum'])->name('laporan.jurnal-umum');
    Route::delete('/laporan/jurnal-umum/{id}', [LaporanController::class, 'destroy'])
    ->name('laporan.jurnal-umum.destroy');
    Route::get('/laporan/jurnal-umum/export/pdf', [LaporanController::class, 'exportPdf'])
    ->name('laporan.jurnal-umum.export.pdf');
    Route::get('/laporan/jurnal-umum/export/excel', [LaporanController::class, 'exportExcel'])
    ->name('laporan.jurnal-umum.export.excel');
    Route::get('laporan/buku-besar', [LaporanController::class, 'bukuBesar'])->name('laporan.buku-besar');
    Route::get('laporan/laba-rugi', [LaporanController::class, 'labaRugi'])->name('laporan.laba-rugi');
    
    // Akun
    Route::resource('akun', AkunController::class);
    
    // Aset
    Route::resource('aset', AsetController::class);
    
    // Kategori Aset
    Route::resource('kategori-aset', KategoriAsetController::class);
    
    // Rasa (Flavor)
    Route::resource('rasa', FlavorController::class);
    
    // Sales Order PDF
    Route::get('/sales-order/{id}/pdf', [SalesOrderPdfController::class, 'download'])->name('sales-order.pdf');
});
