<?php

namespace App\Filament\Admin\Resources\JurnalUmums\Tables;

use Filament\Tables\Table;

class JurnalUmumsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('no_jurnal')
                    ->label('Nomor')
                    ->searchable()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('keterangan')
                    ->label('Referensi')
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('total')
                    ->label('Jumlah')
                    ->money('IDR', locale: 'id_ID'),
            ])
            ->recordAction('view')
            ->defaultSort('tanggal', 'desc');
    }
}
