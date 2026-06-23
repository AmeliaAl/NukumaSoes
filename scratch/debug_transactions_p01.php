<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$kode = 'P01';

$masuk = \App\Models\PersediaanEntry::where('kode_produk', $kode)->sum('jumlah_masuk');
$keluar = \App\Models\ProdukKeluarEntry::where('kode_produk', $kode)->sum('jumlah_keluar');
$balance = $masuk - $keluar;

echo "Product: $kode\n";
echo "Total Masuk: $masuk\n";
echo "Total Keluar: $keluar\n";
echo "Calculated Balance (Masuk - Keluar): $balance\n";

$inventorySum = \App\Models\Inventory::where('kode_produk', $kode)->sum('jumlah');
echo "Inventory Table Sum: $inventorySum\n";
