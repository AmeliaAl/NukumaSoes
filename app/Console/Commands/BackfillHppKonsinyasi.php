<?php

namespace App\Console\Commands;

use App\Models\DetailKonsinyasi;
use App\Models\DetailLaporanKonsinyasi;
use App\Models\DetailPersediaanProduk;
use App\Models\LaporanKonsinyasi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillHppKonsinyasi extends Command
{
    protected $signature   = 'hpp:backfill-konsinyasi {--dry-run : Tampilkan perubahan tanpa menyimpan}';
    protected $description = 'Isi harga_modal_per_pack dan subtotal_hpp pada detail_konsinyasi dan detail_laporan_konsinyasi yang masih 0';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN — tidak ada perubahan yang disimpan.');
        }

        // ── 1. Backfill detail_konsinyasi ────────────────────────────────
        $this->info('Memproses detail_konsinyasi...');

        $details = DetailKonsinyasi::where('harga_modal_per_pack', 0)
            ->orWhereNull('harga_modal_per_pack')
            ->get();

        $updatedKonsinyasi = 0;

        foreach ($details as $detail) {
            $hpp = $this->getHppRataRata($detail->barang_id);

            if ($hpp <= 0) {
                $this->line("  SKIP detail_konsinyasi ID:{$detail->id} barang:{$detail->barang_id} — tidak ada data persediaan");
                continue;
            }

            $subtotalHpp = round($hpp * $detail->qty_titip, 2);

            $this->line("  UPDATE detail_konsinyasi ID:{$detail->id} barang:{$detail->barang_id} qty:{$detail->qty_titip} hpp:{$hpp} subtotal_hpp:{$subtotalHpp}");

            if (! $dryRun) {
                $detail->update([
                    'harga_modal_per_pack' => $hpp,
                    'subtotal_hpp'         => $subtotalHpp,
                ]);
            }

            $updatedKonsinyasi++;
        }

        $this->info("  {$updatedKonsinyasi} baris detail_konsinyasi diupdate.");

        // ── 2. Backfill detail_laporan_konsinyasi ────────────────────────
        $this->info('Memproses detail_laporan_konsinyasi...');

        $detailLaporan = DetailLaporanKonsinyasi::where('harga_modal_per_pack', 0)
            ->orWhereNull('harga_modal_per_pack')
            ->get();

        $updatedLaporan = 0;

        foreach ($detailLaporan as $detail) {
            $hpp = $this->getHppRataRata($detail->barang_id);

            if ($hpp <= 0) {
                $this->line("  SKIP detail_laporan ID:{$detail->id} barang:{$detail->barang_id} — tidak ada data persediaan");
                continue;
            }

            $subtotalHpp = round($hpp * $detail->qty_terjual, 2);

            $this->line("  UPDATE detail_laporan ID:{$detail->id} laporan:{$detail->no_laporan} barang:{$detail->barang_id} qty:{$detail->qty_terjual} hpp:{$hpp} subtotal_hpp:{$subtotalHpp}");

            if (! $dryRun) {
                $detail->update([
                    'harga_modal_per_pack' => $hpp,
                    'subtotal_hpp'         => $subtotalHpp,
                ]);
            }

            $updatedLaporan++;
        }

        $this->info("  {$updatedLaporan} baris detail_laporan_konsinyasi diupdate.");

        // ── 3. Recalculate total_hpp di laporan_konsinyasi ───────────────
        if (! $dryRun && $updatedLaporan > 0) {
            $this->info('Menghitung ulang total_hpp di laporan_konsinyasi...');

            $laporanIds = $detailLaporan->pluck('no_laporan')->unique();

            foreach ($laporanIds as $noLaporan) {
                $laporan = LaporanKonsinyasi::where('no_laporan', $noLaporan)->first();
                if ($laporan) {
                    $totalHpp = (float) $laporan->detailLaporan()->sum('subtotal_hpp');
                    $laporan->updateQuietly(['total_hpp' => $totalHpp]);
                    $this->line("  laporan {$noLaporan} total_hpp={$totalHpp}");
                }
            }
        }

        $this->info('Selesai.');

        return self::SUCCESS;
    }

    /**
     * Ambil HPP rata-rata dari detail_persediaan_produk untuk barang_id tertentu.
     * Menggunakan rata-rata tertimbang dari semua batch yang ada.
     */
    private function getHppRataRata(int $barangId): float
    {
        $result = DetailPersediaanProduk::where('barang_id', $barangId)
            ->where('harga_modal_per_pack', '>', 0)
            ->selectRaw('SUM(stok_awal * harga_modal_per_pack) / NULLIF(SUM(stok_awal), 0) as hpp_rata')
            ->value('hpp_rata');

        return (float) ($result ?? 0);
    }
}
