<?php

namespace App\Filament\Admin\Resources\Mitras;

use App\Filament\Admin\Resources\Mitras\Pages\CreateMitra;
use App\Filament\Admin\Resources\Mitras\Pages\EditMitra;
use App\Filament\Admin\Resources\Mitras\Pages\ListMitras;
use App\Filament\Admin\Resources\Mitras\Schemas\MitraForm;
use App\Filament\Admin\Resources\Mitras\Tables\MitrasTable;
use App\Models\Mitra;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MitraResource extends Resource
{
    protected static ?string $model = Mitra::class;
    protected static ?string $navigationLabel = 'Mitra';
    protected static ?string $pluralModelLabel = 'Daftar Mitra';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static UnitEnum|string|null $navigationGroup = 'Master Data';
    
    public static function canAccess(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

         return $user->isPenjualans() || $user->isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
         return MitraForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MitrasTable::configure($table);
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
            'index' => ListMitras::route('/'),
            'create' => CreateMitra::route('/create'),
            'edit' => EditMitra::route('/{record}/edit'),
        ];
    }
}
