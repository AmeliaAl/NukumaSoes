<?php

namespace App\Filament\Admin\Resources\HargaBarangs\Tables;

use App\Models\Kategori;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class HargaBarangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('barang.nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('barang.kategori.nama_kategori')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),

                BadgeColumn::make('jenis_mitra')
                    ->label('Jenis Mitra')
                    ->colors([
                        'primary' => 'agen',
                        'success' => 'reseller',
                        'warning' => 'konsinyasi',
                        'gray'    => 'umum',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('jenis_mitra')
                    ->label('Jenis Mitra')
                    ->options([
                        'agen'       => 'Agen',
                        'reseller'   => 'Reseller',
                        'konsinyasi' => 'Konsinyasi',
                        'umum'       => 'Umum',
                    ]),

                SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->relationship('barang.kategori', 'nama_kategori'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('barang_id');
    }
}
