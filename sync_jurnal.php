<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PersediaanEntry;
use App\Models\ProdukKeluarEntry;
use App\Models\JurnalUmum;
use App\Models\Coa;
use Illuminate\Support\Facades\DB;

DB::transaction(function () {
    // 1. Clear existing Jurnal Umum to start fresh
    JurnalUmum::truncate();
    echo "Cleared Jurnal Umum table.\n";

    // 2. Repopulate from Produk Masuk (PersediaanEntry)
    $masukEntries = PersediaanEntry::all();
    $coaPersediaanJadi = Coa::where('nama_akun', 'LIKE', '%Persediaan%')->where('nama_akun', 'LIKE', '%Jadi%')->first();
    $refPersediaanJadi = $coaPersediaanJadi ? $coaPersediaanJadi->kode_akun : '140';
    $coaProdukProses = Coa::where('nama_akun', 'LIKE', '%Persediaan%')->where('nama_akun', 'LIKE', '%proses%')->first();
    $refProdukProses = $coaProdukProses ? $coaProdukProses->kode_akun : '143';

    foreach ($masukEntries as $entry) {
        JurnalUmum::create([
            'tanggal' => $entry->tanggal,
            'keterangan' => 'Persediaan Produk Jadi',
            'ref' => $refPersediaanJadi,
            'debit' => $entry->total_harga,
            'kredit' => 0,
            'id_transaksi' => $entry->id_transaksi,
        ]);
        JurnalUmum::create([
            'tanggal' => $entry->tanggal,
            'keterangan' => 'Produk Dalam Proses',
            'ref' => $refProdukProses,
            'debit' => 0,
            'kredit' => $entry->total_harga,
            'id_transaksi' => $entry->id_transaksi,
        ]);
    }
    echo "Repopulated Jurnal from " . count($masukEntries) . " incoming products.\n";

    // 3. Repopulate from Produk Keluar (ProdukKeluarEntry)
    // Group by id_transaksi to handle multi-product mitra sales as one journal entry
    $keluarGroups = ProdukKeluarEntry::all()->groupBy('id_transaksi');
    
    $coaKas = Coa::where('nama_akun', 'LIKE', '%Kas%')->where('nama_akun', 'NOT LIKE', '%Biaya%')->first();
    $refKas = $coaKas ? $coaKas->kode_akun : '111';
    $coaPenjualan = Coa::where('nama_akun', 'LIKE', '%Penjualan%')->where('nama_akun', 'NOT LIKE', '%Retur%')->first();
    $refPenjualan = $coaPenjualan ? $coaPenjualan->kode_akun : '401';
    $coaHPP = Coa::where('nama_akun', 'LIKE', '%Harga Pokok Penjualan%')->first();
    $refHPP = $coaHPP ? $coaHPP->kode_akun : '500';

    // Refresh Persediaan Jadi search for COGS
    $coaPersediaanJadi = Coa::where('nama_akun', 'LIKE', '%Persediaan%')->where('nama_akun', 'LIKE', '%Jadi%')->first();
    $refPersediaanJadi = $coaPersediaanJadi ? $coaPersediaanJadi->kode_akun : '140';

    foreach ($keluarGroups as $idTransaksi => $group) {
        $totalSelling = $group->sum('total_harga');
        $totalCost = $group->sum(function($item) {
            return ($item->jumlah_pack_keluar ?? $item->jumlah_keluar) * $item->harga_pokok_per_pack;
        });
        $tanggal = $group->first()->tanggal;

        // COGS (Recording only Inventory Movement as per user request)
        if ($totalCost > 0) {
            JurnalUmum::create([
                'tanggal' => $tanggal,
                'keterangan' => 'Persediaan Produk Keluar',
                'ref' => $refHPP,
                'debit' => $totalCost,
                'kredit' => 0,
                'id_transaksi' => $idTransaksi,
            ]);
            JurnalUmum::create([
                'tanggal' => $tanggal,
                'keterangan' => 'Persediaan Produk Jadi',
                'ref' => $refPersediaanJadi,
                'debit' => 0,
                'kredit' => $totalCost,
                'id_transaksi' => $idTransaksi,
            ]);
        }
    }
    echo "Repopulated Jurnal from " . count($keluarGroups) . " outgoing transactions.\n";
});

echo "Finished sync.\n";
