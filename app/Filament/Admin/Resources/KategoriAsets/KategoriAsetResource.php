<?php

namespace App\Filament\Admin\Resources\KategoriAsets;

use App\Filament\Admin\Resources\KategoriAsets\Pages\CreateKategoriAset;
use App\Filament\Admin\Resources\KategoriAsets\Pages\EditKategoriAset;
use App\Filament\Admin\Resources\KategoriAsets\Pages\ListKategoriAsets;
use App\Filament\Admin\Resources\KategoriAsets\Schemas\KategoriAsetForm;
use App\Filament\Admin\Resources\KategoriAsets\Tables\KategoriAsetsTable;
use App\Models\kategoriAset;
use App\Traits\HasRoleAccess;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KategoriAsetResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = kategoriAset::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';
    protected static UnitEnum|string|null $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Kategori Aset';
    protected static ?string $pluralModelLabel = 'Daftar Kategori Aset';

    public static function form(Schema $schema): Schema
    {
        return KategoriAsetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KategoriAsetsTable::configure($table);
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
            'index' => ListKategoriAsets::route('/'),
            'create' => CreateKategoriAset::route('/create'),
           // 'edit' => EditKategoriAset::route('/{record}/edit'),
        ];
    }
}
