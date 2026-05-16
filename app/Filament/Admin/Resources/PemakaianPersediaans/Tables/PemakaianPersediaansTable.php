<?php

namespace App\Filament\Admin\Resources\PemakaianPersediaans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PemakaianPersediaansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('asetLancar.nama_barang')
                ->label('Nama Barang')
                ->searchable(),

            TextColumn::make('tanggal')
                ->label('Tanggal')
                ->date('d M Y'),

            TextColumn::make('jumlah')
                ->label('Jumlah')
                ->numeric(),

            TextColumn::make('keterangan')
                ->label('Keterangan')
                ->limit(30),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }
}
