<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Category;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cube';
    }

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('nama_produk')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kode_produk')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('kategori')
                    ->options(Category::all()->pluck('nama_kategori', 'nama_kategori'))
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('satuan')
                    ->maxLength(50),

                Forms\Components\TextInput::make('harga')
                    ->label('Harga Beli / Pokok')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('harga_jual')
                    ->label('Harga Jual')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\DatePicker::make('tgl_expired')
                    ->label('Tanggal Expired (Default)'),
                Forms\Components\Select::make('status')
                    ->options([
                        'aman' => 'Aman',
                        'mau_expired' => 'Mau Expired',
                        'expired' => 'Expired',
                    ])
                    ->default('aman'),
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

                Tables\Columns\TextColumn::make('harga_jual')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aman' => 'success',
                        'mau_expired' => 'warning',
                        'expired' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategori')
                    ->options(Category::all()->pluck('nama_kategori', 'nama_kategori')),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'aman' => 'Aman',
                        'mau_expired' => 'Mau Expired',
                        'expired' => 'Expired',
                    ]),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
