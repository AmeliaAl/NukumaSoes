<?php

namespace App\Console\Commands;

use App\Models\PenjualanNonKonsinyasi;
use App\Models\PenjualanKonsinyasi;
use App\Models\SalesOrder;
use Illuminate\Console\Command;

class GenerateMissingSalesOrders extends Command
{
    protected $signature = 'so:generate-missing';
    protected $description = 'Generate Sales Orders untuk penjualan yang belum memiliki SO';

    public function handle()
    {
        $this->info('Generating missing Sales Orders...');

        // Generate SO untuk Penjualan Non Konsinyasi
        $penjualanNonKonsinyasi = PenjualanNonKonsinyasi::whereDoesntHave('salesOrder')
            ->whereHas('detailPenjualan')
            ->get();

        $countNonKonsinyasi = 0;
        foreach ($penjualanNonKonsinyasi as $penjualan) {
            try {
                SalesOrder::createFromPenjualanNonKonsinyasi($penjualan);
                $countNonKonsinyasi++;
                $this->info("✓ SO created for Non Konsinyasi: {$penjualan->no_invoice}");
            } catch (\Exception $e) {
                $this->error("✗ Failed for {$penjualan->no_invoice}: {$e->getMessage()}");
            }
        }

        // Generate SO untuk Penjualan Konsinyasi
        $penjualanKonsinyasi = PenjualanKonsinyasi::whereDoesntHave('salesOrder')
            ->whereHas('detailKonsinyasi')
            ->get();

        $countKonsinyasi = 0;
        foreach ($penjualanKonsinyasi as $penjualan) {
            try {
                SalesOrder::createFromPenjualanKonsinyasi($penjualan);
                $countKonsinyasi++;
                $this->info("✓ SO created for Konsinyasi: {$penjualan->no_konsinyasi}");
            } catch (\Exception $e) {
                $this->error("✗ Failed for {$penjualan->no_konsinyasi}: {$e->getMessage()}");
            }
        }

        $this->info("\n=== Summary ===");
        $this->info("Non Konsinyasi: {$countNonKonsinyasi} SO created");
        $this->info("Konsinyasi: {$countKonsinyasi} SO created");
        $this->info("Total: " . ($countNonKonsinyasi + $countKonsinyasi) . " SO created");

        return 0;
    }
}
