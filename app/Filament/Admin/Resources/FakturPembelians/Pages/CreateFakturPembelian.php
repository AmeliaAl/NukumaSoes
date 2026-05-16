<?php

namespace App\Filament\Admin\Resources\FakturPembelians\Pages;

use App\Filament\Admin\Resources\FakturPembelians\FakturPembelianResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\FakturPembelianService;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateFakturPembelian extends CreateRecord
{
    protected static string $resource = FakturPembelianResource::class;
    protected static ?string $title = 'Tambah Faktur Pembelian';
    

     protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Hitung total_tagihan
        $subtotal = 0;
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $subtotal += (float) ($item['total_harga'] ?? 0);
            }
        }
        
        $data['total_tagihan'] = $subtotal + (float) ($data['biaya_lain'] ?? 0);
        $data['status'] = 'belum_dibayar';
        
        \Log::info('Total Tagihan yang akan disimpan: ' . $data['total_tagihan']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Double check setelah create
        $faktur = $this->record;
        $subtotal = $faktur->items()->sum('total_harga');
        $totalTagihan = $subtotal + ($faktur->biaya_lain ?? 0);
        
        \Log::info("Faktur {$faktur->no_faktur} - Subtotal: {$subtotal}, Biaya Lain: {$faktur->biaya_lain}, Total: {$totalTagihan}");
        
        if ($faktur->total_tagihan != $totalTagihan) {
            $faktur->updateQuietly(['total_tagihan' => $totalTagihan]);
        }

         \App\Services\FakturPembelianService::buatJurnal($this->record);
    }

     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    
}
