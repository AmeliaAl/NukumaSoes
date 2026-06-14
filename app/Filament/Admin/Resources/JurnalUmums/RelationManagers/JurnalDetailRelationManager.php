<?php

namespace App\Filament\Admin\Resources\JurnalUmums\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;

class JurnalDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'details';
    protected static ?string $title = 'Detail Jurnal';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('akun.kode_akun')
                    ->label('Kode Akun')
                    ->sortable(),

                TextColumn::make('akun.nama_akun')
                    ->label('Nama Akun')
                    ->searchable(),

                TextColumn::make('debit')
                    ->label('Debit')
                    ->money('IDR', locale: 'id_ID')
                    ->alignRight()
                    ->summarize(
                        Sum::make()
                            ->label('Total Debit')
                            ->money('IDR', locale: 'id_ID')
                    ),

                TextColumn::make('kredit')
                    ->label('Kredit')
                    ->money('IDR', locale: 'id_ID')
                    ->alignRight()
                    ->summarize(
                        Sum::make()
                            ->label('Total Kredit')
                            ->money('IDR', locale: 'id_ID')
                    ),
            ])
            ->paginated(false)
        ->striped()
        ->searchable(false)
        ->actions([])        // hilangkan edit
        ->headerActions([]);     // hilangkan tambah
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
