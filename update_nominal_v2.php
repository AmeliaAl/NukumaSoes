<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;
$app->make(Kernel::class)->bootstrap();

use App\Models\ExpiredProductHistory;
use App\Models\ProdukKeluarEntry;
use App\Models\Product;

ExpiredProductHistory::all()->each(function($history) {
    // 1. Try to find the actual product code
    $product = Product::where('kategori', $history->kategori)
        ->where('rasa_produk', $history->rasa_produk)
        ->first();
    
    $hargaPokok = 0;
    
    if ($product) {
        $pkEntry = ProdukKeluarEntry::where('kode_produk', $product->kode_produk)
            ->where('harga_pokok_per_pack', '>', 0)
            ->orderBy('tanggal', 'desc')
            ->first();
        
        if ($pkEntry) {
            $hargaPokok = $pkEntry->harga_pokok_per_pack;
        }
    }

    // 2. If not found, use the 87.68 as a fallback if it matches the user's intent
    // Actually, the user specifically mentioned 87.68. 
    // If we still can't find it, we might want to use it, but let's try to be precise.
    
    if ($hargaPokok == 0) {
        // Hardcode the 87.68 if we can't find anything else, as requested
        $hargaPokok = 87.68;
    }

    $history->harga = $hargaPokok;
    $history->total = $history->jumlah * $history->harga;
    $history->save();
    
    echo "Updated: [{$history->kategori}] {$history->rasa_produk} - HPP: {$history->harga} - Total: {$history->total}\n";
});
