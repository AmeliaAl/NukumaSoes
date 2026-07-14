<?php

namespace App\Console\Commands;

use App\Models\Jurnal;
use App\Models\SaldoAwal;
use App\Models\Pembelian;
use App\Models\Overhead;
use App\Services\JurnalService;
use Illuminate\Console\Command;

/**
 * Artisan Command: jurnal:migrate-data-lama
 *
 * Memindahkan data transaksi lama ke tabel jurnal dan jurnal_detail.
 * Dijalankan MANUAL satu kali saja oleh developer yang memiliki data lama.
 * Anggota tim lain yang melakukan fresh install TIDAK perlu menjalankan ini.
 *
 * Cara menjalankan:
 *   php artisan jurnal:migrate-data-lama
 *
 * Dengan preview (tidak menyimpan):
 *   php artisan jurnal:migrate-data-lama --dry-run
 *
 * Sudah dilengkapi pengecekan duplikasi berdasarkan no_referensi,
 * sehingga aman dijalankan berulang kali.
 */
class MigrasiJurnalDataLama extends Command
{
    protected $signature   = 'jurnal:migrate-data-lama {--dry-run : Preview saja, tidak menyimpan data}';
    protected $description = 'Pindahkan data transaksi lama ke tabel jurnal dan jurnal_detail (jalankan manual, hanya sekali)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info($dryRun
            ? '=== DRY RUN — tidak ada data yang disimpan ==='
            : '=== Migrasi Data Lama ke Tabel Jurnal ==='
        );

        $totalBaru   = 0;
        $totalSkip   = 0;

        // ── Setoran Modal Awal ────────────────────────────────────────────
        $this->line('');
        $this->line('Memproses Setoran Modal Awal...');

        $saldoAwals = SaldoAwal::with('coa')->get();
        foreach ($saldoAwals as $sa) {
            $noRef = $sa->no_bukti;
            if (Jurnal::where('no_referensi', $noRef)->exists()) {
                $this->line("  [SKIP] {$noRef} — sudah ada");
                $totalSkip++;
                continue;
            }

            $noAkunKas = $sa->coa->no_akun ?? '111';
            $this->line("  [NEW]  {$noRef} — {$sa->tanggal} Debit {$noAkunKas} Kredit 311 Rp{$sa->nominal}");

            if (!$dryRun) {
                JurnalService::jurnalSetoranModal(
                    $sa->tanggal,
                    $noRef,
                    $noAkunKas,
                    $sa->coa->nama_akun ?? 'Kas',
                    (float) $sa->nominal
                );
            }
            $totalBaru++;
        }

        // ── Pembelian ─────────────────────────────────────────────────────
        $this->line('');
        $this->line('Memproses Pembelian Bahan Baku...');

        $pembelians = Pembelian::with('coa')->get();
        foreach ($pembelians as $pb) {
            $noRef = 'PB-' . str_pad($pb->id, 3, '0', STR_PAD_LEFT);
            if (Jurnal::where('no_referensi', $noRef)->exists()) {
                $this->line("  [SKIP] {$noRef} — sudah ada");
                $totalSkip++;
                continue;
            }

            $subtotal    = (float) ($pb->subtotal ?: ($pb->qty * $pb->harga));
            $diskon      = (float) ($pb->diskon  ?? 0);
            $ongkir      = (float) ($pb->ongkir  ?? 0);
            $totalBersih = (float) ($pb->total_bersih ?? ($subtotal - $diskon));
            $grandTotal  = (float) ($pb->grand_total  ?? ($totalBersih + $ongkir));
            $noAkunBayar  = $pb->coa->no_akun ?? '111';

            $this->line("  [NEW]  {$noRef} — {$pb->tanggal} Subtotal:{$subtotal} Bayar:{$noAkunBayar}");

            if (!$dryRun) {
                JurnalService::jurnalPembelian(
                    $pb->tanggal, $noRef,
                    $subtotal, $diskon, $ongkir, $grandTotal,
                    $noAkunBayar, $pb->coa->nama_akun ?? 'Kas'
                );
            }
            $totalBaru++;
        }

        // ── Overhead ──────────────────────────────────────────────────────
        $this->line('');
        $this->line('Memproses Overhead...');

        $overheads = Overhead::with(['details.coa', 'details.paymentCoa', 'coa', 'paymentCoa'])->get();
        foreach ($overheads as $oh) {
            $noRef = 'OH-' . str_pad($oh->id, 3, '0', STR_PAD_LEFT);
            if (Jurnal::where('no_referensi', $noRef)->exists()) {
                $this->line("  [SKIP] {$noRef} — sudah ada");
                $totalSkip++;
                continue;
            }

            $details = [];
            if ($oh->details->count() > 0) {
                foreach ($oh->details as $det) {
                    $details[] = [
                        'no_akun_beban' => $det->coa->no_akun ?? '',
                        'nama_beban'    => $det->coa->nama_akun ?? 'Biaya Overhead',
                        'no_akun_bayar' => $det->paymentCoa->no_akun ?? '111',
                        'nominal'       => $det->nominal,
                    ];
                }
            } else {
                $details[] = [
                    'no_akun_beban' => $oh->coa->no_akun ?? '',
                    'nama_beban'    => $oh->coa->nama_akun ?? 'Biaya Overhead',
                    'no_akun_bayar' => $oh->paymentCoa->no_akun ?? '111',
                    'nominal'       => $oh->nominal,
                ];
            }

            $this->line("  [NEW]  {$noRef} — {$oh->tanggal}");

            if (!$dryRun) {
                JurnalService::jurnalOverhead($oh->tanggal, $noRef, $details);
            }
            $totalBaru++;
        }

        // ── Ringkasan ─────────────────────────────────────────────────────
        $this->line('');
        $this->info("Selesai. Dibuat: {$totalBaru} jurnal, Dilewati: {$totalSkip} jurnal.");

        if ($dryRun) {
            $this->warn('DRY RUN — tidak ada data yang disimpan. Jalankan tanpa --dry-run untuk menyimpan.');
        }

        return Command::SUCCESS;
    }
}
