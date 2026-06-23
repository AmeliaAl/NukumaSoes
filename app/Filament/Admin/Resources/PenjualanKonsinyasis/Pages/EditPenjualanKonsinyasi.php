<?php

namespace App\Filament\Admin\Resources\PenjualanKonsinyasis\Pages;

use App\Filament\Admin\Resources\PenjualanKonsinyasis\PenjualanKonsinyasiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;

class EditPenjualanKonsinyasi extends EditRecord
{
    protected static string $resource = PenjualanKonsinyasiResource::class;

    protected function isFormDisabled(): bool
    {
        return $this->record->status === 'LUNAS';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => $this->record->status !== 'LUNAS'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['diskon'] = $this->record->diskon ?? 0;

        // Data lama mungkin punya term = 0 — paksa minimal 1 agar jatuh_tempo bisa dihitung
        if (empty($data['term']) || (int) $data['term'] < 1) {
            $data['term'] = 30;
        }

        // Hitung jatuh_tempo jika belum ada
        if (empty($data['jatuh_tempo']) && ! empty($data['tanggal'])) {
            $data['jatuh_tempo'] = \Carbon\Carbon::parse($data['tanggal'])
                ->addDays((int) $data['term'])
                ->toDateString();
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $subtotal = (int) $this->record->detailKonsinyasi()->sum('subtotal');
        $diskon = (int) str_replace('.', '', $data['diskon'] ?? '0');

        if ($diskon < 0) {
            $diskon = 0;
        }

        if ($diskon > $subtotal) {
            $diskon = $subtotal;
        }

        $data['diskon'] = $diskon;

        return $data;
    }

    #[On('refreshPenjualanKonsinyasiSummary')]
    public function refreshSummary(): void
    {
        $this->record->refresh();
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        $record->refresh();

        return $record;
    }

    public function getSubtotalProperty(): int
    {
        return (int) $this->record->detailKonsinyasi()->sum('subtotal');
    }

    public function getTotalProperty(): int
    {
        $diskon = (int) ($this->data['diskon'] ?? $this->record->diskon ?? 0);

        return max($this->getSubtotalProperty() - $diskon, 0);
    }
}