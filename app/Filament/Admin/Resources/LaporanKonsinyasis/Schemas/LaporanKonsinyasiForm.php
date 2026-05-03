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
                    fn ($query) => $query->whereIn('status', ['BELUM TERJUAL', 'SEBAGIAN TERJUAL'])
                )
                ->searchable()
                ->required()
                ->live(),

            TextInput::make('no_po_mitra')
                ->label('No PO Mitra'),

            // 1. Tanggal laporan diisi dulu: antara tanggal_kirim dan jatuh_tempo
            DatePicker::make('tanggal_laporan')
                ->label('Tanggal Laporan')
                ->default(now())
                ->required()
                ->live()
                ->minDate(function (Get $get) {
                    $penjualan = PenjualanKonsinyasi::find($get('penjualan_konsinyasi_id'));
                    return $penjualan?->tanggal_kirim;
                })
                ->maxDate(function (Get $get) {
                    $penjualan = PenjualanKonsinyasi::find($get('penjualan_konsinyasi_id'));
                    return $penjualan?->jatuh_tempo;
                })
                ->helperText(function (Get $get) {
                    $penjualan = PenjualanKonsinyasi::find($get('penjualan_konsinyasi_id'));
                    if (! $penjualan?->tanggal_kirim || ! $penjualan?->jatuh_tempo) return null;

                    $awal  = \Carbon\Carbon::parse($penjualan->tanggal_kirim)->translatedFormat('d M Y');
                    $akhir = \Carbon\Carbon::parse($penjualan->jatuh_tempo)->translatedFormat('d M Y');

                    return "Rentang: {$awal} s/d {$akhir}";
                }),

            // 2. Periode awal: min tanggal_kirim, max tanggal_laporan
            DatePicker::make('periode_awal')
                ->label('Periode Awal')
                ->required()
                ->live()
                ->minDate(function (Get $get) {
                    $penjualan = PenjualanKonsinyasi::find($get('penjualan_konsinyasi_id'));
                    return $penjualan?->tanggal_kirim;
                })
                ->maxDate(fn (Get $get) => $get('tanggal_laporan'))
                ->beforeOrEqual(fn (Get $get) => $get('tanggal_laporan') ?? now()->toDateString())
                ->validationMessages([
                    'before_or_equal' => 'Periode awal harus sebelum atau sama dengan tanggal laporan.',
                    'max_date'        => 'Periode awal harus sebelum atau sama dengan tanggal laporan.',
                ])
                ->helperText(function (Get $get) {
                    $penjualan = PenjualanKonsinyasi::find($get('penjualan_konsinyasi_id'));
                    if (! $penjualan?->tanggal_kirim) return null;

                    $min = \Carbon\Carbon::parse($penjualan->tanggal_kirim)->translatedFormat('d M Y');
                    $max = $get('tanggal_laporan')
                        ? \Carbon\Carbon::parse($get('tanggal_laporan'))->translatedFormat('d M Y')
                        : null;

                    return $max
                        ? "Rentang: {$min} s/d {$max}"
                        : "Minimal: {$min}";
                }),

            // 3. Periode akhir: min periode_awal, max tanggal_laporan
            DatePicker::make('periode_akhir')
                ->label('Periode Akhir')
                ->required()
                ->minDate(fn (Get $get) => $get('periode_awal'))
                ->maxDate(fn (Get $get) => $get('tanggal_laporan'))
                ->afterOrEqual(fn (Get $get) => $get('periode_awal') ?? now()->toDateString())
                ->beforeOrEqual(fn (Get $get) => $get('tanggal_laporan') ?? now()->toDateString())
                ->validationMessages([
                    'after_or_equal'  => 'Periode akhir tidak boleh lebih awal dari periode awal.',
                    'before_or_equal' => 'Periode akhir harus sebelum atau sama dengan tanggal laporan.',
                    'min_date'        => 'Periode akhir tidak boleh lebih awal dari periode awal.',
                    'max_date'        => 'Periode akhir harus sebelum atau sama dengan tanggal laporan.',
                ])
                ->helperText(function (Get $get) {
                    $periodeAwal     = $get('periode_awal');
                    $tanggalLaporan  = $get('tanggal_laporan');

                    if (! $periodeAwal && ! $tanggalLaporan) return null;

                    $min = $periodeAwal
                        ? \Carbon\Carbon::parse($periodeAwal)->translatedFormat('d M Y')
                        : null;
                    $max = $tanggalLaporan
                        ? \Carbon\Carbon::parse($tanggalLaporan)->translatedFormat('d M Y')
                        : null;

                    if ($min && $max) return "Rentang: {$min} s/d {$max}";
                    if ($max) return "Maksimal: {$max}";
                    return "Minimal: {$min}";
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
