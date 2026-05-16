<?php

namespace App\Filament\Admin\Resources\PerolehanAsets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PerolehanAsetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('fakturPembelian.no_faktur')
                    ->label('No. Faktur')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('nama_aset')
                    ->label('Nama Aset')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight('semibold'),

                TextColumn::make('kategoriAset.nama_kategori')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('vendor.nama_vendor')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('tanggal_pakai')
                    ->label('Tgl. Mulai Pakai')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('qty')
                    ->label('Qty')
                    ->numeric()
                    ->alignCenter()
                    //->suffix(' unit')
                    ->toggleable(),

                TextColumn::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->alignEnd()
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('biaya_lain')
                    ->label('Biaya Lain')
                    ->money('IDR')
                    ->toggleable()
                    ->alignEnd()
                    ->default(0),

                TextColumn::make('total_perolehan')
                    ->label('Total Perolehan')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd()
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('masa_manfaat')
                    ->label('Masa Manfaat')
                    ->numeric()
                    ->suffix(' tahun')
                    ->alignCenter()
                    ->toggleable(),

                BadgeColumn::make('metode_penyusutan')
                    ->label('Metode Penyusutan')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'garis_lurus' => 'Garis Lurus',
                        'saldo_menurun' => 'Saldo Menurun',
                        'jumlah_angka_tahun' => 'Jumlah Angka Tahun',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'garis_lurus',
                        'warning' => 'saldo_menurun',
                        'info' => 'jumlah_angka_tahun',
                    ])
                    ->toggleable(),

                TextColumn::make('nilai_residu')
                    ->label('Nilai Residu')
                    ->money('IDR')
                    ->alignEnd()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('id_kategori')
                    ->label('Kategori Aset')
                    ->relationship('kategoriAset', 'nama_kategori')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('id_vendor')
                    ->label('Vendor')
                    ->relationship('vendor', 'nama_vendor')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                /*Filter::make('tanggal_faktur')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_faktur', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_faktur', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['from'])->format('d M Y');
                        }
                        if ($data['until'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['until'])->format('d M Y');
                        }
                        return $indicators;
                    }),*/

                /*Filter::make('masa_manfaat')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('min')
                            ->label('Minimal (tahun)')
                            ->numeric(),
                        \Filament\Forms\Components\TextInput::make('max')
                            ->label('Maksimal (tahun)')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min'],
                                fn (Builder $query, $value): Builder => $query->where('masa_manfaat', '>=', $value),
                            )
                            ->when(
                                $data['max'],
                                fn (Builder $query, $value): Builder => $query->where('masa_manfaat', '<=', $value),
                            );
                    }),*/
            ])
            ->recordActions([
                ViewAction::make(),
                //EditAction::make(),
            ])
            /*->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])*/
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}