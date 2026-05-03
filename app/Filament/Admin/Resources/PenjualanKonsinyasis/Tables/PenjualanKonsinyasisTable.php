<?php

namespace App\Filament\Admin\Resources\PenjualanKonsinyasis\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PenjualanKonsinyasisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_konsinyasi')
                    ->label('No Konsinyasi')
                    ->searchable(),

                TextColumn::make('mitra.namaMitra')
                    ->label('Mitra')
                    ->searchable(),

                TextColumn::make('jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->date(),

                TextColumn::make('total_konsinyasi')
                    ->label('Total Titipan')
                    ->money('IDR', locale: 'id_ID'),

                TextColumn::make('total_laporan')
                    ->label('Total Terjual')
                    ->money('IDR', locale: 'id_ID')
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'BELUM TERJUAL' => 'gray',
                        'SEBAGIAN TERJUAL' => 'warning',
                        'SELESAI' => 'success',
                        default => 'gray',
                    }),
            ])
            ->actions([
            ViewAction::make(),
            EditAction::make(),
            DeleteAction::make()
                ->visible(fn ($record) => $record->status !== 'SELESAI'),
        ])
        ->defaultSort('id', 'desc');
    }
}