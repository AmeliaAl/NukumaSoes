<?php

namespace App\Filament\Admin\Resources\PenjualanNonKonsinyasis\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class PenjualanNonKonsinyasisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_invoice')
                    ->label('Invoice')
                    ->searchable(),

                    TextColumn::make('no_pesanan')
                    ->label('No Pesanan')
                    ->formatStateUsing(fn ($state) => $state ?: '-')
                    ->toggleable(),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date(),

                TextColumn::make('pelanggan.namaPelanggan')
                    ->label('Pelanggan'),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR', locale: 'id_ID'),

                TextColumn::make('total_terbayar')
                    ->label('Terbayar')
                    ->money('IDR', locale: 'id_ID'),

                TextColumn::make('sisa_piutang')
                    ->label('Sisa Piutang')
                    ->money('IDR', locale: 'id_ID')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),

                BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'BELUM LUNAS',
                        'success' => 'LUNAS',
                    ]),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn ($record) => $record->status !== 'LUNAS'),

                DeleteAction::make()
                    ->visible(fn ($record) => $record->status !== 'LUNAS'),
            ])
            ->defaultSort('id', 'desc');
    }
}
