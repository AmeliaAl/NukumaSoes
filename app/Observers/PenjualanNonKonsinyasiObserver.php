<?php

namespace App\Observers;

use App\Models\PenjualanNonKonsinyasi;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\Log;

class PenjualanNonKonsinyasiObserver
{
    /**
     * Handle the PenjualanNonKonsinyasi "created" event.
     */
    public function created(PenjualanNonKonsinyasi $penjualan): void
    {
        try {
            // Auto-generate Sales Order setelah penjualan dibuat
            if ($penjualan->detailPenjualan()->count() > 0) {
                SalesOrder::createFromPenjualanNonKonsinyasi($penjualan);
                Log::info("Sales Order created for Penjualan Non Konsinyasi: {$penjualan->no_invoice}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to create Sales Order for Penjualan Non Konsinyasi: {$e->getMessage()}");
        }
    }
}
