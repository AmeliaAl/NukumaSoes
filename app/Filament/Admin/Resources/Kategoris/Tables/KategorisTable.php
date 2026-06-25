<?php

namespace App\Filament\Admin\Resources\Kategoris\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KategorisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_kategori')
                    ->label('Nama Kategori')
                    ->searchable(),

                TextColumn::make('berat')
                    ->label('Berat')
                    ->formatStateUsing(function ($state) {
                        if (!$state) {
                            return '-';
                        }

                        if ($state >= 1000) {
                            return number_format($state / 1000, 1, ',', '.') . ' kg';
                        }

                        return number_format($state, 0, ',', '.') . ' gram';
                    }),
                    
                TextColumn::make('jenis_kemasan')
                    ->label('Jenis Kemasan'),

                TextColumn::make('masa_simpan')
                    ->label('Masa Simpan'),

                TextColumn::make('unit_waktu')
                    ->label('Unit Waktu'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}