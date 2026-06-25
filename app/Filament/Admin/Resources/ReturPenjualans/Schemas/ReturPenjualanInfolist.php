<?php

namespace App\Filament\Admin\Resources\ReturPenjualans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReturPenjualanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Retur')
                ->columns(2)
                ->schema([
                    TextEntry::make('no_retur')
                        ->label('No Retur'),

                    TextEntry::make('tanggal')
                        ->label('Tanggal')
                        ->date('d M Y'),

                    TextEntry::make('sumber_penjualan')
                        ->label('Sumber Penjualan')
                        ->state(function ($record): string {
                            if ($record->penjualan_non_konsinyasi_id) {
                                $inv = $record->penjualanNonKonsinyasi?->no_invoice ?? '-';
                                $pelanggan = $record->penjualanNonKonsinyasi?->pelanggan?->namaPelanggan ?? '';
                                return "Non Konsinyasi: {$inv}" . ($pelanggan ? " ({$pelanggan})" : '');
                            }
                            if ($record->penjualan_konsinyasi_id) {
                                $no = $record->penjualanKonsinyasi?->no_konsinyasi ?? '-';
                                $mitra = $record->penjualanKonsinyasi?->mitra?->namaMitra ?? '';
                                return "Konsinyasi: {$no}" . ($mitra ? " ({$mitra})" : '');
                            }
                            return '-';
                        }),

                    TextEntry::make('alasan')
                        ->label('Alasan Retur')
                        ->columnSpanFull(),
                ]),

            Section::make('Detail Barang Diretur')
                ->columnSpanFull()
                ->schema([
                    RepeatableEntry::make('detailRetur')
                        ->label('')
                        ->schema([
                            TextEntry::make('barang.nama_lengkap')
                                ->label('Nama Barang'),

                            TextEntry::make('qty')
                                ->label('Qty'),

                            TextEntry::make('kondisi')
                                ->label('Kondisi')
                                ->default('-'),
                        ])
                        ->columns(3),
                ]),
        ]);
    }
}
