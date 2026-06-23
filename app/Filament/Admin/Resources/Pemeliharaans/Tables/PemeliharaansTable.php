<?php

namespace App\Filament\Admin\Resources\Pemeliharaans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;


class PemeliharaansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')                 
                ->date('d M Y')
                ->sortable(),
                TextColumn::make('aset.nama_aset')->label('Aset')->searchable(),
                TextColumn::make('jenis_perbaikan')
                ->badge()
                ->colors([
                    'warning' => 'maintenance',
                    'success' => 'peningkatan',
                ]),
                TextColumn::make('biaya')->money('IDR', true)->alignRight(),
                TextColumn::make('keterangan')->limit(30),
                TextColumn::make('tambah_umur')->label('Tambah Umur'),
            ])
            ->filters([
                SelectFilter::make('jenis_perbaikan')
                    ->options([
                        'maintenance' => 'Maintenance',
                        'peningkatan' => 'Peningkatan',
                    ]),
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
