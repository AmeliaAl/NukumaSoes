<?php

namespace App\Filament\Admin\Resources\Barangs\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class BarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_barang')
                    ->label('Kode')
                    ->searchable(),

                TextColumn::make('nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rasa')
                    ->label('Rasa')
                    ->searchable(),

                TextColumn::make('kategori.nama_kategori')
                    ->label('Kategori')
                    ->searchable(),

                TextColumn::make('satuan')
                    ->label('Satuan'),

                TextColumn::make('harga')
                    ->label('Harga')
                    ->getStateUsing(function ($record) {
                    if ($record->hargaBarang->isEmpty()) {
                        return '-';
                    }

                    return $record->hargaBarang->map(function ($item) {
                        return ucfirst($item->jenis_mitra) . ': Rp ' . number_format($item->harga, 0, ',', '.');
                    })->implode("\n");
                })
                ->wrap(),
    
                TextColumn::make('stok_awal')
                    ->label('Stok Awal'),

                TextColumn::make('stok')
                    ->label('Stok Saat Ini')
                    ->badge()
                    ->color(fn ($state) => $state <= 0 ? 'danger' : 'success'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}