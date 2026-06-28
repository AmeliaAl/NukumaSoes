<?php

namespace App\Filament\Admin\Resources\Pelanggans;

use App\Filament\Admin\Resources\Pelanggans\Pages\CreatePelanggan;
use App\Filament\Admin\Resources\Pelanggans\Pages\EditPelanggan;
use App\Filament\Admin\Resources\Pelanggans\Pages\ListPelanggans;
use App\Filament\Admin\Resources\Pelanggans\Schemas\PelangganForm;
use App\Filament\Admin\Resources\Pelanggans\Tables\PelanggansTable;
use App\Models\Pelanggan;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PelangganResource extends Resource
{
    protected static ?string $model = Pelanggan::class;
    protected static ?string $navigationLabel = 'Pelanggan';
    protected static ?string $pluralModelLabel = 'Daftar Pelanggan';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;
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
        return PelangganForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PelanggansTable::configure($table);
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
            'index' => ListPelanggans::route('/'),
            'create' => CreatePelanggan::route('/create'),
            'edit' => EditPelanggan::route('/{record}/edit'),
        ];
    }
}
