<?php

namespace App\Filament\Admin\Resources\Pembayarans\Pages;

use App\Filament\Admin\Resources\Pembayarans\PembayaranResource; // ← INI KUNCI
use App\Models\PenjualanNonKonsinyasi;
use Filament\Resources\Pages\CreateRecord;

class CreatePembayaran extends CreateRecord
{
    protected static string $resource = PembayaranResource::class;

    protected function afterCreate(): void
{
    $pembayaran = $this->record;

    $penjualan = PenjualanNonKonsinyasi::find($pembayaran->penjualan_id);

    if (! $penjualan) {
        return;
    }

    $penjualan->total_terbayar += $pembayaran->jumlah_bayar;
    $penjualan->save(); // ← TIDAK ADA STATUS
}

protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}

}
