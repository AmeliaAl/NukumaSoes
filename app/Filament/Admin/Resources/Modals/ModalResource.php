<?php

namespace App\Filament\Admin\Resources\Modals;

use App\Filament\Admin\Resources\Modals\Pages\CreateModal;
use App\Filament\Admin\Resources\Modals\Pages\EditModal;
use App\Filament\Admin\Resources\Modals\Pages\ListModals;
use App\Filament\Admin\Resources\Modals\Schemas\ModalForm;
use App\Filament\Admin\Resources\Modals\Tables\ModalsTable;
use App\Models\Modal;
use App\Traits\HasRoleAccess;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ModalResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = Modal::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static UnitEnum|string|null $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Modal';
    protected static ?string $pluralModelLabel = 'Modal';
    protected static ?int $navigationSort = 80;

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
        return ModalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ModalsTable::configure($table);
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
            'index' => ListModals::route('/'),
            'create' => CreateModal::route('/create'),
            'edit' => EditModal::route('/{record}/edit'),
        ];
    }

}
