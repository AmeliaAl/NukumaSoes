<?php

namespace App\Filament\Admin\Resources\FakturPembelians\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class FakturPembeliansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['vendor', 'items']))
            ->columns([
                TextColumn::make('no_faktur')
                    ->label('No. Faktur')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),
                
                TextColumn::make('vendor.nama_vendor')
                    ->label('Vendor')
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('tanggal_faktur')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->toggleable(),

                 TextColumn::make('items')
                    ->label('Nama Aset')
                    ->formatStateUsing(function ($record) {
                        if ($record->items->isEmpty()) {
                            return '-';
                        }
                        
                        // Group by nama_aset dan sum qty
                        $grouped = $record->items->groupBy('nama_aset')->map(function ($items, $namaAset) {
                            $totalQty = $items->sum('qty');
                            return $namaAset . ' (' . $totalQty . ')';
                        })->values();
                        
                        // Jika hanya 1 jenis item, tampilkan langsung
                        if ($grouped->count() === 1) {
                            return $grouped->first();
                        }
                        
                        // Jika lebih dari 1, tampilkan dengan bullet
                        return $grouped->take(3)->join("\n• ", '• ') . 
                               ($grouped->count() > 3 ? "\n• ..." : '');
                    })
                    ->wrap()
                    ->html()
                    ->limit(50),
        
                TextColumn::make('total_tagihan')
                    ->label('Total Tagihan')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd()
                    ->weight('bold')
                    ->color('success'),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger' => 'belum_dibayar',
                        'warning' => 'belum_lunas',
                        'success' => 'lunas',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'belum_dibayar' => 'Belum Dibayar',
                        'belum_lunas' => 'Belum Lunas',
                        'lunas' => 'Lunas',
                        default => $state,
                    })
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'belum_dibayar' => 'Belum Dibayar',
                        'belum_lunas' => 'Belum Lunas',
                        'lunas' => 'Lunas',
                    ])
                    ->multiple(),
                
                SelectFilter::make('id_vendor')
                    ->label('Vendor')
                    ->relationship('vendor', 'nama_vendor')
                    ->searchable()
                    ->preload(),
                ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('tanggal_faktur', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);

            
    }
}
