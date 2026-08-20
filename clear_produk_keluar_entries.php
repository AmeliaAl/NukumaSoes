<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProdukKeluarEntry;
use App\Models\KartuStokEntry;
use App\Models\JurnalUmum;
use App\Models\Inventory;
use App\Models\Product;

echo "=== HAPUS RIWAYAT PRODUK KELUAR ===" . PHP_EOL;

// Ambil semua entries sebelum dihapus
$entries = ProdukKeluarEntry::all();
echo "Total ProdukKeluarEntry yang akan dihapus: " . $entries->count() . PHP_EOL;
echo PHP_EOL;

// Kembalikan stok inventory (tambah kembali jumlah_keluar)
foreach ($entries as $entry) {
    $inv = Inventory::find($entry->inventory_id);
    if ($inv) {
        $inv->jumlah += (int)$entry->jumlah_keluar;
        $inv->save();
        echo "Inventory #{$inv->id} ({$inv->nama_produk} - {$inv->no_batch}): stok dikembalikan +{$entry->jumlah_keluar} → total {$inv->jumlah}" . PHP_EOL;
    } else {
        echo "Entry #{$entry->id}: inventory_id={$entry->inventory_id} tidak ditemukan, dilewati." . PHP_EOL;
    }
}

echo PHP_EOL;

// Sync semua jumlah produk
$kodeProdukList = $entries->pluck('kode_produk')->unique();
foreach ($kodeProdukList as $kode) {
    Product::syncQuantity($kode);
    echo "Product::syncQuantity({$kode}) selesai." . PHP_EOL;
}

echo PHP_EOL;

// Hapus KartuStokEntry yang keterangannya 'Produk Keluar'
$deleted_kse = KartuStokEntry::where('keterangan', 'like', 'Produk Keluar%')->delete();
echo "KartuStokEntry 'Produk Keluar' dihapus: {$deleted_kse}" . PHP_EOL;

// Hapus semua ProdukKeluarEntry
ProdukKeluarEntry::truncate();
echo "ProdukKeluarEntry berhasil dikosongkan." . PHP_EOL;

echo PHP_EOL . "=== SELESAI ===" . PHP_EOL;
