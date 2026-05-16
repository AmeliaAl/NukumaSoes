<?php

namespace App\Filament\Admin\Resources\BukuBesars\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Actions;
use Filament\Tables\Columns;
use Filament\Tables\Filters;
use Filament\Tables\Columns\TextColumn;

class BukuBesarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')->date(),
                TextColumn::make('no_referensi')->label('Ref'),
                TextColumn::make('deskripsi')->limit(30),
                TextColumn::make('jurnaldetail.debit')
                    ->label('Total Debit')
                    ->formatStateUsing(function ($state, $record) {
                        // Menghitung jumlah debit dari relasi jurnaldetail
                        // dd(var_dump($record));  // Debugging untuk melihat data relasi
                        $debit = $record->jurnaldetail()->sum('debit'); 
                        return 'Rp ' . number_format($debit, 0, ',', '.');
                    })
                    ->alignment('end') // Rata kanan
                , 
                TextColumn::make('jurnaldetail.credit')
                    ->label('Total Kredit')
                    ->formatStateUsing(function ($state, $record) {
                        $credit = $record->jurnaldetail()->sum('credit'); 
                        return 'Rp ' . number_format($credit, 0, ',', '.');

                    })
                    ->alignment('end') // Rata kanan
                , 
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
