<?php

namespace App\Filament\Admin\Resources\Pemeliharaans;

use App\Filament\Admin\Resources\Pemeliharaans\Pages\CreatePemeliharaan;
use App\Filament\Admin\Resources\Pemeliharaans\Pages\EditPemeliharaan;
use App\Filament\Admin\Resources\Pemeliharaans\Pages\ListPemeliharaans;
use App\Filament\Admin\Resources\Pemeliharaans\Schemas\PemeliharaanForm;
use App\Filament\Admin\Resources\Pemeliharaans\Tables\PemeliharaansTable;
use App\Models\Pemeliharaan;
use App\Traits\HasRoleAccess;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PemeliharaanResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = Pemeliharaan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static UnitEnum|string|null $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Pemeliharaan';
    protected static ?string $pluralModelLabel = 'Pemeliharaan Aset';
     protected static ?int $navigationSort = 60;

    public static function form(Schema $schema): Schema
    {
        return PemeliharaanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PemeliharaansTable::configure($table);
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
            'index' => ListPemeliharaans::route('/'),
            'create' => CreatePemeliharaan::route('/create'),
        ];
    }
}
