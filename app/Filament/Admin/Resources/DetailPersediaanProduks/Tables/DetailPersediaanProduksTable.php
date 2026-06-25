<?php

namespace App\Filament\Admin\Resources\DetailPersediaanProduks\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DetailPersediaanProduksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('barang.nama_barang')
                    ->label('Produk')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('barang.rasa')
                    ->label('Rasa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('barang.kategori.nama_kategori')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('batch')
                    ->label('Batch')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('stok_saat_ini')
                    ->label('Stok Saat Ini')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state <= 0 ? 'danger' : ($state <= 10 ? 'warning' : 'success')),

                TextColumn::make('harga_modal_per_pack')
                    ->label('Harga Modal (HPP)')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('tanggal_expired')
                    ->label('Tanggal Expired')
                    ->date('d/m/Y')
                    ->sortable()
                    ->badge()
                    ->color(function ($state) {
                        if (is_null($state)) {
                            return 'gray';
                        }
                        $expired = Carbon::parse($state);
                        if ($expired->isPast()) {
                            return 'danger';
                        }
                        if ($expired->diffInDays(now()) <= 30) {
                            return 'warning';
                        }
                        return 'success';
                    }),
            ])
            ->defaultSort('tanggal_expired', 'asc')
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
