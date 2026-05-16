<?php

namespace App\Filament\Admin\Resources\PemakaianPersediaans;

use App\Filament\Admin\Resources\PemakaianPersediaans\Pages\CreatePemakaianPersediaan;
use App\Filament\Admin\Resources\PemakaianPersediaans\Pages\EditPemakaianPersediaan;
use App\Filament\Admin\Resources\PemakaianPersediaans\Pages\ListPemakaianPersediaans;
use App\Filament\Admin\Resources\PemakaianPersediaans\Schemas\PemakaianPersediaanForm;
use App\Filament\Admin\Resources\PemakaianPersediaans\Tables\PemakaianPersediaansTable;
use App\Models\PemakaianPersediaan;
use App\Traits\HasRoleAccess;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PemakaianPersediaanResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = PemakaianPersediaan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-right-on-rectangle';
    protected static UnitEnum|string|null $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Pemakaian Bahan Habis Pakai';
    protected static ?string $pluralModelLabel =  'Pemakaian Bahan Habis Pakai';
    protected static ?int $navigationSort = 70;

    public static function form(Schema $schema): Schema
    {
        return PemakaianPersediaanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PemakaianPersediaansTable::configure($table);
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
            'index' => ListPemakaianPersediaans::route('/'),
            'create' => CreatePemakaianPersediaan::route('/create'),
        ];
    }

}
