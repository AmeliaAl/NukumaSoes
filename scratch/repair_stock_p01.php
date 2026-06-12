<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Inventory;
use App\Models\PersediaanEntry;
use App\Models\ProdukKeluarEntry;
use App\Models\Product;

$kode = 'P01';

// Calculate actual balance from entries
$masuk = PersediaanEntry::where('kode_produk', $kode)->sum('jumlah_masuk');
$keluar = ProdukKeluarEntry::where('kode_produk', $kode)->sum('jumlah_keluar');
$actualBalance = $masuk - $keluar;

echo "Repairing $kode...\n";
echo "Actual Balance (Masuk $masuk - Keluar $keluar) = $actualBalance\n";

// Update Inventory record(s)
$inventories = Inventory::where('kode_produk', $kode)->orderBy('tgl_expired', 'desc')->get();

if ($inventories->count() == 1) {
    $inv = $inventories->first();
    echo "Updating Inventory ID {$inv->id} from {$inv->jumlah} to $actualBalance\n";
    $inv->jumlah = $actualBalance;
    $inv->save();
} else {
    echo "Multiple batches found. Manual adjustment needed or more complex logic.\n";
    // For now, let's just adjust the oldest active one if there are multiples
}

// Sync Product table
Product::syncQuantity($kode);
echo "Product table synced. Current 'jumlah' for $kode: " . Product::where('kode_produk', $kode)->first()->jumlah . "\n";
