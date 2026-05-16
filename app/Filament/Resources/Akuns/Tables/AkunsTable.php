<?php

namespace App\Filament\Resources\Akuns\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class AkunsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('header_akun'),
                TextColumn::make('no_akun')
                ->sortable(),
                TextColumn::make('nama_akun')
                ->searchable(),
            ])
            ->filters([
                SelectFilter::make('header_akun')
                    ->options([
                        1 => 'Aset/Aktiva',
                        2 => 'Utang',
                        3 => 'Modal',
                        4 => 'Pendapatan',
                        5 => 'Beban',
                    ]),
            ]);  
    }
}
