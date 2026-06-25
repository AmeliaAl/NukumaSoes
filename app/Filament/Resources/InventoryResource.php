<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Models\Inventory;
use App\Models\Product;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    public static function getModelLabel(): string
    {
        return 'Transaksi Masuk (Inventory)';
    }

    public static function getNavigationLabel(): string
    {
        return 'Transaksi Masuk';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-arrow-down-tray';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Transaksi';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('kode_produk')
                    ->label('Produk')
                    ->options(Product::all()->pluck('nama_produk', 'kode_produk'))
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => 
                        $set('nama_produk', Product::where('kode_produk', $state)->value('nama_produk'))
                    ),
                Forms\Components\Hidden::make('nama_produk'), // Auto-filled
                Forms\Components\DatePicker::make('tgl_masuk')
                    ->required()
                    ->default(now()),
                Forms\Components\DatePicker::make('tgl_expired')
                    ->required(),
                Forms\Components\TextInput::make('jumlah')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\TextInput::make('harga')
                    ->label('Harga Beli Satuan')
                    ->numeric()
                    ->prefix('Rp'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tgl_masuk')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kode_produk')
                    ->label('No Batch')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->numeric()
                    ->label('Qty Masuk'),
                Tables\Columns\TextColumn::make('sisa_hari')
                    ->label('Sisa Hari')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($state) => $state < 30 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('tgl_expired')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('mau_expired')
                    ->query(fn ($query) => $query->where('sisa_hari', '<=', 30))
                    ->label('Hampir Expired'),
            ])
            ->actions([
                \Filament\Actions\Action::make('edit')
                    ->label('Edit')
                    ->url(fn (\App\Models\Inventory $record): string => static::getUrl('edit', ['record' => $record])),

                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventories::route('/'),
            'create' => Pages\CreateInventory::route('/create'),
            'edit' => Pages\EditInventory::route('/{record}/edit'),
        ];
    }
}
