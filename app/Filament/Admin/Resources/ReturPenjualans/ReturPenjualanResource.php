<?php

namespace App\Filament\Admin\Resources\ReturPenjualans;

use App\Models\ReturPenjualan;
use App\Filament\Admin\Resources\ReturPenjualans\Pages;
use App\Filament\Admin\Resources\ReturPenjualans\Schemas\ReturPenjualanForm;
use App\Filament\Admin\Resources\ReturPenjualans\Schemas\ReturPenjualanInfolist;
use App\Filament\Admin\Resources\ReturPenjualans\Tables\ReturPenjualansTable;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ReturPenjualanResource extends Resource
{
    protected static ?string $model = ReturPenjualan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUturnLeft;
    protected static UnitEnum|string|null $navigationGroup = 'Gudang';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Retur Barang';
    protected static ?string $pluralModelLabel = 'Retur Barang';
    protected static ?string $modelLabel = 'Retur';
    protected static ?string $recordTitleAttribute = 'no_retur';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

         return $user->isPenjualans() || $user->isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return ReturPenjualanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ReturPenjualanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReturPenjualansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListReturPenjualans::route('/'),
            'create' => Pages\CreateReturPenjualan::route('/create'),
            'view'   => Pages\ViewReturPenjualan::route('/{record}'),
            'edit'   => Pages\EditReturPenjualan::route('/{record}/edit'),
        ];
    }
}
