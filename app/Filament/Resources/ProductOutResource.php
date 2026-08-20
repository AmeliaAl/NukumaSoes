<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductOutResource\Pages;
use App\Models\ProdukKeluarEntry;
use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductOutResource extends Resource
{
    protected static ?string $model = ProdukKeluarEntry::class;

    public static function getModelLabel(): string
    {
        return 'Transaksi Keluar';
    }

    public static function getNavigationLabel(): string
    {
        return 'Transaksi Keluar';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-arrow-up-tray';
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
                    ->afterStateUpdated(function($state, Forms\Set $set) {
                        $product = Product::where('kode_produk', $state)->first();
                        if ($product) {
                            $set('nama_produk', $product->nama_produk);
                            $set('harga', $product->harga_jual);
                        }
                    }),
                Forms\Components\Hidden::make('nama_produk'),
                Forms\Components\DatePicker::make('tanggal')
                    ->required()
                    ->default(now()),
                Forms\Components\TextInput::make('jumlah_keluar')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->reactive()
                    ->afterStateUpdated(function($state, callable $get, Forms\Set $set) {
                        $harga = $get('harga') ?? 0;
                        $set('total_harga', $state * $harga);
                    }),
                Forms\Components\TextInput::make('harga')
                    ->label('Harga Jual Satuan')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->reactive()
                    ->afterStateUpdated(function($state, callable $get, Forms\Set $set) {
                        $qty = $get('jumlah_keluar') ?? 0;
                        $set('total_harga', $state * $qty);
                    }),
                Forms\Components\TextInput::make('total_harga')
                    ->readonly()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('harga_pokok_produksi')
                    ->label('')
                    ->numeric()
                    ->prefix('Rp')
                    ->helperText('Diisi manual oleh user'),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kode_produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah_keluar')
                    ->label('Qty')
                    ->numeric(),
                Tables\Columns\TextColumn::make('total_harga')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_pokok_produksi')
                    ->label('HPP')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListProductOuts::route('/'),
            'create' => Pages\CreateProductOut::route('/create'),
            'edit' => Pages\EditProductOut::route('/{record}/edit'),
        ];
    }
}
