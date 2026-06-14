<?php

namespace App\Filament\Admin\Resources\HargaBarangs;

use App\Models\HargaBarang;
use App\Filament\Admin\Resources\HargaBarangs\Pages\ListHargaBarangs;
use App\Filament\Admin\Resources\HargaBarangs\Pages\CreateHargaBarang;
use App\Filament\Admin\Resources\HargaBarangs\Pages\EditHargaBarang;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HargaBarangResource extends Resource
{
    protected static ?string $model = HargaBarang::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;
    protected static UnitEnum|string|null $navigationGroup = 'Gudang';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Harga Barang';
    protected static ?string $pluralModelLabel = 'Harga Barang';
    protected static ?string $modelLabel = 'Harga Barang';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return \App\Filament\Admin\Resources\HargaBarangs\Schemas\HargaBarangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Admin\Resources\HargaBarangs\Tables\HargaBarangsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListHargaBarangs::route('/'),
            'create' => CreateHargaBarang::route('/create'),
            'edit'   => EditHargaBarang::route('/{record}/edit'),
        ];
    }
}
