<?php

namespace App\Filament\Admin\Resources\PenjualanNonKonsinyasis\Pages;

use App\Filament\Admin\Resources\Pembayarans\PembayaranResource;
use App\Filament\Admin\Resources\PenjualanNonKonsinyasis\PenjualanNonKonsinyasiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Livewire\Attributes\On;

class EditPenjualanNonKonsinyasi extends EditRecord
{
    protected static string $resource = PenjualanNonKonsinyasiResource::class;

    protected $listeners = ['refresh' => '$refresh'];

    protected bool $isSavingMainForm = false;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $this->isSavingMainForm = true;

        parent::save($shouldRedirect, $shouldSendSavedNotification);

        $this->isSavingMainForm = false;
    }

    protected function afterSave(): void
    {
        if (! $this->isSavingMainForm) {
            return;
        }

        $record = $this->record;

        // Jika tunai, belum ada pembayaran, dan sudah ada detail barang → redirect ke create pembayaran
        if (
            $record->jenis_pembayaran === 'tunai' &&
            $record->pembayaran()->count() === 0 &&
            $record->detailPenjualan()->count() > 0 &&
            ($record->total ?? 0) > 0
        ) {
            $this->redirect(
                PembayaranResource::getUrl('create', [
                    'penjualan_id' => $record->id,
                ])
            );
        }
    }

    #[On('refreshPenjualanNonKonsinyasiSummary')]
    public function refreshSummary(): void
    {
        $this->record->refresh();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $subtotal = (int) $this->record->detailPenjualan()->sum('subtotal');
        $diskon = (int) ($data['diskon'] ?? 0);

        if ($diskon < 0) {
            $diskon = 0;
        }

        if ($diskon > $subtotal) {
            $diskon = $subtotal;
        }

        $data['diskon'] = $diskon;

        return $data;
    }


    public function getSubtotalProperty(): int
    {
        return (int) $this->record->detailPenjualan()->sum('subtotal');
    }

    public function getTotalProperty(): int
    {
        $diskon = (int) ($this->data['diskon'] ?? $this->record->diskon ?? 0);

        return max($this->getSubtotalProperty() - $diskon, 0);
    }
}
