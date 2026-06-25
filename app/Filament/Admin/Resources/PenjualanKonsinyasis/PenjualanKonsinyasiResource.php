<?php

namespace App\Filament\Admin\Resources\PenjualanKonsinyasis;

use App\Filament\Admin\Resources\PenjualanKonsinyasis\Pages\CreatePenjualanKonsinyasi;
use App\Filament\Admin\Resources\PenjualanKonsinyasis\Pages\EditPenjualanKonsinyasi;
use App\Filament\Admin\Resources\PenjualanKonsinyasis\Pages\ListPenjualanKonsinyasis;
use App\Filament\Admin\Resources\PenjualanKonsinyasis\Pages\ViewPenjualanKonsinyasi;
use App\Filament\Admin\Resources\PenjualanKonsinyasis\Schemas\PenjualanKonsinyasiForm;
use App\Filament\Admin\Resources\PenjualanKonsinyasis\Schemas\PenjualanKonsinyasiInfolist;
use App\Filament\Admin\Resources\PenjualanKonsinyasis\Tables\PenjualanKonsinyasisTable;
use App\Models\PenjualanKonsinyasi;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Admin\Resources\PenjualanKonsinyasis\RelationManagers\DetailKonsinyasiRelationManager;
use App\Filament\Admin\Resources\PenjualanKonsinyasis\RelationManagers\SetoranKonsinyasiRelationManager;

class PenjualanKonsinyasiResource extends Resource
{
    protected static ?string $model = PenjualanKonsinyasi::class;
    protected static ?string $navigationLabel = 'Kelola Konsinyasi';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;
    protected static UnitEnum|string|null $navigationGroup = 'Penjualan Konsinyasi';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'no_konsinyasi';
    protected static ?string $pluralModelLabel = 'Kelola Penjualan Konsinyasi';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return PenjualanKonsinyasiForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PenjualanKonsinyasiInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenjualanKonsinyasisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Admin\Resources\PenjualanKonsinyasis\RelationManagers\DetailKonsinyasiRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPenjualanKonsinyasis::route('/'),
            'create' => CreatePenjualanKonsinyasi::route('/create'),
            'view' => ViewPenjualanKonsinyasi::route('/{record}'),
            'edit' => EditPenjualanKonsinyasi::route('/{record}/edit'),
        ];
    }
}