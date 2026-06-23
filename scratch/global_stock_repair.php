<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Inventory;
use App\Models\PersediaanEntry;
use App\Models\ProdukKeluarEntry;
use App\Models\Product;

$products = Product::all();

echo "Starting Global Stock Repair...\n";
echo "---------------------------------\n";

foreach ($products as $product) {
    $kode = $product->kode_produk;
    
    // Calculate actual balance from entries
    $masuk = PersediaanEntry::where('kode_produk', $kode)->sum('jumlah_masuk');
    $keluar = ProdukKeluarEntry::where('kode_produk', $kode)->sum('jumlah_keluar');
    $actualBalance = $masuk - $keluar;
    
    // Get total current inventory (Non-Expired)
    $currentInvSum = Inventory::where('kode_produk', $kode)->where('status', '!=', 'Expired')->sum('jumlah');
    
    if ($currentInvSum != $actualBalance) {
        echo "MISMATCH FOUND for $kode: Transaction Balance ($actualBalance) != Inventory Sum ($currentInvSum)\n";
        
        // Adjust the most recent non-expired inventory record to reconcile
        $latestInv = Inventory::where('kode_produk', $kode)
            ->where('status', '!=', 'Expired')
            ->orderBy('tgl_masuk', 'desc')
            ->first();
            
        if ($latestInv) {
            $diff = $actualBalance - $currentInvSum;
            echo "Adjusting Inventory ID {$latestInv->id} by $diff (New Qty: " . ($latestInv->jumlah + $diff) . ")\n";
            $latestInv->jumlah += $diff;
            if ($latestInv->jumlah < 0) $latestInv->jumlah = 0; // Prevent negative
            $latestInv->save();
        } else if ($actualBalance > 0) {
            echo "No active inventory found for $kode but balance is $actualBalance. Creating recovery batch.\n";
            Inventory::create([
                'kode_produk' => $kode,
                'nama_produk' => $product->nama_produk,
                'kategori' => $product->kategori,
                'jumlah' => $actualBalance,
                'harga' => $product->harga ?? 0,
                'status' => 'Tersedia',
                'tgl_masuk' => now(),
            ]);
        }
    }
    
    // Always sync the Product table at the end
    Product::syncQuantity($kode);
}

echo "---------------------------------\n";
echo "Global Stock Repair Completed.\n";
