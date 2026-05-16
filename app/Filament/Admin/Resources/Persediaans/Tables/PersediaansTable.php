<?php

namespace App\Filament\Admin\Resources\Persediaans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;


class PersediaansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('faktur.no_faktur')
                    ->label('No. Faktur')
                    ->searchable(),

                TextColumn::make('nama_barang')
                    ->label('Nama Barang')
                    ->searchable(),

                TextColumn::make('kategoriAset.nama_kategori')
                    ->label('Kategori'),

                TextColumn::make('qty')
                    ->label('Qty')
                    ->suffix(' unit'),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR'),

               TextColumn::make('tanggal_masuk')
                    ->label('Tanggal Masuk')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_masuk', 'desc');
            
    }
}
