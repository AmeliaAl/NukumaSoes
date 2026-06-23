<?php

namespace App\Filament\Admin\Resources\PemakaianPersediaans\Pages;

use App\Filament\Admin\Resources\PemakaianPersediaans\PemakaianPersediaanResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\AsetLancar;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class CreatePemakaianPersediaan extends CreateRecord
{
    protected static string $resource = PemakaianPersediaanResource::class;
    protected static ?string $title = 'Tambah Transaksi Pemakaian Bahan Habis Pakai';


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $aset = AsetLancar::findOrFail($data['aset_lancar_id']);
        if (!$aset) {
            Notification::make()
                ->title('Aset tidak ditemukan')
                ->danger()
                ->send();
            
            $this->halt();
        }

        if ($data['jumlah'] > $aset->stok_tersedia) {
            Notification::make()
                ->title('Stok tidak mencukupi!')
                ->body("Stok tersedia: {$aset->stok_tersedia}, diminta: {$data['jumlah']}")
                ->danger()
                ->send();
                 $this->halt(); // Stop proses create
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        
        DB::transaction(function () use ($record) {
            $aset = AsetLancar::find($record->aset_lancar_id);
            
            if ($aset) {
                $aset->kurangiStok($record->jumlah);
            }
        });

        Notification::make()
            ->title('Pengeluaran berhasil dicatat')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
