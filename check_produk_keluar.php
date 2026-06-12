<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$entries = \App\Models\ProdukKeluarEntry::orderBy('created_at', 'desc')->take(10)->get();
foreach ($entries as $e) {
    echo "ID: {$e->id} | Tanggal: {$e->tanggal} | Produk: {$e->nama_produk} ({$e->kode_produk}) | Qty: {$e->jumlah_keluar} | Total: {$e->total_harga}\n";
}
