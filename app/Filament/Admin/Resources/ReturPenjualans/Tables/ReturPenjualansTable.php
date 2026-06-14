<?php

namespace App\Filament\Admin\Resources\ReturPenjualans\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReturPenjualansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_retur')
                    ->label('No Retur')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('sumber')
                    ->label('Sumber Penjualan')
                    ->getStateUsing(function ($record) {
                        if ($record->penjualan_non_konsinyasi_id) {
                            $inv = $record->penjualanNonKonsinyasi?->no_invoice ?? '-';
                            $pelanggan = optional($record->penjualanNonKonsinyasi?->pelanggan)->namaPelanggan ?? '';
                            return "Non Konsinyasi: {$inv}" . ($pelanggan ? " ({$pelanggan})" : '');
                        }

                        if ($record->penjualan_konsinyasi_id) {
                            $no = $record->penjualanKonsinyasi?->no_konsinyasi ?? '-';
                            $mitra = optional($record->penjualanKonsinyasi?->mitra)->namaMitra ?? '';
                            return "Konsinyasi: {$no}" . ($mitra ? " ({$mitra})" : '');
                        }

                        return '-';
                    })
                    ->wrap(),

                TextColumn::make('detail_retur_count')
                    ->label('Jml Item')
                    ->counts('detailRetur')
                    ->alignCenter(),

                TextColumn::make('barang_diretur')
                    ->label('Barang Diretur')
                    ->getStateUsing(function ($record): string {
                        return $record->detailRetur
                            ->load('barang')
                            ->map(fn ($d) => $d->barang?->nama_lengkap ?? '-')
                            ->join(', ');
                    })
                    ->wrap(),

                TextColumn::make('alasan')
                    ->label('Alasan')
                    ->limit(40)
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }
}
