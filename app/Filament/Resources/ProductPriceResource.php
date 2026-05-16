<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductPriceResource\Pages;
use App\Models\Product;
use App\Models\Category;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;

class ProductPriceResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $modelLabel = 'Harga Produk';
    protected static ?string $pluralModelLabel = 'Harga Produk';
    protected static ?string $navigationLabel = 'Harga Produk';
    protected static ?string $slug = 'harga-produk';

    protected static ?int $navigationSort = 2; // Below Category (if Category has 1)

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-currency-dollar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('kode_produk')
                    ->disabled()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama_produk')
                    ->disabled()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('harga')
                    ->label('Harga Beli / Pokok')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('harga_jual')
                    ->label('Harga Jual')
                    ->numeric()
                    ->prefix('Rp'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_produk')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_produk')
                    ->searchable()
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('kategori')
                    ->searchable(),
                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga Pokok')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_jual')
                    ->label('Harga Jual')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategori')
                    ->options(Category::all()->pluck('nama_kategori', 'nama_kategori')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ]);
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
            'index' => Pages\ListProductPrices::route('/'),
            // 'create' => Pages\CreateProductPrice::route('/create'),
            'edit' => Pages\EditProductPrice::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Typically list & edit only if using Product model
    }

    public static function canViewAny(): bool
    {
        return true;
    }
}
