<?php

namespace App\Filament\Admin\Resources\AsetLancars\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class AsetLancarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
           ->columns([
            TextColumn::make('kode_barang')
                ->label('Kode')
                ->searchable()
                ->sortable()
                ->copyable(),

            TextColumn::make('nama_barang')
                ->label('Nama Barang')
                ->searchable()
                ->sortable()
                ->wrap(),

            TextColumn::make('kategoriAset.nama_kategori')
                ->label('Kategori')
                ->sortable(),

             TextColumn::make('satuan')
                ->label('Satuan')
                ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                //EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                   
                ]),
            ]);
    }
}
