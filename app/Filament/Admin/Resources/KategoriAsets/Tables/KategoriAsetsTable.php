<?php

namespace App\Filament\Admin\Resources\KategoriAsets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Colors\Color;

class KategoriAsetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('kode_kategori', 'asc') 
            ->columns([
                TextColumn::make('kode_kategori')
                    ->label('Kode Kategori')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nama_kategori')
                    ->label('Nama Kategori Aset')
                    ->sortable()
                    ->searchable(),

               TextColumn::make('jenis_aset')
                    ->label('Jenis Aset')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'aset_tetap' => 'danger',   
                        'aset_lancar' => 'warning', 
                        default => 'gray',
                    })
                    ->icon(fn ($state) => match ($state) {
                        'aset_tetap' => 'heroicon-o-building-office',
                        'aset_lancar' => 'heroicon-o-banknotes',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'aset_tetap' => 'Aset Tetap',
                        'aset_lancar' => 'Aset Lancar',
                        default => $state,
                    }),

                TextColumn::make('masa_manfaat')
                    ->label('Masa Manfaat (Tahun)')
                    ->searchable()
                    // 2. Memformat Angka dengan koma/pemisah ribuan (jika angkanya besar)
                    // ->numeric() 
                    ->suffix(' Tahun'),

                TextColumn::make('interval_pemeliharaan')
                    ->label('Interval Pemeliharaan (Bulan)')
                    ->searchable()
                    ->suffix(' Bulan'),
            ])
            ->filters([
                // Jika ingin menambahkan filter berdasarkan metode penyusutan:
                // \Filament\Tables\Filters\SelectFilter::make('metode_penyusutan')
                //     ->options([
                //         'Garis Lurus' => 'Garis Lurus',
                //         'Saldo Menurun' => 'Saldo Menurun',
                //     ])
            ])
            ->actions([
                EditAction::make(),
                // 5. Menambahkan aksi lain jika diperlukan
                // \Filament\Tables\Actions\ViewAction::make(), 
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
