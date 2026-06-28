<?php

namespace App\Filament\Resources\Akuns;

use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use UnitEnum;
use BackedEnum;
use Filament\Tables\Actions;
use Filament\Schemas\Schema;  // Added
use App\Filament\Resources\Akuns\Pages;
use App\Filament\Resources\Akuns\RelationManagers;
use App\Models\Akun;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Akuns\Schemas\AkunForm;
use App\Filament\Resources\Akuns\Tables\AkunsTable;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\Akuns\AkunResource\Pages\ListAkun;
use App\Filament\Resources\Akuns\AkunResource\Pages\CreateAkun;
use App\Filament\Resources\Akuns\AkunResource\Pages\EditAkun;


class AkunResource extends Resource
{
    protected static ?string $model = Akun::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Akun';
    protected static UnitEnum|string|null $navigationGroup = 'Master Data';
    protected static ?string $pluralModelLabel = 'Daftar Akun';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

         return $user->isAsset() || $user->isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
       return AkunForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AkunsTable::configure($table);
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
            'index' => Pages\ListAkuns::route('/'),
            'create' => Pages\CreateAkun::route('/create'),
            //'edit' => Pages\EditAkun::route('/{record}/edit'),
        ];
    }
}
