<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PersediaanEntry;
use App\Models\KartuStokEntry;
use App\Models\JurnalUmum;
use App\Models\Inventory;

echo "=== HAPUS RIWAYAT PRODUK MASUK ===" . PHP_EOL;

// Ambil semua entries sebelum dihapus
$entries = PersediaanEntry::all();
echo "Total PersediaanEntry yang akan dihapus: " . $entries->count() . PHP_EOL;

// Reset inventory jumlah (kurangi jumlah_masuk karena dibatalkan)
foreach ($entries as $entry) {
    $inv = Inventory::find($entry->inventory_id);
    if ($inv) {
        $inv->jumlah -= (int)$entry->jumlah_masuk;
        if ($inv->jumlah < 0) $inv->jumlah = 0;
        $inv->save();
        echo "Inventory #{$inv->id} ({$inv->nama_produk} - {$inv->no_batch}): jumlah diset ke {$inv->jumlah}" . PHP_EOL;
    }
}

// Hapus KartuStokEntry yang keterangannya berupa Produk Masuk
$deleted_kse = KartuStokEntry::where('keterangan', 'like', 'Produk Masuk%')->delete();
echo "KartuStokEntry 'Produk Masuk' dihapus: {$deleted_kse}" . PHP_EOL;

// Hapus JurnalUmum terkait Persediaan
$deleted_jurnal = JurnalUmum::where('ref_type', 'Persediaan')->delete();
echo "JurnalUmum (ref_type=Persediaan) dihapus: {$deleted_jurnal}" . PHP_EOL;

// Hapus semua PersediaanEntry
PersediaanEntry::truncate();
echo "PersediaanEntry berhasil dikosongkan." . PHP_EOL;

echo PHP_EOL . "=== SELESAI ===" . PHP_EOL;
