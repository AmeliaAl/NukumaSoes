<?php

namespace App\Filament\Admin\Resources\SaldoAwals\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SaldoAwalTable
{
    public static function configure(Table $table): Table
    {
        $bulanMap = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April',   5 => 'Mei',       6 => 'Juni',
            7 => 'Juli',    8 => 'Agustus',   9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $table
            ->columns([
                TextColumn::make('coa.kode_akun')
                    ->label('Kode Akun')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('coa.nama_akun')
                    ->label('Nama Akun')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('bulan')
                    ->label('Bulan')
                    ->formatStateUsing(fn ($state) => $bulanMap[$state] ?? $state)
                    ->sortable(),

                TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable(),

                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('tahun', 'desc')
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
