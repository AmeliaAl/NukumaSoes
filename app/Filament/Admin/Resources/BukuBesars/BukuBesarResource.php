<?php

namespace App\Filament\Admin\Resources\BukuBesars;

use App\Filament\Admin\Resources\BukuBesars\Pages\CreateBukuBesar;
use App\Filament\Admin\Resources\BukuBesars\Pages\ListBukuBesars;
use App\Filament\Admin\Resources\BukuBesars\Schemas\BukuBesarForm;
use App\Filament\Admin\Resources\BukuBesars\Tables\BukuBesarsTable;
use App\Models\BukuBesar;
use App\Traits\HasRoleAccess;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BukuBesarResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = BukuBesar::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-group';
    protected static UnitEnum|string|null $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Buku Besar';
    protected static ?string $pluralModelLabel = 'Buku Besar';
    protected static ?int $navigationSort = 302;

    /**
     * Pemilik bisa akses Buku Besar (read-only)
     */
    protected static function canAccessByPemilik(): bool
    {
        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return BukuBesarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BukuBesarsTable::configure($table);
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
            'index' => ListBukuBesars::route('/'),
            'create' => CreateBukuBesar::route('/create'),
        ];
    }
}
