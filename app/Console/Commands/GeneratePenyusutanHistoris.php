<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Aset;
use App\Models\Penyusutan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GeneratePenyusutanHistoris extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aset:generate-historis 
                            {--aset-id= : ID aset tertentu (kosongkan untuk semua aset)}
                            {--sampai= : Generate sampai tanggal tertentu (format: YYYY-MM, default: bulan ini)}
                            {--force : Paksa generate ulang meski sudah ada data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate penyusutan historis dari tanggal perolehan sampai sekarang';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai generate penyusutan historis...');
        $this->newLine();

        // Tentukan sampai bulan berapa
        $sampai = $this->option('sampai') 
            ? Carbon::parse($this->option('sampai') . '-01')
            : Carbon::now();

        // Ambil aset yang akan diproses
        $query = Aset::query();
        if ($this->option('aset-id')) {
            $query->where('id', $this->option('aset-id'));
        }
        $asets = $query->get();

        if ($asets->isEmpty()) {
            $this->error('❌ Tidak ada aset yang ditemukan!');
            return Command::FAILURE;
        }

        $this->info("📦 Ditemukan {$asets->count()} aset untuk diproses");
        $this->newLine();

        $totalGenerated = 0;
        $totalSkipped = 0;

        foreach ($asets as $aset) {
            $this->info("🔧 Memproses: {$aset->nama_aset} ({$aset->kode_aset})");
            
            $tanggalPerolehan = Carbon::parse($aset->tanggal_perolehan);
            $masaManfaatBulan = $aset->masa_manfaat * 12;
            
            // Hitung sampai bulan mana (yang lebih kecil antara sampai atau masa manfaat)
            $bulanMulai = $tanggalPerolehan->copy()->startOfMonth();
            $bulanAkhir = $sampai->copy()->startOfMonth();
            $bulanAkhirMasaManfaat = $tanggalPerolehan->copy()->addMonths($masaManfaatBulan - 1)->startOfMonth();
            
            // Gunakan yang lebih kecil
            if ($bulanAkhir->gt($bulanAkhirMasaManfaat)) {
                $bulanAkhir = $bulanAkhirMasaManfaat;
                $this->warn("   ⚠️  Dibatasi sampai masa manfaat: {$bulanAkhir->format('F Y')}");
            }

            $this->line("   📅 Periode: {$bulanMulai->format('F Y')} s/d {$bulanAkhir->format('F Y')}");

            $generated = 0;
            $skipped = 0;
            $currentMonth = $bulanMulai->copy();
            
            // Ambil akumulasi terakhir sebelum periode yang akan di-generate
            $penyusutanTerakhir = Penyusutan::where('aset_id', $aset->id)
                ->where('periode', '<', $bulanMulai->format('Y-m'))
                ->orderBy('periode', 'desc')
                ->first();
            
            $akumulasiSebelumnya = $penyusutanTerakhir ? $penyusutanTerakhir->akumulasi_penyusutan : 0;

            while ($currentMonth->lte($bulanAkhir)) {
                $periode = $currentMonth->format('Y-m');

                // Cek apakah sudah ada
                $penyusutanExisting = Penyusutan::where('aset_id', $aset->id)
                    ->where('periode', $periode)
                    ->first();

                if ($penyusutanExisting && !$this->option('force')) {
                    $skipped++;
                    // Update akumulasi sebelumnya dari data yang ada
                    $akumulasiSebelumnya = $penyusutanExisting->akumulasi_penyusutan;
                    $currentMonth->addMonth();
                    continue;
                }

                // Hapus jika force
                if ($penyusutanExisting && $this->option('force')) {
                    $penyusutanExisting->delete();
                }

                // Generate penyusutan
                $this->generatePenyusutan($aset, $periode, $akumulasiSebelumnya);
                $generated++;

                // Update akumulasi untuk bulan berikutnya
                $penyusutanBaru = Penyusutan::where('aset_id', $aset->id)
                    ->where('periode', $periode)
                    ->first();
                if ($penyusutanBaru) {
                    $akumulasiSebelumnya = $penyusutanBaru->akumulasi_penyusutan;
                }

                $currentMonth->addMonth();
            }

            $this->info("   ✅ Generated: {$generated} bulan | Skipped: {$skipped} bulan");
            $this->newLine();

            $totalGenerated += $generated;
            $totalSkipped += $skipped;
        }

        $this->newLine();
        $this->info('🎉 Selesai!');
        $this->info("📊 Total Generated: {$totalGenerated} record");
        $this->info("⏭️  Total Skipped: {$totalSkipped} record");

        return Command::SUCCESS;
    }

    protected function generatePenyusutan(Aset $aset, string $periode, float $akumulasiSebelumnya)
    {
        // Hitung beban penyusutan
        $bebanPenyusutan = $this->hitungBebanPenyusutan($aset);
        
        // Hitung akumulasi baru
        $akumulasiPenyusutanBaru = $akumulasiSebelumnya + $bebanPenyusutan;
        
        // Hitung nilai buku baru
        $nilaiBukuBaru = $aset->nilai_perolehan - $akumulasiPenyusutanBaru;

        // Pastikan tidak kurang dari nilai residu
        if ($nilaiBukuBaru < $aset->nilai_residu) {
            $bebanPenyusutan = ($aset->nilai_perolehan - $akumulasiSebelumnya) - $aset->nilai_residu;
            $akumulasiPenyusutanBaru = $aset->nilai_perolehan - $aset->nilai_residu;
            $nilaiBukuBaru = $aset->nilai_residu;
        }

        // Simpan ke database
        Penyusutan::create([
            'aset_id' => $aset->id,
            'periode' => $periode,
            'beban_penyusutan' => $bebanPenyusutan,
            'akumulasi_penyusutan' => $akumulasiPenyusutanBaru,
            'nilai_buku' => $nilaiBukuBaru,
        ]);
    }

    protected function hitungBebanPenyusutan(Aset $aset): float
    {
        return match($aset->metode_penyusutan) {
            'garis_lurus' => $this->hitungGarisLurus($aset),
            'saldo_menurun' => $this->hitungSaldoMenurun($aset),
            'jumlah_angka_tahun' => $this->hitungJumlahAngkaTahun($aset),
            default => 0,
        };
    }

    protected function hitungGarisLurus(Aset $aset): float
    {
        $nilaiSusut = $aset->nilai_perolehan - $aset->nilai_residu;
        $masaManfaatBulan = $aset->masa_manfaat * 12;
        return $masaManfaatBulan > 0 ? $nilaiSusut / $masaManfaatBulan : 0;
    }

    protected function hitungSaldoMenurun(Aset $aset): float
    {
        $rate = (2 / $aset->masa_manfaat) / 12;
        return $aset->nilai_buku * $rate;
    }

    protected function hitungJumlahAngkaTahun(Aset $aset): float
    {
        $n = $aset->masa_manfaat;
        $jumlahAngkaTahun = ($n * ($n + 1)) / 2;
        $nilaiSusutPerTahun = ($aset->nilai_perolehan - $aset->nilai_residu) / $jumlahAngkaTahun;
        $tahunTersisa = $n - floor(($aset->akumulasi_penyusutan / $nilaiSusutPerTahun));
        $tahunTersisa = max(1, $tahunTersisa);
        return ($tahunTersisa * $nilaiSusutPerTahun) / 12;
    }
}
