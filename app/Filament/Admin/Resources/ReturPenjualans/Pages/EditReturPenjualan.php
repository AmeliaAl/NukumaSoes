<?php

namespace App\Filament\Admin\Resources\ReturPenjualans\Pages;

use App\Filament\Admin\Resources\ReturPenjualans\ReturPenjualanResource;
use App\Models\Barang;
use App\Models\DetailReturPenjualan;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReturPenjualan extends EditRecord
{
    protected static string $resource = ReturPenjualanResource::class;

    /**
     * Simpan qty lama sebelum form disimpan,
     * agar bisa dihitung selisih perubahan stok.
     */
    protected array $qtySebelumEdit = [];

    protected function beforeSave(): void
    {
        // Snapshot qty detail yang ada saat ini di DB
        $this->qtySebelumEdit = DetailReturPenjualan::where('retur_id', $this->record->id)
            ->pluck('qty', 'barang_id')
            ->toArray();
    }

    protected function afterSave(): void
    {
        // Reload detail terbaru setelah Repeater menyimpan
        $this->record->load('detailRetur');

        $qtyBaru = $this->record->detailRetur
            ->groupBy('barang_id')
            ->map(fn ($items) => $items->sum('qty'))
            ->toArray();

        // Kumpulkan semua barang_id yang terlibat
        $semuaBarangId = array_unique(
            array_merge(array_keys($this->qtySebelumEdit), array_keys($qtyBaru))
        );

        foreach ($semuaBarangId as $barangId) {
            $lama = (int) ($this->qtySebelumEdit[$barangId] ?? 0);
            $baru = (int) ($qtyBaru[$barangId] ?? 0);
            $selisih = $baru - $lama;

            if ($selisih !== 0) {
                // Selisih positif → qty naik → stok bertambah
                // Selisih negatif → qty turun → stok berkurang
                Barang::where('id', $barangId)->increment('stok', $selisih);
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function () {
                    // Kembalikan stok ke kondisi sebelum retur saat dihapus
                    foreach ($this->record->detailRetur as $detail) {
                        Barang::where('id', $detail->barang_id)
                            ->decrement('stok', (int) $detail->qty);
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
