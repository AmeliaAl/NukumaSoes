<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$entries = \App\Models\ProdukKeluarEntry::latest()->get();
foreach ($entries as $e) {
    echo "ID: {$e->id} | Created: {$e->created_at} | Produk: {$e->nama_produk} | Total: {$e->total_harga}\n";
}
