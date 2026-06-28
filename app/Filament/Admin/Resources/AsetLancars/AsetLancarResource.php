<?php

namespace App\Filament\Admin\Resources\AsetLancars;

use App\Filament\Admin\Resources\AsetLancars\Pages\CreateAsetLancar;
use App\Filament\Admin\Resources\AsetLancars\Pages\EditAsetLancar;
use App\Filament\Admin\Resources\AsetLancars\Pages\ListAsetLancars;
use App\Filament\Admin\Resources\AsetLancars\Schemas\AsetLancarForm;
use App\Filament\Admin\Resources\AsetLancars\Tables\AsetLancarsTable;
use App\Models\AsetLancar;
use App\Traits\HasRoleAccess;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AsetLancarResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = AsetLancar::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wallet';
    protected static UnitEnum|string|null $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Bahan Habis Pakai';
    protected static ?string $pluralModelLabel = 'Daftar Bahan Habis Pakai';
    protected static ?int $navigationSort = 6;
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
        return AsetLancarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AsetLancarsTable::configure($table);
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
            'index' => ListAsetLancars::route('/'),
            'create' => CreateAsetLancar::route('/create'),
          //  'edit' => EditAsetLancar::route('/{record}/edit'),
        ];
    }
}
