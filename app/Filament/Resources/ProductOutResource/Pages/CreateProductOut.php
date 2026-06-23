<?php

namespace App\Filament\Resources\ProductOutResource\Pages;

use App\Filament\Resources\ProductOutResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductOut extends CreateRecord
{
    protected static string $resource = ProductOutResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['id_transaksi'] = 'TXN-KELUAR-' . strtoupper(bin2hex(random_bytes(4)));
        return $data;
    }

    protected function afterCreate(): void
    {
        $data = $this->record;

        $coaPersediaanProduk = \App\Models\Coa::where('nama_akun', 'LIKE', '%Persediaan Produk%')->where('nama_akun', 'NOT LIKE', '%Jadi%')->first();
        $refPersediaanProduk = $coaPersediaanProduk ? $coaPersediaanProduk->kode_akun : '115';

        $coaPersediaanJadi = \App\Models\Coa::where('nama_akun', 'LIKE', '%Persediaan Produk Jadi%')->first();
        $refPersediaanJadi = $coaPersediaanJadi ? $coaPersediaanJadi->kode_akun : '113';

        \App\Models\JurnalUmum::create([
            'tanggal' => $data->tanggal,
            'keterangan' => 'Persediaan Produk',
            'ref' => $refPersediaanProduk,
            'debit' => $data->total_harga,
            'kredit' => 0,
        ]);

        \App\Models\JurnalUmum::create([
            'tanggal' => $data->tanggal,
            'keterangan' => 'Persediaan Produk Jadi',
            'ref' => $refPersediaanJadi,
            'debit' => 0,
            'kredit' => $data->total_harga,
        ]);
    }
}
