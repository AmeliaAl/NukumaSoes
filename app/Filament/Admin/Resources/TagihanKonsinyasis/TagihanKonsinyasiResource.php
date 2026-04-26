<?php

namespace App\Filament\Admin\Resources\TagihanKonsinyasis;

use App\Filament\Admin\Resources\TagihanKonsinyasis\Pages\CreateTagihanKonsinyasi;
use App\Filament\Admin\Resources\TagihanKonsinyasis\Pages\EditTagihanKonsinyasi;
use App\Filament\Admin\Resources\TagihanKonsinyasis\Pages\ListTagihanKonsinyasis;
use App\Filament\Admin\Resources\TagihanKonsinyasis\Pages\ViewTagihanKonsinyasi;
use App\Filament\Admin\Resources\TagihanKonsinyasis\Schemas\TagihanKonsinyasiForm;
use App\Filament\Admin\Resources\TagihanKonsinyasis\Schemas\TagihanKonsinyasiInfolist;
use App\Filament\Admin\Resources\TagihanKonsinyasis\Tables\TagihanKonsinyasisTable;
use App\Models\TagihanKonsinyasi;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TagihanKonsinyasiResource extends Resource
{
    protected static ?string $model = TagihanKonsinyasi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;
    protected static UnitEnum|string|null $navigationGroup = 'Penjualan Konsinyasi';
    protected static ?string $recordTitleAttribute = 'no_tagihan';

    public static function form(Schema $schema): Schema
    {
        return TagihanKonsinyasiForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TagihanKonsinyasiInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TagihanKonsinyasisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PembayaranTagihanRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTagihanKonsinyasis::route('/'),
            'create' => CreateTagihanKonsinyasi::route('/create'),
            'view' => ViewTagihanKonsinyasi::route('/{record}'),
            'edit' => EditTagihanKonsinyasi::route('/{record}/edit'),
        ];
    }
}
