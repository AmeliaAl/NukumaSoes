<?php

namespace App\Filament\Admin\Widgets;

use App\Models\PenjualanNonKonsinyasi;
use App\Models\TagihanKonsinyasi;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GrafikPiutang extends ChartWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static bool $isDiscovered = false;
    protected ?string $maxHeight = '250px';

    public function getHeading(): string
    {
        return 'Grafik Piutang';
    }

    protected function getFilters(): ?array
    {
        $tahunAwal  = 2024;
        $tahunAkhir = (int) Carbon::now()->format('Y');
        $filters    = [];
        for ($y = $tahunAkhir; $y >= $tahunAwal; $y--) {
            $filters[(string) $y] = (string) $y;
        }
        return $filters;
    }

    protected function getData(): array
    {
        $tahun = (int) ($this->filter ?? Carbon::now()->format('Y'));

        // ── Pre-aggregate Non Konsinyasi ─────
        // ─────────────────────────────
        // Total penjualan per bulan (kumulatif dihitung di loop)
        $penjualanPerBulan = DB::table('penjualan_non_konsinyasi')
            ->selectRaw('MONTH(tanggal) as bulan, SUM(total) as total')
            ->whereYear('tanggal', $tahun)
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        // Total pembayaran per bulan (kumulatif dihitung di loop)
        $pembayaranPerBulan = DB::table('pembayaran')
            ->join('penjualan_non_konsinyasi', 'pembayaran.penjualan_id', '=', 'penjualan_non_konsinyasi.id')
            ->selectRaw('MONTH(pembayaran.tanggal_bayar) as bulan, SUM(pembayaran.jumlah_bayar) as total')
            ->whereYear('pembayaran.tanggal_bayar', $tahun)
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        // ── Pre-aggregate Konsinyasi ──────────────────────────────────────
        // Sisa tagihan per bulan: ambil sisa_tagihan dari tagihan yang
        // tanggal_tagihan <= akhir bulan (snapshot saldo akhir bulan)
        // Kita hitung kumulatif: total tagihan s.d. bulan - total terbayar s.d. bulan
        $tagihanPerBulan = DB::table('tagihan_konsinyasi')
            ->selectRaw('MONTH(tanggal_tagihan) as bulan, SUM(total_tagihan) as total_tagihan, SUM(total_terbayar) as total_terbayar')
            ->whereYear('tanggal_tagihan', $tahun)
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan')
            ->toArray();

        // ── Build chart data ──────────────────────────────────────────────
        $bulan              = [];
        $piutangNK          = [];
        $piutangKonsinyasi  = [];

        $kumulatifPenjualan  = 0;
        $kumulatifPembayaran = 0;
        $kumulatifTagihan    = 0;
        $kumulatifTerbayar   = 0;

        for ($m = 1; $m <= 12; $m++) {
            $bulan[] = Carbon::create($tahun, $m, 1)->translatedFormat('M');

            // Non Konsinyasi: saldo kumulatif
            $kumulatifPenjualan  += (int) ($penjualanPerBulan[$m]  ?? 0);
            $kumulatifPembayaran += (int) ($pembayaranPerBulan[$m] ?? 0);
            $piutangNK[]          = max($kumulatifPenjualan - $kumulatifPembayaran, 0);

            // Konsinyasi: saldo kumulatif dari tagihan - terbayar
            $kumulatifTagihan  += (int) ($tagihanPerBulan[$m]->total_tagihan  ?? 0);
            $kumulatifTerbayar += (int) ($tagihanPerBulan[$m]->total_terbayar ?? 0);
            $piutangKonsinyasi[] = max($kumulatifTagihan - $kumulatifTerbayar, 0);
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Piutang Non Konsinyasi',
                    'data'            => $piutangNK,
                    'borderColor'     => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Piutang Konsinyasi',
                    'data'            => $piutangKonsinyasi,
                    'borderColor'     => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $bulan,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'ticks' => [
                        'stepSize' => 100000,
                    ],
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
