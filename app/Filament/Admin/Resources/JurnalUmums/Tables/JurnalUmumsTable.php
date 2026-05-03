<?php

namespace App\Filament\Admin\Resources\JurnalUmums\Tables;

use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class JurnalUmumsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('no_jurnal')
                    ->label('Nomor')
                    ->searchable()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('keterangan')
                    ->label('Referensi')
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('total')
                    ->label('Jumlah')
                    ->money('IDR', locale: 'id_ID'),
            ])
            ->filters([
                Filter::make('tanggal')
                    ->label('Rentang Tanggal')
                    ->form([
                        DatePicker::make('dari')
                            ->label('Dari Tanggal')
                            ->placeholder('Pilih tanggal awal'),
                        DatePicker::make('sampai')
                            ->label('Sampai Tanggal')
                            ->placeholder('Pilih tanggal akhir'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn (Builder $q) => $q->whereDate('tanggal', '>=', $data['dari'])
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $q) => $q->whereDate('tanggal', '<=', $data['sampai'])
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['dari'] ?? null) {
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['dari'])->translatedFormat('d M Y');
                        }

                        if ($data['sampai'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['sampai'])->translatedFormat('d M Y');
                        }

                        return $indicators;
                    }),
            ])
            ->recordAction('view')
            ->defaultSort('tanggal', 'desc');
    }
}
