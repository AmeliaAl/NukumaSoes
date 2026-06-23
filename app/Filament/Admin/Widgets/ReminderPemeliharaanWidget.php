<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use App\Models\Aset;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;


class ReminderPemeliharaanWidget extends Widget
{
    protected string $view = 'filament.admin.widgets.reminder-pemeliharaan-widget';

    public array $dismissedIds = [];

    public function dismiss(int $id): void
{
    $aset = Aset::find($id);

    if (! $aset) return;

    $next = $aset->getNextPemeliharaan();

    if (! $next) return;

    $aset->update([
        'reminder' => $next->format('Y-m-d'),
    ]);
}  

     protected function getViewData(): array
{
    $allAset = Aset::with(['kategori_aset', 'pemeliharaan'])->get();

    $asetPerluPemeliharaan = $allAset
        ->filter(fn ($aset) => $aset->isButuhPemeliharaan(7))
        ->filter(function ($aset) {
    $next = $aset->getNextPemeliharaan();

    if (! $next) {
        return false;
    }

    if (empty($aset->reminder)) {
        return true;
    }

    return Carbon::parse($aset->reminder)->format('Y-m-d') !== $next->format('Y-m-d');
})
        ->map(function ($aset) {
            $next = $aset->getNextPemeliharaan();

            if (! $next) {
                return null;
            }

            $selisihHari = now()->startOfDay()->diffInDays($next->copy()->startOfDay(), false);

            if ($selisihHari < 0) {
                $status = 'terlambat';
                $hariTerlambat = abs($selisihHari);
                $akanJatuhTempo = null;
            } elseif ($selisihHari === 0) {
                $status = 'hari_ini';
                $hariTerlambat = 0;
                $akanJatuhTempo = 0;
            } else {
                $status = 'segera';
                $hariTerlambat = 0;
                $akanJatuhTempo = $selisihHari;
            }

            return [
                'id' => $aset->id,
                'nama_aset' => $aset->nama_aset,
                'kode_aset' => $aset->kode_aset,

                'tanggal_perolehan_raw' => $aset->tanggal_perolehan,
                'tanggal_perolehan' => $aset->tanggal_perolehan
                    ? Carbon::parse($aset->tanggal_perolehan)->format('d M Y')
                    : '-',

                'interval' => $aset->getIntervalPemeliharaanBulan(),
                'next_pemeliharaan_raw' => $next->format('Y-m-d'),
                'next_pemeliharaan' => $next->format('d M Y'),

                'status' => $status,
                'hari_terlambat' => $hariTerlambat,
                'akan_jatuh_tempo' => $akanJatuhTempo,
            ];
        })
        ->filter()
        ->sortBy('next_pemeliharaan_raw')
        ->values();

    return [
        'aset' => $asetPerluPemeliharaan,
        'jumlah' => $asetPerluPemeliharaan->count(),
    ];
}
}
