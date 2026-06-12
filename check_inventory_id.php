<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$entries = \App\Models\ProdukKeluarEntry::all();
foreach ($entries as $e) {
    echo "ID: {$e->id} | Inventory ID: {$e->inventory_id} | Produk: {$e->nama_produk}\n";
}
