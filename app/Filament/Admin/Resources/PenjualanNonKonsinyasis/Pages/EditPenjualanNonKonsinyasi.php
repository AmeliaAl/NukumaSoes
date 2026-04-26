<?php

namespace App\Filament\Admin\Resources\PenjualanNonKonsinyasis\Pages;

use App\Filament\Admin\Resources\PenjualanNonKonsinyasis\PenjualanNonKonsinyasiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Livewire\Attributes\On;

class EditPenjualanNonKonsinyasi extends EditRecord
{
    protected static string $resource = PenjualanNonKonsinyasiResource::class;

    protected $listeners = ['refresh' => '$refresh'];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    #[On('refreshPenjualanNonKonsinyasiSummary')]
    public function refreshSummary(): void
    {
        $this->record->refresh();
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