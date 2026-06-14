<?php

namespace App\Filament\Admin\Resources\Pembayarans\Pages;

use App\Filament\Admin\Resources\Pembayarans\PembayaranResource;
use App\Models\PenjualanNonKonsinyasi;
use Filament\Resources\Pages\CreateRecord;

class CreatePembayaran extends CreateRecord
{
    protected static string $resource = PembayaranResource::class;

    public function mount(): void
    {
        parent::mount();

        $penjualanId = request()->query('penjualan_id');

        if ($penjualanId) {
            $penjualan = PenjualanNonKonsinyasi::find($penjualanId);

            if ($penjualan) {
                $totalBayar = $penjualan->pembayaran()->sum('jumlah_bayar');
                $sisa       = max(($penjualan->total ?? 0) - $totalBayar, 0);

                $this->form->fill([
                    'kode_pembayaran' => \App\Models\Pembayaran::getKodePembayaran(),
                    'tanggal_bayar'   => now()->toDateString(),
                    'penjualan_id'    => $penjualan->id,
                    'pelanggan_id'    => $penjualan->pelanggan_id,
                    'total_transaksi' => $penjualan->total,
                    'jumlah_bayar'    => $sisa,
                ]);
            }
        }
    }

    protected function afterCreate(): void
    {
        $pembayaran = $this->record;
        $penjualan  = PenjualanNonKonsinyasi::find($pembayaran->penjualan_id);

        if (! $penjualan) {
            return;
        }

        $penjualan->refreshStatus();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
