<?php

namespace App\Filament\Admin\Resources\TagihanKonsinyasis\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\LaporanKonsinyasi;

class TagihanKonsinyasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            TextInput::make('no_tagihan')
                ->default(fn () => \App\Models\TagihanKonsinyasi::generateNo())
                ->disabled()
                ->dehydrated()
                ->required(),

            Select::make('laporan_konsinyasi_id')
                ->label('Laporan Konsinyasi')
                ->relationship('laporanKonsinyasi', 'no_laporan')
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, Set $set) {
                    $laporan = LaporanKonsinyasi::find($state);

                    $set('total_tagihan', $laporan?->total_laporan ?? 0);
                    $set('tanggal_tagihan', $laporan?->tanggal_laporan ?? now()->toDateString());
                }),

            Section::make('Detail Laporan Konsinyasi')
                ->schema([
                    Grid::make(2)->schema([
                        Placeholder::make('no_konsinyasi_preview')
                            ->label('No Konsinyasi')
                            ->content(function (Get $get) {
                                $laporan = LaporanKonsinyasi::with('penjualanKonsinyasi')->find($get('laporan_konsinyasi_id'));

                                return $laporan?->penjualanKonsinyasi?->no_konsinyasi ?? '-';
                            }),

                        Placeholder::make('mitra_preview')
                            ->label('Mitra / Toko')
                            ->content(function (Get $get) {
                                $laporan = LaporanKonsinyasi::with('penjualanKonsinyasi.mitra')->find($get('laporan_konsinyasi_id'));

                                return $laporan?->penjualanKonsinyasi?->mitra?->namaMitra ?? '-';
                            }),

                        Placeholder::make('periode_preview')
                            ->label('Periode')
                            ->content(function (Get $get) {
                                $laporan = LaporanKonsinyasi::find($get('laporan_konsinyasi_id'));

                                if (! $laporan) {
                                    return '-';
                                }

                                return $laporan->periode_awal . ' s/d ' . $laporan->periode_akhir;
                            }),

                        Placeholder::make('total_laporan_preview')
                            ->label('Total Laporan')
                            ->content(function (Get $get) {
                                $laporan = LaporanKonsinyasi::find($get('laporan_konsinyasi_id'));

                                return $laporan
                                    ? 'Rp ' . number_format((int) $laporan->total_laporan, 0, ',', '.')
                                    : '-';
                            }),
                    ]),
                ])
                ->columnSpanFull(),

            DatePicker::make('tanggal_tagihan')
                ->label('Tanggal Tagihan')
                ->default(now())
                ->disabled()
                ->dehydrated()
                ->helperText('Otomatis mengikuti tanggal laporan'),

            TextInput::make('total_tagihan')
                ->label('Total Tagihan')
                ->numeric()
                ->required()
                ->disabled()
                ->dehydrated()
                ->helperText('Otomatis dari total laporan'),

            Textarea::make('keterangan'),
        ]);
    }
}