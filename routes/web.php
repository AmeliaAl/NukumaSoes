<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\OverheadController;
use App\Http\Controllers\SaldoAwalController;
use App\Http\Controllers\LaporanPembelianController;
use App\Http\Controllers\LaporanOverheadController;
use App\Http\Controllers\JurnalUmumController;
use App\Http\Controllers\BukuBesarController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::resource('supplier', SupplierController::class);
Route::resource('bahan-baku', BahanBakuController::class);
Route::resource('coa', CoaController::class);
Route::delete('/pembelian/proof/{id}', [PembelianController::class, 'deleteProof'])->name('pembelian.delete-proof');
Route::resource('pembelian', PembelianController::class);
Route::delete('/overhead/proof/{id}', [OverheadController::class, 'deleteProof'])->name('overhead.delete-proof');
Route::resource('overhead', OverheadController::class);
Route::resource('saldo-awal', SaldoAwalController::class);

// ── Notifikasi (placeholder integrasi modul Produksi) ──────────────────────
Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
Route::get(
    'laporan-pembelian',
    [LaporanPembelianController::class, 'index']
);
Route::get(
    'laporan-pembelian/print',
    [LaporanPembelianController::class, 'print']
);
Route::get(
    'laporan-overhead',
    [LaporanOverheadController::class, 'index']
);

Route::get(
    'laporan-overhead/print',
    [LaporanOverheadController::class, 'print']
);

Route::get(
    'jurnal-umum',
    [JurnalUmumController::class, 'index']
);

Route::get(
    'jurnal-umum/print',
    [JurnalUmumController::class, 'print']
);

Route::get(
    'buku-besar',
    [BukuBesarController::class, 'index']
);

Route::get(
    'buku-besar/print',
    [BukuBesarController::class, 'print']
);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';