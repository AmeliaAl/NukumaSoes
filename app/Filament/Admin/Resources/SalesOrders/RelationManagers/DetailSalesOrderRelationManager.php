<?php

namespace App\Filament\Admin\Resources\SalesOrders\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class DetailSalesOrderRelationManager extends RelationManager
{
    protected static string $relationship = 'detailSalesOrder';

    protected static ?string $title = 'Detail Barang';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->columns([
                Tables\Columns\TextColumn::make('barang.nama_lengkap')
                    ->label('Nama Barang')
                    ->searchable(),

                Tables\Columns\TextColumn::make('qty')
                    ->label('Qty')
                    ->numeric(),

                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR', locale: 'id_ID'),

                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('IDR', locale: 'id_ID'),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
