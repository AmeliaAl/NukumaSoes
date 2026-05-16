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

            TextColumn::make('stok_tersedia')
                ->label('Stok')
                ->numeric()
                ->suffix(' unit')
                ->sortable()
                ->color(fn ($record) => $record->isStokMinimum() ? 'danger' : 'success')
                ->weight(fn ($record) => $record->isStokMinimum() ? 'bold' : 'normal'),

            TextColumn::make('harga_satuan_rata')
                ->label('Harga Rata-rata')
                ->money('IDR')
                ->alignEnd()
                ->sortable()
                ->toggleable(),

            TextColumn::make('nilai_total')
                ->label('Nilai Total')
                ->money('IDR')
                ->sortable()
                ->alignEnd()
                ->weight('bold')
                ->color('success'),

            TextColumn::make('tanggal_update_terakhir')
                ->label('Update Terakhir')
                ->date('d M Y')
                ->sortable()
                ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                   
                ]),
            ]);
    }
}
