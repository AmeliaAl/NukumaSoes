<?php

namespace App\Filament\Admin\Resources\Barangs;

use App\Models\Barang;
use App\Filament\Admin\Resources\Barangs\Pages\CreateBarang;
use App\Filament\Admin\Resources\Barangs\Pages\EditBarang;
use App\Filament\Admin\Resources\Barangs\Pages\ListBarangs;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use BackedEnum;
use UnitEnum;
use App\Filament\Admin\Resources\Barangs\Schemas\BarangForm;
use App\Filament\Admin\Resources\Barangs\Tables\BarangsTable;
use Illuminate\Database\Eloquent\Builder;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;
    protected static ?string $navigationLabel = 'Barang';
    protected static UnitEnum|string|null $navigationGroup = 'Master Data';
    protected static ?string $pluralModelLabel = 'Daftar Barang';
    
    public static function form(Schema $schema): Schema
    {
        return BarangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BarangsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('hargaBarang'); 
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBarangs::route('/'),
            'create' => CreateBarang::route('/create'),
            'edit' => EditBarang::route('/{record}/edit'),
        ];
    }
}
