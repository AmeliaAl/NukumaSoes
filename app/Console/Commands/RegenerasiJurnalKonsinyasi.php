<?php

namespace App\Console\Commands;

use App\Models\LaporanKonsinyasi;
use App\Services\JurnalPerpetualService;
use Illuminate\Console\Command;

class RegenerasiJurnalKonsinyasi extends Command
{
    protected $signature   = 'jurnal:regenerasi-konsinyasi {--dry-run : Tampilkan tanpa menyimpan}';
    protected $description = 'Regenerasi jurnal untuk semua laporan konsinyasi yang sudah ada';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN — tidak ada jurnal yang dibuat.');
        }

        $laporans = LaporanKonsinyasi::with('penjualanKonsinyasi', 'detailLaporan')
            ->where('total_laporan', '>', 0)
            ->get();

        $this->info("Memproses {$laporans->count()} laporan konsinyasi...");

        foreach ($laporans as $laporan) {
            $nominal  = (int) $laporan->total_laporan;
            $totalHpp = (float) $laporan->total_hpp;

            $this->line("  {$laporan->no_laporan} | nominal={$nominal} | hpp={$totalHpp}");

            if (! $dryRun) {
                JurnalPerpetualService::laporanKonsinyasiDenganNominal($laporan, $nominal);
            }
        }

        $this->info('Selesai.');

        return self::SUCCESS;
    }
}
