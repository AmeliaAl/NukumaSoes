<?php

namespace App\Filament\Admin\Resources\ReturPenjualans\Pages;

use App\Filament\Admin\Resources\ReturPenjualans\ReturPenjualanResource;
use App\Models\Barang;
use Filament\Resources\Pages\CreateRecord;

class CreateReturPenjualan extends CreateRecord
{
    protected static string $resource = ReturPenjualanResource::class;

    protected function afterCreate(): void
    {
        // Tambah stok untuk setiap barang yang diretur
        foreach ($this->record->detailRetur as $detail) {
            Barang::where('id', $detail->barang_id)
                ->increment('stok', (int) $detail->qty);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
