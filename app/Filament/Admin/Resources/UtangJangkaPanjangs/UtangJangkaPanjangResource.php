<?php

namespace App\Filament\Admin\Resources\UtangJangkaPanjangs;

use App\Filament\Admin\Resources\UtangJangkaPanjangs\Pages\CreateUtangJangkaPanjang;
use App\Filament\Admin\Resources\UtangJangkaPanjangs\Pages\EditUtangJangkaPanjang;
use App\Filament\Admin\Resources\UtangJangkaPanjangs\Pages\ListUtangJangkaPanjangs;
use App\Filament\Admin\Resources\UtangJangkaPanjangs\Schemas\UtangJangkaPanjangForm;
use App\Filament\Admin\Resources\UtangJangkaPanjangs\Tables\UtangJangkaPanjangsTable;
use App\Models\UtangJangkaPanjang;
use App\Traits\HasRoleAccess;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class UtangJangkaPanjangResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = UtangJangkaPanjang::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';
    protected static UnitEnum|string|null $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Utang Jangka Panjang';
    protected static ?string $pluralModelLabel = 'Daftar Utang Jangka Panjang';
    protected static ?int $navigationSort = 201;

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
        return UtangJangkaPanjangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UtangJangkaPanjangsTable::configure($table);
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
            'index' => ListUtangJangkaPanjangs::route('/'),
            'create' => CreateUtangJangkaPanjang::route('/create'),
            'edit' => EditUtangJangkaPanjang::route('/{record}/edit'),
        ];
    }
}
