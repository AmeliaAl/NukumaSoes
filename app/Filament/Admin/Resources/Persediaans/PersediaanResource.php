<?php

namespace App\Filament\Admin\Resources\Persediaans;

use App\Filament\Admin\Resources\Persediaans\Pages\CreatePersediaan;
use App\Filament\Admin\Resources\Persediaans\Pages\EditPersediaan;
use App\Filament\Admin\Resources\Persediaans\Pages\ListPersediaans;
use App\Filament\Admin\Resources\Persediaans\Schemas\PersediaanForm;
use App\Filament\Admin\Resources\Persediaans\Tables\PersediaansTable;
use App\Models\Persediaan;
use App\Traits\HasRoleAccess;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PersediaanResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = Persediaan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';
    protected static UnitEnum|string|null $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Persediaan';
    protected static ?string $pluralModelLabel = 'Perolehan Bahan Habis Pakai';
    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return PersediaanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PersediaansTable::configure($table);
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
            'index' => ListPersediaans::route('/'),
            'create' => CreatePersediaan::route('/create'),
            'edit' => EditPersediaan::route('/{record}/edit'),
        ];
    }
}
