<?php

namespace App\Filament\Admin\Resources\LokasiAsets;

use App\Filament\Admin\Resources\LokasiAsets\Pages\CreateLokasiAset;
use App\Filament\Admin\Resources\LokasiAsets\Pages\EditLokasiAset;
use App\Filament\Admin\Resources\LokasiAsets\Pages\ListLokasiAsets;
use App\Filament\Admin\Resources\LokasiAsets\Schemas\LokasiAsetForm;
use App\Filament\Admin\Resources\LokasiAsets\Tables\LokasiAsetsTable;
use App\Models\LokasiAset;
use App\Traits\HasRoleAccess;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LokasiAsetResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = LokasiAset::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';
    protected static UnitEnum|string|null $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Lokasi Aset';
     protected static ?string $pluralModelLabel = 'Lokasi Aset';

    public static function form(Schema $schema): Schema
    {
        return LokasiAsetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LokasiAsetsTable::configure($table);
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
            'index' => ListLokasiAsets::route('/'),
            'create' => CreateLokasiAset::route('/create'),
            'edit' => EditLokasiAset::route('/{record}/edit'),
        ];
    }
}
