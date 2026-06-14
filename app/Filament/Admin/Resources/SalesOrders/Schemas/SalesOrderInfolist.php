<?php

namespace App\Filament\Admin\Resources\SalesOrders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SalesOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // Kolom kiri: info pemilik (pelanggan / mitra)
                Section::make('Kepada')
                    ->schema([
                        TextEntry::make('pemilik_nama')
                            ->label(false)
                            ->state(function ($record): string {
                                if ($record->jenis === 'Non Konsinyasi') {
                                    return $record->penjualanNonKonsinyasi?->pelanggan?->namaPelanggan ?? '-';
                                }
                                return $record->penjualanKonsinyasi?->mitra?->namaMitra ?? '-';
                            }),

                        TextEntry::make('pemilik_alamat')
                            ->label('Alamat')
                            ->state(function ($record): string {
                                if ($record->jenis === 'Non Konsinyasi') {
                                    return $record->penjualanNonKonsinyasi?->pelanggan?->alamat ?? '-';
                                }
                                return $record->penjualanKonsinyasi?->mitra?->alamat ?? '-';
                            }),

                        TextEntry::make('pemilik_telepon')
                            ->label('Telepon')
                            ->state(function ($record): string {
                                if ($record->jenis === 'Non Konsinyasi') {
                                    return $record->penjualanNonKonsinyasi?->pelanggan?->no_telepon ?? '-';
                                }
                                return $record->penjualanKonsinyasi?->mitra?->no_telepon ?? '-';
                            }),
                    ])
                    ->columnSpan(1)
                    ->extraAttributes(['style' => 'height: 100%']),

                // Kolom kanan: info SO
                Section::make('Detail SO')
                    ->schema([
                        TextEntry::make('no_so')
                            ->label('No SO'),

                        TextEntry::make('tanggal')
                            ->label('Tanggal')
                            ->date('d/m/Y'),

                        TextEntry::make('referensi')
                            ->label('Referensi'),

                        TextEntry::make('jenis')
                            ->label('Jenis')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Non Konsinyasi' => 'success',
                                'Konsinyasi'     => 'info',
                                default          => 'gray',
                            }),
                    ])
                    ->columnSpan(1)
                    ->extraAttributes(['style' => 'height: 100%']),
            ]);
    }
}
