<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$kode = 'P01';
$inventories = \App\Models\Inventory::where('kode_produk', $kode)->get();

echo "Product: $kode\n";
echo "---------------------------------\n";
foreach ($inventories as $inv) {
    echo "ID: {$inv->id} | Qty: {$inv->jumlah} | Status: {$inv->status} | Exp: {$inv->tgl_expired}\n";
}
echo "---------------------------------\n";
echo "Total Sum (All): " . $inventories->sum('jumlah') . "\n";
echo "Total Sum (Active - Non Expired): " . $inventories->where('status', '!=', 'Expired')->sum('jumlah') . "\n";
echo "---------------------------------\n";
$product = \App\Models\Product::where('kode_produk', $kode)->first();
echo "Product Table 'jumlah' column: " . ($product->jumlah ?? 'N/A') . "\n";
