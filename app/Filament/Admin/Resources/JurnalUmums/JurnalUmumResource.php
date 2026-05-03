<?php

namespace App\Filament\Admin\Resources\JurnalUmums;

use App\Filament\Admin\Resources\JurnalUmums\Pages\ListJurnalUmums;
use App\Filament\Admin\Resources\JurnalUmums\Pages\ViewJurnalUmum;
use App\Filament\Admin\Resources\JurnalUmums\RelationManagers\JurnalDetailRelationManager;
use App\Filament\Admin\Resources\JurnalUmums\Tables\JurnalUmumsTable;
use App\Models\JurnalUmum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class JurnalUmumResource extends Resource
{
    protected static ?string $model = JurnalUmum::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document';

    protected static \UnitEnum|string|null $navigationGroup = 'Akuntansi';

    protected static ?string $recordTitleAttribute = 'keterangan';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return JurnalUmumsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            JurnalDetailRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJurnalUmums::route('/'),
            'view'  => ViewJurnalUmum::route('/{record}'),
        ];
    }
    public static function canCreate(): bool
    {
        return false;
    }
    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

}
