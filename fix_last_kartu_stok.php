<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\KartuStokEntry;
use App\Models\PersediaanEntry;
use App\Models\Inventory;

// Cek entry terakhir yang masuk
$kse = KartuStokEntry::where('keterangan', 'like', 'Produk Masuk%')->latest()->first();
$pe  = PersediaanEntry::latest()->first();

if (!$kse || !$pe) {
    echo "Tidak ada entry ditemukan." . PHP_EOL;
    exit;
}

$inv = Inventory::find($pe->inventory_id);

echo "=== DATA ENTRY TERAKHIR ===" . PHP_EOL;
echo "PersediaanEntry  : id={$pe->id}, stok_awal={$pe->stok_awal}, jumlah_masuk={$pe->jumlah_masuk}" . PHP_EOL;
echo "KartuStokEntry   : id={$kse->id}, masuk={$kse->masuk}, no_batch={$kse->no_batch}" . PHP_EOL;
echo "Inventory        : id={$inv->id}, jumlah={$inv->jumlah}, no_batch={$inv->no_batch}" . PHP_EOL;
echo PHP_EOL;

// Hitung stok sebelum penambahan = jumlah sekarang - jumlah_masuk
$stok_sebelum = (int)$inv->jumlah - (int)$pe->jumlah_masuk;
$masuk_benar   = $stok_sebelum + (int)$pe->jumlah_masuk;

echo "Stok sebelum ditambah : {$stok_sebelum}" . PHP_EOL;
echo "Masuk yang benar      : {$stok_sebelum} + {$pe->jumlah_masuk} = {$masuk_benar}" . PHP_EOL;
echo "Masuk saat ini        : {$kse->masuk}" . PHP_EOL;
echo PHP_EOL;

if ($kse->masuk != $masuk_benar) {
    echo "=== KOREKSI ===" . PHP_EOL;
    
    // Update KartuStokEntry
    $kse->masuk = $masuk_benar;
    $kse->save();
    echo "KartuStokEntry id={$kse->id}: masuk dikoreksi → {$masuk_benar}" . PHP_EOL;

    // Update PersediaanEntry stok_awal
    $pe->stok_awal = $stok_sebelum;
    $pe->save();
    echo "PersediaanEntry id={$pe->id}: stok_awal dikoreksi → {$stok_sebelum}" . PHP_EOL;
    
    echo PHP_EOL . "=== KOREKSI SELESAI ===" . PHP_EOL;
} else {
    echo "Data sudah benar, tidak perlu koreksi." . PHP_EOL;
}
