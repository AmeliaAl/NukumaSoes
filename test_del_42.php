<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simulate the deletion exactly as in controller
try {
    $inventory = \App\Models\Inventory::find(42);
    if(!$inventory) {
        echo "ID 42 not found.\n";
        exit;
    }
    
    $kode_produk = $inventory->kode_produk;

    $isExpired = false;
    if ($inventory->status === 'Expired') {
        $isExpired = true;
    } elseif ($inventory->tgl_expired) {
        $expiredDate = \Carbon\Carbon::parse($inventory->tgl_expired);
        $now = \Carbon\Carbon::now();
        if ($expiredDate->lte($now->endOfDay())) {
            $isExpired = true;
        }
    }
    
    echo "Is Expired: " . ($isExpired ? 'Yes' : 'No') . "\n";
    
    if ($isExpired) {
        \App\Models\ExpiredProductHistory::create([
            'no_batch' => $inventory->no_batch ?? $inventory->kode_produk,
            'nama_produk' => $inventory->nama_produk,
            'rasa_produk' => $inventory->rasa_produk,
            'kategori' => $inventory->kategori,
            'jumlah_per_batch' => $inventory->jumlah_per_batch,
            'jumlah' => $inventory->jumlah,
            'harga' => $inventory->harga,
            'total' => $inventory->jumlah * $inventory->harga,
            'tgl_masuk' => $inventory->tgl_masuk,
            'tgl_expired' => $inventory->tgl_expired,
            'status' => 'Expired',
            'sisa_hari' => $inventory->sisa_hari ?? 0,
            'is_journaled' => false,
        ]);
        echo "Created history.\n";
    }

    $inventory->delete();
    echo "Deleted inventory.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
