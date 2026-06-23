<?php

namespace App\Filament\Admin\Resources\FakturPembelians\Pages;

use App\Filament\Admin\Resources\FakturPembelians\FakturPembelianResource;
use Filament\Resources\Pages\EditRecord;

class EditFakturPembelian extends EditRecord
{
    protected static string $resource = FakturPembelianResource::class;



    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Hitung total_tagihan sebelum save
        $subtotal = 0;
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $subtotal += (float) ($item['total_harga'] ?? 0);
            }
        }
        
        $data['total_tagihan'] = $subtotal + (float) ($data['biaya_lain'] ?? 0);
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Refresh total setelah items tersimpan
        $faktur = $this->record;
        $subtotal = $faktur->items()->sum('total_harga');
        $totalTagihan = $subtotal + ($faktur->biaya_lain ?? 0);
        
        if ($faktur->total_tagihan != $totalTagihan) {
            $faktur->updateQuietly(['total_tagihan' => $totalTagihan]);
        }
    }
}
