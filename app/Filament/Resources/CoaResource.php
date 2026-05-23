<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CoaResource\Pages;
use App\Models\Coa;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use UnitEnum;
use BackedEnum;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CoaResource\Schemas\CoaForm;
use App\Filament\Resources\CoaResource\Tables\CoasTable;

use App\Filament\Resources\CoaResource\Pages\ListCoa;
use App\Filament\Resources\CoaResource\Pages\CreateCoa;
use App\Filament\Resources\CoaResource\Pages\EditCoa;

class CoaResource extends Resource
{
    protected static ?string $model = Coa::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Coa';
    protected static UnitEnum|string|null $navigationGroup = 'Master Data';
    protected static ?string $pluralModelLabel = 'Daftar Coa';



    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
       return CoaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CoasTable::configure($table);
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
            'index'  => Pages\ListCoas::route('/'),
            'create' => Pages\CreateCoa::route('/create'),
            'edit'   => Pages\EditCoa::route('/{record}/edit'),
        ];
    }

}
