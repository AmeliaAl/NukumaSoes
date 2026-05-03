<?php

namespace App\Filament\Admin\Resources\TagihanKonsinyasis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;


class TagihanKonsinyasisTable
{

    public static function configure(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('no_tagihan')
                ->label('No Tagihan')
                ->searchable(),

            TextColumn::make('laporanKonsinyasi.no_laporan')
                ->label('No Laporan'),

            TextColumn::make('tanggal_tagihan')
                ->label('Tanggal')
                ->date(),

            TextColumn::make('total_tagihan')
                ->label('Total')
                ->money('IDR', locale: 'id_ID'),

            TextColumn::make('total_terbayar')
                ->label('Terbayar')
                ->money('IDR', locale: 'id_ID')
                ->color(fn ($record) => $record->total_terbayar > 0 ? 'success' : 'gray'),

            TextColumn::make('sisa_tagihan')
                ->label('Sisa')
                ->money('IDR', locale: 'id_ID')
                ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),

            BadgeColumn::make('status')
                ->label('Status')
                ->colors([
                    'danger' => 'BELUM LUNAS',
                    'success' => 'LUNAS',
                ]),
        ])
        ->actions([
            ViewAction::make(),
            EditAction::make()
                ->visible(fn ($record) => $record->status !== 'LUNAS'),
        ])
        ->defaultSort('id', 'desc');
    }

}
