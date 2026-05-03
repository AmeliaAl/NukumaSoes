<?php

namespace App\Filament\Admin\Resources\LaporanKonsinyasis\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class LaporanKonsinyasisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_laporan')
                    ->label('No Laporan')
                    ->searchable(),

                TextColumn::make('penjualanKonsinyasi.no_konsinyasi')
                    ->label('No Konsinyasi')
                    ->searchable(),

                TextColumn::make('tanggal_laporan')
                    ->label('Tanggal Laporan')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->translatedFormat('d M Y') : '-'),

                TextColumn::make('periode_awal')
                    ->label('Periode Awal')
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->translatedFormat('d M Y') : '-'),

                TextColumn::make('periode_akhir')
                    ->label('Periode Akhir')
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->translatedFormat('d M Y') : '-'),

                TextColumn::make('total_laporan')
                    ->label('Total Laporan')
                    ->money('IDR', locale: 'id_ID'),
            ])
            ->filters([
                Filter::make('tanggal_laporan')
                    ->label('Filter Tanggal Laporan')
                    ->form([
                        DatePicker::make('dari')->label('Dari'),
                        DatePicker::make('sampai')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['dari'], fn ($q) => $q->whereDate('tanggal_laporan', '>=', $data['dari']))
                            ->when($data['sampai'], fn ($q) => $q->whereDate('tanggal_laporan', '<=', $data['sampai']));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }
}
