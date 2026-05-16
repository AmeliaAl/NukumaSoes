<?php

namespace App\Filament\Admin\Resources\Modals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

class ModalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('akun'))
            ->columns([
            TextColumn::make('tanggal')->date(),
            TextColumn::make('jenis')
                ->badge()
                ->color(fn (string $state) => match ($state) {
                    'setoran' => 'success', 
                    'prive'   => 'danger',  
                    default   => 'gray',
                }),
            TextColumn::make('akun.nama_akun')
                ->label('Kas / Bank')
                ->searchable()
                ->sortable(),
            TextColumn::make('jumlah')
                ->money('IDR', locale: 'id_ID')
                ->alignEnd()
                ->weight('bold')
                ->color('success'),
            
            ])
            ->defaultSort('tanggal', 'desc')

            ->filters([
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('from')->label('Dari'),
                        DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('tanggal', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('tanggal', '<=', $data['until']));
                    }),
            ])

            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
