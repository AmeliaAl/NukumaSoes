<?php

namespace App\Filament\Admin\Resources\Jurnals;

use App\Filament\Admin\Resources\Jurnals\Pages\ListJurnals;
use App\Filament\Admin\Resources\Jurnals\Schemas\JurnalForm;
use App\Filament\Admin\Resources\Jurnals\Tables\JurnalsTable;
use App\Models\Jurnal;
use App\Traits\HasRoleAccess;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;


class JurnalResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = Jurnal::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-newspaper';
    protected static UnitEnum|string|null $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Jurnal';
    protected static ?string $pluralModelLabel = 'Jurnal';
    protected static ?int $navigationSort = 301;

    /**
     * Pemilik bisa akses Jurnal (read-only)
     */
    protected static function canAccessByPemilik(): bool
    {
        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return JurnalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JurnalsTable::configure($table);
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
            'index' => ListJurnals::route('/'),
        ];
    }
}
