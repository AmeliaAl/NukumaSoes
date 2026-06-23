<?php

namespace App\Filament\Admin\Resources\Penyusutans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Aset;

class PenyusutansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('periode', 'asc')
            ->columns([
                TextColumn::make('periode')
                    ->label('Periode')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('beban_penyusutan')
                    ->label('Beban Penyusutan')
                    ->alignEnd()
                    ->money('IDR')
                    ->color('warning'),

                TextColumn::make('akumulasi_penyusutan')
                    ->label('Akumulasi Penyusutan')
                    ->alignEnd()
                    ->money('IDR')
                    ->color('danger'),

                TextColumn::make('nilai_buku')
                    ->label('Nilai Buku')
                    ->alignEnd()
                    ->money('IDR')
                    ->weight('bold')
                    ->color('success'),
            ])
            ->filters([
                SelectFilter::make('aset_id')
                    ->label('Nama Aset')
                    ->options(Aset::pluck('nama_aset', 'id'))
                    ->searchable(),
            ])
            ->recordActions([])
            ->modifyQueryUsing(function ($query, \Filament\Tables\Contracts\HasTable $livewire) {
                $asetId = $livewire->getTableFilterState('aset_id')['value'] ?? null;
                if ($asetId) {
                    $query->where('aset_id', $asetId);
                }
            })
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}