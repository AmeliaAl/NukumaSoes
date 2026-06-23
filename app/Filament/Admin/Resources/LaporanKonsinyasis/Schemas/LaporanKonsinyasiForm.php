<?php

namespace App\Filament\Admin\Resources\LaporanKonsinyasis\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use App\Models\LaporanKonsinyasi;
use App\Models\PenjualanKonsinyasi;

class LaporanKonsinyasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('no_laporan')
                ->label('No Laporan')
                ->default(fn () => LaporanKonsinyasi::generateNo())
                ->disabled()
                ->dehydrated()
                ->required(),

            Select::make('penjualan_konsinyasi_id')
                ->label('No Konsinyasi')
                ->relationship(
                    'penjualanKonsinyasi',
                    'no_konsinyasi',
                    fn ($query, $record) => $query->whereIn('status', ['BELUM TERJUAL', 'SEBAGIAN TERJUAL'])
                        ->when(
                            $record?->penjualan_konsinyasi_id,
                            fn ($q) => $q->orWhere('id', $record->penjualan_konsinyasi_id)
                        )
                )
                ->searchable()
                ->required()
                ->live(),

            \Filament\Schemas\Components\Section::make('Detail Konsinyasi')
                ->schema([
                    \Filament\Schemas\Components\Grid::make(2)
                        ->schema([
                            \Filament\Forms\Components\Placeholder::make('detail_mitra')
                                ->label('Mitra')
                                ->content(function (Get $get) {
                                    $penjualan = PenjualanKonsinyasi::with('mitra')->find($get('penjualan_konsinyasi_id'));
                                    return $penjualan?->mitra?->namaMitra ?? '-';
                                }),
                            \Filament\Forms\Components\Placeholder::make('tanggal_kirim')
                                ->label('Tanggal Kirim')
                                ->content(function (Get $get) {
                                    $penjualan = PenjualanKonsinyasi::find($get('penjualan_konsinyasi_id'));
                                    return $penjualan?->tanggal_kirim ? \Carbon\Carbon::parse($penjualan->tanggal_kirim)->translatedFormat('d F Y') : '-';
                                }),
                            \Filament\Forms\Components\Placeholder::make('total_barang')
                                ->label('Total Titip Barang')
                                ->content(function (Get $get) {
                                    $penjualan = PenjualanKonsinyasi::find($get('penjualan_konsinyasi_id'));
                                    return $penjualan ? 'Rp ' . number_format($penjualan->total_barang, 0, ',', '.') : '-';
                                }),
                            \Filament\Forms\Components\Placeholder::make('status_konsinyasi')
                                ->label('Status Konsinyasi')
                                ->content(function (Get $get) {
                                    $penjualan = PenjualanKonsinyasi::find($get('penjualan_konsinyasi_id'));
                                    return $penjualan?->status ?? '-';
                                }),
                        ])
                ])
                ->visible(fn (Get $get) => filled($get('penjualan_konsinyasi_id')))
                ->columnSpanFull(),


            TextInput::make('no_po_mitra')
                ->label('No PO Mitra'),

            // 1. Tanggal laporan: min=tanggal_kirim (fallback tanggal), max=min(jatuh_tempo, now)
            DatePicker::make('tanggal_laporan')
                ->label('Tanggal Laporan')
                ->default(now())
                ->required()
                ->live()
                ->minDate(function (Get $get) {
                    $penjualan = PenjualanKonsinyasi::find($get('penjualan_konsinyasi_id'));
                    if (! $penjualan) return null;
                    return $penjualan->tanggal_kirim ?? $penjualan->tanggal;
                })
                ->maxDate(function (Get $get) {
                    $penjualan = PenjualanKonsinyasi::find($get('penjualan_konsinyasi_id'));
                    $jatuhTempo = $penjualan?->jatuh_tempo;
                    if ($jatuhTempo) {
                        // Ambil yang lebih awal: jatuh_tempo atau hari ini
                        $jt = \Carbon\Carbon::parse($jatuhTempo);
                        return $jt->isPast() ? $jt->toDateString() : now()->toDateString();
                    }
                    return now()->toDateString();
                })
                ->helperText(function (Get $get) {
                    $penjualan = PenjualanKonsinyasi::find($get('penjualan_konsinyasi_id'));
                    if (! $penjualan) return null;
                    $min = $penjualan->tanggal_kirim ?? $penjualan->tanggal;
                    $max = $penjualan->jatuh_tempo;
                    if ($min && $max) {
                        return 'Rentang: ' . \Carbon\Carbon::parse($min)->translatedFormat('d M Y')
                            . ' s/d ' . \Carbon\Carbon::parse($max)->translatedFormat('d M Y');
                    }
                    return null;
                }),

            // 2. Periode awal: min=tanggal_kirim (fallback tanggal), max=tanggal_laporan
            DatePicker::make('periode_awal')
                ->label('Periode Awal')
                ->required()
                ->live()
                ->minDate(function (Get $get) {
                    $penjualan = PenjualanKonsinyasi::find($get('penjualan_konsinyasi_id'));
                    if (! $penjualan) return null;
                    return $penjualan->tanggal_kirim ?? $penjualan->tanggal;
                })
                ->maxDate(fn (Get $get) => $get('tanggal_laporan') ?? now()->toDateString())
                ->beforeOrEqual(fn (Get $get) => $get('tanggal_laporan') ?? now()->toDateString())
                ->validationMessages([
                    'before_or_equal' => 'Periode awal harus sebelum atau sama dengan tanggal laporan.',
                    'max_date'        => 'Periode awal harus sebelum atau sama dengan tanggal laporan.',
                ])
                ->helperText(function (Get $get) {
                    $penjualan = PenjualanKonsinyasi::find($get('penjualan_konsinyasi_id'));
                    if (! $penjualan) return null;
                    $min = $penjualan->tanggal_kirim ?? $penjualan->tanggal;
                    $max = $get('tanggal_laporan');
                    if (! $min) return null;
                    $minStr = \Carbon\Carbon::parse($min)->translatedFormat('d M Y');
                    $maxStr = $max ? \Carbon\Carbon::parse($max)->translatedFormat('d M Y') : null;
                    return $maxStr ? "Rentang: {$minStr} s/d {$maxStr}" : "Minimal: {$minStr}";
                }),

            // 3. Periode akhir: min=periode_awal, max=min(jatuh_tempo, tanggal_laporan)
            DatePicker::make('periode_akhir')
                ->label('Periode Akhir')
                ->required()
                ->minDate(fn (Get $get) => $get('periode_awal'))
                ->maxDate(function (Get $get) {
                    $tanggalLaporan = $get('tanggal_laporan');
                    $penjualan      = PenjualanKonsinyasi::find($get('penjualan_konsinyasi_id'));
                    $jatuhTempo     = $penjualan?->jatuh_tempo;

                    if ($tanggalLaporan && $jatuhTempo) {
                        $tl = \Carbon\Carbon::parse($tanggalLaporan);
                        $jt = \Carbon\Carbon::parse($jatuhTempo);
                        return $tl->lt($jt) ? $tl->toDateString() : $jt->toDateString();
                    }
                    return $tanggalLaporan ?? ($jatuhTempo ? \Carbon\Carbon::parse($jatuhTempo)->toDateString() : now()->toDateString());
                })
                ->afterOrEqual(fn (Get $get) => $get('periode_awal') ?? now()->toDateString())
                ->beforeOrEqual(fn (Get $get) => $get('tanggal_laporan') ?? now()->toDateString())
                ->validationMessages([
                    'after_or_equal'  => 'Periode akhir tidak boleh lebih awal dari periode awal.',
                    'before_or_equal' => 'Periode akhir harus sebelum atau sama dengan tanggal laporan.',
                    'min_date'        => 'Periode akhir tidak boleh lebih awal dari periode awal.',
                    'max_date'        => 'Periode akhir harus sebelum atau sama dengan tanggal laporan.',
                ])
                ->helperText(function (Get $get) {
                    $periodeAwal    = $get('periode_awal');
                    $tanggalLaporan = $get('tanggal_laporan');
                    if (! $periodeAwal && ! $tanggalLaporan) return null;
                    $min = $periodeAwal    ? \Carbon\Carbon::parse($periodeAwal)->translatedFormat('d M Y')    : null;
                    $max = $tanggalLaporan ? \Carbon\Carbon::parse($tanggalLaporan)->translatedFormat('d M Y') : null;
                    if ($min && $max) return "Rentang: {$min} s/d {$max}";
                    if ($max) return "Maksimal: {$max}";
                    return $min ? "Minimal: {$min}" : null;
                }),

            TextInput::make('total_laporan')
                ->label('Total Laporan')
                ->numeric()
                ->disabled()
                ->dehydrated()
                ->helperText('Dihitung otomatis dari detail barang terjual'),
        ]);
    }
}
