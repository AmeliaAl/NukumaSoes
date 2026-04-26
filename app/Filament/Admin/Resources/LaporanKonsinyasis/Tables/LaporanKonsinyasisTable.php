<?php

namespace App\Filament\Admin\Resources\LaporanKonsinyasis\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;

class LaporanKonsinyasisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_laporan')->label('No Laporan'),
                TextColumn::make('penjualanKonsinyasi.no_konsinyasi')
                    ->label('No Konsinyasi'),

                TextColumn::make('periode_awal')->date(),
                TextColumn::make('periode_akhir')->date(),

                TextColumn::make('total_laporan')
                    ->money('IDR', locale: 'id_ID'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
