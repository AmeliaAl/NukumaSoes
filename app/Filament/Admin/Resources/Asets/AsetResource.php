<?php

namespace App\Filament\Admin\Resources\Asets;

use App\Filament\Admin\Resources\Asets\Pages\CreateAset;
use App\Filament\Admin\Resources\Asets\Pages\EditAset;
use App\Filament\Admin\Resources\Asets\Pages\ListAsets;
use App\Filament\Admin\Resources\Asets\Schemas\AsetForm;
use App\Filament\Admin\Resources\Asets\Tables\AsetsTable;
use App\Models\Aset;
use App\Traits\HasRoleAccess;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;
use App\Models\Penyusutan;



class AsetResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = Aset::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';
    protected static UnitEnum|string|null $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Aset Tetap';
    protected static ?string $pluralModelLabel = 'Aset Tetap';

    public static function form(Schema $schema): Schema
    {
        return AsetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AsetsTable::configure($table);
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
            'index' => ListAsets::route('/'),
            'create' => CreateAset::route('/create'),
            'edit' => EditAset::route('/{record}/edit'),
        ];
    }

}
