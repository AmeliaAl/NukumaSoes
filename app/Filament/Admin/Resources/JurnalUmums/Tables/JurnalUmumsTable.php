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
                \Filament\Tables\Columns\TextColumn::make('jurnal.tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('akun.nama_akun')
                    ->label('Nama Akun/perkiraan')
                    ->formatStateUsing(function ($record) {
                        return $record->kredit > 0 
                            ? '&nbsp;&nbsp;&nbsp;&nbsp;' . $record->akun->nama_akun 
                            : $record->akun->nama_akun;
                    })
                    ->html()
                    ->searchable()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('akun.kode_akun')
                    ->label('Ref')
                    ->searchable()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('debit')
                    ->label('Debit')
                    ->money('IDR', locale: 'id_ID')
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->money('IDR', locale: 'id_ID')),

                \Filament\Tables\Columns\TextColumn::make('kredit')
                    ->label('Kredit')
                    ->money('IDR', locale: 'id_ID')
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->money('IDR', locale: 'id_ID')),
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
                                fn (Builder $q) => $q->whereHas('jurnal', fn($q2) => $q2->whereDate('tanggal', '>=', $data['dari']))
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $q) => $q->whereHas('jurnal', fn($q2) => $q2->whereDate('tanggal', '<=', $data['sampai']))
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
            ->defaultSort(fn (Builder $query) => $query->orderBy('jurnal_umum_id', 'desc')->orderBy('id', 'asc'));
    }
}
