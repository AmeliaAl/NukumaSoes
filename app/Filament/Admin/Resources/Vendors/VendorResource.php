<?php

namespace App\Filament\Admin\Resources\Vendors;

use App\Filament\Admin\Resources\Vendors\Pages\CreateVendor;
use App\Filament\Admin\Resources\Vendors\Pages\EditVendor;
use App\Filament\Admin\Resources\Vendors\Pages\ListVendors;
use App\Filament\Admin\Resources\Vendors\Schemas\VendorForm;
use App\Filament\Admin\Resources\Vendors\Tables\VendorsTable;
use App\Models\Vendor;
use App\Traits\HasRoleAccess;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class VendorResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = Vendor::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';
    protected static UnitEnum|string|null $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Vendor';
    protected static ?string $pluralModelLabel = 'Vendor';
     protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

         return $user->isAsset() || $user->isAdmin();
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();
        return $user && ($user->isAsset() || $user->isAdmin());
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();
        return $user && ($user->isAsset() || $user->isAdmin());
    }

    public static function canDelete($record): bool
    {
        $user = Auth::user();
        return $user && ($user->isAsset() || $user->isAdmin());
    }

    public static function form(Schema $schema): Schema
    {
        return VendorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorsTable::configure($table);
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
            'index' => ListVendors::route('/'),
            'create' => CreateVendor::route('/create'),
            'edit' => EditVendor::route('/{record}/edit'),
        ];
    }
}
