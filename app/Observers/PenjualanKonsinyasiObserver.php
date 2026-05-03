<?php

namespace App\Observers;

use App\Models\PenjualanKonsinyasi;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\Log;

class PenjualanKonsinyasiObserver
{
    /**
     * Handle the PenjualanKonsinyasi "created" event.
     */
    public function created(PenjualanKonsinyasi $penjualan): void
    {
        try {
            // Auto-generate Sales Order setelah penjualan konsinyasi dibuat
            if ($penjualan->detailKonsinyasi()->count() > 0) {
                SalesOrder::createFromPenjualanKonsinyasi($penjualan);
                Log::info("Sales Order created for Penjualan Konsinyasi: {$penjualan->no_konsinyasi}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to create Sales Order for Penjualan Konsinyasi: {$e->getMessage()}");
        }
    }
}
