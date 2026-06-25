<?php

namespace App\Filament\Admin\Resources\Pembayarans\Tables;

use App\Models\PenjualanNonKonsinyasi;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;

class PembayaransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_pembayaran')
                    ->label('Kode Pembayaran')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tanggal_bayar')
                    ->label('Tanggal Bayar')
                    ->date('d-m-Y')
                    ->sortable(),

                TextColumn::make('penjualan.no_invoice')
                    ->label('No Invoice')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('penjualan.pelanggan.namaPelanggan')
                    ->label('Pelanggan')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('jumlah_bayar')
                    ->label('Jumlah Bayar')
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),

                TextColumn::make('metode_pembayaran')
                    ->label('Metode Pembayaran')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $map = [
                            'tunai' => 'Tunai',
                            'transfer' => 'Transfer',
                            'qris' => 'QRIS',
                        ];
                        return $map[$state] ?? $state;
                    }),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30),
            ])
            ->filters([
    Filter::make('tanggal_bayar')
        ->form([
            DatePicker::make('tanggal_awal')->label('Tanggal Awal'),
            DatePicker::make('tanggal_akhir')->label('Tanggal Akhir'),
        ])
        ->query(function ($query, array $data) {
            if (!empty($data['tanggal_awal'])) {
                $query->where('tanggal_bayar', '>=', $data['tanggal_awal']);
            }
            if (!empty($data['tanggal_akhir'])) {
                $query->where('tanggal_bayar', '<=', $data['tanggal_akhir']);
            }
            return $query;
        }),
])
            ->recordActions([
                EditAction::make(),
            ])
            ->defaultSort('id', 'desc')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
