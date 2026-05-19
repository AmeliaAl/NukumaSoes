<?php

namespace App\Filament\Resources\InventoryResource\Pages;

use App\Filament\Resources\InventoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInventory extends CreateRecord
{
    protected static string $resource = InventoryResource::class;

    protected function afterCreate(): void
    {
        $data = $this->record;

        $coaPersediaanJadi = \App\Models\Coa::where('nama_akun', 'LIKE', '%Persediaan Produk Jadi%')->first();
        $refPersediaanJadi = $coaPersediaanJadi ? $coaPersediaanJadi->kode_akun : '113';

        $coaProdukProses = \App\Models\Coa::where('nama_akun', 'LIKE', '%Produk Dalam Proses%')->first();
        $refProdukProses = $coaProdukProses ? $coaProdukProses->kode_akun : '114';

        $totalHarga = $data->jumlah * $data->harga;

        \App\Models\JurnalUmum::create([
            'tanggal' => $data->tgl_masuk,
            'keterangan' => 'Persediaan Produk Jadi',
            'ref' => $refPersediaanJadi,
            'debit' => $totalHarga,
            'kredit' => 0,
        ]);

        \App\Models\JurnalUmum::create([
            'tanggal' => $data->tgl_masuk,
            'keterangan' => 'Produk Dalam Proses',
            'ref' => $refProdukProses,
            'debit' => 0,
            'kredit' => $totalHarga,
        ]);
    }
}
