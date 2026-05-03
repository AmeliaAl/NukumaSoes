<?php

namespace App\Filament\Admin\Resources\PenjualanNonKonsinyasis;

use App\Filament\Admin\Resources\PenjualanNonKonsinyasis\Pages\CreatePenjualanNonKonsinyasi;
use App\Filament\Admin\Resources\PenjualanNonKonsinyasis\Pages\EditPenjualanNonKonsinyasi;
use App\Filament\Admin\Resources\PenjualanNonKonsinyasis\Pages\ListPenjualanNonKonsinyasis;
use App\Filament\Admin\Resources\PenjualanNonKonsinyasis\Schemas\PenjualanNonKonsinyasiForm;
use App\Filament\Admin\Resources\PenjualanNonKonsinyasis\Tables\PenjualanNonKonsinyasisTable;
use App\Filament\Admin\Resources\PenjualanNonKonsinyasis\RelationManagers\DetailPenjualanNonKonsinyasiRelationManager;
use App\Models\PenjualanNonKonsinyasi;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PenjualanNonKonsinyasiResource extends Resource
{
    protected static ?string $model = \App\Models\PenjualanNonKonsinyasi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;
    protected static UnitEnum|string|null $navigationGroup = 'Penjualan Non Konsinyasi';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'no_invoice';

    public static function form(Schema $schema): Schema
    {
        return PenjualanNonKonsinyasiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenjualanNonKonsinyasisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DetailPenjualanNonKonsinyasiRelationManager::class,
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenjualanNonKonsinyasis::route('/'),
            'create' => Pages\CreatePenjualanNonKonsinyasi::route('/create'),
            'view' => Pages\ViewPenjualanNonKonsinyasi::route('/{record}'),
            'edit' => Pages\EditPenjualanNonKonsinyasi::route('/{record}/edit'),
        ];
    }

}
