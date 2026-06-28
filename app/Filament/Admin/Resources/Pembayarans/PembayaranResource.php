<?php

namespace App\Filament\Admin\Resources\Pembayarans;

use App\Filament\Admin\Resources\Pembayarans\Pages\CreatePembayaran;
use App\Filament\Admin\Resources\Pembayarans\Pages\EditPembayaran;
use App\Filament\Admin\Resources\Pembayarans\Pages\ListPembayarans;
use App\Filament\Admin\Resources\Pembayarans\Schemas\PembayaranForm;
use App\Filament\Admin\Resources\Pembayarans\Tables\PembayaransTable;
use App\Models\Pembayaran;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
    protected static UnitEnum|string|null $navigationGroup = 'Penjualan Non Konsinyasi';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'kode_pembayaran';

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
        return PembayaranForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PembayaransTable::configure($table);
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
            'index' => ListPembayarans::route('/'),
            'create' => CreatePembayaran::route('/create'),
            'edit' => EditPembayaran::route('/{record}/edit'),
        ];
    }
}
