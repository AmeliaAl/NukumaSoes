<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SaldoAwalService;
use App\Models\saldoawal;

class GenerateJurnalSaldoAwal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jurnal:generate-saldo-awal {--bulan=} {--tahun=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate jurnal untuk saldo awal yang belum punya jurnal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bulan = $this->option('bulan');
        $tahun = $this->option('tahun');

        if ($bulan && $tahun) {
            // Generate untuk periode spesifik
            $this->info("Generating jurnal saldo awal untuk {$bulan}/{$tahun}...");
            
            $jurnal = SaldoAwalService::generateJurnalSaldoAwal((int)$bulan, (int)$tahun);
            
            if ($jurnal) {
                $this->info("✅ Jurnal berhasil dibuat: {$jurnal->no_referensi}");
                $this->info("   Total Debit: Rp " . number_format($jurnal->details->sum('debit'), 0, ',', '.'));
                $this->info("   Total Kredit: Rp " . number_format($jurnal->details->sum('credit'), 0, ',', '.'));
            } else {
                $this->warn("⚠️  Tidak ada saldo awal untuk periode ini atau jurnal sudah ada");
            }
        } else {
            // Generate untuk semua periode yang belum punya jurnal
            $this->info("Generating jurnal untuk semua saldo awal yang belum punya jurnal...");
            
            $periodes = saldoawal::whereNull('jurnal_id')
                ->select('bulan', 'tahun')
                ->groupBy('bulan', 'tahun')
                ->get();

            if ($periodes->isEmpty()) {
                $this->info("✅ Semua saldo awal sudah punya jurnal!");
                return 0;
            }

            $count = 0;
            foreach ($periodes as $periode) {
                $jurnal = SaldoAwalService::generateJurnalSaldoAwal($periode->bulan, $periode->tahun);
                
                if ($jurnal) {
                    $this->info("✅ {$periode->bulan}/{$periode->tahun}: {$jurnal->no_referensi}");
                    $count++;
                }
            }

            $this->info("\n🎉 Selesai! Total {$count} jurnal berhasil dibuat.");
        }

        return 0;
    }
}
