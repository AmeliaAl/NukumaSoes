<?php

namespace App\Filament\Admin\Resources\Jurnals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Actions;
use Filament\Tables\Columns;
use Filament\Tables\Filters;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\DatePickerFilter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class JurnalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
             ->columns([
            TextColumn::make('tanggal')->date(),
            TextColumn::make('no_referensi')
            ->label('Ref'),
            TextColumn::make('deskripsi')->limit(30),
            TextColumn::make('total_debit')
                ->label('Total Debit')
                ->alignEnd()
                ->getStateUsing(function ($record) {
                    return $record->jurnaldetail()->sum('debit');
                })
                ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                ->money('IDR', true) // opsional, biar format Rp
    ,
            TextColumn::make('total_kredit')
                ->label('Total Kredit')
                ->alignEnd()
                ->getStateUsing(function ($record) {
                    return $record->jurnaldetail()->sum('credit');
                })
                ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                ->money('IDR', true), // opsional, biar format Rp
            ])
            ->filters([
                Filter::make('tanggal')
                ->form([
                    DatePicker::make('dari')
                        ->label('Dari Tanggal'),
                    DatePicker::make('sampai')
                        ->label('Sampai Tanggal'),
                ])
                ->query(function (Builder $query, array $data) {
                    return $query
                        ->when(
                            $data['dari'],
                            fn ($q) => $q->whereDate('tanggal', '>=', $data['dari'])
                        )
                        ->when(
                            $data['sampai'],
                            fn ($q) => $q->whereDate('tanggal', '<=', $data['sampai'])
                        );
                }),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
