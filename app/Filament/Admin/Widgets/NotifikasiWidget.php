<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Aset;
use Filament\Widgets\Widget;
use Carbon\Carbon;
use App\Models\Penyusutan;

class NotifikasiWidget extends Widget
{
    protected string $view = 'filament.admin.widgets.notifikasi-widget';

    protected function getViewData(): array
    {
        $asets = Aset::with(['kategori_aset', 'pemeliharaan'])->get();

        $pemeliharaan = $asets
            ->filter(function ($aset) {
                $next = $aset->getNextPemeliharaan();

                if (! $next) {
                    return false;
                }

                $selisihHari = now()->startOfDay()->diffInDays($next->copy()->startOfDay(), false);

                return $selisihHari <= 0 && $selisihHari >= -3;
            })
            ->filter(function ($aset) {
                $next = $aset->getNextPemeliharaan();

                if (! $next) {
                    return false;
                }

                if (empty($aset->dismissed_reminder_date)) {
                    return true;
                }

                return Carbon::parse($aset->dismissed_reminder_date)->format('Y-m-d') !== $next->format('Y-m-d');
            })
            ->sortBy(fn ($aset) => $aset->getNextPemeliharaan())
            ->values();

        $notifs = $pemeliharaan->map(function ($aset) {
            $next = $aset->getNextPemeliharaan();
            $selisihHari = now()->startOfDay()->diffInDays($next->copy()->startOfDay(), false);

            return [
                'judul' => 'Pemeliharaan aset',
                'pesan' => $aset->nama_aset . ' (' . $aset->kode_aset . ') jadwal pemeliharaan jatuh tempo ' . $next->format('d M Y'),
                'tanggal' => $next,
                'waktu' => $selisihHari === 0
                    ? 'Hari ini'
                    : abs($selisihHari) . ' hari lalu',
                'warna' => 'warning',
            ];
        });

        //2. Notif penyusutan bulan lalu belum diposting
        $bulanLalu = now()->copy()->subMonth();
        $periodeBulanLalu = $bulanLalu->format('Y-m');
        $namaPeriode = $bulanLalu->translatedFormat('F Y');

        $sudahAdaPenyusutan = Penyusutan::where('periode', $periodeBulanLalu)->exists();

        if (! $sudahAdaPenyusutan) {
            $notifs->push([
                'judul' => 'Penyusutan',
                'pesan' => 'Jurnal penyusutan periode ' . $namaPeriode . ' belum diposting',
                'tanggal' => now(),
                'waktu' => 'Perlu diproses',
                'warna' => 'info',
            ]);
        }

        return [
            'notifs' => $notifs,
        ];
    }
}