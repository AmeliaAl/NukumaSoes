<?php

namespace App\Filament\Admin\Resources\PembayaranAsets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

class PembayaranAsetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('faktur.no_faktur')
                    ->label('No. Faktur')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('faktur.vendor.nama_vendor')
                    ->label('Vendor')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(),
                
                TextColumn::make('tanggal_bayar')
                    ->label('Tanggal Bayar')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('tgl_terima_brg')
                    ->label('Tgl Terima Barang')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('-'),
                
                TextColumn::make('jumlah_bayar')
                    ->label('Jumlah Bayar')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd()
                    ->weight('bold')
                    ->color('success'),
                
                TextColumn::make('total_tagihan')
                    ->label('Total Tagihan')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd()
                    ->toggleable(),
                
                TextColumn::make('sisa_tagihan')
                    ->label('Sisa Tagihan')
                    ->alignEnd()
                    ->money('IDR')
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                
                BadgeColumn::make('metode_pembayaran')
                    ->label('Metode')
                    ->colors([
                        'success' => 'cash',
                        'info' => 'transfer',
                        'warning' => 'giro',
                        'gray' => 'lainnya',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                
                BadgeColumn::make('faktur.status')
                    ->label('Status Faktur')
                    ->colors([
                        'danger' => 'belum_dibayar',
                        'warning' => 'belum_lunas',
                        'success' => 'lunas',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'belum_dibayar' => 'Belum Dibayar',
                        'belum_lunas' => 'Belum Lunas',
                        'lunas' => 'Lunas',
                        default => $state
                    }),
            ])
            ->defaultSort('tanggal_bayar', 'desc')
            ->filters([
                SelectFilter::make('metode_pembayaran')
                    ->label('Metode Pembayaran')
                    ->options([
                        'cash' => 'Cash',
                        'transfer' => 'Transfer Bank',
                        'giro' => 'Giro',
                        'lainnya' => 'Lainnya',
                    ]),

                SelectFilter::make('faktur.status')
                    ->label('Status Faktur')
                    ->options([
                        'belum_dibayar' => 'Belum Dibayar',
                        'belum_lunas' => 'Belum Lunas',
                        'lunas' => 'Lunas',
                    ]),
                
                Filter::make('tanggal_bayar')
                    ->form([
                        DatePicker::make('dari')->label('Dari Tanggal'),
                        DatePicker::make('sampai')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari'], fn ($q, $date) => $q->whereDate('tanggal_bayar', '>=', $date))
                            ->when($data['sampai'], fn ($q, $date) => $q->whereDate('tanggal_bayar', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari'] ?? null) {
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['dari'])->format('d M Y');
                        }
                        if ($data['sampai'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['sampai'])->format('d M Y');
                        }
                        return $indicators;
                    }),

                Filter::make('tgl_terima_brg')
                    ->form([
                        DatePicker::make('dari')->label('Dari Tanggal'),
                        DatePicker::make('sampai')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari'], fn ($q, $date) => $q->whereDate('tgl_terima_brg', '>=', $date))
                            ->when($data['sampai'], fn ($q, $date) => $q->whereDate('tgl_terima_brg', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari'] ?? null) {
                            $indicators[] = 'Terima Dari: ' . \Carbon\Carbon::parse($data['dari'])->format('d M Y');
                        }
                        if ($data['sampai'] ?? null) {
                            $indicators[] = 'Terima Sampai: ' . \Carbon\Carbon::parse($data['sampai'])->format('d M Y');
                        }
                        return $indicators;
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ])
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}